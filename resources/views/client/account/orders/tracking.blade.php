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

        {{-- Thông báo hoàn tiền thành công --}}
        @php
            $completedRefund = $order->refunds->where('status', 'completed')->first();
        @endphp
        @if($completedRefund)
            <div class="card border-0 shadow-sm mt-4 border-success">
                <div class="card-body">
                    <div class="alert alert-success mb-0">
                        <h6 class="alert-heading fw-bold">
                            <i class="bi bi-check-circle-fill me-2"></i>Đã hoàn tiền thành công
                        </h6>
                        <p class="mb-0">
                            Quý khách có gì thắc mắc xin liên hệ đội ngũ Meteor Shop.
                        </p>
                        @if($completedRefund->refund_amount)
                            <hr>
                            <p class="mb-0">
                                <strong>Số tiền đã hoàn:</strong> 
                                <span class="text-success">{{ number_format($completedRefund->refund_amount, 0, ',', '.') }} đ</span>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Action buttons --}}
        @if ($order->order_status === 'completed' || $order->canCancelRefund())
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Thao tác</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @if ($order->order_status === 'completed')
                            <a class="btn btn-outline-warning" href="{{ route('client.account.orders.refund.return', $order) }}">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Trả hàng hoàn tiền
                            </a>
                        @endif

                        @if ($order->canCancelRefund())
                            <a class="btn btn-outline-danger" href="{{ route('client.account.orders.refund.cancel', $order) }}">
                                <i class="bi bi-x-circle me-1"></i> Hủy đơn và hoàn tiền
                            </a>
                        @endif

                        @php
                            $pendingCancelRefund = $order->refunds
                                ->where('refund_type', 'cancel')
                                ->where('status', 'pending')
                                ->first();
                        @endphp

                        @if ($pendingCancelRefund)
                            <form action="{{ route('client.account.orders.refund.cancel.reset', $order) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-dark"
                                        onclick="return confirm('Bạn muốn đặt lại đơn hàng và dừng hoàn tiền?');">
                                    <i class="bi bi-arrow-repeat me-1"></i> Đặt lại
                                </button>
                            </form>
                        @endif

                        <a class="btn btn-outline-secondary" href="{{ route('client.account.orders.show', $order) }}">
                            <i class="bi bi-eye me-1"></i> Xem chi tiết đơn hàng
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

