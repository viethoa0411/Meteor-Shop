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
    <div class="container py-5">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb" style="background:transparent; padding:0; margin-bottom:12px;">
                <li class="breadcrumb-item">
                    <a href="{{ route('client.products.index') }}">Sản phẩm</a>
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
                    {{ number_format($product->price, 0, ',', '.') }} đ
                </p>

                <p style="font-size:14px; color:#555; margin-bottom:10px;">
                    Còn: <span id="stock-display" style="font-weight:bold;">--</span>
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
                @endphp

                {{-- Hiệu ứng hover --}}
                    <
                    script >
                    document.addEventListener('DOMContentLoaded', function() {

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
                            const stockDisplay = document.getElementById('stock-display');
                            const buyNowBtn = document.getElementById('buy-now-btn');
                            const addBtn = document.getElementById('add-to-cart');
                            const productId = {{ $product->id }};
                            const productVariants = @json($variantOptions);
                            const baseProductStock = {{ (int) ($product->stock ?? 0) }};
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
                                if (productVariants.length === 0) {
                                    stockDisplay.textContent = baseProductStock;
                                    currentMaxStock = baseProductStock;
                                    qtyInput.setAttribute('max', baseProductStock);
                                    clampQuantity();
                                    return;
                                }

                                const selectedVariant = updateSelectedVariant();

                                if (selectedVariant && selectedVariant.stock > 0) {
                                    currentMaxStock = selectedVariant.stock;
                                    stockDisplay.textContent = currentMaxStock;
                                    qtyInput.setAttribute('max', currentMaxStock);
                                } else if (selectedVariant) {
                                    currentMaxStock = 0;
                                    stockDisplay.textContent = '0 (Hết hàng)';
                                    qtyInput.setAttribute('max', 0);
                                } else {
                                    stockDisplay.textContent = '-- (Vui lòng chọn phân loại)';
                                    currentMaxStock = 0;
                                    qtyInput.removeAttribute('max');
                                }

                                clampQuantity();
                            };

                            // Init stock display
                            updateStockInfo();

                            minus?.addEventListener('click', () => {
                                let val = parseInt(qtyInput.value, 10) || 1;
                                if (val > 1) {
                                    qtyInput.value = val - 1;
                                }
                            });

                            plus?.addEventListener('click', () => {
                                let val = parseInt(qtyInput.value, 10) || 1;
                                if (productVariants.length > 0 && currentMaxStock === 0) {
                                    alert('Vui lòng chọn Màu và Kích cỡ trước!');
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

                                    document.querySelectorAll(group).forEach(b => {
                                        b.classList.remove('active');
                                        if (isColor && b.dataset.color) {
                                            b.style.background = b.dataset.color;
                                            b.style.color = '#fff';
                                        } else {
                                            b.style.background = '#fff';
                                            b.style.color = '#111';
                                        }
                                    });

                                    btn.classList.add('active');
                                    btn.style.background = '#111';
                                    btn.style.color = '#fff';

                                    updateStockInfo();
                                });
                            });

                            buyNowBtn?.addEventListener('click', function(event) {
                                    event.preventDefault();
                                    const quantity = parseInt(qtyInput.value, 10) || 1;
                                    const colorBtn = document.querySelector('.color-btn.active');
                                    const sizeBtn = document.querySelector('.size-btn.active');

                                    @if ($product->variants->count() > 0)
                                        if (!colorBtn || !sizeBtn) {
                                            alert('Vui lòng chọn màu và kích cỡ');
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

                                    @auth
                                    window.location.href = '{{ route('client.checkout.index') }}' + '?' + params
                                        .toString();
                                @else
                                    window.location.href = '{{ route('client.login') }}';
                                @endauth
                            });

                        addBtn?.addEventListener('click', (event) => {
                            event.preventDefault();
                            const quantity = parseInt(qtyInput.value, 10) || 1;
                            const colorBtn = document.querySelector('.color-btn.active');
                            const sizeBtn = document.querySelector('.size-btn.active');

                            @if ($product->variants->count() > 0)
                                if (!colorBtn || !sizeBtn) {
                                    alert('Vui lòng chọn màu và kích cỡ');
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
                                window.location.reload();
                            }
                            else {
                                alert(data.message || 'Không thể thêm vào giỏ hàng.');
                            }
                        });
                    });
            });
        </script>
        <style>
            .btn-variant.active {
                border: 1px solid #111 !important;
                background: #111 !important;
                color: #fff !important;
            }

            input[type=number]::-webkit-inner-spin-button,
            input[type=number]::-webkit-outer-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
        </style>
    </div>

@endsection
