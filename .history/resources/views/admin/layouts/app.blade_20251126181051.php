<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Meteor Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: #ffffff;
            color: #333;
            transition: background 0.3s, color 0.3s;
        }

        .admin-container {
            display: flex;
            flex: 1;
            min-height: calc(100vh - 56px - 50px);
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #ffffff;
            border-right: 1px solid #e5e7eb;
            color: #333;
            flex-shrink: 0;
            transition: background 0.3s, color 0.3s, border-color 0.3s;
        }

        .sidebar a {
            color: #333;
            padding: 12px 30px;
            display: block;
            text-decoration: none;
            font-weight: 500;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #f1f3f5;
        }

        /* Submenu */
        .submenu {
            display: none;
            background-color: #f8f9fa;
        }

        .submenu a {
            padding-left: 45px;
            color: #444;
        }

        .submenu a:hover {
            background-color: #e9ecef;
        }

        .dropdown-menu-item.active .submenu {
            display: block;
        }

        .dropdown-menu-item > a .bi-chevron-right {
            transition: transform 0.3s;
        }

        .dropdown-menu-item.active > a .bi-chevron-right {
            transform: rotate(90deg);
            }

        /* Header */
        .navbar {
            background-color: #ffffff !important;
            border-bottom: 1px solid #e5e7eb;
        }

        main {
            flex: 1;
            padding: 20px;
            background-color: #f8f9fa;
            color: #333;
            transition: background 0.3s, color 0.3s;
        }

        /* Footer */
        .footer {
            height: 50px;
            background-color: #343a40;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* MOBILE */
        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
        }

        /* DARK MODE */
        body.dark {
            background-color: #1e1e1e;
            color: #e8e8e8;
        }

        body.dark .navbar {
            background-color: #2b2b2b !important;
            color: #fff;
        }

        body.dark .navbar a {
            color: inherit !important;
        }

        body.dark .sidebar {
            background-color: #2b2b2b;
            border-color: #444;
        }

        body.dark .sidebar a {
            color: #e8e8e8;
        }

        body.dark .sidebar a:hover,
        body.dark .sidebar a.active {
            background-color: #3a3a3a;
        }

        body.dark .submenu {
            background-color: #333;
        }

        body.dark .submenu a {
            color: #ddd;
        }

        body.dark .submenu a:hover {
            background-color: #444;
        }

        body.dark main {
            background-color: #2b2b2b !important;
            color: #ddd;
        }

        body.dark .footer {
            background-color: #2b2b2b !important;
            color: #ddd;
        }

        /* Dark mode helpers for components */
        body.dark .card {
            background-color: #1f1f1f;
            color: #f5f5f5;
            border-color: #2f2f2f;
        }

        body.dark .card-header {
            background-color: #242424 !important;
            border-color: #2f2f2f !important;
            color: #f5f5f5;
        }

        .table tbody tr {
            background-color: #ffffff;
        }

        .table tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }

        body.dark .table {
            background-color: #1b1b1b;
            color: #f0f0f0;
        }

        body.dark .table thead,
        body.dark .table-light {
            background-color: #151515 !important;
            color: #ffffff !important;
        }

        body.dark .table thead th {
            border-color: #2f2f2f !important;
            background-color: #151515 !important;
            color: #ffffff !important;
        }

        body.dark .table td,
        body.dark .table th {
            border-color: #2f2f2f !important;
            color: #ffffff !important;
        }

        body.dark .table-bordered > :not(caption) > * > * {
            border-color: #2f2f2f;
        }

        body.dark table.table tbody tr,
        body.dark table.table tbody tr td {
            background-color: #151515 !important;
        }

        body.dark table.table tbody tr:nth-of-type(odd),
        body.dark table.table tbody tr:nth-of-type(odd) td,
        body.dark .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: #1c1c1c !important;
        }

        body.dark .table-striped > tbody > tr:nth-of-type(odd) > * {
            background-color: #1c1c1c !important;
        }

        body.dark .table-hover tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.08);
        }

        body.dark .list-group-item {
            background-color: #1f1f1f;
            color: #f5f5f5;
            border-color: #2f2f2f;
        }

        body.dark .alert {
            background-color: #2b3b4b;
            color: #f8f9fa;
            border-color: #1f2a35;
        }

        body.dark .form-control,
        body.dark .form-select {
            background-color: #1c1c1c;
            color: #f5f5f5;
            border-color: #444;
        }

        body.dark .form-control::placeholder,
        body.dark .form-select::placeholder {
            color: #d0d0d0;
        }

        body.dark .form-control:focus,
        body.dark .form-select:focus {
            background-color: #1c1c1c;
            color: #fff;
            border-color: #4a90e2;
            box-shadow: none;
        }

        body.dark .bg-light {
            background-color: #1b1b1b !important;
        }

        body.dark .bg-white {
            background-color: #1f1f1f !important;
            color: #f5f5f5 !important;
        }

        body.dark .bg-body {
            background-color: #1f1f1f !important;
            color: #f5f5f5 !important;
        }

        body.dark .border-start,
        body.dark .border-top,
        body.dark .border {
            border-color: #2f2f2f !important;
        }

        body.dark .text-muted {
            color: #b5bfd2 !important;
        }

        body.dark .text-dark {
            color: #ffffff !important;
        }

        body.dark .shadow-sm {
            box-shadow: 0 0.25rem 1rem rgba(0, 0, 0, 0.65) !important;
        }
    </style>
