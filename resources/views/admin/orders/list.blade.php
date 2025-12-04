@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Danh sách đơn hàng</h2>
        <div>
            <a href="{{ route('admin.orders.returns.index') }}" class="btn btn-warning me-2">
                <i class="bi bi-arrow-repeat me-1"></i>Quản lý trả hàng
            </a>
            <a href="{{ route('admin.orders.analytics', ['date_range' => 'all', 'start_date' => '2025-10-01', 'end_date' => '2025-11-29', 'status' => 'all']) }}" class="btn btn-primary">
                <i class="bi bi-bar-chart me-1"></i>Thống kê
            </a>
        </div>
    </div>

    {{-- BỘ LỌC TRẠNG THÁI + TÌM KIẾM --}}
    <form method="GET" action="{{ route('admin.orders.list') }}" class="row g-2 mb-3 align-items-center">
        <div class="col-md-3">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="all" {{ ($status == 'all' || $status == null) ? 'selected' : '' }}>Tất cả</option>
                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                <option value="processing" {{ $status == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                <option value="shipping" {{ $status == 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                <option value="return_requested" {{ $status == 'return_requested' ? 'selected' : '' }}>Yêu cầu trả hàng</option>
                <option value="returned" {{ $status == 'returned' ? 'selected' : '' }}>Đã trả hàng</option>
            </select>
        </div>

        <div class="col-md-4">
            <input type="text" name="keyword" value="{{ $keyword ?? '' }}" class="form-control"
                   placeholder="Tìm theo mã đơn hoặc tên khách hàng...">
        </div>

        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-search"></i> Tìm kiếm
            </button>
        </div>
    </form>

    {{-- DANH SÁCH ĐƠN HÀNG --}}
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Mã đơn</th>
                <th>Khách hàng</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Ngày đặt</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
        @forelse($orders as $order)
            <tr>
                <td>{{ $order->order_code }}</td>
                <td>{{ $order->customer_name }}</td>
                <td>{{ number_format($order->final_total, 0, ',', '.') }}₫</td>

                <td>
    @php
        $statusConfig = [
            'pending' => ['label' => 'Chờ xác nhận', 'color' => 'dark', 'icon' => 'bi-hourglass-split'],
            'processing' => ['label' => 'Đang xử lý', 'color' => 'primary', 'icon' => 'bi-gear'],
            'shipping' => ['label' => 'Đang giao hàng', 'color' => 'info', 'icon' => 'bi-truck'],
            'completed' => ['label' => 'Hoàn thành', 'color' => 'success', 'icon' => 'bi-check-circle'],
            'cancelled' => ['label' => 'Đã hủy', 'color' => 'danger', 'icon' => 'bi-x-circle'],
            'return_requested' => ['label' => 'Yêu cầu trả hàng', 'color' => 'warning', 'icon' => 'bi-arrow-repeat'],
            'returned' => ['label' => 'Đã trả hàng', 'color' => 'secondary', 'icon' => 'bi-arrow-counterclockwise'],
        ];
        $cfg = $statusConfig[$order->order_status] ?? ['label' => ucfirst($order->order_status), 'color' => 'secondary', 'icon' => 'bi-question-circle'];
    @endphp
    <span class="badge bg-{{ $cfg['color'] }} px-3 py-2"><i class="bi {{ $cfg['icon'] }} me-1"></i>{{ $cfg['label'] }}</span>
                </td>

                <td>{{ date('d/m/Y', strtotime($order->created_at)) }}</td>
                <td>
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info">
                        Cập nhật
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted">Không có đơn hàng nào phù hợp.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
