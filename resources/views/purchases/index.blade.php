@extends('layouts.app')

@section('title', 'Riwayat Pembelian Barang Masuk - ElectroMart')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-arrow-down-left-circle text-primary me-2"></i>Transaksi Pembelian Barang Masuk (Restock)</h5>
                    <p class="text-muted small mb-0">Catatan faktur pembelian produk elektronik dari supplier resmi beserta penambahan stok otomatis</p>
                </div>
                <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm px-3">
                    <i class="bi bi-plus-circle me-1"></i> Catat Pembelian Baru
                </a>
            </div>

            <div class="card-body px-4 pt-0">
                <!-- Search & Filters -->
                <form action="{{ route('purchases.index') }}" method="GET" class="row g-2 mb-3 align-items-center">
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari nomor PO, supplier..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light text-muted small">Dari</span>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" title="Tanggal Mulai">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light text-muted small">Sampai</span>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" title="Tanggal Selesai">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan (Cancelled)</option>
                        </select>
                    </div>
                    <div class="col-auto ms-auto d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-funnel me-1"></i>Filter</button>
                        @if(request()->anyFilled(['search', 'start_date', 'end_date', 'status']))
                            <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                        @endif
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>No. Faktur PO</th>
                                <th>Tanggal Transaksi</th>
                                <th>Distributor / Supplier</th>
                                <th>Petugas Input</th>
                                <th class="text-center" style="width: 110px;">Item Barang</th>
                                <th class="text-end">Total Pembelian</th>
                                <th class="text-center" style="width: 120px;">Status</th>
                                <th class="text-end" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchases as $index => $purchase)
                                <tr>
                                    <td class="text-muted small">{{ $purchases->firstItem() + $index }}</td>
                                    <td>
                                        <a href="{{ route('purchases.show', $purchase) }}" class="fw-bold font-monospace text-primary text-decoration-none">
                                            {{ $purchase->purchase_number }}
                                        </a>
                                    </td>
                                    <td class="small text-muted">
                                        {{ $purchase->purchased_at->format('d M Y, H:i') }}
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $purchase->supplier->name ?? '-' }}</div>
                                        <div class="small text-muted">{{ $purchase->supplier->phone ?? '' }}</div>
                                    </td>
                                    <td>
                                        <span class="small text-dark fw-medium">{{ $purchase->user->name ?? '-' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-2 py-1">{{ $purchase->items_count }} jenis</span>
                                    </td>
                                    <td class="text-end fw-bold text-dark">
                                        Rp {{ number_format($purchase->total, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        @if($purchase->status === 'completed')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                <i class="bi bi-check-circle me-1"></i>Selesai
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                                <i class="bi bi-x-circle me-1"></i>Dibatalkan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-outline-secondary btn-sm" title="Lihat Faktur Detail">
                                            <i class="bi bi-receipt me-1"></i> Faktur
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                        Belum ada riwayat transaksi pembelian barang.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $purchases->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
