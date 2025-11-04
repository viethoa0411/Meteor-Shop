<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>@yield('title',  'Meteor Shop' )</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }
        .nav-link { position: relative; font-weight: 600; font-size: 14px; letter-spacing: .2px; }
        .nav-link:hover { color: #e05b4a; }
        .nav-red { color: #e05b4a; }
        .nav-dd:after { content: '\25BE'; font-size: 10px; margin-left: 6px; }
        .shadow-thin { box-shadow: 0 1px 0 rgba(0,0,0,.06); }
    </style>
    @stack('head')

</head>
<body>
    
    <header class="fixed top-0 left-0 right-0 z-50 bg-white shadow-thin">
        {{-- TOP BAR --}}
        <div class="max-w-7xl mx-auto px-4 h-9 flex items-center justify-between text-[13px] text-gray-600">
            <div class="flex items-center gap-4">
                <a href="#" class="text-red-600 font-semibold">VN</a>
                <a href="#" class="hover:text-gray-800">EN</a>
            </div>
            <div class="flex items-center gap-5">
                <span class="flex items-center gap-2 text-gray-700"><i class="fa-solid fa-phone text-red-600"></i> 0903 884 358</span>
                <a href="#" class="hover:text-gray-800">Giới thiệu</a>
                <a href="#" class="hover:text-gray-800">Khuyến mãi</a>
                <a href="#" class="text-red-600 font-semibold">Giảm giá đặc biệt</a>
            </div>
            <div class="hidden md:flex items-center gap-5">
                <a href="#" title="Hệ thống cửa hàng" class="hover:text-gray-800"><i class="fa-solid fa-location-dot"></i></a>
                <a href="{{ route('client.wishlist.index') }}" title="Yêu thích" class="hover:text-gray-800 relative">
                    <i class="fa-regular fa-heart"></i>
                    @php $wishCount = collect(session('wishlist', []))->count(); @endphp
                    @if($wishCount>0)
                        <span class="absolute -top-2 -right-2 text-[10px] bg-gray-900 text-white rounded-full px-1.5">{{ $wishCount }}</span>
                    @endif
                </a>
                <a href="{{ route('client.compare.index') }}" title="So sánh" class="hover:text-gray-800 relative">
                    <i class="fa-solid fa-code-compare"></i>
                    @php $cmpCount = collect(session('compare', []))->count(); @endphp
                    @if($cmpCount>0)
                        <span class="absolute -top-2 -right-2 text-[10px] bg-gray-900 text-white rounded-full px-1.5">{{ $cmpCount }}</span>
                    @endif
                </a>
                <div class="relative group" id="miniCartWrap">
                    <a href="{{ route('client.cart.index') }}" title="Giỏ hàng" class="hover:text-gray-800 relative flex items-center">
                        <i class="fa-solid fa-cart-shopping"></i>
                        @php $cartCount = collect(session('cart', []))->sum('qty'); @endphp
                        @if($cartCount>0)
                            <span class="absolute -top-2 -right-2 text-[10px] bg-red-600 text-white rounded-full px-1.5">{{ $cartCount }}</span>
                        @endif
                    </a>
                    <div class="invisible opacity-0 pointer-events-none group-hover:pointer-events-auto group-hover:visible group-hover:opacity-100 transition-opacity duration-150 absolute right-0 top-full mt-3 z-50 w-[360px]">
                        <div class="bg-white border border-gray-100 shadow-xl rounded-lg overflow-hidden">
                            @php $cart = collect(session('cart', [])); @endphp
                            @if($cart->count() === 0)
                                <div class="p-5 text-sm text-gray-600">Giỏ hàng trống.</div>
                            @else
                                <div class="max-h-80 overflow-auto divide-y">
                                    @foreach($cart as $item)
                                        <div class="flex items-center gap-3 p-3">
                                            <img src="{{ $item['image'] ? asset('storage/'.$item['image']) : 'https://via.placeholder.com/60x60?text=No+Image' }}" class="w-12 h-12 rounded object-cover" alt="{{ $item['name'] }}">
                                            <div class="flex-1">
                                                <div class="text-sm font-medium text-gray-900 line-clamp-1">{{ $item['name'] }}</div>
                                                <div class="text-[12px] text-gray-500">SL: {{ $item['qty'] }}</div>
                                            </div>
                                            <div class="text-sm font-semibold text-red-600 whitespace-nowrap">{{ number_format(($item['price']??0) * ($item['qty']??1), 0, ',', '.') }} đ</div>
                                        </div>
                                    @endforeach
                                </div>
                                @php $subtotal = $cart->reduce(fn($c,$i)=>$c + ($i['price']??0)*($i['qty']??1), 0); @endphp
                                <div class="p-4">
                                    <div class="flex items-center justify-between mb-3 text-sm">
                                        <span class="text-gray-600">Tạm tính</span>
                                        <span class="font-semibold text-gray-900">{{ number_format($subtotal, 0, ',', '.') }} đ</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('client.cart.index') }}" class="flex-1 px-3 py-2 border rounded-lg text-sm text-gray-700 text-center">Xem giỏ hàng</a>
                                        <a href="{{ route('client.checkout.index') }}" class="flex-1 px-3 py-2 bg-gray-900 text-white rounded-lg text-sm text-center">Thanh toán</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <a href="#" class="hover:text-gray-800">Đăng nhập</a>
                <a href="#" title="Tài khoản" class="hover:text-gray-800"><i class="fa-regular fa-user"></i></a>
            </div>
        </div>

        {{-- MAIN NAV --}}
        <nav class="max-w-7xl mx-auto px-4 h-16 md:h-[68px] flex items-center gap-4">
            {{-- Logo --}}
            <a href="{{ route('client.home') }}" class="px-4 py-2 border border-gray-800 text-gray-900 font-bold tracking-wide">Meteor Shop</a>

            {{-- Menu center --}}
            @php $parents = ($categories ?? collect())->where('parent_id', null)->values(); @endphp
                    <ul class="hidden lg:flex items-center gap-8 mx-6 flex-1 select-none">
                        {{-- Sản phẩm: Mega menu --}}
                        <li class="relative group">
                            <a href="{{ route('client.product.list') }}" class="nav-link uppercase nav-dd flex items-center gap-1 py-2 focus:outline-none">Sản phẩm <i class="fa-solid fa-chevron-down text-[10px]"></i></a>
                            <div class="invisible opacity-0 pointer-events-none group-hover:pointer-events-auto group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100 transition-opacity duration-200 absolute left-0 right-0 top-full z-50 pt-3">
                                <div class="w-full bg-white shadow-xl border border-gray-100 rounded-b-lg overflow-hidden">
                                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-8 p-8 md:min-h-[440px]">
                                        @php $parents = $parents ?? (($categories ?? collect())->where('parent_id', null)->values()); @endphp
                                        @forelse($parents as $parent)
                                            <div class="min-w-[180px] pr-4">
                                                <a href="{{ route('client.product.list', ['category' => $parent->slug]) }}" class="block text-gray-900 font-semibold mb-3 hover:text-blue-600 text-[16px]">{{ $parent->name }}</a>
                                                @php $children = ($categories ?? collect())->where('parent_id', $parent->id); @endphp
                                                <ul class="space-y-1.5 text-[15px] leading-7">
                                                    @foreach($children as $child)
                                                        <li>
                                                            <a class="block px-1 py-2.5 rounded text-gray-700 hover:text-gray-900 hover:bg-gray-50" href="{{ route('client.product.list', ['category' => $child->slug]) }}">{{ $child->name }}</a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @empty
                                            <div class="col-span-full text-center text-gray-500">Đang cập nhật danh mục…</div>
                                        @endforelse
                                        <div class="hidden lg:block lg:col-span-2">
                                            @php $bn = ($banners ?? collect())->first(); @endphp
                                            @if($bn)
                                                <a href="{{ $bn->link ?? '#' }}" class="block overflow-hidden rounded-lg border border-gray-100 h-full">
                                                    <img src="{{ \Illuminate\Support\Str::startsWith($bn->image, ['http://','https://']) ? $bn->image : asset('storage/'.$bn->image) }}" class="w-full h-[300px] md:h-full object-cover" alt="promo">
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        {{-- Phòng: dropdown --}}
                        <li class="relative group">
                            <a href="#" class="nav-link uppercase nav-dd flex items-center gap-1 py-2 focus:outline-none">Phòng <i class="fa-solid fa-chevron-down text-[10px]"></i></a>
                            <div class="invisible opacity-0 pointer-events-none group-hover:pointer-events-auto group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100 transition-opacity duration-200 absolute left-0 top-full z-50 pt-3">
                                <div class="bg-white shadow-xl border border-gray-100 rounded-lg w-72 p-3">
                                    @php $parents = $parents ?? (($categories ?? collect())->where('parent_id', null)->values()); @endphp
                                    @foreach(($parents ?? collect())->take(8) as $room)
                                        <a href="{{ route('client.product.list', ['category' => $room->slug]) }}" class="flex items-center justify-between px-3 py-2.5 rounded hover:bg-gray-50">
                                            <span class="text-gray-800 text-[15px]">{{ $room->name }}</span>
                                            <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                                        </a>
                                    @endforeach
                                </div>
            </div>
                        </li>
                <li><a href="#" class="nav-link uppercase nav-red">Bộ sưu tập</a></li>
                <li><a href="#" class="nav-link uppercase">Thiết kế nội thất</a></li>
                <li><a href="{{ route('client.blog.index') }}" class="nav-link uppercase">Góc cảm hứng</a></li>
                </ul>

            {{-- Search box right --}}
            <form action="{{ route('client.product.search') }}" method="GET" class="hidden md:flex items-center w-[340px]" autocomplete="off">
                <div class="relative w-full">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fa fa-search"></i></span>
                    <input id="globalSearch" type="text" name="q" placeholder="Tìm sản phẩm" value="{{ request('q') ?? '' }}"
                           class="w-full h-10 pl-9 pr-3 rounded-md border border-gray-200 text-[14px] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-300" />
                    <div id="searchSuggest" class="hidden absolute z-50 top-11 left-0 right-0 bg-white border border-gray-200 rounded-md shadow-xl p-3">
                        <div class="text-[12px] text-gray-500 mb-2">Gợi ý</div>
                        <div id="suggestContent" class="space-y-2"></div>
                    </div>
                </div>
            </form>

            {{-- Mobile menu button --}}
            <button class="lg:hidden ml-auto p-2 text-gray-700" id="mobileMenuBtn"><i class="fa fa-bars text-lg"></i></button>
        </nav>

        {{-- Mobile overlay & drawer --}}
        <div class="fixed inset-0 bg-black/40 hidden" id="mobileOverlay"></div>
        <div class="fixed top-0 right-0 h-full w-80 bg-white shadow-2xl translate-x-full transition-transform duration-300 overflow-y-auto" id="mobileMenu">
            <div class="p-5">
                <div class="flex items-center justify-between mb-4">
                    <strong class="text-gray-900">Menu</strong>
                    <button id="closeMobileMenu" class="p-2 text-gray-600"><i class="fa fa-times"></i></button>
                </div>
                <form action="{{ route('client.product.search') }}" method="GET" class="mb-4">
                    <input type="text" name="q" placeholder="Tìm sản phẩm" value="{{ request('q') ?? '' }}"
                           class="w-full h-10 px-3 rounded-md border border-gray-200 text-[14px] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-300" />
                </form>
                <nav class="space-y-1">
                    <a href="{{ route('client.product.list') }}" class="block py-2 text-gray-800">Sản phẩm</a>
                    <a href="#" class="block py-2 text-gray-800">Phòng</a>
                    <a href="#" class="block py-2 text-red-600 font-semibold">Bộ sưu tập</a>
                    <a href="#" class="block py-2 text-gray-800">Thiết kế nội thất</a>
                    <a href="{{ route('client.blog.index') }}" class="block py-2 text-gray-800">Góc cảm hứng</a>
                </nav>
            </div>
        </div>
    </header>

    <div class="h-[100px]"></div>

    <main>
        @yield('content')
    </main>

    <footer class="bg-gray-900 text-gray-400 py-10 px-4">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
            <div>
                <h4 class="text-white text-sm font-semibold mb-4">CHĂM SÓC KHÁCH HÀNG</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-yellow-400 transition-colors">Trung tâm trợ giúp</a></li>
                    <li><a href="#" class="hover:text-yellow-400 transition-colors">Meteor Blog</a></li>
                    <li><a href="#" class="hover:text-yellow-400 transition-colors">Hướng dẫn mua hàng</a></li>
                    <li><a href="#" class="hover:text-yellow-400 transition-colors">Hướng dẫn bán hàng</a></li>
                    <li><a href="#" class="hover:text-yellow-400 transition-colors">Thanh toán</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white text-sm font-semibold mb-4">VỀ METEOR</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-yellow-400 transition-colors">Giới thiệu</a></li>
                    <li><a href="#" class="hover:text-yellow-400 transition-colors">Tuyển dụng</a></li>
                    <li><a href="#" class="hover:text-yellow-400 transition-colors">Liên hệ</a></li>
                    <li><a href="#" class="hover:text-yellow-400 transition-colors">Tin tức</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white text-sm font-semibold mb-4">THEO DÕI CHÚNG TÔI</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-yellow-400 transition-colors">Facebook</a></li>
                    <li><a href="#" class="hover:text-yellow-400 transition-colors">Instagram</a></li>
                    <li><a href="#" class="hover:text-yellow-400 transition-colors">Tiktok</a></li>
                    <li><a href="#" class="hover:text-yellow-400 transition-colors">YouTube</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white text-sm font-semibold mb-4">DANH MỤC</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-yellow-400 transition-colors">Nội thất phòng khách</a></li>
                    <li><a href="#" class="hover:text-yellow-400 transition-colors">Nội thất phòng ngủ</a></li>
                    <li><a href="#" class="hover:text-yellow-400 transition-colors">Nội thất phòng ăn</a></li>
                    <li><a href="#" class="hover:text-yellow-400 transition-colors">Nội thất văn phòng</a></li>
                    <li><a href="#" class="hover:text-yellow-400 transition-colors">Nội thất ngoài trời</a></li>
                </ul>
            </div>
        </div>
        <hr class="border-gray-700 my-8">
        <div class="text-center text-sm">
            © 2025 METEOR SHOP. Tất cả các quyền được bảo lưu.
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const closeMobileMenu = document.getElementById('closeMobileMenu');
            const mobileMenu = document.getElementById('mobileMenu');
            const mobileOverlay = document.getElementById('mobileOverlay');

            function openMobileMenu() {
                mobileMenu.classList.add('translate-x-0','active');
                mobileOverlay.classList.remove('hidden');
                mobileOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
            function closeMobileMenuFunc() {
                mobileMenu.classList.remove('translate-x-0','active');
                mobileOverlay.classList.add('hidden');
                mobileOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }
            mobileMenuBtn && mobileMenuBtn.addEventListener('click', openMobileMenu);
            closeMobileMenu && closeMobileMenu.addEventListener('click', closeMobileMenuFunc);
            mobileOverlay && mobileOverlay.addEventListener('click', closeMobileMenuFunc);
        });
    </script>

    @stack('scripts')
</body>
</html>