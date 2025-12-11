@extends('client.layouts.app')

@section('title', 'Theo dõi vận đơn #' . $order->order_code)

@section('content')
    <div class="py-5">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <a href="{{ route('client.account.orders.index') }}" class="btn btn-link text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
            </a>
            <a href="{{ $order->tracking_url ?? '#' }}" target="_blank" class="btn btn-outline-primary">
                Xem trên trang vận chuyển
            </a>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <h4 class="fw-bold mb-1">#{{ $order->order_code }}</h4>
                        <p class="text-muted mb-0">
                            Đơn vị vận chuyển:
                            <span class="fw-semibold">{{ $order->shipping_provider ?? 'Đang cập nhật' }}</span>
                        </p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-{{ $order->status_badge }} px-3 py-2">
                            <i class="bi {{ $order->status_icon }} me-1"></i>{{ $order->status_label }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Tiến trình vận chuyển</h5>
                <div class="timeline">
                    @php
                        $steps = [
                            'order_date' => 'Đã tiếp nhận đơn',
                            'confirmed_at' => 'Đã xác nhận',
                            'packed_at' => 'Đã đóng gói',
                            'shipped_at' => 'Đang giao',
                            'delivered_at' => 'Đã giao thành công',
                        ];
                    @endphp

                    @foreach ($steps as $key => $label)
                        @php
                            $done = $timeline[$key] ?? null;
                        @endphp
                        <div class="d-flex position-relative pb-4">
                            <div class="flex-shrink-0">
                                <span class="timeline-dot {{ $done ? 'bg-primary' : 'bg-light' }}"></span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="fw-semibold">{{ $label }}</div>
                                <div class="text-muted small">
                                    {{ $done ? $done->format('d/m/Y H:i') : 'Đang chờ cập nhật' }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <style>
                    .timeline {
                        position: relative;
                        padding-left: 20px;
                    }

                    .timeline::before {
                        content: '';
                        position: absolute;
                        left: 6px;
                        top: 0;
                        bottom: 0;
                        width: 2px;
                        background: #e9ecef;
                    }

                    .timeline-dot {
                        width: 14px;
                        height: 14px;
                        border-radius: 50%;
                        display: inline-block;
                        border: 2px solid #fff;
                        box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.05);
                    }
                </style>
            </div>
        </div>

        {{-- Action buttons --}}
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Thao tác</h6>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-outline-secondary" href="{{ route('client.account.orders.show', $order) }}">
                        <i class="bi bi-eye me-1"></i> Xem chi tiết đơn hàng
                    </a>

                    @if ($order->canCancel())
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                            <i class="bi bi-x-circle me-1"></i>
                            @if ($order->payment_method === 'wallet' && $order->payment_status === 'paid')
                                Hủy đơn
                            @else
                                Hủy đơn hàng
                            @endif
                        </button>
                    @endif
                </div>

                @if ($order->canCancel() && $order->payment_method === 'wallet' && $order->payment_status === 'paid')
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Nếu bạn hủy đơn hàng, số tiền <strong class="text-success">{{ number_format($order->final_total, 0, ',', '.') }}đ</strong> sẽ được hoàn lại vào ví của bạn.
                    </div>
                @endif
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
                                        Số tiền <strong>{{ number_format($order->final_total, 0, ',', '.') }}đ</strong> sẽ được hoàn lại vào ví của bạn sau khi hủy đơn.
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label class="form-label">Lý do hủy đơn <span class="text-danger">*</span></label>
                                    <select name="reason" class="form-select" required>
                                        <option value="">-- Chọn lý do --</option>
                                        <option value="Đổi ý, không muốn mua nữa">Đổi ý, không muốn mua nữa</option>
                                        <option value="Muốn thay đổi sản phẩm">Muốn thay đổi sản phẩm</option>
                                        <option value="Muốn thay đổi địa chỉ giao hàng">Muốn thay đổi địa chỉ giao hàng</option>
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
    </div>
@endsection

