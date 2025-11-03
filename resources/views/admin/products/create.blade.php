@extends('admin.layouts.app')

@section('title', 'Thêm sản phẩm')

@push('styles')
    <style>
        .img-preview {
            width: 100%; max-width: 420px; height: 200px;
            object-fit: cover; 
            border: 1px solid #e9ecef; 
            border-radius: .5rem; 
            display: block;
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

                            {{-- Biến thể --}}
                            <h4>Biến thể sản phẩm</h4>

                                {{-- Màu sắc --}}
                                <h5>Danh sách màu sắc</h5>

                                    <div class="form-group mb-3">
                                        <label for="color_name">Tên màu (tuỳ chọn)</label>
                                        <input type="text" id="color_name" class="form-control" placeholder="VD: Xám nhạt">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="color_code">Màu (mã HEX)</label>
                                        <input type="color" id="color_code" class="form-control form-control-color" value="#000000" title="Chọn màu">
                                    </div>

                                    <button type="button" class="btn btn-primary mb-3" id="btn-save-color">+ Lưu màu</button>

                                    <div id="color-list" class="mb-3"></div>

                                    {{-- Kích thước --}}
                            <label>Kích thước</label>
                                <div class="row">
                                    <div class="col-md-3">
                                        <input type="number" id="length-input" step="0.01" class="form-control" placeholder="Chiều dài (cm)">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" id="width-input" step="0.01" class="form-control" placeholder="Chiều rộng (cm)">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" id="height-input" step="0.01" class="form-control" placeholder="Chiều cao (cm)">
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" class="btn btn-outline-secondary" id="btn-add-size">+ Thêm kích thước</button>
                                    </div>
                                </div>

                                <div id="size-list" class="mt-3"></div>
                                <div id="hidden-fields"></div>

                            {{-- end biến thể--}}


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


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        // ====== MÀU ======
        const colorList = document.getElementById('color-list');
        const hidden    = document.getElementById('hidden-fields');
        const btnSave   = document.getElementById('btn-save-color');
        let colorIdx = 0;

        btnSave.addEventListener('click', () => {
            const name = document.getElementById('color_name').value.trim();
            const code = document.getElementById('color_code').value.trim();
            if (!code) return alert('Hãy chọn mã màu!');

            const row = document.createElement('div');
            row.className = 'd-flex align-items-center gap-2 mb-2';

            const swatch = document.createElement('span');
            swatch.style.cssText = `width:25px;height:25px;border:1px solid #000;background:${code};border-radius:4px;`;

            const label = document.createElement('span');
            label.textContent = name ? `${name} (${code})` : code;

            const del = document.createElement('button');
            del.type = 'button';
            del.className = 'btn btn-sm btn-link text-danger';
            del.textContent = 'x';

            // hidden inputs
            const hiddenBlock = document.createElement('div');
            hiddenBlock.innerHTML = `
            <input type="hidden" name="colors[${colorIdx}][name]" value="${name}">
            <input type="hidden" name="colors[${colorIdx}][code]" value="${code}">
            `;

            del.onclick = () => { colorList.removeChild(row); hidden.removeChild(hiddenBlock); };

            row.appendChild(swatch); row.appendChild(label); row.appendChild(del);
            colorList.appendChild(row);
            hidden.appendChild(hiddenBlock);
            colorIdx++;

            document.getElementById('color_name').value = '';
            document.getElementById('color_code').value = '#000000';
        });

        // ====== KÍCH THƯỚC ======
        const sizeList = document.getElementById('size-list');
        const btnAdd   = document.getElementById('btn-add-size');
        let sizeIdx = 0;

        btnAdd.addEventListener('click', () => {
            const L = document.getElementById('length-input').value.trim();
            const W = document.getElementById('width-input').value.trim();
            const H = document.getElementById('height-input').value.trim();
            if (!L || !W || !H) return alert('Hãy nhập đủ chiều dài, rộng, cao!');

            const row = document.createElement('div');
            row.className = 'd-flex align-items-center gap-2 mb-2';

            const badge = document.createElement('span');
            badge.className = 'badge bg-secondary';
            badge.textContent = `${L} × ${W} × ${H} cm`;

            const del = document.createElement('button');
            del.type = 'button';
            del.className = 'btn btn-sm btn-link text-danger';
            del.textContent = 'x';

            const hiddenBlock = document.createElement('div');
            hiddenBlock.innerHTML = `
            <input type="hidden" name="sizes[${sizeIdx}][length]" value="${L}">
            <input type="hidden" name="sizes[${sizeIdx}][width]"  value="${W}">
            <input type="hidden" name="sizes[${sizeIdx}][height]" value="${H}">
            `;

            del.onclick = () => { sizeList.removeChild(row); hidden.removeChild(hiddenBlock); };

            row.appendChild(badge); row.appendChild(del);
            sizeList.appendChild(row);
            hidden.appendChild(hiddenBlock);
            sizeIdx++;

            document.getElementById('length-input').value = '';
            document.getElementById('width-input').value  = '';
            document.getElementById('height-input').value = '';
        });
        });
    </script>
@endpush
@endsection
