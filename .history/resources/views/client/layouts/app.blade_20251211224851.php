@php
<<<<<<< HEAD
    $cart = session()->get('cart', []);
    $cartCount = 0;

    foreach ($cart as $item) {
        $cartCount += $item['quantity'];
=======
    $cart = [];
    $cartCount = 0;

    if (auth()->check()) {
        $cartModel = \App\Models\Cart::with(['items.product'])
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();

        if ($cartModel) {
            foreach ($cartModel->items as $ci) {
                $product = $ci->product;
                $cart[$ci->id] = [
                    'name' => $product ? $product->name : '',
                    'price' => (float) $ci->price,
                    'quantity' => (int) $ci->quantity,
                ];
                $cartCount += (int) $ci->quantity;
            }
        }
    } else {
        $sessionCart = session()->get('cart', []);
        foreach ($sessionCart as $id => $item) {
            $cart[$id] = $item;
            $cartCount += $item['quantity'] ?? 0;
        }
    }

    $wishlistItems = collect();
    $wishlistCount = 0;

    if (auth()->check()) {
        $wishlistItems = \App\Models\Wishlist::with('product')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();
        $wishlistCount = $wishlistItems->count();
>>>>>>> fd7b4da683f7cef1efaff108fd509ead4ee20159
    }
@endphp

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">

    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        .client-header {
            background: #fff;
            border-bottom: 1px solid #ebebeb;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .client-header__inner {
            max-width: 1320px;
            margin: 0 auto;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 24px;
        }

        .client-logo {
            font-size: 32px;
            font-weight: 700;
            color: #111;
            text-transform: none;
            display: flex;
            align-items: flex-end;
            gap: 4px;
        }

        .client-search {
            flex: 1;
            display: flex;
            align-items: stretch;
            border: 1px solid #d6d6d6;
            border-radius: 24px;
            overflow: hidden;
            background: #fff;
            max-width: 520px;
        }

        .client-search input {
            flex: 1;
            border: none;
            padding: 10px 16px;
            font-size: 15px;
            outline: none;
        }

        .client-search button {
            background: #3b3b3b;
            border: none;
            width: 48px;
            display: grid;
            place-items: center;
>>>>>>> fd7b4da683f7cef1efaff108fd509ead4ee20159
            color: #fff;
        }

        .client-actions {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-left: 0;
        }

        .client-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #2b2b2b;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .client-pill:hover {
            color: #2b5c73;
        }

        .client-pill__icon {
>>>>>>> fd7b4da683f7cef1efaff108fd509ead4ee20159
            font-size: 20px;
        }

