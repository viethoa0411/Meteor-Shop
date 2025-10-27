@extends('admin.layouts.app')

@section('title', 'Thêm sản phẩm')

@push('styles')
    <style>
        .img-preview {
            width: 100%; max-width: 420px; height: 200px;
            object-fit: cover; border: 1px solid #e9ecef; border-radius: .5rem; display: block;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10 col-lg-8 mx-auto">

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <a href="{{ route('admin.products.list') }}" class="btn btn-secondary">← Danh sách</a>

                <div class="card mt-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Thêm sản phẩm</h4>
                    </div>

                    <div class="card-body">
                        <form id="productCreateForm" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            {{-- Tên --}}
                            <div class="mb-3">
                                <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            </div>

                            <div class="row g-3">
                                {{-- Slug --}}
                                <div class="col-md-6">
                                    <label class="form-label">Slug (để trống sẽ tự tạo)</label>
                                    <input type="text" name="slug" class="form-control" value="{{ old('slug') }}">
                                </div>

                                {{-- Giá --}}
                                <div class="col-md-3">
                                    <label class="form-label">Giá (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="price" class="form-control"
                                        value="{{ old('price') }}" required>
                                </div>

                                {{-- Tồn kho --}}
                                <div class="col-md-3">
                                    <label class="form-label">Tồn kho <span class="text-danger">*</span></label>
                                    <input type="number" name="stock" class="form-control"
                                        value="{{ old('stock', 0) }}" required>
                                </div>
                            </div>

                            <div class="row g-3 mt-1">
                                {{-- Danh mục --}}
                                <div class="col-md-6">
                                    <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-select" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    @foreach ($categories as $c)
                                        <option value="{{ $c->id }}" {{ old('category_id')==$c->id ? 'selected' : '' }}>
                                        {{ $c->name }}
                                        </option>
                                    @endforeach
                                    </select>
                                </div>

                                {{-- Thương hiệu --}}
                                <div class="col-md-6">
                                    <label class="form-label">Thương hiệu</label>
                                    <select name="brand_id" class="form-select">
                                    <option value="">-- Không chọn --</option>
                                    @foreach ($brands as $b)
                                        <option value="{{ $b->id }}" {{ old('brand_id')==$b->id ? 'selected' : '' }}>
                                        {{ $b->name }}
                                        </option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Trạng thái --}}
                            <div class="mb-3 mt-3">
                                <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="active"   {{ old('status')==='active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status')==='inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            {{-- Mô tả --}}
                            <div class="mb-3">
                                <label class="form-label">Mô tả</label>
                                <textarea name="description" rows="5" class="form-control">{{ old('description') }}</textarea>
                            </div>

                            {{-- Ảnh đại diện + Preview (cùng 1 dòng 4:6) --}}
                            <div class="row g-3 align-items-end">
                                {{-- Upload (≈40%) --}}
                                <div class="col-12 col-lg-12">
                                    <label class="form-label">Ảnh đại diện</label>
                                    <input type="file" name="image" accept="image/*" class="form-control">
                                    <div class="form-text">Hỗ trợ: jpg, jpeg, png, webp (≤ 4MB)</div>
                                </div>
                            </div>

                          
                        </form>
                    </div>
                </div>
                <div class="d-flex mt-3">
                    <div class="ms-auto">
                        <a href="{{ route('admin.products.list') }}" class="btn btn-danger me-2">Hủy</a>
                        <button type="submit" form="productCreateForm" class="btn btn-primary">Lưu</button>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
