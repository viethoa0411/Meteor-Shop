@extends('client.layouts.app')

@section('title', 'Chi tiết đơn hàng #' . $order->order_code)

@push('head')
    <style>
        .product-item-link {
            transition: all 0.2s ease;
        }

        .product-item-link:hover {
            color: #007bff !important;
        }

        .product-item-link img {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .product-item-link:hover img {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .product-item-link.fw-semibold:hover {
            text-decoration: underline !important;
        }
    </style>
@endpush

@section('content')
    <div class="py-5">
        <div class="mb-4">
            <a href="{{ route('client.account.orders.index') }}" class="btn btn-link text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
            </a>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div>
                        <h4 class="fw-bold mb-1">Đơn hàng #{{ $order->order_code }}</h4>
                        <p class="text-muted mb-0">Ngày đặt: {{ optional($order->display_order_date)->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-{{ $order->status_badge }} px-3 py-2">
                            <i class="bi {{ $order->status_icon }} me-1"></i>{{ $order->status_label }}
                        </span>
                        <div class="fw-bold fs-4 text-primary mt-2">
                            {{ number_format($order->final_total, 0, ',', '.') }} đ
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0 fw-bold">Sản phẩm</h5>
                    </div>
                    <div class="card-body">
                        @foreach ($order->items as $item)
                            <div class="d-flex align-items-center gap-3 border-bottom py-3">
                                @php
                                    $product = $item->product;
                                    $productSlug = $product->slug ?? null;
                                @endphp
                                @if ($productSlug)
                                    <a href="{{ route('client.product.detail', $productSlug) }}" class="product-item-link text-decoration-none">
                                        <img src="{{ $item->image_path ? asset('storage/' . $item->image_path) : 'https://via.placeholder.com/80x80?text=No+Image' }}"
                                            alt="{{ $item->product_name ?? optional($item->product)->name }}" width="80"
                                            height="80" class="rounded" style="cursor: pointer;">
                                    </a>
                                @else
                                    <img src="{{ $item->image_path ? asset('storage/' . $item->image_path) : 'https://via.placeholder.com/80x80?text=No+Image' }}"
                                        alt="{{ $item->product_name ?? optional($item->product)->name }}" width="80"
                                        height="80" class="rounded">
                                @endif
                                <div class="flex-grow-1">
                                    @if ($productSlug)
                                        <a href="{{ route('client.product.detail', $productSlug) }}" 
                                           class="product-item-link text-decoration-none text-dark fw-semibold d-inline-block">
                                            {{ $item->product_name ?? optional($item->product)->name }}
                                        </a>
                                    @else
                                        <div class="fw-semibold">{{ $item->product_name ?? optional($item->product)->name }}</div>
                                    @endif
                                    <div class="text-muted small">
                                        @if ($item->variant_name)
                                            Biến thể: {{ $item->variant_name }} |
                                        @endif
                                        SL: {{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }} đ
                                    </div>
                                </div>
                                <div class="fw-semibold">{{ number_format($item->subtotal, 0, ',', '.') }} đ</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0 fw-bold">Ghi chú</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $order->notes ?? 'Không có ghi chú bổ sung.' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0 fw-bold">Thông tin giao hàng</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-1 fw-semibold">{{ $order->customer_name ?? 'N/A' }}</p>
                        <p class="text-muted mb-1">{{ $order->shipping_phone ?? 'N/A' }}</p>
                        <p class="text-muted mb-0">{{ $order->shipping_address ?? 'N/A' }}</p>
                        @if ($order->shipping_city || $order->shipping_district || $order->shipping_ward)
                            <p class="text-muted small mb-0 mt-1">
                                {{ trim(($order->shipping_ward ?? '') . ', ' . ($order->shipping_district ?? '') . ', ' . ($order->shipping_city ?? ''), ', ') }}
                            </p>
                        @endif
                    </div>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0 fw-bold">Thanh toán</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính</span>
                            <span>{{ number_format($order->total_price ?? 0, 0, ',', '.') }} đ</span>
                        </div>
                        @if ($order->discount_amount > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span>Giảm giá</span>
                                <span class="text-success">- {{ number_format($order->discount_amount, 0, ',', '.') }} đ</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí vận chuyển</span>
                            <span>{{ number_format($order->shipping_fee ?? 0, 0, ',', '.') }} đ</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Tổng cộng</span>
                            <span>{{ number_format($order->final_total, 0, ',', '.') }} đ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

