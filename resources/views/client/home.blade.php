@extends('client.layout')

@section('title', 'Trang chủ')

@push('head')
@vite('resources/css/home.css')
@endpush

@section('content')

{{-- Hero Section: lấy từ DB banners --}}
<section class="relative w-full h-[70vh] md:h-[80vh] overflow-hidden">
    <div class="hero-slider relative w-full h-full">
        @if(($banners ?? collect())->count() > 0)
            @foreach($banners as $idx => $bn)
                <div class="slide {{ $idx === 0 ? 'active' : '' }}">
                    <img src="{{ \Illuminate\Support\Str::startsWith($bn->image, ['http://','https://']) ? $bn->image : asset('storage/'.$bn->image) }}" alt="{{ $bn->title ?? 'Banner' }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/40"></div>
                    <div class="absolute inset-0 flex items-center justify-center text-center text-white px-6">
                        <div class="max-w-3xl">
                            @if($bn->title)
                                <h1 class="text-3xl md:text-5xl font-bold tracking-tight mb-4">{{ $bn->title }}</h1>
                            @endif
                            @if($bn->link)
                                <a href="{{ $bn->link }}" class="btn-primary">Xem ngay</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @else
    <div class="slide active">
                <img src="https://picsum.photos/2000/1200?random=11" alt="Hero" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/40"></div>
                <div class="absolute inset-0 flex items-center justify-center text-center text-white px-6">
                    <div class="max-w-3xl">
                        <h1 class="text-4xl md:text-6xl font-bold tracking-tight mb-4">Nâng tầm không gian sống</h1>
                        <p class="text-base md:text-lg text-gray-200 mb-8">Bộ sưu tập nội thất tối giản, tinh tế, lấy cảm hứng từ phong cách hiện đại.</p>
                        <div class="flex items-center justify-center gap-3">
                            <a href="#" class="btn-primary">Khám phá ngay</a>
                            <a href="#collections" class="px-6 py-3 bg-white text-gray-900 rounded-lg font-semibold hover:bg-gray-100 transition">Xem bộ sưu tập</a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    {{-- Dots --}}
    @if(($banners ?? collect())->count() > 1)
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20 flex gap-3">
            @foreach($banners as $i => $bn)
                <button class="slider-dot {{ $i === 0 ? 'active' : '' }} w-3 h-3 rounded-full bg-white/70"></button>
            @endforeach
        </div>
    @endif
</section>

{{-- Feature bar: Shipping / Support / Return --}}
<section class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 py-6 grid grid-cols-1 md:grid-cols-3 gap-4 text-gray-800">
        <div class="feature-tile flex items-center gap-3 appear">
            <i class="fa-solid fa-truck-fast text-blue-600 text-xl"></i>
            <div>
                <div class="font-semibold">Giao hàng nhanh</div>
                <div class="text-sm text-gray-500">Miễn phí cho đơn từ 1.000.000đ</div>
            </div>
        </div>
        <div class="feature-tile flex items-center gap-3 appear appear-delay-1">
            <i class="fa-solid fa-headset text-blue-600 text-xl"></i>
            <div>
                <div class="font-semibold">Hỗ trợ 24/7</div>
                <div class="text-sm text-gray-500">Tư vấn thiết kế miễn phí</div>
            </div>
        </div>
        <div class="feature-tile flex items-center gap-3 appear appear-delay-2">
            <i class="fa-solid fa-rotate-left text-blue-600 text-xl"></i>
            <div>
                <div class="font-semibold">Đổi trả dễ dàng</div>
                <div class="text-sm text-gray-500">Trong vòng 7 ngày</div>
            </div>
        </div>
    </div>
</section>

{{-- Brand strip từ DB --}}
<section class="bg-gray-50">
    <div class="brand-strip max-w-7xl mx-auto px-4 py-8 grid grid-cols-2 md:grid-cols-6 gap-6 items-center">
        @forelse(($featuredBrands ?? $brands ?? collect()) as $brand)
            <a href="#" class="text-center text-gray-700 font-semibold hover:text-gray-900 transition">{{ $brand->name }}</a>
        @empty
            <span class="text-center text-gray-400 col-span-2 md:col-span-5">Chưa có thương hiệu</span>
        @endforelse
    </div>
</section>

{{-- New Products --}}
<section class="py-12 md:py-16 px-4 md:px-8 bg-white" id="new">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center gap-3 mb-4">
            <h2 class="section-title text-2xl md:text-3xl text-gray-900">Sản phẩm mới</h2>
            <span class="badge-new">New</span>
        </div>
        <p class="text-gray-600 mb-8 text-sm md:text-base">Hàng mới cập nhật gần đây</p>
        <hr class="mb-8 border-gray-200">

        @if ($newProducts->count() === 0)
            <div class="text-center py-12">
                <p class="text-gray-500">Hiện chưa có sản phẩm mới.</p>
            </div>
        @else 
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                @foreach ($newProducts as $p)
                    <a href="{{ route('client.product.detail', $p->slug) }}" class="product-card group appear">
                        <div class="product-img-container overflow-hidden rounded-lg mb-4">
                            <img src="{{ $p->image ? asset('storage/'.$p->image) : 'https://via.placeholder.com/400x400?text=No+Image' }}" 
                            alt="{{ $p->name }}"
                                 class="product-img w-full aspect-square object-cover transition-transform duration-500 group-hover:scale-110">
                        </div>
                        <h3 class="product-name text-base font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
                            {{ $p->name }}
                        </h3>
                        <div class="product-price text-lg font-bold text-red-600">
                            {{ number_format($p->price, 0, ',', '.') }} đ
                        </div>
                    </a>
                @endforeach
            </div>            
        @endif
    </div>
