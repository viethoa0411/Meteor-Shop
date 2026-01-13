@extends('admin.layouts.app')
@section('title', 'Thêm khuyến mãi')

@section('content')
<div class="container-fluid py-4">
    <div id="client-error-alert" class="alert alert-danger" style="display: none;">
        <ul class="mb-0" id="client-error-list"></ul>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <a href="{{ route('admin.promotions.list') }}" class="btn btn-secondary">← Danh sách</a>

    <div class="card mt-3">
        <div class="card-header"><h4 class="mb-0">Thêm khuyến mãi</h4></div>
        <div class="card-body">
            <form action="{{ route('admin.promotions.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Mã khuyến mãi *</label>
                        <input type="text" name="code" class="form-control" value="{{ old('code') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tên *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Trạng thái *</label>
                        <select name="status" class="form-select" required>
                            <option value="active" {{ old('status')==='active'?'selected':'' }}>Hoạt động</option>
                            <option value="inactive" {{ old('status')==='inactive'?'selected':'' }}>Tạm ẩn</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-4">
                        <label class="form-label">Kiểu giảm *</label>
                        <select name="discount_type" class="form-select" required>
                            <option value="percent" {{ old('discount_type')==='percent'?'selected':'' }}>Phần trăm</option>
                            <option value="fixed" {{ old('discount_type')==='fixed'?'selected':'' }}>Số tiền</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Giá trị giảm % hoặc số tiền</label>
                        <input type="number" step="0.01" max="10000000" name="discount_value" class="form-control" value="{{ old('discount_value') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Giới hạn giảm tối đa (nếu %) </label>
                        <input type="number" step="0.01" max="10000000" name="max_discount" class="form-control" value="{{ old('max_discount') }}">
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-4">
                        <label class="form-label">Giá trị đơn tối thiểu</label>
                        <input type="number" step="0.01" name="min_amount" class="form-control" value="{{ old('min_amount', 0) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Số lần mua tối thiểu (2+)</label>
                        <input type="number" name="min_orders" class="form-control" value="{{ old('min_orders', 0) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Giới hạn dùng mỗi khách</label>
                        <input type="number" name="limit_per_user" class="form-control" value="{{ old('limit_per_user') }}">
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-4">
                        <label class="form-label">Giới hạn tổng hệ thống</label>
                        <input type="number" name="limit_global" class="form-control" value="{{ old('limit_global') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Phạm vi áp dụng *</label>
                        <select name="scope" class="form-select" required>
                            <option value="all" {{ old('scope')==='all'?'selected':'' }}>Toàn bộ</option>
                            <option value="category" {{ old('scope')==='category'?'selected':'' }}>Theo danh mục</option>
                            <option value="product" {{ old('scope')==='product'?'selected':'' }}>Theo sản phẩm</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label">Thời gian bắt đầu *</label>
                        <input type="datetime-local" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Thời gian kết thúc *</label>
                        <input type="datetime-local" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description" rows="3" class="form-control">{{ old('description') }}</textarea>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-12" id="category_container" style="display: none;">
                        <label class="form-label">Danh mục áp dụng</label>
                        <select name="category_ids[]" class="form-select" multiple style="height: 200px;">
                            @foreach ($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Giữ Ctrl (Windows) hoặc Command (Mac) để chọn nhiều</div>
                    </div>
                    <div class="col-12" id="product_container" style="display: none;">
                        <label class="form-label">Sản phẩm áp dụng</label>
                        <select name="product_ids[]" class="form-select" multiple style="height: 200px;">
                            @foreach ($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Giữ Ctrl (Windows) hoặc Command (Mac) để chọn nhiều</div>
                    </div>
                </div>

                <div class="d-flex mt-3">
                    <div class="ms-auto">
                        <a href="{{ route('admin.promotions.list') }}" class="btn btn-danger me-2">Hủy</a>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
 </div>

 <script>
    document.addEventListener('DOMContentLoaded', function() {
        const scopeSelect = document.querySelector('select[name="scope"]');
        const categoryContainer = document.getElementById('category_container');
        const productContainer = document.getElementById('product_container');

        // Validation for Discount Value
        const discountTypeSelect = document.querySelector('select[name="discount_type"]');
        const discountValueInput = document.querySelector('input[name="discount_value"]');
        const maxDiscountInput = document.querySelector('input[name="max_discount"]');
        const clientErrorAlert = document.getElementById('client-error-alert');
        const clientErrorList = document.getElementById('client-error-list');

        function showClientError(message) {
            clientErrorList.innerHTML = `<li>${message}</li>`;
            clientErrorAlert.style.display = 'block';
            // Scroll to top to see error
            // window.scrollTo({ top: 0, behavior: 'smooth' }); // Optional
        }

        function hideClientError() {
            clientErrorAlert.style.display = 'none';
            clientErrorList.innerHTML = '';
        }

        function validateDiscountValue() {
            const type = discountTypeSelect.value;
            
            // Update max attribute based on type
            if (type === 'percent') {
                discountValueInput.max = 100;
            } else {
                discountValueInput.max = 10000000;
            }

            let value = parseFloat(discountValueInput.value);

            // Hide error initially
            hideClientError();

            if (isNaN(value)) return;

            if (type === 'percent') {
                if (value > 100) {
                    showClientError('Giảm giá phần trăm tối đa là 100%');
                    discountValueInput.value = 100;
                }
            } else if (type === 'fixed') {
                if (value > 10000000) {
                    showClientError('Giảm giá số tiền tối đa là 10.000.000đ');
                    discountValueInput.value = 10000000;
                }
            }
        }
        
        if (maxDiscountInput) {
            maxDiscountInput.addEventListener('input', function() {
                let val = parseFloat(this.value);
                if (val > 10000000) {
                    showClientError('Giới hạn giảm tối đa là 10.000.000đ');
                    this.value = 10000000;
                }
            });
        }

        discountTypeSelect.addEventListener('change', validateDiscountValue);
        discountValueInput.addEventListener('input', validateDiscountValue);
        
        validateDiscountValue(); // Init

        // Form Submission Validation
        const form = document.querySelector('form');
        const categorySelect = document.querySelector('select[name="category_ids[]"]');
        const productSelect = document.querySelector('select[name="product_ids[]"]');

        form.addEventListener('submit', function(e) {
            const scope = scopeSelect.value;
            let isValid = true;
            let errorMessage = '';

            if (scope === 'category') {
                const selectedCategories = Array.from(categorySelect.selectedOptions);
                if (selectedCategories.length === 0) {
                    isValid = false;
                    errorMessage = 'Vui lòng chọn ít nhất một danh mục.';
                }
            } else if (scope === 'product') {
                const selectedProducts = Array.from(productSelect.selectedOptions);
                if (selectedProducts.length === 0) {
                    isValid = false;
                    errorMessage = 'Vui lòng chọn ít nhất một sản phẩm.';
                }
            }

            if (!isValid) {
                e.preventDefault();
                showClientError(errorMessage);
            }
        });

        function toggleScope() {
            const scope = scopeSelect.value;
            if (scope === 'category') {
                categoryContainer.style.display = 'block';
                productContainer.style.display = 'none';
            } else if (scope === 'product') {
                categoryContainer.style.display = 'none';
                productContainer.style.display = 'block';
            } else {
                categoryContainer.style.display = 'none';
                productContainer.style.display = 'none';
            }
        }

        scopeSelect.addEventListener('change', toggleScope);
        toggleScope(); // Chạy lần đầu khi trang tải
    });
 </script>
@endsection
