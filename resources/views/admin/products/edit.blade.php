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
            <div class="col-12">



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

                            @php
                                $hasOrders = $product->hasOrders();
                            @endphp

                            @if ($hasOrders)
                                <div class="alert alert-warning mb-3">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    <strong>Lưu ý:</strong> Sản phẩm này đã có đơn hàng. Một số thông tin không thể thay đổi
                                    để đảm bảo tính nhất quán của đơn hàng.
                                </div>
                            @endif

                            {{-- Tên sản phẩm --}}
                            <div class="mb-3">
                                <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $product->name) }}"
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Slug (để trống sẽ tự tạo)</label>
                                    <input type="text" name="slug" class="form-control"
                                        value="{{ old('slug', $product->slug) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Giá (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="text" name="price" class="form-control price-input"
                                        value="{{ old('price', number_format($product->price, 0, ',', '.')) }}"
                                        required>
                                </div>
                            </div>

                            {{-- Danh mục --}}
                            <div class="mt-3">
                                <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select"
                                    {{ $hasOrders ? 'disabled' : 'required' }}>
                                    @foreach ($categories as $c)
                                        <option value="{{ $c->id }}"
                                            {{ old('category_id', $product->category_id) == $c->id ? 'selected' : '' }}>
                                            {{ $c->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($hasOrders)
                                    <input type="hidden" name="category_id" value="{{ $product->category_id }}">
                                    <small class="text-muted">Không thể thay đổi khi sản phẩm đã có đơn hàng</small>
                                @endif
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
                                    <input type="file" name="image" accept="image/*" class="form-control"
                                        {{ $hasOrders ? 'disabled' : '' }}>
                                    <div class="form-text">Hỗ trợ: jpg, jpeg, png, webp (≤ 4MB)</div>
                                    @if ($hasOrders)
                                        <small class="text-muted">Không thể thay đổi ảnh khi sản phẩm đã có đơn hàng</small>
                                    @endif
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
                                <input type="file" name="images[]" accept="image/*" class="form-control" multiple
                                    {{ $hasOrders ? 'disabled' : '' }}>
                                <div class="form-text">Chọn nhiều ảnh cùng lúc. Hỗ trợ: jpg, jpeg, png, webp (≤ 4MB)</div>
                                @if ($hasOrders)
                                    <small class="text-muted">Không thể thay đổi ảnh phụ khi sản phẩm đã có đơn hàng</small>
                                @endif
                            </div>

                            {{-- Hiển thị ảnh phụ hiện tại --}}
                            @if ($product->images->count())
                                <div class="mt-2 d-flex flex-wrap">
                                    @foreach ($product->images as $img)
                                        <div class="img-container" id="img-{{ $img->id }}">
                                            <img src="{{ asset('storage/' . $img->image) }}" class="img-preview rounded"
                                                alt="Ảnh phụ"
                                                style="width:100%; max-width:250px; height:200px; object-fit:cover; border:1px solid #e9ecef; border-radius:8px; display:block;">

                                            <button type="button" class="btn btn-sm btn-danger btn-remove-img"
                                                data-id="{{ $img->id }}">
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
                                @foreach ($product->variants as $idx => $v)
                                    <div class="variant-item border rounded p-3 mb-2">

                                        <input type="hidden" name="variants[{{ $idx }}][id]"
                                            value="{{ $v->id }}">

                                        <div class="row g-3">
                                            @php
                                                $variantHasOrders = $v->hasOrders();
                                            @endphp
                                            <div class="col-md-3">
                                                <label class="form-label">Tên màu <span class="text-danger">*</span></label>
                                                <input name="variants[{{ $idx }}][color_name]"
                                                    class="form-control"
                                                    required
                                                    value="{{ old('variants.' . $idx . '.color_name', $v->color_name) }}"
                                                    {{ $hasOrders && $variantHasOrders ? 'readonly' : '' }}>
                                                @if ($hasOrders && $variantHasOrders)
                                                    <small class="text-muted">Đã có đơn hàng</small>
                                                @endif
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label">Mã màu <span class="text-danger">*</span></label>
                                                <input type="color" name="variants[{{ $idx }}][color_code]"
                                                    class="form-control form-control-color"
                                                    required
                                                    value="{{ old('variants.' . $idx . '.color_code', $v->color_code) }}"
                                                    {{ $hasOrders && $variantHasOrders ? 'disabled' : '' }}>
                                                @if ($hasOrders && $variantHasOrders)
                                                    <input type="hidden"
                                                        name="variants[{{ $idx }}][color_code]"
                                                        value="{{ $v->color_code }}">
                                                @endif
                                            </div>

                                            <div class="col-md-2">
                                                <label>Dài (cm)</label>
                                                <input type="number" step="1"
                                                    name="variants[{{ $idx }}][length]" class="form-control"
                                                    value="{{ old('variants.' . $idx . '.length', (float)$v->length) }}"
                                                    {{ $hasOrders && $variantHasOrders ? 'readonly' : '' }}>
                                            </div>

                                            <div class="col-md-2">
                                                <label>Rộng (cm)</label>
                                                <input type="number" step="10"
                                                    name="variants[{{ $idx }}][width]" class="form-control"
                                                    value="{{ old('variants.' . $idx . '.width', (float)$v->width) }}"
                                                    {{ $hasOrders && $variantHasOrders ? 'readonly' : '' }}>
                                            </div>

                                            <div class="col-md-2">
                                                <label>Cao (cm)</label>
                                                <input type="number" step="10"
                                                    name="variants[{{ $idx }}][height]" class="form-control"
                                                    value="{{ old('variants.' . $idx . '.height', (float)$v->height) }}"
                                                    placeholder="VD: 0.8"
                                                    {{ $hasOrders && $variantHasOrders ? 'readonly' : '' }}>

                                            </div>
                                        </div>
                                        <div class="row g-3 mt-2">
                                            <div class="col-md-2">
                                                <label>Cân nặng (kg)</label>
                                                <input type="number" step="1"
                                                    name="variants[{{ $idx }}][weight]" class="form-control"
                                                    value="{{ old('variants.' . $idx . '.weight', (float)$v->weight) }}"
                                                    {{ $hasOrders && $variantHasOrders ? 'readonly' : '' }}>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Tồn</label>  
                                                <input type="number" name="variants[{{ $idx }}][stock]"
                                                    class="form-control"
                                                    value="{{ old('variants.' . $idx . '.stock', $v->stock) }}">
                                            </div>

                                            <div class="col-md-2 mt-3">
                                                <label>Giá (VNĐ)</label>
                                                <input type="text"
                                                    name="variants[{{ $idx }}][price]" class="form-control price-input"
                                                    value="{{ old('variants.' . $idx . '.price', number_format($v->price ?? $product->price, 0, ',', '.')) }}"
                                                    placeholder="Giá biến thể">
                                                <small class="text-muted">Để trống = giá SP</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- DIV CHỨA BIẾN THỂ MỚI --}}
                            <div id="new-variants"></div>

                            {{-- Nút thêm biến thể --}}
                            <button type="button" id="addNewVariant" class="btn btn-primary mt-2">
                                + Thêm biến thể
                            </button>
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
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to format price with dots
            function formatPrice(input) {
                // Remove existing dots and non-digits
                let value = input.value.replace(/\./g, '').replace(/\D/g, '');
                
                if (value === '') {
                    input.value = '';
                    return;
                }
                
                // Format with dots
                input.value = new Intl.NumberFormat('vi-VN').format(value);
            }

            // Attach event listener to existing inputs
            document.querySelectorAll('.price-input').forEach(input => {
                input.addEventListener('input', function() {
                    formatPrice(this);
                });
            });

            // Handle dynamically added inputs (if any)
            document.body.addEventListener('input', function(e) {
                if (e.target.classList.contains('price-input')) {
                    formatPrice(e.target);
                }
            });

            let newVariantIndex = 0;

            document.getElementById("addNewVariant").addEventListener("click", function() {

                let html = `
                <div class="new-variant-item border rounded p-3 mb-2">

                    <div class="row g-3">
                        
                        <div class="col-md-3">
                            <label class="form-label">Tên màu <span class="text-danger">*</span></label>
                            <input name="variants[new_${newVariantIndex}][color_name]" class="form-control" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Mã màu <span class="text-danger">*</span></label>
                            <input type="color" name="variants[new_${newVariantIndex}][color_code]"
                                class="form-control form-control-color" required>
                        </div>

                        <div class="col-md-2">
                            <label>Dài (mm)</label>
                            <input type="number" step="0.01"
                                name="variants[new_${newVariantIndex}][length]" class="form-control" placeholder="VD: 2.5">
                        </div>

                        <div class="col-md-2">
                            <label>Rộng (mm)</label>
                            <input type="number" step="0.01"
                                name="variants[new_${newVariantIndex}][width]" class="form-control" placeholder="VD: 1.8">
                        </div>

                        <div class="col-md-2">
                            <label>Cao (mm)</label>
                            <input type="number" step="0.01"
                                name="variants[new_${newVariantIndex}][height]" class="form-control" placeholder="VD: 0.8">
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-3">
                            <label>Cân nặng (kg)</label>
                            <input type="number" step="0.01"
                                name="variants[new_${newVariantIndex}][weight]" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Tồn</label>
                            <input type="number" 
                                name="variants[new_${newVariantIndex}][stock]" class="form-control">
                        </div>
                        <div class="col-md-2 mt-3">
                            <label>Giá (VNĐ)</label>
                            <input type="text"
                                name="variants[new_${newVariantIndex}][price]" class="form-control price-input" placeholder="Giá biến thể">
                            <small class="text-muted">Để trống = giá SP</small>
                        </div>
                    </div>
                </div>`;

                document.getElementById("new-variants").insertAdjacentHTML("beforeend", html);
                newVariantIndex++;
            });
        });
    </script>
@endpush
