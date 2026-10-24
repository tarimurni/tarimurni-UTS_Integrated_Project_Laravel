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
     * Display unified dashboard based on user role.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // Data for Admin and Staff
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalSuppliers = Supplier::count();
        $lowStockProducts = Product::with('category')
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->get();
        $totalLowStock = $lowStockProducts->count();

        $totalSalesCount = Sale::count();
        $totalSalesAmount = Sale::where('status', 'completed')->sum('total');

        // Customer specific stats
        $customerSalesCount = $user->isCustomer()
            ? Sale::where('customer_id', $user->id)->count()
            : 0;

        return view('dashboard', compact(
            'user',
            'totalProducts',
            'totalCategories',
            'totalSuppliers',
            'lowStockProducts',
            'totalLowStock',
            'totalSalesCount',
            'totalSalesAmount',
            'customerSalesCount'
        ));
    }
}
