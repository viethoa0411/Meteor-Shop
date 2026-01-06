@extends('admin.layouts.app')

@section('title','Thêm bài viết')

@php
    $flattenCategories = function ($items, $prefix = '') use (&$flattenCategories) {
        $options = [];
        foreach ($items as $item) {
            $options[] = ['id' => $item->id, 'label' => $prefix . $item->name];
            if ($item->children && $item->children->count()) {
                $options = array_merge($options, $flattenCategories($item->children, $prefix . '— '));
            }
        }
        return $options;
    };
    $categoryOptions = $flattenCategories($categories);
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
            <h3 class="fw-semibold mb-1">Thêm bài viết mới</h3>
            <p class="text-muted mb-0">Tự động lưu nháp mỗi 45s, hỗ trợ xem trước và lên lịch xuất bản.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.blogs.list') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
            <button type="button" class="btn btn-outline-info" id="previewBtn" disabled>
                <i class="bi bi-eyeglasses"></i> Xem trước
            </button>
            <button type="button" class="btn btn-secondary" id="saveDraftBtn">
                <i class="bi bi-save"></i> Lưu nháp
            </button>
            <button type="submit" form="postForm" class="btn btn-primary" id="publishBtn">
                <i class="bi bi-rocket-takeoff"></i> Xuất bản
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

    <form id="postForm" action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="blog_id" id="blog_id" value="{{ old('blog_id') }}">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" id="title" value="{{ old('title') }}" required>
                            <div class="form-help">H1 chính của bài viết. Tự động tạo slug.</div>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Đường dẫn (slug)</label>
                                <input type="text" class="form-control @error('slug') is-invalid @enderror" name="slug" id="slug" value="{{ old('slug') }}">
                                <div class="form-help">Tự sinh theo tiêu đề, có thể chỉnh tay.</div>
                                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mô tả ngắn</label>
                                <input type="text" class="form-control @error('excerpt') is-invalid @enderror" name="excerpt" value="{{ old('excerpt') }}" maxlength="500">
                                <div class="form-help">Tối đa 500 ký tự, phục vụ SEO/snippet.</div>
                                @error('excerpt')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">Nội dung <span class="text-danger">*</span></label>
                            <textarea class="form-control editor-height @error('content') is-invalid @enderror" name="content" id="content">{{ old('content') }}</textarea>
                            @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Thiết lập SEO</h6>
                        <div class="mb-3">
                            <label class="form-label">Tiêu đề SEO</label>
                            <input type="text" class="form-control @error('seo_title') is-invalid @enderror" name="seo_title" value="{{ old('seo_title') }}">
                            @error('seo_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả meta</label>
                            <textarea class="form-control @error('seo_description') is-invalid @enderror" name="seo_description" rows="2" maxlength="160">{{ old('seo_description') }}</textarea>
                            <div class="form-help">Tối đa 160 ký tự.</div>
                            @error('seo_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Đường dẫn chuẩn (canonical URL)</label>
                            <input type="url" class="form-control @error('canonical_url') is-invalid @enderror" name="canonical_url" value="{{ old('canonical_url') }}">
                            @error('canonical_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="noindex" id="noindex" {{ old('noindex') ? 'checked' : '' }}>
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
                                    <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Nháp</option>
                                    <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Đã xuất bản</option>
                                    <option value="scheduled" {{ old('status') === 'scheduled' ? 'selected' : '' }}>Đã lên lịch</option>
                                </select>
                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Thời gian xuất bản</label>
                                <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" name="published_at" value="{{ old('published_at') }}">
                                <div class="form-help">Bỏ trống để publish ngay.</div>
                                @error('published_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tác giả <span class="text-danger">*</span></label>
                                <select name="author_id" class="form-select @error('author_id') is-invalid @enderror" required>
                                    <option value="">Chọn tác giả</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ old('author_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('author_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <label class="form-label">Danh mục</label>
                            <select name="categories[]" class="form-select" multiple size="6">
                                @foreach ($categoryOptions as $option)
                                    <option value="{{ $option['id'] }}" {{ collect(old('categories', []))->contains($option['id']) ? 'selected' : '' }}>
                                        {{ $option['label'] }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-help">Chọn đa cấp, giữ Ctrl/Cmd để chọn nhiều.</div>
                            @error('categories')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <label class="form-label">Thẻ (tags)</label>
                            <select name="tags[]" class="form-select" multiple size="5">
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->id }}" {{ collect(old('tags', []))->contains($tag->id) ? 'selected' : '' }}>
                                        {{ $tag->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-help">Autocomplete qua ô tìm kiếm (Ctrl/Cmd + F) của trình duyệt.</div>
                            @error('tags')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <label class="form-label">Ảnh đại diện</label>
                            <input type="file" class="form-control @error('thumbnail') is-invalid @enderror" name="thumbnail" id="thumbnail" accept="image/*">
                            <div class="form-help">JPEG, PNG, WEBP. Tối đa 2MB.</div>
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
                                <div class="text-muted small" id="autosaveStatus">Chưa lưu</div>
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
    const previewBtn = document.getElementById('previewBtn');
    const blogIdInput = document.getElementById('blog_id');
    const autosaveStatus = document.getElementById('autosaveStatus');
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
        // giữ nguyên status đã chọn (published/scheduled)
    });

    const autosave = async () => {
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
                previewBtn.disabled = false;
                autosaveStatus.textContent = 'Đã lưu tự động';
            } else {
                autosaveStatus.textContent = 'Autosave lỗi';
            }
        } catch (error) {
            autosaveStatus.textContent = 'Autosave lỗi';
        }
    };
    setInterval(autosave, 45000);

    previewBtn?.addEventListener('click', () => {
        if (!blogIdInput.value) return;
        const url = "{{ route('admin.blogs.preview', ['id' => '__ID__']) }}".replace('__ID__', blogIdInput.value);
        window.open(url, '_blank');
    });
</script>
@endpush


