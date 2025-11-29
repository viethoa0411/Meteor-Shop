@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1">Đơn vận chuyển</h2>
            <div class="text-muted small">
                Đơn hàng: #{{ $order->order_code }}
            </div>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
            @if(!in_array($order->order_status, ['cancelled', 'completed']))
                <a href="{{ route('admin.orders.shipments.create', $order->id) }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tạo đơn vận chuyển
                </a>
            @endif
        </div>
    </div>

    {{-- Order Summary --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <small class="text-muted">Khách hàng</small>
                    <div class="fw-bold">{{ $order->customer_name }}</div>
                </div>
                <div class="col-md-3">
                    <small class="text-muted">Trạng thái đơn hàng</small>
                    <div>
                        <span class="badge bg-{{ $order->status_meta['badge'] }}">
                            {{ $order->status_meta['label'] }}
                        </span>
                    </div>
                </div>
                <div class="col-md-3">
                    <small class="text-muted">Tổng tiền</small>
                    <div class="fw-bold">{{ number_format($order->final_total, 0, ',', '.') }}₫</div>
                </div>
                <div class="col-md-3">
                    <small class="text-muted">Địa chỉ giao hàng</small>
                    <div class="small">{{ $order->shipping_city }}, {{ $order->shipping_district }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Shipments List --}}
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Danh sách đơn vận chuyển ({{ $shipments->count() }})</h5>
        </div>
        <div class="card-body">
            @if($shipments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Mã đơn vận chuyển</th>
                                <th>Đơn vị vận chuyển</th>
                                <th>Mã vận đơn</th>
                                <th>Trạng thái</th>
                                <th>Phí vận chuyển</th>
                                <th>Ngày tạo</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shipments as $shipment)
                                <tr>
                                    <td>
                                        <strong>{{ $shipment->shipment_code }}</strong>
                                    </td>
                                    <td>
                                        <div>{{ $shipment->carrier_name ?? ucfirst($shipment->carrier) }}</div>
                                        <small class="text-muted">{{ strtoupper($shipment->carrier) }}</small>
                                    </td>
                                    <td>
                                        @if($shipment->tracking_number)
                                            <div class="fw-bold">{{ $shipment->tracking_number }}</div>
                                            @if($shipment->tracking_url)
                                                <a href="{{ $shipment->tracking_url }}" target="_blank" class="small">
                                                    <i class="bi bi-box-arrow-up-right"></i> Theo dõi
                                                </a>
                                            @endif
                                        @else
                                            <span class="text-muted">Chưa có</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusLabels = [
                                                'pending' => ['label' => 'Chờ xử lý', 'badge' => 'warning'],
                                                'label_created' => ['label' => 'Đã tạo nhãn', 'badge' => 'info'],
                                                'picked_up' => ['label' => 'Đã lấy hàng', 'badge' => 'primary'],
                                                'in_transit' => ['label' => 'Đang vận chuyển', 'badge' => 'info'],
                                                'out_for_delivery' => ['label' => 'Đang giao hàng', 'badge' => 'primary'],
                                                'delivered' => ['label' => 'Đã giao', 'badge' => 'success'],
                                                'failed' => ['label' => 'Thất bại', 'badge' => 'danger'],
                                                'returned' => ['label' => 'Đã trả lại', 'badge' => 'secondary'],
                                            ];
                                            $statusInfo = $statusLabels[$shipment->status] ?? ['label' => ucfirst($shipment->status), 'badge' => 'secondary'];
                                        @endphp
                                        <span class="badge bg-{{ $statusInfo['badge'] }}">
                                            {{ $statusInfo['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ number_format($shipment->shipping_cost ?? 0, 0, ',', '.') }}₫
                                    </td>
                                    <td>
                                        <div>{{ $shipment->created_at->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $shipment->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#statusModal{{ $shipment->id }}">
                                                <i class="bi bi-pencil"></i> Cập nhật
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Status Update Modal --}}
                                <div class="modal fade" id="statusModal{{ $shipment->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.orders.shipments.updateStatus', [$order->id, $shipment->id]) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Cập nhật trạng thái vận chuyển</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Trạng thái hiện tại</label>
                                                        <div class="alert alert-{{ $statusInfo['badge'] }} mb-0">
                                                            {{ $statusInfo['label'] }}
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Chuyển sang trạng thái <span class="text-danger">*</span></label>
                                                        <select name="status" class="form-select" required>
                                                            <option value="pending" {{ $shipment->status === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                                            <option value="label_created" {{ $shipment->status === 'label_created' ? 'selected' : '' }}>Đã tạo nhãn</option>
                                                            <option value="picked_up" {{ $shipment->status === 'picked_up' ? 'selected' : '' }}>Đã lấy hàng</option>
                                                            <option value="in_transit" {{ $shipment->status === 'in_transit' ? 'selected' : '' }}>Đang vận chuyển</option>
                                                            <option value="out_for_delivery" {{ $shipment->status === 'out_for_delivery' ? 'selected' : '' }}>Đang giao hàng</option>
                                                            <option value="delivered" {{ $shipment->status === 'delivered' ? 'selected' : '' }}>Đã giao</option>
                                                            <option value="failed" {{ $shipment->status === 'failed' ? 'selected' : '' }}>Thất bại</option>
                                                            <option value="returned" {{ $shipment->status === 'returned' ? 'selected' : '' }}>Đã trả lại</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-truck" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="text-muted mt-3">Chưa có đơn vận chuyển nào</p>
                    @if(!in_array($order->order_status, ['cancelled', 'completed']))
                        <a href="{{ route('admin.orders.shipments.create', $order->id) }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Tạo đơn vận chuyển đầu tiên
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

