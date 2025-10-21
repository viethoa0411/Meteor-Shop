@extends('admin.layouts.app')

@section('title', 'Sửa sản phẩm')

@push('styles')
    <style>
        .img-preview {
            width: 200px; height: 200px; object-fit: cover;
            border: 1px solid #e9ecef; border-radius: .5rem;
    }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 col-lg-10 mx-auto">

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
                        <h4 class="mb-0">Sửa sản phẩm: <span class="text-primary">{{ $product->name }}</span></h4>
                    </div>

                    <div class="card-body">
                        <form id="productUpdate" action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- Tên --}}
                            <div class="mb-3">
                                <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $product->name) }}" required>
                            </div>

                            <div class="row g-3">
                                {{-- Slug --}}
                                <div class="col-md-6">
                                    <label class="form-label">Slug (để trống sẽ tự tạo)</label>
                                    <input type="text" name="slug" class="form-control"
                                        value="{{ old('slug', $product->slug) }}">
                                </div>

                                {{-- Giá --}}
                                <div class="col-md-3">
                                    <label class="form-label">Giá (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="price" class="form-control"
                                        value="{{ old('price', $product->price) }}" required>
                                </div>

                                {{-- Tồn kho --}}
                                <div class="col-md-3">
                                    <label class="form-label">Tồn kho <span class="text-danger">*</span></label>
                                    <input type="number" name="stock" class="form-control"
                                        value="{{ old('stock', $product->stock) }}" required>
                                </div>
                            </div>

                            <div class="row g-3 mt-1">
                                {{-- Danh mục --}}
                                <div class="col-md-6">
                                    <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-select" required>
                                    @foreach ($categories as $c)
                                        <option value="{{ $c->id }}"
                                        {{ (int)old('category_id', $product->category_id) === (int)$c->id ? 'selected' : '' }}>
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
                                        <option value="{{ $b->id }}"
                                        {{ (int)old('brand_id', $product->brand_id) === (int)$b->id ? 'selected' : '' }}>
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
                                    <option value="active"   {{ old('status', $product->status)==='active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $product->status)==='inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            {{-- Mô tả --}}
                            <div class="mb-3">
                                <label class="form-label">Mô tả</label>
                                <textarea name="description" rows="5" class="form-control">{{ old('description', $product->description) }}</textarea>
                            </div>

                            {{-- Ảnh đại diện --}}
                            <div class="row g-3 align-items-begin">
                                <div class="col-12 col-lg-7">
                                    <label class="form-label">Ảnh đại diện</label>
                                    <input type="file" name="image" accept="image/*" class="form-control">
                                    <div class="form-text">Hỗ trợ: jpg, jpeg, png, webp (≤ 4MB)</div>
                                </div>
                                <div class="col-12 col-lg-5">
                                    <label class="form-label d-block">Ảnh hiện tại</label>
                                    @if ($product->image)
                                    <img
                                        src="{{ asset('storage/'.$product->image) }}"
                                        alt="image"
                                        style="width:100%; max-width:250px; height:200px; object-fit:cover; border:1px solid #e9ecef; border-radius:8px; display:block;"
                                    >
                                    @else
                                    <div class="text-secondary">— Chưa có ảnh —</div>
                                    @endif
                                        </div>
                                    </div>
                                    {{-- end Ảnh đại diện --}}
                                    <div></div>
                                    <br>
                           
                               
                        </form>
                    </div>
                </div>
                <div class="d-flex mt-3">
                    <div class="ms-auto">
                        <a href="{{ route('admin.products.list') }}" class="btn btn-secondary me-2">Quay lại</a>
                        {{-- <button type="submit" form="productUpdate" class="btn btn-primary">Cập nhật</button> --}}
                    <button type="submit" form="productUpdate" class="btn btn-primary">Cập nhật</button>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
