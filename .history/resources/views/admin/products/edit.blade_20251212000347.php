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
                            @if($product->images->count())
                                <div class="mt-2 d-flex flex-wrap">
                                    @foreach($product->images as $img)
                                    <div class="img-container" id="img-{{ $img->id }}">
                                    <img src="{{ asset('storage/' . $img->image) }}"
                                                                class="img-preview rounded" alt="Ảnh phụ"
                                                                style="width:100%; max-width:250px; height:200px; object-fit:cover; border:1px solid #e9ecef; border-radius:8px; display:block;">
                                                            
                                        <button type="button" class="btn btn-sm btn-danger btn-remove-img" data-id="{{ $img->id }}">
                                            &times;
                                        </button>
                                    </div>
                                    @endforeach
                                </div>
                            @endif              

                            {{-- ====================== BIẾN THỂ ====================== --}}
                            <hr>
                            <h4>Biến thể sản phẩm</h4>

                              {{-- Biến thể cũ --}}
                            <div id="existing-variants">
                                @foreach($product->variants as $idx => $v)
                                    <div class="variant-item border rounded p-3 mb-2">

                                        <input type="hidden" name="variants[{{ $idx }}][id]" value="{{ $v->id }}">

                                        <div class="row g-3">

                                            <div class="col-md-3">
                                                <label class="form-label">Tên màu</label>
                                                <input name="variants[{{ $idx }}][color_name]" 
                                                        class="form-control"
                                                        value="{{ old('variants.'.$idx.'.color_name', $v->color_name) }}">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label">Mã màu</label>
                                                <input type="color" name="variants[{{ $idx }}][color_code]"
                                                        class="form-control form-control-color" 
                                                        value="{{ old('variants.'.$idx.'.color_code', $v->color_code) }}">

                                            </div>

                                            <div class="col-md-2">
                                                <label>Dài</label>
                                                <input type="number" step="0.01" name="variants[{{ $idx }}][length]"
                                                        class="form-control" 
                                                        value="{{ old('variants.'.$idx.'.length', $v->length) }}">

                                            </div>

                                            <div class="col-md-2">
                                                <label>Rộng</label>
                                                <input type="number" step="0.01" name="variants[{{ $idx }}][width]"
                                                        class="form-control" 
                                                        value="{{ old('variants.'.$idx.'.width', $v->width) }}">

                                            </div>

                                            <div class="col-md-2">
                                                <label>Cao</label>
                                                <input type="number" step="0.01" name="variants[{{ $idx }}][height]"
                                                        class="form-control" 
                                                        value="{{ old('variants.'.$idx.'.height', $v->height) }}">

                                            </div>

                                            <div class="col-md-2 mt-3">
                                                <label>Tồn</label>
                                                <input type="number" 
                                                        name="variants[{{ $idx }}][stock]" class="form-control"
                                                       value="{{ old('variants.'.$idx.'.stock', $v->stock) }}">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- DIV CHỨA BIẾN THỂ MỚI --}}
                            <div id="new-variants"></div>

                            {{-- Nút thêm biến thể --}}
                            <button type="button" id="addNewVariant" class="btn btn-primary mt-2">+ Thêm biến thể</button>

                            <hr>



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

    <script>
    (function() {
        'use strict';
        
        function initVariantHandler() {
            let newVariantIndex = 0;
            const addNewVariantBtn = document.getElementById("addNewVariant");
            const newVariantsContainer = document.getElementById("new-variants");

            if (!addNewVariantBtn) {
                console.error('Button addNewVariant not found!');
                return;
            }

            if (!newVariantsContainer) {
                console.error('Container new-variants not found!');
                return;
            }

            console.log('Variant handler initialized');

            // Xử lý click nút thêm biến thể
            addNewVariantBtn.addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Add variant button clicked');
                
                if (this.disabled) {
                    alert('Không thể thêm biến thể mới khi sản phẩm đã có đơn hàng');
                    return false;
                }

                const variantIndex = newVariantIndex;
                const html = `
                    <div class="new-variant-item border rounded p-3 mb-2 position-relative" data-variant-index="${variantIndex}">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 remove-new-variant" 
                                style="z-index: 10;" title="Xóa biến thể này">
                            <i class="bi bi-x"></i>
                        </button>

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Tên màu <span class="text-danger">*</span></label>
                                <input name="variants[new_${variantIndex}][color_name]" class="form-control" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Mã màu <span class="text-danger">*</span></label>
                                <input type="color" name="variants[new_${variantIndex}][color_code]"
                                    class="form-control form-control-color" value="#000000" required>
                            </div>

                            <div class="col-md-2">
                                <label>Dài (cm) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0"
                                    name="variants[new_${variantIndex}][length]" class="form-control" required>
                            </div>

                            <div class="col-md-2">
                                <label>Rộng (cm) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0"
                                    name="variants[new_${variantIndex}][width]" class="form-control" required>
                            </div>

                            <div class="col-md-2">
                                <label>Cao (cm) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0"
                                    name="variants[new_${variantIndex}][height]" class="form-control" required>
                            </div>

                            <div class="col-md-2 mt-3">
                                <label>Tồn kho <span class="text-danger">*</span></label>
                                <input type="number" min="0"
                                    name="variants[new_${variantIndex}][stock]" class="form-control" value="0" required>
                            </div>

                            <div class="col-md-2 mt-3">
                                <label>Giá (VNĐ)</label>
                                <input type="number" step="0.01" min="0"
                                    name="variants[new_${variantIndex}][price]" class="form-control" placeholder="Giá biến thể">
                                <small class="text-muted">Để trống = giá SP</small>
                            </div>

                            <div class="col-md-3 mt-3">
                                <label>Cân nặng <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0"
                                    name="variants[new_${variantIndex}][weight]" class="form-control" placeholder="0.00" required>
                            </div>

                            <div class="col-md-2 mt-3">
                                <label>Đơn vị <span class="text-danger">*</span></label>
                                <select name="variants[new_${variantIndex}][weight_unit]" class="form-select" required>
                                    <option value="kg" selected>kg</option>
                                    <option value="g">g</option>
                                    <option value="lb">lb</option>
                                </select>
                            </div>
                        </div>
                    </div>`;

                newVariantsContainer.insertAdjacentHTML("beforeend", html);
                console.log('Variant added with index:', variantIndex);
                
                newVariantIndex++;
                return false;
            });

            // Xử lý xóa biến thể mới (event delegation)
            document.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-new-variant');
                if (removeBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const variantItem = removeBtn.closest('.new-variant-item');
                    if (variantItem) {
                        if (confirm('Bạn có chắc muốn xóa biến thể này?')) {
                            variantItem.remove();
                            console.log('Variant removed');
                        }
                    }
                }
            });
        }
        
        // Khởi tạo khi DOM ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initVariantHandler);
        } else {
            initVariantHandler();
        }
    })();
    </script>
@endsection
