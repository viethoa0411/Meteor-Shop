@extends('client.layouts.app')

@push('head')
    {{-- SEO Meta Tags --}}
    <title>{{ $product->name }} - {{ config('app.name') }}</title>
    <meta name="description"
        content="{{ $product->short_description ?? Str::limit(strip_tags($product->description), 160) }}">
    <meta name="keywords" content="{{ $product->name }}, {{ $product->category->name ?? '' }}">

    {{-- Open Graph --}}
    <meta property="og:title" content="{{ $product->name }}">
    <meta property="og:description"
        content="{{ $product->short_description ?? Str::limit(strip_tags($product->description), 160) }}">
    <meta property="og:image" content="{{ $product->image ? asset('storage/' . $product->image) : '' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="product">

    {{-- Schema.org JSON-LD --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org/",
        "@type": "Product",
        "name": "{{ $product->name }}",
        "description": "{{ strip_tags($product->description ?? '') }}",
        "image": "{{ $product->image ? asset('storage/' . $product->image) : '' }}",
        "sku": "{{ $product->sku ?? '' }}",
        "brand": {
            "@type": "Brand",
            "name": "{{ $product->brand->name ?? 'Meteor Shop' }}"
        },
        "offers": {
            "@type": "Offer",
            "url": "{{ url()->current() }}",
            "priceCurrency": "VND",
            "price": "{{ $product->display_price }}",
            "availability": "{{ $product->in_stock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}"
        },
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "{{ number_format($ratingAvg, 1) }}",
            "reviewCount": "{{ $totalReviews }}"
        }
    }
    </script>
@endpush

