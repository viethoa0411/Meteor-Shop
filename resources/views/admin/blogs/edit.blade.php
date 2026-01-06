@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa bài viết')

@php
    $isScheduled = $blog->status === 'published' && $blog->published_at && $blog->published_at->isFuture();
    $currentStatus = $blog->status === 'draft' ? 'draft' : ($isScheduled ? 'scheduled' : 'published');
    $publishedAtValue = old('published_at', $blog->published_at ? $blog->published_at->format('Y-m-d\TH:i') : '');
@endphp

@push('styles')
<style>
    .form-help { color: #6c757d; font-size: 0.85rem; }
    .sticky-panel { position: sticky; top: 90px; }
    .editor-height { min-height: 420px; }
    .badge-inline { background: #eef2ff; color: #3b48c8; border: 1px solid #dfe3ff; }
</style>
@endpush

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3 gap-2">
        <div>
            <p class="text-uppercase text-muted small mb-1">Trung tâm nội dung</p>
            <h3 class="fw-semibold mb-1">Chỉnh sửa bài viết</h3>
            <p class="text-muted mb-0">Cập nhật nội dung, hỗ trợ tự động lưu và xem trước.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.blogs.list') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại danh sách
            </a>
            <a href="{{ route('admin.blogs.preview', $blog->id) }}" class="btn btn-outline-info" target="_blank">
                <i class="bi bi-eyeglasses"></i> Xem trước
            </a>
            <button type="button" class="btn btn-secondary" id="saveDraftBtn">
                <i class="bi bi-save"></i> Lưu nháp
            </button>
            <button type="submit" form="postForm" class="btn btn-primary" id="publishBtn">
                <i class="bi bi-arrow-repeat"></i> Cập nhật bài viết
            </button>
        </div>
    </div>

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

    <form id="postForm" action="{{ route('admin.blogs.update', $blog->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="blog_id" id="blog_id" value="{{ $blog->id }}">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" id="title" value="{{ old('title', $blog->title) }}" required>
                            <div class="form-help">H1 chính của bài viết.</div>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Đường dẫn (slug)</label>
                                <input type="text" class="form-control @error('slug') is-invalid @enderror" name="slug" id="slug" value="{{ old('slug', $blog->slug) }}">
                                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mô tả ngắn</label>
                                <input type="text" class="form-control @error('excerpt') is-invalid @enderror" name="excerpt" value="{{ old('excerpt', $blog->excerpt) }}" maxlength="500">
                                <div class="form-help">Tối đa 500 ký tự.</div>
                                @error('excerpt')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">Nội dung <span class="text-danger">*</span></label>
                            <textarea class="form-control editor-height @error('content') is-invalid @enderror" name="content" id="content">{{ old('content', $blog->content) }}</textarea>
                            @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Thiết lập SEO</h6>
                        <div class="mb-3">
                            <label class="form-label">Tiêu đề SEO</label>
                            <input type="text" class="form-control @error('seo_title') is-invalid @enderror" name="seo_title" value="{{ old('seo_title', $blog->seo_title) }}">
                            @error('seo_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả meta</label>
                            <textarea class="form-control @error('seo_description') is-invalid @enderror" name="seo_description" rows="2" maxlength="160">{{ old('seo_description', $blog->seo_description) }}</textarea>
                            <div class="form-help">Tối đa 160 ký tự.</div>
                            @error('seo_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="noindex" id="noindex" {{ old('noindex', $blog->noindex) ? 'checked' : '' }}>
                            <label class="form-check-label" for="noindex">Noindex / Nofollow</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="sticky-panel">
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" name="status" id="statusSelect" required>
                                    <option value="draft" {{ $currentStatus === 'draft' ? 'selected' : '' }}>Nháp</option>
                                    <option value="published" {{ $currentStatus === 'published' ? 'selected' : '' }}>Đã xuất bản</option>
                                    <option value="scheduled" {{ $currentStatus === 'scheduled' ? 'selected' : '' }}>Đã lên lịch</option>
                                </select>
                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Thời gian xuất bản</label>
                                <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" name="published_at" value="{{ $publishedAtValue }}">
                                <div class="form-help">Để trống để publish ngay.</div>
                                @error('published_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tác giả <span class="text-danger">*</span></label>
                                <select name="author_id" class="form-select @error('author_id') is-invalid @enderror" required>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ old('author_id', $blog->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('author_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <label class="form-label">Ảnh đại diện</label>
                            @if ($blog->thumbnail)
                                <div class="mb-2">
                                    <img src="{{ asset('blogs/images/' . $blog->thumbnail) }}" alt="{{ $blog->title }}" class="img-fluid rounded border" style="max-height:160px; object-fit:cover;">
                                    <div class="form-help mt-1">Ảnh hiện tại</div>
                                </div>
                            @endif
                            <input type="file" class="form-control @error('thumbnail') is-invalid @enderror" name="thumbnail" id="thumbnail" accept="image/*">
                            <div class="form-help">Để trống nếu không thay đổi. Tối đa 2MB.</div>
                            @error('thumbnail')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="mt-2" id="thumbPreviewWrapper" style="display:none;">
                                <img id="thumbPreview" src="#" alt="Preview" class="img-fluid rounded border">
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">Autosave</div>
                                <div class="form-check form-switch mt-1">
                                    <input class="form-check-input" type="checkbox" id="autosaveToggle">
                                    <label class="form-check-label small text-muted" for="autosaveToggle">
                                        Bật tự động lưu nháp (45s)
                                    </label>
                                </div>
                                <div class="text-muted small mt-1" id="autosaveStatus">Đang tắt</div>
                            </div>
                            <div class="text-success small"><i class="bi bi-clock-history"></i> 45s</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    const statusSelect = document.getElementById('statusSelect');
    const saveDraftBtn = document.getElementById('saveDraftBtn');
    const publishBtn = document.getElementById('publishBtn');
    const blogIdInput = document.getElementById('blog_id');
    const autosaveStatus = document.getElementById('autosaveStatus');
    const autosaveToggle = document.getElementById('autosaveToggle');
    const thumbnailInput = document.getElementById('thumbnail');
    const thumbPreviewWrapper = document.getElementById('thumbPreviewWrapper');
    const thumbPreview = document.getElementById('thumbPreview');
    let slugTouched = false;

    CKEDITOR.replace('content', { height: 420 });

    titleInput?.addEventListener('input', () => {
        if (slugTouched) return;
        const slug = titleInput.value
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
        slugInput.value = slug;
    });
    slugInput?.addEventListener('input', () => slugTouched = true);

    if (thumbnailInput) {
        thumbnailInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = ev => {
                thumbPreviewWrapper.style.display = 'block';
                thumbPreview.src = ev.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    const submitWithStatus = (statusValue) => {
        statusSelect.value = statusValue;
        document.getElementById('postForm').submit();
    };

    saveDraftBtn?.addEventListener('click', () => submitWithStatus('draft'));

    publishBtn?.addEventListener('click', (e) => {
        if (statusSelect.value === 'scheduled' && !document.querySelector('[name="published_at"]').value) {
            e.preventDefault();
            alert('Vui lòng chọn thời gian publish khi chọn Scheduled.');
            return;
        }
    });

    const autosave = async () => {
        if (!autosaveToggle || !autosaveToggle.checked) {
            return;
        }
        const formData = new FormData();
        formData.append('title', titleInput.value);
        formData.append('excerpt', document.querySelector('[name="excerpt"]').value);
        formData.append('content', CKEDITOR.instances.content.getData());
        if (blogIdInput.value) formData.append('blog_id', blogIdInput.value);

        try {
            const res = await fetch('{{ route('admin.blogs.autosave') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                blogIdInput.value = data.blog_id;
                autosaveStatus.textContent = 'Đã lưu tự động';
            } else {
                autosaveStatus.textContent = 'Autosave lỗi';
            }
        } catch (error) {
            autosaveStatus.textContent = 'Autosave lỗi';
        }
    };
    setInterval(autosave, 45000);
</script>
@endpush