</section>

{{-- Collections grid từ DB (danh mục cha) --}}
<section id="collections" class="py-12 md:py-16 px-4 md:px-8 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        <div class="grid md:grid-cols-2 gap-6 md:gap-8 items-stretch">
            @php $first = ($topCategories ?? collect())->first(); $rest = ($topCategories ?? collect())->slice(1); @endphp
            @if($first)
                <a href="#" class="collection-card group appear">
                    <img src="https://picsum.photos/1200/800?random=151" alt="{{ $first->name }}">
                    <div class="overlay"></div>
                    <div class="caption">
                        <div class="text-sm uppercase tracking-wider opacity-80">Danh mục</div>
                        <div class="text-2xl font-bold">{{ $first->name }}</div>
                    </div>
                </a>
            @endif
            <div class="grid grid-cols-1 gap-6">
                @foreach($rest as $i => $cat)
                    <a href="#" class="collection-card group appear {{ $i === 0 ? 'appear-delay-1' : 'appear-delay-2' }}">
                        <img src="https://picsum.photos/1200/800?random={{ 152 + $i }}" alt="{{ $cat->name }}">
                        <div class="overlay"></div>
                        <div class="caption">
                            <div class="text-sm uppercase tracking-wider opacity-80">Danh mục</div>
                            <div class="text-xl font-semibold">{{ $cat->name }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
            </div>
</section>

{{-- Best Sellers --}}
<section class="py-12 md:py-16 px-4 md:px-8 bg-white">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center gap-3 mb-4">
            <h2 class="section-title text-2xl md:text-3xl text-gray-900">Bán chạy</h2>
            <span class="badge-new">Hot</span>
        </div>
        <p class="text-gray-600 mb-8 text-sm md:text-base">Lựa chọn nhiều nhất từ khách hàng</p>
        <hr class="mb-8 border-gray-200">
        
        @if (($bestProducts ?? $outstandingProducts ?? collect())->count() === 0)
            <div class="text-center py-12">
                <p class="text-gray-500">Hiện chưa có sản phẩm.</p>
            </div>
    @else   
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                @foreach (($bestProducts ?? $outstandingProducts) as $o)
                    <a href="{{ route('client.product.detail', $o->slug) }}" class="product-card group appear">
                        <div class="product-img-container overflow-hidden rounded-lg mb-4">
                            <img src="{{ $o->image ? asset('storage/'.$o->image) : 'https://via.placeholder.com/400x400?text=No+Image' }}" 
                        alt="{{ $o->name }}"
                                 class="product-img w-full aspect-square object-cover transition-transform duration-500 group-hover:scale-110">
                    </div>
                        <h3 class="product-name text-base font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
                            {{ $o->name }}
                        </h3>
                        <div class="product-price text-lg font-bold text-red-600">
                        {{ number_format($o->price, 0, ',', '.') }} đ
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
</section>

{{-- Blog teaser --}}
<section class="py-12 px-4 bg-white">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center gap-3 mb-6">
            <h2 class="section-title text-2xl md:text-3xl text-gray-900">Góc cảm hứng</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            @forelse(($posts ?? collect()) as $post)
                <a href="#" class="group rounded-2xl overflow-hidden border border-gray-100">
                    <img src="{{ $post->image ? asset('storage/'.$post->image) : 'https://picsum.photos/1200/800?random=401' }}" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-700" alt="{{ $post->title }}">
                    <div class="p-4">
                        <div class="text-sm text-gray-500">{{ optional($post->published_at)->format('d/m/Y') }}</div>
                        <div class="font-semibold text-gray-900 line-clamp-2 group-hover:text-blue-600">{{ $post->title }}</div>
                        <div class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $post->excerpt }}</div>
            </div>
                </a>
            @empty
                <div class="text-gray-500">Chưa có bài viết.</div>
            @endforelse
        </div>
    </div>
</section>

{{-- Newsletter CTA --}}
<section class="py-16 md:py-20 px-4 md:px-8 bg-gray-50">
    <div class="max-w-3xl mx-auto text-center newsletter">
        <h3 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3">Nhận ưu đãi độc quyền</h3>
        <p class="text-gray-600 mb-6">Đăng ký nhận bản tin để cập nhật sản phẩm mới và khuyến mãi</p>
        <form class="flex flex-col sm:flex-row gap-3 justify-center p-4" method="post" action="{{ route('client.newsletter.subscribe') }}">
            @csrf
            <input type="email" name="email" placeholder="Email của bạn" class="px-4 py-3 bg-white border border-gray-200 rounded-lg flex-1 min-w-[240px] focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <button class="btn-primary">Đăng ký</button>
        </form>
    </div>
</section>

@endsection

@push('scripts')
@vite('resources/js/home.js')
@endpush
