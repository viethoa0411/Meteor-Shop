@extends('client.layouts.app')

@section('title', 'Xác nhận đơn hàng')

@section('content')
    <div class="container py-5">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb" style="background:transparent; padding:0;">
                <li class="breadcrumb-item"><a href="{{ route('client.home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Giỏ hàng</a></li>
                <li class="breadcrumb-item"><a href="{{ route('client.checkout.index', ['type' => 'cart']) }}">Thanh toán</a></li>
                <li class="breadcrumb-item active">Xác nhận đơn hàng</li>
            </ol>
        </nav>

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                {{-- Thông tin khách hàng --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Thông tin khách hàng</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>Họ tên:</strong> {{ $checkoutSession['customer_name'] }}</p>
                        <p class="mb-1"><strong>Số điện thoại:</strong> {{ $checkoutSession['customer_phone'] }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $checkoutSession['customer_email'] }}</p>
                        <p class="mb-0"><strong>Địa chỉ:</strong>
                            {{ $checkoutSession['shipping_address'] }},
                            {{ $checkoutSession['shipping_ward'] }},
                            {{ $checkoutSession['shipping_district'] }},
                            {{ $checkoutSession['shipping_city'] }}
                        </p>
                    </div>
                </div>

                {{-- Thông tin sản phẩm --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Sản phẩm ({{ count($checkoutSession['items']) }})</h5>
                    </div>
                    <div class="card-body">
                        @foreach ($checkoutSession['items'] as $item)
                            <div class="d-flex mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <img src="{{ $item['image'] ? asset('storage/' . $item['image']) : 'https://via.placeholder.com/120' }}"
                                    alt="{{ $item['name'] }}"
                                    style="width: 120px; height: 120px; object-fit: cover; border-radius: 8px;">
                                <div class="ms-3 flex-grow-1">
                                    <h6 class="mb-2">{{ $item['name'] }}</h6>
                                    @if ($item['color'] || $item['size'])
                                        <p class="text-muted small mb-1">
                                            @if ($item['color'])
                                                <strong>Màu:</strong> {{ $item['color'] }}
                                            @endif
                                            @if ($item['size'])
                                                @if ($item['color']) | @endif
                                                <strong>Size:</strong> {{ $item['size'] }}
                                            @endif
                                        </p>
                                    @endif
                                    <p class="text-muted small mb-1">
                                        <strong>Số lượng:</strong> {{ $item['quantity'] }}
                                    </p>
                                    <p class="mb-0">
                                        <strong>Đơn giá:</strong>
                                        <span class="text-danger">{{ number_format($item['price'], 0, ',', '.') }} đ</span>
                                    </p>
                                </div>
                                <div class="text-end">
                                    <strong class="text-danger">
                                        {{ number_format($item['subtotal'], 0, ',', '.') }} đ
                                    </strong>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Phương thức vận chuyển & thanh toán --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Phương thức vận chuyển & thanh toán</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <strong>Vận chuyển:</strong>
                            @if ($checkoutSession['shipping_method'] == 'standard')
                                Giao hàng tiêu chuẩn
                            @elseif ($checkoutSession['shipping_method'] == 'express')
                                Giao hàng nhanh
                            @else
                                Giao hàng hỏa tốc
                            @endif
                        </p>
                        <p class="mb-0">
                            <strong>Thanh toán:</strong>
                            @if ($checkoutSession['payment_method'] == 'cash')
                                Thanh toán khi nhận hàng (COD)
                            @elseif ($checkoutSession['payment_method'] == 'bank')
                                Chuyển khoản ngân hàng
                            @elseif ($checkoutSession['payment_method'] == 'momo')
                                Ví Momo
                            @else
                                {{ $checkoutSession['payment_method'] }}
                            @endif
                        </p>
                    </div>
                </div>

                @if (!empty($checkoutSession['notes']))
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="bi bi-chat-left-text me-2"></i>Ghi chú</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $checkoutSession['notes'] }}</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Tóm tắt đơn hàng --}}
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Tóm tắt đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-2 d-flex justify-content-between">
                            <span>Tạm tính:</span>
                            <strong>{{ number_format($checkoutSession['subtotal'], 0, ',', '.') }} đ</strong>
                        </div>
                        <div class="mb-2 d-flex justify-content-between">
                            <span>Phí vận chuyển:</span>
                            <strong>
                                @if ($checkoutSession['shipping_fee'] == 0)
                                    <span class="text-success">Miễn phí</span>
                                @else
                                    {{ number_format($checkoutSession['shipping_fee'], 0, ',', '.') }} đ
                                @endif
                            </strong>
                        </div>
                        @php $discount = $checkoutSession['discount_amount'] ?? 0; @endphp
                        @if ($discount > 0)
                            <div class="mb-2 d-flex justify-content-between">
                                <span>Giảm giá @if(!empty($checkoutSession['promotion']['code']))(<strong>{{ $checkoutSession['promotion']['code'] }}</strong>)@endif:</span>
                                <strong class="text-success">- {{ number_format($discount, 0, ',', '.') }} đ</strong>
                            </div>
                        @endif
                        <div class="mb-3 pt-2 border-top d-flex justify-content-between">
                            <span class="fs-5 fw-bold">Tổng cộng:</span>
                            <span class="fs-5 fw-bold text-danger">
                                {{ number_format($checkoutSession['final_total'], 0, ',', '.') }} đ
                            </span>
                        </div>

                        <form action="{{ route('client.checkout.createOrder') }}" method="POST" id="confirmForm">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg w-100 mb-2">
                                <i class="bi bi-check-circle me-2"></i>Xác nhận đặt hàng
                            </button>
                        </form>

                        <a href="{{ route('client.checkout.index', ['type' => 'cart']) }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-left me-2"></i>Quay lại chỉnh sửa
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('confirmForm').addEventListener('submit', function(e) {
                const btn = this.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';
            });
        </script>
    @endpush
@endsection
