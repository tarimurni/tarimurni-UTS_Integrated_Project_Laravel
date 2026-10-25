@extends('layouts.app')

@section('title', 'Masuk - ElectroMart')

@section('content')
<div class="row justify-content-center align-items-center py-4 py-md-5">
    <div class="col-md-7 col-lg-5 col-xl-4">
        <!-- Single Clean Login Card -->
        <div class="card border-0 shadow-sm p-4 p-md-4 rounded-4 bg-white">
            <div class="text-center mb-4 pt-2">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary rounded-4 p-3 shadow-sm mb-3">
                    <i class="bi bi-cpu-fill text-white fs-2"></i>
                </div>
                <h3 class="fw-bold text-dark mb-1">Masuk ke ElectroMart</h3>
                <p class="text-muted small mb-0">Masukkan email dan kata sandi akun Anda</p>
            </div>

            <form action="{{ route('login.post') }}" method="POST">
                @csrf

                <!-- Input Email -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold small text-secondary">Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                        <input type="email"
                               class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autofocus
                               placeholder="nama@electromart.test">
                    </div>
                    @error('email')
                        <div class="invalid-feedback d-block small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Input Password -->
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold small text-secondary">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                        <input type="password"
                               class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror"
                               id="password"
                               name="password"
                               required
                               placeholder="Kata sandi akun">
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Checkbox Ingat Saya -->
                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                    <label class="form-check-label small text-muted" for="remember">Ingat Saya</label>
                </div>

                <!-- Tombol Masuk Biru Penuh -->
                <button type="submit" class="btn btn-primary w-100 py-2 fs-6 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk
                </button>
            </form>

            <!-- Link Kembali ke Katalog Toko -->
            <div class="text-center mt-4 pt-3 border-top">
                <a href="{{ route('catalog.index') }}" class="small text-muted text-decoration-none d-inline-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left"></i>
                    <span>Kembali ke Katalog Toko</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
