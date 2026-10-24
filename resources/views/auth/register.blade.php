@extends('layouts.app')

@section('title', 'Daftar Customer Baru - ElectroMart')

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-md-7 col-lg-6">
        <div class="card p-4 p-md-5 border-0 shadow-sm">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-success rounded-3 p-3 shadow-sm mb-3">
                    <i class="bi bi-person-plus-fill text-white fs-3"></i>
                </div>
                <h3 class="fw-bold text-dark mb-1">Pendaftaran Pelanggan Baru</h3>
                <p class="text-muted small">Buat akun Customer untuk menikmati belanja elektronik mudah di ElectroMart</p>
            </div>

            <form action="{{ route('register.post') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold small text-secondary">Nama Lengkap <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="Contoh: Andi Pratama">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold small text-secondary">Alamat Email <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                        <input type="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="nama@email.com">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label fw-semibold small text-secondary">Nomor Telepon / WhatsApp</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-telephone text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0 @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold small text-secondary">Role Akun Otomatis</label>
                        <input type="text" class="form-control bg-light" value="Customer (Pelanggan)" readonly disabled>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label fw-semibold small text-secondary">Alamat Lengkap Pengiriman</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2" placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kota...">{{ old('address') }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label fw-semibold small text-secondary">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                            <input type="password" class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Min. 8 karakter">
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="password_confirmation" class="form-label fw-semibold small text-secondary">Ulangi Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-lock text-muted"></i></span>
                            <input type="password" class="form-control border-start-0 ps-0" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi password">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fs-6">
                    <i class="bi bi-check2-circle me-1"></i> Selesaikan Pendaftaran
                </button>
            </form>

            <div class="text-center mt-4 pt-3 border-top">
                <span class="small text-muted">Sudah punya akun terdaftar?</span>
                <a href="{{ route('login') }}" class="small fw-bold text-decoration-none ms-1">Masuk ke Sini</a>
            </div>
        </div>
    </div>
</div>
@endsection
