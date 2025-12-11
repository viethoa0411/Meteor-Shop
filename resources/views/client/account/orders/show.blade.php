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
                                    <a href="{{ route('client.product.detail', $productSlug) }}"
                                        class="product-item-link text-decoration-none">
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
                                        <div class="fw-semibold">
                                            {{ $item->product_name ?? optional($item->product)->name }}</div>
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
                                <span class="text-success">- {{ number_format($order->discount_amount, 0, ',', '.') }}
                                    đ</span>
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
                {{-- Action buttons --}}
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Thao tác</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @if ($order->canReceive())
                                <form action="{{ route('client.account.orders.markReceived', $order) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success"
                                        onclick="return confirm('Bạn đã nhận được hàng? Xác nhận này sẽ cập nhật trạng thái đơn hàng sang "Hoàn
                                        thành".');">
                                        <i class="bi bi-check-circle me-1"></i> Đã nhận hàng
                                    </button>
                                </form>
                            @endif

                            @if ($order->canTrack())
                                <a class="btn btn-outline-primary"
                                    href="{{ route('client.account.orders.tracking', $order) }}">
                                    <i class="bi bi-truck me-1"></i> Theo dõi vận đơn
                                </a>
                            @endif
                                    @if ($order->canReturnRefund())
                                        @php
                                            $daysRemaining = $order->getReturnDaysRemaining();
                                        @endphp
                                        <div class="d-flex flex-column">
                                            <a class="btn btn-outline-warning"
                                                href="{{ route('client.account.orders.refund.return', $order) }}">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i> Trả hàng hoàn tiền
                                            </a>
                                            @if ($daysRemaining !== null && $daysRemaining > 0)
                                                <small class="text-muted text-center mt-1">(Còn {{ $daysRemaining }}
                                                    ngày)</small>
                                            @endif
                                        </div>
                                    @elseif ($order->order_status === 'completed' && $order->isReturnExpired())
                                        <div class="alert alert-warning small mb-0 py-2">
                                            <i class="bi bi-exclamation-triangle me-1"></i>
                                            Đã quá thời hạn 7 ngày để yêu cầu trả hàng hoàn tiền
                                        </div>
                                    @endif

                                    @if ($order->order_status === 'delivered' && $order->delivered_at)
                                        @php
                                            $deliveredAt = \Carbon\Carbon::parse($order->delivered_at);
                                            $autoCompleteAt = $deliveredAt->copy()->addDays(2);
                                            $remainingHours = max(0, now()->diffInHours($autoCompleteAt));
                                            $remainingDays = intdiv($remainingHours, 24);
                                            $remainingHoursMod = $remainingHours % 24;
                                        @endphp
                                        <div class="alert alert-info mt-2 mb-0 w-100">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Nếu bạn không xác nhận, hệ thống sẽ tự chuyển trạng thái sang <strong>Hoàn
                                                thành</strong>
                                            sau 2 ngày kể từ thời điểm giao hàng ({{ $deliveredAt->format('d/m/Y H:i') }}).
                                            @if ($autoCompleteAt->isFuture())
                                                <br><small>Còn {{ $remainingDays }} ngày {{ $remainingHoursMod }}
                                                    giờ.</small>
                                            @endif
                                        </div>
                                    @endif

                                    @if ($order->canCancelRefund())
                                        <a class="btn btn-outline-danger"
                                            href="{{ route('client.account.orders.refund.cancel', $order) }}">
                                            <i class="bi bi-x-circle me-1"></i> Hủy đơn và hoàn tiền
                                        </a>
                                    @endif

                                    @php
                                        $pendingCancelRefund = $order
                                            ->refunds()
                                            ->where('type', 'cancel')
                                            ->where('status', 'pending')
                                            ->first();

                                    @endphp

                                    @if ($pendingCancelRefund)
                                        <form action="{{ route('client.account.orders.refund.cancel.reset', $order) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-dark"
                                                onclick="return confirm('Bạn muốn đặt lại đơn hàng và dừng hoàn tiền?');">
                                                <i class="bi bi-arrow-repeat me-1"></i> Đặt lại
                                            </button>
                                        </form>
                                    @endif

                                    @if ($order->canCancel())
                                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                                data-bs-target="#cancelOrderModal">
                                                <i class="bi bi-x-circle me-1"></i>
                                                @if ($order->payment_method === 'wallet' && $order->payment_status === 'paid')
                                                    Hủy đơn và hoàn tiền
                                                @else
                                                    Hủy đơn hàng
                                                @endif
                                            </button>
                                        @endif
                        </div>

                        @if ($order->canCancel() && $order->payment_method === 'wallet' && $order->payment_status === 'paid')
                            <div class="alert alert-info mt-3 mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                Nếu bạn hủy đơn hàng, số tiền <strong
                                    class="text-success">{{ number_format($order->final_total, 0, ',', '.') }}đ</strong>
                                sẽ
                                được hoàn lại vào ví của bạn.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal hủy đơn --}}
    @if ($order->canCancel())
        <div class="modal fade" id="cancelOrderModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                            Xác nhận hủy đơn hàng
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('client.account.orders.cancel', $order) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            @if ($order->payment_method === 'wallet' && $order->payment_status === 'paid')
                                <div class="alert alert-success">
                                    <i class="bi bi-wallet2 me-1"></i>
                                    Số tiền <strong>{{ number_format($order->final_total, 0, ',', '.') }}đ</strong> sẽ được
                                    hoàn lại vào ví của bạn sau khi hủy đơn.

                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label">Lý do hủy đơn <span class="text-danger">*</span></label>
                                <select name="reason" class="form-select" required>
                                    <option value="">-- Chọn lý do --</option>
                                    <option value="Đổi ý, không muốn mua nữa">Đổi ý, không muốn mua nữa</option>
                                    <option value="Muốn thay đổi sản phẩm">Muốn thay đổi sản phẩm</option>
                                    <option value="Muốn thay đổi địa chỉ giao hàng">Muốn thay đổi địa chỉ giao hàng
                                    </option>

                                    <option value="Tìm được giá tốt hơn">Tìm được giá tốt hơn</option>
                                    <option value="Đặt nhầm">Đặt nhầm</option>
                                    <option value="Lý do khác">Lý do khác</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Ghi chú thêm</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Nhập ghi chú nếu có..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-x-circle me-1"></i> Xác nhận hủy đơn
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
