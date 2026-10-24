<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ElectroMart') - Inventory & Sales Management</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            --brand-dark: #0f172a;
            --brand-surface: #ffffff;
            --brand-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-brand {
            font-weight: 800;
            letter-spacing: -0.5px;
            font-size: 1.35rem;
        }

        .navbar {
            background-color: #0f172a !important;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.15);
        }

        .nav-link {
            font-weight: 500;
            color: #cbd5e1 !important;
            transition: all 0.2s ease;
            padding: 0.5rem 0.85rem !important;
            border-radius: 8px;
        }

        .nav-link:hover, .nav-link.active {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.08);
        }

        .card {
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 14px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn {
            border-radius: 9px;
            font-weight: 600;
            padding: 0.55rem 1.15rem;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
        }

        .btn-primary:hover {
            box-shadow: 0 6px 16px rgba(13, 110, 253, 0.35);
            transform: translateY(-1px);
        }

        .badge-role-admin {
            background-color: #ef4444;
            color: #ffffff;
        }

        .badge-role-staff {
            background-color: #f59e0b;
            color: #ffffff;
        }

        .badge-role-customer {
            background-color: #10b981;
            color: #ffffff;
        }

        .footer {
            margin-top: auto;
            background-color: #ffffff;
            border-top: 1px solid #e2e8f0;
            padding: 1.25rem 0;
            color: #64748b;
            font-size: 0.875rem;
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Main Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top py-2">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 text-white" href="{{ route('dashboard') }}">
                <span class="d-inline-flex align-items-center justify-content-center bg-primary rounded-3 p-2 shadow-sm">
                    <i class="bi bi-cpu-fill text-white fs-5"></i>
                </span>
                <span>Electro<span class="text-primary">Mart</span></span>
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                @auth
                    <!-- Role-based Navigation Links -->
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3 gap-1">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2 me-1"></i> Dashboard
                            </a>
                        </li>

                        @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                                    <i class="bi bi-box-seam me-1"></i> Produk
                                </a>
                            </li>
                        @endif

                        @if(Auth::user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                                    <i class="bi bi-tags me-1"></i> Kategori
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}">
                                    <i class="bi bi-truck me-1"></i> Supplier
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                    <i class="bi bi-people me-1"></i> Pengguna
                                </a>
                            </li>
                        @endif

                        @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="bi bi-arrow-down-left-circle me-1"></i> Pembelian
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="bi bi-cash-stack me-1"></i> Penjualan (Kasir)
                                </a>
                            </li>
                        @endif

                        @if(Auth::user()->isCustomer())
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="bi bi-grid me-1"></i> Katalog Produk
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="bi bi-receipt me-1"></i> Pesanan Saya
                                </a>
                            </li>
                        @endif
                    </ul>

                    <!-- User Profile & Logout -->
                    <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 py-1 px-3 bg-dark bg-opacity-50 rounded-pill border border-secondary border-opacity-25" href="#" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle fs-5 text-primary"></i>
                                <span class="fw-semibold text-white">{{ Auth::user()->name }}</span>
                                @if(Auth::user()->isAdmin())
                                    <span class="badge badge-role-admin rounded-pill text-uppercase px-2 py-1" style="font-size: 0.65rem;">Admin</span>
                                @elseif(Auth::user()->isStaff())
                                    <span class="badge badge-role-staff rounded-pill text-uppercase px-2 py-1" style="font-size: 0.65rem;">Staff</span>
                                @else
                                    <span class="badge badge-role-customer rounded-pill text-uppercase px-2 py-1" style="font-size: 0.65rem;">Customer</span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2" aria-labelledby="userMenu">
                                <li class="px-3 py-2 border-bottom">
                                    <div class="small text-muted">Email Login</div>
                                    <div class="fw-semibold small text-truncate">{{ Auth::user()->email }}</div>
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2 py-2">
                                            <i class="bi bi-box-arrow-right"></i>
                                            <span>Keluar (Logout)</span>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                @else
                    <!-- Guest Navigation Links -->
                    <ul class="navbar-nav ms-auto gap-2">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary btn-sm px-3" href="{{ route('register') }}">
                                <i class="bi bi-person-plus me-1"></i> Daftar Customer
                            </a>
                        </li>
                    </ul>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content Container with Centralized Flash Messages -->
    <main class="container my-4 flex-grow-1">

        <!-- Centralized Flash Messages Component -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center rounded-3 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill fs-5 me-2 text-success"></i>
                <div class="fw-medium">{{ session('success') }}</div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center rounded-3 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2 text-danger"></i>
                <div class="fw-medium">{{ session('error') }}</div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center rounded-3 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-circle-fill fs-5 me-2 text-warning"></i>
                <div class="fw-medium">{{ session('warning') }}</div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show d-flex align-items-center rounded-3 shadow-sm mb-4" role="alert">
                <i class="bi bi-info-circle-fill fs-5 me-2 text-info"></i>
                <div class="fw-medium">{{ session('info') }}</div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center mb-1">
                    <i class="bi bi-x-circle-fill fs-5 me-2 text-danger"></i>
                    <strong>Mohon periksa kesalahan input berikut:</strong>
                </div>
                <ul class="mb-0 ps-4 small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container text-center">
            <div class="fw-semibold text-dark">ElectroMart - Inventory & Sales Management System</div>
            <div class="small text-muted">UTS Pemrograman Web 2 &bull; Terintegrasi Laravel 12 & MySQL XAMPP</div>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
