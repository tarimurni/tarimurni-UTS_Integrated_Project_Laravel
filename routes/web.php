<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root Redirect
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

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
