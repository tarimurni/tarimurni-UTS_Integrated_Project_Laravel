@extends('layouts.app')

@section('title', 'Transaksi Penjualan & Kasir - ElectroMart')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-cash-stack text-success me-2"></i>Transaksi Penjualan (Kasir & POS)</h5>
                    <p class="text-muted small mb-0">Riwayat penjualan produk elektronik, kasir POS, dan pemotongan stok otomatis</p>
                </div>
                @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                    <a href="{{ route('sales.create') }}" class="btn btn-success btn-sm px-3">
                        <i class="bi bi-cart-plus-fill me-1"></i> Buka Kasir / Transaksi Baru
                    </a>
                @endif
            </div>

            <div class="card-body px-4 pt-0">
                <!-- Search & Filters -->
                <form action="{{ route('sales.index') }}" method="GET" class="row g-2 mb-3 align-items-center">
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari nomor invoice, pelanggan..." value="{{ request('search') }}">
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
                            <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                        @endif
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>No. Invoice</th>
                                <th>Tanggal & Waktu</th>
                                <th>Pelanggan / Customer</th>
                                <th>Kasir / Petugas</th>
                                <th class="text-center" style="width: 110px;">Item Terjual</th>
                                <th class="text-end">Total Penjualan</th>
                                <th class="text-center" style="width: 120px;">Status</th>
                                <th class="text-end" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $index => $sale)
                                <tr>
                                    <td class="text-muted small">{{ $sales->firstItem() + $index }}</td>
                                    <td>
                                        <a href="{{ route('sales.show', $sale) }}" class="fw-bold font-monospace text-primary text-decoration-none">
                                            {{ $sale->sale_number }}
                                        </a>
                                    </td>
                                    <td class="small text-muted">
                                        {{ $sale->sold_at->format('d M Y, H:i') }}
                                    </td>
                                    <td>
                                        @if($sale->customer)
                                            <div class="fw-semibold text-dark"><i class="bi bi-person-fill text-success me-1"></i>{{ $sale->customer->name }}</div>
                                            <div class="small text-muted">{{ $sale->customer->email }}</div>
                                        @else
                                            <span class="badge bg-light text-secondary border">Pembeli Umum (Walk-in)</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="small text-dark fw-medium">{{ $sale->user->name ?? '-' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-2 py-1">{{ $sale->items_count }} item</span>
                                    </td>
                                    <td class="text-end fw-bold text-success">
                                        Rp {{ number_format($sale->total, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        @if($sale->status === 'completed')
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
                                        <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline-secondary btn-sm" title="Lihat Struk Penjualan">
                                            <i class="bi bi-receipt me-1"></i> Struk
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                        Belum ada riwayat transaksi penjualan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $sales->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
