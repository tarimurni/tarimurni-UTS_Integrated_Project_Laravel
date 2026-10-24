@extends('layouts.app')

@section('title', 'Detail Supplier: ' . $supplier->name . ' - ElectroMart')

@section('content')
<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <span class="badge bg-secondary bg-opacity-10 text-secondary p-2 rounded-3 mb-3">
                    <i class="bi bi-truck fs-4"></i>
                </span>
                <h4 class="fw-bold text-dark mb-1">{{ $supplier->name }}</h4>
                <p class="text-muted small mb-3">{{ $supplier->address ?? 'Alamat tidak dicatat.' }}</p>

                <hr class="my-3">

                <div class="mb-2">
                    <span class="text-muted small d-block">Nomor Telepon:</span>
                    <strong class="text-dark"><i class="bi bi-telephone me-1 text-primary"></i>{{ $supplier->phone }}</strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted small d-block">Email Resmi:</span>
                    <strong class="text-dark"><i class="bi bi-envelope me-1 text-primary"></i>{{ $supplier->email }}</strong>
                </div>
                <div class="mb-3">
                    <span class="text-muted small d-block">Total Transaksi Pembelian:</span>
                    <strong class="text-dark">{{ $supplier->purchases->count() }} kali transaksi</strong>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary btn-sm flex-grow-1">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary btn-sm flex-grow-1">
                            <i class="bi bi-pencil me-1"></i> Ubah
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-receipt text-primary me-2"></i>Riwayat Pembelian Barang dari Supplier Ini</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No. Faktur</th>
                            <th>Tanggal Pembelian</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Total Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supplier->purchases as $purchase)
                            <tr>
                                <td class="ps-4 fw-bold font-monospace text-secondary">{{ $purchase->purchase_number }}</td>
                                <td>{{ $purchase->purchased_at->format('d M Y, H:i') }}</td>
                                <td>
                                    <span class="badge {{ $purchase->status === 'completed' ? 'bg-success' : 'bg-danger' }} px-2 py-1">
                                        {{ ucfirst($purchase->status) }}
                                    </span>
                                </td>
                                <td class="text-end pe-4 fw-bold text-dark">
                                    Rp {{ number_format($purchase->total, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted small">
                                    Belum ada transaksi pembelian barang dari supplier ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