<<<<<<< HEAD
        /* Menu ngang */
        .main-nav>ul {
            list-style: none;
=======
        .client-account {
>>>>>>> fd7b4da683f7cef1efaff108fd509ead4ee20159
            display: flex;
            align-items: center;
            gap: 8px;
            color: #2b2b2b;
        }

        .client-account__icon {
            font-size: 22px;
            color: #6b6b6b;
        }

        .client-account__labels {
            display: flex;
            flex-direction: column;
            font-size: 12px;
            line-height: 1.3;
        }

        .client-account__primary {
            font-weight: 600;
            color: #111;
        }

        .client-account__secondary {
            color: #818181;
            font-size: 13px;
        }

<<<<<<< HEAD
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
=======
        .client-account__secondary.dropdown-toggle::after {
            margin-left: 6px;
        }

        .client-cart {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #111;
            position: relative;
        }

        .client-cart__badge {
            position: absolute;
            top: -6px;
            right: -12px;
            background: #ff6624;
>>>>>>> fd7b4da683f7cef1efaff108fd509ead4ee20159
            color: #fff;
            border-radius: 50%;
            min-width: 18px;
            height: 18px;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .client-nav {
            border-top: 1px solid #ebebeb;
            background: #fff;
        }

        .client-nav__inner {
            max-width: 1320px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            justify-content: center;
        }

        .client-nav ul {
            list-style: none;
            display: flex;
            gap: 24px;
            margin: 0;
            padding: 10px 0;
            color: #2b2b2b;
            font-weight: 500;
        }

        .client-nav li {
            position: relative;
        }

        ::-webkit-scrollbar {
            display: none;
        }

        .client-nav a,
        .client-nav button {
            color: inherit;
            background: none;
            border: none;
            font: inherit;
            padding: 0;
            cursor: pointer;
        }

        .client-nav .dropdown-menu {
            display: none;
            position: absolute;
            top: 100% ;
            left: 0;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.08);
            min-width: 220px;
            padding: 8px 0;
            z-index: 1002;
        }

        .client-nav .dropdown-menu li a {
            display: block;
            padding: 10px 18px;
            color: #333;
            font-weight: 400;
            white-space: nowrap;
        }

        .client-nav .dropdown-menu li a:hover {
            background: #f6f6f6;
            color: #2b5c73;
        }

        .client-nav li:hover>.dropdown-menu {
            display: block;
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

<<<<<<< HEAD
=======

>>>>>>> fd7b4da683f7cef1efaff108fd509ead4ee20159
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
        }

        */ .article-card:hover {
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

    <header class="client-header">
        <div class="client-header__inner">
            <a href="{{ route('client.home') }}" class="client-logo">
                Meteor
            </a>

            <form action="{{ route('client.product.search') }}" method="GET" class="client-search">
>>>>>>> fd7b4da683f7cef1efaff108fd509ead4ee20159
                <input type="text" name="query" placeholder="Tìm kiếm sản phẩm..."
                    value="{{ $searchQuery ?? '' }}">
                <button type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </form>

<<<<<<< HEAD
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
=======
            <div class="client-actions">
                <div class="client-cart">
                    @auth
                        <a data-bs-toggle="offcanvas" href="#wishlistCanvas" role="button" class="client-pill">
                            <i class="bi bi-heart client-pill__icon"></i>
                        </a>
                        <span class="client-cart__badge {{ $wishlistCount > 0 ? '' : 'd-none' }}" data-wishlist-badge>
                            {{ $wishlistCount }}
                        </span>
                    @else
                        <a href="{{ route('client.login') }}" class="client-pill">
                            <i class="bi bi-heart client-pill__icon"></i>
                        </a>
                    @endauth
                </div>

                <div class="client-cart">
                    @auth
                        <a data-bs-toggle="offcanvas" href="#cartCanvas" role="button" class="client-pill">
                            <i class="bi bi-cart3 client-pill__icon"></i>
                        </a>
                        @if ($cartCount > 0)
                            <span class="client-cart__badge">{{ $cartCount }}</span>
                        @endif
                    @else
                        <a href="{{ route('client.login') }}" class="client-pill">
                            <i class="bi bi-cart3 client-pill__icon"></i>
                        </a>
                    @endauth
                </div>

                <div class="client-account">
                    <i class="fa-regular fa-user client-account__icon"></i>
                    <div class="client-account__labels">
                        @auth
                            <span class="client-account__primary">{{ Auth::user()->name }}</span>
                            <div class="dropdown">
                                <a class="client-account__secondary dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Tài khoản của tôi
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end mt-2">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('client.account.wallet.index') }}">
                                            <i class="bi bi-wallet2 me-2"></i>Ví của tôi
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('client.account.orders.index') }}">
                                            <i class="bi bi-receipt-cutoff me-2"></i>Đơn hàng
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form action="{{ route('client.logout') }}" method="POST">
                                            @csrf
                                            <button class="dropdown-item" type="submit">
                                                <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <a class="client-account__primary" href="{{ route('client.login') }}">Đăng nhập</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <nav class="client-nav">
            <div class="client-nav__inner">
                <ul>
                    <li>
                        <a href="#" class="dropdown-toggle">Sản phẩm </a>
                        <ul class="dropdown-menu">
                            @forelse ($childCategories as $child)
                                <li>
                                    <a href="{{ route('client.product.category', $child->slug) }}">
                                        {{ $child->name }}
                                    </a>
                                </li>
                            @empty
                                <li><span class="d-block px-3 py-2 text-muted">Đang cập nhật</span></li>
                            @endforelse
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="dropdown-toggle">Phòng </a>
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
                    <li><a href="{{ route('client.contact.list') }}">Thiết kế nội thất</a></li>
                    <li><a href="{{ route('client.blogs.list') }}">Bài Viết</a></li>
                    <li><a href="#">Góc chia sẻ</a></li>
                </ul>
            </div>
        </nav>
    </header>


