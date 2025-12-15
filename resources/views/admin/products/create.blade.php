@extends('admin.layouts.app')

@section('title', 'Thêm sản phẩm')

@push('styles')
    <style>
        .img-preview {
            width: 100%;
            max-width: 420px;
            height: 200px;
            object-fit: cover;
            border: 1px solid #e9ecef;
            border-radius: .5rem;
            display: block;
        }

        .variant-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 5px;
        }

        .variant-swatch {
            width: 25px;
            height: 25px;
            border: 1px solid #000;
            border-radius: 4px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10 col-lg-8 mx-auto">

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
                    <div class="card-header">
                        <h4 class="mb-0">Thêm sản phẩm</h4>
                    </div>
                    <div class="card-body">
                        <form id="productCreateForm" action="{{ route('admin.products.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            {{-- Tên --}}
                            <div class="mb-3">
                                <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                    required>
                            </div>

                            <div class="row g-3 mb-3">
                                {{-- Slug --}}
                                <div class="col-md-6">
                                    <label class="form-label">Slug (tự tạo nếu để trống)</label>
                                    <input type="text" name="slug" class="form-control" value="{{ old('slug') }}">
                                </div>

                                {{-- Giá --}}
                                <div class="col-md-3">
                                    <label class="form-label">Giá (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="price" class="form-control"
                                        value="{{ old('price') }}" required>
                                </div>
                            </div>

                            {{-- Danh mục --}}
                            <div class="mb-3">
                                <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    @foreach ($categories as $c)
                                        <option value="{{ $c->id }}"
                                            {{ old('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Trạng thái --}}
                            <div class="mb-3">
                                <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive
                                    </option>
                                </select>
                            </div>

                            {{-- Mô tả --}}
                            <div class="mb-3">
                                <label class="form-label">Mô tả</label>
                                <textarea name="description" rows="5" class="form-control">{{ old('description') }}</textarea>
                            </div>

                            {{-- Ảnh đại diện --}}
                            <div class="mb-3">
                                <label class="form-label">Ảnh đại diện</label>
                                <input type="file" name="image" accept="image/*" class="form-control">
                            </div>

                            {{-- Ảnh chi tiết --}}
                            <div class="mb-3">
                                <label class="form-label">Ảnh chi tiết (có thể chọn nhiều)</label>
                                <input type="file" name="images[]" accept="image/*" class="form-control" multiple>
                                <div class="form-text">Hỗ trợ: jpg, jpeg, png, webp (≤ 4MB)</div>
                            </div>

                            {{-- Biến thể sản phẩm --}}
                            <h4 class="mt-4">Biến thể sản phẩm</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label>Tên màu</label>
                                    <input type="text" id="color_name" class="form-control" placeholder="VD: Xám nhạt">
                                </div>
                                <div class="col-md-6">
                                    <label>Màu (HEX)</label>
                                    <input type="color" id="color_code" class="form-control form-control-color"
                                        value="#000000">
                                </div>
                            </div>
                            <div class="row g-3 mt-2">
                                <div class="col-md-3">
                                    <input type="number" id="length" step="0.01" class="form-control"
                                        placeholder="Chiều dài (m) - VD: 2.5">
                                </div>
                                <div class="col-md-3">
                                    <input type="number" id="width" step="0.01" class="form-control"
                                        placeholder="Chiều rộng (m) - VD: 1.8">
                                </div>
                                <div class="col-md-3">
                                    <input type="number" id="height" step="0.01" class="form-control"
                                        placeholder="Chiều cao (m) - VD: 0.8">
                                </div>
                            </div>
                            <div class="row g-3 mt-2">
                                <div class="col-md-3">
                                    <input type="number" id="weight" step="0.01" class="form-control"
                                        placeholder="Cân nặng (kg)">
                                </div>
                                <div class="col-md-3">
                                    <input type="number" id="variant_stock" class="form-control"
                                        placeholder="Số lượng sản phẩm">
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary mt-2 mb-3" id="add_variant">Thêm biến
                                thể</button>

                            <div id="variant-list" class="mb-3"></div>
                            <div id="hidden-variants"></div>

                            <div class="d-flex mt-3">
                                <div class="ms-auto">
                                    <a href="{{ route('admin.products.list') }}" class="btn btn-danger me-2">Hủy</a>
                                    <button type="submit" class="btn btn-primary">Lưu</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addBtn = document.getElementById('add_variant');
            const variantList = document.getElementById('variant-list');
            const hiddenVariants = document.getElementById('hidden-variants');
            let idx = 0;

            addBtn.addEventListener('click', function() {
                const colorName = document.getElementById('color_name').value.trim();
                const colorCode = document.getElementById('color_code').value.trim();
                const length = document.getElementById('length').value.trim();
                const width = document.getElementById('width').value.trim();
                const height = document.getElementById('height').value.trim();
                const weight = document.getElementById('weight').value.trim();
                const stock = document.getElementById('variant_stock').value.trim();

                if (!length || !width || !height) return alert('Nhập đủ kích thước!');
                if (!colorCode) return alert('Chọn màu!');
                if (!weight) return alert('Nhập cân nặng cho biến thể!');
                if (!stock) return alert('Nhập tồn kho cho biến thể!');

                const row = document.createElement('div');
                row.className = 'variant-row';
                row.innerHTML = `
                    <div class="variant-swatch" style="background:${colorCode}"></div>
                    <span>${colorName || colorCode} - ${length}×${width}×${height} m - ${weight} kg - <b>${stock}</b> sp</span>
                    <button type="button" class="btn btn-sm btn-link text-danger">x</button>
                `;

                const delBtn = row.querySelector('button');
                const hiddenDiv = document.createElement('div');
                delBtn.addEventListener('click', () => {
                    variantList.removeChild(row);
                    hiddenVariants.removeChild(hiddenDiv);
                });

                variantList.appendChild(row);

                hiddenDiv.innerHTML = `
            <input type="hidden" name="variants[${idx}][color_name]" value="${colorName}">
            <input type="hidden" name="variants[${idx}][color_code]" value="${colorCode}">
            <input type="hidden" name="variants[${idx}][length]" value="${length}">
            <input type="hidden" name="variants[${idx}][width]" value="${width}">
            <input type="hidden" name="variants[${idx}][height]" value="${height}">
            <input type="hidden" name="variants[${idx}][weight]" value="${weight}">
            <input type="hidden" name="variants[${idx}][stock]" value="${stock}">
        `;
                hiddenVariants.appendChild(hiddenDiv);
                idx++;

                // Reset input
                document.getElementById('color_name').value = '';
                document.getElementById('color_code').value = '#000000';
                document.getElementById('length').value = '';
                document.getElementById('width').value = '';
                document.getElementById('height').value = '';
                document.getElementById('weight').value = '';
                document.getElementById('variant_stock').value = '';
            });
        });
    </script>

@endsection
