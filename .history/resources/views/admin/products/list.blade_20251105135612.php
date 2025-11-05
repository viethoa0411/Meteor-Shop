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

        {{-- Tiêu đề + nút thêm --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">Danh sách sản phẩm</h2>
            <a href="{{ route('admin.products.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Thêm sản phẩm
            </a>
        </div>

        {{-- Ô tìm kiếm --}}
        <form action="{{ route('admin.products.list') }}" method="GET" class="mb-3 d-flex" role="search">
            <input type="text" name="search" class="form-control me-2" placeholder="Nhập tên sản phẩm cần tìm..."
                value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
            @if (request('search'))
                <a href="{{ route('admin.products.list') }}" class="btn btn-secondary ms-2">Xóa tìm</a>
            @endif
        </form>

        {{-- Bảng sản phẩm --}}
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
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
                            <a href="{{ route('admin.products.show', $product) }}"
                                style="text-decoration:none;color:#0d6efd;"
                                onmouseover="this.style.textDecoration='underline'"
                                onmouseout="this.style.textDecoration='none'">
                                {{ $product->name }}
                            </a>
                        </td>
                        <td>
                            @if ($product->image)
                                <img src="{{ Storage::url($product->image) }}" alt="Ảnh sản phẩm"
                                    style="width:70px;height:70px;object-fit:cover;border-radius:5px;border:1px solid #dee2e6;">
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
                            {{-- Nút sửa --}}
                            <a href="{{ route('admin.products.edit', $product->id) }}"
                                class="btn btn-primary btn-sm">Sửa</a>

                            {{-- Form xóa --}}
                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                                class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xoá sản phẩm này không?')">
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

        {{-- Phân trang --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $products->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
{{-- @section('title', 'Danh sách sản phẩm')

@section('content')
    @if (@session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label='Close'></button>
        </div>
    @endif

    @if (@session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="CLose"></button>
        </div>
    @endif

    @extends('admin.layouts.app')

    <div class="px-4 py6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-semibold">Danh sách sản phẩm</h1>
            <div class="space-x-2">
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Thêm sản phẩm
                </a>
            </div>
        </div>

        @if (@session('success'))
            <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
        @endif

        {{-- Ô tìm kiếm  --}}
        <form method="GET" class="mb-4">
            <div class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo tên..."
                    class="border rounded px-3 py-2 w-full">
                <button class="border rounded px-4 py-2">Tìm</button>
            </div>
        </form>

        <div class="overflow-x-auto bg-white border rounded">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left">
                        <th class="px-3 py-3">#</th>
                        <th class="px-3 py-3">Ảnh</th>
                        <th class="px-3 py-3">Tên </th>
                        <th class="px-3 py-3">Slug</th>
                        <th class="px-3 py-3">Giá</th>
                        <th class="px-3 py-3">Kho</th>
                        <th class="px-3 py-3">Danh mục</th>
                        <th class="px-3 py-3">Thương hiệu</th>
                        <th class="px-3 py2 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $i => $p)
                        <tr class="border-t">
                            <td class="px-3 py-2">{{ $products->firstItem() + $i }}</td>
                            <td class="px-3 py-2">
                                @if ($p->image)
                                    {{-- <img src="{{ asset('storage/'.$p->image) }}" alt="" style="width:100px;height:100px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;" /> --}}
                                    {{-- trước đây: asset('storage/'.$p->image) --}}
                                    <img src="{{ Storage::url($p->image) }}" alt=""
                                        style="width:100px;height:100px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;" />
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 font-medium">
                                <a href="{{ route('admin.products.show', $p) }}"
                                    style="text-decoration: none; color:#0d6efd;"
                                    onmouseover="this.style.textDecoration='underline'"
                                    onmouseout="this.style.textDecoration='none'">
                                    {{ $p->name }}
                                </a>
                            </td>

                            <td class="px-3 py-2 text-gray-600">{{ $p->slug }}</td>
                            <td class="px-3 py-2">{{ number_format($p->price, 0, ',', '.') }}đ</td>
                            <td class="px-3 py-2">{{ number_format($p->stock, 0, ',', '.') }}</td>
                            <td class="px-3 py-2">{{ $p->category->name ?? '_' }}</td>
                            <td class="px-3 py-2">{{ $p->brand->name ?? '_' }}</td>
                            <td class="px-3 py-2">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.products.edit', $p) }}"
                                        class="px-3 py-1 btn btn-warning rounded border mb-2">Sửa</a>
                                    <form action="{{ route('admin.products.destroy', $p) }}" method="POST"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn xoá sản phẩm này không?')">
                                        @csrf @method('DELETE')
                                        <button class="px-3 py-1 rounded bg-red-600 btn btn-danger">Xoá</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-3 py-6 text-center text-gray-500">Chưa có sản phẩm.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $products->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
    </div>

@endsection --}}
