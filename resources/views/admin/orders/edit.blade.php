@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa đơn hàng #' . $order->order_code)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit me-2"></i>Chỉnh sửa đơn hàng
        </h1>
        <div>
            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-info me-2">
                <i class="fas fa-eye me-2"></i>Xem chi tiết
            </a>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.orders.update', $order) }}">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Order Status -->
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-cogs me-2"></i>Trạng thái đơn hàng
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="order_status" class="form-label">Trạng thái đơn hàng <span class="text-danger">*</span></label>
                                    <select class="form-select @error('order_status') is-invalid @enderror" id="order_status" name="order_status" required>
                                        <option value="pending" {{ old('order_status', $order->order_status) == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                        <option value="processing" {{ old('order_status', $order->order_status) == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                        <option value="completed" {{ old('order_status', $order->order_status) == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                        <option value="cancelled" {{ old('order_status', $order->order_status) == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                        <option value="refunded" {{ old('order_status', $order->order_status) == 'refunded' ? 'selected' : '' }}>Đã hoàn tiền</option>
                                    </select>
                                    @error('order_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_status" class="form-label">Trạng thái thanh toán <span class="text-danger">*</span></label>
                                    <select class="form-select @error('payment_status') is-invalid @enderror" id="payment_status" name="payment_status" required>
                                        <option value="pending" {{ old('payment_status', $order->payment_status) == 'pending' ? 'selected' : '' }}>Chờ thanh toán</option>
                                        <option value="paid" {{ old('payment_status', $order->payment_status) == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                                        <option value="failed" {{ old('payment_status', $order->payment_status) == 'failed' ? 'selected' : '' }}>Thanh toán thất bại</option>
                                    </select>
                                    @error('payment_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Lưu ý:</strong> Thay đổi trạng thái đơn hàng sẽ được ghi nhận trong hệ thống.
                        </div>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-user me-2"></i>Thông tin khách hàng
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Khách hàng</label>
                            <div class="form-control-plaintext">
                                <strong>{{ $order->user->name }}</strong><br>
                                <small class="text-muted">{{ $order->user->email }}</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('shipping_address') is-invalid @enderror" 
                                      id="shipping_address" name="shipping_address" rows="3" 
                                      placeholder="Nhập địa chỉ giao hàng chi tiết" required>{{ old('shipping_address', $order->shipping_address) }}</textarea>
                            @error('shipping_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="shipping_phone" class="form-label">Số điện thoại giao hàng</label>
                            <input type="text" class="form-control @error('shipping_phone') is-invalid @enderror" 
                                   id="shipping_phone" name="shipping_phone" 
                                   value="{{ old('shipping_phone', $order->shipping_phone) }}" 
                                   placeholder="Nhập số điện thoại">
                            @error('shipping_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="shipping_fee" class="form-label">Phí vận chuyển</label>
                            <input type="number" class="form-control @error('shipping_fee') is-invalid @enderror" 
                                   id="shipping_fee" name="shipping_fee" 
                                   value="{{ old('shipping_fee', $order->shipping_fee) }}" 
                                   min="0" step="1000" placeholder="0">
                            @error('shipping_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Ghi chú</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3" 
                                      placeholder="Ghi chú thêm về đơn hàng">{{ old('notes', $order->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-6">
                <!-- Order Details -->
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
                                        <th width="50%">Sản phẩm</th>
                                        <th width="15%">Giá</th>
                                        <th width="10%">SL</th>
                                        <th width="20%">Thành tiền</th>
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
                                                         class="me-2" width="40" height="40" 
                                                         style="object-fit: cover; border-radius: 4px;">
                                                @else
                                                    <div class="bg-light me-2 d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px; border-radius: 4px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <strong>{{ $detail->product->name }}</strong><br>
                                                    <small class="text-muted">{{ $detail->product->category->name ?? 'N/A' }}</small>
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
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Lưu ý:</strong> Không thể chỉnh sửa sản phẩm trong đơn hàng để đảm bảo tính toàn vẹn dữ liệu.
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
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
                                <td class="text-end" id="shipping-fee-display">{{ $order->formatted_shipping_fee }}</td>
                            </tr>
                            <tr class="border-top">
                                <td><strong class="fs-5">Tổng cộng:</strong></td>
                                <td class="text-end">
                                    <strong class="fs-5 text-success" id="final-total">{{ $order->formatted_final_total }}</strong>
                                </td>
                            </tr>
                        </table>

                        @if($order->promotion)
                        <div class="mt-3 p-3 bg-light rounded">
                            <h6 class="text-info mb-2">
                                <i class="fas fa-tag me-2"></i>Mã khuyến mãi đã áp dụng
                            </h6>
                            <p class="mb-1"><strong>{{ $order->promotion->name }}</strong></p>
                            <p class="mb-0 text-muted">{{ $order->promotion->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Order History -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-history me-2"></i>Lịch sử đơn hàng
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Đơn hàng được tạo</h6>
                                    <p class="timeline-text">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            @if($order->updated_at != $order->created_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Cập nhật lần cuối</h6>
                                    <p class="timeline-text">{{ $order->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-save me-2"></i>Cập nhật đơn hàng
                        </button>
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-info btn-lg me-3">
                            <i class="fas fa-eye me-2"></i>Xem chi tiết
                        </a>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Hủy
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Update final total when shipping fee changes
document.getElementById('shipping_fee').addEventListener('input', function() {
    const shippingFee = parseFloat(this.value) || 0;
    const totalProducts = {{ $order->total_price }};
    const discountAmount = {{ $order->discount_amount }};
    const finalTotal = totalProducts - discountAmount + shippingFee;
    
    document.getElementById('shipping-fee-display').textContent = formatCurrency(shippingFee);
    document.getElementById('final-total').textContent = formatCurrency(finalTotal);
});

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}
</script>
@endpush

<style>
.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
}

.text-danger {
    color: #e74a3b !important;
}

.text-success {
    color: #1cc88a !important;
}

.fs-5 {
    font-size: 1.25rem !important;
}

.table th {
    background-color: #343a40 !important;
    color: white !important;
    border-color: #454d55 !important;
}

.badge {
    font-size: 0.75em;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1.125rem;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -25px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #007bff;
}

.timeline-title {
    margin: 0 0 5px 0;
    font-size: 14px;
    font-weight: 600;
}

.timeline-text {
    margin: 0;
    font-size: 12px;
    color: #6c757d;
}
</style>
@endsection
