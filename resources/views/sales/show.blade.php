@extends('layouts.app')

@section('title', 'Struk Penjualan: ' . $sale->sale_number . ' - ElectroMart')

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
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#snapModal">
                    <i class="bi bi-credit-card-2-front me-1"></i> Buka Layanan ElectroPay (Snap)
                </button>

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
                            <i class="bi bi-check-circle-fill me-1"></i> LUNAS (PAID)
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
                            @if(($sale->user && $sale->user->isCustomer()) || ($sale->user->role ?? '') === 'customer')
                                <span>Kasir / Kanal:</span>
                                <strong class="text-dark">Checkout Mandiri (Katalog Online)</strong>
                            @else
                                <span>Diproses Oleh:</span>
                                <strong class="text-dark">{{ $sale->user->name ?? '-' }} ({{ strtoupper($sale->user->role ?? 'Petugas') }})</strong>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center small text-muted mb-1">
                            <span>Metode Pembayaran:</span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                <i class="bi bi-credit-card me-1"></i>{{ $sale->payment_method_label }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Referensi Gateway:</span>
                            <span class="font-monospace text-dark fw-semibold">{{ $sale->payment_reference ?? ('PG-MID-' . date('Ymd') . '-' . str_pad($sale->id, 5, '0', STR_PAD_LEFT)) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center small text-muted mb-1">
                            <span>Status Pembayaran:</span>
                            @if($sale->payment_status === 'refunded' || $sale->status === 'cancelled')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>REFUNDED
                                </span>
                            @elseif($sale->payment_status === 'paid' || $sale->status === 'completed')
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <i class="bi bi-check-circle-fill me-1"></i>LUNAS (PAID)
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                    <i class="bi bi-clock-history me-1"></i>UNPAID
                                </span>
                            @endif
                        </div>
                        @if($sale->paid_at)
                            <div class="d-flex justify-content-between small text-muted">
                                <span>Waktu Verifikasi:</span>
                                <strong class="text-dark">{{ $sale->paid_at->format('d/m/Y H:i') }} WIB</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Purchased Items Table -->
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
                        @foreach($sale->items as $index => $item)
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
                                <td style="text-align: right; white-space: nowrap;">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td style="text-align: right; white-space: nowrap;" class="fw-bold text-success">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" class="text-end fw-bold" style="font-size: 0.95rem; vertical-align: middle;">Total Pembayaran:</td>
                            <td class="text-end text-success fw-bold" style="white-space: nowrap; font-size: 1.15rem; font-weight: 700; vertical-align: middle;">
                                Rp {{ number_format($sale->total, 0, ',', '.') }}
                            </td>
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

<!-- ElectroPay Midtrans Snap Modal (Interactive Popup) -->
<div class="modal fade d-print-none" id="snapModal" tabindex="-1" aria-labelledby="snapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Modal Header with Midtrans Snap Theme -->
            <div class="p-3 px-4 text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #0d9488 100%);">
                <div class="d-flex align-items-center gap-2">
                    <span class="bg-primary text-white p-2 rounded-3 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-lightning-charge-fill fs-6"></i>
                    </span>
                    <div>
                        <h6 class="fw-bold mb-0 text-white">ElectroPay <span class="badge bg-white text-dark ms-1" style="font-size: 0.6rem;">Midtrans Snap</span></h6>
                        <div class="text-white-50" style="font-size: 0.72rem;">Ref: {{ $sale->payment_reference ?? ('PG-MID-' . date('Ymd') . '-' . str_pad($sale->id, 5, '0', STR_PAD_LEFT)) }}</div>
                    </div>
                </div>
                <div class="text-end d-flex align-items-center gap-3">
                    <div>
                        <div class="text-white-50" style="font-size: 0.68rem;">Total Tagihan:</div>
                        <div class="fw-bold text-warning font-monospace fs-6">Rp {{ number_format($sale->total, 0, ',', '.') }}</div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <div class="modal-body p-4 bg-light">
                <!-- Status Notice -->
                @if($sale->payment_status === 'paid')
                    <div class="alert alert-success border-success-subtle d-flex align-items-center justify-content-between mb-3 py-2 px-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                            <div class="small">
                                <strong>LUNAS:</strong> Pembayaran telah terverifikasi via <strong>{{ $sale->payment_method_label }}</strong>.
                            </div>
                        </div>
                        <span class="badge bg-success text-white" style="font-size: 0.65rem;">PAID</span>
                    </div>
                @endif

                <!-- Nav Tabs -->
                <ul class="nav nav-pills nav-fill bg-white p-1 rounded-3 border mb-3 shadow-sm" id="snapTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link py-2 small {{ $sale->payment_method === 'qris' || empty($sale->payment_method) ? 'active' : '' }}" id="snap-qris-tab" data-bs-toggle="pill" data-bs-target="#snap-tab-qris" type="button" role="tab">
                            <i class="bi bi-qr-code-scan me-1 text-danger"></i> QRIS Instant
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link py-2 small {{ in_array($sale->payment_method, ['va_bca', 'va_bsi', 'va_mandiri']) ? 'active' : '' }}" id="snap-va-tab" data-bs-toggle="pill" data-bs-target="#snap-tab-va" type="button" role="tab">
                            <i class="bi bi-bank2 me-1 text-primary"></i> Virtual Account
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link py-2 small {{ $sale->payment_method === 'ewallet' ? 'active' : '' }}" id="snap-ewallet-tab" data-bs-toggle="pill" data-bs-target="#snap-tab-ewallet" type="button" role="tab">
                            <i class="bi bi-phone-fill me-1 text-info"></i> E-Wallet
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link py-2 small {{ $sale->payment_method === 'cash' ? 'active' : '' }}" id="snap-cash-tab" data-bs-toggle="pill" data-bs-target="#snap-tab-cash" type="button" role="tab">
                            <i class="bi bi-cash-stack me-1 text-success"></i> Tunai Kasir
                        </button>
                    </li>
                </ul>

                <!-- Tab Contents -->
                <div class="tab-content">
                    <!-- QRIS -->
                    <div class="tab-pane fade {{ $sale->payment_method === 'qris' || empty($sale->payment_method) ? 'show active' : '' }}" id="snap-tab-qris" role="tabpanel">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-5 text-center">
                                <div class="bg-white p-3 rounded-3 shadow-sm border d-inline-block">
                                    <div class="bg-danger text-white py-1 px-2 rounded-2 fw-bold text-center mb-1" style="font-size: 0.72rem;">
                                        <i class="bi bi-qr-code me-1"></i> QRIS STANDAR NASIONAL
                                    </div>
                                    <div class="fw-bold text-dark small" style="font-size: 0.75rem;">ELECTROMART OFFICIAL STORE</div>
                                    <div class="text-muted" style="font-size: 0.65rem;">NMID: ID1020261026001</div>

                                    <!-- QR Vector -->
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 160 160" width="140" height="140" class="my-2">
                                        <rect x="10" y="10" width="40" height="40" fill="#000" rx="3" />
                                        <rect x="16" y="16" width="28" height="28" fill="#fff" rx="2" />
                                        <rect x="22" y="22" width="16" height="16" fill="#000" rx="2" />

                                        <rect x="110" y="10" width="40" height="40" fill="#000" rx="3" />
                                        <rect x="116" y="16" width="28" height="28" fill="#fff" rx="2" />
                                        <rect x="122" y="22" width="16" height="16" fill="#000" rx="2" />

                                        <rect x="10" y="110" width="40" height="40" fill="#000" rx="3" />
                                        <rect x="16" y="116" width="28" height="28" fill="#fff" rx="2" />
                                        <rect x="22" y="122" width="16" height="16" fill="#000" rx="2" />

                                        <circle cx="80" cy="80" r="18" fill="#0d9488" />
                                        <path d="M77 73 L85 80 L77 87" stroke="#ffffff" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>

                                    <div class="fw-bold text-danger small">Rp {{ number_format($sale->total, 0, ',', '.') }}</div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <h6 class="fw-bold text-dark small mb-2"><i class="bi bi-camera me-1 text-danger"></i> Scan QRIS via Aplikasi Favorit Anda:</h6>
                                <p class="text-muted small mb-2" style="font-size: 0.78rem;">
                                    Buka BCA Mobile, Livin' by Mandiri, BRImo, BSI Mobile, GoPay, OVO, atau DANA, lalu arahkan kamera ke QRIS.
                                </p>
                                <form action="{{ route('sales.payment.simulate', $sale) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="payment_method" value="qris">
                                    <button type="submit" class="btn btn-danger btn-sm w-100 py-2 fw-semibold">
                                        <i class="bi bi-check-circle-fill me-1"></i> Simulasikan Pembayaran QRIS Berhasil (Callback Lunas)
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- VA -->
                    <div class="tab-pane fade {{ in_array($sale->payment_method, ['va_bca', 'va_bsi', 'va_mandiri']) ? 'show active' : '' }}" id="snap-tab-va" role="tabpanel">
                        <div class="bg-white p-3 rounded-3 border text-center mb-3">
                            <span class="badge bg-primary-subtle text-primary mb-1">{{ $sale->payment_method_label }}</span>
                            <div class="text-muted small" style="font-size: 0.72rem;">Nomor Virtual Account 16 Digit:</div>
                            <div class="d-flex justify-content-center align-items-center gap-2 my-2">
                                <span class="fs-4 fw-bold font-monospace text-dark" id="modalVaDisplay">{{ $sale->virtual_account_number }}</span>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="copyModalVA()">
                                    <i class="bi bi-clipboard me-1"></i> Salin
                                </button>
                            </div>
                            <div id="modalCopyAlert" class="small text-success fw-semibold" style="display: none; font-size: 0.75rem;">
                                <i class="bi bi-check-circle-fill me-1"></i> Nomor Virtual Account disalin ke clipboard!
                            </div>
                            <div class="text-muted small" style="font-size: 0.72rem;">
                                Nominal: <strong>Rp {{ number_format($sale->total, 0, ',', '.') }}</strong> (Otomatis tanpa kode unik)
                            </div>
                        </div>

                        <form action="{{ route('sales.payment.simulate', $sale) }}" method="POST">
                            @csrf
                            <input type="hidden" name="payment_method" value="{{ in_array($sale->payment_method, ['va_bca', 'va_bsi', 'va_mandiri']) ? $sale->payment_method : 'va_bca' }}">
                            <button type="submit" class="btn btn-primary btn-sm w-100 py-2 fw-semibold">
                                <i class="bi bi-check-circle-fill me-1"></i> Simulasikan Pembayaran VA Berhasil (Callback Lunas)
                            </button>
                        </form>
                    </div>

                    <!-- E-WALLET -->
                    <div class="tab-pane fade {{ $sale->payment_method === 'ewallet' ? 'show active' : '' }}" id="snap-tab-ewallet" role="tabpanel">
                        <div class="bg-white p-3 rounded-3 border text-center mb-3">
                            <i class="bi bi-phone-vibrate text-info fs-2 d-block mb-1"></i>
                            <h6 class="fw-bold text-dark small mb-1">Notifikasi Pembayaran E-Wallet Terkirim</h6>
                            <p class="text-muted small mb-0" style="font-size: 0.78rem;">
                                Silakan buka aplikasi GoPay / OVO / DANA di ponsel Anda untuk menyetujui transaksi Rp {{ number_format($sale->total, 0, ',', '.') }}.
                            </p>
                        </div>
                        <form action="{{ route('sales.payment.simulate', $sale) }}" method="POST">
                            @csrf
                            <input type="hidden" name="payment_method" value="ewallet">
                            <button type="submit" class="btn btn-info text-white btn-sm w-100 py-2 fw-semibold">
                                <i class="bi bi-check-circle-fill me-1"></i> Simulasikan Konfirmasi E-Wallet Berhasil
                            </button>
                        </form>
                    </div>

                    <!-- CASH -->
                    <div class="tab-pane fade {{ $sale->payment_method === 'cash' ? 'show active' : '' }}" id="snap-tab-cash" role="tabpanel">
                        <div class="bg-white p-3 rounded-3 border text-center mb-3">
                            <i class="bi bi-cash-stack text-success fs-2 d-block mb-1"></i>
                            <h6 class="fw-bold text-dark small mb-1">Pembayaran Kasir Tunai</h6>
                            <p class="text-muted small mb-0" style="font-size: 0.78rem;">
                                Pembayaran tunai fisik sejumlah Rp {{ number_format($sale->total, 0, ',', '.') }} diterima langsung di meja kasir.
                            </p>
                        </div>
                        <form action="{{ route('sales.payment.simulate', $sale) }}" method="POST">
                            @csrf
                            <input type="hidden" name="payment_method" value="cash">
                            <button type="submit" class="btn btn-success btn-sm w-100 py-2 fw-semibold">
                                <i class="bi bi-check-circle me-1"></i> Konfirmasi Pembayaran Kasir Lunas
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-white d-flex justify-content-between py-2 px-4">
                <a href="{{ route('sales.payment', $sale) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Buka Layar Penuh ElectroPay
                </a>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('open_snap_modal'))
            const snapModalElement = document.getElementById('snapModal');
            if (snapModalElement) {
                const snapModal = new bootstrap.Modal(snapModalElement);
                snapModal.show();
            }
        @endif
    });

    function copyModalVA() {
        const vaText = document.getElementById('modalVaDisplay').innerText.replace(/\s+/g, '');
        navigator.clipboard.writeText(vaText).then(function() {
            const alertBox = document.getElementById('modalCopyAlert');
            alertBox.style.display = 'block';
            setTimeout(() => { alertBox.style.display = 'none'; }, 3000);
        });
    }
</script>
@endpush
