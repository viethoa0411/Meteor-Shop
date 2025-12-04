@extends('admin.layouts.app')
@section('title', 'Quản lý danh mục trang chủ')

@section('content')
<div class="container-fluid py-4">
    {{-- Thông báo --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Tiêu đề --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <h3 class="fw-bold text-primary mb-0">
                    <i class="bi bi-grid-fill me-2"></i>Quản lý danh mục trang chủ
                </h3>
                <a href="{{ route('admin.home-categories.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Thêm mới
                </a>
            </div>
        </div>
    </div>

    {{-- Bảng danh sách --}}
    @if ($categories->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3">Chưa có danh mục nào.</p>
                <a href="{{ route('admin.home-categories.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Thêm danh mục đầu tiên
                </a>
            </div>
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="80">Ảnh</th>
                                <th>Tên danh mục</th>
                                <th>Link</th>
                                <th width="100">Thứ tự</th>
                                <th width="120">Trạng thái</th>
                                <th width="150">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td>
                                        @if ($category->image)
                                            <img src="{{ asset('storage/' . $category->image) }}" 
                                                alt="{{ $category->name }}"
                                                class="img-thumbnail" 
                                                style="width: 60px; height: 60px; object-fit: cover;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center"
                                                style="width: 60px; height: 60px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td><strong>{{ $category->name }}</strong></td>
                                    <td>
                                        @if ($category->link)
                                            <a href="{{ $category->link }}" target="_blank" class="text-decoration-none">
                                                <i class="bi bi-link-45deg"></i> {{ Str::limit($category->link, 30) }}
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $category->sort_order }}</span>
                                    </td>
                                    <td>
                                        @if ($category->status == 'active')
                                            <span class="badge bg-success">Hoạt động</span>
                                        @else
                                            <span class="badge bg-warning">Tạm ẩn</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.home-categories.edit', $category->id) }}"
                                                class="btn btn-sm btn-warning" title="Sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.home-categories.destroy', $category->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

