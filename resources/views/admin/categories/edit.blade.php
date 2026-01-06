@extends('admin.layouts.app')

@section('title', 'Sửa danh mục')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Sửa danh mục</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Tên danh mục</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}"
                    required>
            </div>

            <div class="mb-3">
                <label for="slug" class="form-label">Slug</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug', $category->slug) }}">
            </div>

            <div class="mb-3">
                <label for="parent_id" class="form-label">Danh mục cha</label>
                <select name="parent_id" class="form-select">
                    <option value="">— Không có —</option>
                    @foreach ($parents as $parent)
                        <option value="{{ $parent->id }}" {{ $category->parent_id == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Ảnh danh mục</label>
                @if ($category->image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $category->image) }}" alt="Ảnh danh mục hiện tại" style="max-height: 120px; border:1px solid #e9ecef; border-radius:6px;">
                    </div>
                @endif
                <input type="file" name="image" id="image" class="form-control" accept="image/*">
                <small class="form-text text-muted">Hỗ trợ: jpg, jpeg, png, webp (≤ 4MB)</small>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select name="status" class="form-select" required>
                    <option value="active" {{ $category->status == 'active' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="inactive" {{ $category->status == 'inactive' ? 'selected' : '' }}>Tạm ẩn</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('admin.categories.list') }}" class="btn btn-secondary">Hủy</a>
        </form>

    </div>
@endsection
