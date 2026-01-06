@extends('admin.layouts.app')

@section('title', 'Post Detail')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <p class="text-uppercase text-muted small mb-1">Content Hub</p>
            <h3 class="fw-semibold mb-0">{{ $blog->title }}</h3>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.blogs.list') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
            <a href="{{ route('admin.blogs.preview', $blog->id) }}" class="btn btn-outline-info" target="_blank"><i class="bi bi-aspect-ratio"></i> Preview</a>
            <a href="{{ route('admin.blogs.edit', $blog->id) }}" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit</a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    @if ($blog->thumbnail)
                        <div class="mb-3 text-center">
                            <img src="{{ asset('blogs/images/' . $blog->thumbnail) }}" alt="{{ $blog->title }}" class="img-fluid rounded" style="max-height: 360px; object-fit: cover;">
                        </div>
                    @endif
                    <div class="mb-3">
                        <span class="badge {{ $blog->status === 'draft' ? 'bg-secondary' : (($blog->published_at && $blog->published_at->isFuture()) ? 'bg-warning text-dark' : 'bg-success') }}">
                            {{ $blog->status === 'draft' ? 'Draft' : (($blog->published_at && $blog->published_at->isFuture()) ? 'Scheduled' : 'Published') }}
                        </span>
                        @if ($blog->published_at)
                            <span class="ms-2 text-muted small">Publish at: {{ $blog->published_at->format('d/m/Y H:i') }}</span>
                        @endif
                    </div>
                    @if ($blog->excerpt)
                        <p class="text-muted">{{ $blog->excerpt }}</p>
                    @endif
                    <div class="border rounded p-3 bg-light">
                        {!! $blog->content !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="fw-semibold">Thông tin</h6>
                    <div class="small text-muted">Slug</div>
                    <div class="mb-2"><code>{{ $blog->slug }}</code></div>
                    <div class="small text-muted">Tác giả</div>
                    <div class="mb-2">{{ $blog->user->name ?? 'N/A' }}</div>
                    <div class="small text-muted">Tạo lúc</div>
                    <div class="mb-2">{{ $blog->created_at->format('d/m/Y H:i') }}</div>
                    <div class="small text-muted">Cập nhật</div>
                    <div>{{ $blog->updated_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
            {{-- Quan hệ danh mục & tag đã được gỡ bỏ theo yêu cầu --}}
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-semibold">SEO</h6>
                    <div class="small text-muted">SEO Title</div>
                    <div class="mb-2">{{ $blog->seo_title ?? '—' }}</div>
                    <div class="small text-muted">Meta Description</div>
                    <div class="mb-2">{{ $blog->seo_description ?? '—' }}</div>
                    <div class="small text-muted">Canonical</div>
                    <div class="mb-2">{{ $blog->canonical_url ?? '—' }}</div>
                    <div class="small text-muted">Index</div>
                    <div>{{ $blog->noindex ? 'Noindex/Nofollow' : 'Indexable' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




