@extends('admin.layouts.app')

@section('title', 'Chi tiết đơn hàng #' . $order->order_code)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-invoice me-2"></i>Chi tiết đơn hàng
            </h1>
            <p class="text-muted mb-0">Mã đơn hàng: <strong>{{ $order->order_code }}</strong></p>
        </div>
        <div>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
            <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Chỉnh sửa
            </a>
        </div>
    </div>

    <!-- Order Status Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Trạng thái đơn hàng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <span class="badge {{ $order->status_badge_class }} fs-6">
                                    {{ $order->status_text }}
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Trạng thái thanh toán</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <span class="badge {{ $order->payment_status_badge_class }} fs-6">
                                    {{ $order->payment_status_text }}
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Order Information -->
        <div class="col-lg-8">
            <!-- Customer Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user me-2"></i>Thông tin khách hàng
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Tên khách hàng:</strong></td>
                                    <td>{{ $order->user->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $order->user->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Số điện thoại:</strong></td>
                                    <td>{{ $order->user->phone ?? 'Chưa cập nhật' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Địa chỉ:</strong></td>
                                    <td>{{ $order->user->address ?? 'Chưa cập nhật' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Ngày đặt hàng:</strong></td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Cập nhật cuối:</strong></td>
                                    <td>{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phương thức TT:</strong></td>
                                    <td>{{ $order->payment_method_text }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Mã khuyến mãi:</strong></td>
                                    <td>
                                        @if($order->promotion)
                                            <span class="badge bg-info">{{ $order->promotion->code }}</span>
                                        @else
                                            <span class="text-muted">Không có</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-box me-2"></i>Chi tiết sản phẩm
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="40%">Sản phẩm</th>
                                    <th width="15%">Giá</th>
                                    <th width="10%">Số lượng</th>
                                    <th width="15%">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderDetails as $index => $detail)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($detail->product->image)
                                                <img src="{{ asset('storage/' . $detail->product->image) }}" 
                                                     alt="{{ $detail->product->name }}" 
                                                     class="me-3" width="50" height="50" 
                                                     style="object-fit: cover; border-radius: 4px;">
                                            @else
                                                <div class="bg-light me-3 d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px; border-radius: 4px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <strong>{{ $detail->product->name }}</strong><br>
                                                <small class="text-muted">
                                                    Danh mục: {{ $detail->product->category->name ?? 'N/A' }}
                                                    @if($detail->product->brand)
                                                        | Thương hiệu: {{ $detail->product->brand->name }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $detail->formatted_price }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $detail->quantity }}</span>
                                    </td>
                                    <td>
                                        <strong class="text-success">{{ $detail->formatted_subtotal }}</strong>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-shipping-fast me-2"></i>Thông tin giao hàng
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Địa chỉ giao hàng:</strong></td>
                                    <td>{{ $order->shipping_address }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Số điện thoại:</strong></td>
                                    <td>{{ $order->shipping_phone ?? 'Chưa cập nhật' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Phí vận chuyển:</strong></td>
                                    <td>{{ $order->formatted_shipping_fee }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Ghi chú:</strong></td>
                                    <td>{{ $order->notes ?? 'Không có ghi chú' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calculator me-2"></i>Tổng kết đơn hàng
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Tổng tiền sản phẩm:</strong></td>
                            <td class="text-end">{{ $order->formatted_total_price }}</td>
                        </tr>
                        @if($order->discount_amount > 0)
                        <tr>
                            <td><strong>Giảm giá:</strong></td>
                            <td class="text-end text-danger">-{{ $order->formatted_discount_amount }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td><strong>Phí vận chuyển:</strong></td>
                            <td class="text-end">{{ $order->formatted_shipping_fee }}</td>
                        </tr>
                        <tr class="border-top">
                            <td><strong class="fs-5">Tổng cộng:</strong></td>
                            <td class="text-end">
                                <strong class="fs-5 text-success">{{ $order->formatted_final_total }}</strong>
                            </td>
                        </tr>
                    </table>

                    @if($order->promotion)
                    <div class="mt-3 p-3 bg-light rounded">
                        <h6 class="text-info mb-2">
                            <i class="fas fa-tag me-2"></i>Mã khuyến mãi
                        </h6>
                        <p class="mb-1"><strong>{{ $order->promotion->name }}</strong></p>
                        <p class="mb-0 text-muted">{{ $order->promotion->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>Thao tác nhanh
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($order->order_status === 'pending')
                        <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="order_status" value="processing">
                            <input type="hidden" name="payment_status" value="{{ $order->payment_status }}">
                            <input type="hidden" name="shipping_address" value="{{ $order->shipping_address }}">
                            <input type="hidden" name="shipping_phone" value="{{ $order->shipping_phone }}">
                            <input type="hidden" name="shipping_fee" value="{{ $order->shipping_fee }}">
                            <input type="hidden" name="notes" value="{{ $order->notes }}">
                            <button type="submit" class="btn btn-info w-100">
                                <i class="fas fa-play me-2"></i>Bắt đầu xử lý
                            </button>
                        </form>
                        @endif

                        @if($order->order_status === 'processing')
                        <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="order_status" value="completed">
                            <input type="hidden" name="payment_status" value="paid">
                            <input type="hidden" name="shipping_address" value="{{ $order->shipping_address }}">
                            <input type="hidden" name="shipping_phone" value="{{ $order->shipping_phone }}">
                            <input type="hidden" name="shipping_fee" value="{{ $order->shipping_fee }}">
                            <input type="hidden" name="notes" value="{{ $order->notes }}">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check me-2"></i>Hoàn thành đơn hàng
                            </button>
                        </form>
                        @endif

                        @if(in_array($order->order_status, ['pending', 'processing']))
                        <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="order_status" value="cancelled">
                            <input type="hidden" name="payment_status" value="{{ $order->payment_status }}">
                            <input type="hidden" name="shipping_address" value="{{ $order->shipping_address }}">
                            <input type="hidden" name="shipping_phone" value="{{ $order->shipping_phone }}">
                            <input type="hidden" name="shipping_fee" value="{{ $order->shipping_fee }}">
                            <input type="hidden" name="notes" value="{{ $order->notes }}">
                            <button type="submit" class="btn btn-danger w-100" 
                                    onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
                                <i class="fas fa-times me-2"></i>Hủy đơn hàng
                            </button>
                        </form>
                        @endif

                        <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-warning w-100">
                            <i class="fas fa-edit me-2"></i>Chỉnh sửa đơn hàng
                        </a>

                        <button type="button" class="btn btn-outline-danger w-100" 
                                onclick="confirmDelete({{ $order->id }})">
                            <i class="fas fa-trash me-2"></i>Xóa đơn hàng
                        </button>
                    </div>
                </div>
            </div>
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
                Bạn có chắc chắn muốn xóa đơn hàng <strong>{{ $order->order_code }}</strong> không? 
                Hành động này không thể hoàn tác.
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
</script>
@endpush

<style>
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

.table th {
    background-color: #343a40 !important;
    color: white !important;
    border-color: #454d55 !important;
}

.badge {
    font-size: 0.75em;
}

.fs-5 {
    font-size: 1.25rem !important;
}

.fs-6 {
    font-size: 1rem !important;
}
</style>
@endsection
