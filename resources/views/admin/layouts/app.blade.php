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
            color: #fff !important;
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
    </style>
</head>

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

            <a href="{{ route('admin.dashboard') }}" class="active"><i class="bi bi-house-door-fill me-2"></i> Dashboard</a>
            <a href="{{ route('admin.categories.list') }}"><i class="bi bi-folder-plus me-2"></i> Danh mục</a>
            <a href="{{ route('admin.products.list') }}"><i class="bi bi-box-seam me-2"></i> Sản phẩm</a>
            <a href="{{ route('admin.orders.list') }}"><i class="bi bi-cart-fill me-2"></i> Đơn hàng</a>
            <a href="{{ route('admin.blogs.list') }}"><i class="bi bi-list-ul me-2"></i> Bài viết</a>
            <a href="{{ route('admin.banners.list') }}"><i class="bi bi-image-fill me-2"></i> Banner</a>

            <!-- Quản lý tài khoản -->
            <div class="dropdown-menu-item">
                <a href="#"><i class="bi bi-people-fill me-2"></i> Quản lý tài khoản
                    <i class="bi bi-chevron-right float-end"></i>
                </a>
                <div class="submenu">
                    <a href="{{ route('admin.account.admin.list') }}"><i class="bi bi-person-badge-fill me-2"></i> Quản lý Admin</a>
                    <a href="{{ route('admin.account.users.list') }}"><i class="bi bi-people-fill me-2"></i> Quản lý User</a>
                </div>
            </div>

            <a href="#"><i class="bi bi-gear-fill me-2"></i> Cài đặt</a>
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

        if (localStorage.getItem("theme") === "dark") {
            document.body.classList.add("dark");
            themeIcon.classList.replace("bi-moon-fill", "bi-sun-fill");
        }

        toggleBtn.onclick = () => {
            document.body.classList.toggle("dark");
            const isDark = document.body.classList.contains("dark");
            themeIcon.classList.replace(
                isDark ? "bi-moon-fill" : "bi-sun-fill",
                isDark ? "bi-sun-fill" : "bi-moon-fill"
            );
            localStorage.setItem("theme", isDark ? "dark" : "light");
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
