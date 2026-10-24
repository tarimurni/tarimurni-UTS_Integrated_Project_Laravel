<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Services\SaleService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function __construct(
        protected SaleService $saleService
    ) {}

    /**
     * Display a listing of sales transactions.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = Sale::with(['user', 'customer'])->withCount('items');

        // If logged in as customer, only show their own transactions
        if ($user->isCustomer()) {
            $query->where(function ($q) use ($user) {
                $q->where('customer_id', $user->id)
                  ->orWhere('user_id', $user->id);
            });
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('sale_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($startDate = $request->input('start_date')) {
            $query->whereDate('sold_at', '>=', $startDate);
        }

        if ($endDate = $request->input('end_date')) {
            $query->whereDate('sold_at', '<=', $endDate);
        }

        $sales = $query->latest('sold_at')->paginate(10)->withQueryString();

        return view('sales.index', compact('sales'));
    }

    /**
     * Show cashier POS form for creating a new sale (Admin & Staff).
     */
    public function create(): View
    {
        $customers = User::where('role', 'customer')->orderBy('name')->get();
        $products = Product::where('stock', '>', 0)
            ->with('category')
            ->orderBy('name')
            ->get(['id', 'sku', 'name', 'brand', 'image_url', 'color_name', 'color_hex', 'category_id', 'selling_price', 'stock']);

        return view('sales.create', compact('customers', 'products'));
    }

    /**
     * Store a new sale transaction (Admin & Staff Cashier).
     */
    public function store(StoreSaleRequest $request): RedirectResponse
    {
        try {
            $sale = $this->saleService->createSale(
                creator: Auth::user(),
                customerId: $request->input('customer_id'),
                items: $request->input('items', []),
                soldAt: $request->input('sold_at')
            );

            return redirect()->route('sales.show', $sale)
                ->with('success', "Transaksi Kasir {$sale->sale_number} berhasil diproses dan struk penjualan siap dicetak.");
        } catch (ValidationException $ve) {
            return back()->withErrors($ve->errors())->withInput();
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memproses transaksi kasir: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified sale invoice / receipt.
     */
    public function show(Sale $sale): View
    {
        $user = Auth::user();

        // Security check: customer can only view their own invoice
        if ($user->isCustomer()) {
            if ($sale->customer_id !== $user->id && $sale->user_id !== $user->id) {
                abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk melihat faktur transaksi milik pelanggan lain.');
            }
        }

        $sale->load(['user', 'customer', 'items.product.category']);

        return view('sales.show', compact('sale'));
    }

    /**
     * Cancel sale transaction and restore product stock (Admin Only).
     */
    public function cancel(Sale $sale): RedirectResponse
    {
        if (! Auth::user() || ! Auth::user()->isAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Admin yang berhak membatalkan transaksi penjualan.');
        }

        try {
            $this->saleService->cancelSale($sale);

            return redirect()->route('sales.show', $sale)
                ->with('warning', "Transaksi {$sale->sale_number} telah DIBATALKAN dan stok barang berhasil dikembalikan ke inventaris.");
        } catch (Exception $e) {
            return back()->with('error', 'Pembatalan penjualan gagal: ' . $e->getMessage());
        }
    }
}
