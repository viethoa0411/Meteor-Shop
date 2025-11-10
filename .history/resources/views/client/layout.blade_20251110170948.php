<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>@yield('title',  'Meteor Shop' )</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background: #f9fafb;
            margin: 0;
        }

        header {
            max-width: 100%;
            padding: 24px;
            background: #111;
            margin: 0 auto;
            color: #fff;
            padding: 5px 10px;
        }

        /* Header chính */
        .header {
            bottom: #111;
            color: #fff;
            padding: 10px 0;
            position: relative;
        }

        .container-header {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        

        /* Logo */
        .logo a{
            color: #fff;
            text-decoration: none;
            font-size: 20px;
            font-weight: 600;
        }

        /* Menu ngang */
        .main-nav ul {
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

        /* Ô tìm kiếm */
        .search-box {
            display: flex;
            align-items: center;
            background: #222;
            border-radius: 20px;
            overflow: hidden;
            width: 20%;
        }

        .search-box input {
            float: 1;
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

        /* Icon ☰ nằm cùng dòng */
        .menu-toggle {
            font-size: 22px;
            cursor: pointer;
            padding: 6px 10px;
            transition: color 0.3s;
        }

        .menu-toggle:hover {
              color: #ffb703;
        }

        /* MENU DỌC */
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
            box-shadow: -4px 0 12px  rgba(0, 0, 0, 0.3);
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

        footer {
            max-width: 100%;
            padding: 24px;
            background-color: #111;
            margin: 0 auto;
            color: #fff;
            padding: 10px 15px;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .product-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,.05);
            padding: 16px;
            display: flex;
            flex-direction: column;
            overflow: hidden; /* giữ ảnh trong khung khi zoom */
            transition: transform 0.3s ease; /* mượt khi hover */
        }

        .product-card:hover {
            transform: translateY(-20px);  /* nâng nhẹ toàn thẻ */
            padding: 16px;
        }

        .product-card img {
            width: 100%;
            aspect-ratio: 1/1; /* giữ tỉ lệ vuông */
            object-fit: cover;
            border-radius: 6px;
            background: #eee;
            transition: transform 0.4s ease; /* hiệu ứng phóng to mượt */
            display: block;
            transform-origin: center center; /* phóng to từ tâm ảnh */
        }

        .product-img {
            
        }
        
        .product-name {
            font-size: 16px;
            font-weight: 600;
            color: #111;
            margin: 12px 0 4px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2 ; /* số dòng muốn hiển thị */
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
            align-items: stretch;  /* đảm bảo các ô cao bằng nhau */
            gap: 24px;
        }

        .badge-new {
            font-size: 12px;
            font-weight: 500;
            background: #111;
            color: #fff;
            display: inline-block;
            padding: 2px 8px ;
            border-radius: 10px;
        }

        .related-wrap {
            display: grid;
            grid-template-columns:  repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
        }

        .slide-wrapper  {
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
            display:flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity .7s
        }

        .slide.active{
            opacity:  1;
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

        button {
            z-index: 1;
            padding: 10px 20px ;
            border: none;
            border-radius: 8px;
            background: #09f;
            color: #fff;
            cursor: pointer;
            font-style: 1em;
        }

        .article-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8ox 20px rgba(0,0,0,0.15);
        }

        @media (max-width:776px) {
            .room{grid-template-columns: 1fr !important;
        }}

        @media (max-width:776px) {
            .grid-products{
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 16px;
        }}
    </style>
    @stack('head')

</head>
<body>
    
    <header class="header">
        <div class="container-header">
            {{-- Logo --}}
            <div class="logo">
                <a href="{{ route('client.home') }}">METEOR SHOP</a>
            </div>

            {{-- Menu ngang --}}
            <nav class="main-nav">
                <ul>
                    <li><a href="#">Sản phẩm</a></li>
                    <li><a href="#">Phòng</a></li>
                    <li><a href="#">Bộ sưu tập</a></li>
                    <li><a href="#">Thiết kế nội thất</a></li>
                    <li><a href="#">Góc chia sẻ</a></li>
                </ul>
            </nav>
            <!-- Ô tìm kiếm -->
            <form action="{{ route('client.product.search') }}" method="GET" class="search-box">
                <input type="text" name="query" placeholder="Tìm kiếm sản phẩm..." value="{{ $searchQuery ?? '' }}">
                <button type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </form>

            <!-- Icon menu dọc -->
            <div class="menu-toggle">☰</div>
        </div>

        <!-- Menu dọc -->
        <div class="overlay"></div>
            @if ($cate->count() === 0)
                <p>Hiện chưa có danh mục.</p>
            @else
                <div class="vertical-menu">
                    @foreach ($cate as $c)
                        <a href="">{{ $c->name }}</a>
                    @endforeach    
                </div>            
            @endif
    </header>

    <main class="cintainer">
        @yield('content')
    </main>

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
            © 2025 METEOR SHOP. Tất cả các quyền được bảo lưu.

        </div>
    </footer>
</body>
</html>