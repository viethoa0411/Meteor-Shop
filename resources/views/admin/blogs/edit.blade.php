@extends('admin.layouts.app')

@section('title', 'Sửa blog')

@push('styles')
<style>
    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #dc3545;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6 .4.4.4-.4m0 4.8-.4-.4-.4.4'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: #dc3545;
    }
</style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Sửa bài viết</h4>
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

                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('admin.blogs.update', $blog->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- Tiêu đề --}}
                            <div class="mb-3">
                                <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                    value="{{ old('title', $blog->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback d-block" style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    {{-- Tác giả --}}
                                    <div class="mb-3">
                                        <label for="author" class="form-label">Tác giả <span class="text-danger">*</span></label>
                                        <input type="text" name="author" class="form-control @error('author') is-invalid @enderror" 
                                               id="author" value="{{ old('author', $blog->user ? ($blog->user->name ?? $blog->user->email) : '') }}" 
                                               placeholder="Nhập tên hoặc email tác giả" required>
                                        <small class="form-text text-muted">Nhập tên hoặc email của tác giả (phải tồn tại trong hệ thống).</small>
                                        @error('author')
                                            <div class="invalid-feedback d-block" style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    {{-- Trạng thái --}}
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                        @php
                                            $currentStatus = old('status', $blog->status === 'published' ? 'active' : 'inactive');
                                        @endphp
                                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                            <option value="active" {{ $currentStatus == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                            <option value="inactive" {{ $currentStatus == 'inactive' ? 'selected' : '' }}>Dừng hoạt động</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback d-block" style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    {{-- Ảnh đại diện --}}
                                    <div class="mb-3">
                                        <label for="thumbnail" class="form-label">Ảnh đại diện</label>
                                        @if ($blog->thumbnail)
                                            <div class="mb-2">
                                                <img src="{{ asset('blogs/images/' . $blog->thumbnail) }}" alt="{{ $blog->title }}" 
                                                     style="max-width: 200px; max-height: 150px; border-radius: 4px;">
                                                <p class="text-muted small mt-1">Ảnh hiện tại</p>
                                            </div>
                                        @endif
                                        <input type="file" name="thumbnail" class="form-control @error('thumbnail') is-invalid @enderror" 
                                               id="thumbnail" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                                        <small class="form-text text-muted">Để trống nếu không muốn thay đổi. Định dạng: jpeg, png, jpg, gif, webp. Tối đa 2MB.</small>
                                        @error('thumbnail')
                                            <div class="invalid-feedback d-block" style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Mô tả ngắn --}}
                            <div class="mb-3">
                                <label for="excerpt" class="form-label">Mô tả ngắn</label>
                                <textarea name="excerpt" class="form-control @error('excerpt') is-invalid @enderror" 
                                          id="excerpt" rows="3" maxlength="500">{{ old('excerpt', $blog->excerpt) }}</textarea>
                                <small class="form-text text-muted">Tối đa 500 ký tự.</small>
                                @error('excerpt')
                                    <div class="invalid-feedback d-block" style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Nội dung --}}
                            <div class="mb-3">
                                <label for="content" class="form-label">Nội dung <span class="text-danger">*</span></label>
                                <textarea name="content" class="form-control @error('content') is-invalid @enderror" 
                                          id="content" rows="10" required>{{ old('content', $blog->content) }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback d-block" style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.blogs.list') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Quay lại
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Cập nhật
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

