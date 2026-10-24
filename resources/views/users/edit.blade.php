@extends('layouts.app')

@section('title', 'Ubah Pengguna: ' . $user->name . ' - ElectroMart')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>Ubah Data Pengguna</h5>
                <p class="text-muted small mb-0">Perbarui profil atau role pengguna '{{ $user->name }}'</p>
            </div>

            <div class="card-body p-4 pt-0">
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <label for="name" class="form-label fw-semibold small text-secondary">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label for="role" class="form-label fw-semibold small text-secondary">Penugasan Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required {{ $user->id === Auth::id() ? 'disabled' : '' }}>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin (Akses Penuh)</option>
                                <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>Staff (Gudang & Kasir)</option>
                                <option value="customer" {{ old('role', $user->role) === 'customer' ? 'selected' : '' }}>Customer (Pelanggan)</option>
                            </select>
                            @if($user->id === Auth::id())
                                <input type="hidden" name="role" value="admin">
                                <div class="form-text text-muted small">Anda tidak dapat mengubah role akun sendiri.</div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label fw-semibold small text-secondary">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label fw-semibold small text-secondary">Nomor Telepon</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label fw-semibold small text-secondary">Alamat Tempat Tinggal</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2">{{ old('address', $user->address) }}</textarea>
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-4">
                        <div class="fw-semibold small text-dark mb-2"><i class="bi bi-shield-lock me-1"></i>Ubah Password (Kosongkan jika tidak ingin mengubah password)</div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label for="password" class="form-label small text-secondary">Password Baru</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Minimal 8 karakter">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="password_confirmation" class="form-label small text-secondary">Ulangi Password Baru</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password baru">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check2-circle me-1"></i> Perbarui Pengguna
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