>>>>>>> fd7b4da683f7cef1efaff108fd509ead4ee20159
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

<<<<<<< HEAD
                <div class="d-flex justify-content-between fw-bold mb-3">
                    <span>Tổng:</span>
                    <span
                        id="cart-total">{{ number_format(array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart))) }}₫</span>
                </div>

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
=======
    {{-- Offcanvas Wishlist --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="wishlistCanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Danh sách yêu thích</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column" style="height: 100%;">
            @if (auth()->check())
                @if ($wishlistItems->count())
                    <ul class="list-group mb-3">
                        @foreach ($wishlistItems as $wishlist)
                            @php $product = $wishlist->product; @endphp
                            @if ($product)
                                <li class="list-group-item d-flex align-items-center position-relative">
                                    <a href="{{ route('client.product.detail', $product->slug) }}"
                                        class="d-flex flex-column text-decoration-none text-dark flex-grow-1 pe-4">
                                        <strong>{{ $product->name }}</strong>
                                        <small class="text-muted">
                                            {{ number_format($product->price, 0, ',', '.') }}₫
                                        </small>
                                    </a>
                                    <button class="btn-close position-absolute top-0 end-0 m-2 remove-wishlist-item"
                                        data-product-id="{{ $product->id }}"></button>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                    <div class="mt-auto d-flex flex-column gap-2">
                        <a href="{{ route('client.wishlist.index') }}" class="btn btn-outline-dark w-100">
                            Xem danh sách chi tiết
                        </a>
                    </div>
                @else
                    <p>Danh sách yêu thích trống.</p>
                    <a href="{{ route('client.products.index') }}" class="btn btn-primary w-100 mt-2">
                        Khám phá sản phẩm
                    </a>
                @endif
            @else
                <p>Vui lòng đăng nhập để xem danh sách yêu thích.</p>
                <a href="{{ route('client.login') }}" class="btn btn-primary w-100 mt-2">
                    Đăng nhập
                </a>
            @endif
        </div>
    </div>

    {{-- Offcanvas Cart --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="cartCanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Giỏ hàng</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column" style="height: 100%;">
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
                    <span id="cart-total">
                        {{ number_format(array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart))) }}₫
                    </span>
                </div>

                <div class="mt-auto d-flex flex-column gap-2">
                    <a href="{{ route('cart.index') }}" class="btn btn-dark w-100">Xem giỏ hàng</a>
                </div>
            @else
                <p>Giỏ hàng trống.</p>
                <a href="{{ route('client.home') }}" class="btn btn-primary w-100 mt-2">
                    Quay về trang chủ
                </a>
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

    {{-- SweetAlert2 for client-side modals (báo cáo bình luận, thông báo đẹp) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Thành công',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: "{{ session('error') }}",
            });
        </script>
    @endif

    @stack('scripts')

    <!-- Bootstrap JS Bundle to enable dropdown -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Chatbox Widget - Dynamic -->
    <div class="chatbox-wrapper" id="chatboxWrapper" style="display: none;">
        <!-- Chat Icon Button -->
        <div class="chatbox-toggle" id="chatboxToggle">
            <i class="bi bi-chat-dots-fill chatbox-toggle__icon"></i>
            <span class="chatbox-toggle__badge" id="chatBadge" style="display: none;">0</span>
        </div>

        <!-- Chat Popup -->
        <div class="chatbox-popup" id="chatboxPopup">
            <div class="chatbox-popup__header" id="chatboxHeader">
                <div class="chatbox-popup__header-info">
                    <div class="chatbox-popup__avatar">
                        <i class="bi bi-headset"></i>
                    </div>
                    <div class="chatbox-popup__header-text">
                        <h4 id="chatboxTitle">Hỗ trợ trực tuyến</h4>
                        <span class="chatbox-popup__status" id="chatboxStatus">
                            <span class="chatbox-popup__status-dot"></span>
                            <span id="chatboxStatusText">Trực tuyến</span>
                        </span>
                    </div>
                </div>
                <button class="chatbox-popup__close" id="chatboxClose">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="chatbox-popup__messages" id="chatMessages">
                <!-- Messages will be loaded dynamically -->
                <div class="chatbox-loading" id="chatLoading">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span>Đang tải...</span>
                </div>
            </div>

            <div class="chatbox-popup__quick-replies" id="chatQuickReplies">
                <!-- Quick replies will be loaded dynamically -->
            </div>

            <div class="chatbox-popup__input">
                <input type="file" id="chatImageInput" accept="image/*" style="display: none;">
                <button class="chatbox-popup__attach" id="chatAttach" title="Gửi hình ảnh">
                    <i class="bi bi-image"></i>
                </button>
                <input type="text" id="chatInput" placeholder="Nhập tin nhắn..." autocomplete="off">
                <button class="chatbox-popup__send" id="chatSend">
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>
            <!-- Image Preview -->
            <div class="chatbox-popup__preview" id="chatImagePreview" style="display: none;">
                <div class="chatbox-popup__preview-inner">
                    <img src="" alt="Preview" id="chatPreviewImg">
                    <button class="chatbox-popup__preview-remove" id="chatPreviewRemove">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Chatbox Wrapper */
        .chatbox-wrapper {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* Chat Toggle Button */
        .chatbox-toggle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .chatbox-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 25px rgba(102, 126, 234, 0.5);
        }

        .chatbox-toggle__icon {
            font-size: 28px;
            color: #fff;
        }

        .chatbox-toggle__badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff4757;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
        }

        /* Chat Popup */
        .chatbox-popup {
            position: absolute;
            bottom: 75px;
            right: 0;
            width: 380px;
            max-height: 520px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            display: none;
            flex-direction: column;
            overflow: hidden;
            animation: chatboxSlideUp 0.3s ease;
        }

        .chatbox-popup.active {
            display: flex;
        }

        @keyframes chatboxSlideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Chat Header */
        .chatbox-popup__header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chatbox-popup__header-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .chatbox-popup__avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .chatbox-popup__header-text h4 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }

        .chatbox-popup__status {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            opacity: 0.9;
        }

        .chatbox-popup__status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #2ecc71;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .chatbox-popup__close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }

        .chatbox-popup__close:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Chat Messages */
        .chatbox-popup__messages {
            flex: 1;
            padding: 16px;
            overflow-y: auto;
            background: #f8f9fa;
            max-height: 280px;
        }

        .chatbox-message {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
        }

        .chatbox-message--sent {
            flex-direction: row-reverse;
        }

        .chatbox-message__avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 14px;
            flex-shrink: 0;
        }

        .chatbox-message--sent .chatbox-message__avatar {
            background: #3498db;
        }

        .chatbox-message__content {
            max-width: 70%;
            background: #fff;
            padding: 10px 14px;
            border-radius: 16px;
            border-top-left-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .chatbox-message--sent .chatbox-message__content {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border-radius: 16px;
            border-top-right-radius: 4px;
        }

        .chatbox-message__content p {
            margin: 0 0 6px 0;
            font-size: 14px;
            line-height: 1.4;
        }

        .chatbox-message__content p:last-of-type {
            margin-bottom: 0;
        }

        .chatbox-message__time {
            font-size: 11px;
            color: #999;
            display: block;
            margin-top: 6px;
        }

        .chatbox-message--sent .chatbox-message__time {
            color: rgba(255, 255, 255, 0.7);
        }

        /* Quick Replies */
        .chatbox-popup__quick-replies {
            padding: 12px 16px;
            background: #fff;
            border-top: 1px solid #eee;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .chatbox-quick-reply {
            background: #f0f2f5;
            border: 1px solid #e4e6eb;
            border-radius: 20px;
            padding: 8px 14px;
            font-size: 13px;
            color: #333;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .chatbox-quick-reply:hover {
            background: #667eea;
            color: #fff;
            border-color: #667eea;
        }

        /* Chat Input */
        .chatbox-popup__input {
            padding: 12px 16px;
            background: #fff;
            border-top: 1px solid #eee;
            display: flex;
            gap: 10px;
        }

        .chatbox-popup__input input {
            flex: 1;
            border: 1px solid #e4e6eb;
            border-radius: 24px;
            padding: 10px 16px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }

        .chatbox-popup__input input:focus {
            border-color: #667eea;
        }

        .chatbox-popup__send {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s;
        }

        .chatbox-popup__send:hover {
            transform: scale(1.05);
        }

        /* Attach button */
        .chatbox-popup__attach {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #f0f2f5;
            border: none;
            color: #667eea;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            font-size: 18px;
        }

        .chatbox-popup__attach:hover {
            background: #667eea;
            color: #fff;
        }

        /* Image Preview */
        .chatbox-popup__preview {
            padding: 10px 16px;
            background: #f8f9fa;
            border-top: 1px solid #eee;
        }

        .chatbox-popup__preview-inner {
            position: relative;
            display: inline-block;
        }

        .chatbox-popup__preview img {
            max-width: 150px;
            max-height: 100px;
            border-radius: 8px;
            object-fit: cover;
        }

        .chatbox-popup__preview-remove {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #dc3545;
            border: 2px solid #fff;
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        /* Image in message */
        .chatbox-message__image {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .chatbox-message__image:hover {
            transform: scale(1.02);
        }

        /* Image modal */
        .chatbox-image-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.9);
            z-index: 10002;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .chatbox-image-modal img {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
        }

        .chatbox-image-modal__close {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: #fff;
            font-size: 20px;
            cursor: pointer;
        }

        /* Bot message style */
        .chatbox-message--bot .chatbox-message__avatar {
            background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
        }

        .chatbox-message--bot .chatbox-message__content {
            background: #e8f5e9;
            border-left: 3px solid #00b894;
        }

        /* Loading */
        .chatbox-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 20px;
            color: #666;
        }

        /* Typing indicator */
        .chatbox-typing {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
        }

        .chatbox-typing__dots {
            display: flex;
            gap: 4px;
        }

        .chatbox-typing__dots span {
            width: 8px;
            height: 8px;
            background: #667eea;
            border-radius: 50%;
            animation: typingBounce 1.4s infinite ease-in-out;
        }

        .chatbox-typing__dots span:nth-child(1) {
            animation-delay: -0.32s;
        }

        .chatbox-typing__dots span:nth-child(2) {
            animation-delay: -0.16s;
        }

        @keyframes typingBounce {

            0%,
            80%,
            100% {
                transform: scale(0);
            }

            40% {
                transform: scale(1);
            }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .chatbox-wrapper {
                bottom: 16px;
                right: 16px;
            }

            .chatbox-popup {
                width: calc(100vw - 32px);
                right: 0;
                bottom: 70px;
            }

            .chatbox-toggle {
                width: 54px;
                height: 54px;
            }

            .chatbox-toggle__icon {
                font-size: 24px;
            }
        }

        .chatbox-wrapper.chatbox-hidden {
            display: none !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chatbox elements
            const chatboxWrapper = document.getElementById('chatboxWrapper');
            const chatboxToggle = document.getElementById('chatboxToggle');
            const chatboxPopup = document.getElementById('chatboxPopup');
            const chatboxClose = document.getElementById('chatboxClose');
            const chatInput = document.getElementById('chatInput');
            const chatSend = document.getElementById('chatSend');
            const chatMessages = document.getElementById('chatMessages');
            const chatQuickReplies = document.getElementById('chatQuickReplies');
            const chatBadge = document.getElementById('chatBadge');
            const chatLoading = document.getElementById('chatLoading');
            const chatboxTitle = document.getElementById('chatboxTitle');
            const chatboxStatusText = document.getElementById('chatboxStatusText');
            const chatboxHeader = document.getElementById('chatboxHeader');
            const chatAttach = document.getElementById('chatAttach');
            const chatImageInput = document.getElementById('chatImageInput');
            const chatImagePreview = document.getElementById('chatImagePreview');
            const chatPreviewImg = document.getElementById('chatPreviewImg');
            const chatPreviewRemove = document.getElementById('chatPreviewRemove');

            // State
            let chatSettings = null;
            let sessionToken = localStorage.getItem('chat_session_token') || '';
            let lastMessageId = 0;
            let pollingInterval = null;
            let isEnabled = false;
            let selectedImage = null;

            // Initialize chatbox
            initChatbox();

            // Image upload handlers
            chatAttach.addEventListener('click', () => chatImageInput.click());

            chatImageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    if (file.size > 5 * 1024 * 1024) {
                        alert('Hình ảnh không được vượt quá 5MB');
                        return;
                    }
                    selectedImage = file;
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        chatPreviewImg.src = e.target.result;
                        chatImagePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });

            chatPreviewRemove.addEventListener('click', function() {
                selectedImage = null;
                chatImageInput.value = '';
                chatImagePreview.style.display = 'none';
            });

            async function initChatbox() {
                try {
                    const response = await fetch('/chat/settings' + (sessionToken ? '?session_token=' +
                        sessionToken : ''));
                    const data = await response.json();

                    if (!data.enabled) {
                        chatboxWrapper.style.display = 'none';
                        return;
                    }

                    isEnabled = true;
                    chatSettings = data.settings;
                    sessionToken = data.session_token;
                    localStorage.setItem('chat_session_token', sessionToken);

                    // Apply settings
                    applySettings(data.settings);

                    // Load messages
                    if (data.messages && data.messages.length > 0) {
                        chatLoading.style.display = 'none';
                        data.messages.forEach(msg => {
                            appendMessage(msg);
                            lastMessageId = Math.max(lastMessageId, msg.id);
                        });
                    } else {
                        chatLoading.style.display = 'none';
                    }

                    // Update unread badge
                    updateBadge(data.unread_count || 0);

                    // Show chatbox
                    chatboxWrapper.style.display = 'block';

                    // Start polling for new messages
                    startPolling();

                } catch (error) {
                    console.error('Failed to initialize chatbox:', error);
                    chatboxWrapper.style.display = 'none';
                }
            }

            function applySettings(settings) {
                // Title
                chatboxTitle.textContent = settings.title || 'Hỗ trợ trực tuyến';

                // Status
                chatboxStatusText.textContent = settings.is_working_hours ? 'Trực tuyến' : 'Ngoài giờ làm việc';

                // Colors
                if (settings.primary_color) {
                    const gradient =
                        `linear-gradient(135deg, ${settings.primary_color} 0%, ${settings.secondary_color || settings.primary_color} 100%)`;
                    chatboxHeader.style.background = gradient;
                    document.querySelector('.chatbox-toggle').style.background = gradient;
                }

                // Quick replies
                if (settings.quick_replies && settings.quick_replies.length > 0) {
                    chatQuickReplies.innerHTML = settings.quick_replies.map(qr => `
                        <button class="chatbox-quick-reply" data-message="${qr.message || qr.text}">
                            <i class="bi ${qr.icon || 'bi-chat'}"></i> ${qr.text}
                        </button>
                    `).join('');

                    // Bind quick reply events
                    document.querySelectorAll('.chatbox-quick-reply').forEach(btn => {
                        btn.addEventListener('click', function() {
                            sendMessage(this.dataset.message);
                        });
                    });
                } else {
                    chatQuickReplies.style.display = 'none';
                }

                // Mobile visibility
                if (!settings.show_on_mobile && window.innerWidth <= 768) {
                    chatboxWrapper.classList.add('chatbox-hidden');
                }
            }

            function appendMessage(msg) {
                const isClient = msg.sender_type === 'client';
                const isBot = msg.sender_type === 'bot';
                const typeClass = isClient ? 'chatbox-message--sent' : (isBot ?
                    'chatbox-message--received chatbox-message--bot' : 'chatbox-message--received');
                const icon = isClient ? 'bi-person-fill' : (isBot ? 'bi-robot' : 'bi-headset');

                // Check if message has image
                let contentHtml = '';
                if (msg.message_type === 'image' && msg.attachment_url) {
                    contentHtml =
                        `<img src="${msg.attachment_url}" class="chatbox-message__image" onclick="openImageModal('${msg.attachment_url}')" alt="Image">`;
                    if (msg.message && msg.message !== '[Hình ảnh]') {
                        contentHtml += `<p>${escapeHtml(msg.message)}</p>`;
                    }
                } else {
                    contentHtml = `<p>${escapeHtml(msg.message)}</p>`;
                }

                const html = `
                    <div class="chatbox-message ${typeClass}" data-id="${msg.id}">
                        <div class="chatbox-message__avatar">
                            <i class="bi ${icon}"></i>
                        </div>
                        <div class="chatbox-message__content">
                            ${msg.sender_name && !isClient ? `<small class="text-muted d-block mb-1">${msg.sender_name}</small>` : ''}
                            ${contentHtml}
                            <span class="chatbox-message__time">${msg.time || 'Vừa xong'}</span>
                        </div>
                    </div>
                `;
                chatMessages.insertAdjacentHTML('beforeend', html);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            // Image modal function (global)
            window.openImageModal = function(imageUrl) {
                const modal = document.createElement('div');
                modal.className = 'chatbox-image-modal';
                modal.innerHTML = `
                    <button class="chatbox-image-modal__close" onclick="this.parentElement.remove()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    <img src="${imageUrl}" alt="Full image">
                `;
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) modal.remove();
                });
                document.body.appendChild(modal);
            };

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            async function sendMessage(message, imageFile = null) {
                if (!message.trim() && !imageFile) return;
                if (!isEnabled) return;

                chatInput.value = '';
                chatInput.disabled = true;
                chatSend.disabled = true;

                // Clear image preview
                if (selectedImage) {
                    chatImagePreview.style.display = 'none';
                    chatImageInput.value = '';
                }

                // Show sent message immediately
                const tempMsg = {
                    id: 'temp-' + Date.now(),
                    message: imageFile ? '📷 Đang gửi hình ảnh...' : message,
                    sender_type: 'client',
                    time: 'Đang gửi...'
                };
                appendMessage(tempMsg);

                try {
                    let response;
                    // Get CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                    'content');

                    if (imageFile) {
                        // Send with FormData for image upload
                        const formData = new FormData();
                        formData.append('_token', csrfToken);

                        formData.append('image', imageFile);
                        if (message.trim()) {
                            formData.append('message', message);
                        }
                        formData.append('session_token', sessionToken);
                        formData.append('page_url', window.location.href);

                        response = await fetch('/chat/send', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken

                            },
                            body: formData
                        });
                    } else {
                        // Send as JSON for text only
                        response = await fetch('/chat/send', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                message: message,
                                session_token: sessionToken,
                                page_url: window.location.href
                            })
                        });
                    }

                    if (!response.ok) {
                        throw new Error('HTTP error! status: ' + response.status);
                    }

                    const data = await response.json();

                    if (data.success) {
                        // Update session token
                        if (data.session_token) {
                            sessionToken = data.session_token;
                            localStorage.setItem('chat_session_token', sessionToken);
                        }

                        // Remove temp message and add real messages
                        const tempEl = document.querySelector(`[data-id="${tempMsg.id}"]`);
                        if (tempEl) tempEl.remove();

                        data.messages.forEach(msg => {
                            appendMessage(msg);
                            lastMessageId = Math.max(lastMessageId, msg.id);
                        });

                        // Clear selected image
                        selectedImage = null;

                        // Play sound if enabled
                        if (chatSettings?.play_sound && data.messages.length > 1) {
                            playNotificationSound();
                        }
                    } else if (data.error) {
                        console.error('Server error:', data.error);
                        const tempEl = document.querySelector(`[data-id="${tempMsg.id}"]`);
                        if (tempEl) {
                            tempEl.querySelector('.chatbox-message__time').textContent = 'Lỗi: ' + data.error;
                        }
                    }
                } catch (error) {
                    console.error('Failed to send message:', error);
                    console.error('Error details:', error.message);
                    // Update temp message to show error
                    const tempEl = document.querySelector(`[data-id="${tempMsg.id}"]`);
                    if (tempEl) {
                        tempEl.querySelector('.chatbox-message__time').textContent = 'Lỗi gửi tin';
                    }
                }

                chatInput.disabled = false;
                chatSend.disabled = false;
                chatInput.focus();
            }

            async function pollNewMessages() {
                if (!isEnabled || !sessionToken) return;

                try {
                    const response = await fetch(
                        `/chat/messages?session_token=${sessionToken}&last_id=${lastMessageId}`);
                    const data = await response.json();

                    if (data.messages && data.messages.length > 0) {
                        data.messages.forEach(msg => {
                            appendMessage(msg);
                            lastMessageId = Math.max(lastMessageId, msg.id);
                        });

                        // Update badge if popup is closed
                        if (!chatboxPopup.classList.contains('active')) {
                            const currentBadge = parseInt(chatBadge.textContent) || 0;
                            updateBadge(currentBadge + data.messages.length);
                        }

                        // Play sound
                        if (chatSettings?.play_sound) {
                            playNotificationSound();
                        }
                    }
                } catch (error) {
                    console.error('Polling error:', error);
                }
            }

            function startPolling() {
                if (pollingInterval) clearInterval(pollingInterval);
                pollingInterval = setInterval(pollNewMessages, 5000);
            }

            function updateBadge(count) {
                if (count > 0) {
                    chatBadge.textContent = count > 99 ? '99+' : count;
                    chatBadge.style.display = 'flex';
                } else {
                    chatBadge.style.display = 'none';
                }
            }

            function playNotificationSound() {
                try {
                    const audio = new Audio(
                        'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2teleQAA'
                        );
                    audio.volume = 0.3;
                    audio.play().catch(() => {});
                } catch (e) {}
            }

            // Event listeners
            chatboxToggle.addEventListener('click', function() {
                chatboxPopup.classList.toggle('active');
                if (chatboxPopup.classList.contains('active')) {
                    updateBadge(0);
                    chatInput.focus();
                }
            });

            chatboxClose.addEventListener('click', function() {
                chatboxPopup.classList.remove('active');
            });

            chatSend.addEventListener('click', function() {
                sendMessage(chatInput.value, selectedImage);
            });

            chatInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && !selectedImage) {
                    sendMessage(chatInput.value);
                }
            });
        });
    </script>
>>>>>>> fd7b4da683f7cef1efaff108fd509ead4ee20159
</body>

</html>
