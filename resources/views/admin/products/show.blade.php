@extends('admin.layouts.app')
@section('title', 'Chi tiết sản phẩm')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Chi tiết: {{ $product->name }}</h4>
                </div>
                <div class="card-body">

                    {{-- Ảnh đại diện --}}
                    @if($product->image)
                        <div class="mb-3">
                            <strong>Ảnh đại diện:</strong><br>
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                 style="max-width: 300px; border-radius: 8px; margin-top: 5px;">
                        </div>
                        <hr>
                    @endif

                    {{-- Album ảnh --}}
                    @if($product->gallery && count($product->gallery) > 0)
                        <div class="mb-3">
                            <strong>Album ảnh:</strong><br>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                @foreach($product->gallery as $img)
                                    <img src="{{ asset('storage/' . $img) }}" alt="Ảnh gallery"
                                         style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px;">
                                @endforeach
                            </div>
                        </div>
                        <hr>
                    @endif

                    {{-- Thông tin sản phẩm chính --}}
                    <p><strong>Slug:</strong> {{ $product->slug }}</p>
                    <p><strong>Giá:</strong> {{ number_format($product->price, 0, ',', '.') }} VNĐ</p>
                    <p><strong>Tồn kho:</strong> {{ $product->stock }}</p>
                    <p><strong>Trạng thái:</strong>
                        @if($product->status == 'active')
                            <span class="badge bg-success">Hoạt động</span>
                        @else
                            <span class="badge bg-secondary">Không hoạt động</span>
                        @endif
                    </p>
                    <div class="mb-3">
                        <strong>Mô tả:</strong>
                        <div>{!! nl2br(e($product->description)) !!}</div>
                    </div>

                    {{-- --- THÊM PHẦN NÀY: HIỂN THỊ BIẾN THỂ --- --}}
                    <hr> 
                    <div class="mb-3">
                        <strong>Các Biến thể:</strong>
                        {{-- Kiểm tra xem $product->variants có tồn tại và có phần tử không --}}
                        @if(isset($product->variants) && $product->variants->count() > 0)
                            <div class="table-responsive mt-2">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50px;">Ảnh</th>
                                            <th>Màu sắc</th>
                                            <th>Chất liệu</th>
                                            <th>SKU</th>
                                            <th>Giá</th>
                                            <th>Tồn kho</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($product->variants as $variant)
                                            <tr>
                                                <td>
                                                    @if($variant->image)
                                                        <img src="{{ asset('storage/' . $variant->image) }}" alt="Variant Image" 
                                                             style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                                    @else
                                                        <small class="text-muted">N/A</small>
                                                    @endif
                                                </td>
                                                <td>{{ $variant->color ?? 'N/A' }}</td>
                                                <td>{{ $variant->material ?? 'N/A' }}</td>
                                                <td>{{ $variant->sku }}</td>
                                                <td>{{ number_format($variant->price, 0, ',', '.') }} VNĐ</td>
                                                <td>{{ $variant->stock }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mt-2">Sản phẩm này chưa có biến thể nào.</p>
                        @endif
                    </div>
                    {{-- --- HẾT PHẦN BIẾN THỂ --- --}}

                    {{-- Các nút bấm --}}
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.products.list') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại Danh sách
                        </a>
                        {{-- --- THÊM LẠI NÚT CHỈNH SỬA --- --}}
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil-square"></i> Chỉnh sửa
                        </a>
                        {{-- ----------------------------- --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection