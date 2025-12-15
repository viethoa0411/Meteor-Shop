@extends('client.layouts.app')

@section('title')
    @if (isset($order) && $order->payment_method == 'momo' && $order->payment_status != 'paid')
        Đơn hàng chưa hoàn thành
    @else
        Đặt hàng thành công
    @endif
@endsection

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                {{-- Thông báo --}}
                <div class="text-center mb-4">
                    @if (isset($order) && $order->payment_method == 'momo' && $order->payment_status != 'paid')
                        <div class="mb-3" style="font-size: 80px; color: #ffc107;">
                            <i class="bi bi-exclamation-circle-fill"></i>
                        </div>
                        <h2 class="mb-2">Đơn hàng chưa hoàn thành!</h2>
                        <p class="text-muted mb-3">Bạn đã đặt hàng nhưng quá trình thanh toán Momo chưa hoàn tất.</p>
                        
                        <form action="{{ route('client.checkout.momo_payment.process', $order->order_code) }}" method="POST" class="d-inline-block">
                            @csrf
                            <button type="submit" class="btn btn-primary px-4 py-2" style="background-color: #a50064; border-color: #a50064;">
                                <i class="bi bi-qr-code me-2"></i> Tiếp tục thanh toán Momo
                            </button>
                        </form>
                    @else
                        <div class="mb-3" style="font-size: 80px; color: #28a745;">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <h2 class="mb-2">Đặt hàng thành công!</h2>
                        <p class="text-muted">Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ xử lý đơn hàng của bạn sớm nhất có thể.</p>
                    @endif
                </div>

                {{-- MOMO QR CODE SECTION (CHỈ HIỆN KHI THANH TOÁN MOMO) - Giữ lại alert nhưng sửa nội dung --}}
                @if (isset($order) && $order->payment_method == 'momo' && $order->payment_status != 'paid')
                    <div class="alert alert-warning mb-4 text-center">
                        <p class="mb-0">Nếu bạn đã thanh toán và bị trừ tiền, vui lòng liên hệ bộ phận CSKH để được hỗ trợ.</p>
                    </div>
                @endif

                {{-- Mã đơn hàng --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-body text-center">
                        <p class="mb-2 text-muted">Mã đơn hàng của bạn:</p>
                        <h3 class="mb-0 text-primary">{{ $order->order_code }}</h3>
                    </div>
                </div>

                {{-- Tóm tắt đơn hàng --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Tóm tắt đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        {{-- Sản phẩm --}}
                        @foreach ($order->items as $item)
                            <div class="d-flex mb-3 pb-3 border-bottom">
                                <img src="{{ $item->image_path ? asset('storage/' . $item->image_path) : 'https://via.placeholder.com/100' }}"
                                    alt="{{ $item->product_name }}" 
                                    style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                                <div class="ms-3 flex-grow-1">
                                    <h6 class="mb-1">{{ $item->product_name }}</h6>
                                    @if ($item->variant_name)
                                        <p class="text-muted small mb-1">{{ $item->variant_name }}</p>
                                    @endif
                                    <p class="text-muted small mb-1">Số lượng: {{ $item->quantity }}</p>
                                    <p class="mb-0">
                                        <strong>{{ number_format($item->subtotal, 0, ',', '.') }} đ</strong>
                                    </p>
                                </div>
                            </div>
                        @endforeach

                        {{-- Tổng tiền --}}
                        <div class="mb-2 d-flex justify-content-between">
                            <span>Tạm tính:</span>
                            <strong>{{ number_format($order->sub_total ?? $order->total_price, 0, ',', '.') }} đ</strong>
                        </div>
                        <div class="mb-2 d-flex justify-content-between">
                            <span>Phí vận chuyển:</span>
                            <strong>
                                @if (($order->shipping_fee ?? 0) == 0)
                                    <span class="text-success">Miễn phí</span>
                                @else
                                    {{ number_format($order->shipping_fee, 0, ',', '.') }} đ
                                @endif
                            </strong>
                        </div>
                        @if (($order->discount_amount ?? 0) > 0)
                            <div class="mb-2 d-flex justify-content-between text-danger">
                                <span>Giảm giá:</span>
                                <strong>-{{ number_format($order->discount_amount, 0, ',', '.') }} đ</strong>
                            </div>
                        @endif
                        <div class="mb-0 pt-2 border-top d-flex justify-content-between">
                            <span class="fs-5 fw-bold">Tổng cộng:</span>
                            <span class="fs-5 fw-bold text-danger">
                                {{ number_format($order->final_total, 0, ',', '.') }} đ
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Thông tin giao hàng --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Thông tin giao hàng</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>Người nhận:</strong> {{ $order->customer_name }}</p>
                        <p class="mb-1"><strong>Số điện thoại:</strong> {{ $order->customer_phone }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $order->customer_email }}</p>
                        <p class="mb-0"><strong>Địa chỉ:</strong> 
                            {{ $order->shipping_address }}, 
                            {{ $order->shipping_ward }}, 
                            {{ $order->shipping_district }}, 
                            {{ $order->shipping_city }}
                        </p>
                    </div>
                </div>

                {{-- Nút hành động --}}
                <div class="d-flex gap-2 justify-content-center mb-4">
                    <a href="{{ route('client.account.orders.show', $order) }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-eye me-2"></i>Theo dõi đơn hàng
                    </a>
                    <a href="{{ route('client.home') }}" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-house me-2"></i>Tiếp tục mua sắm
                    </a>
                </div>

                {{-- Sản phẩm liên quan --}}
                @if ($relatedProducts->count() > 0)
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="bi bi-grid me-2"></i>Có thể bạn sẽ thích</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach ($relatedProducts as $p)
                                    <div class="col-md-3">
                                        <a href="{{ route('client.product.detail', ['slug' => $p->slug]) }}" 
                                           class="text-decoration-none">
                                            <div class="card h-100">
                                                <img src="{{ $p->image ? asset('storage/' . $p->image) : 'https://via.placeholder.com/200' }}"
                                                    class="card-img-top" alt="{{ $p->name }}"
                                                    style="height: 200px; object-fit: cover;">
                                                <div class="card-body">
                                                    <h6 class="card-title small mb-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                        {{ $p->name }}
                                                    </h6>
                                                    <p class="card-text text-danger fw-bold mb-0">
                                                        {{ number_format($p->price, 0, ',', '.') }} đ
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

