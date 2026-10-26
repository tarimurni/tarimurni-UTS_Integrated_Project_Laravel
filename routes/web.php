<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root and Public Catalog Routes (Direct access for customers without login)
Route::get('/', [CatalogController::class, 'index'])->name('home');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::post('/catalog/checkout', [CatalogController::class, 'checkout'])->name('catalog.checkout');

// Guest Authentication Routes (Single clean login for Admin & Staff)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // Portal Login for Backoffice
    Route::get('/portal/login', [AuthController::class, 'showPortalLoginForm'])->name('portal.login');
    Route::post('/portal/login', [AuthController::class, 'login'])->name('portal.login.post');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Authenticated Routes (Requires Login)
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Central Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


    // ==========================================
    // Master Data & Transaction Mutating (Admin Only)
    // ==========================================
    Route::middleware('role:admin')->group(function () {
        // Products mutating
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Categories mutating
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Suppliers mutating
        Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

        // User Management (Admin Only)
        Route::resource('users', UserController::class)->except(['show']);

        // Transaction Cancellations (Admin Only)
        Route::post('/purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])->name('purchases.cancel');
        Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');
    });

    // ==========================================
    // Master Data & POS Sales Routes (Admin & Staff)
    // ==========================================
    Route::middleware('role:admin,staff')->group(function () {
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

        Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');

        // Purchases (Restock)
        Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
        Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
        Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');

        // Sales (Kasir & POS)
        Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
        Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
        Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    });

    // Shared Invoice / Receipt Viewer & Payment Gateway (Accessible by Admin/Staff, and Customer for their own orders)
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::get('/sales/{sale}/payment', [SaleController::class, 'paymentGate'])->name('sales.payment');
    Route::post('/sales/{sale}/payment/simulate', [SaleController::class, 'simulatePayment'])->name('sales.payment.simulate');

    // ==========================================
    // Customer Orders History (Customer Only)
    // ==========================================
    Route::middleware('role:customer')->group(function () {
        Route::get('/customer/orders', [CatalogController::class, 'orders'])->name('customer.orders');
    });

    // ==========================================
    // Role-Based Authorization Route Test Areas
    // ==========================================

    // Route only for Admin
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/test-area', function () {
            return redirect()->route('dashboard')
                ->with('success', 'Verifikasi Berhasil: Anda memiliki akses ke area khusus ADMIN.');
        })->name('test.admin');
    });

    // Route for Admin and Staff
    Route::middleware('role:admin,staff')->prefix('staff')->group(function () {
        Route::get('/test-area', function () {
            return redirect()->route('dashboard')
                ->with('success', 'Verifikasi Berhasil: Anda memiliki akses ke area STAFF & ADMIN.');
        })->name('test.staff');
    });

    // Route only for Customer
    Route::middleware('role:customer')->prefix('customer')->group(function () {
        Route::get('/test-area', function () {
            return redirect()->route('dashboard')
                ->with('success', 'Verifikasi Berhasil: Anda memiliki akses ke area khusus CUSTOMER.');
        })->name('test.customer');
    });
});
