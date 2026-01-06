@extends('admin.layouts.app')

@section('title', 'Quản lý bài viết')

@section('content')
<div class="container-fluid py-3">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3 gap-3">
        <div>
            <p class="text-uppercase text-muted small mb-1">Trung tâm nội dung · Admin</p>
            <h3 class="fw-semibold mb-1">Quản lý bài viết</h3>
            <p class="text-muted mb-0">Quản lý toàn bộ vòng đời nội dung: lọc, điều phối, xuất bản, lên lịch.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Thêm bài viết
            </a>
        </div>
    </div>

    {{-- Bộ lọc nâng cao --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form id="filterForm" class="row g-3 align-items-end" method="GET" action="{{ route('admin.blogs.list') }}">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small text-muted">Tìm kiếm (tiêu đề / slug)</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="search" name="keyword" class="form-control" placeholder="Nhập tiêu đề, slug..."
                               value="{{ request('keyword') }}" id="keywordInput">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small text-muted">Trạng thái</label>
                    <select name="status" class="form-select" id="statusFilter">
                        <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>Tất cả</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small text-muted">Tác giả</label>
                    <select name="author" class="form-select">
                        <option value="">Tất cả</option>
                        @foreach ($authors as $author)
                            <option value="{{ $author->id }}" {{ request('author') == $author->id ? 'selected' : '' }}>
                                {{ $author->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small text-muted">Khoảng thời gian</label>
                    <div class="row g-2">
                        <div class="col-12 col-sm-6">
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-12 col-sm-6">
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small text-muted">Sắp xếp theo</label>
                    <select name="sort_by" class="form-select">
                        <option value="created_at" {{ request('sort_by', 'created_at') === 'created_at' ? 'selected' : '' }}>Ngày tạo</option>
                        <option value="published_at" {{ request('sort_by') === 'published_at' ? 'selected' : '' }}>Ngày xuất bản</option>
                        <option value="view_count" {{ request('sort_by') === 'view_count' ? 'selected' : '' }}>Lượt xem</option>
                        <option value="updated_at" {{ request('sort_by') === 'updated_at' ? 'selected' : '' }}>Cập nhật</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small text-muted">Thứ tự</label>
                    <select name="sort_order" class="form-select">
                        <option value="desc" {{ request('sort_order', 'desc') === 'desc' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Cũ trước</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-1"></i> Áp dụng bộ lọc
                    </button>
                </div>
                <div class="col-lg-2 col-md-6">
                    <a href="{{ route('admin.blogs.list') }}" class="btn btn-outline-secondary w-100">Đặt lại</a>
                </div>
            </form>
        </div>
    </div>

    @if ($blogs->isEmpty())
        <div class="card">
            <div class="card-body text-center text-muted">
                Chưa có bài viết nào. <a href="{{ route('admin.blogs.create') }}">Tạo bài viết đầu tiên</a>.
            </div>
        </div>
    @else
        <form id="bulkActionForm" method="POST" action="{{ route('admin.blogs.bulk-action') }}">
            @csrf
            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:40px;"><input type="checkbox" id="selectAll"></th>
                                <th>Tiêu đề</th>
                                <th>Tác giả</th>
                                <th>Trạng thái</th>
                                <th>Lượt xem</th>
                                <th>Thời gian publish</th>
                                <th>Cập nhật</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($blogs as $blog)
                                @php
                                    $isScheduled = $blog->status === 'published' && $blog->published_at && $blog->published_at->isFuture();
                                    $statusLabel = $blog->status === 'draft' ? 'Nháp' : ($isScheduled ? 'Đã lên lịch' : 'Đã xuất bản');
                                @endphp
                                <tr>
                                    <td>
                                        <input type="checkbox" class="row-checkbox" name="ids[]" value="{{ $blog->id }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="ratio ratio-4x3" style="width:80px; max-width:80px;">
                                                @if ($blog->thumbnail)
                                                    <img src="{{ asset('blogs/images/' . $blog->thumbnail) }}" class="rounded border" alt="{{ $blog->title }}" style="object-fit:cover;">
                                                @else
                                                    <div class="bg-light border rounded d-flex align-items-center justify-content-center text-muted">N/A</div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ \Illuminate\Support\Str::limit($blog->title, 60) }}</div>
                                                <div class="text-muted small">/{{ $blog->slug }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $blog->user->name ?? 'N/A' }}</td>
                                    <td>
                                        @if ($statusLabel === 'Nháp')
                                            <span class="badge bg-secondary">Nháp</span>
                                        @elseif($statusLabel === 'Đã lên lịch')
                                            <span class="badge bg-warning text-dark">Đã lên lịch</span>
                                        @else
                                            <span class="badge bg-success">Đã xuất bản</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($blog->view_count ?? 0) }}</td>
                                    <td>{{ $blog->published_at ? $blog->published_at->format('d/m/Y H:i') : '—' }}</td>
                                    <td>{{ $blog->updated_at?->format('d/m/Y H:i') }}</td>
                                    <td class="text-end">
                                        <div class="d-inline-flex flex-wrap justify-content-end gap-1">
                                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.blogs.preview', $blog->id) }}" target="_blank" title="Xem trước">
                                                <i class="bi bi-aspect-ratio"></i>
                                            </a>
                                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.blogs.edit', $blog->id) }}" title="Chỉnh sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-success toggle-status-btn" data-id="{{ $blog->id }}" title="Đổi trạng thái">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                            <a class="btn btn-sm btn-outline-dark" href="{{ route('admin.blogs.show', $blog->id) }}" title="Xem chi tiết">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.blogs.destroy', $blog->id) }}" method="POST" class="d-inline-block"
                                                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 p-3 border-top bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <select name="action" class="form-select form-select-sm" style="max-width:200px;" required>
                            <option value="">Hành động hàng loạt</option>
                            <option value="publish">Xuất bản</option>
                            <option value="unpublish">Ẩn (chuyển về nháp)</option>
                            <option value="delete">Xóa</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary" id="applyBulkBtn" disabled>Thực hiện</button>
                    </div>
                    <div class="ms-auto">
                        {{ $blogs->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection

@push('scripts')
<script>
    const filterForm = document.getElementById('filterForm');
    const keywordInput = document.getElementById('keywordInput');
    const selectAll = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const applyBulkBtn = document.getElementById('applyBulkBtn');
    const filtersKey = 'admin_blog_filters';

    // Lưu & khôi phục bộ lọc (localStorage)
    if (filterForm) {
        const savedFilters = localStorage.getItem(filtersKey);
        if (savedFilters) {
            const data = JSON.parse(savedFilters);
            [...filterForm.elements].forEach(el => {
                if (!el.name || el.value) return;
                if (data[el.name]) el.value = data[el.name];
            });
        }

        filterForm.addEventListener('change', () => {
            const payload = {};
            [...filterForm.elements].forEach(el => {
                if (el.name) payload[el.name] = el.value;
            });
            localStorage.setItem(filtersKey, JSON.stringify(payload));
        });
    }

    // Tìm kiếm realtime với debounce 500ms
    let debounceTimer;
    if (keywordInput) {
        keywordInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => filterForm?.submit(), 500);
        });
    }

    // Bulk select
    const updateBulkState = () => {
        const anyChecked = [...rowCheckboxes].some(cb => cb.checked);
        applyBulkBtn.disabled = !anyChecked;
        if (selectAll) {
            const allChecked = [...rowCheckboxes].every(cb => cb.checked);
            selectAll.checked = allChecked;
        }
    };

    if (selectAll) {
        selectAll.addEventListener('change', () => {
            rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
            updateBulkState();
        });
    }
    rowCheckboxes.forEach(cb => cb.addEventListener('change', updateBulkState));
    updateBulkState();

    // Inline toggle status
    const toggleButtons = document.querySelectorAll('.toggle-status-btn');
    toggleButtons.forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            btn.disabled = true;
            try {
                const res = await fetch(`{{ url('admin/blogs/toggle-status') }}/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });
                if (res.ok) {
                    location.reload();
                } else {
                    alert('Không cập nhật được trạng thái.');
                }
            } catch (e) {
                alert('Lỗi mạng, thử lại.');
            } finally {
                btn.disabled = false;
            }
        });
    });
</script>
@endpush


