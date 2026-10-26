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
        $query = Product::with('category');

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

        if ($stockStatus = $request->input('stock_status')) {
            if ($stockStatus === 'low_stock') {
                $query->whereColumn('stock', '<=', 'minimum_stock');
            } elseif ($stockStatus === 'out_of_stock') {
                $query->where('stock', '<=', 0);
            } elseif ($stockStatus === 'available') {
                $query->where('stock', '>', 0);
            }
        }

        $products = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('customer.catalog', compact('products', 'categories'));
    }

    /**
     * Process customer checkout from catalog (supports both guest and authenticated customers).
     */
    public function checkout(CustomerCheckoutRequest $request): RedirectResponse
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // If guest, find or create customer record using name and phone
        if (! $user) {
            $name = trim($request->input('customer_name') ?? $request->input('name') ?? '');
            $phone = trim($request->input('customer_phone') ?? $request->input('phone') ?? '') ?: null;

            if (empty($name)) {
                return back()->withErrors(['customer_name' => 'Nama Pelanggan wajib diisi untuk memproses pesanan.'])->withInput();
            }

            // Find existing customer by name/phone or create a new passwordless record
            $customer = \App\Models\User::where('role', 'customer')
                ->where(function ($query) use ($name, $phone) {
                    $query->where('name', $name);
                    if (! empty($phone)) {
                        $query->orWhere('phone', $phone);
                    }
                })
                ->first();

            if (! $customer) {
                $slug = \Illuminate\Support\Str::slug($name);
                if (empty($slug)) {
                    $slug = 'pelanggan';
                }
                $randomSuffix = strtolower(\Illuminate\Support\Str::random(6));
                $email = "{$slug}_{$randomSuffix}@customer.local";

                $customer = \App\Models\User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(24)),
                    'role' => 'customer',
                    'phone' => $phone,
                ]);
            } else {
                if (! empty($phone) && empty($customer->phone)) {
                    $customer->update(['phone' => $phone]);
                }
            }

            Auth::login($customer, true);
            $request->session()->regenerate();
            $user = $customer;
        }

        try {
            $paymentMethod = $request->input('payment_method', 'qris');
            $sale = $this->saleService->createSale(
                creator: $user,
                customerId: $user->id,
                items: $request->input('items', []),
                paymentMethod: $paymentMethod,
                paymentStatus: 'paid'
            );

            return redirect()->route('sales.show', $sale)
                ->with('success', "Pesanan Anda (#{$sale->sale_number}) berhasil dibuat dengan metode pembayaran {$sale->payment_method_label}!")
                ->with('open_snap_modal', true);
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
            ->paginate(10)
            ->withQueryString();

        return view('customer.orders', compact('orders'));
    }
}
