<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Quản lý phòng trọ')</title>

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f4f6f9;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, .04);
        }

        .nav-link {
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            transition: color 0.2s;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #0d6efd !important;
        }

        .footer {
            background-color: #ffffff;
            border-top: 1px solid #dee2e6;
            font-size: 0.875rem;
            color: #6c757d;
        }
    </style>
</head>

<body>

    <!-- Navbar hiện đại -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="{{ route('dashboard') }}">
                <i class="bi bi-house-gear-fill me-2"></i>Quản lý phòng trọ
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false"
                aria-label="Mở hoặc đóng menu điều hướng">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard*') ? 'active text-primary' : '' }}"
                            href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2 me-1"></i> Trang chủ
                        </a>
                    </li>

                    @auth
                        @if(auth()->user()->role === 'admin')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active text-primary' : '' }}"
                                    href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-grid-1x2 me-1"></i> Quản trị
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('properties.*') ? 'active text-primary' : '' }}"
                                    href="{{ route('properties.index') }}">
                                    <i class="bi bi-buildings me-1"></i> Quản lý khu trọ
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('users.*') ? 'active text-primary' : '' }}"
                                    href="{{ route('users.index') }}">
                                    <i class="bi bi-people me-1"></i> Quản lý tài khoản
                                </a>
                            </li>
                        @endif
                    @endauth

                    @if(auth()->user()->role === 'manager' && auth()->user()->property_id)
                        <!-- 1. Phòng -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('rooms.*') ? 'active text-primary' : '' }}"
                                href="{{ route('rooms.index', auth()->user()->property_id) }}">
                                <i class="bi bi-door-open me-1"></i> Phòng
                            </a>
                        </li>

                        <!-- 2. Người thuê -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('tenants.*') ? 'active text-primary' : '' }}"
                                href="{{ route('tenants.index', auth()->user()->property_id) }}">
                                <i class="bi bi-people me-1"></i> Người thuê
                            </a>
                        </li>

                        <!-- 3. Hợp đồng -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('contracts.*') ? 'active text-primary' : '' }}"
                                href="{{ route('contracts.index', auth()->user()->property_id) }}">
                                <i class="bi bi-file-earmark-text me-1"></i> Hợp đồng
                            </a>
                        </li>

                        <!-- 4. Hóa đơn -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('invoices.*') ? 'active text-primary' : '' }}"
                                href="{{ route('invoices.index', auth()->user()->property_id) }}">
                                <i class="bi bi-receipt me-1"></i> Hóa đơn
                            </a>
                        </li>
                    @endif

                    <!-- Nút đăng xuất -->
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <i class="bi bi-box-arrow-right me-1"></i> Đăng xuất
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container py-4">
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer nhỏ gọn -->
    <footer class="footer py-3 text-center">
        <div class="container">
            <span>QUANLYPHONGTRO &copy; {{ date('Y') }} Hệ thống quản lý nhà trọ</span>
        </div>
    </footer>

    <!-- Bootstrap 5.3.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
