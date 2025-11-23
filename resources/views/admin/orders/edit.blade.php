@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Chỉnh sửa đơn hàng #{{ $order->order_code }}</h2>
        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
            </a>
    </div>

    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            {{-- Left Column --}}
            <div class="col-lg-8">
                {{-- Customer Info --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Thông tin khách hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tên khách hàng <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" class="form-control" 
                                       value="{{ old('customer_name', $order->customer_name) }}" required>
                                </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="customer_phone" class="form-control" 
                                       value="{{ old('customer_phone', $order->customer_phone) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="customer_email" class="form-control" 
                                       value="{{ old('customer_email', $order->customer_email) }}" required>
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
                                <input type="text" name="shipping_city" class="form-control" 
                                       value="{{ old('shipping_city', $order->shipping_city) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Quận/Huyện <span class="text-danger">*</span></label>
                                <input type="text" name="shipping_district" class="form-control" 
                                       value="{{ old('shipping_district', $order->shipping_district) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Phường/Xã <span class="text-danger">*</span></label>
                                <input type="text" name="shipping_ward" class="form-control" 
                                       value="{{ old('shipping_ward', $order->shipping_ward) }}" required>
                        </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Địa chỉ chi tiết <span class="text-danger">*</span></label>
                                <textarea name="shipping_address" class="form-control" rows="2" required>{{ old('shipping_address', $order->shipping_address) }}</textarea>
                        </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phương thức vận chuyển <span class="text-danger">*</span></label>
                                <select name="shipping_method" class="form-select" required>
                                    <option value="standard" {{ $order->shipping_method === 'standard' ? 'selected' : '' }}>Tiêu chuẩn</option>
                                    <option value="express" {{ $order->shipping_method === 'express' ? 'selected' : '' }}>Nhanh</option>
                                    <option value="fast" {{ $order->shipping_method === 'fast' ? 'selected' : '' }}>Siêu tốc</option>
                                </select>
                        </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phương thức thanh toán <span class="text-danger">*</span></label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="cash" {{ $order->payment_method === 'cash' ? 'selected' : '' }}>COD</option>
                                    <option value="bank" {{ $order->payment_method === 'bank' ? 'selected' : '' }}>Chuyển khoản</option>
                                    <option value="momo" {{ $order->payment_method === 'momo' ? 'selected' : '' }}>Momo</option>
                                    <option value="paypal" {{ $order->payment_method === 'paypal' ? 'selected' : '' }}>PayPal</option>
                                </select>
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
                            @foreach($order->items as $index => $item)
                                <div class="row mb-3 product-row" data-index="{{ $index }}">
                                    <div class="col-md-5">
                                        <select name="items[{{ $index }}][product_id]" class="form-select product-select" required>
                                            <option value="">Chọn sản phẩm</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" 
                                                        data-price="{{ $product->price }}"
                                                        {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                    {{ $product->name }} - {{ number_format($product->price, 0, ',', '.') }}₫
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" name="items[{{ $index }}][quantity]" 
                                               class="form-control quantity-input" 
                                               value="{{ $item->quantity }}" 
                                               min="1" required>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control subtotal-display" 
                                               value="{{ number_format($item->subtotal, 0, ',', '.') }}₫" 
                                               readonly>
                                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeProductRow(this)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                                </div>
                                            </div>
                                    @endforeach
                        </div>
                        <div class="mt-3 text-end">
                            <strong>Tổng tiền: <span id="totalAmount">{{ number_format($order->final_total, 0, ',', '.') }}₫</span></strong>
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
                            <label class="form-label">Mã đơn</label>
                            <input type="text" class="form-control" value="{{ $order->order_code }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <input type="text" class="form-control" 
                                   value="{{ $order->status_meta['label'] }}" readonly>
                    </div>
                        <div class="mb-3">
                            <label class="form-label">Ngày đặt</label>
                            <input type="text" class="form-control" 
                                   value="{{ $order->created_at->format('d/m/Y H:i') }}" readonly>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-save"></i> Lưu thay đổi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let productIndex = {{ $order->items->count() }};

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
        document.getElementById('totalAmount').textContent = total.toLocaleString('vi-VN') + '₫';
}

    // Initialize
    attachEventListeners();
    calculateTotal();
</script>
@endpush
@endsection