@section('content')
    <div class="container py-4 py-md-5">
        {{-- A. PAGE HEADER - Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb mb-0" style="background:transparent; padding:0;">
                <li class="breadcrumb-item">
                    <a href="{{ route('client.home') }}">Trang chủ</a>
                </li>
                @if ($product->category && $product->category->parent)
                    <li class="breadcrumb-item">
                        <a href="{{ route('client.product.search', ['category' => $product->category->parent->slug]) }}">
                            {{ $product->category->parent->name }}
                        </a>
                    </li>
                @endif
                @if ($product->category)
                    <li class="breadcrumb-item">
                        <a href="{{ route('client.product.search', ['category' => $product->category->slug]) }}">
                            {{ $product->category->name }}
                        </a>
                    </li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>
        {{-- Main Product Layout --}}
        <div class="row g-4 mb-5">
            {{-- B. PRODUCT GALLERY (Left) --}}
            <div class="col-lg-6">
                <div class="product-gallery">
                    {{-- Main Image with Zoom --}}
                    <div class="main-image-wrapper position-relative mb-3"
                        style="aspect-ratio: 1/1; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1); background: #f8f9fa;">
                        <img id="mainProductImage"
                        src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/600x600?text=No+Image' }}"
                            alt="{{ $product->name }}" class="w-100 h-100" style="object-fit: cover; cursor: zoom-in;"
                            onclick="openLightbox(this.src)">
                </div>

                    {{-- Thumbnails --}}
                @if ($product->images && $product->images->count() > 0)
                        <div class="thumbnails d-flex gap-2 flex-wrap justify-content-center">
                            <div class="thumbnail-item {{ !$product->image ? 'active' : '' }}"
                                style="width: 80px; height: 80px; border: 2px solid #ddd; border-radius: 8px; overflow: hidden; cursor: pointer; transition: all 0.3s;"
                                onclick="changeMainImage('{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/600x600?text=No+Image' }}', this)">
                                <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/80x80?text=No+Image' }}"
                                    alt="Main" class="w-100 h-100" style="object-fit: cover;">
                            </div>
                        @foreach ($product->images as $img)
                                <div class="thumbnail-item"
                                    style="width: 80px; height: 80px; border: 2px solid #ddd; border-radius: 8px; overflow: hidden; cursor: pointer; transition: all 0.3s;"
                                    onclick="changeMainImage('{{ asset('storage/' . $img->image) }}', this)">
                                    <img src="{{ asset('storage/' . $img->image) }}" alt="Gallery" class="w-100 h-100"
                                        style="object-fit: cover;">
                            </div>
                        @endforeach
                    </div>
                @endif
                </div>
            </div>

            {{-- Thông tin --}}
            <div
                style="width:50%; border:1px solid #ddd; border-radius:12px; padding:24px; box-shadow:0 4px 10px rgba(0,0,0,0.05); background:#fff;">
                {{-- Tên + Giá --}}
                <h2 style="font-size:28px; font-weight:700; margin-bottom:10px;">{{ $product->name }}</h2>

                {{-- Rating + số lượng đánh giá --}}
                <div class="mb-3 d-flex align-items-center gap-2">
                    <div class="rating-stars" style="color: #f4b400; font-size: 20px;">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= floor($ratingAvg))
                                ★
                            @elseif($i - 0.5 <= $ratingAvg)
                                ☆
                            @else
                                ☆
                            @endif
                        @endfor
                    </div>
                    <span class="text-muted">
                        <strong>{{ number_format($ratingAvg, 1) }}</strong>
                        ({{ $totalReviews }} đánh giá)
                    </span>
                    <a href="#reviews-section" class="text-decoration-none ms-2">Xem đánh giá</a>
                </div>

                {{-- Giá --}}
                <p style="font-size:24px; font-weight:600; color:#d41; margin-bottom:10px;">
                    <span id="price-display">{{ number_format($product->price, 0, ',', '.') }} đ</span>
                </p>

                <p style="font-size:14px; color:#555; margin-bottom:10px;">
                    Còn: <span id="stock-display" style="font-weight:bold;">--</span>
                </p>

                {{--  Cân nặng --}}
                <p style="font-size:14px; color:#555; margin-bottom:10px;">
                    Cân nặng: <span id="weight-display" style="font-weight:bold;">--</span> <span id="weight-unit-display" style="font-weight:bold;"></span>
                </p>


                {{-- Thông tin chung --}}
                <div style="margin-bottom:16px; line-height:1.7; color:#444;">
                    <p><strong>Danh mục:</strong>
                        <a href="{{ route('client.product.category', ['slug' => $product->category->slug ?? '']) }}"
                            style="color:#111; text-decoration:none;">
                            {{ $product->category->name ?? 'Không xác định' }}
                        </a>
                    </p>
                </div>

                {{-- Chọn biến thể --}}
                {{-- CHỌN MÀU --}}
                @if ($product->variants->count() > 0)
                    <div style="margin-bottom:20px;">
                        <label style="font-weight:600; display:block; margin-bottom:6px;">Chọn màu:</label>
                        <div style="display:flex; gap:8px; flex-wrap:wrap;">
                            @foreach ($product->variants->unique('color_name') as $variant)
                                <button type="button" class="btn-variant color-btn"
                                    data-color="{{ $variant->color_name }}"
                                    data-color-code="{{ $variant->color_code ?? '#fff' }}"
                                    style="border:1px solid #ccc;
                                            background-color: {{ $variant->color_code ?? '#fff' }};
                                            color: {{ strtolower($variant->color_name) === 'trắng' ? '#000' : '#fff' }};
                                            padding:6px 12px;
                                            border-radius:6px;
                                            cursor:pointer;">
                                    {{ $variant->color_name }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- CHỌN KÍCH CỠ --}}
                    <div style="margin-bottom:20px;">
                        <label style="font-weight:600; display:block; margin-bottom:6px;">Chọn kích cỡ:</label>
                        <div style="display:flex; gap:8px; flex-wrap:wrap;">
                            @foreach ($product->variants->unique(fn($v) => "{$v->length}x{$v->width}x{$v->height}") as $variant)
                                <button type="button" class="btn-variant size-btn"
                                    data-size="{{ intval($variant->length) }}x{{ intval($variant->width) }}x{{ intval($variant->height) }}"
                                    style="border:1px solid #111;
                                        background:#fff;
                                        color:#111;
                                        padding:6px 12px;
                                        border-radius:6px;
                                        cursor:pointer;">
                                    {{ intval($variant->length) }}x{{ intval($variant->width) }}x{{ intval($variant->height) }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                       
                
                @else
                    {{-- fallback nếu sản phẩm không có variant --}}
                    <div style="margin-bottom:20px;">
                        <label style="font-weight:600; display:block; margin-bottom:6px;">Chọn màu:</label>
                        <button type="button" class="btn-variant active"
                            style="border:1px solid #111; background:#111; color:#fff; padding:6px 12px; border-radius:6px;">
                            {{ $product->color ?? 'Không xác định' }}
                        </button>
                    </div>

                    <div style="margin-bottom:20px;">
                        <label style="font-weight:600; display:block; margin-bottom:6px;">Chọn kích cỡ:</label>
                        <button type="button" class="btn-variant"
                            style="border:1px solid #111; background:#fff; color:#111; padding:6px 12px; border-radius:6px;">
                            {{ $product->length ?? '?' }}x{{ $product->width ?? '?' }}x{{ $product->height ?? '?' }}
                        </button>
                    </div>
                @endif


                {{-- Số lượng --}}
                <div style="display:flex; align-items:center; margin-bottom:24px;">
                    <label style="font-weight:600; margin-right:10px;">Số lượng:</label>
                    <div
                        style="display:flex; align-items:center; border:1px solid #ccc; border-radius:6px; overflow:hidden;">
                        <button type="button" class="minus"
                            style="border:none; background:#fff; color:#000;padding:8px 14px; font-size:18px; cursor:pointer;border-right:1px solid #ccc;">−</button>
                        <input id="product-quantity" type="number" value="1" min="1" autocomplete="off"
                            style="width:60px; text-align:center; border:none; outline:none; font-size:16px; border-right:1px solid #ccc;">
                        <button type="button" class="plus"
                            style="border:none; background:#fff; color:#000;padding:8px 14px; font-size:18px; cursor:pointer;">+</button>
                    </div>
                </div>


                {{-- Nút hành động --}}
                <div style="display:flex; flex-wrap:wrap; gap:16px;">
                    <button type="button" id="wishlist-toggle" class="product-action-btn"
                        data-product-id="{{ $product->id }}" data-liked="{{ $isInWishlist ? 'true' : 'false' }}"
                        style="border: 2px solid #000; color:#000; background:#fff; padding:10px 20px; border-radius:6px; font-weight:500; cursor:pointer;">
                        <i class="bi {{ $isInWishlist ? 'bi-heart-fill text-danger' : 'bi-heart' }} me-1"></i>
                        <span>{{ $isInWishlist ? 'Đã thích' : 'Yêu thích' }}</span>
                    </button>

                    <button id="buy-now-btn" type="button" class="product-action-btn"
                        style="border: 2px solid #000; color:#000; background:#fff; padding:10px 20px; border-radius:6px; font-weight:500; cursor:pointer;">
                        Mua ngay
                    </button>

                    @auth
                        <button id="add-to-cart" type="button" class="product-action-btn"
                            style="border: 2px solid #000; color:#000; background:#fff; padding: 10px 20px; border-radius: 6px; cursor: pointer;">
                            <i class="bi bi-cart"></i> Thêm vào giỏ
                        </button>
                    @else
                        <a href="{{ route('client.login') }}" class="product-action-btn"
                            style="border: 2px solid #000; color:#000; background:#fff; padding: 10px 20px; border-radius: 6px; text-decoration:none; display:inline-flex; align-items:center; gap:8px;">
                            <i class="bi bi-cart"></i> Thêm vào giỏ
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        {{-- D. PRODUCT DETAILS TABS --}}
        <div class="product-details-tabs mb-5">
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-tab" data-bs-toggle="tab"
                        data-bs-target="#description" type="button" role="tab" aria-controls="description"
                        aria-selected="true">
                        <i class="bi bi-file-text me-2"></i>Mô tả
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs" type="button"
                        role="tab" aria-controls="specs" aria-selected="false">
                        <i class="bi bi-gear me-2"></i>Thông số kỹ thuật
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews"
                        type="button" role="tab" aria-controls="reviews" aria-selected="false">
                        <i class="bi bi-star me-2"></i>Đánh giá ({{ $totalReviews }})
                    </button>
                </li>
            </ul>
            <div class="tab-content border border-top-0 rounded-bottom p-4" id="productTabsContent">
                {{-- Description Tab --}}
                <div class="tab-pane fade show active" id="description" role="tabpanel"
                    aria-labelledby="description-tab">
                    <div class="product-description">
                        @if ($product->description)
                            <div class="description-content">
                                {!! nl2br(e($product->description)) !!}
                            </div>
                        @else
                            <div class="text-muted text-center py-4">
                                <i class="bi bi-info-circle fs-4 d-block mb-2"></i>
                                <p class="mb-0">Chưa có mô tả chi tiết cho sản phẩm này.</p>
                            </div>
                        @endif
            </div>
        </div>

                {{-- Specs Tab --}}
                <div class="tab-pane fade" id="specs" role="tabpanel" aria-labelledby="specs-tab">
                    <div class="specs-content">
                        <table class="table table-bordered table-hover mb-0">
                            <tbody>
                                <tr>
                                    <th width="200" class="bg-light">Tên sản phẩm</th>
                                    <td>{{ $product->name }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">SKU</th>
                                    <td>{{ $product->sku ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Danh mục</th>
                                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                                </tr>
                                @if ($product->brand)
                                    <tr>
                                        <th class="bg-light">Thương hiệu</th>
                                        <td>{{ $product->brand->name }}</td>
                                    </tr>
                                @endif
                                @if ($product->length || $product->width || $product->height)
                                    <tr>
                                        <th class="bg-light">Kích thước</th>
                                        <td>{{ $product->length ?? '?' }} x {{ $product->width ?? '?' }} x
                                            {{ $product->height ?? '?' }} cm</td>
                                    </tr>
                                @endif
                                @if ($product->color_code)
                                    <tr>
                                        <th class="bg-light">Mã màu</th>
                                        <td>
                                            <span class="d-inline-flex align-items-center">
                                                <span class="color-swatch me-2"
                                                    style="width: 20px; height: 20px; background-color: {{ $product->color_code }}; border: 1px solid #ddd; border-radius: 3px;"></span>
                                                {{ $product->color_code }}
                                            </span>
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <th class="bg-light">Trạng thái</th>
                                    <td>
                                        @if ($product->in_stock)
                                            <span class="badge bg-success">Còn hàng</span>
                                        @else
                                            <span class="badge bg-danger">Hết hàng</span>
                                        @endif
                                    </td>
                                </tr>
                                @if ($product->stock !== null)
                                    <tr>
                                        <th class="bg-light">Số lượng tồn kho</th>
                                        <td>{{ number_format($product->stock, 0, ',', '.') }} sản phẩm</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Reviews Tab --}}
                <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                    <div id="reviews-section">
                        @include('client.products.partials.reviews')
                    </div>
                </div>
            </div>
        </div>

        {{-- F. RELATED PRODUCTS --}}
        @if ($relatedProducts->count() > 0)
            <div class="related-products mb-5">
                <h3 class="h4 mb-4">Sản phẩm liên quan</h3>
                <div class="row g-4">
                    @foreach ($relatedProducts as $related)
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="{{ route('client.product.detail', ['slug' => $related->slug]) }}"
                                class="text-decoration-none">
                                <div class="card h-100 product-card">
                                    <div class="position-relative" style="aspect-ratio: 1/1; overflow: hidden;">
                                        <img src="{{ $related->image ? asset('storage/' . $related->image) : 'https://via.placeholder.com/400x400?text=No+Image' }}"
                                            alt="{{ $related->name }}" class="card-img-top w-100 h-100"
                                            style="object-fit: cover; transition: transform 0.3s;">
                            </div>
                                    <div class="card-body">
                                        <h6 class="card-title text-dark mb-2"
                                            style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ $related->name }}
                                        </h6>
                                        <p class="card-text text-danger fw-bold mb-0">
                                            {{ number_format($related->display_price, 0, ',', '.') }} đ
                                        </p>
                            </div>
                            </div>
                        </a>
                        </div>
                    @endforeach
                </div>
        </div>
        @endif

        {{-- G. RECENTLY VIEWED PRODUCTS --}}
        <div class="recently-viewed-products mb-5" id="recently-viewed-section" style="display: none;">
            <h3 class="h4 mb-4">Sản phẩm vừa xem</h3>
            <div class="row g-4" id="recently-viewed-list">
                {{-- Loaded via JavaScript from localStorage --}}
            </div>
        </div>
        @php
            $variantOptions = $product->variants
                ->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'color_name' => $variant->color_name,
                        'length' => (int) $variant->length,
                        'width' => (int) $variant->width,
                        'height' => (int) $variant->height,
                        'stock' => (int) $variant->stock,
                        'price' => $variant->price ? (float) $variant->price : (float) $product->price,
                        'weight' => $variant->weight ? (float) $variant->weight : null,
                        'weight_unit' => $variant->weight_unit ?? 'kg',
                    ];
                })
                ->values();
        @endphp {{-- Hiệu ứng hover --}} 

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const wishlistBtn = document.getElementById('wishlist-toggle');

                wishlistBtn?.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    const label = this.querySelector('span');
                    const icon = this.querySelector('i');
                    const originalHTML = this.innerHTML;
                    this.disabled = true;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';

                    fetch("{{ route('client.wishlist.toggle') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                product_id: productId
                            })
                        })
                        .then(async res => {
                            if (res.status === 401) {
                                window.location.href = '{{ route('client.login') }}';
                                return null;
                            }
                            return res.json();
                        })
                        .then(data => {
                            if (!data) return;
                            if (data.status === 'success') {
                                if (data.liked) {
                                    icon.classList.remove('bi-heart');
                                    icon.classList.add('bi-heart-fill', 'text-danger');
                                    wishlistBtn.setAttribute('data-liked', 'true');
                                    if (label) label.textContent = 'Đã thích';
                                } else {
                                    icon.classList.remove('bi-heart-fill', 'text-danger');
                                    icon.classList.add('bi-heart');
                                    wishlistBtn.setAttribute('data-liked', 'false');
                                    if (label) label.textContent = 'Yêu thích';
                                }
                                window.location.reload();
                            } else {
                                alert(data.message || 'Không thể cập nhật danh sách yêu thích.');
                                wishlistBtn.disabled = false;
                                wishlistBtn.innerHTML = originalHTML;
                            }
                        })
                        .catch(() => {
                            alert('Có lỗi xảy ra, vui lòng thử lại.');
                            wishlistBtn.disabled = false;
                            wishlistBtn.innerHTML = originalHTML;
                        });
                });

                // ----- Hiệu ứng hover thẻ sản phẩm liên quan -----
                document.querySelectorAll('.product-card').forEach(card => {
                    card.addEventListener('mouseenter', () => {
                        card.style.transform = 'translateY(-8px)';
                        card.style.boxShadow = '0 6px 14px rgba(0,0,0,0.12)';
                        const img = card.querySelector('img');
                        if (img) img.style.transform = 'scale(1.05)';
                    });
                    card.addEventListener('mouseleave', () => {
                        card.style.transform = 'translateY(0)';
                        card.style.boxShadow = '0 2px 8px rgba(0,0,0,0.05)';
                        const img = card.querySelector('img');
                        if (img) img.style.transform = 'scale(1)';
                    });
                });

                const qtyInput = document.getElementById('product-quantity');
                const minus = document.querySelector('.minus');
                const plus = document.querySelector('.plus');
                // const stockDisplay = document.getElementById('stock-display');
                const priceDisplay = document.getElementById('price-display');
                const weightDisplay = document.getElementById('weight-display');
                const weightUnitDisplay = document.getElementById('weight-unit-display');
                const priceDisplay = document.getElementById('price-display');
                const buyNowBtn = document.getElementById('buy-now-btn');
                const addBtn = document.getElementById('add-to-cart');
                const productId = {{ $product->id }};
                const productVariants = @json($variantOptions);
                const baseProductStock = {{ (int) ($product->stock ?? 0) }};
                const baseProductPrice = {{ (float) $product->price }};

                @auth
                const isAuthenticated = true;
            @else
                const isAuthenticated = false;
            @endauth
                let selectedVariantId = null;
                let currentMaxStock = productVariants.length > 0 ? 0 : baseProductStock;

                const normalize = (value) => (value || '').toString().trim().toLowerCase();
                const parseSize = (size) => {
                    if (!size) {
                        return {
                            length: null,
                            width: null,
                            height: null
                        };
                    }
                    const parts = size.split('x').map(num => parseInt(num, 10) || null);
                    return {
                        length: parts[0] ?? null,
                        width: parts[1] ?? null,
                        height: parts[2] ?? null,
                    };
                };

                const clampQuantity = () => {
                    let val = parseInt(qtyInput.value, 10) || 1;
                    if (val < 1) val = 1;
                    if (currentMaxStock > 0 && val > currentMaxStock) {
                        val = currentMaxStock;
                    }
                    qtyInput.value = val;
                };

                const updateSelectedVariant = () => {
                    const activeColor = document.querySelector('.color-btn.active');
                    const activeSize = document.querySelector('.size-btn.active');

                    if (!activeColor || !activeSize) {
                        selectedVariantId = null;
                        return null;
                    }

                    const {
                        length,
                        width,
                        height
                    } = parseSize(activeSize.dataset.size);

                    const matchedVariant = productVariants.find(variant =>
                        normalize(variant.color_name) === normalize(activeColor.dataset.color) &&
                        Number(variant.length) === Number(length) &&
                        Number(variant.width) === Number(width) &&
                        Number(variant.height) === Number(height)
                    );

                    selectedVariantId = matchedVariant ? matchedVariant.id : null;
                    return matchedVariant;
                };

                const updateStockInfo = () => {
                    if (!stockDisplay) return;
                    
                    if (productVariants.length === 0) {
                        stockDisplay.textContent = baseProductStock;
                        currentMaxStock = baseProductStock;
                        qtyInput.setAttribute('max', baseProductStock);
                        clampQuantity();
                        return;
                    }

                    const selectedVariant = updateSelectedVariant();

                    if (selectedVariant && selectedVariant.stock !== undefined && selectedVariant.stock !== null) {
                        if (selectedVariant.stock > 0) {
                            currentMaxStock = selectedVariant.stock;
                            stockDisplay.textContent = currentMaxStock;
                            qtyInput.setAttribute('max', currentMaxStock);
                        } else {
                            currentMaxStock = 0;
                            stockDisplay.textContent = '0 (Hết hàng)';
                            qtyInput.setAttribute('max', 0);
                        }
                    } else {
                        stockDisplay.textContent = '-- (Vui lòng chọn phân loại)';
                        currentMaxStock = 0;
                        qtyInput.removeAttribute('max');
                    }

                    clampQuantity();
                };

                // Hàm cập nhật cân nặng dựa trên biến thể đã chọn
                const updateWeightInfo = () => {
                    if (!weightDisplay || !weightUnitDisplay) return;
                    
                    if (productVariants.length === 0) {
                        // Nếu không có variant, không hiển thị cân nặng
                        weightDisplay.textContent = '--';
                        weightUnitDisplay.textContent = '';
                        return;
                    }

                    const selectedVariant = updateSelectedVariant();

                    // Nếu chưa chọn đủ màu và kích thước
                    if (!selectedVariant) {
                        weightDisplay.textContent = '-- (Vui lòng chọn phân loại)';
                        weightUnitDisplay.textContent = '';
                        return;
                    }

                    // Nếu tìm thấy biến thể, hiển thị cân nặng
                    if (selectedVariant.weight && selectedVariant.weight > 0) {
                        // Format số để hiển thị đẹp hơn (loại bỏ số 0 thừa)
                        const weightValue = parseFloat(selectedVariant.weight);
                        const formattedWeight = weightValue % 1 === 0 ? weightValue.toString() : weightValue.toFixed(2);
                        weightDisplay.textContent = formattedWeight;
                        weightUnitDisplay.textContent = selectedVariant.weight_unit || 'kg';
                    } else {
                        weightDisplay.textContent = '--';
                        weightUnitDisplay.textContent = '';
                    }
                };

                // Hàm cập nhật giá dựa trên biến thể đã chọn
                const updatePriceInfo = () => {
                    if (!priceDisplay) return;
                    
                    if (productVariants.length === 0) {
                        // Nếu không có variant, hiển thị giá sản phẩm chính
                        priceDisplay.textContent = baseProductPrice.toLocaleString('vi-VN') + ' đ';
                        return;
                    }

                    const selectedVariant = updateSelectedVariant();

                    // Nếu chưa chọn đủ màu và kích thước, hiển thị giá sản phẩm chính
                    if (!selectedVariant) {
                        priceDisplay.textContent = baseProductPrice.toLocaleString('vi-VN') + ' đ';
                        return;
                    }

                    // Nếu tìm thấy biến thể, hiển thị giá của biến thể
                    if (selectedVariant.price) {
                        priceDisplay.textContent = selectedVariant.price.toLocaleString('vi-VN') + ' đ';
                    } else {
                        // Nếu không có giá biến thể, dùng giá sản phẩm chính
                        priceDisplay.textContent = baseProductPrice.toLocaleString('vi-VN') + ' đ';
                    }
                };

            // Cập nhật thông tin kho, cân nặng và giá
            updateStockInfo();
            updateWeightInfo();
            updatePriceInfo();

                minus?.addEventListener('click', () => {
                    let val = parseInt(qtyInput.value, 10) || 1;
                    if (val > 1) {
                        qtyInput.value = val - 1;
                    }
                });

                plus?.addEventListener('click', () => {
                    let val = parseInt(qtyInput.value, 10) || 1;
                    if (productVariants.length > 0 && currentMaxStock === 0) {
                    alert('Vui lòng chọn Màu và Kích cỡ trước! 590');
                        return;
                    }

                    if (currentMaxStock === 0 || val < currentMaxStock) {
                        qtyInput.value = val + 1;
                    } else {
                        alert('Đã đạt giới hạn tồn kho (' + currentMaxStock + ')');
                    }
                });

                qtyInput.addEventListener('change', clampQuantity);

                document.querySelectorAll('.btn-variant').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const isColor = btn.classList.contains('color-btn');
                        const group = isColor ? '.color-btn' : '.size-btn';
                        const wasActive = btn.classList.contains('active');

                        if (wasActive) {
                            // Nếu đã active, hủy chọn
                            btn.classList.remove('active');
                            if (isColor) {
                                const code = btn.dataset.colorCode || '#fff';
                                btn.style.background = code;
                                btn.style.border = '1px solid #ccc';
                                btn.style.boxShadow = 'none';
                                const name = (btn.dataset.color || '').toLowerCase();
                                btn.style.color = name === 'trắng' ? '#000' : '#fff';
                            } else {
                                btn.style.background = '#fff';
                                btn.style.color = '#111';
                                btn.style.border = '1px solid #111';
                                btn.style.boxShadow = 'none';
                            }
                        } else {
                            // Hủy chọn tất cả button cùng nhóm
                            document.querySelectorAll(group).forEach(b => {
                                b.classList.remove('active');
                                if (isColor) {
                                    const code = b.dataset.colorCode || '#fff';
                                    b.style.background = code;
                                    b.style.border = '1px solid #ccc';
                                    b.style.boxShadow = 'none';
                                    const name = (b.dataset.color || '').toLowerCase();
                                    b.style.color = name === 'trắng' ? '#000' : '#fff';
                                } else {
                                    b.style.background = '#fff';
                                    b.style.color = '#111';
                                    b.style.border = '1px solid #111';
                                    b.style.boxShadow = 'none';
                                }
                            });

                            // Chọn button hiện tại
                            btn.classList.add('active');
                            if (isColor) {
                                const code = btn.dataset.colorCode || '#fff';
                                btn.style.background = code;
                                btn.style.border = '2px solid #0d6efd';
                                btn.style.boxShadow = '0 0 0 3px rgba(13,110,253,.2)';
                                const name = (btn.dataset.color || '').toLowerCase();
                                btn.style.color = name === 'trắng' ? '#000' : '#fff';
                            } else {
                                btn.style.background = '#eef5ff';
                                btn.style.color = '#0d6efd';
                                btn.style.border = '2px solid #0d6efd';
                                btn.style.boxShadow = '0 2px 6px rgba(13,110,253,.15)';
                            }
                        }

                        updateStockInfo();
                        updateWeightInfo();
                        updatePriceInfo();
                    });
                });

            if (buyNowBtn && !buyNowBtn.dataset.bound) {
                buyNowBtn.dataset.bound = 'true';
                buyNowBtn.addEventListener('click', function(event) {
                        event.preventDefault();
                        const quantity = parseInt(qtyInput.value, 10) || 1;
                        const colorBtn = document.querySelector('.color-btn.active');
                        const sizeBtn = document.querySelector('.size-btn.active');

                        @if ($product->variants->count() > 0)
                            if (!colorBtn || !sizeBtn) {
                            alert('Vui lòng chọn màu và kích cỡ 637');
                                return;
                            }
                            const selectedVariant = updateSelectedVariant();
                            if (!selectedVariant || !selectedVariant.id) {
                                alert('Không tìm thấy biến thể phù hợp cho lựa chọn hiện tại.');
                                return;
                            }
                        @endif

                        const params = new URLSearchParams({
                            product_id: productId,
                            qty: quantity,
                            type: 'buy_now'
                        });

                        if (selectedVariantId) {
                            params.append('variant_id', selectedVariantId);
                        }
                        if (colorBtn) {
                            params.append('color', colorBtn.dataset.color);
                        }
                        if (sizeBtn) {
                            params.append('size', sizeBtn.dataset.size);
                        }

                    if (isAuthenticated) {
                        window.location.href = '{{ route('client.checkout.index') }}' + '?' + params
                            .toString();
                    } else {
                        window.location.href = '{{ route('client.login') }}';
                    }
                });
            }

            @auth
            if (addBtn && !addBtn.dataset.bound) {
                addBtn.dataset.bound = 'true';
                addBtn.addEventListener('click', (event) => {
                    event.preventDefault();
                    const quantity = parseInt(qtyInput.value, 10) || 1;
                    const colorBtn = document.querySelector('.color-btn.active');
                    const sizeBtn = document.querySelector('.size-btn.active');

                    @if ($product->variants->count() > 0)
                        if (!colorBtn || !sizeBtn) {
                            alert('Vui lòng chọn màu và kích cỡ ');
                            return;
                        }
                        const selectedVariant = updateSelectedVariant();
                        if (!selectedVariant || !selectedVariant.id) {
                            alert('Không tìm thấy biến thể hợp lệ.');
                            return;
                        }
                    @endif

                    const payload = {
                        product_id: {{ $product->id }},
                        quantity: quantity,
                        color: colorBtn ? colorBtn.dataset.color : null,
                        size: sizeBtn ? sizeBtn.dataset.size : null,
                        variant_id: selectedVariantId
                    };

                    const originalHTML = addBtn.innerHTML;
                    addBtn.disabled = true;
                    addBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2"></span>Đang thêm...';

                    fetch("{{ route('cart.add') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(payload)
                        })
                        .then(res => {
                            if (res.status === 401) {
                                window.location.href = '{{ route('client.login') }}';
                                return null;
                            }
                            return res.json();
                        })
                        .then(data => {
                            if (!data) return;
                            if (data.status === 'success') {
                                alert('Đã thêm vào giỏ hàng!');
                                window.location.reload();
                            } else {
                                alert(data.message || 'Không thể thêm vào giỏ hàng.');
                                addBtn.disabled = false;
                                addBtn.innerHTML = originalHTML;
                            }
                        })
                        .catch(() => {
                            alert('Có lỗi xảy ra, vui lòng thử lại.');
                            addBtn.disabled = false;
                            addBtn.innerHTML = originalHTML;
                        });
                });
            }
            @endauth
            });
        </script>
        <style>
            .color-btn.active {
                border: 2px solid #0d6efd !important;
                box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.2);
                color: #fff !important;
            }

            .size-btn.active {
                background: #eef5ff !important;
                border: 2px solid #0d6efd !important;
                color: #0d6efd !important;
                box-shadow: 0 2px 6px rgba(13, 110, 253, 0.15);
            }

            .product-action-btn {
                transition: transform 0.15s ease, box-shadow 0.15s ease;
            }

            .product-action-btn:hover {
                transform: scale(1.05);
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
            }

            input[type=number]::-webkit-inner-spin-button,
            input[type=number]::-webkit-outer-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
        </style>
    </div>

    {{-- Lightbox Modal --}}
    <div class="modal fade" id="imageLightbox" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3"
                    data-bs-dismiss="modal"></button>
                <img id="lightboxImage" src="" alt="" class="w-100">
            </div>
        </div>
    </div>

    {{-- H. MOBILE CTA FOOTER (Fixed) --}}
    <div class="mobile-cta-footer d-lg-none fixed-bottom bg-white border-top shadow-lg p-3" style="display: none;">
        <div class="container-fluid">
            <div class="row g-2">
                <div class="col-6">
                    <button id="mobile-add-cart" class="btn btn-outline-dark w-100">Thêm giỏ</button>
                </div>
                <div class="col-6">
                    <button id="mobile-buy-now" class="btn btn-danger w-100">Mua ngay</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Product Data
            @php
                $variantsData = $product->variants->map(function ($v) use ($product) {
                    return [
                        'id' => $v->id,
                        'color_name' => $v->color_name,
                        'color_code' => $v->color_code ?? '#ccc',
                        'length' => (int) $v->length,
                        'width' => (int) $v->width,
                        'height' => (int) $v->height,
                        'stock' => (int) $v->stock,
                        'price' => $v->price ?? $product->price,
                        'sku' => $v->sku ?? '',
                    ];
                });
            @endphp
            const productData = {
                id: {{ $product->id }},
                name: @json($product->name),
                price: {{ $product->price }},
                salePrice: {{ $product->sale_price ?? 'null' }},
                stock: {{ $product->stock ?? 0 }},
                variants: @json($variantsData)
            };

            let selectedColor = null;
            let selectedSize = null;
            let selectedVariantId = null;
            let currentMaxStock = productData.stock;

            // Gallery Functions
            function changeMainImage(src, element) {
                document.getElementById('mainProductImage').src = src;
                document.querySelectorAll('.thumbnail-item').forEach(el => el.classList.remove('active'));
                if (element) element.classList.add('active');
            }

            function openLightbox(src) {
                document.getElementById('lightboxImage').src = src;
                new bootstrap.Modal(document.getElementById('imageLightbox')).show();
            }

            // Variant Selection
            document.querySelectorAll('.variant-color-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.variant-color-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    selectedColor = this.dataset.color;
                    loadSizesForColor(selectedColor);
                    updateVariant();
                });
            });

            function loadSizesForColor(color) {
                const sizesContainer = document.getElementById('size-selection');
                sizesContainer.innerHTML = '';

                const sizes = [...new Set(productData.variants
                    .filter(v => v.color_name === color)
                    .map(v => `${v.length}x${v.width}x${v.height}`))];

                sizes.forEach(size => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'btn btn-outline-secondary variant-size-btn';
                    btn.textContent = size;
                    btn.dataset.size = size;
                    btn.style.minWidth = '80px';
                    btn.style.borderRadius = '8px';
                    btn.addEventListener('click', function() {
                        document.querySelectorAll('.variant-size-btn').forEach(b => b.classList.remove(
                            'active'));
                        this.classList.add('active');
                        selectedSize = this.dataset.size;
                        updateVariant();
                    });
                    sizesContainer.appendChild(btn);
                });
            }

            function updateVariant() {
                if (!selectedColor || !selectedSize) {
                    selectedVariantId = null;
                    currentMaxStock = productData.stock;
                    document.getElementById('stock-display').textContent = productData.stock || '--';
                    return;
                }

                const [length, width, height] = selectedSize.split('x').map(Number);
                const variant = productData.variants.find(v =>
                    v.color_name === selectedColor &&
                    v.length === length &&
                    v.width === width &&
                    v.height === height
                );

                if (variant) {
                    selectedVariantId = variant.id;
                    currentMaxStock = variant.stock;
                    document.getElementById('stock-display').textContent = variant.stock > 0 ? variant.stock : '0 (Hết hàng)';
                } else {
                    selectedVariantId = null;
                    currentMaxStock = 0;
                    document.getElementById('stock-display').textContent = '--';
                }
            }

            // Quantity Controls
            document.querySelectorAll('.quantity-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const input = document.getElementById('product-quantity');
                    let val = parseInt(input.value) || 1;
                    if (this.dataset.action === 'increase') {
                        if (currentMaxStock > 0 && val >= currentMaxStock) {
                            alert('Đã đạt giới hạn tồn kho');
                            return;
                        }
                        input.value = val + 1;
                    } else {
                        if (val > 1) input.value = val - 1;
                    }
                });
            });

            // Add to Cart
            document.getElementById('add-to-cart-btn')?.addEventListener('click', function() {
                addToCart();
            });

            document.getElementById('mobile-add-cart')?.addEventListener('click', function() {
                addToCart();
            });

            function addToCart() {
                const colorBtn = document.querySelector('.color-btn.active');
                const sizeBtn = document.querySelector('.size-btn.active');
                if (productData.variants.length > 0 && (!colorBtn || !sizeBtn)) {
                    alert('Vui lòng chọn màu và kích cỡ');
                    return;
                }

                const quantity = parseInt(document.getElementById('product-quantity').value) || 1;

                let variantIdForSubmit = null;
                if (colorBtn && sizeBtn) {
                    const [l, w, h] = (sizeBtn.dataset.size || '').split('x').map(Number);
                    const match = productData.variants.find(v =>
                        (v.color_name || '').toLowerCase() === (colorBtn.dataset.color || '').toLowerCase() &&
                        v.length === l && v.width === w && v.height === h
                    );
                    variantIdForSubmit = match ? match.id : null;
                }

                const payload = {
                    product_id: productData.id,
                    quantity: quantity,
                    variant_id: variantIdForSubmit,
                    color: colorBtn ? colorBtn.dataset.color : null,
                    size: sizeBtn ? sizeBtn.dataset.size : null
                };

                fetch("{{ route('cart.add') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            alert('Đã thêm vào giỏ hàng!');
                            // Reload cart count if exists
                            if (typeof updateCartCount === 'function') updateCartCount();
                        } else {
                            alert(data.message || 'Không thể thêm vào giỏ hàng.');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Có lỗi xảy ra');
                    });
            }

            // Buy Now (chỉ gắn 1 handler, tránh duplicated alerts)
            (function() {
                const btn = document.getElementById('buy-now-btn');
                if (btn && !btn.dataset.bound) {
                    btn.dataset.bound = 'true';
                    btn.addEventListener('click', function() {
                        buyNow();
                    });
                }
            })();

            function buyNow() {
                const colorBtn = document.querySelector('.color-btn.active');
                const sizeBtn = document.querySelector('.size-btn.active');
                if (productData.variants.length > 0 && (!colorBtn || !sizeBtn)) {
                    alert('Vui lòng chọn màu và kích cỡ');
                    return;
                }

                const quantity = parseInt(document.getElementById('product-quantity').value) || 1;
                const params = new URLSearchParams({
                    product_id: productData.id,
                    qty: quantity,
                    type: 'buy_now'
                });

                let variantIdForSubmit = null;
                if (colorBtn && sizeBtn) {
                    const [l, w, h] = (sizeBtn.dataset.size || '').split('x').map(Number);
                    const match = productData.variants.find(v =>
                        (v.color_name || '').toLowerCase() === (colorBtn.dataset.color || '').toLowerCase() &&
                        v.length === l && v.width === w && v.height === h
                    );
                    variantIdForSubmit = match ? match.id : null;
                }

                if (variantIdForSubmit) params.append('variant_id', variantIdForSubmit);
                if (colorBtn) params.append('color', colorBtn.dataset.color);
                if (sizeBtn) params.append('size', sizeBtn.dataset.size);

                @auth
                window.location.href = '{{ route('client.checkout.index') }}?' + params.toString();
            @else
                window.location.href = '{{ route('client.login') }}';
            @endauth
            }

            // Recently Viewed Products
            function saveToRecentlyViewed() {
                const viewed = JSON.parse(localStorage.getItem('recentlyViewed') || '[]');
                const product = {
                    id: productData.id,
                    name: productData.name,
                    slug: '{{ $product->slug }}',
                    image: '{{ $product->image ? asset('storage/' . $product->image) : '' }}',
                    price: productData.salePrice || productData.price
                };

                const index = viewed.findIndex(p => p.id === product.id);
                if (index > -1) viewed.splice(index, 1);
                viewed.unshift(product);
                viewed.splice(10); // Keep only last 10

                localStorage.setItem('recentlyViewed', JSON.stringify(viewed));
            }

            function loadRecentlyViewed() {
                const viewed = JSON.parse(localStorage.getItem('recentlyViewed') || '[]')
                    .filter(p => p.id !== productData.id)
                    .slice(0, 4);

                if (viewed.length === 0) return;

                const container = document.getElementById('recently-viewed-list');
                container.innerHTML = viewed.map(product => `
                <div class="col-6 col-md-3">
                    <a href="/products/${product.slug}" class="text-decoration-none">
                        <div class="card h-100">
                            <img src="${product.image}" class="card-img-top" style="aspect-ratio: 1/1; object-fit: cover;">
                            <div class="card-body">
                                <h6 class="card-title">${product.name}</h6>
                                <p class="text-danger fw-bold">${new Intl.NumberFormat('vi-VN').format(product.price)} đ</p>
                            </div>
                        </div>
                    </a>
                </div>
            `).join('');

                document.getElementById('recently-viewed-section').style.display = 'block';
            }

            // Share Product
            function shareProduct() {
                if (navigator.share) {
                    navigator.share({
                        title: productData.name,
                        text: 'Xem sản phẩm này',
                        url: window.location.href
                    });
                } else {
                    navigator.clipboard.writeText(window.location.href);
                    alert('Đã copy link vào clipboard!');
                }
            }

            // Initialize
            document.addEventListener('DOMContentLoaded', function() {
                saveToRecentlyViewed();
                loadRecentlyViewed();

                // Show mobile CTA on scroll
                if (window.innerWidth < 992) {
                    let lastScroll = 0;
                    window.addEventListener('scroll', function() {
                        const currentScroll = window.pageYOffset;
                        const ctaFooter = document.querySelector('.mobile-cta-footer');
                        if (currentScroll > 300 && currentScroll > lastScroll) {
                            ctaFooter.style.display = 'block';
                        } else if (currentScroll < lastScroll) {
                            ctaFooter.style.display = 'none';
                        }
                        lastScroll = currentScroll;
                    });
                }

                // Không tự động chọn biến thể mặc định

                // Handle "Xem đánh giá" link - switch to reviews tab
                const reviewsLink = document.querySelector('a[href="#reviews-section"]');
                if (reviewsLink) {
                    reviewsLink.addEventListener('click', function(e) {
                        e.preventDefault();
                        const reviewsTab = document.getElementById('reviews-tab');
                        if (reviewsTab) {
                            const tab = new bootstrap.Tab(reviewsTab);
                            tab.show();
                            // Scroll to tabs section
                            setTimeout(() => {
                                document.querySelector('.product-details-tabs').scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'start'
                                });
                            }, 100);
                        }
                    });
                }

                // Handle URL hash to open specific tab
                if (window.location.hash === '#reviews-section' || window.location.hash === '#reviews') {
                    const reviewsTab = document.getElementById('reviews-tab');
                    if (reviewsTab) {
                        const tab = new bootstrap.Tab(reviewsTab);
                        tab.show();
                    }
                }
            });

            // Product card hover effect
            document.querySelectorAll('.product-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    const img = this.querySelector('img');
                    if (img) img.style.transform = 'scale(1.1)';
                    this.style.transform = 'translateY(-5px)';
                });
                card.addEventListener('mouseleave', function() {
                    const img = this.querySelector('img');
                    if (img) img.style.transform = 'scale(1)';
                    this.style.transform = 'translateY(0)';
                });
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .thumbnail-item.active {
                border-color: #000 !important;
                border-width: 3px !important;
            }

            .thumbnail-item:hover {
                transform: scale(1.1);
                border-color: #000 !important;
            }

            .product-card {
                transition: transform 0.3s;
            }

            .product-card img {
                transition: transform 0.3s;
            }

            .mobile-cta-footer {
                z-index: 1000;
            }

            /* Product Details Tabs */
            .product-details-tabs {
                margin-top: 3rem;
            }

            .product-details-tabs .nav-tabs {
                border-bottom: 2px solid #e9ecef;
                background: #f8f9fa;
                border-radius: 8px 8px 0 0;
                padding: 0.5rem;
            }

            .product-details-tabs .nav-item {
                margin-right: 0.5rem;
            }

            .product-details-tabs .nav-link {
                border: none;
                border-radius: 8px;
                padding: 0.75rem 1.5rem;
                color: #6c757d;
                font-weight: 500;
                transition: all 0.3s ease;
                background: transparent;
            }

            .product-details-tabs .nav-link:hover {
                color: #667eea;
                background: rgba(102, 126, 234, 0.1);
            }

            .product-details-tabs .nav-link.active {
                color: #667eea;
                background: white;
                border-bottom: 3px solid #667eea;
                font-weight: 600;
            }

            .product-details-tabs .nav-link i {
                font-size: 1.1rem;
            }

            .product-details-tabs .tab-content {
                background: white;
                min-height: 200px;
            }

            .product-details-tabs .description-content {
                line-height: 1.8;
                color: #475569;
            }

            .product-details-tabs .specs-content .table {
                margin-bottom: 0;
            }

            .product-details-tabs .specs-content .table th {
                font-weight: 600;
                color: #334155;
            }

            .product-details-tabs .specs-content .table td {
                color: #64748b;
            }

            .product-details-tabs .color-swatch {
                display: inline-block;
            }

            @media (max-width: 768px) {
                .product-details-tabs .nav-link {
                    padding: 0.5rem 1rem;
                    font-size: 0.9rem;
                }

                .product-details-tabs .nav-link i {
                    font-size: 1rem;
                }
            }
        </style>
    @endpush
@endsection
