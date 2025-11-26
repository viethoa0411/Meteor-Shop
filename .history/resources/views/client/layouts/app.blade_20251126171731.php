@php
    $cart = session()->get('cart', []);
    $cartCount = 0;

    foreach ($cart as $item) {
        $cartCount += $item['quantity'];
    }
@endphp

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Meteor Shop')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
            padding: 5px 10px;
            /* Tùy chỉnh theo file 1 */
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

        .container-header>* {
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
        .main-nav>ul {
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
            position: absolute;
            top: 100%;
            left: 0;
            background: #ffffff;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
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

        .menu-item:hover>.dropdown-menu {
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
            flex: 1;
            /* Đã sửa lỗi chính tả từ 'float: 1' thành 'flex: 1' */
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
            color: #ffb703;
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
            grid-template-columns: repeat(auto-fill, minmax(calc(90%/4), 1fr));
            align-items: stretch;
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


        /* Đã bỏ comment cho button, sử dụng style từ file 2 */
        button {
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
    @php
        // Lấy danh mục cha (Phòng) nếu chưa có sẵn
        // Giữ lại logic Laravel Blade từ File 1 để đảm bảo Menu Dropdown hoạt động
        $parentCategories =
            $parentCategories ?? \App\Models\Category::whereNull('parent_id')->where('status', 1)->get();

        // Giả định $childCategories hoặc $cate được truyền vào View hoặc cần được định nghĩa
        // Nếu $childCategories chưa được truyền, bạn cần phải định nghĩa nó ở đây hoặc trong Controller
        $childCategories = $childCategories ?? [];
        // Giả định $cate là danh mục dùng cho Menu dọc
        $cate = $cate ?? ($parentCategories->isNotEmpty() ? $parentCategories : collect());
    @endphp

    <header class="header">
        <div class="container-header">
            {{-- Logo --}}
            <div class="logo">
                <a href="{{ route('client.home') }}">METEOR SHOP</a>
            </div>

            {{-- Menu ngang --}}
            <nav class="main-nav">
                <ul>
                    {{-- Dropdown Sản phẩm (Child Categories) --}}
                    <li class="menu-item dropdown">
                        <a href="#" class="dropdown-toggle">Sản phẩm</a>
                        <ul class="dropdown-menu">
                            @foreach ($childCategories as $child)
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
                            @foreach ($parentCategories as $parent)
                                <li>
                                    <a href="{{ route('client.product.category', $parent->slug) }}">
                                        {{ $parent->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                    <li><a href="#">Bộ sưu tập</a></li>
                    <li><a href="#">Thiết kế nội thất</a></li>
                    {{-- Lấy link Bài Viết từ File 2 --}}
                    <li><a href="{{ route('client.blogs.list') }}">Bài Viết</a></li>
                    <li><a href="#">Góc chia sẻ</a></li>
                </ul>
            </nav>

            <!-- Ô tìm kiếm -->
            {{-- <form action="{{ route('client.product.search') }}" method="GET" class="search-box"> --}}
            <form action="{{ route('client.product.search') }}" method="GET" class="search-box">
                <input type="text" name="query" placeholder="Tìm kiếm sản phẩm..."
                    value="{{ $searchQuery ?? '' }}">
                <button type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </form>

            <div class="ms-auto d-flex align-items-center gap-3" style="margin-left:0 !important;">
                @auth
                    <div class="position-relative">
                        <a class="text-white fs-4" data-bs-toggle="offcanvas" href="#cartCanvas" role="button">
                            <i class="bi bi-cart3"></i>
                        </a>
                        @if ($cartCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                style="font-size: 12px;">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </div>
                    {{-- DROPDOWN USER --}}
                    <div class="dropdown">
                        <a class="text-white dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('client.account.orders.index') }}">Đơn hàng của
                                    tôi</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form action="{{ route('client.logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item" type="submit">Đăng xuất</button>
                                </form>
                            </li>
                        </ul>
                    </div>
<<<<<<< HEAD

                @else
                    {{-- NẾU CHƯA ĐĂNG NHẬP --}}
                    <div class="d-flex w-100">
                        <a class="btn btn-outline-light flex-fill" href="{{ route('login') }}">Đăng nhập</a>
                        <a class="btn btn-primary flex-fill ms-2" href="{{ route('register') }}">Đăng ký</a>
                    </div>
                @endauth
            </div>

            </div>

        <!-- Menu dọc -->
        <div class="overlay"></div>
            @if (isset($cate) && $cate->count() > 0)
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
    </header>

=======
                @else
                    <div class="d-flex align-items-center gap-3">
                        {{-- ICON GIỎ HÀNG --}}
                        <div class="position-relative">
                            <a class="text-white fs-4" data-bs-toggle="offcanvas" href="#cartCanvas" role="button">
                                <i class="bi bi-cart3"></i>
                            </a>
                            @if ($cartCount > 0)
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                    style="font-size: 12px;">
                                    {{ $cartCount }}
                                </span>
                            @endif
                        </div>
                        {{-- NÚT ĐĂNG NHẬP --}}
                        <a class="btn btn-outline-light" href="{{ route('client.login') }}">Đăng nhập</a>
                    </div>
                @endauth
            </div>
        </div>
    </header>
>>>>>>> eca3fb6387947a26f91d698ae62b346887ad3fab
    <main class="container">
        @yield('content')
    </main>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="cartCanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Giỏ hàng</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
        </div>
        {{-- giỏ hàng --}}
        <div class="offcanvas-body d-flex flex-column" style="height: 100%;">
            @php
                $cart = session('cart', []);
            @endphp

            @if ($cart && count($cart))
                <ul class="list-group mb-3">
                    @foreach ($cart as $id => $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center position-relative"
                            id="cart-item-{{ $id }}">
                            <div>
                                <strong>{{ $item['name'] }}</strong> <br>
                                Số lượng: {{ $item['quantity'] }}
                            </div>
                            <span>{{ number_format($item['price'] * $item['quantity']) }}₫</span>
                            <button class="btn-close position-absolute top-0 end-0 m-2 remove-cart-item"
                                data-id="{{ $id }}"></button>
                        </li>
                    @endforeach
                </ul>

                <div class="d-flex justify-content-between fw-bold mb-3">
                    <span>Tổng:</span>
                    <span
                        id="cart-total">{{ number_format(array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart))) }}₫</span>
                </div>

<<<<<<< HEAD
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
            © 2025 METEOR SHOP. Tất cả các quyền được bảo lưu.
        </div>
    </footer>

    {{-- Script cho menu dọc --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.querySelector('.menu-toggle');
            const verticalMenu = document.querySelector('.vertical-menu');
            const overlay = document.querySelector('.overlay');

            function closeMenu() {
                verticalMenu.classList.remove('active');
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
</body>
=======
                <!-- Nút luôn ở cuối -->
                <div class="mt-auto d-flex flex-column gap-2">
                    <a href="{{ route('cart.index') }}" class="btn btn-dark w-100">Xem giỏ hàng</a>
                </div>
            @else
                <p>Giỏ hàng trống.</p>
                <a href="{{ route('client.home') }}" class="btn btn-primary w-100 mt-2">Quay về trang chủ</a>
            @endif
        </div>
    </div>
    <footer id="footer" class="footer-wrapper">
        <div class="footer-widgets footer footer-2 dark">
            <div class="row dark large-columns-4 mb-0">
                <div id="text-14" class="col pb-0 widget widget_text"><span class="widget-title">Kết nối với
                        Meteor</span>
                    <div class="is-divider small"></div>
                    <div class="textwidget">
                        <p>
                            <img decoding="async" class="logo_ft img-fluid"
                                src="{{ asset('storage/images/meteor.jpg') }}" alt="Logo Meteor"
                                style="max-width: 120px;">
                        </p>


                        <div class="follow">
                            <h4>Follow us</h4>
                            <p><a href="">Instagram</a>–<a href="">Youtube</a>–<a
                                    href="">Facebook</a></p>
                        </div>
                    </div>
                </div>
                <div id="nav_menu-2" class="col pb-0 widget widget_nav_menu"><span class="widget-title">Meteor</span>
                    <div class="is-divider small"></div>
                    <div class="menu-ve-nha-xinh-container">
                        <ul id="menu-ve-nha-xinh" class="menu">
                            <li id="menu-item-41004"
                                class="menu-item menu-item-type-post_type menu-item-object-page menu-item-41004"><a
                                    href="#">Giới thiệu</a></li>
                            <li id="menu-item-41005"
                                class="menu-item menu-item-type-custom menu-item-object-custom menu-item-41005"><a
                                    href="">Chuyện meteor</a></li>
                            <li id="menu-item-41000"
                                class="menu-item menu-item-type-post_type menu-item-object-page menu-item-41000"><a
                                    href="">Tổng công ty</a></li>
                            <li id="menu-item-41002"
                                class="menu-item menu-item-type-post_type menu-item-object-page menu-item-41002"><a
                                    href="">Tuyển dụng</a></li>
                            <li id="menu-item-41001"
                                class="menu-item menu-item-type-post_type menu-item-object-page menu-item-41001"><a
                                    href="">Thẻ hội viên</a></li>
                            <li id="menu-item-41003"
                                class="menu-item menu-item-type-post_type menu-item-object-page menu-item-41003"><a
                                    href="">Đổi trả hàng</a></li>
                        </ul>
                    </div>
                </div>
                <div id="nav_menu-3" class="col pb-0 widget widget_nav_menu"><span class="widget-title">CẢM HỨNG
                        Meteor</span>
                    <div class="is-divider small"></div>
                    <div class="menu-cam-hung-nha-xinh-container">
                        <ul id="menu-cam-hung-nha-xinh" class="menu">
                            <li id="menu-item-449"
                                class="menu-item menu-item-type-post_type menu-item-object-page menu-item-449"><a
                                    href="">Sản phẩm</a></li>
                            <li id="menu-item-450"
                                class="menu-item menu-item-type-custom menu-item-object-custom menu-item-450"><a
                                    href="">Ý tưởng và cảm hứng</a></li>
                        </ul>
                    </div>
                </div>
                <div id="block_widget-3" class="col pb-0 widget block_widget">
                    <span class="widget-title">Newsletter</span>
                    <div class="is-divider small"></div>
                    <div id="text-2944331817" class="text">
                        <p>Hãy để lại email của bạn để nhận được những ý tưởng trang trí mới và những thông tin, ưu đãi
                            từ Meteor</p>
                        <p>Email: meteor</p>
                        <p>Hotline: <strong>0397766836</strong></p>
                        <style>
                            #text-2944331817 {
                                font-size: 0.75rem;
                            }
                        </style>
                    </div>

                    <div role="form" class="wpcf7" id="wpcf7-f9-o1" lang="en-US" dir="ltr">
                        <div class="screen-reader-response">
                            <p role="status" aria-live="polite" aria-atomic="true"></p>
                            <ul></ul>
                        </div>
                        <form action="" method="post" class="wpcf7-form init" novalidate="novalidate"
                            data-status="init">
                            <div style="display: none;">
                                <input type="hidden" name="_wpcf7" value="9">
                                <input type="hidden" name="_wpcf7_version" value="5.5.2">
                                <input type="hidden" name="_wpcf7_locale" value="en_US">
                                <input type="hidden" name="_wpcf7_unit_tag" value="wpcf7-f9-o1">
                                <input type="hidden" name="_wpcf7_container_post" value="0">
                                <input type="hidden" name="_wpcf7_posted_data_hash" value="">
                                <input type="hidden" name="_wpcf7_recaptcha_response"
                                    value="0cAFcWeA7swwLl_8VvpFI06BH3gsjO68Ua_z5VNFU3hy53nMAl1Ib7MeCY5iXtu94dRupk7wiA0keDJ5HgJdgtgo0EYcDooyKZ63qDfxkzaFXYp5nkEMhcr5_ue_kmeQU92aHNxsy1mWUxkQSKxN8OWCh6dzQdp-KzwjpGSFz4OPB-SOb1hbW1z8pZO8-hDZet1qfO2B5uU3s3GdEUfy1YJxrd7si21y0xUlVXLGtRiCG0t8dNFC_5oplJUw-1SX90fY-210RRm1Ee7D2dBieO58yWy-vKauhvB0yohn7yrNyo9CIvSYVz-QUfGqHLrgkOtkGddun16vrAHo8Z_ElyFdzntv7DI6ZDLfUi_mPDOnaataHiFt2X4nDFOq97xzSs9xEZxMR6SB5R9WTqJtC8lLASyMMnBeUsZBH-PB0yjNhs6B4kD2RMULDnqLynhTXu5sprEQIi3oh-hij4WC9plTBrZgcT5pcoRABIzY5xI6IGrLQfVwqY5tqcpPr0COV8-bFAlVDRQa9NO7AaXdPYQCCeM4aLO9CQvgA4oV4SsCs7gbTRZofv0P1hswqLW-dN1WYbDYRn0OPu3-A1A2RTbPNWikLvekFLE23T5y62gi5akjQVwaIdh5W9dOAcP6Se3m65nJCIk5AJ_fUhmc8HmBG4ieMc9ezZSLa0lG7_WqkTJ4AHm28pSwdK9SYiUdG4xQwZcxHHBW05E3Jex1l4im_aN5gAmzXxOrbckL8vXAzrYDQ7L2jNxTHuzTncUOIs1i8soQ_wUrerU40dgDRKcz-5qMYD6HwW-h8feMooaH2QXYRmbn2FByIMFCr7Bw8jvgyKCDlCJRz7">
                            </div>
                            <div class="flex-row form-flat medium-flex-wrap">
                                <div class="flex-col flex-grow">
                                    <span class="wpcf7-form-control-wrap your-email"><input type="email"
                                            name="your-email" value="" size="40"
                                            class="wpcf7-form-control wpcf7-text wpcf7-email wpcf7-validates-as-required wpcf7-validates-as-email"
                                            aria-required="true" aria-invalid="false"
                                            placeholder="Nhập email của bạn"></span>
                                </div>
                                <div class="flex-col ml-half">
                                    <input type="submit" value="Đăng ký"
                                        class="wpcf7-form-control has-spinner wpcf7-submit button"><span
                                        class="wpcf7-spinner"></span>
                                </div>
                            </div>
                            <div class="wpcf7-response-output" aria-hidden="true"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute-footer dark medium-text-center small-text-center">
            <div class="container clearfix">
                <hr style="margin:30px auto;width:90%;border:0;border-top:1px solid #ddd;">
                <div style="text-align: center; color:#bdbdbd; font-size: 16px">
                    © 2025 METEOR SHOP
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ----- Xóa sản phẩm khỏi giỏ hàng và reload -----
            document.querySelectorAll('.remove-cart-item').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;

                    fetch("{{ route('cart.remove') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                id
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                // Reload lại toàn bộ trang
                                window.location.reload();
                            } else {
                                alert(data.message || 'Có lỗi xảy ra!');
                            }
                        })
                        .catch(err => console.error(err));
                });
            });

        });
    </script>


    @stack('scripts')

    <!-- Bootstrap JS Bundle to enable dropdown -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

>>>>>>> eca3fb6387947a26f91d698ae62b346887ad3fab
</html>
