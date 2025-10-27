@extends('admin.layouts.app')

@section('title', 'Tạo đơn hàng mới')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus-circle me-2"></i>Tạo đơn hàng mới
        </h1>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>

    <form method="POST" action="{{ route('admin.orders.store') }}" id="orderForm">
        @csrf
        
        <div class="row">
            <!-- Customer Information -->
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-user me-2"></i>Thông tin khách hàng
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Khách hàng <span class="text-danger">*</span></label>
                            <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                <option value="">Chọn khách hàng</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('shipping_address') is-invalid @enderror" 
                                      id="shipping_address" name="shipping_address" rows="3" 
                                      placeholder="Nhập địa chỉ giao hàng chi tiết" required>{{ old('shipping_address') }}</textarea>
                            @error('shipping_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="shipping_phone" class="form-label">Số điện thoại giao hàng</label>
                            <input type="text" class="form-control @error('shipping_phone') is-invalid @enderror" 
                                   id="shipping_phone" name="shipping_phone" 
                                   value="{{ old('shipping_phone') }}" 
                                   placeholder="Nhập số điện thoại">
                            @error('shipping_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="shipping_fee" class="form-label">Phí vận chuyển</label>
                            <input type="number" class="form-control @error('shipping_fee') is-invalid @enderror" 
                                   id="shipping_fee" name="shipping_fee" 
                                   value="{{ old('shipping_fee', 0) }}" 
                                   min="0" step="1000" placeholder="0">
                            @error('shipping_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Phương thức thanh toán <span class="text-danger">*</span></label>
                            <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                                <option value="">Chọn phương thức thanh toán</option>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Tiền mặt</option>
                                <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>Chuyển khoản</option>
                                <option value="momo" {{ old('payment_method') == 'momo' ? 'selected' : '' }}>MoMo</option>
                                <option value="paypal" {{ old('payment_method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="promotion_id" class="form-label">Mã khuyến mãi</label>
                            <select class="form-select @error('promotion_id') is-invalid @enderror" id="promotion_id" name="promotion_id">
                                <option value="">Không sử dụng mã khuyến mãi</option>
                                @foreach($promotions as $promotion)
                                <option value="{{ $promotion->id }}" {{ old('promotion_id') == $promotion->id ? 'selected' : '' }}>
                                    {{ $promotion->code }} - {{ $promotion->name }} ({{ $promotion->formatted_discount_value }})
                                </option>
                                @endforeach
                            </select>
                            @error('promotion_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Ghi chú</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3" 
                                      placeholder="Ghi chú thêm về đơn hàng">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Selection -->
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-box me-2"></i>Chọn sản phẩm
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="products-container">
                            <div class="product-item border rounded p-3 mb-3">
                                <div class="row align-items-center">
                                    <div class="col-md-5">
                                        <label class="form-label">Sản phẩm</label>
                                        <select class="form-select product-select" name="products[0][product_id]" required>
                                            <option value="">Chọn sản phẩm</option>
                                            @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                                {{ $product->name }} - {{ number_format($product->price, 0, ',', '.') }} VNĐ
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Số lượng</label>
                                        <input type="number" class="form-control quantity-input" 
                                               name="products[0][quantity]" min="1" value="1" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Thành tiền</label>
                                        <input type="text" class="form-control subtotal-display" readonly>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm remove-product" style="margin-top: 25px;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-outline-primary" id="add-product">
                            <i class="fas fa-plus me-2"></i>Thêm sản phẩm
                        </button>
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
                                <td class="text-end" id="total-products">0 VNĐ</td>
                            </tr>
                            <tr>
                                <td><strong>Giảm giá:</strong></td>
                                <td class="text-end text-danger" id="discount-amount">0 VNĐ</td>
                            </tr>
                            <tr>
                                <td><strong>Phí vận chuyển:</strong></td>
                                <td class="text-end" id="shipping-fee-display">0 VNĐ</td>
                            </tr>
                            <tr class="border-top">
                                <td><strong class="fs-5">Tổng cộng:</strong></td>
                                <td class="text-end">
                                    <strong class="fs-5 text-success" id="final-total">0 VNĐ</strong>
                                </td>
                            </tr>
                        </table>
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
                            <i class="fas fa-save me-2"></i>Tạo đơn hàng
                        </button>
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
let productIndex = 0;

// Add product row
document.getElementById('add-product').addEventListener('click', function() {
    productIndex++;
    const container = document.getElementById('products-container');
    const newProduct = document.querySelector('.product-item').cloneNode(true);
    
    // Update input names
    newProduct.querySelector('.product-select').name = `products[${productIndex}][product_id]`;
    newProduct.querySelector('.quantity-input').name = `products[${productIndex}][quantity]`;
    newProduct.querySelector('.quantity-input').value = 1;
    newProduct.querySelector('.subtotal-display').value = '';
    
    container.appendChild(newProduct);
    updateOrderSummary();
});

// Remove product row
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-product')) {
        const productItem = e.target.closest('.product-item');
        if (document.querySelectorAll('.product-item').length > 1) {
            productItem.remove();
            updateOrderSummary();
        }
    }
});

// Update subtotal when product or quantity changes
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('product-select') || e.target.classList.contains('quantity-input')) {
        updateSubtotal(e.target.closest('.product-item'));
        updateOrderSummary();
    }
});

// Update shipping fee display
document.getElementById('shipping_fee').addEventListener('input', function() {
    updateOrderSummary();
});

// Update promotion discount
document.getElementById('promotion_id').addEventListener('change', function() {
    updateOrderSummary();
});

function updateSubtotal(productItem) {
    const productSelect = productItem.querySelector('.product-select');
    const quantityInput = productItem.querySelector('.quantity-input');
    const subtotalDisplay = productItem.querySelector('.subtotal-display');
    
    if (productSelect.value && quantityInput.value) {
        const price = parseFloat(productSelect.selectedOptions[0].dataset.price);
        const quantity = parseInt(quantityInput.value);
        const subtotal = price * quantity;
        subtotalDisplay.value = formatCurrency(subtotal);
    } else {
        subtotalDisplay.value = '';
    }
}

function updateOrderSummary() {
    let totalProducts = 0;
    
    // Calculate total products
    document.querySelectorAll('.product-item').forEach(function(item) {
        const productSelect = item.querySelector('.product-select');
        const quantityInput = item.querySelector('.quantity-input');
        
        if (productSelect.value && quantityInput.value) {
            const price = parseFloat(productSelect.selectedOptions[0].dataset.price);
            const quantity = parseInt(quantityInput.value);
            totalProducts += price * quantity;
        }
    });
    
    // Calculate discount
    let discountAmount = 0;
    const promotionSelect = document.getElementById('promotion_id');
    if (promotionSelect.value) {
        // This would need to be calculated based on the selected promotion
        // For now, we'll set it to 0 and handle it server-side
    }
    
    // Get shipping fee
    const shippingFee = parseFloat(document.getElementById('shipping_fee').value) || 0;
    
    // Calculate final total
    const finalTotal = totalProducts - discountAmount + shippingFee;
    
    // Update display
    document.getElementById('total-products').textContent = formatCurrency(totalProducts);
    document.getElementById('discount-amount').textContent = formatCurrency(discountAmount);
    document.getElementById('shipping-fee-display').textContent = formatCurrency(shippingFee);
    document.getElementById('final-total').textContent = formatCurrency(finalTotal);
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

// Initialize
updateOrderSummary();
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

.product-item {
    background-color: #f8f9fa;
}

.product-item:hover {
    background-color: #e9ecef;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1.125rem;
}
</style>
@endsection
