@extends('admin.layouts.app')
@section('title', 'Quản lý Biến thể cho: ' . $product->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Quản lý Biến thể cho: <span class="fw-bold">{{ $product->name }}</span></h4>
                    <small>ID Sản phẩm: {{ $product->id }}</small>
                </div>
                <div class="card-body">
                    @if (session('success')) <div class="alert alert-success alert-dismissible fade show" role="alert"> {{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> </div> @endif
                    @if ($errors->any()) <div class="alert alert-danger">
                        <h6>Đã có lỗi xảy ra:</h6>
                        <ul class="mb-0"> @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul>
                    </div> @endif

                    <form action="{{ route('admin.products.variants.store', $product->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn btn-success btn-sm" id="add-variant-btn"> <i class="bi bi-plus-circle"></i> Thêm biến thể </button>
                        </div>

                        <div id="variants-container">
                            
                            <div class="row fw-bold g-2 align-items-center mb-2" id="variant-header" style="{{ $product->variants->isEmpty() && !old('variants') ? 'display: none;' : '' }}">
                                <div class="col-md-2">Màu sắc</div>
                                <div class="col-md-2">Chất liệu</div>
                                <div class="col-md-2">Giá <span class="text-danger">*</span></div>
                                <div class="col-md-1">Tồn kho <span class="text-danger">*</span></div>
                                <div class="col-md-3">Ảnh</div>
                                <div class="col-md-2">Hành động</div>
                            </div>
                            <hr id="variant-hr" style="{{ $product->variants->isEmpty() && !old('variants') ? 'display: none;' : '' }}" class="mt-0 mb-2">

                            
                            @php
                            // Đã sửa $variant->name thành color, size
                            $variants = old('variants', $product->variants->map(function ($variant) {
                            return [ 'id' => $variant->id, 'color' => $variant->color, 'size' => $variant->size, 'sku' => $variant->sku, 'price' => $variant->price, 'stock' => $variant->stock, 'image_url' => $variant->image ? asset('storage/' . $variant->image) : null ];
                            })->toArray());
                            @endphp

                            @if(is_array($variants))
                            @foreach($variants as $index => $variant)
                            <div class="row g-2 mb-2 variant-row align-items-center">
                                <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant['id'] ?? '' }}">
                                <div class="col-md-2"> <input type="text" name="variants[{{ $index }}][color]" class="form-control form-control-sm @error('variants.'.$index.'.color') is-invalid @enderror" placeholder="Màu" value="{{ $variant['color'] ?? '' }}"> </div>
                                <div class="col-md-2"> <input type="text" name="variants[{{ $index }}][material]" class="form-control form-control-sm @error('variants.'.$index.'.material') is-invalid @enderror" placeholder="Chất liệu" value="{{ $variant['material'] ?? '' }}"></div>
                                <div class="col-md-2"> <input type="number" name="variants[{{ $index }}][price]" class="form-control form-control-sm @error('variants.'.$index.'.price') is-invalid @enderror" placeholder="Giá" value="{{ $variant['price'] ?? '' }}" step="1000" min="0" required> </div>
                                <div class="col-md-1"> <input type="number" name="variants[{{ $index }}][stock]" class="form-control form-control-sm @error('variants.'.$index.'.stock') is-invalid @enderror" placeholder="Kho" value="{{ $variant['stock'] ?? '' }}" min="0" required> </div>
                                <div class="col-md-3"> <input type="file" name="variants[{{ $index }}][image]" class="form-control form-control-sm @error('variants.'.$index.'.image') is-invalid @enderror"> @if(isset($variant['image_url'])) <img src="{{ $variant['image_url'] }}" height="30" class="mt-1 rounded"> @elseif(isset($variant['image'])) <span class="text-muted small">Ảnh đã chọn</span> @endif </div>
                                <div class="col-md-2"> <button type="button" class="btn btn-danger btn-sm remove-variant-btn">Xóa</button> </div>
                            </div>
                            @endforeach
                            @endif
                        </div>
                        <div id="deleted-variants-container"></div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.products.list') }}" class="btn btn-secondary"> <i class="bi bi-arrow-left"></i> Quay lại DS Sản phẩm </a>
                            <button type="submit" class="btn btn-primary"> <i class="bi bi-save"></i> Lưu các thay đổi </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<template id="variant-template">

    <div class="row g-2 mb-2 variant-row align-items-center">
        <input type="hidden" name="variants[__INDEX__][id]" value="">
        <div class="col-md-2"> <input type="text" name="variants[__INDEX__][color]" class="form-control form-control-sm" placeholder="Màu"> </div> 
        <div class="col-md-2"> <input type="text" name="variants[__INDEX__][size]" class="form-control form-control-sm" placeholder="Cỡ"> </div> 
        <div class="col-md-2"> <input type="number" name="variants[__INDEX__][price]" class="form-control form-control-sm" placeholder="Giá" step="1000" min="0" required> </div>
        <div class="col-md-1"> <input type="number" name="variants[__INDEX__][stock]" class="form-control form-control-sm" placeholder="Kho" min="0" required> </div>
        <div class="col-md-3"> <input type="file" name="variants[__INDEX__][image]" class="form-control form-control-sm"> </div>
        <div class="col-md-2"> <button type="button" class="btn btn-danger btn-sm remove-variant-btn">Xóa</button> </div>
    </div>

</template>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addBtn = document.getElementById('add-variant-btn');
        const container = document.getElementById('variants-container');
        const templateNode = document.getElementById('variant-template');
        const deletedContainer = document.getElementById('deleted-variants-container');
        const header = document.getElementById('variant-header');
        const hr = document.getElementById('variant-hr');

        let variantIndex = container.querySelectorAll('.variant-row').length > 0 ?
            Math.max(...Array.from(container.querySelectorAll('.variant-row input[name*="[id]"]')).map((input, i) => i)) + 1 :
            0;

        function addRemoveListener(button) {
            button.addEventListener('click', function(e) {
                const row = e.target.closest('.variant-row');
                const variantIdInput = row.querySelector('input[name*="[id]"]');

                if (variantIdInput && variantIdInput.value) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'deleted_variants[]';
                    hiddenInput.value = variantIdInput.value;
                    deletedContainer.appendChild(hiddenInput);
                }

                row.remove();

                if (container.querySelectorAll('.variant-row').length === 0) {
                    header.style.display = 'none';
                    hr.style.display = 'none';
                }
            });
        }

        addBtn.addEventListener('click', function() {
            header.style.display = '';
            hr.style.display = '';

            const newRowContent = templateNode.innerHTML.replace(/__INDEX__/g, variantIndex);
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = newRowContent.trim();
            const newRowElement = tempDiv.firstElementChild;

            addRemoveListener(newRowElement.querySelector('.remove-variant-btn'));
            hr.insertAdjacentElement('afterend', newRowElement);
            variantIndex++;
        });

        container.querySelectorAll('.remove-variant-btn').forEach(button => {
            addRemoveListener(button);
        });
    });
</script>
@endpush