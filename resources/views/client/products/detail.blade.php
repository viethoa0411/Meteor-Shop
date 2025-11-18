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
                        <a href="{{ route('client.product.category', ['slug' => $product->category->parent->slug]) }}">
                            {{ $product->category->parent->name }}
                        </a>
                    </li>
                @endif

                @if ($product->category)
                    <li class="breadcrumb-item">
                        <a href="{{ route('client.product.category', ['slug' => $product->category->slug]) }}">
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
            <div style="width:45%;">
                <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/600x600?text=No+Image' }}"
                    alt="{{ $product->name }}"
                    style="width:100%; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); transition:transform 0.3s;"
                    onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
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
                                    data-variant-id="{{ $variant->id }}"
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
                                    data-variant-id="{{ $variant->id }}"
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
                        <input type="number" value="1" min="1" autocomplete="off"
                            style="width:60px; text-align:center; border:none; outline:none; font-size:16px; border-right:1px solid #ccc;">
                        <button type="button" class="plus"
                            style="border:none; background:#fff; color:#000;padding:8px 14px; font-size:18px; cursor:pointer;">+</button>
                    </div>
                </div>


                {{-- Nút hành động --}}
                <div style="display:flex; flex-wrap:wrap; gap:16px;">
                    <button type="button" id="buyNowBtn"
                        style="background:#d41; color:#fff; border:none; padding:10px 20px; border-radius:6px; font-weight:500; cursor:pointer;">
                        Mua ngay
                    </button>
                    <button type="button" id="addToCartBtn"
                        style="background:#111; color:#fff; border:none; padding:10px 20px; border-radius:6px; font-weight:500; cursor:pointer;">
                        <i class="bi bi-cart"></i> Thêm vào giỏ
                    </button>
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

        {{-- Hiệu ứng hover --}}
        <script>
            document.addEventListener('DOMContentLoaded', () => {
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
            });
        </script>


        {{-- Script tăng giảm số lượng + chọn biến thể + Mua ngay --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let selectedVariantId = null;
                const productId = {{ $product->id }};

                const minus = document.querySelector('.minus');
                const plus = document.querySelector('.plus');
                const input = document.querySelector('input[type="number"]');
                if (minus && plus && input) {
                    minus.addEventListener('click', () => {
                        if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
                    });
                    plus.addEventListener('click', () => {
                        input.value = parseInt(input.value) + 1;
                    });
                }

                // Xử lý chọn variant
                document.querySelectorAll('.btn-variant').forEach(btn => {
                    btn.addEventListener('click', () => {
                        // bỏ active trong nhóm tương ứng
                        const isColor = btn.classList.contains('color-btn');
                        const group = isColor ? '.color-btn' : '.size-btn';
                        document.querySelectorAll(group).forEach(b => {
                            b.classList.remove('active');
                            b.style.background = isColor ? (b.dataset.color ? 'transparent' : '#fff') : '#fff';
                            b.style.color = '#111';
                        });

                        // thêm active cho nút được chọn
                        btn.classList.add('active');
                        btn.style.background = '#111';
                        btn.style.color = '#fff';

                        // Lưu variant_id nếu có
                        if (btn.dataset.variantId) {
                            selectedVariantId = btn.dataset.variantId;
                        }
                    });
                });

                // Nút Mua ngay
                document.getElementById('buyNowBtn')?.addEventListener('click', function() {
                    const qty = parseInt(input.value) || 1;
                    
                    // Kiểm tra đăng nhập
                    @auth
                        // Tạo URL checkout
                        let url = '{{ route("client.checkout.index") }}?product_id=' + productId + '&qty=' + qty + '&type=buy_now';
                        if (selectedVariantId) {
                            url += '&variant_id=' + selectedVariantId;
                        }
                        window.location.href = url;
                    @else
                        // Chưa đăng nhập, chuyển đến trang login
                        window.location.href = '{{ route("client.login") }}';
                    @endauth
                });

                // Nút Thêm vào giỏ (có thể implement sau)
                document.getElementById('addToCartBtn')?.addEventListener('click', function() {
                    alert('Tính năng thêm vào giỏ đang được phát triển');
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
