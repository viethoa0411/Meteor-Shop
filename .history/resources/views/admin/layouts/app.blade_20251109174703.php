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
        }

        .admin-container {
            display: flex;
            flex: 1;
            min-height: calc(100vh - 56px - 50px);
        }

        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: #fff;
            flex-shrink: 0;
        }

        .sidebar a {
            color: #fff;
            padding: 12px 30px;
            display: block;
            text-decoration: none;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #495057;
        }

        /* ----------- MENU CON (hover) ----------- */
        .dropdown-menu-item {
            position: relative;
        }

        .dropdown-menu-item > a {
            cursor: pointer;
            display: block;
            padding: 12px 30px;
        }

        .submenu {
            display: none;
            flex-direction: column;
            background-color: #3e444a;
        }

        .submenu a {
            padding: 10px 50px;
            font-size: 0.95rem;
        }

        /* Hiển thị submenu khi hover */
        .dropdown-menu-item:hover .submenu {
            display: flex;
            animation: fadeIn 0.3s ease-in-out;
        }

        /* Hiệu ứng hiển thị mượt */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        main {
            flex: 1;
            padding: 20px;
            background-color: #f8f9fa;
        }

        .footer {
            height: 50px;
            background-color: rgba(var(--bs-dark-rgb));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .navbar-brand {
            padding-left: 20px;
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
        }

        /* Submenu */
        .submenu {
            display: none;
            background-color: #3d434a;
        }

        .submenu a {
            padding-left: 45px;
        }

        .dropdown-menu-item:hover .submenu {
            display: block;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">AdminPanel</a>
                <div class="ms-auto">
                    <div class="dropdown">
                        <a class="btn btn-secondary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                            {{ Auth::user()->name ?? 'Admin' }}
                            <span class="badge bg-info ms-2">{{ Auth::user()->role ?? 'N/A' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                <form action="#" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="dropdown-item"
                                        onclick="return confirm('Bạn chắc chắn muốn đăng xuất?');">
                                        <i class="bi bi-box-arrow-right"></i> Đăng xuất
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Nội dung chính -->
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h5 class="text-center py-3 border-bottom border-secondary">Quản trị</h5>
            <a href="#" class="active"><i class="bi bi-house-door-fill me-2"></i> Dashboard</a>
            <a href="{{ route('admin.categories.list') }}"><i class="bi bi-folder-plus me-2"></i> Danh mục</a>
            <a href="{{ route('admin.products.list') }}"><i class="bi bi-box-seam me-2"></i> Sản phẩm</a>
            <a href="{{ route('admin.orders.index')}}"><i class="bi bi-cart-fill me-2"></i> Đơn hàng</a>
            <div class="dropdown-menu-item">
                <a href="#"><i class="bi bi-people-fill me-2"></i> Quản lý tài khoản <i class="bi bi-chevron-right float-end"></i></a>
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
        <span>© 2025 Xuất bản bởi <strong>Meteor-Shop</strong> — Cảm ơn bạn đã đồng hành cùng chúng tôi!</span>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
