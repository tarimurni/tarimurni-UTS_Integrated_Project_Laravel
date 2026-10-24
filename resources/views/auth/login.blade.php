@extends('layouts.app')

@section('title', 'Pilih Portal Masuk - ElectroMart')

@push('styles')
<style>
    .portal-nav-pill {
        border: 2px solid transparent;
        border-radius: 12px;
        padding: 0.85rem 0.75rem;
        transition: all 0.25s ease-in-out;
        background-color: #ffffff;
        color: #475569;
        text-align: center;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .portal-nav-pill:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(0,0,0,0.08);
        color: #0f172a;
    }
    .portal-nav-pill.active-customer {
        background: linear-gradient(145deg, #ecfdf5 0%, #d1fae5 100%);
        border-color: #10b981;
        color: #065f46 !important;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
    }
    .portal-nav-pill.active-staff {
        background: linear-gradient(145deg, #fffbeb 0%, #fef3c7 100%);
        border-color: #f59e0b;
        color: #92400e !important;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
    }
    .portal-nav-pill.active-admin {
        background: linear-gradient(145deg, #eff6ff 0%, #dbeafe 100%);
        border-color: #3b82f6;
        color: #1e40af !important;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
    }
    .portal-card {
        border-radius: 16px;
        box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.08);
    }
    .portal-badge {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        border-radius: 6px;
        padding: 0.2rem 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center py-3">
    <div class="col-lg-7 col-xl-6">

        <!-- Top Header Banner -->
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center bg-dark rounded-4 p-3 shadow-sm mb-3">
                <i class="bi bi-cpu-fill text-primary fs-2"></i>
            </div>
            <h3 class="fw-bolder text-dark mb-1">Selamat Datang di ElectroMart</h3>
            <p class="text-muted small mb-0">Pusat Resmi Penjualan & Manajemen Perangkat Elektronik Terlengkap</p>
        </div>

        <!-- 3-Way Portal Selector Navigation Tabs -->
        <div class="card bg-light border-0 p-2 mb-4 rounded-4 shadow-sm">
            <div class="row g-2" role="tablist" id="portalTabs">
                <!-- Tab 1: Customer -->
                <div class="col-4">
                    <button type="button"
                            id="tab-button-customer"
                            class="w-100 portal-nav-pill {{ ($portal ?? 'customer') === 'customer' ? 'active active-customer' : '' }}"
                            data-bs-toggle="pill"
                            data-bs-target="#pane-customer"
                            role="tab"
                            aria-controls="pane-customer"
                            aria-selected="{{ ($portal ?? 'customer') === 'customer' ? 'true' : 'false' }}"
                            onclick="handlePortalSelect('customer')">
                        <i class="bi bi-cart3 fs-4 text-success"></i>
                        <span class="fw-bold small lh-1">Pelanggan</span>
                        <span class="portal-badge bg-success-subtle text-success">Tanpa Sandi</span>
                    </button>
                </div>

                <!-- Tab 2: Staff -->
                <div class="col-4">
                    <button type="button"
                            id="tab-button-staff"
                            class="w-100 portal-nav-pill {{ ($portal ?? 'customer') === 'staff' ? 'active active-staff' : '' }}"
                            data-bs-toggle="pill"
                            data-bs-target="#pane-staff"
                            role="tab"
                            aria-controls="pane-staff"
                            aria-selected="{{ ($portal ?? 'customer') === 'staff' ? 'true' : 'false' }}"
                            onclick="handlePortalSelect('staff')">
                        <i class="bi bi-person-badge-fill fs-4 text-warning"></i>
                        <span class="fw-bold small lh-1">Staff Gudang</span>
                        <span class="portal-badge bg-warning-subtle text-warning-emphasis">Kasir & POS</span>
                    </button>
                </div>

                <!-- Tab 3: Admin -->
                <div class="col-4">
                    <button type="button"
                            id="tab-button-admin"
                            class="w-100 portal-nav-pill {{ ($portal ?? 'customer') === 'admin' ? 'active active-admin' : '' }}"
                            data-bs-toggle="pill"
                            data-bs-target="#pane-admin"
                            role="tab"
                            aria-controls="pane-admin"
                            aria-selected="{{ ($portal ?? 'customer') === 'admin' ? 'true' : 'false' }}"
                            onclick="handlePortalSelect('admin')">
                        <i class="bi bi-shield-lock-fill fs-4 text-primary"></i>
                        <span class="fw-bold small lh-1">Admin</span>
                        <span class="portal-badge bg-primary-subtle text-primary">Owner / IT</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Tab Content Panes -->
        <div class="tab-content" id="portalTabContent">

            <!-- ======================================================== -->
            <!-- PORTAL 1: CUSTOMER (PASSWORDLESS - NAMA & HP SAJA)       -->
            <!-- ======================================================== -->
            <div class="tab-pane fade {{ ($portal ?? 'customer') === 'customer' ? 'show active' : '' }}" id="pane-customer" role="tabpanel" aria-labelledby="tab-button-customer">
                <div class="card p-4 p-md-5 border-0 bg-white portal-card">
                    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                        <div class="rounded-3 bg-success-subtle p-3 text-success">
                            <i class="bi bi-bag-heart-fill fs-3"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h4 class="fw-bold text-dark mb-0">Portal Belanja Pelanggan</h4>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Passwordless</span>
                            </div>
                            <p class="text-muted small mb-0 mt-1">Cukup masukkan nama Anda untuk langsung memilih & belanja produk elektronik impian.</p>
                        </div>
                    </div>

                    <!-- Customer Passwordless Login Form -->
                    <form action="{{ route('login.customer.post') }}" method="POST" id="customerLoginForm">
                        @csrf

                        <!-- Nama Lengkap Pelanggan (Wajib) -->
                        <div class="mb-3">
                            <label for="customer_name" class="form-label fw-semibold small text-secondary">
                                Nama Lengkap Pelanggan <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-person-fill text-muted"></i></span>
                                <input type="text"
                                       class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror"
                                       id="customer_name"
                                       name="name"
                                       value="{{ old('name') }}"
                                       required
                                       placeholder="Masukkan nama Anda, misal: Andi Pratama"
                                       {{ ($portal ?? 'customer') === 'customer' ? 'autofocus' : '' }}>
                            </div>
                            <div class="form-text small text-muted">
                                <i class="bi bi-info-circle me-1"></i>Nama akan tercetak resmi di invoice pemesanan Anda.
                            </div>
                        </div>

                        <!-- Nomor WhatsApp / HP (Opsional) -->
                        <div class="mb-4">
                            <label for="customer_phone" class="form-label fw-semibold small text-secondary">
                                Nomor WhatsApp / HP <span class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-whatsapp text-success"></i></span>
                                <input type="text"
                                       class="form-control border-start-0 ps-0 @error('phone') is-invalid @enderror"
                                       id="customer_phone"
                                       name="phone"
                                       value="{{ old('phone') }}"
                                       placeholder="0812xxxx (Opsional)">
                            </div>
                            <div class="form-text small text-muted">
                                Berguna untuk konfirmasi status kurir dan pengiriman barang elektronik.
                            </div>
                        </div>

                        <!-- Tombol Mulai Belanja Sekarang -->
                        <button type="submit" class="btn btn-success w-100 py-2 fs-6 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-cart-check-fill fs-5"></i> Mulai Belanja Sekarang
                        </button>
                    </form>

                    <!-- Quick Demo Pelanggan Box -->
                    <div class="card mt-4 bg-light border-0 rounded-3 p-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-lightning-charge-fill text-success fs-5"></i>
                            <span class="fw-bold small text-dark">Akses Cepat Pengujian Pelanggan Demo:</span>
                        </div>
                        <p class="small text-muted mb-2">
                            Klik tombol di bawah ini untuk langsung mengisi nama pelanggan demo dan membuka Katalog Produk secara instan tanpa password:
                        </p>
                        <button type="button"
                                class="btn btn-outline-success fw-semibold w-100 py-2 d-flex align-items-center justify-content-center gap-2"
                                onclick="quickCustomerDemo('Andi Pratama - Customer', '08123456789')">
                            <i class="bi bi-person-check-fill"></i> Masuk Cepat sebagai Pelanggan Demo (Andi Pratama)
                        </button>
                    </div>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- PORTAL 2: STAFF GUDANG & KASIR (EMAIL & PASSWORD)        -->
            <!-- ======================================================== -->
            <div class="tab-pane fade {{ ($portal ?? 'customer') === 'staff' ? 'show active' : '' }}" id="pane-staff" role="tabpanel" aria-labelledby="tab-button-staff">
                <div class="card p-4 p-md-5 border-0 bg-white portal-card">
                    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                        <div class="rounded-3 bg-warning-subtle p-3 text-warning-emphasis">
                            <i class="bi bi-person-badge-fill fs-3"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h4 class="fw-bold text-dark mb-0">Portal Staff Gudang & Kasir</h4>
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Role: Staff</span>
                            </div>
                            <p class="text-muted small mb-0 mt-1">Akses pencatatan restock barang, transaksi kasir POS, dan stok gudang.</p>
                        </div>
                    </div>

                    <!-- Staff Login Form -->
                    <form action="{{ route('login.staff.post') }}" method="POST" id="staffLoginForm">
                        @csrf

                        <!-- Email Staff -->
                        <div class="mb-3">
                            <label for="staff_email" class="form-label fw-semibold small text-secondary">
                                Email Staff <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope-fill text-muted"></i></span>
                                <input type="email"
                                       class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror"
                                       id="staff_email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       required
                                       placeholder="staff@electromart.test"
                                       {{ ($portal ?? 'customer') === 'staff' ? 'autofocus' : '' }}>
                            </div>
                        </div>

                        <!-- Password Staff -->
                        <div class="mb-3">
                            <label for="staff_password" class="form-label fw-semibold small text-secondary">
                                Password Staff <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill text-muted"></i></span>
                                <input type="password"
                                       class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror"
                                       id="staff_password"
                                       name="password"
                                       required
                                       placeholder="Masukkan password Staff">
                            </div>
                        </div>

                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="staff_remember" name="remember" value="1">
                            <label class="form-check-label small text-muted" for="staff_remember">Ingat Sesi Staff di Perangkat Ini</label>
                        </div>

                        <!-- Tombol Masuk Staff -->
                        <button type="submit" class="btn btn-warning text-dark w-100 py-2 fs-6 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-box-arrow-in-right fs-5"></i> Masuk ke Portal Staff & Kasir
                        </button>
                    </form>

                    <!-- Quick Demo Staff Box -->
                    <div class="card mt-4 bg-light border-0 rounded-3 p-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-shield-check text-warning-emphasis fs-5"></i>
                            <span class="fw-bold small text-dark">Kredensial Akun Staff Demo:</span>
                        </div>
                        <p class="small text-muted mb-2">Klik tombol di bawah untuk mengisi kredensial Staff secara otomatis:</p>
                        <button type="button"
                                class="btn btn-outline-warning text-dark fw-semibold w-100 py-2 d-flex align-items-center justify-content-center gap-2"
                                onclick="fillStaffCredentials('staff@electromart.test', 'password123')">
                            <i class="bi bi-person-badge-fill text-warning"></i> Isi Kredensial Staff Demo (staff@electromart.test)
                        </button>
                    </div>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- PORTAL 3: ADMINISTRATOR / OWNER (EMAIL & PASSWORD)       -->
            <!-- ======================================================== -->
            <div class="tab-pane fade {{ ($portal ?? 'customer') === 'admin' ? 'show active' : '' }}" id="pane-admin" role="tabpanel" aria-labelledby="tab-button-admin">
                <div class="card p-4 p-md-5 border-0 bg-white portal-card">
                    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                        <div class="rounded-3 bg-primary-subtle p-3 text-primary">
                            <i class="bi bi-shield-lock-fill fs-3"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h4 class="fw-bold text-dark mb-0">Portal Administrator / Owner</h4>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Role: Admin</span>
                            </div>
                            <p class="text-muted small mb-0 mt-1">Akses penuh manajemen produk, kategori, data supplier, kelola akun, dan laporan keuangan.</p>
                        </div>
                    </div>

                    <!-- Admin Login Form -->
                    <form action="{{ route('login.admin.post') }}" method="POST" id="adminLoginForm">
                        @csrf

                        <!-- Email Admin -->
                        <div class="mb-3">
                            <label for="admin_email" class="form-label fw-semibold small text-secondary">
                                Email Administrator <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope-fill text-muted"></i></span>
                                <input type="email"
                                       class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror"
                                       id="admin_email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       required
                                       placeholder="admin@electromart.test"
                                       {{ ($portal ?? 'customer') === 'admin' ? 'autofocus' : '' }}>
                            </div>
                        </div>

                        <!-- Password Admin -->
                        <div class="mb-3">
                            <label for="admin_password" class="form-label fw-semibold small text-secondary">
                                Password Admin <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill text-muted"></i></span>
                                <input type="password"
                                       class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror"
                                       id="admin_password"
                                       name="password"
                                       required
                                       placeholder="Masukkan password Administrator">
                            </div>
                        </div>

                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="admin_remember" name="remember" value="1">
                            <label class="form-check-label small text-muted" for="admin_remember">Ingat Sesi Admin di Perangkat Ini</label>
                        </div>

                        <!-- Tombol Masuk Admin -->
                        <button type="submit" class="btn btn-primary w-100 py-2 fs-6 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-shield-lock-fill fs-5"></i> Masuk ke Portal Administrator
                        </button>
                    </form>

                    <!-- Quick Demo Admin Box -->
                    <div class="card mt-4 bg-light border-0 rounded-3 p-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-shield-check text-primary fs-5"></i>
                            <span class="fw-bold small text-dark">Kredensial Akun Admin Demo:</span>
                        </div>
                        <p class="small text-muted mb-2">Klik tombol di bawah untuk mengisi kredensial Admin secara otomatis:</p>
                        <button type="button"
                                class="btn btn-outline-primary fw-semibold w-100 py-2 d-flex align-items-center justify-content-center gap-2"
                                onclick="fillAdminCredentials('admin@electromart.test', 'password123')">
                            <i class="bi bi-person-fill-gear"></i> Isi Kredensial Admin Demo (admin@electromart.test)
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <!-- Direct Portal Route Footer Guide -->
        <div class="text-center mt-3 text-muted small">
            <span>Navigasi langsung: </span>
            <a href="{{ route('login.customer') }}" class="text-decoration-none text-success fw-semibold mx-1">/login/customer</a> &bull;
            <a href="{{ route('login.staff') }}" class="text-decoration-none text-warning-emphasis fw-semibold mx-1">/login/staff</a> &bull;
            <a href="{{ route('login.admin') }}" class="text-decoration-none text-primary fw-semibold mx-1">/login/admin</a>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // Handle tab switching & dynamic active classes
    function handlePortalSelect(portalName) {
        const customerBtn = document.getElementById('tab-button-customer');
        const staffBtn = document.getElementById('tab-button-staff');
        const adminBtn = document.getElementById('tab-button-admin');

        // Reset classes
        customerBtn.classList.remove('active-customer');
        staffBtn.classList.remove('active-staff');
        adminBtn.classList.remove('active-admin');

        if (portalName === 'customer') {
            customerBtn.classList.add('active-customer');
        } else if (portalName === 'staff') {
            staffBtn.classList.add('active-staff');
        } else if (portalName === 'admin') {
            adminBtn.classList.add('active-admin');
        }

        // Update URL path seamlessly without page reload
        if (window.history && window.history.replaceState) {
            window.history.replaceState(null, '', '{{ url("/login") }}/' + portalName);
        }
    }

    // Quick fill and auto submit for Customer Demo
    function quickCustomerDemo(name, phone) {
        document.getElementById('customer_name').value = name;
        document.getElementById('customer_phone').value = phone;
        document.getElementById('customerLoginForm').submit();
    }

    // Quick fill for Staff Demo
    function fillStaffCredentials(email, password) {
        document.getElementById('staff_email').value = email;
        document.getElementById('staff_password').value = password;
    }

    // Quick fill for Admin Demo
    function fillAdminCredentials(email, password) {
        document.getElementById('admin_email').value = email;
        document.getElementById('admin_password').value = password;
    }
</script>
@endpush
