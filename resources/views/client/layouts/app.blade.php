<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Meteor Shop')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<<<<<<< HEAD
    {{-- Giữ lại cả hai link CSS nếu bạn có file 'css/app.css' --}}
=======
>>>>>>>  Chức năng login client
    {{-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> --}}
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background: #f9fafb;
            margin: 0;
        }

        /* Tùy chỉnh chung cho Header */
        header {
            max-width: 100%;
            background: #111;
            color: #fff;
            padding: 5px 10px; /* Tùy chỉnh theo file 1 */
        }

        .header {
            color: #fff;
            padding: 10px 0;
            position: relative;
        }

        .container-header {
            max-width: 1300px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .container-header > * {
            align-self: center;
        }

        /* Logo */
        .logo a {
            color: #fff;
            text-decoration: none;
            font-size: 20px;
            font-weight: 600;
        }

        /* Menu ngang */
        .main-nav > ul {
            list-style: none;
            display: flex;
            gap: 20px;
            margin: 0;
            padding: 0;
        }

        .main-nav a {
            color: #fff;
            text-decoration: none;
            font-style: 14px;
            font-weight: 500;
            transition: color 0.3s;
        }

        .main-nav a:hover {
            color: #ffb703;
        }

        /* Dropdown Menu CSS */
        .menu-item {
            position: relative;
        }
        .dropdown-menu {
            display: none !important;
            position: absolute;
            top: 100%;
            left: 0;
            background: #ffffff;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            min-width: 220px;
            z-index: 1002;
            padding: 8px 0;
            flex-direction: column;
        }
        .dropdown-menu li a {
            display: block;
            padding: 10px 14px;
            color: #333;
            white-space: nowrap;
        }
        .dropdown-menu li a:hover {
            background: #f5f5f5;
            color: #007bff;
        }
        .menu-item:hover > .dropdown-menu {
            display: block !important;
        }
        /* End Dropdown Menu CSS */


        /* Ô tìm kiếm */
        .search-box {
            display: flex;
            align-items: center;
            background: #222;
            border-radius: 20px;
            overflow: hidden;
            width: 20%;
            align-self: center;
            margin: 0;
        }

        .search-box input {
            flex: 1; /* Đã sửa lỗi chính tả từ 'float: 1' thành 'flex: 1' */
            padding: 8px 12px;
            border: none;
            background: transparent;
            color: #fff;
        }

        .search-box button {
            background: none;
            border: none;
            color: #fff;
            padding: 8px 10px;
            cursor: pointer;
        }

        .search-box button:hover {
            color: #ffb703;
        }

        /* Icon menu dọc */
        .menu-toggle {
            font-size: 22px;
            cursor: pointer;
            padding: 6px 5px;
            transition: color 0.3s;
        }

        .menu-toggle:hover {
<<<<<<< HEAD
            color: #ffb703;
=======
color: #ffb703;
>>>>>>>  Chức năng login client
        }

        /* MENU DỌC (Sidebar) */
        .vertical-menu {
            position: fixed;
            top: 0;
            right: -33%;
            width: 33%;
            height: 100vh;
            background: rgb(91, 101, 101);
            display: flex;
            flex-direction: column;
            padding: 20px;
            box-shadow: -4px 0 12px rgba(0, 0, 0, 0.3);
            transition: right 0.3s ease;
            z-index: 1001;
        }

        .vertical-menu a {
            color: #fff;
            text-decoration: none;
            padding: 10px 0;
            font-weight: 500;
        }

        .vertical-menu.active {
            right: 0;
        }

        /* Lớp mờ nền */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0, 0, 0, 0.3);
            display: none;
            z-index: 1000;
        }

        .overlay.active {
            display: block;
        }

        /* Footer */
        footer {
            max-width: 100%;
            background-color: #111;
            margin: 0 auto;
            color: #fff;
            padding: 10px 15px;
        }

        /* Các style khác */
        a {
            text-decoration: none;
            color: inherit;
        }

        .product-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .05);
            padding: 16px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-20px);
            padding: 16px;
        }

        .product-card img {
            width: 100%;
            aspect-ratio: 1/1;
            object-fit: cover;
            border-radius: 6px;
            background: #eee;
            transition: transform 0.4s ease;
            display: block;
            transform-origin: center center;
        }
<<<<<<< HEAD

=======
        
