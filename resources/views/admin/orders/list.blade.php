@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Danh sách đơn hàng</h2>

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

                {{-- Badge màu trạng thái --}}
                <td>
    @php
        // 7 Trạng thái mới với màu sắc và tên tiếng Việt
        $colors = [
            'pending'          => 'dark',      // Xám đậm
            'processing'       => 'primary',   // Xanh dương
            'shipping'         => 'info',      // Xanh nhạt
            'completed'        => 'success',   // Xanh lá
            'cancelled'        => 'danger',    // Đỏ
            'return_requested' => 'warning',   // Vàng/Cam
            'returned'         => 'secondary', // Xám
        ];

        $labels = [
            'pending'          => 'Chờ xác nhận',
            'processing'       => 'Đang xử lý',
            'shipping'         => 'Đang giao hàng',
            'completed'        => 'Hoàn thành',
            'cancelled'        => 'Đã hủy',
            'return_requested' => 'Yêu cầu trả hàng',
            'returned'         => 'Đã trả hàng',
        ];

        $color = $colors[$order->order_status] ?? 'light';
        $label = $labels[$order->order_status] ?? ucfirst($order->order_status);
    @endphp

    <span class="badge bg-{{ $color }}">{{ $label }}</span>
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
