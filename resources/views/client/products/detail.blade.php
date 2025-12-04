@extends('client.layouts.app')

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

        {{-- Chi tiết sản phẩm --}}
        <div style="display:flex; justify-content:space-between; flex-wrap:wrap; align-items:flex-start; gap:20px;">
            {{-- Ảnh --}}
            <div style="flex:0 0 45%; max-width:45%;">
                {{-- Ảnh chính --}}
                <div
                    style="
        width:100%;
        aspect-ratio:1/1;
        overflow:hidden;
        border-radius:10px;
        box-shadow:0 6px 18px rgba(0,0,0,0.15);
        position:relative;
    ">
                    <img id="mainImage"
                        src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/600x600?text=No+Image' }}"
                        alt="{{ $product->name }}"
                        style="
                position:absolute;
                inset:0;
                width:100%;
                height:100%;
                object-fit:cover;
             ">
                </div>

                {{-- Ảnh phụ --}}
                @if ($product->images && $product->images->count() > 0)
                    <div
                        style="
            display:flex;
            flex-wrap:wrap;
            gap:10px;
            justify-content:center;
            margin-top:14px;
        ">
                        @foreach ($product->images as $img)
                            <div style="
                    width:80px;
                    height:80px;
                    border:1px solid #ddd;
                    border-radius:8px;
                    overflow:hidden;
                    cursor:pointer;
                    transition:all 0.25s ease-in-out;
                    box-shadow:0 2px 6px rgba(0,0,0,0.08);
                    background:#fff;
                "
                                onclick="document.getElementById('mainImage').src='{{ asset('storage/' . $img->image) }}'"
                                onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 6px 12px rgba(0,0,0,0.15)'"
                                onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.08)'">
                                <img src="{{ asset('storage/' . $img->image) }}" alt="Ảnh phụ"
                                    style="width:100%; height:100%; object-fit:cover;">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Thông tin --}}
            <div
                style="width:50%; border:1px solid #ddd; border-radius:12px; padding:24px; box-shadow:0 4px 10px rgba(0,0,0,0.05); background:#fff;">

                {{-- Tên + Giá --}}
                <h2 style="font-size:28px; font-weight:700; margin-bottom:10px;">{{ $product->name }}</h2>

                {{-- Rating + số lượng đánh giá --}}
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:10px;">
                    <div style="color:#f4b400; font-size:18px;">★★★★★</div>
                    <div style="color:#777;">4.8/5 ({{ rand(30, 120) }} đánh giá)</div>
                </div>

                {{-- Giá --}}
                
                <p style="font-size:24px; font-weight:600; color:#d41; margin-bottom:10px;">
                    {{ number_format($product->price, 0, ',', '.') }} đ
                </p>

                <p style="font-size:14px; color:#555; margin-bottom:10px;">
                    Còn: <span id="stock-display" style="font-weight:bold;">--</span>
                </p>

                {{-- Mã giảm giá giả lập --}}
                <div style="margin-bottom:15px;">
                    <span
                        style="background:#ffe8e8; color:#d41; font-weight:600; padding:6px 10px; border-radius:6px; font-size:14px;">
                        🔖 Giảm 10% cho đơn từ 1.000.000đ
                    </span>
                    <span style="margin-left:10px; color:#666; font-size:13px;">(Flash Sale đang diễn ra)</span>
                </div>

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
                    <button type="button"
                        id="wishlist-toggle"
                        class="product-action-btn"
                        data-product-id="{{ $product->id }}"
                        data-liked="{{ $isInWishlist ? 'true' : 'false' }}"
                        style="border: 2px solid #000; color:#000; background:#fff; padding:10px 20px; border-radius:6px; font-weight:500; cursor:pointer;">
                        <i class="bi {{ $isInWishlist ? 'bi-heart-fill text-danger' : 'bi-heart' }} me-1"></i>
                        <span>{{ $isInWishlist ? 'Đã thích' : 'Yêu thích' }}</span>
                    </button>

                    <button id="buy-now-btn" type="button"
                        class="product-action-btn"
                        style="border: 2px solid #000; color:#000; background:#fff; padding:10px 20px; border-radius:6px; font-weight:500; cursor:pointer;">
                        Mua ngay
                    </button>

                    @auth
                        <button id="add-to-cart" type="button"
                            class="product-action-btn"
                            style="border: 2px solid #000; color:#000; background:#fff; padding: 10px 20px; border-radius: 6px; cursor: pointer;">
                            <i class="bi bi-cart"></i> Thêm vào giỏ
                        </button>
                    @else
                        <a href="{{ route('client.login') }}"
                            class="product-action-btn"
                            style="border: 2px solid #000; color:#000; background:#fff; padding: 10px 20px; border-radius: 6px; text-decoration:none; display:inline-flex; align-items:center; gap:8px;">
                            <i class="bi bi-cart"></i> Thêm vào giỏ
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        {{-- Mô tả sản phẩm --}}
        <div style="margin-top:50px;">
            <h4 style="font-weight:600; margin-bottom:12px;">Mô tả sản phẩm</h4>
            <div style="border:1px solid #eee; border-radius:8px; padding:16px; background:#fff;">
                {!! nl2br(e($product->description ?? 'Chưa có mô tả chi tiết.')) !!}
            </div>
        </div>

        {{-- Sản phẩm cùng danh mục --}}
        <div class="product"
            style="margin-top:60px; padding:30px 20px 50px;border:1px solid #e6e6e6;border-radius:16px; background:#fffaf3; box-shadow:0 4px 16px rgba(0,0,0,0.08); transition:all 0.3s ease;">
            <h2 style="font-size:20px;font-weight:600;margin:30px 0 16px 0;text-align:center;">
                Có thể bạn sẽ thích
            </h2>

            <hr style="margin-left:20px; margin-right:20px; border:0; border-top:1px solid #eee; margin-bottom:24px;">

            @if ($relatedProducts->count() === 0)
                <p style="padding-left:20px;">Hiện chưa có sản phẩm liên quan.</p>
            @else
                <div class="grid-products"
                    style="display:grid; grid-template-columns:repeat(4, 1fr); gap:24px; padding:0 20px;">
                    @foreach ($relatedProducts as $p)
                        <a href="{{ route('client.product.detail', ['slug' => $p->slug]) }}" class="product-card"
                            style="background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.05);
                            text-decoration:none; color:#111; overflow:hidden; transition:all 0.3s ease;
                            padding:12px; display:flex; flex-direction:column; align-items:center;">
                            <div class="product-img" style="width:100%; overflow:hidden; border-radius:6px;">
                                <img src="{{ $p->image ? asset('storage/' . $p->image) : 'https://via.placeholder.com/400x400?text=No+Image' }}"
                                    alt="{{ $p->name }}"
                                    style="width:100%; aspect-ratio:1/1; object-fit:cover; transition:transform 0.35s ease;">
                            </div>
                            <div class="product-name"
                                style="font-size:15px; font-weight:600; color:#111; margin:10px 0 4px; text-align:center;
                                    line-height:1.3; display:-webkit-box; -webkit-line-clamp:2;
                                    -webkit-box-orient:vertical; overflow:hidden;">
                                {{ $p->name }}
                            </div>
                            <div class="product-price" style="color:#d41; font-weight:600; font-size:14px;">
                                {{ number_format($p->price, 0, ',', '.') }} đ
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
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
        @endphp

        {{-- Hiệu ứng hover --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const wishlistBtn = document.getElementById('wishlist-toggle');

                wishlistBtn?.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    const label = this.querySelector('span');

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
                                const icon = wishlistBtn.querySelector('i');
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
                            }
                        })
                        .catch(() => {
                            alert('Có lỗi xảy ra, vui lòng thử lại.');
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
                        window.location.href = '{{ route('client.checkout.index') }}' + '?' + params.toString();
                    @else
                        window.location.href = '{{ route('client.login') }}';
                    @endauth
                });

            @auth
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
                    };

                    const originalHTML = addBtn.innerHTML;
                    addBtn.disabled = true;
                    addBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang thêm...';

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
            @endauth
            });
        </script>
        <style>
            .btn-variant.active {
                border: 1px solid #111 !important;
                background: #111 !important;
                color: #fff !important;
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
@endsection
