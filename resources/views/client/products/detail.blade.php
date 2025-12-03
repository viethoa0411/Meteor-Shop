@extends('client.layouts.app')

@push('head')
    {{-- SEO Meta Tags --}}
    <title>{{ $product->name }} - {{ config('app.name') }}</title>
    <meta name="description" content="{{ $product->short_description ?? Str::limit(strip_tags($product->description), 160) }}">
    <meta name="keywords" content="{{ $product->name }}, {{ $product->category->name ?? '' }}">
    
    {{-- Open Graph --}}
    <meta property="og:title" content="{{ $product->name }}">
    <meta property="og:description" content="{{ $product->short_description ?? Str::limit(strip_tags($product->description), 160) }}">
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

        {{-- Product Name, SKU, Status --}}
        <div class="mb-3">
            <h1 class="h2 mb-2" style="font-weight:700;">{{ $product->name }}</h1>
            @if($product->sku)
                <p class="text-muted small mb-1">SKU: <strong>{{ $product->sku }}</strong></p>
            @endif
            <p class="mb-0">
                <span class="badge {{ $product->in_stock ? 'bg-success' : 'bg-danger' }}">
                    {{ $product->in_stock ? '✓ Còn hàng' : '✗ Hết hàng' }}
                </span>
                @if($product->total_sold > 0)
                    <span class="badge bg-info ms-2">Đã bán: {{ $product->total_sold }}</span>
                @endif
            </p>
        </div>

        {{-- Main Product Layout --}}
        <div class="row g-4 mb-5">
            {{-- B. PRODUCT GALLERY (Left) --}}
            <div class="col-lg-6">
                <div class="product-gallery">
                    {{-- Main Image with Zoom --}}
                    <div class="main-image-wrapper position-relative mb-3" style="aspect-ratio: 1/1; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1); background: #f8f9fa;">
                        <img id="mainProductImage" 
                             src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/600x600?text=No+Image' }}" 
                             alt="{{ $product->name }}"
                             class="w-100 h-100" 
                             style="object-fit: cover; cursor: zoom-in;"
                             onclick="openLightbox(this.src)">
                    </div>

                    {{-- Thumbnails --}}
                    @if ($product->images && $product->images->count() > 0)
                        <div class="thumbnails d-flex gap-2 flex-wrap justify-content-center">
                            <div class="thumbnail-item {{ !$product->image ? 'active' : '' }}" 
                                 style="width: 80px; height: 80px; border: 2px solid #ddd; border-radius: 8px; overflow: hidden; cursor: pointer; transition: all 0.3s;"
                                 onclick="changeMainImage('{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/600x600?text=No+Image' }}', this)">
                                <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/80x80?text=No+Image' }}" 
                                     alt="Main"
                                     class="w-100 h-100" 
                                     style="object-fit: cover;">
                            </div>
                            @foreach ($product->images as $img)
                                <div class="thumbnail-item" 
                                     style="width: 80px; height: 80px; border: 2px solid #ddd; border-radius: 8px; overflow: hidden; cursor: pointer; transition: all 0.3s;"
                                     onclick="changeMainImage('{{ asset('storage/' . $img->image) }}', this)">
                                    <img src="{{ asset('storage/' . $img->image) }}" 
                                         alt="Gallery"
                                         class="w-100 h-100" 
                                         style="object-fit: cover;">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- C. PRODUCT INFO (Right) --}}
            <div class="col-lg-6">
                <div class="product-info">
                    {{-- 1. Product Name with Tags --}}
                    <div class="mb-3">
                        @if($product->total_sold > 100)
                            <span class="badge bg-danger mb-2">🔥 Best Seller</span>
                        @endif
                        @if($product->created_at->gt(now()->subDays(30)))
                            <span class="badge bg-primary mb-2">✨ Mới</span>
                        @endif
                        @if($product->discount_percent > 0)
                            <span class="badge bg-warning text-dark mb-2">-{{ $product->discount_percent }}%</span>
                        @endif
                    </div>

                    {{-- 2. Price --}}
                    <div class="mb-3">
                        @if($product->sale_price && $product->sale_price < $product->price)
                            <div class="d-flex align-items-baseline gap-2">
                                <span class="h3 text-danger fw-bold mb-0">{{ number_format($product->sale_price, 0, ',', '.') }} đ</span>
                                <span class="text-muted text-decoration-line-through">{{ number_format($product->price, 0, ',', '.') }} đ</span>
                                <span class="badge bg-danger">Tiết kiệm {{ number_format($product->price - $product->sale_price, 0, ',', '.') }} đ</span>
                            </div>
                        @else
                            <span class="h3 text-danger fw-bold">{{ number_format($product->price, 0, ',', '.') }} đ</span>
                        @endif
                    </div>

                    {{-- 3. Rating --}}
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <div class="rating-stars" style="color: #f4b400; font-size: 20px;">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($ratingAvg))
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

                    {{-- 4. Short Description --}}
                    @if($product->short_description)
                        <div class="mb-4">
                            <ul class="list-unstyled">
                                @foreach(explode("\n", $product->short_description) as $line)
                                    @if(trim($line))
                                        <li class="mb-1">✓ {{ trim($line) }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- 5. Variants Selection --}}
                    @if ($product->variants && $product->variants->count() > 0)
                        {{-- 5.1 Color Selection --}}
                        <div class="mb-3">
                            <label class="fw-bold mb-2 d-block">Màu sắc:</label>
                            <div class="d-flex gap-2 flex-wrap" id="color-selection">
                                @foreach ($product->variants->unique('color_name') as $variant)
                                    <button type="button" 
                                            class="btn btn-outline-secondary variant-color-btn {{ $loop->first ? 'active' : '' }}"
                                            data-color="{{ $variant->color_name }}"
                                            style="min-width: 80px; border-radius: 8px;">
                                        <span style="display: inline-block; width: 20px; height: 20px; background: {{ $variant->color_code ?? '#ccc' }}; border-radius: 50%; margin-right: 6px; vertical-align: middle;"></span>
                                        {{ $variant->color_name }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- 5.2 Size Selection --}}
                        <div class="mb-3">
                            <label class="fw-bold mb-2 d-block">Kích thước:</label>
                            <div class="d-flex gap-2 flex-wrap" id="size-selection">
                                {{-- Sizes will be loaded dynamically based on selected color --}}
                            </div>
                        </div>
                    @endif

                    {{-- 6. Quantity --}}
                    <div class="mb-3">
                        <label class="fw-bold mb-2 d-block">Số lượng:</label>
                        <div class="d-flex align-items-center" style="max-width: 150px;">
                            <button type="button" class="btn btn-outline-secondary quantity-btn" data-action="decrease" style="border-radius: 8px 0 0 8px;">−</button>
                            <input type="number" 
                                   id="product-quantity" 
                                   value="1" 
                                   min="1" 
                                   class="form-control text-center" 
                                   style="border-radius: 0; border-left: none; border-right: none;">
                            <button type="button" class="btn btn-outline-secondary quantity-btn" data-action="increase" style="border-radius: 0 8px 8px 0;">+</button>
                        </div>
                        <small class="text-muted d-block mt-1">
                            Còn: <span id="stock-display" class="fw-bold">--</span>
                        </small>
                    </div>

                    {{-- 7. Shipping Info --}}
                    <div class="mb-4 p-3 bg-light rounded">
                        <small class="text-muted d-block mb-1">🚚 Vận chuyển:</small>
                        <small>Dự kiến giao hàng trong 2-4 ngày (GHN/GHTK)</small>
                    </div>

                    {{-- 8. CTA Buttons --}}
                    <div class="d-flex gap-2 flex-wrap mb-4">
                        <button id="add-to-cart-btn" 
                                class="btn btn-dark btn-lg flex-fill" 
                                style="min-width: 200px;">
                            <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                        </button>
                        <button id="buy-now-btn" 
                                class="btn btn-danger btn-lg flex-fill" 
                                style="min-width: 200px;">
                            Mua ngay
                        </button>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary" id="wishlist-btn">
                            <i class="bi bi-heart"></i> Yêu thích
                        </button>
                        <button class="btn btn-outline-secondary" onclick="shareProduct()">
                            <i class="bi bi-share"></i> Chia sẻ
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- D. PRODUCT DETAILS TABS --}}
        <div class="product-details-tabs mb-5">
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab" aria-controls="description" aria-selected="true">
                        <i class="bi bi-file-text me-2"></i>Mô tả
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs" type="button" role="tab" aria-controls="specs" aria-selected="false">
                        <i class="bi bi-gear me-2"></i>Thông số kỹ thuật
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">
                        <i class="bi bi-star me-2"></i>Đánh giá ({{ $totalReviews }})
                    </button>
                </li>
            </ul>
            <div class="tab-content border border-top-0 rounded-bottom p-4" id="productTabsContent">
                {{-- Description Tab --}}
                <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                    <div class="product-description">
                        @if($product->description)
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
                            @if($product->brand)
                            <tr>
                                    <th class="bg-light">Thương hiệu</th>
                                <td>{{ $product->brand->name }}</td>
                            </tr>
                            @endif
                            @if($product->length || $product->width || $product->height)
                            <tr>
                                    <th class="bg-light">Kích thước</th>
                                <td>{{ $product->length ?? '?' }} x {{ $product->width ?? '?' }} x {{ $product->height ?? '?' }} cm</td>
                            </tr>
                            @endif
                                @if($product->color_code)
                                <tr>
                                    <th class="bg-light">Mã màu</th>
                                    <td>
                                        <span class="d-inline-flex align-items-center">
                                            <span class="color-swatch me-2" style="width: 20px; height: 20px; background-color: {{ $product->color_code }}; border: 1px solid #ddd; border-radius: 3px;"></span>
                                            {{ $product->color_code }}
                                        </span>
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <th class="bg-light">Trạng thái</th>
                                    <td>
                                        @if($product->in_stock)
                                            <span class="badge bg-success">Còn hàng</span>
                                        @else
                                            <span class="badge bg-danger">Hết hàng</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($product->stock !== null)
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
        @if($relatedProducts->count() > 0)
            <div class="related-products mb-5">
                <h3 class="h4 mb-4">Sản phẩm liên quan</h3>
                <div class="row g-4">
                    @foreach($relatedProducts as $related)
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="{{ route('client.product.detail', ['slug' => $related->slug]) }}" class="text-decoration-none">
                                <div class="card h-100 product-card">
                                    <div class="position-relative" style="aspect-ratio: 1/1; overflow: hidden;">
                                        <img src="{{ $related->image ? asset('storage/' . $related->image) : 'https://via.placeholder.com/400x400?text=No+Image' }}" 
                                             alt="{{ $related->name }}"
                                             class="card-img-top w-100 h-100" 
                                             style="object-fit: cover; transition: transform 0.3s;">
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title text-dark mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
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
    </div>

    {{-- Lightbox Modal --}}
    <div class="modal fade" id="imageLightbox" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
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
            $variantsData = $product->variants->map(function($v) use ($product) {
                return [
                    'id' => $v->id,
                    'color_name' => $v->color_name,
                    'color_code' => $v->color_code ?? '#ccc',
                    'length' => (int)$v->length,
                    'width' => (int)$v->width,
                    'height' => (int)$v->height,
                    'stock' => (int)$v->stock,
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
            if(element) element.classList.add('active');
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
                    document.querySelectorAll('.variant-size-btn').forEach(b => b.classList.remove('active'));
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
            if (productData.variants.length > 0 && (!selectedColor || !selectedSize)) {
                alert('Vui lòng chọn màu và kích cỡ');
                return;
            }

            const quantity = parseInt(document.getElementById('product-quantity').value) || 1;
            const payload = {
                product_id: productData.id,
                quantity: quantity,
                variant_id: selectedVariantId,
                color: selectedColor,
                size: selectedSize
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

        // Buy Now
        document.getElementById('buy-now-btn')?.addEventListener('click', function() {
            buyNow();
        });

        document.getElementById('mobile-buy-now')?.addEventListener('click', function() {
            buyNow();
        });

        function buyNow() {
            if (productData.variants.length > 0 && (!selectedColor || !selectedSize)) {
                alert('Vui lòng chọn màu và kích cỡ');
                return;
            }

            const quantity = parseInt(document.getElementById('product-quantity').value) || 1;
            const params = new URLSearchParams({
                product_id: productData.id,
                qty: quantity,
                type: 'buy_now'
            });

            if (selectedVariantId) params.append('variant_id', selectedVariantId);
            if (selectedColor) params.append('color', selectedColor);
            if (selectedSize) params.append('size', selectedSize);

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

            // Initialize first color if exists
            const firstColorBtn = document.querySelector('.variant-color-btn');
            if (firstColorBtn) {
                firstColorBtn.click();
            }

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
