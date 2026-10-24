@extends('layouts.app')

@section('title', 'Masuk - ElectroMart')

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-md-6 col-lg-5">
        <div class="card p-4 p-md-5 border-0 shadow-sm">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary rounded-3 p-3 shadow-sm mb-3">
                    <i class="bi bi-cpu-fill text-white fs-3"></i>
                </div>
                <h3 class="fw-bold text-dark mb-1">Masuk ke Akun</h3>
                <p class="text-muted small">Silakan masukkan email dan password untuk melanjutkan</p>
            </div>

            <form action="{{ route('login.post') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold small text-secondary">Alamat Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                        <input type="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@electromart.test">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold small text-secondary">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Minimal 8 karakter">
                    </div>
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                    <label class="form-check-label small text-muted" for="remember">Ingat Saya di Perangkat Ini</label>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fs-6">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
                </button>
            </form>

            <div class="text-center mt-4 pt-3 border-top">
                <span class="small text-muted">Belum memiliki akun pelanggan?</span>
                <a href="{{ route('register') }}" class="small fw-bold text-decoration-none ms-1">Daftar Customer Baru</a>
            </div>
        </div>

        <!-- Quick Demo Credentials Box for Testing -->
        <div class="card mt-3 bg-light border-0 shadow-sm p-3">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-shield-check text-primary"></i>
                <span class="fw-bold small text-dark">Akun Demo Pengujian Cepat:</span>
            </div>
            <div class="small text-muted mb-2">Klik tombol di bawah untuk mengisi form login secara otomatis:</div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-sm btn-outline-danger flex-grow-1" onclick="fillCredentials('admin@electromart.test', 'password123')">
                    <i class="bi bi-person-fill-gear me-1"></i> Admin
                </button>
                <button type="button" class="btn btn-sm btn-outline-warning text-dark flex-grow-1" onclick="fillCredentials('staff@electromart.test', 'password123')">
                    <i class="bi bi-person-badge-fill me-1"></i> Staff
                </button>
                <button type="button" class="btn btn-sm btn-outline-success flex-grow-1" onclick="fillCredentials('customer@electromart.test', 'password123')">
                    <i class="bi bi-person-heart me-1"></i> Customer
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function fillCredentials(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
@endpush
