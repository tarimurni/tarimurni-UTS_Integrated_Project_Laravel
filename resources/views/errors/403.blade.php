@extends('layouts.app')

@section('title', '403 Forbidden - Akses Ditolak')

@section('content')
<div class="row justify-content-center py-5">
    <div class="col-md-7 col-lg-6 text-center">
        <div class="card p-4 p-md-5 border-0 shadow-sm">
            <div class="mb-4">
                <span class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded-circle p-4">
                    <i class="bi bi-shield-lock-fill" style="font-size: 3.5rem;"></i>
                </span>
            </div>
            <h1 class="fw-bold text-dark mb-2">403 Forbidden</h1>
            <h4 class="text-danger fw-semibold mb-3">Akses Ditolak</h4>
            <p class="text-muted mb-4">
                {{ $exception->getMessage() ?: 'Maaf, Anda tidak memiliki hak akses (role) yang diizinkan untuk membuka halaman ini.' }}
            </p>
            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-primary px-4 py-2">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