>>>>>>>  Chức năng login client
        .product-name {
            font-size: 16px;
            font-weight: 600;
            color: #111;
            margin: 12px 0 4px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-name:hover {
            color: #09f;
            text-decoration: underline;
        }

        .product-price {
            color: #d41;
            font-weight: 600;
            font-size: 15px;
        }

        .grid-products {
            display: grid;
<<<<<<< HEAD
            grid-template-columns: repeat(auto-fill, minmax(calc(90%/4), 1fr));
            align-items: stretch;
=======
grid-template-columns: repeat(auto-fill, minmax(calc(90%/4), 1fr));
            align-items: stretch;  /* đảm bảo các ô cao bằng nhau */
>>>>>>>  Chức năng login client
            gap: 24px;
        }

        .badge-new {
            font-size: 12px;
            font-weight: 500;
            background: #111;
            color: #fff;
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
        }

        .related-wrap {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
        }

        .slide-wrapper {
            position: relative;
            width: 100%;
            max-width: 100%;
            height: 600px;
            overflow: hidden;
            margin-bottom: 24px;
        }

        .slide {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity .7s
        }

        .slide.active {
            opacity: 1;
        }

        .imageSlide {
            width: 100vw;
            height: 90vh;
            object-fit: cover;
            position: absolute;
            z-index: -1;
            filter: brightness(0.6)
        }

        h2 {
            color: #000;
            font-size: 2em;
            margin-bottom: 20px;
            z-index: 1
        }

<<<<<<< HEAD
        /* Đã bỏ comment cho button, sử dụng style từ file 2 */
        button {
=======
        /* button {
>>>>>>>  Chức năng login client
            z-index: 1;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            background: #09f;
            color: #fff;
            cursor: pointer;
            font-style: 1em;
        } */

        .article-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8ox 20px rgba(0, 0, 0, 0.15);
        }

        @media (max-width:776px) {
            .room {
                grid-template-columns: 1fr !important;
            }
        }

        @media (max-width:776px) {
            .grid-products {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 16px;
            }
        }
    </style>
    @stack('head')

</head>
<body>

<<<<<<< HEAD
    @php
        // Lấy danh mục cha (Phòng) nếu chưa có sẵn
        // Giữ lại logic Laravel Blade từ File 1 để đảm bảo Menu Dropdown hoạt động
        $parentCategories = $parentCategories
            ?? \App\Models\Category::whereNull('parent_id')->where('status', 1)->get();

        // Giả định $childCategories hoặc $cate được truyền vào View hoặc cần được định nghĩa
        // Nếu $childCategories chưa được truyền, bạn cần phải định nghĩa nó ở đây hoặc trong Controller
        $childCategories = $childCategories ?? [];
        // Giả định $cate là danh mục dùng cho Menu dọc
        $cate = $cate ?? ($parentCategories->isNotEmpty() ? $parentCategories : collect());
    @endphp

=======
>>>>>>>  Chức năng login client
    <header class="header">
        <div class="container-header">
            {{-- Logo --}}
            <div class="logo">
                <a href="{{ route('client.home') }}">METEOR SHOP</a>
            </div>

            {{-- Menu ngang (Đã gộp và giữ lại Dropdown từ File 1) --}}
            <nav class="main-nav">
                <ul>
<<<<<<< HEAD
                    {{-- Dropdown Sản phẩm (Child Categories) --}}
                    <li class="menu-item dropdown">
                        <a href="#" class="dropdown-toggle">Sản phẩm</a>
                        <ul class="dropdown-menu">
                            @foreach($childCategories as $child)
                                <li>
                                    <a href="{{ route('client.product.category', $child->slug) }}">
                                        {{ $child->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                    {{-- Dropdown Phòng (Parent Categories) --}}
                    <li class="menu-item dropdown">
                        <a href="#" class="dropdown-toggle">Phòng</a>
                        <ul class="dropdown-menu">
                            @foreach($parentCategories as $parent)
                                <li>
                                    <a href="{{ route('client.product.category', $parent->slug) }}">
                                        {{ $parent->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
=======
                   <li><a href="#">Sản phẩm</a></li>
                    <li><a href="#">Phòng</a></li>
>>>>>>>  Chức năng login client
                    <li><a href="#">Bộ sưu tập</a></li>
                    <li><a href="#">Thiết kế nội thất</a></li>
<<<<<<< HEAD
                    {{-- Lấy link Bài Viết từ File 2 --}}
                    <li><a href="{{ route('client.blogs.list') }}">Bài Viết</a></li>
=======
                    <li><a href="{{ route('client.blog.list') }}">Bài Viết</a></li>
>>>>>>>  Giao diện trang blog ở phía người dùng
                    <li><a href="#">Góc chia sẻ</a></li>
                </ul>
            </nav>
<<<<<<< HEAD

            <form action="{{ route('client.product.search') }}" method="GET" class="search-box">
=======
            <!-- Ô tìm kiếm -->
{{-- <form action="#" method="GET" class="search-box"> --}}
            <form action="#" method="GET" class="search-box">
>>>>>>>  Chức năng login client
                <input type="text" name="query" placeholder="Tìm kiếm sản phẩm..." value="{{ $searchQuery ?? '' }}">
                <button type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </form>

<<<<<<< HEAD
=======

            <!-- Icon menu dọc -->
>>>>>>>  Chức năng login client
            <div class="menu-toggle">☰</div>
            
            <div class="ms-auto d-flex align-items-center gap-3" style="margin-left:0 !important;">
                @auth

                    {{-- DROPDOWN USER --}}
                    <div class="dropdown">
                        <a class="text-white dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="#">
                                    Thông tin tài khoản
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('client.logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item" type="submit">Đăng xuất</button>
                                </form>
                            </li>
                        </ul>
                    </div>

                @else
                    {{-- NẾU CHƯA ĐĂNG NHẬP --}}
                    <div class="d-flex w-100">
                        <a class="btn btn-outline-light flex-fill" href="{{ route('client.login') }}">Đăng nhập</a>
                        <a class="btn btn-primary flex-fill ms-2" href="#">Đăng ký</a>
                    </div>
                @endauth
            </div>

            </div>

        <div class="overlay"></div>
<<<<<<< HEAD
        {{-- Đã điều chỉnh logic hiển thị Menu dọc --}}
        @if ($cate->count() > 0)
            <div class="vertical-menu">
                @foreach ($cate as $c)
                    <a href="{{ route('client.product.category', $c->slug) }}">{{ $c->name }}</a>
                @endforeach
            </div>
        @else
            <div class="vertical-menu">
                <a href="#">Hiện chưa có danh mục</a>
            </div>
        @endif
=======
            @if (isset($cate) && $cate->count() > 0)
                <div class="vertical-menu">
                    @foreach ($cate as $c)
                        <a href="#">{{ $c->name }}</a>
                    @endforeach    
                </div>      
            @else
                <div class="vertical-menu">
                    <a href="#">Hiện chưa có danh mục</a>
                </div>
            @endif
>>>>>>>  Chức năng login client
    </header>
<main class="container">
        @yield('content')
    </main>

<<<<<<< HEAD
    {{-- Footer chi tiết từ File 2 (sử dụng HTML structure từ File 2 và style của File 1) --}}
    <footer id="footer" class="footer-wrapper" style="background: #000000; padding:40px 0 20px; font-size:13px; color:#555">
        <div class="footer-widgets footer footer-2 dark">
            <div style="max-width:1200px; margin:auto; display:flex; justify-content:space-between;flex-wrap:wrap; gap:30px">

                {{-- Cột 1: Kết nối với Meteor --}}
                <div style="flex:1; min-width:180px">
                    <h4 style="font-size: 14px; font-weight:600;margin-bottom:12px">Kết nối với Meteor</h4>
                    <div class="textwidget">
                        <p>
                            <img decoding="async" class="logo_ft img-fluid"
                                src="{{ asset('storage/images/meteor.jpg') }}" alt="Logo Meteor"
                                style="max-width: 120px;">
                        </p>
                        <div class="follow">
                            <h4>Follow us</h4>
                            <p>
                                <a href="">Instagram</a> –
                                <a href="">Youtube</a> –
                                <a href="">Facebook</a>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Cột 2: Về METEOR --}}
                <div style="flex:1; min-width:180px">
                    <h4 style="font-size: 14px; font-weight:600;margin-bottom:12px">Meteor</h4>
                    <div class="menu-ve-nha-xinh-container">
                        <ul style="list-style: none; padding: 0;">
                            <li style="margin: 6px 0"><a href="#">Giới thiệu</a></li>
                            <li style="margin: 6px 0"><a href="">Chuyện meteor</a></li>
                            <li style="margin: 6px 0"><a href="">Tổng công ty</a></li>
                            <li style="margin: 6px 0"><a href="">Tuyển dụng</a></li>
                            <li style="margin: 6px 0"><a href="">Thẻ hội viên</a></li>
                            <li style="margin: 6px 0"><a href="">Đổi trả hàng</a></li>
                        </ul>
                    </div>
                </div>

                {{-- Cột 3: CẢM HỨNG Meteor --}}
                <div style="flex:1; min-width:180px">
                    <h4 style="font-size: 14px; font-weight:600;margin-bottom:12px">CẢM HỨNG Meteor</h4>
                    <div class="menu-cam-hung-nha-xinh-container">
                        <ul style="list-style: none; padding: 0;">
                            <li style="margin: 6px 0"><a href="">Sản phẩm</a></li>
                            <li style="margin: 6px 0"><a href="">Ý tưởng và cảm hứng</a></li>
                        </ul>
                    </div>
                </div>

                {{-- Cột 4: Newsletter/Thông tin liên hệ --}}
                <div style="flex:1; min-width:180px">
                    <h4 style="font-size: 14px; font-weight:600;margin-bottom:12px">Newsletter</h4>
                    <div class="text" style="font-size: 0.75rem;">
                        <p>Hãy để lại email của bạn để nhận được những ý tưởng trang trí mới và những thông tin, ưu đãi từ Meteor</p>
                        <p>Email: meteor</p>
                        <p>Hotline: <strong>0397766836</strong></p>
                    </div>
                    {{-- Giữ lại phần form (chưa hoàn chỉnh) để developer tự hoàn thiện --}}
                    <div role="form" class="wpcf7" id="wpcf7-f9-o1" lang="en-US" dir="ltr">
                        <form action="" method="post" class="wpcf7-form init" novalidate="novalidate" data-status="init">
                             </form>
                    </div>
                </div>
            </div>
        </div>

        <hr style="margin:30px auto;width:90%;border:0;border-top:1px solid #333;"> {{-- Đã điều chỉnh màu border cho phù hợp nền đen --}}
        <div style="text-align: center; color:#bdbdbd; font-size: 13px"> {{-- Đã điều chỉnh font size --}}
=======
    <footer style="background: #000000; padding:40px 0 20px; font-size:13px; color:#555">
        <div style="max-width:1200px; margin:auto; display:flex; justify-content:space-between;flex-wrap:wrap; gap:30px">
            <!-- Cột 1 -->
            <div style="flex:1; min-width:180px">
                <h4 style="font-size: 14px; font-weight:600;margin-bottom:12px">CHĂM SÓC KHÁCH HÀNG</h4>
                <p style="margin: 6px 0">Trung tâm trợ giúp</p>
                <p style="margin: 6px 0">Meteor Blog</p>
                <p style="margin: 6px 0">Hướng dẫn mua hàng</p>
                <p style="margin: 6px 0">Hướng dẫn bán hàng</p>
                <p style="margin: 6px 0">Thanh toán</p>
            </div>

             <!-- Cột 2-->
            <div style="flex:1; min-width:180px">
                <h4 style="font-size: 14px; font-weight:600;margin-bottom:12px">VỀ METEOR</h4>
                <p style="margin: 6px 0">Facebook</p>
                <p style="margin: 6px 0">Instagram</p>
                <p style="margin: 6px 0">Tiktok</p>
                <p style="margin: 6px 0">YouTube</p>
            </div>

             <!-- Cột 3 -->
            <div style="flex:1; min-width:180px">
                <h4 style="font-size: 14px; font-weight:600;margin-bottom:12px">THEO DÕI CHÚNG TÔI TRÊN</h4>
                <p style="margin: 6px 0">Trung tâm trợ giúp</p>
                <p style="margin: 6px 0">Meteor Blog</p>
                <p style="margin: 6px 0">Hướng dẫn mua hàng</p>
                <p style="margin: 6px 0">Hướng dẫn bán hàng</p>
                <p style="margin: 6px 0">Thanh toán</p>
            </div>

             <!-- Cột 4 -->
            <div style="flex:1; min-width:180px">
                <h4 style="font-size: 14px; font-weight:600;margin-bottom:12px">DANH MỤC</h4>
                <a href="" style="margin: 6px 0">Nội thất phòng khách</a> <br>
                <a href="" style="margin: 6px 0">Nội thất phòng ngủ</a> <br>
                <a href="" style="margin: 6px 0">Nội thất phòng ăn</a> <br>
                <a href="" style="margin: 6px 0">Nội thất văn phòng</a> <br>
                <a href="" style="margin: 6px 0">Nội thất ngoài trời</a> <br>
            </div>
        </div>
        <hr style="margin:30px auto;width:90%;border:0;border-top:1px solid #ddd;">
        <div style="text-align: center; color:#bdbdbd; font-size: 16px">
>>>>>>>  Chức năng login client
            © 2025 METEOR SHOP. Tất cả các quyền được bảo lưu.
        </div>
    </footer>

<<<<<<< HEAD

    {{-- Script cho menu dọc (giữ lại từ File 1) --}}
=======
    {{-- Script cho menu dọc --}}
>>>>>>>  Chức năng login client
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.querySelector('.menu-toggle');
            const verticalMenu = document.querySelector('.vertical-menu');
            const overlay = document.querySelector('.overlay');

            function closeMenu() {
<<<<<<< HEAD
                verticalMenu.classList.remove('active');
=======
verticalMenu.classList.remove('active');
>>>>>>>  Chức năng login client
                overlay.classList.remove('active');
            }

            menuToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                verticalMenu.classList.toggle('active');
                overlay.classList.toggle('active');
            });

            overlay.addEventListener('click', closeMenu);
            document.addEventListener('click', function(e) {
                if (!verticalMenu.contains(e.target) && !menuToggle.contains(e.target)) {
                    closeMenu();
                }
            });
        });
    </script>
<<<<<<< HEAD
=======
    <!-- Bootstrap JS Bundle to enable dropdown -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
>>>>>>>  Chức năng login client
</body>
</html>
