@extends('layouts.app')

@section('title', 'Struk Penjualan: ' . $sale->sale_number . ' - ElectroMart')

@push('styles')
<style>
    @page {
        size: A4 portrait;
        margin: 10mm;
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

        /* Informasi Pelanggan dan Transaksi 2 Kolom Berdampingan Horizontal */
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

        /* Tabel Produk & Item Struk */
        .table-responsive {
            overflow: visible !important;
            display: block !important;
            width: 100% !important;
            margin-bottom: 10px !important;
        }

        table,
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

        .table th,
        .table td {
            padding: 5px 6px !important;
            font-size: 11px !important;
            border: 1px solid #cbd5e1 !important;
            width: auto !important;
        }

        .table thead th {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            font-weight: 700 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .table tfoot td {
            font-size: 11.5px !important;
            font-weight: 700 !important;
            padding: 5px 6px !important;
            background-color: #f8fafc !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .table .text-end {
            white-space: nowrap !important;
        }

        /* Catatan Garansi / Footer Struk */
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
            @if(Auth::user()->isCustomer())
                <a href="{{ route('customer.orders') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Pesanan Saya
                </a>
            @else
                <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Penjualan
                </a>
            @endif

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Cetak Struk (Print)
                </button>

                @if(Auth::user()->isAdmin() && $sale->status === 'completed')
                    <form action="{{ route('sales.cancel', $sale) }}" method="POST" class="d-inline" onsubmit="return confirm('PERINGATAN: Membatalkan transaksi ini akan mengembalikan stok seluruh produk ke gudang. Lanjutkan pembatalan?');">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-x-octagon me-1"></i> Batalkan Transaksi Penjualan
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Receipt / Invoice Card -->
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
                    <div class="text-muted small">Toko Elektronik & Distribusi Perangkat Resmi</div>
                    <div class="text-muted small">Jl. Surya Kencana No. 45, Jakarta Barat &bull; WhatsApp: 0812-3456-7890</div>
                </div>

                <div class="text-md-end">
                    <h3 class="fw-bold text-dark mb-1">STRUK PENJUALAN RESMI</h3>
                    <div class="fw-bold font-monospace text-success fs-5 mb-2">{{ $sale->sale_number }}</div>
                    @if($sale->status === 'completed')
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-6">
                            <i class="bi bi-check-circle-fill me-1"></i> Pembayaran Lunas (Completed)
                        </span>
                    @else
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 fs-6">
                            <i class="bi bi-x-circle-fill me-1"></i> TRANSAKSI DIBATALKAN
                        </span>
                    @endif
                </div>
            </div>

            <!-- Transaction Details Info Box -->
            <div class="invoice-info-row row g-4 mb-4">
                <div class="col-md-6">
                    <div class="invoice-info-box p-3 bg-light rounded-3 h-100">
                        <span class="text-secondary small fw-bold text-uppercase d-block mb-2">Data Pelanggan:</span>
                        @if($sale->customer)
                            <h6 class="fw-bold text-dark mb-1"><i class="bi bi-person-fill text-success me-1"></i>{{ $sale->customer->name }}</h6>
                            @if(!empty($sale->customer->email) && !str_ends_with(strtolower($sale->customer->email), '@customer.local'))
                                <div class="small text-muted mb-1"><i class="bi bi-envelope me-1"></i>{{ $sale->customer->email }}</div>
                            @endif
                            <div class="small text-muted mb-1"><i class="bi bi-telephone me-1"></i>{{ $sale->customer->phone ?? '-' }}</div>
                            <div class="small text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $sale->customer->address ?? '-' }}</div>
                        @else
                            <h6 class="fw-bold text-dark mb-1">Pembeli Umum (Walk-in Customer)</h6>
                            <div class="small text-muted">Transaksi kasir langsung tanpa akun member terdaftar.</div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="invoice-info-box p-3 bg-light rounded-3 h-100">
                        <span class="text-secondary small fw-bold text-uppercase d-block mb-2">Informasi Transaksi:</span>
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Waktu Transaksi:</span>
                            <strong class="text-dark">{{ $sale->sold_at->format('d F Y, H:i') }} WIB</strong>
                        </div>
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Diproses Oleh:</span>
                            <strong class="text-dark">{{ $sale->user->name ?? '-' }} ({{ strtoupper($sale->user->role ?? 'User') }})</strong>
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Metode Pembayaran:</span>
                            <strong class="text-dark">Tunai / Transfer Lunas</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Purchased Items Table -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;" class="text-center">No</th>
                            <th style="width: 140px;">SKU</th>
                            <th>Nama Produk Elektronik & Brand</th>
                            <th>Kategori</th>
                            <th class="text-center" style="width: 100px;">Kuantitas</th>
                            <th class="text-end" style="width: 180px;">Harga Satuan</th>
                            <th class="text-end" style="width: 190px;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $index => $item)
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
                                <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold text-success">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="6" class="text-end fw-bold fs-6">Total Pembayaran:</td>
                            <td class="text-end fw-bold fs-5 text-success">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Cancellation Notice if Cancelled -->
            @if($sale->status === 'cancelled')
                <div class="alert alert-danger mb-0 rounded-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                        <div>
                            <strong>Status: Transaksi Ini Telah Dibatalkan.</strong>
                            <div class="small">Kuantitas stok barang yang tercantum pada struk ini telah dikembalikan secara otomatis ke inventaris gudang.</div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Warranty Note -->
            <div class="invoice-footer-note mt-4 pt-3 border-top text-center text-muted small">
                <i class="bi bi-shield-check text-success me-1"></i>
                Terima kasih atas kunjungan dan kepercayaan Anda berbelanja di ElectroMart. Simpan struk ini sebagai bukti garansi resmi produk.
            </div>

        </div>
    </div>
</div>
@endsection
