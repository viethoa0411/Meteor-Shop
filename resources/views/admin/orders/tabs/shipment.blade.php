<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-truck"></i> Đơn vận chuyển</h5>
                @if($order->canShip())
                    <a href="{{ route('admin.orders.shipments.create', $order->id) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle"></i> Tạo đơn vận chuyển
                    </a>
                @endif
            </div>
            <div class="card-body p-0">
                @if($order->shipments && $order->shipments->count() > 0)
                    @foreach($order->shipments as $shipment)
                        <div class="border-bottom p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <div class="fw-bold">{{ $shipment->shipment_code }}</div>
                                    <div class="small text-muted">
                                        Đơn vị: {{ $shipment->carrier_name ?? ucfirst($shipment->carrier) }}
                                    </div>
                                </div>
                                <div>
                                    @php
                                        $statusMeta = $shipment->status_meta;
                                    @endphp
                                    <span class="badge bg-{{ $statusMeta['badge'] }}">
                                        {{ $statusMeta['label'] }}
                                    </span>
                                </div>
                            </div>
                            @if($shipment->tracking_number)
                                <div class="mb-2">
                                    <strong>Mã tracking:</strong>
                                    @if($shipment->tracking_url)
                                        <a href="{{ $shipment->tracking_url }}" target="_blank" class="text-decoration-none">
                                            {{ $shipment->tracking_number }}
                                        </a>
                                    @else
                                        {{ $shipment->tracking_number }}
                                    @endif
                                </div>
                            @endif
                            <div class="small text-muted">
                                Tạo lúc: {{ $shipment->created_at->format('d/m/Y H:i') }}
                                @if($shipment->creator)
                                    bởi {{ $shipment->creator->name }}
                                @endif
                            </div>
                            @if($shipment->delivered_at)
                                <div class="text-success small mt-2">
                                    <i class="bi bi-check-circle"></i> Đã giao: {{ $shipment->delivered_at->format('d/m/Y H:i') }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-truck" style="font-size: 3rem;"></i>
                        <div class="mt-2">Chưa có đơn vận chuyển</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Thông tin vận chuyển</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Địa chỉ giao hàng:</strong>
                    <div class="small text-muted mt-1">
                        {{ $order->shipping_address }},<br>
                        {{ $order->shipping_ward }}, {{ $order->shipping_district }}, {{ $order->shipping_city }}
                    </div>
                </div>
                <div class="mb-3">
                    <strong>Phương thức:</strong>
                    <div class="small text-muted mt-1">{{ $order->shipping_method }}</div>
                </div>
                <div class="mb-3">
                    <strong>Phí vận chuyển:</strong>
                    <div class="small text-muted mt-1">{{ number_format($order->shipping_fee ?? 0, 0, ',', '.') }}₫</div>
                </div>
                @if($order->tracking_code)
                    <div>
                        <strong>Mã tracking hiện tại:</strong>
                        <div class="small text-muted mt-1">
                            @if($order->tracking_url)
                                <a href="{{ $order->tracking_url }}" target="_blank">{{ $order->tracking_code }}</a>
                            @else
                                {{ $order->tracking_code }}
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

