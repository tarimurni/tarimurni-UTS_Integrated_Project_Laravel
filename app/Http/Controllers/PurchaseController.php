<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    /**
     * Display a listing of purchases.
     */
    public function index(Request $request): View
    {
        $query = Purchase::with(['supplier', 'user'])->withCount('items');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('purchase_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $purchases = $query->latest('purchased_at')->paginate(10)->withQueryString();

        return view('purchases.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new purchase transaction.
     */
    public function create(): View
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get(['id', 'sku', 'name', 'brand', 'purchase_price', 'stock']);

        return view('purchases.create', compact('suppliers', 'products'));
    }

    /**
     * Store a newly created purchase transaction in storage (Atomic Stock-In).
     */
    public function store(StorePurchaseRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $purchase = DB::transaction(function () use ($validated) {
                // 1. Generate unique Purchase Order Number: PO-YYYYMMDD-XXXX
                $datePrefix = now()->format('Ymd');
                do {
                    $purchaseNumber = 'PO-' . $datePrefix . '-' . strtoupper(Str::random(4));
                } while (Purchase::where('purchase_number', $purchaseNumber)->exists());

                // 2. Create Purchase Header
                $purchase = Purchase::create([
                    'supplier_id' => $validated['supplier_id'],
                    'user_id' => Auth::id(),
                    'purchase_number' => $purchaseNumber,
                    'total' => 0, // Will be computed from items
                    'status' => 'completed',
                    'purchased_at' => $validated['purchased_at'] ?? now(),
                ]);

                $grandTotal = 0;

                // 3. Process each purchase item with lockForUpdate & stock increment
                foreach ($validated['items'] as $itemData) {
                    /** @var Product $product */
                    $product = Product::lockForUpdate()->findOrFail($itemData['product_id']);

                    $qty = (int) $itemData['quantity'];
                    $unitCost = (float) $itemData['unit_cost'];
                    $subtotal = $qty * $unitCost;

                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'unit_cost' => $unitCost,
                        'subtotal' => $subtotal,
                    ]);

                    // Automatically increment product stock in warehouse
                    $product->increment('stock', $qty);

                    // Update latest purchase price if applicable
                    if ($product->purchase_price != $unitCost) {
                        $product->update(['purchase_price' => $unitCost]);
                    }

                    $grandTotal += $subtotal;
                }

                // 4. Update Header with computed total
                $purchase->update(['total' => $grandTotal]);

                return $purchase;
            });

            return redirect()->route('purchases.show', $purchase)
                ->with('success', "Transaksi Pembelian {$purchase->purchase_number} berhasil dicatat dan stok produk telah bertambah secara otomatis.");
        } catch (Exception $e) {
            return back()->withInput()
                ->with('error', 'Gagal memproses transaksi pembelian: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified purchase invoice / details.
     */
    public function show(Purchase $purchase): View
    {
        $purchase->load(['supplier', 'user', 'items.product.category']);

        return view('purchases.show', compact('purchase'));
    }

    /**
     * Cancel purchase transaction and roll back product stocks (Admin Only).
     */
    public function cancel(Purchase $purchase): RedirectResponse
    {
        if (! Auth::user() || ! Auth::user()->isAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Admin yang berhak membatalkan transaksi pembelian.');
        }

        if ($purchase->status !== 'completed') {
            return back()->with('error', 'Hanya transaksi berstatus Selesai (Completed) yang dapat dibatalkan.');
        }

        try {
            DB::transaction(function () use ($purchase) {
                // 1. Verify and decrement stock for each item
                foreach ($purchase->items as $item) {
                    /** @var Product $product */
                    $product = Product::lockForUpdate()->findOrFail($item->product_id);

                    if ($product->stock < $item->quantity) {
                        throw new Exception("Stok produk '{$product->name}' saat ini ({$product->stock} unit) tidak mencukupi untuk dikurangi {$item->quantity} unit (kemungkinan barang sudah terjual).");
                    }

                    $product->decrement('stock', $item->quantity);
                }

                // 2. Mark purchase as cancelled
                $purchase->update(['status' => 'cancelled']);
            });

            return redirect()->route('purchases.show', $purchase)
                ->with('warning', "Transaksi {$purchase->purchase_number} telah DIBATALKAN dan stok barang berhasil ditarik kembali.");
        } catch (Exception $e) {
            return back()->with('error', 'Pembatalan transaksi gagal: ' . $e->getMessage());
        }
    }
}
