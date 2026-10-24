<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Product::with('category');

        // Search by SKU, name, or brand
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Filter by stock status
        if ($request->input('filter') === 'low_stock') {
            $query->whereColumn('stock', '<=', 'minimum_stock');
        }

        $products = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource (Admin only).
     */
    public function create(): View
    {
        $this->ensureUserIsAdmin();

        $categories = Category::orderBy('name')->get();

        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage (Admin only).
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['stock'] = $data['stock'] ?? 0;

        $product = Product::create($data);

        return redirect()->route('products.index')
            ->with('success', "Produk '{$product->name}' (SKU: {$product->sku}) berhasil ditambahkan.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): View
    {
        $product->load(['category', 'purchaseItems.purchase.supplier', 'saleItems.sale.user']);

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource (Admin only).
     */
    public function edit(Product $product): View
    {
        $this->ensureUserIsAdmin();

        $categories = Category::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage (Admin only).
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        return redirect()->route('products.index')
            ->with('success', "Produk '{$product->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified resource from storage (Admin only, with Soft Delete protection).
     */
    public function destroy(Product $product): RedirectResponse
    {
        $this->ensureUserIsAdmin();

        $hasTransactions = $product->purchaseItems()->exists() || $product->saleItems()->exists();

        // Perform Soft Delete
        $product->delete();

        if ($hasTransactions) {
            return redirect()->route('products.index')
                ->with('info', "Produk '{$product->name}' berhasil diarsipkan (soft delete) untuk menjaga integritas riwayat transaksi penjualan/pembelian.");
        }

        return redirect()->route('products.index')
            ->with('success', "Produk '{$product->name}' berhasil dihapus.");
    }

    /**
     * Helper to enforce Admin-only access on mutating actions.
     */
    protected function ensureUserIsAdmin(): void
    {
        if (! Auth::user() || ! Auth::user()->isAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Admin yang memiliki izin untuk menambah, mengedit, atau menghapus produk.');
        }
    }
}
