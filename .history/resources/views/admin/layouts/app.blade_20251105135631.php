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
            /* trừ header và footer */
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
                            <i class="bi bi-person-circle"></i> Admin
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="#" onclick="return confirm('Đăng xuất khỏi hệ thống!!');">
                                    Đăng xuất
                                </a>
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
            <a href="#" class=""><i class="bi bi-house-door-fill me-2"></i> Dashboard</a>
            <a href="{{ route('admin.categories.list') }}"><i class="bi bi-folder-plus me-2"></i> Danh mục</a>
            <a href="{{ route('admin.products.list') }}"><i class="bi bi-box-seam me-2"></i> Sản phẩm</a>
            <a href="#"><i class="bi bi-cart-fill me-2"></i> Đơn hàng</a>

            <ul class="nav nav-pills flex-column mb-sm-auto mb-0">
                <li>
                    <a class="nav-link text-white" data-bs-toggle="collapse" href="#userSubmenu" role="button"
                        aria-expanded="false" aria-controls="userSubmenu">
                        <i class="bi bi-people-fill me-2"></i>Người dùng
                        <i class="fas fa-angle-down float-end"></i>
                    </a>
                    <div class="collapse ps-3" id="userSubmenu">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a href="#" class="nav-link text-white">
                                    <i class="fas fa-user-cog me-2"></i> Tài khoản quản trị
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link text-white">
                                    <i class="fas fa-user me-2"></i> Tài khoản khách hàng
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
            <a href="#"><i class="bi bi-gear-fill me-2"></i> Cài đặt</a>
        </aside>

        <!-- Content -->
        <main>
            @yield('content')
        </main>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <span>&copy; 2025 AdminPanel. All rights reserved.</span>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>

</html>
