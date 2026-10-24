@extends('layouts.app')

@section('title', 'Dashboard - ElectroMart')

@section('content')
<div class="row g-4">

    <!-- Welcome Header Banner -->
    <div class="col-12">
        <div class="card p-4 border-0 shadow-sm bg-white">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle" style="width: 58px; height: 58px;">
                        <i class="bi bi-person-circle fs-2"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h4 class="fw-bold mb-0 text-dark">Halo, {{ $user->name }}!</h4>
                            @if($user->isAdmin())
                                <span class="badge badge-role-admin text-uppercase px-2 py-1">Admin</span>
                            @elseif($user->isStaff())
                                <span class="badge badge-role-staff text-uppercase px-2 py-1">Staff Gudang & Kasir</span>
                            @else
                                <span class="badge badge-role-customer text-uppercase px-2 py-1">Customer</span>
                            @endif
                        </div>
                        <p class="text-muted small mb-0">
                            Masuk sebagai <strong>{{ $user->email }}</strong> &bull; Sistem Inventaris & Penjualan Elektronik
                        </p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    @if($user->isCustomer())
                        <a href="{{ route('catalog.index') }}" class="btn btn-primary btn-sm px-3">
                            <i class="bi bi-cart3 me-1"></i> Belanja di Katalog
                        </a>
                    @elseif($user->isAdmin() || $user->isStaff())
                        <a href="{{ route('sales.create') }}" class="btn btn-success btn-sm px-3">
                            <i class="bi bi-cart-check me-1"></i> Buka Kasir (POS)
                        </a>
                        <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm px-3">
                            <i class="bi bi-arrow-down-left-circle me-1"></i> Catat Pembelian
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($user->isAdmin() || $user->isStaff())
        <!-- Row 1: Real-Time Aggregate KPI Cards for Admin & Staff -->
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 border-0 shadow-sm bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Produk Elektronik Aktif</span>
                    <span class="badge bg-primary bg-opacity-10 text-primary p-2 rounded-3">
                        <i class="bi bi-box-seam fs-5"></i>
                    </span>
                </div>
                <h3 class="fw-bold text-dark mb-1">{{ $totalProducts }}</h3>
                <span class="small text-muted">{{ $totalCategories }} kategori &bull; {{ $totalSuppliers }} supplier</span>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 border-0 shadow-sm bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Penjualan Hari Ini</span>
                    <span class="badge bg-success bg-opacity-10 text-success p-2 rounded-3">
                        <i class="bi bi-cash-stack fs-5"></i>
                    </span>
                </div>
                <h3 class="fw-bold text-success mb-1">Rp {{ number_format($salesToday, 0, ',', '.') }}</h3>
                <span class="small text-muted">{{ now()->translatedFormat('d F Y') }}</span>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 border-0 shadow-sm bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Penjualan Bulan Ini</span>
                    <span class="badge bg-info bg-opacity-10 text-info p-2 rounded-3">
                        <i class="bi bi-graph-up-arrow fs-5"></i>
                    </span>
                </div>
                <h3 class="fw-bold text-primary mb-1">Rp {{ number_format($salesThisMonth, 0, ',', '.') }}</h3>
                <span class="small text-muted">{{ $totalSalesCount }} transaksi selesai</span>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 border-0 shadow-sm bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Pembelian Bulan Ini</span>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary p-2 rounded-3">
                        <i class="bi bi-cart-down fs-5"></i>
                    </span>
                </div>
                <h3 class="fw-bold text-dark mb-1">Rp {{ number_format($purchasesThisMonth, 0, ',', '.') }}</h3>
                <span class="small text-muted">Biaya restock barang masuk</span>
            </div>
        </div>

        <!-- Low Stock Alert Section -->
        @if($totalLowStock > 0)
            <div class="col-12">
                <div class="card border-0 shadow-sm overflow-hidden">
                    <div class="card-header bg-danger bg-opacity-10 border-0 py-3 px-4 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2 text-danger">
                            <i class="bi bi-bell-fill fs-5"></i>
                            <h5 class="fw-bold mb-0">Peringatan: Stok Barang Menipis (Di Bawah Batas Minimum)</h5>
                        </div>
                        <span class="badge bg-danger rounded-pill px-3 py-2">{{ $totalLowStock }} Produk Perlu Restock</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">SKU</th>
                                    <th>Nama Produk Elektronik</th>
                                    <th>Kategori</th>
                                    <th>Harga Beli</th>
                                    <th class="text-center">Sisa Stok</th>
                                    <th class="text-center">Batas Minimum</th>
                                    <th class="text-center pe-4">Indikator</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowStockProducts as $item)
                                    <tr>
                                        <td class="ps-4 fw-bold text-secondary font-monospace">{{ $item->sku }}</td>
                                        <td class="fw-semibold text-dark">{{ $item->name }}</td>
                                        <td><span class="badge bg-light text-dark border">{{ $item->category->name ?? '-' }}</span></td>
                                        <td>Rp {{ number_format($item->purchase_price, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            @if($item->stock <= 0)
                                                <span class="badge bg-danger px-3 py-2 fw-bold">HABIS (0)</span>
                                            @else
                                                <span class="badge bg-warning text-dark px-3 py-2 fw-bold">{{ $item->stock }} unit</span>
                                            @endif
                                        </td>
                                        <td class="text-center text-muted fw-semibold">{{ $item->minimum_stock }} unit</td>
                                        <td class="text-center pe-4">
                                            @if($item->stock <= 0)
                                                <span class="badge bg-danger text-uppercase px-2 py-1">Stok Habis</span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 text-uppercase px-2 py-1">
                                                    Restock Segera
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- 5 Most Recent Sales Transactions -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-receipt text-primary me-2"></i>5 Transaksi Penjualan Terakhir</h6>
                    <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary btn-sm">Lihat Semua Penjualan</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No. Invoice</th>
                                <th>Waktu</th>
                                <th>Pelanggan</th>
                                <th>Kasir / Petugas</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSales as $sale)
                                <tr>
                                    <td class="ps-4 fw-bold font-monospace text-primary">{{ $sale->sale_number }}</td>
                                    <td class="small text-muted">{{ $sale->sold_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $sale->customer->name ?? 'Pembeli Umum' }}</td>
                                    <td>{{ $sale->user->name ?? '-' }}</td>
                                    <td class="text-end fw-bold text-success">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        @if($sale->status === 'completed')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Selesai</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Dibatalkan</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline-secondary btn-sm py-0 px-2">
                                            <i class="bi bi-eye"></i> Struk
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted small">
                                        Belum ada transaksi penjualan terbaru.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @else
        <!-- Customer Dashboard View -->
        <div class="col-sm-6 col-lg-4">
            <div class="card p-4 border-0 shadow-sm bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Total Pesanan Anda</span>
                    <span class="badge bg-primary bg-opacity-10 text-primary p-2 rounded-3">
                        <i class="bi bi-bag-check fs-5"></i>
                    </span>
                </div>
                <h3 class="fw-bold text-dark mb-1">{{ $customerOrdersCount }}</h3>
                <span class="small text-muted">Pesanan elektronik selesai</span>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4">
            <div class="card p-4 border-0 shadow-sm bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Akumulasi Belanja</span>
                    <span class="badge bg-success bg-opacity-10 text-success p-2 rounded-3">
                        <i class="bi bi-cash-coin fs-5"></i>
                    </span>
                </div>
                <h3 class="fw-bold text-success mb-1">Rp {{ number_format($customerTotalSpent, 0, ',', '.') }}</h3>
                <span class="small text-muted">Total nilai belanja akun Anda</span>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4">
            <div class="card p-4 border-0 shadow-sm bg-white h-100 d-flex flex-column justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold d-block mb-1">Belanja Elektronik Baru</span>
                    <h6 class="fw-bold text-dark mb-2">Katalog Resmi ElectroMart</h6>
                    <p class="text-muted small mb-0">Pilihan laptop, smartphone, komponen PC, dan monitor original.</p>
                </div>
                <a href="{{ route('catalog.index') }}" class="btn btn-primary btn-sm mt-3">
                    <i class="bi bi-arrow-right-circle me-1"></i> Buka Katalog Belanja
                </a>
            </div>
        </div>

        <!-- Customer Recent Orders Table -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-primary me-2"></i>Pesanan Terakhir Anda</h6>
                    <a href="{{ route('customer.orders') }}" class="btn btn-outline-secondary btn-sm">Lihat Semua Pesanan</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No. Invoice</th>
                                <th>Tanggal Transaksi</th>
                                <th>Produk yang Dibeli</th>
                                <th class="text-end">Total Pembayaran</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-4">Struk</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customerRecentOrders as $order)
                                <tr>
                                    <td class="ps-4 fw-bold font-monospace text-primary">{{ $order->sale_number }}</td>
                                    <td class="small text-muted">{{ $order->sold_at->format('d/m/Y H:i') }}</td>
                                    <td class="small text-dark">
                                        @foreach($order->items->take(2) as $item)
                                            <div>&bull; {{ $item->product->name ?? 'Produk' }} ({{ $item->quantity }} unit)</div>
                                        @endforeach
                                    </td>
                                    <td class="text-end fw-bold text-success">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Lunas</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('sales.show', $order) }}" class="btn btn-outline-secondary btn-sm py-0 px-2">
                                            <i class="bi bi-receipt"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted small">
                                        Anda belum memiliki riwayat pesanan. Silakan kunjungi katalog untuk mulai berbelanja!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Role-Based Authorization Route Test Areas (Tester Matrix) -->
    <div class="col-12">
        <div class="card p-4 border-0 shadow-sm bg-white">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-shield-lock-fill text-primary fs-5"></i>
                <h5 class="fw-bold text-dark mb-0">Pengujian Server-Side Role-Based Middleware (HTTP 403 Forbidden)</h5>
            </div>
            <p class="text-muted small mb-3">
                Klik tombol di bawah ini untuk menguji verifikasi otorisasi middleware role pada server:
            </p>

            <div class="row g-3">
                <div class="col-md-4">
                    <div class="p-3 border rounded-3 text-center h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="badge badge-role-admin text-uppercase mb-2">Hanya Admin</div>
                            <h6 class="fw-bold mb-1">Route Khusus Admin</h6>
                            <p class="text-muted small mb-3"><code>role:admin</code></p>
                        </div>
                        <a href="{{ route('test.admin') }}" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Uji Akses Admin
                        </a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-3 border rounded-3 text-center h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="badge badge-role-staff text-uppercase mb-2">Admin & Staff</div>
                            <h6 class="fw-bold mb-1">Route Admin & Staff</h6>
                            <p class="text-muted small mb-3"><code>role:admin,staff</code></p>
                        </div>
                        <a href="{{ route('test.staff') }}" class="btn btn-outline-warning text-dark btn-sm">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Uji Akses Staff
                        </a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-3 border rounded-3 text-center h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="badge badge-role-customer text-uppercase mb-2">Hanya Customer</div>
                            <h6 class="fw-bold mb-1">Route Khusus Customer</h6>
                            <p class="text-muted small mb-3"><code>role:customer</code></p>
                        </div>
                        <a href="{{ route('test.customer') }}" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Uji Akses Customer
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
