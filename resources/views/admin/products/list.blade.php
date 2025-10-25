@extends('admin.layouts.app')
@section('title', 'Danh sách sản phẩm')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Quản lý Sản phẩm</h4>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Thêm mới
                </a>
            </div>
        </div>
        <div class="card-body">

            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped table-hover border">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 70px;">Ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Giá</th>
                            <th>Tồn kho</th>
                            <th>Trạng thái</th>
                            <th style="width: 200px;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                        <tr>
                            <td>
                                @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                    style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                @else
                                <span class="text-muted" style="font-size: 12px;">N/A</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.products.show', $product->id) }}" class="text-decoration-none fw-bold">
                                    {{ $product->name }}
                                </a>
                                <br>
                                <small class="text-muted">Slug: {{ $product->slug }}</small>
                            </td>
                            <td>{{ number_format($product->price, 0, ',', '.') }} VNĐ</td>
                            <td>{{ $product->stock }}</td>
                            <td>
                                @if($product->status == 'active')
                                <span class="badge bg-success">Hoạt động</span>
                                @else
                                <span class="badge bg-secondary">Không hoạt động</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.products.variants.index', $product->id) }}" class="btn btn-info btn-sm" title="Biến thể">
                                    <i class="bi bi-boxes"></i>
                                </a>

                                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning btn-sm" title="Sửa">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Bạn có chắc muốn XÓA VĨNH VIỄN sản phẩm này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Xóa vĩnh viễn">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Không tìm thấy sản phẩm nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection