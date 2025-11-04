@extends('client.layout')

@section('title', $product->name)

@push('head')
@vite('resources/css/product-detail.css')
@endpush

@section('content')
<section class="max-w-7xl mx-auto px-4 py-6 md:py-10">
    {{-- Breadcrumbs --}}
    <nav class="text-[13px] text-gray-500 mb-4 flex items-center gap-2">
        <a href="{{ route('client.home') }}" class="hover:text-blue-600">Trang chủ</a>
        <span>/</span>
        @if($product->category)
            <a href="#" class="hover:text-blue-600">{{ $product->category->name }}</a>
            <span>/</span>
        @endif
        <span class="text-gray-900">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-10">
        {{-- GALLERY --}}
        <div>
            <div class="aspect-square overflow-hidden rounded-xl bg-gray-100 mb-3">
                <img id="pd-main-image" src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/800x800?text=No+Image' }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
            </div>
            <div class="grid grid-cols-4 gap-3">
                <button class="thumb active" data-src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/800x800?text=No+Image' }}">
                    <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/200x200?text=No+Image' }}" class="w-full aspect-square object-cover rounded-lg" alt="thumb">
                </button>
                <button class="thumb" data-src="https://picsum.photos/800/800?random=22">
                    <img src="https://picsum.photos/200/200?random=22" class="w-full aspect-square object-cover rounded-lg" alt="thumb">
                </button>
                <button class="thumb" data-src="https://picsum.photos/800/800?random=23">
                    <img src="https://picsum.photos/200/200?random=23" class="w-full aspect-square object-cover rounded-lg" alt="thumb">
                </button>
                <button class="thumb" data-src="https://picsum.photos/800/800?random=24">
                    <img src="https://picsum.photos/200/200?random=24" class="w-full aspect-square object-cover rounded-lg" alt="thumb">
                </button>
            </div>
        </div>

        {{-- INFO (Sticky on desktop) --}}
        <div class="lg:sticky lg:top-24 self-start">
            <div class="flex items-start justify-between gap-3 mb-2">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
                <form method="post" action="{{ route('client.wishlist.toggle') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button class="text-gray-500 hover:text-red-500" title="Yêu thích" type="submit"><i class="fa-regular fa-heart"></i></button>
                </form>
            </div>

            <div class="flex items-end gap-3 mb-4">
                <span class="text-2xl md:text-3xl font-bold text-red-600">{{ number_format($product->price, 0, ',', '.') }} đ</span>
                @if($product->stock > 0)
                    <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded-full">Còn hàng: {{ $product->stock }}</span>
                @else
                    <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded-full">Hết hàng</span>
                @endif
            </div>

            {{-- Variant selectors --}}
            @php
                $colors = ($product->variants ?? collect())->whereNotNull('color_code')->unique('color_code');
                $sizes = ($product->variants ?? collect())->filter(fn($v)=>!is_null($v->length) || !is_null($v->width) || !is_null($v->height));
            @endphp
            @if($colors->count() > 0)
            <div class="mb-4">
                <div class="text-sm font-medium text-gray-700 mb-2">Màu sắc</div>
                <div class="flex flex-wrap gap-2">
                    @foreach($colors as $v)
                        <button class="color-dot" style="background: {{ $v->color_code }}" title="{{ $v->color_name ?? $v->color_code }}" data-color="{{ $v->color_code }}"></button>
                    @endforeach
                </div>
            </div>
            @endif

            @if($sizes->count() > 0)
            <div class="mb-4">
                <div class="text-sm font-medium text-gray-700 mb-2">Kích thước</div>
                <div class="flex flex-wrap gap-2">
                    @foreach($sizes as $v)
                        <button class="size-pill" data-size="{{ $v->length }}x{{ $v->width }}x{{ $v->height }}">{{ (float)$v->length }} x {{ (float)$v->width }} x {{ (float)$v->height }} cm</button>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Quantity & CTA --}}
            <form class="flex items-center gap-3 mb-4" method="post" action="{{ route('client.cart.add') }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}" />
                <div class="qty flex items-center border border-gray-300 rounded-md overflow-hidden">
                    <button type="button" class="px-3 py-2 text-gray-600" data-qty="-1">-</button>
                    <input id="qty" name="qty" type="number" value="1" min="1" class="w-14 text-center outline-none py-2"/>
                    <button type="button" class="px-3 py-2 text-gray-600" data-qty="1">+</button>
                </div>
                <button class="px-4 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition" type="submit">Thêm vào giỏ</button>
                <a href="{{ route('client.checkout.index') }}" class="btn-primary flex-1 text-center">Mua ngay</a>
            </form>

            {{-- Thông số nhanh --}}
            <div class="rounded-lg border border-gray-200 p-4 mb-4 text-[13px]">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="text-gray-500">Kích thước</div>
                        <div class="font-semibold text-gray-900">{{ $product->length ?? '—' }}{{ isset($product->length) ? ' x ' : '' }}{{ $product->width ?? '' }}{{ isset($product->width) ? ' x ' : '' }}{{ $product->height ?? '' }} {{ isset($product->length) ? 'cm' : '' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Vật liệu</div>
                        <div class="font-semibold text-gray-900">{{ $product->material ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Collection</div>
                        <div class="font-semibold text-gray-900">{{ $product->brand->name ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Mã SP</div>
                        <div class="font-semibold text-gray-900">{{ $product->slug }}</div>
                    </div>
                </div>
            </div>

            {{-- Accordion: Bảo hành / Vận chuyển / Đổi trả --}}
            <div class="accordion">
                <details class="acc-item" open>
                    <summary class="acc-title">Chính sách bảo hành</summary>
                    <div class="acc-content">
                        <ul class="text-[13px] text-gray-700 space-y-2 leading-6">
                            <li>✔ Sản phẩm mới 100% từ Nhà sản xuất, bảo đảm chất lượng.</li>
                            <li>✔ Hỗ trợ lắp đặt nội thành.</li>
                            <li>✔ Bảo hành theo tiêu chuẩn của nhà sản xuất.</li>
                            <li>✔ Đổi trả trong 7 ngày nếu lỗi do nhà sản xuất.</li>
                        </ul>
                    </div>
                </details>
                <details class="acc-item">
                    <summary class="acc-title">Vận chuyển</summary>
                    <div class="acc-content text-[13px] text-gray-700">Giao nhanh nội thành, miễn phí cho đơn từ 1.000.000đ.</div>
                </details>
                <details class="acc-item">
                    <summary class="acc-title">Đổi trả</summary>
                    <div class="acc-content text-[13px] text-gray-700">Đổi trả trong vòng 7 ngày nếu sản phẩm lỗi do NSX.</div>
                </details>
            </div>
        </div>
    </div>

    {{-- Tabs: Mô tả / Thông số kỹ thuật / Đánh giá --}}
    <div class="mt-12">
        <div class="border-b border-gray-200 mb-6">
            <nav class="flex gap-6 overflow-x-auto">
                <button class="tab-link active" data-target="#tab-desc">Mô tả sản phẩm</button>
                <button class="tab-link" data-target="#tab-specs">Thông số kỹ thuật</button>
                <button class="tab-link" data-target="#tab-reviews">Đánh giá</button>
            </nav>
        </div>
        <div id="tab-desc" class="tab-pane active">
            <div class="text-gray-700 leading-7">
                {!! nl2br(e($product->description ?? 'Đang cập nhật...')) !!}
            </div>
        </div>
        <div id="tab-specs" class="tab-pane">
            <div class="overflow-hidden rounded-xl border border-gray-200">
                <table class="w-full text-sm">
                    <tbody>
                        <tr class="border-b border-gray-100"><td class="p-3 text-gray-500 w-1/3">Kích thước</td><td class="p-3">{{ $product->length ?? '—' }}{{ isset($product->length) ? ' x ' : '' }}{{ $product->width ?? '' }}{{ isset($product->width) ? ' x ' : '' }}{{ $product->height ?? '' }} {{ isset($product->length) ? 'cm' : '' }}</td></tr>
                        <tr class="border-b border-gray-100"><td class="p-3 text-gray-500">Vật liệu</td><td class="p-3">{{ $product->material ?? '—' }}</td></tr>
                        <tr class="border-b border-gray-100"><td class="p-3 text-gray-500">Danh mục</td><td class="p-3">{{ $product->category->name ?? '—' }}</td></tr>
                        <tr class="border-b border-gray-100"><td class="p-3 text-gray-500">Thương hiệu</td><td class="p-3">{{ $product->brand->name ?? '—' }}</td></tr>
                        <tr><td class="p-3 text-gray-500">Mã sản phẩm</td><td class="p-3">{{ $product->slug }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="tab-reviews" class="tab-pane">
            @php($reviews = $reviews ?? collect())
            @if($reviews->count() > 0)
                {{-- Nếu đã có dữ liệu đánh giá --}}
                <div class="grid md:grid-cols-3 gap-6">
                    <div>
                        <div class="text-4xl font-bold text-gray-900">{{ number_format($reviews->avg('rating'), 1) }}</div>
                        <div class="text-sm text-gray-500">Trên {{ $reviews->count() }} đánh giá</div>
                    </div>
                    <div class="md:col-span-2 space-y-4">
                        @foreach($reviews as $rv)
                            <div class="border-b border-gray-100 pb-3">
                                <div class="text-sm font-semibold text-gray-800">{{ $rv->user_name ?? 'Ẩn danh' }}</div>
                                <div class="text-[13px] text-gray-500">{{ $rv->created_at ? $rv->created_at->format('d/m/Y') : '' }}</div>
                                <div class="text-[13px] text-gray-700 mt-2">{{ $rv->comment ?? '' }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                {{-- Layout giống ảnh: histogram rỗng + danh sách mẫu ngắn --}}
                <div class="space-y-4">
                    <div class="text-sm text-gray-500">Chưa có đánh giá nào cho sản phẩm.</div>
                </div>
            @endif
        </div>
    </div>

    {{-- Có thể bạn cũng thích --}}
    <div class="mt-12">
        <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-6">Có thể bạn cũng thích</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @forelse($relatedProducts as $rp)
                <a href="{{ route('client.product.detail', $rp->slug) }}" class="product-card group">
                    <div class="product-img-container overflow-hidden rounded-lg mb-3">
                        <img src="{{ $rp->image ? asset('storage/'.$rp->image) : 'https://via.placeholder.com/400x400?text=No+Image' }}" alt="{{ $rp->name }}" class="w-full aspect-square object-cover transition-transform duration-500 group-hover:scale-110">
                    </div>
                    <h3 class="product-name text-gray-900 text-sm font-semibold mb-1 line-clamp-2 group-hover:text-blue-600 transition-colors">{{ $rp->name }}</h3>
                    <div class="product-price text-red-600 font-bold">{{ number_format($rp->price, 0, ',', '.') }} đ</div>
                </a>
            @empty
                <div class="text-gray-500">Chưa có sản phẩm liên quan.</div>
            @endforelse
        </div>
    </div>

    {{-- Sản phẩm vừa xem --}}
    @if(($recentlyViewedProducts ?? collect())->count() > 0)
    <div class="mt-14">
        <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-6">Sản phẩm vừa xem</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($recentlyViewedProducts as $rv)
                <a href="{{ route('client.product.detail', $rv->slug) }}" class="product-card group">
                    <div class="product-img-container overflow-hidden rounded-lg mb-3">
                        <img src="{{ $rv->image ? asset('storage/'.$rv->image) : 'https://via.placeholder.com/400x400?text=No+Image' }}" alt="{{ $rv->name }}" class="w-full aspect-square object-cover transition-transform duration-500 group-hover:scale-110">
                    </div>
                    <h3 class="product-name text-gray-900 text-sm font-semibold mb-1 line-clamp-2 group-hover:text-blue-600 transition-colors">{{ $rv->name }}</h3>
                    <div class="product-price text-red-600 font-bold">{{ number_format($rv->price, 0, ',', '.') }} đ</div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Khối cảm hứng/thiết kế --}}
    <div class="mt-16 grid md:grid-cols-2 gap-6">
        <a href="#" class="group relative rounded-2xl overflow-hidden border border-gray-100">
            <img src="https://picsum.photos/1200/800?random=301" class="w-full h-[260px] md:h-[340px] object-cover transition-transform duration-700 group-hover:scale-105" alt="Mẫu thiết kế phòng khách">
            <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
            <div class="absolute bottom-5 left-5 text-white">
                <div class="text-2xl font-bold">Mẫu thiết kế phòng khách</div>
                <div class="text-sm opacity-80">Phòng khách là không gian chính của ngôi nhà</div>
            </div>
        </a>
        <a href="#" class="group relative rounded-2xl overflow-hidden border border-gray-100">
            <img src="https://picsum.photos/1200/800?random=302" class="w-full h-[260px] md:h-[340px] object-cover transition-transform duration-700 group-hover:scale-105" alt="Đồ trang trí">
            <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
            <div class="absolute bottom-5 left-5 text-white">
                <div class="text-2xl font-bold">Đồ trang trí</div>
                <div class="text-sm opacity-80">Gợi ý cảm hứng và nét sinh động cho không gian</div>
            </div>
        </a>
    </div>

</section>

{{-- Sticky purchase bar (mobile) --}}
<div class="fixed md:hidden bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-3 flex items-center gap-3 z-40">
    <div class="qty flex items-center border border-gray-300 rounded-md overflow-hidden">
        <button class="px-3 py-2 text-gray-600" data-qty="-1">-</button>
        <input id="qty-mobile" type="number" value="1" min="1" class="w-14 text-center outline-none py-2"/>
        <button class="px-3 py-2 text-gray-600" data-qty="1">+</button>
    </div>
    <button class="btn-primary flex-1">Mua ngay</button>
</div>
@endsection

@push('scripts')
@vite('resources/js/product-detail.js')
@endpush
