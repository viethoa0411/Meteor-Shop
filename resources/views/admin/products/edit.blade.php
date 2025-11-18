@extends('admin.layouts.app')

@section('title', 'Sửa sản phẩm')

@push('styles')
    <style>
        .img-preview {
            width: 100%;
            max-width: 150px;
            height: 150px;
            object-fit: cover;
            border: 1px solid #e9ecef;
            border-radius: .5rem;
            display: block;
        }

        .img-container {
            position: relative;
            display: inline-block;
            margin-right: .5rem;
            margin-bottom: .5rem;
        }

        .img-container button {
            position: absolute;
            top: 2px;
            right: 2px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 col-lg-10 mx-auto">

                {{-- Thông báo --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
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

                <a href="{{ route('admin.products.list') }}" class="btn btn-secondary mb-3">← Danh sách</a>

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Sửa sản phẩm: <span class="text-primary">{{ $product->name }}</span></h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.products.update', $product->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- Tên sản phẩm --}}
                            <div class="mb-3">
                                <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $product->name) }}" required>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Slug (để trống sẽ tự tạo)</label>
                                    <input type="text" name="slug" class="form-control"
                                        value="{{ old('slug', $product->slug) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Giá (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="number" name="price" step="0.01" class="form-control"
                                        value="{{ old('price', $product->price) }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tồn kho <span class="text-danger">*</span></label>
                                    <input type="number" name="stock" class="form-control"
                                        value="{{ old('stock', $product->stock) }}" required>
                                </div>
                            </div>

                            {{-- Danh mục --}}
                            <div class="mt-3">
                                <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select" required>
                                    @foreach ($categories as $c)
                                        <option value="{{ $c->id }}"
                                            {{ old('category_id', $product->category_id) == $c->id ? 'selected' : '' }}>
                                            {{ $c->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Trạng thái --}}
                            <div class="mt-3">
                                <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="active" {{ $product->status === 'active' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="inactive" {{ $product->status === 'inactive' ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                            </div>

                            {{-- Mô tả --}}
                            <div class="mb-3 mt-3">
                                <label class="form-label">Mô tả</label>
                                <textarea name="description" rows="5" class="form-control">{{ old('description', $product->description) }}</textarea>
                            </div>

                            {{-- Ảnh đại diện --}}
                            <div class="row g-3 mb-3">
                                <div class="col-lg-7">
                                    <label class="form-label">Ảnh đại diện</label>
                                    <input type="file" name="image" accept="image/*" class="form-control">
                                    <div class="form-text">Hỗ trợ: jpg, jpeg, png, webp (≤ 4MB)</div>
                                </div>
                                <div class="col-lg-5">
                                    <label class="form-label d-block">Ảnh hiện tại</label>
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" class="img-preview rounded"
                                            alt="Ảnh đại diện"
                                            style="width:100%; max-width:150px; height:150px; object-fit:cover; border:1px solid #e9ecef; border-radius:8px; display:block;">
                                    @else
                                        <div class="text-secondary">— Chưa có ảnh —</div>
                                    @endif
                                </div>
                            </div>

                            {{-- Ảnh phụ --}}
                            <div class="mb-3">
                                <label class="form-label">Ảnh phụ</label>
                                <input type="file" name="images[]" accept="image/*" class="form-control" multiple>
                                <div class="form-text">Chọn nhiều ảnh cùng lúc. Hỗ trợ: jpg, jpeg, png, webp (≤ 4MB)</div>
                            </div>

                            {{-- Hiển thị ảnh phụ hiện tại --}}
                            @if ($product->images->count())
                                <div class="mt-2">
                                    <label class="form-label d-block">Ảnh phụ hiện tại</label>
                                    <div class="d-flex flex-wrap" id="images-list">
                                        @foreach ($product->images as $img)
                                            <div class="img-container" id="img-{{ $img->id }}">
                                                <img src="{{ asset('storage/' . $img->image) }}"
                                                    class="img-preview rounded" alt="Ảnh phụ"
                                                    style="width:100%; max-width:250px; height:200px; object-fit:cover; border:1px solid #e9ecef; border-radius:8px; display:block;">
                                                <button type="button" class="btn btn-sm btn-danger btn-remove-img"
                                                    data-id="{{ $img->id }}">&times;</button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Nút lưu --}}
                            <div class="d-flex mt-4">
                                <div class="ms-auto">
                                    <a href="{{ route('admin.products.list') }}" class="btn btn-secondary me-2">Quay
                                        lại</a>
                                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.btn-remove-img').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Xóa ảnh này?')) {
                    let imgId = this.dataset.id;
                    fetch("{{ route('admin.products.images.destroy', ['product' => $product->id, 'image' => 'IMAGE_ID']) }}"
                            .replace('IMAGE_ID', imgId), {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }

                            })

                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                document.getElementById('img-' + imgId).remove();
                            } else {
                                alert('Xóa thất bại!');
                            }
                        });
                }
            });
        });
    </script>
@endpush

