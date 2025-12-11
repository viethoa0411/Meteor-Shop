@extends('admin.layouts.app')
@section('title', 'Bình luận chờ duyệt')

@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="fw-bold text-warning mb-0">
                    <i class="bi bi-clock-history me-2"></i>Bình luận chờ duyệt
                    <span class="badge bg-warning text-dark ms-2">{{ $reviews->total() }}</span>
                </h3>
                <a href="{{ route('admin.comments.index') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="GET" class="mb-3">
                <div class="row g-2">
                    <div class="col-md-8">
                        <input type="text" name="search" class="form-control" 
                               value="{{ request('search') }}" placeholder="Tìm kiếm theo nội dung, sản phẩm...">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Tìm kiếm
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="50"><input type="checkbox" id="selectAll"></th>
                            <th>Sản phẩm</th>
                            <th>User</th>
                            <th>Rating</th>
                            <th>Nội dung</th>
                            <th>Ngày</th>
                            <th width="200" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr data-review-id="{{ $review->id }}">
                                <td><input type="checkbox" class="form-check-input review-checkbox" value="{{ $review->id }}"></td>
                                <td>
                                    <a href="{{ route('admin.products.show', $review->product_id) }}" target="_blank" class="text-decoration-none">
                                        {{ Str::limit($review->product->name ?? 'N/A', 40) }}
                                    </a>
                                </td>
                                <td>
                                    <div class="small">{{ $review->user->name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ Str::limit($review->user->email ?? '', 30) }}</small>
                                </td>
                                <td>
                                    <div class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            {{ $i <= $review->rating ? '★' : '☆' }}
                                        @endfor
                                    </div>
                                    <small class="text-muted">{{ $review->rating }}/5</small>
                                </td>
                                <td>{{ Str::limit($review->content ?? $review->comment, 80) }}</td>
                                <td><small class="text-muted">{{ $review->created_at->format('d/m/Y H:i') }}</small></td>
                                <td>
                                    <div class="btn-group btn-group-sm w-100">
                                        <button type="button" class="btn btn-outline-primary" onclick="quickView({{ $review->id }})" title="Xem nhanh">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <a href="{{ route('admin.comments.show', $review->id) }}" class="btn btn-outline-info" title="Chi tiết">
                                            <i class="bi bi-info-circle"></i>
                                        </a>
                                        <button class="btn btn-success" onclick="approveReview({{ $review->id }})" title="Phê duyệt">
                                            <i class="bi bi-check"></i>
                                        </button>
                                        <button class="btn btn-danger" onclick="rejectReview({{ $review->id }})" title="Từ chối">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                    <p class="text-muted mb-0">Không có bình luận nào chờ duyệt</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($reviews->hasPages())
                <div class="border-top bg-light py-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-success btn-sm" onclick="bulkApprove()">
                            <i class="bi bi-check-all"></i> Phê duyệt đã chọn
                        </button>
                        <div class="text-muted small">
                            <i class="bi bi-info-circle text-primary me-1"></i>
                            Hiển thị <strong>{{ $reviews->firstItem() }}</strong>–<strong>{{ $reviews->lastItem() }}</strong>
                            trên tổng <strong>{{ $reviews->total() }}</strong> bình luận
                        </div>
                    </div>
                    <div>
                        {{ $reviews->appends(request()->except('page'))->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            @else
                <div class="mt-3">
                    <button class="btn btn-success btn-sm" onclick="bulkApprove()">
                        <i class="bi bi-check-all"></i> Phê duyệt đã chọn
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

@include('admin.comments.partials.quick-view-modal')

@push('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';
    let selectedReviews = new Set();

    function showToast(type, message) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        Toast.fire({ icon: type, title: message });
    }

    function showLoading() {
        document.getElementById('loadingOverlay')?.classList.remove('d-none');
    }

    function hideLoading() {
        document.getElementById('loadingOverlay')?.classList.add('d-none');
    }

    function quickView(id) {
        const modal = new bootstrap.Modal(document.getElementById('quickViewModal'));
        const content = document.getElementById('quickViewContent');
        content.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
        modal.show();
        fetch(`/admin/comments/${id}/quick-view`, {
            headers: {'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'}
        })
        .then(res => res.json())
        .then(data => content.innerHTML = data.html || '<div class="alert alert-danger">Không thể tải dữ liệu</div>')
        .catch(() => content.innerHTML = '<div class="alert alert-danger">Có lỗi xảy ra</div>');
    }

    function approveReview(id) {
        Swal.fire({
            title: 'Xác nhận',
            text: 'Phê duyệt bình luận này?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            confirmButtonText: 'Phê duyệt'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                fetch(`/admin/comments/${id}/approve`, {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json'}
                })
                .then(res => res.json())
                .then(data => {
                    hideLoading();
                    if (data.status === 'success') {
                        showToast('success', data.message);
                        document.querySelector(`tr[data-review-id="${id}"]`)?.remove();
                        if (document.querySelectorAll('tbody tr').length === 1) location.reload();
                    }
                });
            }
        });
    }

    function rejectReview(id) {
        Swal.fire({
            title: 'Xác nhận',
            text: 'Từ chối bình luận này?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Từ chối'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                fetch(`/admin/comments/${id}/reject`, {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json'}
                })
                .then(res => res.json())
                .then(data => {
                    hideLoading();
                    if (data.status === 'success') {
                        showToast('success', data.message);
                        document.querySelector(`tr[data-review-id="${id}"]`)?.remove();
                        if (document.querySelectorAll('tbody tr').length === 1) location.reload();
                    }
                });
            }
        });
    }

    function bulkApprove() {
        const ids = Array.from(document.querySelectorAll('.review-checkbox:checked')).map(cb => cb.value);
        if (ids.length === 0) {
            showToast('warning', 'Vui lòng chọn ít nhất một bình luận');
            return;
        }
        Swal.fire({
            title: 'Xác nhận',
            text: `Phê duyệt ${ids.length} bình luận?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            confirmButtonText: 'Phê duyệt'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                fetch('/admin/comments/bulk-approve', {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json'},
                    body: JSON.stringify({ ids })
                })
                .then(res => res.json())
                .then(data => {
                    hideLoading();
                    if (data.status === 'success') {
                        showToast('success', data.message);
                        location.reload();
                    }
                });
            }
        });
    }

    document.getElementById('selectAll')?.addEventListener('change', function() {
        document.querySelectorAll('.review-checkbox').forEach(cb => cb.checked = this.checked);
    });
</script>
@endpush

@push('styles')
<style>
    /* Pagination Wrapper - Beautiful Design */
    .pagination-wrapper {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-top: 2px solid #e9ecef;
        border-radius: 0 0 0.5rem 0.5rem;
        margin-top: 0;
    }

    .pagination-info {
        display: flex;
        align-items: center;
    }

    .pagination-info i {
        font-size: 1.1rem;
    }

    .pagination-container {
        background: white;
        padding: 0.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        display: inline-flex;
    }

    /* Navigation Arrow Buttons */
    .pagination-nav-btn {
        width: 3rem;
        height: 3rem;
        border-radius: 0.5rem;
        border: 1.5px solid #e9ecef;
        background: #ffffff;
        color: #495057;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 1.25rem;
        font-weight: 600;
        cursor: pointer;
    }

    .pagination-nav-btn:hover:not(.pagination-nav-btn-disabled) {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        border-color: #0d6efd;
        color: white;
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }

    .pagination-nav-btn-disabled {
        opacity: 0.4;
        cursor: not-allowed;
        background-color: #f8f9fa;
        border-color: #e9ecef;
    }

    .pagination-numbers {
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }

    .pagination {
        margin-bottom: 0;
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.375rem;
        align-items: center;
    }

    .pagination .page-link {
        padding: 0.625rem 0.875rem;
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: 0.5rem;
        margin: 0;
        min-width: 2.75rem;
        height: 2.75rem;
        text-align: center;
        border: 1.5px solid #e9ecef;
        color: #495057;
        background-color: #ffffff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .pagination .page-link:hover {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        border-color: #0d6efd;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }

    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        border-color: #0d6efd;
        color: white;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.4);
        transform: scale(1.05);
    }

    .pagination .page-item.disabled .page-link {
        opacity: 0.4;
        cursor: not-allowed;
        pointer-events: none;
    }

    /* Hide default Previous/Next from Laravel pagination */
    .pagination .page-item:first-child,
    .pagination .page-item:last-child {
        display: none;
    }

    @media (max-width: 768px) {
        .pagination-wrapper {
            padding: 0.75rem !important;
        }

        .pagination {
            font-size: 0.8rem;
            gap: 0.25rem;
        }
        
        .pagination .page-link {
            padding: 0.5rem 0.625rem;
            font-size: 0.8rem;
            min-width: 2.25rem;
            height: 2.25rem;
        }

        .pagination-container {
            padding: 0.375rem;
        }

        .pagination-nav-btn {
            width: 2.5rem;
            height: 2.5rem;
            font-size: 1rem;
        }
    }
</style>
@endpush
@endsection
