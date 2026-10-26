@extends('layouts.app')

@section('title', 'Faktur Pembelian: ' . $purchase->purchase_number . ' - ElectroMart')

@push('styles')
<style>
    @page {
        size: A4 portrait;
        margin: 10mm;
    }

    /* Desain Tabel Invoice Modern & Bersih (Tanpa garis vertikal kaku) */
    .invoice-table {
        border-collapse: collapse !important;
        width: 100% !important;
        background-color: #ffffff;
    }

    .invoice-table thead th {
        background-color: #f8fafc !important;
        color: #475569;
        font-weight: 600;
        font-size: 0.825rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        border-top: 1px solid #e2e8f0 !important;
        border-bottom: 2px solid #e2e8f0 !important;
        border-left: none !important;
        border-right: none !important;
        padding: 10px 14px !important;
        vertical-align: middle !important;
    }

    .invoice-table tbody td {
        padding: 10px 14px !important;
        vertical-align: middle !important;
        border-top: none !important;
        border-bottom: 1px solid #e2e8f0 !important;
        border-left: none !important;
        border-right: none !important;
    }

    .invoice-table tfoot td {
        padding: 12px 14px !important;
        vertical-align: middle !important;
        background-color: #f8fafc !important;
        border-top: 2px solid #e2e8f0 !important;
        border-bottom: 1px solid #e2e8f0 !important;
        border-left: none !important;
        border-right: none !important;
    }

    @media print {
        /* Sembunyikan elemen navigasi, sidebar, navbar atas, tombol aksi, dan footer */
        nav,
        .navbar,
        footer,
        .footer,
        .sidebar,
        #sidebar,
        .d-print-none,
        .btn,
        .alert-dismissible {
            display: none !important;
        }

        /* Reset semua pembungkus utama agar muat 100% dan tidak terdorong ke kanan */
        body {
            background: #ffffff !important;
            background-color: #ffffff !important;
            color: #0f172a !important;
            font-family: 'Plus Jakarta Sans', Arial, sans-serif !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body,
        main,
        main.container,
        .main-content,
        .container,
        .container-fluid,
        .row,
        [class*="col-"],
        .card {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            margin: 0 !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
            border: none !important;
            overflow: visible !important;
            background: transparent !important;
        }

        /* Invoice Card Container */
        .invoice-card {
            border: none !important;
            padding: 0 !important;
            box-shadow: none !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        /* Header Invoice Ringkas */
        .invoice-header {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            align-items: flex-start !important;
            border-bottom: 1px solid #cbd5e1 !important;
            padding-bottom: 10px !important;
            margin-bottom: 12px !important;
        }

        .invoice-header h4,
        .invoice-header h3 {
            font-size: 16px !important;
            margin-bottom: 2px !important;
        }

        .invoice-header .fs-5 {
            font-size: 14px !important;
        }

        .invoice-header .badge {
            font-size: 11px !important;
            padding: 3px 8px !important;
        }

        /* Informasi Distributor dan Transaksi 2 Kolom Berdampingan Horizontal */
        .invoice-info-row {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            align-items: stretch !important;
            gap: 12px !important;
            margin-bottom: 12px !important;
            width: 100% !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .invoice-info-row > [class*="col-"],
        .invoice-info-row > div {
            flex: 0 0 calc(50% - 6px) !important;
            width: calc(50% - 6px) !important;
            max-width: calc(50% - 6px) !important;
        }

        .invoice-info-box {
            font-size: 12px !important;
            line-height: 1.35 !important;
            padding: 8px 12px !important;
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 6px !important;
            height: 100% !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .invoice-info-box h6 {
            font-size: 12.5px !important;
            margin-bottom: 3px !important;
        }

        .invoice-info-box .small,
        .invoice-info-box div,
        .invoice-info-box span,
        .invoice-info-box strong {
            font-size: 11.5px !important;
            line-height: 1.35 !important;
        }

        /* Tabel Produk & Item Faktur */
        .table-responsive {
            overflow: visible !important;
            display: block !important;
            width: 100% !important;
            margin-bottom: 10px !important;
        }

        table.invoice-table,
        .table {
            width: 100% !important;
            max-width: 100% !important;
            table-layout: auto !important;
            border-collapse: collapse !important;
            font-size: 11.5px !important;
            margin-bottom: 0 !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .invoice-table thead th {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            font-weight: 700 !important;
            border-top: 1px solid #cbd5e1 !important;
            border-bottom: 1px solid #cbd5e1 !important;
            border-left: none !important;
            border-right: none !important;
            padding: 6px 8px !important;
            font-size: 11px !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .invoice-table tbody td {
            padding: 6px 8px !important;
            font-size: 11px !important;
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }

        .invoice-table tfoot td {
            font-size: 11.5px !important;
            font-weight: 700 !important;
            padding: 6px 8px !important;
            background-color: #f8fafc !important;
            border-top: 1px solid #cbd5e1 !important;
            border-bottom: 1px solid #cbd5e1 !important;
            border-left: none !important;
            border-right: none !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .invoice-table .text-end {
            white-space: nowrap !important;
        }

        /* Catatan Garansi / Footer Faktur */
        .invoice-footer-note {
            font-size: 10.5px !important;
            margin-top: 8px !important;
            padding-top: 6px !important;
            border-top: 1px solid #e2e8f0 !important;
            text-align: center !important;
            color: #64748b !important;
        }
    }
</style>
@endpush

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
        <div class="card invoice-card border-0 shadow-sm p-4 p-md-5 bg-white">

            <!-- Invoice Header -->
            <div class="invoice-header d-flex flex-column flex-md-row justify-content-between align-items-md-start border-bottom pb-4 mb-4 gap-3">
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
            <div class="invoice-info-row row g-4 mb-4">
                <div class="col-md-6">
                    <div class="invoice-info-box p-3 bg-light rounded-3 h-100">
                        <span class="text-secondary small fw-bold text-uppercase d-block mb-2">Informasi Distributor / Supplier:</span>
                        <h6 class="fw-bold text-dark mb-1">{{ $purchase->supplier->name ?? 'Supplier Dihapus' }}</h6>
                        <div class="small text-muted mb-1"><i class="bi bi-telephone me-1"></i>{{ $purchase->supplier->phone ?? '-' }}</div>
                        <div class="small text-muted mb-1"><i class="bi bi-envelope me-1"></i>{{ $purchase->supplier->email ?? '-' }}</div>
                        <div class="small text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $purchase->supplier->address ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="invoice-info-box p-3 bg-light rounded-3 h-100">
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
                <table class="table invoice-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 5%; text-align: center;">No</th>
                            <th style="width: 13%; white-space: nowrap; font-size: 13px;">SKU</th>
                            <th style="width: 34%;">Nama Produk</th>
                            <th style="width: 16%; white-space: nowrap;">Kategori</th>
                            <th style="width: 8%; text-align: center; white-space: nowrap;">Qty</th>
                            <th style="width: 12%; text-align: right; white-space: nowrap;">Harga Satuan</th>
                            <th style="width: 12%; text-align: right; white-space: nowrap;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->items as $index => $item)
                            <tr>
                                <td style="text-align: center;" class="text-muted small">{{ $index + 1 }}</td>
                                <td style="white-space: nowrap; font-size: 13px;" class="fw-bold font-monospace text-secondary">{{ $item->product->sku ?? '-' }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->product->name ?? 'Produk Diarsipkan' }}</div>
                                    @if(isset($item->product->brand))
                                        <div style="font-size: 12px; color: #64748b;">Brand: {{ $item->product->brand }}</div>
                                    @endif
                                </td>
                                <td style="white-space: nowrap;">
                                    <span class="badge bg-light text-dark border">{{ $item->product->category->name ?? '-' }}</span>
                                </td>
                                <td style="text-align: center; white-space: nowrap;" class="fw-bold">{{ $item->quantity }} unit</td>
                                <td style="text-align: right; white-space: nowrap;">Rp {{ number_format($item->unit_cost, 0, ',', '.') }}</td>
                                <td style="text-align: right; white-space: nowrap;" class="fw-bold text-dark">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" class="text-end fw-bold" style="font-size: 0.95rem; vertical-align: middle;">Grand Total Pembelian:</td>
                            <td class="text-end text-primary fw-bold" style="white-space: nowrap; font-size: 1.15rem; font-weight: 700; vertical-align: middle;">
                                Rp {{ number_format($purchase->total, 0, ',', '.') }}
                            </td>
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