</head>
@stack('scripts')

<body>

    <!-- Header -->
    <nav class="navbar navbar-expand-lg px-3 shadow-sm">
        <a class="navbar-brand" href="{{ route('admin.dashboard') }}">Meteor-Shop</a>

        <div class="ms-auto d-flex align-items-center">

            <!-- Nút Dark/Light -->
            <button id="themeToggle" class="btn btn-outline-secondary me-3">
                <i id="themeIcon" class="bi bi-moon-fill"></i>
            </button>

            <!-- Tài khoản -->
            <div class="dropdown">
                <a class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle"></i>
                    {{ Auth::user()->name ?? 'Admin' }}
                    <span class="badge bg-info ms-2">{{ Auth::user()->role ?? 'N/A' }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a href="" class="dropdown-item">Profile</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                                @csrf
                            <button class="dropdown-item">
                                <i class="bi bi-box-arrow-right"></i> Đăng xuất
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Admin Layout -->
    <div class="admin-container">

        <!-- Sidebar -->
        <aside class="sidebar">
            <h5 class="navbar navbar-expand-lg px-3 shadow-sm">Quản trị</h5>

            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-house-door-fill me-2"></i> Dashboard
            </a>
            <a href="{{ route('admin.categories.list') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="bi bi-folder-plus me-2"></i> Danh mục
            </a>
            <a href="{{ route('admin.products.list') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam me-2"></i> Sản phẩm
            </a>
            <a href="{{ route('admin.orders.list') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="bi bi-cart-fill me-2"></i> Đơn hàng
            </a>
            <a href="{{ route('admin.blogs.list') }}" class="{{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">
                <i class="bi bi-list-ul me-2"></i> Danh sách bài viết
            </a>
            <a href="{{ route('admin.banners.list') }}" class="{{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                <i class="bi bi-image-fill me-2"></i> Quản lý Banner
            </a>
            <!-- Quản lý tài khoản -->
            <div class="dropdown-menu-item {{ request()->routeIs('admin.account.*') ? 'active' : '' }}">
                <a href="#" class="{{ request()->routeIs('admin.account.*') ? 'active' : '' }}"><i class="bi bi-people-fill me-2"></i> Quản lý tài khoản
                    <i class="bi bi-chevron-right float-end"></i>
                </a>
                <div class="submenu">
                    <a href="{{ route('admin.account.admin.list') }}" class="{{ request()->routeIs('admin.account.admin.*') ? 'active' : '' }}">
                        <i class="bi bi-person-badge-fill me-2"></i> Quản lý Admin
                    </a>
                    <a href="{{ route('admin.account.users.list') }}" class="{{ request()->routeIs('admin.account.users.*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill me-2"></i> Quản lý User
                    </a>
                </div>
            </div>

            <a href="#" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear-fill me-2"></i> Cài đặt
            </a>
        </aside>

        <!-- Content -->
        <main>
            @yield('content')
        </main>
        
    </div>

    <!-- Footer -->
    <footer class="footer">
        <span>© 2025 Meteor-Shop — Admin Panel</span>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Dark mode
        const toggleBtn = document.getElementById("themeToggle");
        const themeIcon = document.getElementById("themeIcon");

        const setTheme = (isDark) => {
            document.body.classList.toggle("dark", isDark);
            document.documentElement.classList.toggle("dark", isDark);
            themeIcon.classList.toggle("bi-moon-fill", !isDark);
            themeIcon.classList.toggle("bi-sun-fill", isDark);
            localStorage.setItem("theme", isDark ? "dark" : "light");
            window.dispatchEvent(new CustomEvent("theme-changed", { detail: { isDark } }));
        };

        setTheme(localStorage.getItem("theme") === "dark");

        toggleBtn.onclick = () => {
            const isDark = !document.body.classList.contains("dark");
            setTheme(isDark);
        };

        // Toggle submenu
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.dropdown-menu-item > a').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const parent = this.parentElement;
                    parent.classList.toggle('active');
                });
            });
        });
    </script>
</body>

</html>
