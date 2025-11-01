@extends('admin.layouts.app')

<<<<<<< HEAD
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@section('title','Danh sách danh mục')
@section('content')
    <h1 class="text-center mb-4">Danh sách danh mục</h1>
=======
@section('title', 'Danh sách danh mục')

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
        <h2 class="mb-0">Danh sách danh mục</h2>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Thêm danh mục
        </a>
    </div>

    {{-- Ô tìm kiếm --}}
    <form action="{{ route('admin.categories.list') }}" method="GET" class="mb-3 d-flex" role="search">
        <input type="text" name="keyword" class="form-control me-2"
               placeholder="Nhập tên danh mục cần tìm..." value="{{ request('keyword') }}">
        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
        @if (request('keyword'))
            <a href="{{ route('admin.categories.list') }}" class="btn btn-secondary ms-2">Xóa tìm</a>
        @endif
    </form>

    {{-- Bảng danh mục --}}
    <table class="table table-bordered table-striped align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên danh mục</th>
                <th>Mô tả</th>
                <th>Danh mục cha</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->description ?? '—' }}</td>
                    <td>{{ $category->parent ? $category->parent->name : 'Không có' }}</td>
                    <td>
                        <span class="badge {{ $category->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                            {{ $category->status == 'active' ? 'Hoạt động' : 'Tạm ẩn' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-primary btn-sm">Sửa</a>
                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Bạn có chắc chắn muốn xoá danh mục này không?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Không tìm thấy danh mục nào</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
>>>>>>> 5b833b85b2c1795c4b56c34cd61d94684e33eca5
@endsection
