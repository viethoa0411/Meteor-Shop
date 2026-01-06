@extends('admin.layouts.app')

@section('title', 'Thêm danh mục')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Thêm danh mục mới</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Tên danh mục</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Nhập tên danh mục" required>
        </div>

        <div class="mb-3">
            <label for="slug" class="form-label">Slug (tự tạo nếu để trống)</label>
            <input type="text" class="form-control" id="slug" name="slug" placeholder="vd: ao-nam-thoi-trang">
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Ảnh danh mục</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
            <small class="form-text text-muted">Hỗ trợ: jpg, jpeg, png, webp (≤ 4MB)</small>
        </div>

        <div class="mb-3">
            <label for="parent_id" class="form-label">Danh mục cha</label>
            <select class="form-select" name="parent_id">
                <option value="">— Không có —</option>
                @foreach ($parents as $parent)
                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Trạng thái</label>
            <select class="form-select" name="status" required>
                <option value="active">Hoạt động</option>
                <option value="inactive">Tạm ẩn</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Thêm danh mục</button>
        <a href="{{ route('admin.categories.list') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
