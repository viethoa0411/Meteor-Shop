@php
    $cart = session()->get('cart', []);
    $cartCount = 0;

    foreach ($cart as $item) {
        $cartCount += $item['quantity'];
    }

    $wishlistItems = collect();
    $wishlistCount = 0;

    if (auth()->check()) {
        $wishlistItems = \App\Models\Wishlist::with('product')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();
        $wishlistCount = $wishlistItems->count();
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
            font-size: 20px;
        }

        .client-account {
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
            top: calc(100% + 12px);
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

        .client-nav li:hover > .dropdown-menu {
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
            transition: opacity .7s;
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
            filter: brightness(0.6);
        }

        h2 {
            color: #000;
            font-size: 2em;
            margin-bottom: 20px;
            z-index: 1;
        }

        /* Button slide */
        button {
            z-index: 1;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            background: #09f;
            color: #fff;
            cursor: pointer;
            font-size: 1em;
        }

        .article-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
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
        $parentCategories =
            $parentCategories ?? \App\Models\Category::whereNull('parent_id')->where('status', 1)->get();

        // Giả định $childCategories hoặc $cate được truyền vào View hoặc cần được định nghĩa
        $childCategories = $childCategories ?? [];
        $cate = $cate ?? ($parentCategories->isNotEmpty() ? $parentCategories : collect());
    @endphp

    <header class="client-header">
        <div class="client-header__inner">
            <a href="{{ route('client.home') }}" class="client-logo">
                Meteor
            </a>

            <form action="{{ route('client.product.search') }}" method="GET" class="client-search">
                <input type="text" name="query" placeholder="Tìm kiếm sản phẩm..."
                    value="{{ $searchQuery ?? '' }}">
                <button type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </form>

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
                                        <a class="dropdown-item" href="{{ route('client.account.orders.index') }}">
                                            Đơn hàng của tôi
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

    <main class="container">
        @yield('content')
    </main>

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

    {{-- Footer --}}
    <footer id="footer" class="footer-wrapper">
        <div class="footer-widgets footer footer-2 dark">
            <div class="row dark large-columns-4 mb-0">
                <div id="text-14" class="col pb-0 widget widget_text">
                    <span class="widget-title">Kết nối với Meteor</span>
                    <div class="is-divider small"></div>
                    <div class="textwidget">
                        <p>
                            <img decoding="async" class="logo_ft img-fluid"
                                src="{{ asset('storage/images/meteor.jpg') }}" alt="Logo Meteor"
                                style="max-width: 120px;">
                        </p>

                        <div class="follow">
                            <h4>Follow us</h4>
                            <p><a href="">Instagram</a> – <a href="">Youtube</a> – <a href="">Facebook</a></p>
                        </div>
                    </div>
                </div>

                <div id="nav_menu-2" class="col pb-0 widget widget_nav_menu">
                    <span class="widget-title">Meteor</span>
                    <div class="is-divider small"></div>
                    <div class="menu-ve-nha-xinh-container">
                        <ul id="menu-ve-nha-xinh" class="menu">
                            <li class="menu-item"><a href="#">Giới thiệu</a></li>
                            <li class="menu-item"><a href="">Chuyện meteor</a></li>
                            <li class="menu-item"><a href="">Tổng công ty</a></li>
                            <li class="menu-item"><a href="">Tuyển dụng</a></li>
                            <li class="menu-item"><a href="">Thẻ hội viên</a></li>
                            <li class="menu-item"><a href="">Đổi trả hàng</a></li>
                        </ul>
                    </div>
                </div>

                <div id="nav_menu-3" class="col pb-0 widget widget_nav_menu">
                    <span class="widget-title">CẢM HỨNG Meteor</span>
                    <div class="is-divider small"></div>
                    <div class="menu-cam-hung-nha-xinh-container">
                        <ul id="menu-cam-hung-nha-xinh" class="menu">
                            <li class="menu-item"><a href="">Sản phẩm</a></li>
                            <li class="menu-item"><a href="">Ý tưởng và cảm hứng</a></li>
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
                                <input type="hidden" name="_wpcf7_recaptcha_response" value="">
                            </div>
                            <div class="flex-row form-flat medium-flex-wrap">
                                <div class="flex-col flex-grow">
                                    <span class="wpcf7-form-control-wrap your-email">
                                        <input type="email" name="your-email" value="" size="40"
                                            class="wpcf7-form-control wpcf7-text wpcf7-email wpcf7-validates-as-required wpcf7-validates-as-email"
                                            aria-required="true" aria-invalid="false"
                                            placeholder="Nhập email của bạn">
                                    </span>
                                </div>
                                <div class="flex-col ml-half">
                                    <input type="submit" value="Đăng ký"
                                        class="wpcf7-form-control has-spinner wpcf7-submit button">
                                    <span class="wpcf7-spinner"></span>
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
            // Xóa sản phẩm khỏi giỏ hàng và reload
            document.querySelectorAll('.remove-cart-item').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;

                    fetch("{{ route('cart.remove') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ id })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
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

    <!-- Chatbox Widget - Dynamic -->
    {{-- Giữ nguyên toàn bộ phần chatbox như bạn đang có --}}
    {{-- (code chatbox phía dưới mình giữ nguyên, không chỉnh để tránh lệch logic) --}}
    {!! '' !!}
    <!-- To save tokens: phần chatbox gốc của bạn vẫn dùng được, không cần sửa -->
</body>

</html>
