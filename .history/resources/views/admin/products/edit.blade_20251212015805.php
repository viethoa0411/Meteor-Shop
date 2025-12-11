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
                        
                            @if($hasOrders ?? false)
                                <div class="alert alert-warning mb-3">
                                    <i class="bi bi-exclamation-triangle"></i> 
                                    <strong>Lưu ý:</strong> Sản phẩm này đã có đơn hàng. Một số thông tin không thể thay đổi để đảm bảo tính nhất quán của đơn hàng.
                                </div>
                            @endif

                            {{-- Tên sản phẩm --}}
                            <div class="mb-3">
                                <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control"
                                         value="{{ old('name', $product->name) }}" 
                                    {{ ($hasOrders ?? false) ? 'readonly' : 'required' }}>
                                @if($hasOrders ?? false)
                                    <small class="text-muted">Không thể thay đổi khi sản phẩm đã có đơn hàng</small>
                                @endif
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
                                <select name="category_id" class="form-select" {{ ($hasOrders ?? false) ? 'disabled' : 'required' }}>
                                    @foreach ($categories as $c)
                                        <option value="{{ $c->id }}"
                                            {{ old('category_id', $product->category_id) == $c->id ? 'selected' : '' }}>
                                            {{ $c->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($hasOrders ?? false)
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
                                           {{ ($hasOrders ?? false) ? 'disabled' : '' }}>   
                                             <div class="form-text">Hỗ trợ: jpg, jpeg, png, webp (≤ 4MB)</div>
                                    @if($hasOrders ?? false)
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
                                       {{ ($hasOrders ?? false) ? 'disabled' : '' }}>
                                <div class="form-text">Chọn nhiều ảnh cùng lúc. Hỗ trợ: jpg, jpeg, png, webp (≤ 4MB)</div>
                                @if($hasOrders ?? false)
                                    <small class="text-muted">Không thể thay đổi ảnh phụ khi sản phẩm đã có đơn hàng</small>
                                @endif
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
                                            @php
                                                $variantHasOrders = $v->hasOrders();
                                            @endphp
                                            <div class="col-md-3">
                                                <label class="form-label">Tên màu</label>
                                                <input name="variants[{{ $idx }}][color_name]" 
                                                        class="form-control"
                                                        value="{{ old('variants.'.$idx.'.color_name', $v->color_name) }}"
                                                          {{ ($hasOrders ?? false) && $variantHasOrders ? 'readonly' : '' }}>
                                                @if(($hasOrders ?? false) && $variantHasOrders)
                                                    <small class="text-muted">Đã có đơn hàng</small>
                                                @endif
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label">Mã màu</label>
                                                <input type="color" name="variants[{{ $idx }}][color_code]"
                                                        class="form-control form-control-color" 
                                                        value="{{ old('variants.'.$idx.'.color_code', $v->color_code) }}"
                                                        {{ ($hasOrders ?? false) && $variantHasOrders ? 'disabled' : '' }}>
                                                @if(($hasOrders ?? false) && $variantHasOrders)
                                                    <input type="hidden" name="variants[{{ $idx }}][color_code]" value="{{ $v->color_code }}">
                                                @endif
                                            </div>

                                            <div class="col-md-2">
                                                <label>Dài</label>
                                                <input type="number" step="0.01" name="variants[{{ $idx }}][length]"
                                                        class="form-control" 
                                                        value="{{ old('variants.'.$idx.'.length', $v->length) }}"
                                                            {{ ($hasOrders ?? false) && $variantHasOrders ? 'readonly' : '' }}>
                                            </div>

                                            <div class="col-md-2">
                                                <label>Rộng</label>
                                                <input type="number" step="0.01" name="variants[{{ $idx }}][width]"
                                                        class="form-control" 
                                                        value="{{ old('variants.'.$idx.'.width', $v->width) }}"
                                                            {{ ($hasOrders ?? false) && $variantHasOrders ? 'readonly' : '' }}>
                                            </div>

                                            <div class="col-md-2">
                                                <label>Cao</label>
                                                <input type="number" step="0.01" name="variants[{{ $idx }}][height]"
                                                        class="form-control" 
                                                        value="{{ old('variants.'.$idx.'.height', $v->height) }}"
                                                            {{ ($hasOrders ?? false) && $variantHasOrders ? 'readonly' : '' }}>
                                            </div>

                                            <div class="col-md-2 mt-3">
                                                <label>Tồn kho</label>
                                                <input type="number" min="0"
                                                        name="variants[{{ $idx }}][stock]" class="form-control"
                                                       value="{{ old('variants.'.$idx.'.stock', $v->stock) }}">
                                            </div>

                                            <div class="col-md-2 mt-3">
                                                <label>Giá (VNĐ)</label>
                                                <input type="number" step="0.01" min="0"
                                                        name="variants[{{ $idx }}][price]" class="form-control"
                                                       value="{{ old('variants.'.$idx.'.price', $v->price ?? $product->price) }}" 
                                                       placeholder="Giá biến thể">
                                                <small class="text-muted">Để trống = giá SP</small>
                                            </div>


                                            {{-- Cân nặng --}}
                                            <div class="col-md-3 mt-3">
                                                <label>Cân nặng</label>
                                                <input type="number" step="0.01" min="0"
                                                        name="variants[{{ $idx }}][weight]" class="form-control"
                                                       value="{{ old('variants.'.$idx.'.weight', $v->weight) }}" placeholder="0.00"
                                                       {{ ($hasOrders ?? false) && $variantHasOrders ? 'readonly' : '' }}>
                                            </div>

                                            <div class="col-md-2 mt-3">
                                                <label>Đơn vị</label>
                                                <select name="variants[{{ $idx }}][weight_unit]" class="form-select"
                                                        {{ ($hasOrders ?? false) && $variantHasOrders ? 'disabled' : '' }}>
                                                    <option value="kg" {{ old('variants.'.$idx.'.weight_unit', $v->weight_unit ?? 'kg') == 'kg' ? 'selected' : '' }}>kg</option>
                                                    <option value="g" {{ old('variants.'.$idx.'.weight_unit', $v->weight_unit ?? 'kg') == 'g' ? 'selected' : '' }}>g</option>
                                                    <option value="lb" {{ old('variants.'.$idx.'.weight_unit', $v->weight_unit ?? 'kg') == 'lb' ? 'selected' : '' }}>lb</option>
                                                </select>
                                                @if(($hasOrders ?? false) && $variantHasOrders)
                                                    <input type="hidden" name="variants[{{ $idx }}][weight_unit]" value="{{ $v->weight_unit ?? 'kg' }}">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- DIV CHỨA BIẾN THỂ MỚI --}}
                            <div id="new-variants"></div>

                            {{-- Nút thêm biến thể --}}
                            <button type="button" id="addNewVariant" class="btn btn-primary mt-2" 
                                    {{ ($hasOrders ?? false) ? 'disabled' : '' }}>
                                + Thêm biến thể
                            </button>
                            @if($hasOrders ?? false)
                                <small class="text-muted d-block mt-1">Không thể thêm biến thể mới khi sản phẩm đã có đơn hàng</small>
                            @endif
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
        document.addEventListener("DOMContentLoaded", function () {

            let newVariantIndex = 0;

            document.getElementById("addNewVariant").addEventListener("click", function () {

                let html = `
                <div class="new-variant-item border rounded p-3 mb-2">

                    <div class="row g-3">
                        
                        <div class="col-md-3">
                            <label class="form-label">Tên màu</label>
                            <input name="variants[new_${newVariantIndex}][color_name]" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Mã màu</label>
                            <input type="color" name="variants[new_${newVariantIndex}][color_code]"
                                class="form-control form-control-color">
                        </div>

                        <div class="col-md-2">
                            <label>Dài</label>
                            <input type="number" step="0.01"
                                name="variants[new_${newVariantIndex}][length]" class="form-control">
                        </div>

                        <div class="col-md-2">
                            <label>Rộng</label>
                            <input type="number" step="0.01"
                                name="variants[new_${newVariantIndex}][width]" class="form-control">
                        </div>

                        <div class="col-md-2">
                            <label>Cao</label>
                            <input type="number" step="0.01"
                                name="variants[new_${newVariantIndex}][height]" class="form-control">
                        </div>

                        <div class="col-md-2 mt-3">
                            <label>Tồn</label>
                            <input type="number" 
                                name="variants[new_${newVariantIndex}][stock]" class="form-control">
                        </div>

                        <div class="col-md-2 mt-3">
                            <label>Giá (VNĐ)</label>
                            <input type="number" step="0.01" min="0"
                                name="variants[new_${variantIndex}][price]" class="form-control" placeholder="Giá biến thể">
                            <small class="text-muted">Để trống = giá SP</small>
                        </div>
git 
                         <div class="col-md-3 mt-3">
                            <label>Cân nặng</label>
                            <input type="number" step="1" 
                                name="variants[new_${newVariantIndex}][weight]" class="form-control" placeholder="0.00">
                        </div>

                        <div class="col-md-2 mt-3">
                            <label>Đơn vị</label>
                            <select name="variants[new_${newVariantIndex}][weight_unit]" class="form-select">
                                <option value="kg" selected>kg</option>
                                <option value="g">g</option>
                                <option value="lb">lb</option>
                            </select>
                        </div>

                    </div>
                </div>`;

                document.getElementById("new-variants").insertAdjacentHTML("beforeend", html);

                newVariantIndex++;
            });

        });
    </script>
@endpush
