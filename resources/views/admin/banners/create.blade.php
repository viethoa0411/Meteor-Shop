@extends('admin.layouts.app')
@section('title', 'Thêm mới Banner')

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="bi bi-plus-circle me-2"></i>Thêm mới Banner
                </h4>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        {{-- Cột trái --}}
                        <div class="col-md-8">
                            {{-- Tiêu đề --}}
                            <div class="mb-3">
                                <label for="title" class="form-label">
                                    Tiêu đề banner <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                    name="title" value="{{ old('title') }}" placeholder="Nhập tiêu đề banner" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- Link --}}
                            <div class="mb-3">
                                <label for="link" class="form-label">Liên kết (URL)</label>
                                <input type="url" class="form-control @error('link') is-invalid @enderror" id="link"
                                    name="link" value="{{ old('link') }}" placeholder="https://example.com">
                                <small class="form-text text-muted">Link đích khi người dùng click vào banner</small>
                                @error('link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Thứ tự --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="sort_order" class="form-label">Thứ tự hiển thị</label>
                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                        id="sort_order" name="sort_order" value="{{ old('sort_order') }}" min="0"
                                        placeholder="Tự động">
                                    <small class="form-text text-muted">Số càng nhỏ, hiển thị càng trước</small>
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Trạng thái --}}
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">
                                        Trạng thái <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status"
                                        name="status" required>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Hoạt động
                                        </option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tạm ẩn
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Thời gian hiển thị --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="start_date" class="form-label">Ngày bắt đầu</label>
                                    <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror"
                                        id="start_date" name="start_date" value="{{ old('start_date') }}">
                                    <small class="form-text text-muted">Để trống = hiển thị ngay</small>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="end_date" class="form-label">Ngày kết thúc</label>
                                    <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror"
                                        id="end_date" name="end_date" value="{{ old('end_date') }}">
                                    <small class="form-text text-muted">Để trống = hiển thị mãi</small>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Cột phải --}}
                        <div class="col-md-4">
                            {{-- Upload ảnh --}}
                            <div class="mb-3">
                                <label for="image" class="form-label">
                                    Hình ảnh banner <span class="text-danger">*</span>
                                </label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image"
                                    name="image" accept="image/*" required onchange="previewImage(this)">
                                <small class="form-text text-muted">
                                    Định dạng: JPG, PNG, WEBP. Tối đa 5MB
                                </small>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                {{-- Preview ảnh --}}
                                <div class="mt-3" id="imagePreview" style="display: none;">
                                    <img id="previewImg" src="" alt="Preview" class="img-fluid rounded shadow-sm"
                                        style="max-height: 300px; width: 100%; object-fit: contain;">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Nút hành động --}}
                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <a href="{{ route('admin.banners.list') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Lưu & Hiển thị ngay
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function previewImage(input) {
                const preview = document.getElementById('imagePreview');
                const previewImg = document.getElementById('previewImg');

                if (input.files && input.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        preview.style.display = 'block';
                    }

                    reader.readAsDataURL(input.files[0]);
                } else {
                    preview.style.display = 'none';
                }
            }
        </script>
    @endpush
@endsection

