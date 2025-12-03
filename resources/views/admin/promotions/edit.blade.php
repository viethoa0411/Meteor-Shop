@extends('admin.layouts.app')
@section('title', 'Sửa khuyến mãi')

@section('content')
<div class="container-fluid py-4">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.promotions.list') }}" class="btn btn-secondary">← Danh sách</a>

    <div class="card mt-3">
        <div class="card-header"><h4 class="mb-0">Sửa khuyến mãi: {{ $promotion->code }}</h4></div>
        <div class="card-body">
            <form action="{{ route('admin.promotions.update', $promotion->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Mã khuyến mãi *</label>
                        <input type="text" name="code" class="form-control" value="{{ old('code', $promotion->code) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tên *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $promotion->name) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Trạng thái *</label>
                        <select name="status" class="form-select" required>
                            <option value="active" {{ old('status', $promotion->status)==='active'?'selected':'' }}>Hoạt động</option>
                            <option value="inactive" {{ old('status', $promotion->status)==='inactive'?'selected':'' }}>Tạm ẩn</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-4">
                        <label class="form-label">Kiểu giảm *</label>
                        <select name="discount_type" class="form-select" required>
                            <option value="percent" {{ old('discount_type', $promotion->discount_type)==='percent'?'selected':'' }}>Phần trăm</option>
                            <option value="fixed" {{ old('discount_type', $promotion->discount_type)==='fixed'?'selected':'' }}>Số tiền</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Giá trị giảm *</label>
                        <input type="number" step="0.01" name="discount_value" class="form-control" value="{{ old('discount_value', $promotion->discount_value) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Giới hạn giảm tối đa (nếu %) </label>
                        <input type="number" step="0.01" name="max_discount" class="form-control" value="{{ old('max_discount', $promotion->max_discount) }}">
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-4">
                        <label class="form-label">Giá trị đơn tối thiểu</label>
                        <input type="number" step="0.01" name="min_amount" class="form-control" value="{{ old('min_amount', $promotion->min_amount) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Số lần mua tối thiểu (2+)</label>
                        <input type="number" name="min_orders" class="form-control" value="{{ old('min_orders', $promotion->min_orders) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Giới hạn dùng mỗi khách</label>
                        <input type="number" name="limit_per_user" class="form-control" value="{{ old('limit_per_user', $promotion->limit_per_user) }}">
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-4">
                        <label class="form-label">Giới hạn tổng hệ thống</label>
                        <input type="number" name="limit_global" class="form-control" value="{{ old('limit_global', $promotion->limit_global) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Giới hạn tổng (legacy)</label>
                        <input type="number" name="usage_limit" class="form-control" value="{{ old('usage_limit', $promotion->usage_limit) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Phạm vi áp dụng *</label>
                        <select name="scope" class="form-select" required>
                            <option value="all" {{ old('scope', $promotion->scope)==='all'?'selected':'' }}>Toàn bộ</option>
                            <option value="category" {{ old('scope', $promotion->scope)==='category'?'selected':'' }}>Theo danh mục</option>
                            <option value="product" {{ old('scope', $promotion->scope)==='product'?'selected':'' }}>Theo sản phẩm</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label">Thời gian bắt đầu *</label>
                        <input type="datetime-local" name="start_date" class="form-control" value="{{ old('start_date', $promotion->start_date ? $promotion->start_date->format('Y-m-d\TH:i') : '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Thời gian kết thúc *</label>
                        <input type="datetime-local" name="end_date" class="form-control" value="{{ old('end_date', $promotion->end_date ? $promotion->end_date->format('Y-m-d\TH:i') : '') }}" required>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description" rows="3" class="form-control">{{ old('description', $promotion->description) }}</textarea>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label">Danh mục áp dụng</label>
                        <select name="category_ids[]" class="form-select" multiple>
                            @foreach ($categories as $c)
                                <option value="{{ $c->id }}" {{ in_array($c->id, old('category_ids', $selectedCategoryIds)) ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Chỉ dùng khi chọn phạm vi "Theo danh mục"</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Sản phẩm áp dụng</label>
                        <select name="product_ids[]" class="form-select" multiple>
                            @foreach ($products as $p)
                                <option value="{{ $p->id }}" {{ in_array($p->id, old('product_ids', $selectedProductIds)) ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Chỉ dùng khi chọn phạm vi "Theo sản phẩm"</div>
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
@endsection

