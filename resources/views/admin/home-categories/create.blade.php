@extends('admin.layouts.app')
@section('title', 'Thêm danh mục trang chủ')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Thêm danh mục trang chủ mới</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.home-categories.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="name" name="name" 
                placeholder="VD: Sofa, Giường, Bàn làm việc..." value="{{ old('name') }}" required>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Ảnh <span class="text-danger">*</span></label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
            <div class="form-text">Chấp nhận: JPG, JPEG, PNG, WEBP. Tối đa: 4MB</div>
            <div id="imagePreview" class="mt-2"></div>
        </div>

        <div class="mb-3">
            <label for="link" class="form-label">Link (URL khi click vào ảnh)</label>
            <input type="text" class="form-control" id="link" name="link" 
                placeholder="VD: /danh-muc/sofa" value="{{ old('link') }}">
        </div>

        <div class="mb-3">
            <label for="sort_order" class="form-label">Thứ tự hiển thị</label>
            <input type="number" class="form-control" id="sort_order" name="sort_order" 
                value="{{ old('sort_order', 0) }}" min="0">
            <div class="form-text">Số nhỏ hơn sẽ hiển thị trước</div>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Trạng thái</label>
            <select class="form-select" name="status" required>
                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tạm ẩn</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Thêm danh mục
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

