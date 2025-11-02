@extends('admin.layouts.app')

@section('title', 'Quản lý đơn hàng')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-shopping-cart me-2"></i>Quản lý đơn hàng
        </h1>
        <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tạo đơn hàng mới
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng đơn hàng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_orders']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Chờ xử lý</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['pending_orders']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Hoàn thành</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['completed_orders']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Doanh thu hôm nay</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['today_revenue'], 0, ',', '.') }} VNĐ</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Bộ lọc và tìm kiếm
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="order_id" class="form-label">ID đơn hàng</label>
                    <input type="number" class="form-control" id="order_id" name="order_id" 
                           value="{{ request('order_id') }}" placeholder="Nhập ID đơn hàng">
                </div>
                <div class="col-md-3">
                    <label for="order_code" class="form-label">Mã đơn hàng</label>
                    <input type="text" class="form-control" id="order_code" name="order_code" 
                           value="{{ request('order_code') }}" placeholder="Nhập mã đơn hàng">
                </div>
                <div class="col-md-3">
                    <label for="customer_name" class="form-label">Tên khách hàng</label>
                    <input type="text" class="form-control" id="customer_name" name="customer_name" 
                           value="{{ request('customer_name') }}" placeholder="Nhập tên khách hàng">
                </div>
                <div class="col-md-3">
                    <label for="customer_email" class="form-label">Email khách hàng</label>
                    <input type="email" class="form-control" id="customer_email" name="customer_email" 
                           value="{{ request('customer_email') }}" placeholder="Nhập email">
                </div>
                <div class="col-md-3">
                    <label for="order_status" class="form-label">Trạng thái đơn hàng</label>
                    <select class="form-select" id="order_status" name="order_status">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending" {{ request('order_status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                        <option value="processing" {{ request('order_status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                        <option value="completed" {{ request('order_status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="cancelled" {{ request('order_status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                        <option value="refunded" {{ request('order_status') == 'refunded' ? 'selected' : '' }}>Đã hoàn tiền</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="payment_status" class="form-label">Trạng thái thanh toán</label>
                    <select class="form-select" id="payment_status" name="payment_status">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Chờ thanh toán</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                        <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Thanh toán thất bại</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="payment_method" class="form-label">Phương thức thanh toán</label>
                    <select class="form-select" id="payment_method" name="payment_method">
                        <option value="">Tất cả phương thức</option>
                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Tiền mặt</option>
                        <option value="bank" {{ request('payment_method') == 'bank' ? 'selected' : '' }}>Chuyển khoản</option>
                        <option value="momo" {{ request('payment_method') == 'momo' ? 'selected' : '' }}>MoMo</option>
                        <option value="paypal" {{ request('payment_method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Từ ngày</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" 
                           value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Đến ngày</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" 
                           value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <label for="total_from" class="form-label">Tổng tiền từ</label>
                    <input type="number" class="form-control" id="total_from" name="total_from" 
                           value="{{ request('total_from') }}" placeholder="0" min="0">
                </div>
                <div class="col-md-3">
                    <label for="total_to" class="form-label">Tổng tiền đến</label>
                    <input type="number" class="form-control" id="total_to" name="total_to" 
                           value="{{ request('total_to') }}" placeholder="999999999" min="0">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Tìm kiếm
                    </button>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Xóa bộ lọc
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Danh sách đơn hàng
            </h6>
            <div class="d-flex align-items-center">
                <label for="per_page" class="form-label me-2 mb-0">Hiển thị:</label>
                <select class="form-select form-select-sm" id="per_page" name="per_page" style="width: auto;" onchange="this.form.submit()">
                    <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                    <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15</option>
                    <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="ordersTable">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="12%">Mã đơn hàng</th>
                            <th width="15%">Khách hàng</th>
                            <th width="10%">Ngày đặt</th>
                            <th width="12%">Tổng tiền</th>
                            <th width="10%">Trạng thái</th>
                            <th width="10%">Thanh toán</th>
                            <th width="8%">Phương thức</th>
                            <th width="18%">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>
                                <strong class="text-primary">{{ $order->order_code }}</strong>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $order->user->name }}</strong><br>
                                    <small class="text-muted">{{ $order->user->email }}</small>
                                </div>
                            </td>
                            <td>
                                <div>
                                    {{ $order->created_at->format('d/m/Y') }}<br>
                                    <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                </div>
                            </td>
                            <td>
                                <strong class="text-success">{{ $order->formatted_final_total }}</strong>
                                @if($order->discount_amount > 0)
                                <br><small class="text-danger">-{{ $order->formatted_discount_amount }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $order->status_badge_class }}">
                                    {{ $order->status_text }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $order->payment_status_badge_class }}">
                                    {{ $order->payment_status_text }}
                                </span>
                            </td>
                            <td>
                                <small>{{ $order->payment_method_text }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.orders.show', $order) }}" 
                                       class="btn btn-sm btn-info" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.orders.edit', $order) }}" 
                                       class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="confirmDelete({{ $order->id }})" title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>Không có đơn hàng nào</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($orders->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Hiển thị {{ $orders->firstItem() }} đến {{ $orders->lastItem() }} 
                    trong tổng số {{ $orders->total() }} đơn hàng
                </div>
                <div>
                    {{ $orders->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa đơn hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa đơn hàng này không? Hành động này không thể hoàn tác.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(orderId) {
    document.getElementById('deleteForm').action = `/admin/orders/${orderId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Auto-submit form when per_page changes
document.getElementById('per_page').addEventListener('change', function() {
    const form = this.closest('form');
    if (form) {
        form.submit();
    }
});
</script>
@endpush

<style>
.table th {
    background-color: #343a40 !important;
    color: white !important;
    border-color: #454d55 !important;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75em;
}

.btn-group .btn {
    margin-right: 2px;
}

.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.text-xs {
    font-size: 0.7rem;
}

.font-weight-bold {
    font-weight: 700 !important;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.text-gray-300 {
    color: #dddfeb !important;
}
</style>
@endsection
