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
                            Masuk sebagai <strong>{{ $user->email }}</strong> &bull; Sistem Manajemen Inventaris & Penjualan Elektronik
                        </p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-light text-secondary border px-3 py-2 d-flex align-items-center gap-2">
                        <i class="bi bi-calendar3"></i> {{ now()->translatedFormat('l, d F Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @if($user->isAdmin() || $user->isStaff())
        <!-- Key Metrics Cards for Admin / Staff -->
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 border-0 shadow-sm bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Total Produk Elektronik</span>
                    <span class="badge bg-primary bg-opacity-10 text-primary p-2 rounded-3">
                        <i class="bi bi-box-seam fs-5"></i>
                    </span>
                </div>
                <h3 class="fw-bold text-dark mb-1">{{ $totalProducts }}</h3>
                <span class="small text-muted">Item terdaftar dalam katalog</span>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 border-0 shadow-sm bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Kategori Produk</span>
                    <span class="badge bg-info bg-opacity-10 text-info p-2 rounded-3">
                        <i class="bi bi-tags fs-5"></i>
                    </span>
                </div>
                <h3 class="fw-bold text-dark mb-1">{{ $totalCategories }}</h3>
                <span class="small text-muted">Kategori aktif</span>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 border-0 shadow-sm bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Supplier / Distributor</span>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary p-2 rounded-3">
                        <i class="bi bi-truck fs-5"></i>
                    </span>
                </div>
                <h3 class="fw-bold text-dark mb-1">{{ $totalSuppliers }}</h3>
                <span class="small text-muted">Mitra distributor resmi</span>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 border-0 shadow-sm bg-white h-100 {{ $totalLowStock > 0 ? 'border-danger border-opacity-50' : '' }}">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Peringatan Stok Menipis</span>
                    <span class="badge bg-danger bg-opacity-10 text-danger p-2 rounded-3">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    </span>
                </div>
                <h3 class="fw-bold text-danger mb-1">{{ $totalLowStock }}</h3>
                <span class="small text-danger fw-medium">Produk &le; stok minimum</span>
            </div>
        </div>

        <!-- Low Stock Alert Table Section -->
        @if($totalLowStock > 0)
            <div class="col-12">
                <div class="card border-0 shadow-sm overflow-hidden">
                    <div class="card-header bg-danger bg-opacity-10 border-0 py-3 px-4 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2 text-danger">
                            <i class="bi bi-bell-fill fs-5"></i>
                            <h5 class="fw-bold mb-0">Peringatan: Stok Barang Menipis (Perlu Restock)</h5>
                        </div>
                        <span class="badge bg-danger rounded-pill px-3 py-2">{{ $totalLowStock }} Produk Kritis</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">SKU</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga Beli</th>
                                    <th class="text-center">Sisa Stok</th>
                                    <th class="text-center">Stok Min</th>
                                    <th class="text-center pe-4">Status</th>
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
                                            <span class="badge bg-danger px-3 py-2 fw-bold fs-6">{{ $item->stock }}</span>
                                        </td>
                                        <td class="text-center text-muted fw-semibold">{{ $item->minimum_stock }}</td>
                                        <td class="text-center pe-4">
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">
                                                Restock Segera
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @else
        <!-- Customer View -->
        <div class="col-12">
            <div class="card p-4 border-0 shadow-sm bg-white">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-bag-check text-success me-2"></i>Area Belanja Pelanggan ElectroMart</h5>
                <p class="text-muted mb-4">
                    Selamat datang di akun pelanggan Anda. Jelajahi produk elektronik berkualitas tinggi dengan garansi resmi dan kemudahan transaksi.
                </p>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 text-center">
                            <i class="bi bi-cart3 fs-1 text-primary mb-2"></i>
                            <h6 class="fw-bold">Katalog Produk</h6>
                            <p class="text-muted small mb-0">Temukan laptop, gadget, dan periferal terbaru</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 text-center">
                            <i class="bi bi-clock-history fs-1 text-info mb-2"></i>
                            <h6 class="fw-bold">Riwayat Pesanan</h6>
                            <p class="text-muted small mb-0">{{ $customerSalesCount }} transaksi tersimpan</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 text-center">
                            <i class="bi bi-shield-check fs-1 text-success mb-2"></i>
                            <h6 class="fw-bold">Garansi Resmi</h6>
                            <p class="text-muted small mb-0">100% Produk Original Bergaransi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Server-Side Role-Based Middleware Tester Matrix -->
    <div class="col-12">
        <div class="card p-4 border-0 shadow-sm bg-white">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-shield-lock-fill text-primary fs-5"></i>
                <h5 class="fw-bold text-dark mb-0">Pengujian Server-Side Role-Based Middleware (HTTP 403 Forbidden)</h5>
            </div>
            <p class="text-muted small mb-3">
                Klik tombol di bawah ini untuk menguji perlindungan server-side role middleware (<code>RoleMiddleware</code>). Jika role akun Anda saat ini tidak diizinkan, server akan merespons dengan <strong>HTTP 403 Forbidden</strong>:
            </p>

            <div class="row g-3">
                <div class="col-md-4">
                    <div class="p-3 border rounded-3 text-center h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="badge badge-role-admin text-uppercase mb-2">Hanya Admin</div>
                            <h6 class="fw-bold mb-1">Route Khusus Admin</h6>
                            <p class="text-muted small mb-3"><code>middleware(['auth', 'role:admin'])</code></p>
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
                            <p class="text-muted small mb-3"><code>middleware(['auth', 'role:admin,staff'])</code></p>
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
                            <p class="text-muted small mb-3"><code>middleware(['auth', 'role:customer'])</code></p>
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
