@extends('admin.layouts.app')

@section('title', 'Sửa sản phẩm')

@push('styles')
    <style>
        .img-preview {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border: 1px solid #e9ecef;
            border-radius: .5rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 col-lg-10 mx-auto">
                {{-- Hiển thị thông báo --}} @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
                    </div>
                    @if (session('success'))
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
                            <h4 class="mb-0">
                                Sửa sản phẩm:
                                <span class="text-primary">{{ $product->name }}</span>
                            </h4>
                        </div>

                        <div class="card-body">
                            <form id="productUpdate" action="{{ route('admin.products.update', $product->id) }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                {{-- Tên sản phẩm --}}
                                <div class="mb-3">
                                    <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $product->name) }}" required>
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
                                        <form id="productUpdate" action="{{ route('admin.products.update', $product) }}" method="POST"
                                            enctype="multipart/form-data">
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
                            {{-- Biến thể --}} <h5>Biến thể đã lưu</h5>
                            <div class="row">
                                @foreach ($product->variants as $v)
                                    <div class="col-md-4 mb-3">
                                        <div class="border p-3 rounded">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span
                                                    style="width: 18px; height:18px; border:1px solid #000; background:{{ $v->color_code }}; display:inline-block"></span>
                                                <strong>{{ $v->color_name ?? 'Màu' }}</strong>
                                                <small class="text-muted">{{ $v->color_code }}</small>
                                            </div>
                                            <div>Kích thước: {{ $v->length }} × {{ $v->width }}
                                                × {{ $v->height }} cm </div>
                                            @if (!is_null($v->price))
                                                <div>Giá: {{ number_format($v->price, 0, ',', '.') }}₫
                                                </div>
                                            @endif
                                            <div>Tồn kho: {{ $v->stock }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="row g-3 mt-1">
                                {{-- Danh mục --}}
                                <div class="col-md-6">
                                    <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-select" required>
                                        @foreach ($categories as $c)
                                            <option value="{{ $c->id }}"
                                                {{ (int) old('category_id', $product->category_id) === (int) $c->id ? 'selected' : '' }}>
                                                {{ $c->name }}
                                            </option>
                                        @endforeach
                                        @foreach ($categories as $c)
                                            <option value="{{ $c->id }}"
                                                {{ (int) old('category_id', $product->category_id) === (int) $c->id ? 'selected' : '' }}>
                                                {{ $c->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Trạng thái --}}
                            <div class="mb-3 mt-3">
                                <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="active"
                                        {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>
                                        Active
                                        </option>
                                        <option value="inactive"
                                            {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>
                                            Inactive
                                        </option>
                                        <option value="active"
                                            {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="inactive"
                                            {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>
                                            Inactive</option>
                                </select>
                            </div>

                            {{-- Mô tả --}}
                            <div class="mb-3">
                                <label class="form-label">Mô tả</label>
                                <textarea name="description" rows="5" class="form-control">{{ old('description', $product->description) }}</textarea>
                            </div>
                            <div class="row g-3 align-items-begin">
                                <div class="col-12 col-lg-7">
                                    <label class="form-label">Ảnh đại diện</label>
                                    <input type="file" name="image" accept="image/*" class="form-control">
                                    <div class="form-text">Hỗ trợ: jpg, jpeg, png, webp (≤ 4MB)
                                    </div>
                                </div>
                                <div class="col-12 col-lg-5">
                                    <label class="form-label d-block">Ảnh hiện tại</label>
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="image"
                                            style="width:100%; max-width:250px; height:200px; object-fit:cover; border:1px solid #e9ecef; border-radius:8px; display:block;">
                                    @else
                                        <div class="text-secondary">— Chưa có ảnh —</div>
                                    @endif
                                </div>
                            </div>

                            {{-- Nút cập nhật --}}
                            <div class="d-flex mt-4">
                                <div class="ms-auto">
                                    <a href="{{ route('admin.products.list') }}" class="btn btn-secondary me-2">Quay
                                        lại</a>
                                    <button type="submit" class="btn btn-primary">Cập
                                        nhật</button>
                                </div>
                            </div>
                        </form>
                    </div> {{-- end card-body --}}
                </div> {{-- end card --}}
            </div>
        </div>
        </form>
    </div>
    </div>
    </div>
    </div>
    </div>
@endsection
