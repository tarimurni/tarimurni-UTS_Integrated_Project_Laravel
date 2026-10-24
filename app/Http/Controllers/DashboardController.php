<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display real-time aggregate dashboard based on user role.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // 1. Data for Admin and Staff
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalSuppliers = Supplier::count();

        // Sales aggregate figures
        $salesToday = Sale::where('status', 'completed')
            ->whereDate('sold_at', today())
            ->sum('total');

        $salesThisMonth = Sale::where('status', 'completed')
            ->whereMonth('sold_at', now()->month)
            ->whereYear('sold_at', now()->year)
            ->sum('total');

        // Purchases aggregate figures
        $purchasesThisMonth = Purchase::where('status', 'completed')
            ->whereMonth('purchased_at', now()->month)
            ->whereYear('purchased_at', now()->year)
            ->sum('total');

        $totalSalesCount = Sale::where('status', 'completed')->count();

        // Low stock products alert query
        $lowStockProducts = Product::with('category')
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock')
            ->get();
        $totalLowStock = $lowStockProducts->count();

        // 5 most recent sales transactions
        $recentSales = Sale::with(['user', 'customer'])
            ->latest('sold_at')
            ->take(5)
            ->get();

        // 2. Data for Customer
        $customerOrdersCount = 0;
        $customerTotalSpent = 0;
        $customerRecentOrders = collect();

        if ($user->isCustomer()) {
            $customerSalesQuery = Sale::where(function ($q) use ($user) {
                $q->where('customer_id', $user->id)
                  ->orWhere('user_id', $user->id);
            });

            $customerOrdersCount = (clone $customerSalesQuery)->count();
            $customerTotalSpent = (clone $customerSalesQuery)
                ->where('status', 'completed')
                ->sum('total');

            $customerRecentOrders = (clone $customerSalesQuery)
                ->with(['items.product'])
                ->latest('sold_at')
                ->take(5)
                ->get();
        }

        return view('dashboard', compact(
            'user',
            'totalProducts',
            'totalCategories',
            'totalSuppliers',
            'salesToday',
            'salesThisMonth',
            'purchasesThisMonth',
            'totalSalesCount',
            'lowStockProducts',
            'totalLowStock',
            'recentSales',
            'customerOrdersCount',
            'customerTotalSpent',
            'customerRecentOrders'
        ));
    }
}
