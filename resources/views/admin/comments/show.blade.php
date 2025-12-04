@extends('admin.layouts.app')
@section('title', 'Chi tiết bình luận')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8">
            {{-- Review Info --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-chat-dots me-2"></i>Chi tiết bình luận</h5>
                </div>
                <div class="card-body">
                    {{-- Product Info --}}
                    <div class="mb-4 pb-4 border-bottom">
                        <h6 class="text-muted mb-2">Sản phẩm</h6>
                        <div class="d-flex align-items-center gap-3">
                            @if($review->product && $review->product->image)
                                <img src="{{ asset('storage/' . $review->product->image) }}" 
                                     alt="{{ $review->product->name }}" 
                                     style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                            @endif
                            <div>
                                <h5 class="mb-1">
                                    <a href="{{ route('admin.products.show', $review->product_id) }}" target="_blank">
                                        {{ $review->product->name ?? 'N/A' }}
                                    </a>
                                </h5>
                                <small class="text-muted">ID: {{ $review->product_id }}</small>
                            </div>
                        </div>
                    </div>

                    {{-- User Info --}}
                    <div class="mb-4 pb-4 border-bottom">
                        <h6 class="text-muted mb-2">Người đánh giá</h6>
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px;">
                                <span class="text-white fw-bold fs-4">{{ substr($review->user->name ?? 'U', 0, 1) }}</span>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ $review->user->name ?? 'N/A' }}</h6>
                                <small class="text-muted">{{ $review->user->email ?? '' }}</small><br>
                                <small class="text-muted">{{ $review->user->phone ?? '' }}</small>
                            </div>
                        </div>
                    </div>

                    {{-- Rating --}}
                    <div class="mb-4 pb-4 border-bottom">
                        <h6 class="text-muted mb-2">Đánh giá</h6>
                        <div class="text-warning fs-4 mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                {{ $i <= $review->rating ? '★' : '☆' }}
                            @endfor
                        </div>
                        <span class="badge bg-primary">{{ $review->rating }}/5 sao</span>
                    </div>

                    {{-- Helpful Count --}}
                    <div class="mb-4 pb-4 border-bottom">
                        <h6 class="text-muted mb-2">Đánh giá hữu ích</h6>
                        <span class="badge bg-primary fs-5">
                            <i class="bi bi-hand-thumbs-up-fill"></i>
                            {{ $review->helpful_votes_count ?? 0 }} lượt hữu ích
                        </span>
                    </div>

                    {{-- Content --}}
                    <div class="mb-4 pb-4 border-bottom">
                        <h6 class="text-muted mb-2">Nội dung</h6>
                        <p class="mb-0">{{ $review->content ?? $review->comment }}</p>
                    </div>

                    {{-- Images --}}
                    @if($review->images && count($review->images) > 0)
                        <div class="mb-4 pb-4 border-bottom">
                            <h6 class="text-muted mb-2">Hình ảnh đính kèm</h6>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach($review->images as $img)
                                    @php
                                        $path = is_string($img) ? trim($img, '/\\') : '';
                                        $publicPath = 'storage/' . $path;
                                    @endphp
                                    <img src="{{ asset($publicPath) }}" 
                                         alt="Review image" 
                                         class="rounded" 
                                         style="width: 120px; height: 120px; object-fit: cover; cursor: pointer;"
                                         onerror="this.onerror=null;this.src='https://via.placeholder.com/120x120?text=No+Image';"
                                         onclick="window.open(this.src, '_blank')">
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Tags --}}
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Tags</h6>
                        <div class="d-flex gap-2 flex-wrap">
                            @if($review->is_verified_purchase)
                                <span class="badge bg-success">✓ Đã mua hàng</span>
                            @endif
                            @if($review->images && count($review->images) > 0)
                                <span class="badge bg-info"><i class="bi bi-image"></i> Có hình ảnh</span>
                            @endif
                            @if($review->rating >= 4)
                                <span class="badge bg-success">Tích cực</span>
                            @elseif($review->rating <= 2)
                                <span class="badge bg-danger">Tiêu cực</span>
                            @endif
                        </div>
                    </div>

                    {{-- Status & Actions --}}
                    <div class="d-flex gap-2 flex-wrap">
                        @if($review->status != 'approved')
                            <button class="btn btn-success" onclick="approveReview({{ $review->id }})">
                                <i class="bi bi-check-circle"></i> Phê duyệt
                            </button>
                        @endif
                        @if($review->status != 'rejected')
                            <button class="btn btn-danger" onclick="rejectReview({{ $review->id }})">
                                <i class="bi bi-x-circle"></i> Từ chối
                            </button>
                        @endif
                        @if($review->status != 'hidden')
                            <button class="btn btn-warning" onclick="hideReview({{ $review->id }})">
                                <i class="bi bi-eye-slash"></i> Ẩn
                            </button>
                        @else
                            <button class="btn btn-info" onclick="showReview({{ $review->id }})">
                                <i class="bi bi-eye"></i> Hiện
                            </button>
                        @endif
                        <button class="btn btn-outline-danger" onclick="deleteReview({{ $review->id }})">
                            <i class="bi bi-trash"></i> Xóa
                        </button>
                    </div>
                </div>
            </div>

            {{-- Admin Reply --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h5 class="mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-reply me-1"></i>
                        Phản hồi của admin
                    </h5>
                    <button type="button"
                            class="btn btn-outline-danger btn-sm d-none"
                            id="btnDeleteSelectedReplies"
                            data-review-id="{{ $review->id }}"
                            disabled
                            onclick="bulkDeleteReplies(this)">
                        <i class="bi bi-trash me-1"></i>
                        Xóa <span class="selected-count">0</span> phản hồi
                    </button>
                </div>
                <div class="card-body">
                    @if($review->replies->count() > 0)
                        @foreach($review->replies as $reply)
                            <div class="border rounded p-3 mb-3 bg-light position-relative reply-item" data-reply-id="{{ $reply->id }}">
                                <div class="d-flex justify-content-between gap-2 mb-2">
                                    <div class="d-flex gap-2">
                                        <div class="form-check mt-1">
                                            <input class="form-check-input reply-checkbox"
                                                   type="checkbox"
                                                   value="{{ $reply->id }}"
                                                   data-reply-id="{{ $reply->id }}"
                                                   onchange="handleReplyCheckboxChange(this)">
                                        </div>
                                        <div>
                                            <strong>{{ $reply->admin->name ?? 'Admin' }}</strong>
                                            <small class="text-muted d-block">{{ $reply->created_at->format('d/m/Y H:i') }}</small>
                                        </div>
                                    </div>
                                    <button type="button"
                                            class="btn btn-sm btn-link text-danger px-2"
                                            onclick="deleteReply(this, {{ $review->id }}, {{ $reply->id }})">
                                        <i class="bi bi-trash"></i> Xóa
                                    </button>
                                </div>
                                <p class="mb-0">{{ $reply->content }}</p>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted mb-0">Chưa có phản hồi</p>
                    @endif

                    <form id="replyForm" class="mt-3">
                        <div class="mb-3">
                            <textarea name="content" class="form-control" rows="3" 
                                      placeholder="Nhập phản hồi của bạn..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i> Gửi phản hồi
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Audit Log History --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Lịch sử thay đổi</h5>
                </div>
                <div class="card-body">
                    @php
                        $auditLogs = \App\Models\ReviewAuditLog::where('review_id', $review->id)
                            ->with('admin')
                            ->orderBy('created_at', 'desc')
                            ->get();
                    @endphp
                    
                    @if($auditLogs->count() > 0)
                        <div class="timeline">
                            @foreach($auditLogs as $log)
                                <div class="timeline-item mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong>{{ $log->admin->name ?? 'System' }}</strong>
                                            <span class="badge bg-{{ $log->action === 'approve' ? 'success' : ($log->action === 'reject' ? 'danger' : ($log->action === 'delete' ? 'danger' : 'warning')) }} ms-2">
                                                {{ ucfirst($log->action) }}
                                            </span>
                                            @if($log->old_status && $log->new_status)
                                                <small class="text-muted d-block mt-1">
                                                    {{ $log->old_status }} → {{ $log->new_status }}
                                                </small>
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ $log->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    @if($log->notes)
                                        <p class="mb-0 mt-1 small">{{ $log->notes }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">Chưa có lịch sử thay đổi</p>
                    @endif
                </div>
            </div>

            {{-- Report History --}}
            @if($review->reports->count() > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="bi bi-flag me-2"></i>Lịch sử báo cáo ({{ $review->reports->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @foreach($review->reports as $report)
                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <strong>{{ $report->user->name ?? 'N/A' }}</strong>
                                    <small class="text-muted">{{ $report->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                                <div class="mb-2">
                                    <span class="badge bg-danger">{{ $report->reason_label }}</span>
                                </div>
                                @if($report->description)
                                    <p class="mb-0 small">{{ $report->description }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Info --}}
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Thông tin</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="background-color:rgba(245, 239, 239, 0)!important;">Trạng thái:</th>
                            <td>
                                @php
                                    $statusBadges = [
                                        'pending' => ['class' => 'warning', 'text' => 'Chờ duyệt'],
                                        'approved' => ['class' => 'success', 'text' => 'Đã duyệt'],
                                        'rejected' => ['class' => 'danger', 'text' => 'Từ chối'],
                                        'hidden' => ['class' => 'secondary', 'text' => 'Ẩn'],
                                    ];
                                    $status = $statusBadges[$review->status ?? 'pending'] ?? $statusBadges['pending'];
                                @endphp
                                <span class="badge bg-{{ $status['class'] }}">{{ $status['text'] }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th style="background-color:rgba(245, 239, 239, 0)!important;">Ngày tạo:</th>
                            <td>{{ $review->created_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th style="background-color:rgba(245, 239, 239, 0)!important;">Số lần báo cáo:</th>
                            <td><span class="badge bg-danger">{{ $review->reported_count }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';
    const replySelectedIds = new Set();

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
        const overlay = document.createElement('div');
        overlay.id = 'loadingOverlay';
        overlay.className = 'position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex justify-content-center align-items-center';
        overlay.style.zIndex = '9999';
        overlay.innerHTML = '<div class="text-center text-white"><div class="spinner-border text-light mb-3" style="width: 3rem; height: 3rem;"></div><p class="fs-5">Đang xử lý...</p></div>';
        document.body.appendChild(overlay);
    }

    function hideLoading() {
        document.getElementById('loadingOverlay')?.remove();
    }

    // Reply Form
    document.getElementById('replyForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const content = this.querySelector('textarea[name="content"]').value;
        if (!content.trim()) {
            showToast('warning', 'Vui lòng nhập nội dung phản hồi');
            return;
        }
        
        showLoading();
        fetch(`/admin/comments/{{ $review->id }}/reply`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ content })
        })
        .then(res => res.json())
        .then(data => {
            hideLoading();
            if (data.status === 'success') {
                showToast('success', data.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('error', data.message || 'Có lỗi xảy ra');
            }
        })
        .catch(err => {
            hideLoading();
            showToast('error', 'Có lỗi xảy ra');
        });
    });

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
                        setTimeout(() => location.reload(), 1000);
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
                        setTimeout(() => location.reload(), 1000);
                    }
                });
            }
        });
    }

    function hideReview(id) {
        Swal.fire({
            title: 'Xác nhận',
            text: 'Ẩn bình luận này?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            confirmButtonText: 'Ẩn'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                fetch(`/admin/comments/${id}/hide`, {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json'}
                })
                .then(res => res.json())
                .then(data => {
                    hideLoading();
                    if (data.status === 'success') {
                        showToast('success', data.message);
                        setTimeout(() => location.reload(), 1000);
                    }
                });
            }
        });
    }

    function showReview(id) {
        showLoading();
        fetch(`/admin/comments/${id}/show`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json'}
        })
        .then(res => res.json())
        .then(data => {
            hideLoading();
            if (data.status === 'success') {
                showToast('success', data.message);
                setTimeout(() => location.reload(), 1000);
            }
        });
    }

    function deleteReply(buttonEl, reviewId, replyId) {
        Swal.fire({
            title: 'Xác nhận xóa phản hồi',
            text: 'Bạn chắc chắn muốn xóa phản hồi này? Hành động không thể hoàn tác.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            showLoading();
            fetch(`/admin/comments/${reviewId}/reply/${replyId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                hideLoading();
                if (data.status === 'success') {
                    showToast('success', data.message || 'Đã xóa phản hồi');
                    try {
                        if (buttonEl) {
                            const replyCard = buttonEl.closest('.reply-item');
                            replyCard?.remove();
                        }
                        replySelectedIds.delete(String(replyId));
                        updateReplySelectionState();
                    } catch (e) {
                        console.warn('Cannot remove reply DOM node', e);
                    }
                } else {
                    showToast('error', data.message || 'Không thể xóa phản hồi');
                }
            })
            .catch(() => {
                hideLoading();
                showToast('error', 'Có lỗi xảy ra khi xóa phản hồi');
            });
        });
    }

    function handleReplyCheckboxChange(checkbox) {
        const replyId = checkbox?.dataset?.replyId;
        if (!replyId) {
            return;
        }

        if (checkbox.checked) {
            replySelectedIds.add(String(replyId));
        } else {
            replySelectedIds.delete(String(replyId));
        }
        updateReplySelectionState();
    }

    function updateReplySelectionState() {
        const bulkBtn = document.getElementById('btnDeleteSelectedReplies');
        if (!bulkBtn) {
            return;
        }
        const count = replySelectedIds.size;
        const countBadge = bulkBtn.querySelector('.selected-count');
        if (countBadge) {
            countBadge.textContent = count;
        }
        bulkBtn.disabled = count === 0;
        const hasReplies = document.querySelectorAll('.reply-item').length > 0;
        bulkBtn.classList.toggle('d-none', !hasReplies);
    }

    function bulkDeleteReplies(buttonEl) {
        const reviewId = buttonEl?.dataset?.reviewId;
        if (!reviewId || replySelectedIds.size === 0) {
            return;
        }

        Swal.fire({
            title: 'Xóa nhiều phản hồi?',
            text: `Bạn sắp xóa ${replySelectedIds.size} phản hồi đã chọn.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then(result => {
            if (!result.isConfirmed) {
                return;
            }

            const replyIds = Array.from(replySelectedIds);
            showLoading();
            fetch(`/admin/comments/${reviewId}/replies/bulk`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ reply_ids: replyIds })
            })
            .then(res => res.json())
            .then(data => {
                hideLoading();
                if (data.status === 'success') {
                    showToast('success', data.message || 'Đã xóa phản hồi');
                    (data.deleted_ids || replyIds).forEach(id => {
                        const normalizedId = String(id);
                        const card = document.querySelector(`.reply-item[data-reply-id="${normalizedId}"]`);
                        if (card) {
                            card.remove();
                        }
                        replySelectedIds.delete(normalizedId);
                    });
                    updateReplySelectionState();
                } else {
                    showToast('error', data.message || 'Không thể xóa phản hồi');
                }
            })
            .catch(() => {
                hideLoading();
                showToast('error', 'Có lỗi xảy ra khi xóa phản hồi');
            });
        });
    }

    document.addEventListener('DOMContentLoaded', updateReplySelectionState);

    function deleteReview(id) {
        Swal.fire({
            title: 'Xác nhận xóa',
            text: 'Bạn có chắc muốn xóa vĩnh viễn bình luận này? Hành động này không thể hoàn tác!',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                fetch(`/admin/comments/${id}`, {
                    method: 'DELETE',
                    headers: {'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json'}
                })
                .then(res => res.json())
                .then(data => {
                    hideLoading();
                    if (data.status === 'success') {
                        showToast('success', data.message);
                        setTimeout(() => {
                            window.location.href = '{{ route('admin.comments.index') }}';
                        }, 1000);
                    } else {
                        showToast('error', data.message || 'Có lỗi xảy ra');
                    }
                });
            }
        });
    }
</script>
@endpush


@endsection

