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
    </div>
@endsection

