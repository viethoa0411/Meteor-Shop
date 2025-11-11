@extends('admin.layouts.app')

@section('title', 'Danh sách sản phẩm')

@section('content')
<div class="container mt-4">
    {{-- Thông báo --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Danh sách sản phẩm</h2>
        <a href="{{ route('admin.products.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Thêm sản phẩm
        </a>
    </div>

    <form action="{{ route('admin.products.list') }}" method="GET" class="mb-3 d-flex" role="search">
        <input type="text" name="search" class="form-control me-2" placeholder="Nhập tên sản phẩm cần tìm..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
        @if (request('search'))
            <a href="{{ route('admin.products.list') }}" class="btn btn-secondary ms-2">Xóa tìm</a>
        @endif
    </form>

    <table class="table table-bordered table-striped align-middle">
        <thead>
            <tr class="text-center">
                <th>ID</th>
                <th>Tên sản phẩm</th>
                <th>Ảnh bìa</th>
                <th>Slug</th>
                <th>Giá</th>
                <th>Tồn kho</th>
                <th>Danh mục</th>
                <th>Trạng thái</th>
                <th width="150">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr class="text-center">
                    <td>{{ $product->id }}</td>
                    <td class="text-start">
                        <a href="{{ route('admin.products.show', $product) }}" style="text-decoration:none;color:#0d6efd;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                            {{ $product->name }}
                        </a>
                    </td>
                    <td>
                        @if ($product->image)
                            <img src="{{ Storage::url($product->image) }}" alt="Ảnh sản phẩm" style="width:70px;height:70px;object-fit:cover;border-radius:5px;border:1px solid #dee2e6;">
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-muted">{{ $product->slug }}</td>
                    <td>{{ number_format($product->price, 0, ',', '.') }}₫</td>
                    <td>{{ $product->stock }}</td>
                    <td>{{ $product->category->name ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $product->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                            {{ $product->status == 'active' ? 'Hoạt động' : 'Tạm ẩn' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary btn-sm">Sửa</a>
                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xoá sản phẩm này không?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center text-muted">Không tìm thấy sản phẩm nào</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-4">
        {{ $products->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection