<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show main login form (defaults to customer portal or specified portal).
     */
    public function showLoginForm(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $portal = $request->query('portal', 'customer');
        if (! in_array($portal, ['customer', 'staff', 'admin'], true)) {
            $portal = 'customer';
        }

        return view('auth.login', compact('portal'));
    }

    /**
     * Show Customer passwordless portal.
     */
    public function showCustomerLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login', ['portal' => 'customer']);
    }

    /**
     * Show Staff portal.
     */
    public function showStaffLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login', ['portal' => 'staff']);
    }

    /**
     * Show Administrator portal.
     */
    public function showAdminLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login', ['portal' => 'admin']);
    }

    /**
     * Handle passwordless customer login.
     * Looks up existing customer by name (or phone), or creates a new one automatically.
     */
    public function loginCustomer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:25'],
        ], [
            'name.required' => 'Nama lengkap pelanggan wajib diisi untuk mulai belanja.',
        ]);

        $name = trim($validated['name']);
        $phone = isset($validated['phone']) && trim($validated['phone']) !== '' ? trim($validated['phone']) : null;

        // Search for existing customer account with matching name (exact or prefix) or phone
        $customer = User::where('role', 'customer')
            ->where(function ($query) use ($name, $phone) {
                $query->where('name', $name)
                    ->orWhere('name', 'like', $name . '%');
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

            $customer = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(\Illuminate\Support\Str::random(24)),
                'role' => 'customer',
                'phone' => $phone,
            ]);
        } else {
            // Update phone if it was empty previously and provided now
            if (! empty($phone) && empty($customer->phone)) {
                $customer->update(['phone' => $phone]);
            }
        }

        Auth::login($customer, true);
        $request->session()->regenerate();

        return redirect()->route('catalog.index')
            ->with('success', "Halo {$customer->name}, selamat datang di ElectroMart! Silakan pilih perangkat elektronik yang Anda butuhkan.");
    }

    /**
     * Handle Staff login with email and password.
     * Validates that authenticated user has role === 'staff'.
     */
    public function loginStaff(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Alamat email Staff wajib diisi.',
            'email.email' => 'Format email Staff tidak valid.',
            'password.required' => 'Password Staff wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            /** @var User $user */
            $user = Auth::user();

            if (! $user->isStaff()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login.staff')
                    ->withErrors(['email' => 'Akses ditolak. Portal ini khusus untuk akun Staff Gudang & Kasir.'])
                    ->withInput($request->only('email'));
            }

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', "Selamat datang, {$user->name}! Anda berhasil masuk ke Portal Staff.");
        }

        return redirect()->route('login.staff')
            ->withErrors(['email' => 'Kombinasi email atau password Staff tidak cocok.'])
            ->withInput($request->only('email'));
    }

    /**
     * Handle Admin login with email and password.
     * Validates that authenticated user has role === 'admin'.
     */
    public function loginAdmin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Alamat email Administrator wajib diisi.',
            'email.email' => 'Format email Administrator tidak valid.',
            'password.required' => 'Password Administrator wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            /** @var User $user */
            $user = Auth::user();

            if (! $user->isAdmin()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login.admin')
                    ->withErrors(['email' => 'Akses ditolak. Portal ini khusus untuk Administrator / Owner.'])
                    ->withInput($request->only('email'));
            }

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', "Selamat datang Administrator, {$user->name}!");
        }

        return redirect()->route('login.admin')
            ->withErrors(['email' => 'Kombinasi email atau password Administrator tidak cocok.'])
            ->withInput($request->only('email'));
    }

    /**
     * General login authentication (for testing and backward compatibility).
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();

            $targetRoute = $user->isCustomer() ? route('catalog.index') : route('dashboard');

            return redirect()->intended($targetRoute)
                ->with('success', "Selamat datang kembali, {$user->name}!");
        }

        return back()
            ->withErrors(['email' => 'Kombinasi email dan password yang Anda masukkan tidak cocok.'])
            ->onlyInput('email');
    }

    /**
     * Show customer registration form.
     */
    public function showRegisterForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    /**
     * Handle customer registration.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', "Pendaftaran berhasil! Selamat datang di ElectroMart, {$user->name}.");
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
