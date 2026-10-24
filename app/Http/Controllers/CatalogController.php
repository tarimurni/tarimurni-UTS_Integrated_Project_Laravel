<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerCheckoutRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function __construct(
        protected SaleService $saleService
    ) {}

    /**
     * Display electronics catalog for customers.
     */
    public function index(Request $request): View
    {
        $query = Product::with('category')->where('stock', '>', 0);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('customer.catalog', compact('products', 'categories'));
    }

    /**
     * Process customer checkout from catalog.
     */
    public function checkout(CustomerCheckoutRequest $request): RedirectResponse
    {
        $user = Auth::user();

        try {
            $sale = $this->saleService->createSale(
                creator: $user,
                customerId: $user->id,
                items: $request->input('items', [])
            );

            return redirect()->route('sales.show', $sale)
                ->with('success', "Pesanan Anda (#{$sale->sale_number}) berhasil dibuat! Terima kasih telah berbelanja di ElectroMart.");
        } catch (ValidationException $ve) {
            return back()->withErrors($ve->errors())->withInput();
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display order history for the logged-in customer.
     */
    public function orders(Request $request): View
    {
        $user = Auth::user();

        $orders = Sale::where('customer_id', $user->id)
            ->orWhere('user_id', $user->id)
            ->with(['items.product'])
            ->latest('sold_at')
            ->paginate(10);

        return view('customer.orders', compact('orders'));
    }
}
