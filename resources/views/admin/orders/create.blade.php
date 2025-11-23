@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Tạo đơn hàng thủ công</h2>
        <a href="{{ route('admin.orders.list') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    <form action="{{ route('admin.orders.store') }}" method="POST">
        @csrf

        <div class="row">
            {{-- Left Column --}}
            <div class="col-lg-8">
                {{-- Customer Selection --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Chọn khách hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Khách hàng <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-select" id="userSelect" required>
                                <option value="">-- Chọn khách hàng --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                            data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}"
                                            data-phone="{{ $user->phone }}">
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Customer Info --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Thông tin khách hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tên khách hàng <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" id="customer_name" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="customer_phone" id="customer_phone" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="customer_email" id="customer_email" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Shipping Info --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Thông tin vận chuyển</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                <input type="text" name="shipping_city" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Quận/Huyện <span class="text-danger">*</span></label>
                                <input type="text" name="shipping_district" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Phường/Xã <span class="text-danger">*</span></label>
                                <input type="text" name="shipping_ward" class="form-control" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Địa chỉ chi tiết <span class="text-danger">*</span></label>
                                <textarea name="shipping_address" class="form-control" rows="2" required></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phương thức vận chuyển <span class="text-danger">*</span></label>
                                <select name="shipping_method" class="form-select" required>
                                    <option value="standard">Tiêu chuẩn</option>
                                    <option value="express">Nhanh</option>
                                    <option value="fast">Siêu tốc</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phí vận chuyển</label>
                                <input type="number" name="shipping_fee" class="form-control" value="0" min="0" step="1000">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Products --}}
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Sản phẩm</h5>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addProductRow()">
                            <i class="bi bi-plus-circle"></i> Thêm sản phẩm
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="productsContainer">
                            {{-- Products will be added here --}}
                        </div>
                        <div class="mt-3">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Giảm giá</label>
                                    <input type="number" name="discount_amount" class="form-control" value="0" min="0" step="1000">
                                </div>
                                <div class="col-md-6 text-end">
                                    <strong>Tổng tiền: <span id="totalAmount">0₫</span></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column --}}
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header">
                        <h5 class="mb-0">Thông tin đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Phương thức thanh toán <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" required>
                                <option value="cash">COD</option>
                                <option value="bank">Chuyển khoản</option>
                                <option value="momo">Momo</option>
                                <option value="paypal">PayPal</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-save"></i> Tạo đơn hàng
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let productIndex = 0;

    // Auto-fill customer info when user is selected
    document.getElementById('userSelect').addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (option.value) {
            document.getElementById('customer_name').value = option.dataset.name || '';
            document.getElementById('customer_email').value = option.dataset.email || '';
            document.getElementById('customer_phone').value = option.dataset.phone || '';
        }
    });

    function addProductRow() {
        const html = `
            <div class="row mb-3 product-row" data-index="${productIndex}">
                <div class="col-md-5">
                    <select name="items[${productIndex}][product_id]" class="form-select product-select" required>
                        <option value="">Chọn sản phẩm</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                {{ $product->name }} - {{ number_format($product->price, 0, ',', '.') }}₫
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" name="items[${productIndex}][quantity]" 
                           class="form-control quantity-input" value="1" min="1" required>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control subtotal-display" value="0₫" readonly>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeProductRow(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
        document.getElementById('productsContainer').insertAdjacentHTML('beforeend', html);
        productIndex++;
        attachEventListeners();
    }

    function removeProductRow(btn) {
        btn.closest('.product-row').remove();
        calculateTotal();
    }

    function attachEventListeners() {
        document.querySelectorAll('.product-select, .quantity-input').forEach(el => {
            el.removeEventListener('change', calculateRowTotal);
            el.removeEventListener('input', calculateRowTotal);
            el.addEventListener('change', calculateRowTotal);
            el.addEventListener('input', calculateRowTotal);
        });
    }

    function calculateRowTotal(e) {
        const row = e.target.closest('.product-row');
        const select = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity-input');
        const subtotalDisplay = row.querySelector('.subtotal-display');

        const price = parseFloat(select.options[select.selectedIndex]?.dataset.price || 0);
        const quantity = parseInt(quantityInput.value || 0);
        const subtotal = price * quantity;

        subtotalDisplay.value = subtotal.toLocaleString('vi-VN') + '₫';
        calculateTotal();
    }

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal-display').forEach(display => {
            const value = display.value.replace(/[^\d]/g, '');
            total += parseFloat(value || 0);
        });
        
        const discount = parseFloat(document.querySelector('input[name="discount_amount"]').value || 0);
        const shippingFee = parseFloat(document.querySelector('input[name="shipping_fee"]').value || 0);
        const finalTotal = total - discount + shippingFee;
        
        document.getElementById('totalAmount').textContent = finalTotal.toLocaleString('vi-VN') + '₫';
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        addProductRow(); // Add first row
        document.querySelector('input[name="discount_amount"]').addEventListener('input', calculateTotal);
        document.querySelector('input[name="shipping_fee"]').addEventListener('input', calculateTotal);
    });
</script>
@endpush
@endsection
