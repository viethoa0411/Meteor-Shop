@extends('admin.layouts.app')
@section('title', 'Sửa danh mục trang chủ')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Sửa danh mục trang chủ</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.home-categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="name" name="name" 
                value="{{ old('name', $category->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Ảnh</label>
            @if ($category->image)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $category->image) }}" 
                        alt="{{ $category->name }}" 
                        class="img-thumbnail" style="max-height: 150px;">
                    <p class="text-muted small mt-1">Ảnh hiện tại</p>
                </div>
            @endif
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
            <div class="form-text">Để trống nếu không muốn thay đổi ảnh. Chấp nhận: JPG, JPEG, PNG, WEBP. Tối đa: 4MB</div>
            <div id="imagePreview" class="mt-2"></div>
        </div>

        <div class="mb-3">
            <label for="link" class="form-label">Link (URL khi click vào ảnh)</label>
            <input type="text" class="form-control" id="link" name="link" 
                placeholder="VD: /danh-muc/sofa" value="{{ old('link', $category->link) }}">
        </div>

        <div class="mb-3">
            <label for="sort_order" class="form-label">Thứ tự hiển thị</label>
            <input type="number" class="form-control" id="sort_order" name="sort_order" 
                value="{{ old('sort_order', $category->sort_order) }}" min="0">
            <div class="form-text">Số nhỏ hơn sẽ hiển thị trước</div>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Trạng thái</label>
            <select class="form-select" name="status" required>
                <option value="active" {{ old('status', $category->status) == 'active' ? 'selected' : '' }}>Hoạt động</option>
                <option value="inactive" {{ old('status', $category->status) == 'inactive' ? 'selected' : '' }}>Tạm ẩn</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle"></i> Cập nhật
        </button>
        <a href="{{ route('admin.home-categories.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>

@push('scripts')
<script>
document.getElementById('image').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="img-thumbnail" style="max-height: 150px;">`;
        }
        reader.readAsDataURL(this.files[0]);
    }
});
</script>
@endpush
@endsection

