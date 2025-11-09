@extends('admin.layouts.app')

@section('title', 'Chi tiết blog')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">Chi tiết bài viết</h1>
            <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-journal-text"></i> {{ $blog->title }}</h5>
            </div>

            <div class="card-body">
                @if ($blog->thumbnail)
                    <div class="mb-4 text-center">
                        <img src="{{ asset('blog/images/' . $blog->thumbnail) }}" alt="{{ $blog->title }}" 
                             class="img-fluid" style="max-height: 400px; border-radius: 8px;">
                    </div>
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Người tạo:</strong>
                        <p>{{ $blog->user->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Trạng thái:</strong>
                        <p>
                            @if ($blog->status === 'published')
                                <span class="badge bg-success">Hoạt động</span>
                            @else
                                <span class="badge bg-warning text-dark">Dừng hoạt động</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Slug:</strong>
                        <p><code>{{ $blog->slug }}</code></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Ngày tạo:</strong>
                        <p>{{ $blog->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                @if ($blog->excerpt)
                    <div class="mb-3">
                        <strong>Mô tả ngắn:</strong>
                        <p>{{ $blog->excerpt }}</p>
                    </div>
                @endif

                <div class="mb-3">
                    <strong>Nội dung:</strong>
                    <div class="mt-2 p-3 bg-light rounded" style="white-space: pre-wrap;">{{ $blog->content }}</div>
                </div>

                <div class="mb-3">
                    <strong>Cập nhật lần cuối:</strong>
                    <p>{{ $blog->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <div class="card-footer text-end">
                <a href="{{ route('admin.blogs.edit', $blog->id) }}" class="btn btn-info">
                    <i class="bi bi-pencil-square"></i> Sửa
                </a>

                <form action="#" method="POST" class="d-inline"
                      onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này không?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Xóa
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

