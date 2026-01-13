@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">

        {{-- Tiêu đề --}}
        <div class="card border-0 shadow-sm mb-4 bg-body">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <h3 class="fw-bold text-primary mb-0">
                        <i class="bi bi-cart-fill me-2"></i>Danh sách đơn hàng
                    </h3>
                    <div>
                        <a href="{{ route('admin.orders.returns.index') }}" class="btn btn-warning position-relative">
                            <i class="bi bi-arrow-repeat me-1"></i>Quản lý trả hàng
                            @php
                                $pendingReturns = \App\Models\Order::where('return_status', 'requested')->count();
                            @endphp
                            @if ($pendingReturns > 0)
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $pendingReturns > 99 ? '99+' : $pendingReturns }}
                                </span>
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Toolbar: Bộ lọc + tìm kiếm --}}
        <div class="card shadow-sm bg-body mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.orders.list') }}"
                      class="row g-2 g-md-3 align-items-center">
                    {{-- Lọc trạng thái --}}
                    <div class="col-md-4">
                        <div class="d-flex flex-wrap gap-1">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="all" {{ ($status == 'all' || $status == null) ? 'selected' : '' }}>Tất cả</option>
                                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                                <option value="processing" {{ $status == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                <option value="shipping" {{ $status == 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                                <option value="delivered" {{ $status == 'delivered' ? 'selected' : '' }}>Đã giao</option>
                                <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                <option value="return_requested" {{ $status == 'return_requested' ? 'selected' : '' }}>Yêu cầu trả hàng</option>
                                <option value="returned" {{ $status == 'returned' ? 'selected' : '' }}>Đã trả hàng</option>
                            </select>
                        </div>
                    </div>

                    {{-- Tìm kiếm --}}
                    <div class="col-md-5">
                        <div class="input-group">
                            <input type="text" name="keyword" value="{{ $keyword ?? '' }}" class="form-control"
                                   placeholder="Tìm theo mã đơn hoặc tên khách hàng...">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Tìm kiếm
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Bảng đơn hàng --}}
        <div class="table-responsive shadow-sm rounded bg-white">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead>
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
                        <td data-label="Mã đơn">{{ $order->order_code }}</td>
                        <td data-label="Khách hàng">{{ $order->customer_name }}</td>
                        <td data-label="Tổng tiền">{{ number_format($order->final_total, 0, ',', '.') }}₫</td>
                        <td data-label="Trạng thái">
                            @php
                                $statusConfig = [
                                    'pending' => ['label' => 'Chờ xác nhận', 'color' => 'dark', 'icon' => 'bi-hourglass-split'],
                                    'processing' => ['label' => 'Đang xử lý', 'color' => 'primary', 'icon' => 'bi-gear'],
                                    'shipping' => ['label' => 'Đang giao hàng', 'color' => 'info', 'icon' => 'bi-truck'],
                                    'delivered' => ['label' => 'Đã giao', 'color' => 'success', 'icon' => 'bi-box-seam'],
                                    'completed' => ['label' => 'Hoàn thành', 'color' => 'success', 'icon' => 'bi-check-circle'],
                                    'cancelled' => ['label' => 'Đã hủy', 'color' => 'danger', 'icon' => 'bi-x-circle'],
                                    'return_requested' => ['label' => 'Yêu cầu trả hàng', 'color' => 'warning', 'icon' => 'bi-arrow-repeat'],
                                    'returned' => ['label' => 'Đã trả hàng', 'color' => 'secondary', 'icon' => 'bi-arrow-counterclockwise'],
                                ];
                                $cfg = $statusConfig[$order->order_status] ?? [
                                    'label' => ucfirst($order->order_status),
                                    'color' => 'secondary',
                                    'icon' => 'bi-question-circle'
                                ];
                            @endphp
                            <span class="badge bg-{{ $cfg['color'] }} px-3 py-2">
                                <i class="bi {{ $cfg['icon'] }} me-1"></i>{{ $cfg['label'] }}
                            </span>
                        </td>
                        <td data-label="Ngày đặt">{{ date('d/m/Y', strtotime($order->created_at)) }}</td>
                        <td data-label="Thao tác">
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
    </div>
@endsection
