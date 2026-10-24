@extends('layouts.app')

@section('title', 'Faktur Pembelian: ' . $purchase->purchase_number . ' - ElectroMart')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-10">

        <!-- Top Actions Bar -->
        <div class="d-flex justify-content-between align-items-center mb-3 d-print-none">
            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Pembelian
            </a>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Cetak Faktur (Print)
                </button>

                @if(Auth::user()->isAdmin() && $purchase->status === 'completed')
                    <form action="{{ route('purchases.cancel', $purchase) }}" method="POST" class="d-inline" onsubmit="return confirm('PERINGATAN: Membatalkan faktur ini akan menarik kembali (mengurangi) stok produk yang sudah masuk ke gudang. Lanjutkan pembatalan?');">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-x-octagon me-1"></i> Batalkan Faktur Pembelian
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Invoice Card -->
        <div class="card border-0 shadow-sm p-4 p-md-5 bg-white">

            <!-- Invoice Header -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start border-bottom pb-4 mb-4 gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="d-inline-flex align-items-center justify-content-center bg-primary rounded-3 p-2 shadow-sm">
                            <i class="bi bi-cpu-fill text-white fs-4"></i>
                        </span>
                        <h4 class="fw-bold mb-0 text-dark">Electro<span class="text-primary">Mart</span></h4>
                    </div>
                    <div class="text-muted small">Sistem Manajemen Inventaris & Penjualan Elektronik</div>
                    <div class="text-muted small">Jl. Surya Kencana No. 45, Jakarta Barat &bull; Telp: (021) 555-8888</div>
                </div>

                <div class="text-md-end">
                    <h3 class="fw-bold text-dark mb-1">FAKTUR PEMBELIAN</h3>
                    <div class="fw-bold font-monospace text-primary fs-5 mb-2">{{ $purchase->purchase_number }}</div>
                    @if($purchase->status === 'completed')
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-6">
                            <i class="bi bi-check-circle-fill me-1"></i> Transaksi Selesai
                        </span>
                    @else
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 fs-6">
                            <i class="bi bi-x-circle-fill me-1"></i> DIBATALKAN (CANCELLED)
                        </span>
                    @endif
                </div>
            </div>

            <!-- Transaction Details Info Box -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 h-100">
                        <span class="text-secondary small fw-bold text-uppercase d-block mb-2">Informasi Distributor / Supplier:</span>
                        <h6 class="fw-bold text-dark mb-1">{{ $purchase->supplier->name ?? 'Supplier Dihapus' }}</h6>
                        <div class="small text-muted mb-1"><i class="bi bi-telephone me-1"></i>{{ $purchase->supplier->phone ?? '-' }}</div>
                        <div class="small text-muted mb-1"><i class="bi bi-envelope me-1"></i>{{ $purchase->supplier->email ?? '-' }}</div>
                        <div class="small text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $purchase->supplier->address ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 h-100">
                        <span class="text-secondary small fw-bold text-uppercase d-block mb-2">Detail Transaksi Masuk:</span>
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Tanggal Pembelian:</span>
                            <strong class="text-dark">{{ $purchase->purchased_at->format('d F Y, H:i') }} WIB</strong>
                        </div>
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Petugas Pencatat:</span>
                            <strong class="text-dark">{{ $purchase->user->name ?? '-' }} ({{ strtoupper($purchase->user->role ?? 'Petugas') }})</strong>
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Waktu Input Sistem:</span>
                            <strong class="text-dark">{{ $purchase->created_at->format('d/m/Y H:i') }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Purchase Items Table -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;" class="text-center">No</th>
                            <th style="width: 140px;">SKU Produk</th>
                            <th>Nama Produk Elektronik & Brand</th>
                            <th>Kategori</th>
                            <th class="text-center" style="width: 100px;">Kuantitas</th>
                            <th class="text-end" style="width: 180px;">Biaya Satuan</th>
                            <th class="text-end" style="width: 190px;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->items as $index => $item)
                            <tr>
                                <td class="text-center text-muted small">{{ $index + 1 }}</td>
                                <td class="fw-bold font-monospace text-secondary small">{{ $item->product->sku ?? '-' }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->product->name ?? 'Produk Diarsipkan' }}</div>
                                    @if(isset($item->product->brand))
                                        <div class="small text-muted">Brand: {{ $item->product->brand }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $item->product->category->name ?? '-' }}</span>
                                </td>
                                <td class="text-center fw-bold fs-6">{{ $item->quantity }} unit</td>
                                <td class="text-end">Rp {{ number_format($item->unit_cost, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold text-dark">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="6" class="text-end fw-bold fs-6">Grand Total Pembelian:</td>
                            <td class="text-end fw-bold fs-5 text-primary">Rp {{ number_format($purchase->total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Cancellation Notice if Cancelled -->
            @if($purchase->status === 'cancelled')
                <div class="alert alert-danger mb-0 rounded-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                        <div>
                            <strong>Status: Transaksi Ini Telah Dibatalkan.</strong>
                            <div class="small">Kuantitas stok barang yang tercantum di atas telah otomatis dikurangi kembali dari stok gudang.</div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
