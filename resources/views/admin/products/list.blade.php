@extends('admin.layouts.app')
@section('title', 'Danh sách sản phẩm')

@section('content')
    <div class="container-fluid py-4">

        {{-- Thông báo --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Tiêu đề --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <h3 class="fw-bold text-primary mb-0">
                        <i class="bi bi-box-seam me-2"></i>Danh sách sản phẩm
                    </h3>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

                    {{-- Bộ lọc trạng thái --}}
                    <div class="d-flex gap-1 align-items-center">
                        <a href="{{ route('admin.products.list', ['status' => 'all'] + request()->except('status')) }}"
                            class="btn {{ request('status', 'active') == 'all' ? 'btn-primary' : 'btn-outline-primary' }} btn-sm px-2 py-1">
                            <i class="bi bi-list-ul"></i> Tất cả
                        </a>

                        <a href="{{ route('admin.products.list', ['status' => 'active'] + request()->except('status')) }}"
                            class="btn {{ request('status', 'active') == 'active' ? 'btn-success' : 'btn-outline-success' }} btn-sm px-2 py-1">
                            <i class="bi bi-check-circle-fill"></i> Hoạt động
                        </a>

                        <a href="{{ route('admin.products.list', ['status' => 'inactive'] + request()->except('status')) }}"
                            class="btn {{ request('status', 'active') == 'inactive' ? 'btn-warning' : 'btn-outline-warning' }} btn-sm px-2 py-1">
                            <i class="bi bi-pause-circle-fill"></i> Tạm ẩn
                        </a>
                    </div>

                    {{-- Ô tìm kiếm --}}
                    <form action="{{ route('admin.products.list') }}" method="GET" class="d-flex flex-grow-1 mx-md-4"
                        style="max-width: 500px;">
                        <input type="hidden" name="status" value="{{ request('status', 'active') }}">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Nhập tên sản phẩm..."
                                value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>

                            @if (request('search'))
                                <a href="{{ route('admin.products.list', ['status' => request('status', 'active')]) }}"
                                    class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            @endif
                        </div>
                    </form>

                    {{-- Nút thêm sản phẩm --}}
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Thêm sản phẩm
                    </a>

                </div>
            </div>
        </div>

        {{-- Bảng sản phẩm --}}
        @if ($products->isEmpty())
            <p class="text-center py-4">Không tìm thấy sản phẩm nào.</p>
        @else
            <div class="table-responsive shadow-sm rounded">
                <table class="table table-striped table-hover align-middle text-center mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên</th>
                            <th>Ảnh</th>
                            <th>Giá</th>
                            <th>Danh mục</th>
                            <th>Trạng thái</th>
                            <th width="150">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>

                                <td class="text-center">
                                    <a href="{{ route('admin.products.show', $product) }}" class="text-dark"
                                        style="text-decoration:none;">
                                        {{ $product->name }}
                                    </a>
                                </td>

                                <td>
                                    <a href="{{ route('admin.products.show', $product) }}"
                                        style="text-decoration:none;color:#0d6efd;"
                                        onmouseover="this.style.textDecoration='underline'"
                                        onmouseout="this.style.textDecoration='none'">
                                        @if ($product->image)
                                            <img src="{{ Storage::url($product->image) }}"
                                                style="width:60px;height:60px;object-fit:cover;border-radius:5px;">
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </a>

                                </td>

                                <td>{{ number_format($product->price, 0, ',', '.') }}₫</td>

                                <td>{{ $product->category->name ?? '—' }}</td>

                                <td>
                                    <span
                                        class="badge {{ $product->status == 'active' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ $product->status == 'active' ? 'Hoạt động' : 'Tạm ẩn' }}
                                    </span>
                                </td>

                                <td>
                                    <a href="{{ route('admin.products.edit', $product->id) }}"
                                        class="btn btn-info btn-sm">Sửa</a>

                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                                        onsubmit="return confirm('Xoá sản phẩm này?')" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Xóa</button>
                                    </form>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $products->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        @endif

    </div>
@endsection
