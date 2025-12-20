@extends('admin.layouts.app')
@section('title', 'Quản lý bình luận')

@section('content')
<div class="container-fluid py-4">
    {{-- Statistics Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Tổng bình luận</h6>
                            <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                        </div>
                        <i class="bi bi-chat-dots fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Chờ duyệt</h6>
                            <h3 class="mb-0">{{ $stats['pending'] ?? 0 }}</h3>
                        </div>
                        <i class="bi bi-clock-history fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Đã duyệt</h6>
                            <h3 class="mb-0">{{ $stats['approved'] ?? 0 }}</h3>
                        </div>
                        <i class="bi bi-check2-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Bị báo cáo</h6>
                            <h3 class="mb-0">{{ $stats['reported'] ?? 0 }}</h3>
                        </div>
                        <i class="bi bi-flag fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Header --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h3 class="fw-bold text-primary mb-0">
                    <i class="bi bi-chat-dots me-2"></i>Quản lý bình luận
                </h3>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.comments.pending') }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-clock-history"></i> Chờ duyệt 
                        <span class="badge bg-white text-warning ms-1">{{ isset($stats) && isset($stats['pending']) ? $stats['pending'] : 0 }}</span>
                    </a>
                    <a href="{{ route('admin.comments.reported') }}" class="btn btn-danger btn-sm">
                        <i class="bi bi-flag"></i> Bị báo cáo 
                        <span class="badge bg-white text-danger ms-1">{{ isset($stats) && isset($stats['reported']) ? $stats['reported'] : 0 }}</span>
                    </a>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="sortByHelpful()" title="Sắp xếp theo hữu ích nhất">
                        <i class="bi bi-hand-thumbs-up"></i> Hữu ích nhất
                    </button>
                    <a href="{{ route('admin.comments.settings') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-gear"></i> Cài đặt
                    </a>
                    <button type="button" class="btn btn-info btn-sm" onclick="exportReviews()">
                        <i class="bi bi-download"></i> Xuất Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Advanced Filters --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-funnel me-2"></i>Bộ lọc nâng cao</h6>
                <button type="button" class="btn btn-sm btn-link text-decoration-none" onclick="toggleFilters()">
                    <i class="bi bi-chevron-up" id="filterToggleIcon"></i>
                </button>
            </div>
        </div>
        <div class="card-body" id="filtersSection">
            <form method="GET" action="{{ route('admin.comments.index') }}" id="filterForm" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Sản phẩm</label>
                    <select name="product_id" class="form-select form-select-sm" id="productFilter">
                        <option value="">Tất cả sản phẩm</option>
                        @if(isset($products) && $products->count() > 0)
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                {{ Str::limit($product->name, 50) }}
                            </option>
                        @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Rating</label>
                    <select name="rating" class="form-select form-select-sm">
                        <option value="">Tất cả</option>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                {{ $i }} sao
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Trạng thái</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Tất cả</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                        <option value="hidden" {{ request('status') == 'hidden' ? 'selected' : '' }}>Ẩn</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Từ ngày</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" 
                           value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Đến ngày</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" 
                           value="{{ request('date_to') }}">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-sm btn-outline-secondary w-100" onclick="clearFilters()" title="Xóa bộ lọc">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="col-12">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control form-control-sm" 
                                   value="{{ request('search') }}" placeholder="Tìm kiếm theo nội dung, sản phẩm, user...">
                        </div>
                        <div class="col-md-8">
                            <div class="d-flex gap-2 flex-wrap align-items-center">
                                <div class="form-check form-check-sm">
                                    <input type="checkbox" name="has_images" value="1" {{ request('has_images') ? 'checked' : '' }} 
                                           class="form-check-input" id="has_images">
                                    <label for="has_images" class="form-check-label small">Có ảnh</label>
                                </div>
                                <div class="form-check form-check-sm">
                                    <input type="checkbox" name="reported" value="1" {{ request('reported') ? 'checked' : '' }} 
                                           class="form-check-input" id="reported">
                                    <label for="reported" class="form-check-label small">Bị report</label>
                                </div>
                                <div class="form-check form-check-sm">
                                    <input type="checkbox" name="verified" value="1" {{ request('verified') ? 'checked' : '' }} 
                                           class="form-check-input" id="verified">
                                    <label for="verified" class="form-check-label small">Đã mua hàng</label>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-search"></i> Lọc
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Batch Actions Bar (Sticky) --}}
    <div id="batchActionsBar" class="card shadow-lg border-primary mb-3" style="display: none; position: sticky; top: 0; z-index: 1000; background: white;">
        <div class="card-body py-2">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <div class="text-center text-md-start">
                    <strong id="selectedCount">0</strong> bình luận đã chọn
                </div>
                <div class="d-flex gap-2 flex-wrap justify-content-center">
                    <button type="button" class="btn btn-success btn-sm" onclick="batchAction('approve')">
                        <i class="bi bi-check2-circle"></i> Phê duyệt
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="batchAction('reject')">
                        <i class="bi bi-x-circle"></i> Từ chối
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" onclick="batchAction('hide')">
                        <i class="bi bi-eye-slash"></i> Ẩn
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="batchAction('delete')">
                        <i class="bi bi-trash"></i> Xóa
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="clearSelection()">
                        <i class="bi bi-x"></i> Bỏ chọn
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            
                            <th width="80" class="align-middle">Ảnh</th>
                            <th class="align-middle sortable" data-sort="product_id">
                                Sản phẩm
                                <i class="bi bi-arrow-down-up ms-1 sort-icon"></i>
                            </th>
                            <th class="align-middle sortable" data-sort="user_id">
                                User
                                <i class="bi bi-arrow-down-up ms-1 sort-icon"></i>
                            </th>
                            <th width="100" class="align-middle sortable" data-sort="rating">
                                Rating
                                <i class="bi bi-arrow-down-up ms-1 sort-icon"></i>
                            </th>
                            <th width="100" class="align-middle sortable" data-sort="helpful_votes_count">
                                Hữu ích
                                <i class="bi bi-arrow-down-up ms-1 sort-icon"></i>
                            </th>
                            <th class="align-middle">Nội dung</th>
                            <th width="100" class="align-middle sortable" data-sort="status">
                                Trạng thái
                                <i class="bi bi-arrow-down-up ms-1 sort-icon"></i>
                            </th>
                            <th width="120" class="align-middle sortable" data-sort="created_at">
                                Ngày
                                <i class="bi bi-arrow-down-up ms-1 sort-icon"></i>
                            </th>
                            <th width="150" class="align-middle text-center">
                               
                                Hành động
                            </th>
                        </tr>
                    </thead>
                    <tbody id="reviewsTableBody">
                        @forelse($reviews as $review)
                            <tr data-review-id="{{ $review->id }}" class="review-row">
                                
                                <td class="align-middle">
                                    @php
                                        $productImage = null;
                                        if ($review->product) {
                                            // Try main image field first
                                            if ($review->product->image) {
                                                $productImage = asset('storage/' . $review->product->image);
                                            }
                                            // Fallback to first product image
                                            elseif ($review->product->images && $review->product->images->count() > 0) {
                                                $firstImage = $review->product->images->first();
                                                $productImage = asset('storage/' . $firstImage->image_path ?? $firstImage->path ?? $firstImage->image ?? '');
                                            }
                                        }
                                    @endphp
                                    @if($productImage)
                                        <img src="{{ $productImage }}" 
                                             alt="{{ $review->product->name ?? 'Product' }}" 
                                             class="rounded quick-view-trigger" 
                                             style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;"
                                             data-quick-view-id="{{ $review->id }}"
                                             onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'50\' height=\'50\'%3E%3Crect width=\'50\' height=\'50\' fill=\'%23f0f0f0\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\' font-size=\'20\'%3E%3F%3C/text%3E%3C/svg%3E'; this.style.cursor='default';">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center rounded quick-view-trigger" 
                                             style="width: 50px; height: 50px; cursor: pointer;"
                                             data-quick-view-id="{{ $review->id }}">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <a href="{{ route('admin.products.show', $review->product_id) }}" target="_blank" class="text-decoration-none">
                                        <strong>{{ Str::limit($review->product->name ?? 'N/A', 40) }}</strong>
                                    </a>
                                    <div class="d-flex gap-1 mt-1 flex-wrap">
                                        @if($review->is_verified_purchase)
                                            <span class="badge bg-success" style="font-size: 0.7rem;">✓ Đã mua</span>
                                        @endif
                                        @if($review->images && count($review->images) > 0)
                                            <span class="badge bg-info" style="font-size: 0.7rem;">
                                                <i class="bi bi-image"></i> {{ count($review->images) }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 32px; height: 32px; font-size: 0.8rem;">
                                            <span class="text-white fw-bold">{{ substr($review->user->name ?? 'U', 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <div class="small fw-bold">{{ Str::limit($review->user->name ?? 'N/A', 20) }}</div>
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">{{ Str::limit($review->user->email ?? '', 25) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div class="text-warning small">
                                        @for($i = 1; $i <= 5; $i++)
                                            {{ $i <= $review->rating ? '★' : '☆' }}
                                        @endfor
                                    </div>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $review->rating }}/5</small>
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-info">
                                        <i class="bi bi-hand-thumbs-up"></i> 
                                        {{ $review->helpful_votes_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <div class="review-content quick-view-trigger" style="max-width: 300px; cursor: pointer;" 
                                         data-quick-view-id="{{ $review->id }}"
                                         title="Click để xem chi tiết">
                                        <span class="text-truncate d-block">{{ Str::limit($review->content ?? $review->comment, 80) }}</span>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    @php
                                        $statusBadges = [
                                            'pending' => ['class' => 'warning', 'text' => 'Chờ duyệt', 'icon' => 'clock'],
                                            'approved' => ['class' => 'success', 'text' => 'Đã duyệt', 'icon' => 'check-circle'],
                                            'rejected' => ['class' => 'danger', 'text' => 'Từ chối', 'icon' => 'x-circle'],
                                            'hidden' => ['class' => 'secondary', 'text' => 'Ẩn', 'icon' => 'eye-slash'],
                                        ];
                                        $status = $statusBadges[$review->status ?? 'pending'] ?? $statusBadges['pending'];
                                    @endphp
                                    <span class="badge bg-{{ $status['class'] }}">
                                        <i class="bi bi-{{ $status['icon'] }}"></i> {{ $status['text'] }}
                                    </span>
                                    @if($review->reported_count > 0)
                                        <span class="badge bg-danger mt-1 d-block" style="font-size: 0.7rem;">
                                            {{ $review->reported_count }} report
                                        </span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <small class="text-muted">{{ $review->created_at->format('d/m/Y') }}</small><br>
                                    <small class="text-muted" style="font-size: 0.7rem;">{{ $review->created_at->format('H:i') }}</small>
                                </td>
                                <td class="align-middle text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary quick-view-btn" 
                                                data-quick-view-id="{{ $review->id }}" 
                                                title="Xem nhanh">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <a href="{{ route('admin.comments.show', $review->id) }}" 
                                           class="btn btn-outline-info" 
                                           title="Chi tiết">
                                            <i class="bi bi-info-circle"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" 
                                                data-bs-toggle="dropdown">
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" id="dropdown-menu-{{ $review->id }}">
                                            @if($review->status == 'rejected')
                                                {{-- Khi từ chối: hiển thị Phê duyệt --}}
                                                <li><a class="dropdown-item approve-action" href="#" onclick="approveReview({{ $review->id }}, event)">
                                                    <i class="bi bi-check2-circle text-success"></i> Phê duyệt
                                                </a></li>
                                            @elseif($review->status == 'approved')
                                                {{-- Khi đã duyệt: hiển thị Từ chối --}}
                                                <li><a class="dropdown-item reject-action" href="#" onclick="rejectReview({{ $review->id }}, event)">
                                                    <i class="bi bi-x-circle text-danger"></i> Từ chối
                                                </a></li>
                                            @else
                                                {{-- Khi pending hoặc hidden: hiển thị cả hai --}}
                                                <li><a class="dropdown-item approve-action" href="#" onclick="approveReview({{ $review->id }}, event)">
                                                    <i class="bi bi-check2-circle text-success"></i> Phê duyệt
                                                </a></li>
                                                <li><a class="dropdown-item reject-action" href="#" onclick="rejectReview({{ $review->id }}, event)">
                                                    <i class="bi bi-x-circle text-danger"></i> Từ chối
                                                </a></li>
                                            @endif
                                            @if($review->status != 'hidden')
                                                <li><a class="dropdown-item" href="#" onclick="hideReview({{ $review->id }}, event)">
                                                    <i class="bi bi-eye-slash text-warning"></i> Ẩn
                                                </a></li>
                                            @else
                                                <li><a class="dropdown-item" href="#" onclick="showReview({{ $review->id }}, event)">
                                                    <i class="bi bi-eye text-info"></i> Hiện
                                                </a></li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteReview({{ $review->id }}, event)">
                                                <i class="bi bi-trash"></i> Xóa
                                            </a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                    <p class="text-muted mb-0">Không có bình luận nào</p>
                                </td>
                            </tr>
                        @endforelse

                        {{-- Pagination at bottom of table --}}
                        @if(isset($reviews) && $reviews->hasPages())
                            <tr>
                                <td colspan="10" class="border-0">
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 py-3 px-3 bg-light border-top">
                                        <div class="text-muted small">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Hiển thị
                                            <strong>{{ $reviews->firstItem() ?? 0 }}</strong>–<strong>{{ $reviews->lastItem() ?? 0 }}</strong>
                                            trên tổng
                                            <strong>{{ $reviews->total() ?? 0 }}</strong> bình luận
                                        </div>
                                        <div>
                                            {{ $reviews->appends(request()->except('page'))->links('vendor.pagination.bootstrap-5') }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

{{-- Quick View Modal --}}
@include('admin.comments.partials.quick-view-modal')

{{-- Loading Overlay --}}
<div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-none" 
     style="z-index: 9999;">
    <div class="d-flex justify-content-center align-items-center h-100">
        <div class="text-center text-white">
            <div class="spinner-border text-light mb-3" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="fs-5">Đang xử lý...</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Global variables
    let selectedReviews = new Set();
    const csrfToken = '{{ csrf_token() }}';

    // Toast notification helper
    function showToast(type, message, title = '') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        const icons = {
            success: 'success',
            error: 'error',
            warning: 'warning',
            info: 'info'
        };

        Toast.fire({
            icon: icons[type] || 'info',
            title: title || message,
            text: title ? message : ''
        });
    }

    // Show loading overlay
    function showLoading() {
        document.getElementById('loadingOverlay').classList.remove('d-none');
    }

    // Hide loading overlay
    function hideLoading() {
        document.getElementById('loadingOverlay').classList.add('d-none');
    }

    // Update selected count
    function updateSelectedCount() {
        const count = selectedReviews.size;
        document.getElementById('selectedCount').textContent = count;
        const bar = document.getElementById('batchActionsBar');
        if (count > 0) {
            bar.style.display = 'block';
        } else {
            bar.style.display = 'none';
        }
    }

    // Sortable columns
    document.querySelectorAll('.sortable').forEach(th => {
        th.style.cursor = 'pointer';
        th.addEventListener('click', function() {
            const sortBy = this.dataset.sort;
            const currentSort = '{{ request('sort_by', 'created_at') }}';
            const currentOrder = '{{ request('sort_order', 'desc') }}';
            let newOrder = 'asc';
            
            if (currentSort === sortBy && currentOrder === 'asc') {
                newOrder = 'desc';
            }
            
            const url = new URL(window.location.href);
            url.searchParams.set('sort_by', sortBy);
            url.searchParams.set('sort_order', newOrder);
            window.location.href = url.toString();
        });
        
        // Update sort icon
        const currentSort = '{{ request('sort_by', 'created_at') }}';
        const currentOrder = '{{ request('sort_order', 'desc') }}';
        if (th.dataset.sort === currentSort) {
            const icon = th.querySelector('.sort-icon');
            if (icon) {
                icon.className = currentOrder === 'asc' 
                    ? 'bi bi-arrow-up ms-1 sort-icon text-primary' 
                    : 'bi bi-arrow-down ms-1 sort-icon text-primary';
            }
        }
    });

    // Column visibility toggle
    document.querySelectorAll('.column-toggle').forEach(cb => {
        cb.addEventListener('change', function() {
            const columnIndex = this.dataset.column;
            const table = document.querySelector('table');
            const rows = table.querySelectorAll('tr');
            
            rows.forEach(row => {
                const cell = row.cells[columnIndex];
                if (cell) {
                    cell.style.display = this.checked ? '' : 'none';
                }
            });
            
            // Save to localStorage
            const visibility = JSON.parse(localStorage.getItem('columnVisibility') || '{}');
            visibility[columnIndex] = this.checked;
            localStorage.setItem('columnVisibility', JSON.stringify(visibility));
        });
        
        // Load saved visibility
        const visibility = JSON.parse(localStorage.getItem('columnVisibility') || '{}');
        if (visibility[this.dataset.column] === false) {
            this.checked = false;
            this.dispatchEvent(new Event('change'));
        }
    });

    // Go to page
    function goToPageNumber() {
        const pageInput = document.getElementById('goToPage');
        if (!pageInput) return;
        
        const page = parseInt(pageInput.value);
        const maxPage = {{ isset($reviews) && $reviews->lastPage() ? $reviews->lastPage() : 1 }};
        if (page >= 1 && page <= maxPage) {
            const url = new URL(window.location.href);
            url.searchParams.set('page', page);
            window.location.href = url.toString();
        } else {
            showToast('warning', `Trang phải từ 1 đến ${maxPage}`);
        }
    }

    // Change per page
    function changePerPage(perPage) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        url.searchParams.set('page', '1'); // Reset to first page
        window.location.href = url.toString();
    }

    // Select All (including select all on page)
    document.getElementById('selectAll')?.addEventListener('change', function() {
        const checked = this.checked;
        document.querySelectorAll('.review-checkbox').forEach(cb => {
            cb.checked = checked;
            if (checked) {
                selectedReviews.add(cb.value);
            } else {
                selectedReviews.delete(cb.value);
            }
        });
        updateSelectedCount();
    });

    // Individual checkbox
    document.querySelectorAll('.review-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            if (this.checked) {
                selectedReviews.add(this.value);
            } else {
                selectedReviews.delete(this.value);
            }
            updateSelectedCount();
            
            // Update select all
            const allChecked = document.querySelectorAll('.review-checkbox:checked').length === document.querySelectorAll('.review-checkbox').length;
            document.getElementById('selectAll').checked = allChecked;
        });
    });

    // Clear selection
    function clearSelection() {
        selectedReviews.clear();
        document.querySelectorAll('.review-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('selectAll').checked = false;
        updateSelectedCount();
    }

    // Quick View Modal
    let currentReviewId = null;
    let allReviewIds = [];

    function refreshReviewIds() {
        allReviewIds = Array.from(document.querySelectorAll('.review-row'))
            .map(row => row.dataset.reviewId)
            .filter(Boolean);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', refreshReviewIds);
    } else {
        refreshReviewIds();
    }

    document.addEventListener('click', function(event) {
        const trigger = event.target.closest('[data-quick-view-id]');
        if (!trigger) {
            return;
        }

        const reviewId = trigger.getAttribute('data-quick-view-id');
        if (!reviewId) {
            console.error('Quick view trigger missing data-quick-view-id attribute', trigger);
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        const id = parseInt(reviewId);
        if (Number.isNaN(id)) {
            console.error('Quick view trigger contains invalid ID', reviewId);
            alert('Lỗi: ID bình luận không hợp lệ');
            return;
        }

        quickView(id);
    });

    // Quick View function (now global)
    window.quickView = function(id) {
        if (!id || id === 'undefined' || id === 'null') {
            console.error('Quick view: Review ID is required or invalid');
            alert('Lỗi: Không có ID bình luận hợp lệ');
            return;
        }

        currentReviewId = id;
        const modalEl = document.getElementById('quickViewModal');
        const content = document.getElementById('quickViewContent');
        const detailLink = document.getElementById('viewDetailLink');
        const prevBtn = document.getElementById('prevReviewBtn');
        const nextBtn = document.getElementById('nextReviewBtn');
        
        console.log('Modal element:', modalEl);
        console.log('Content element:', content);
        console.log('Modal element exists:', !!modalEl);
        console.log('Content element exists:', !!content);
        console.log('Bootstrap available:', typeof bootstrap !== 'undefined');
        
        if (!modalEl) {
            console.error('Quick view modal not found in DOM');
            console.log('Searching for modal in document...');
            const allModals = document.querySelectorAll('.modal');
            console.log('All modals found:', allModals.length);
            allModals.forEach((m, i) => {
                console.log(`Modal ${i}:`, m.id, m.className);
            });
            alert('Modal không tìm thấy trong DOM. Vui lòng refresh trang.');
            return;
        }

        if (!content) {
            console.error('Quick view content element not found');
            alert('Content element không tìm thấy. Vui lòng refresh trang.');
            return;
        }

        // Initialize review IDs if not already done
        if (allReviewIds.length === 0) {
            allReviewIds = Array.from(document.querySelectorAll('.review-row')).map(row => row.dataset.reviewId);
        }

        // Set loading content
        content.innerHTML = '<div class="quick-view-loading"><div class="spinner-wrapper"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div><div class="loading-text">Đang tải thông tin bình luận</div><div class="loading-dots"><span></span><span></span><span></span></div></div>';
        
        if (detailLink) {
            detailLink.href = `/admin/comments/${id}`;
        }
        
        // Show/hide navigation buttons
        if (prevBtn && nextBtn) {
            const currentIndex = allReviewIds.indexOf(id.toString());
            if (currentIndex > 0) {
                prevBtn.style.display = 'inline-flex';
            } else {
                prevBtn.style.display = 'none';
            }
            if (currentIndex < allReviewIds.length - 1 && currentIndex >= 0) {
                nextBtn.style.display = 'inline-flex';
            } else {
                nextBtn.style.display = 'none';
            }
        }
        
        // Show modal
        try {
            // Check if Bootstrap is available
            if (typeof bootstrap === 'undefined') {
                console.error('Bootstrap is not loaded');
                alert('Bootstrap chưa được tải. Vui lòng refresh trang.');
                return;
            }
            
            // Remove any existing modal instances to avoid conflicts
            const existingModal = bootstrap.Modal.getInstance(modalEl);
            if (existingModal) {
                console.log('Disposing existing modal instance');
                existingModal.dispose();
            }
            
            // Create new modal instance
            console.log('Creating new modal instance');
            const modal = new bootstrap.Modal(modalEl, {
                backdrop: true,
                keyboard: true,
                focus: true
            });
            
            // Add event listeners
            modalEl.addEventListener('shown.bs.modal', function() {
                console.log('✅ Modal is now visible and shown');
            }, { once: true });
            
            modalEl.addEventListener('show.bs.modal', function() {
                console.log('🔄 Modal is showing...');
            }, { once: true });
            
            // Show modal
            console.log('Calling modal.show()...');
            modal.show();
            console.log('Modal.show() called successfully');
            
        } catch (error) {
            console.error('❌ Error showing modal:', error);
            console.error('Error details:', error.stack);
            alert('Không thể mở modal: ' + error.message);
            return;
        }

        // Fetch review data
        const quickViewUrl = `/admin/comments/${id}/quick-view`;
        console.log('Fetching review data from:', quickViewUrl);
        
        fetch(quickViewUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(res => {
            console.log('Response status:', res.status, res.statusText);
            if (!res.ok) {
                // Try to get error message from response
                return res.text().then(text => {
                    try {
                        const json = JSON.parse(text);
                        throw new Error(json.message || `HTTP error! status: ${res.status}`);
                    } catch (e) {
                        throw new Error(`HTTP error! status: ${res.status}. ${text.substring(0, 100)}`);
                    }
                });
            }
            return res.json();
        })
        .then(data => {
            console.log('✅ Received data:', data);
            if (data.html) {
                content.innerHTML = data.html;
                console.log('✅ Content updated successfully');
                
                // Initialize image lightbox after content is loaded
                if (data.images && data.images.length > 0) {
                    currentReviewImages = data.images;
                    currentImageIndex = 0;
                }
            } else {
                console.error('❌ No HTML in response', data);
                content.innerHTML = '<div class="alert alert-danger">Không thể tải dữ liệu. Vui lòng thử lại.</div>';
            }
        })
        .catch(err => {
            console.error('❌ Quick view fetch error:', err);
            console.error('Error details:', err.stack);
            content.innerHTML = '<div class="alert alert-danger">Có lỗi xảy ra: ' + err.message + '</div>';
        });
    };
    
    // Make navigateReview globally accessible
    window.navigateReview = function(direction) {
        if (!currentReviewId) return;
        const currentIndex = allReviewIds.indexOf(currentReviewId.toString());
        let newIndex;
        
        if (direction === 'prev' && currentIndex > 0) {
            newIndex = currentIndex - 1;
        } else if (direction === 'next' && currentIndex < allReviewIds.length - 1) {
            newIndex = currentIndex + 1;
        } else {
            return;
        }
        
        const newId = allReviewIds[newIndex];
        quickView(newId);
    }

    // Image Lightbox
    let currentReviewImages = [];
    let currentImageIndex = 0;

    function openImageLightbox(reviewId, imageIndex) {
        // Get images from current review
        fetch(`/admin/comments/${reviewId}/quick-view`, {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.images && data.images.length > 0) {
                currentReviewImages = data.images;
                currentImageIndex = imageIndex;
                showLightboxImage();
            }
        });
    }

    window.showLightboxImage = function() {
        const modalEl = document.getElementById('imageLightboxModal');
        if (!modalEl) {
            console.error('Image lightbox modal not found');
            return;
        }
        const modal = new bootstrap.Modal(modalEl);
        const img = document.getElementById('lightboxImage');
        const title = document.getElementById('lightboxImageTitle');
        const prevBtn = document.getElementById('prevImageBtn');
        const nextBtn = document.getElementById('nextImageBtn');
        
        img.src = currentReviewImages[currentImageIndex];
        title.textContent = `Hình ảnh ${currentImageIndex + 1} / ${currentReviewImages.length}`;
        
        prevBtn.disabled = currentImageIndex === 0;
        nextBtn.disabled = currentImageIndex === currentReviewImages.length - 1;
        
        modal.show();
    }

    window.changeLightboxImage = function(direction) {
        currentImageIndex += direction;
        if (currentImageIndex < 0) currentImageIndex = 0;
        if (currentImageIndex >= currentReviewImages.length) currentImageIndex = currentReviewImages.length - 1;
        showLightboxImage();
    }

    // Keyboard shortcuts for quick view
    document.addEventListener('keydown', function(e) {
        const quickViewModal = document.getElementById('quickViewModal');
        if (quickViewModal && quickViewModal.classList.contains('show')) {
            if (e.key === 'ArrowLeft') {
                navigateReview('prev');
            } else if (e.key === 'ArrowRight') {
                navigateReview('next');
            }
        }
        
        const lightboxModal = document.getElementById('imageLightboxModal');
        if (lightboxModal && lightboxModal.classList.contains('show')) {
            if (e.key === 'ArrowLeft') {
                changeLightboxImage(-1);
            } else if (e.key === 'ArrowRight') {
                changeLightboxImage(1);
            }
        }
    });

    // Approve Review (AJAX - no reload)
    function approveReview(id, event) {
        if (event) event.preventDefault();
        event.stopPropagation();
        
        if (typeof Swal === 'undefined') {
            if (confirm('Bạn có chắc muốn phê duyệt bình luận này?')) {
                performApprove(id);
            }
            return;
        }
        
        Swal.fire({
            title: 'Xác nhận',
            text: 'Bạn có chắc muốn phê duyệt bình luận này?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Phê duyệt',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                performApprove(id);
            }
        });
    }
    
    function performApprove(id) {
                showLoading();
                fetch(`/admin/comments/${id}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
                })
        .then(res => {
            if (!res.ok) {
                return res.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }
                });
            }
            return res.json();
        })
                .then(data => {
                    hideLoading();
                    if (data.status === 'success') {
                        showToast('success', data.message);
                        updateReviewRow(id, 'approved');
                        updateBadgeCounts();
                    } else {
                        showToast('error', data.message || 'Có lỗi xảy ra');
                    }
                })
                .catch(err => {
                    hideLoading();
            console.error('Approve error:', err);
            showToast('error', 'Có lỗi xảy ra: ' + err.message);
        });
    }

    // Reject Review
    function rejectReview(id, event) {
        if (event) event.preventDefault();
        event.stopPropagation();
        
        if (typeof Swal === 'undefined') {
            if (confirm('Bạn có chắc muốn từ chối bình luận này?')) {
                performReject(id);
            }
            return;
        }
        
        Swal.fire({
            title: 'Xác nhận',
            text: 'Bạn có chắc muốn từ chối bình luận này?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Từ chối',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                performReject(id);
            }
        });
    }
    
    function performReject(id) {
                showLoading();
                fetch(`/admin/comments/${id}/reject`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
                })
        .then(res => {
            if (!res.ok) {
                return res.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }
                });
            }
            return res.json();
        })
                .then(data => {
                    hideLoading();
                    if (data.status === 'success') {
                        showToast('success', data.message);
                        updateReviewRow(id, 'rejected');
                        updateBadgeCounts();
                    } else {
                        showToast('error', data.message || 'Có lỗi xảy ra');
                    }
                })
                .catch(err => {
                    hideLoading();
            console.error('Reject error:', err);
            showToast('error', 'Có lỗi xảy ra: ' + err.message);
        });
    }

    // Hide Review
    function hideReview(id, event) {
        if (event) event.preventDefault();
        event.stopPropagation();
        
        if (typeof Swal === 'undefined') {
            if (confirm('Bạn có chắc muốn ẩn bình luận này?')) {
                performHide(id);
            }
            return;
        }
        
        Swal.fire({
            title: 'Xác nhận',
            text: 'Bạn có chắc muốn ẩn bình luận này?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ẩn',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                performHide(id);
            }
        });
    }
    
    function performHide(id) {
                showLoading();
                fetch(`/admin/comments/${id}/hide`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
                })
        .then(res => {
            if (!res.ok) {
                return res.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }
                });
            }
            return res.json();
        })
                .then(data => {
                    hideLoading();
                    if (data.status === 'success') {
                        showToast('success', data.message);
                        updateReviewRow(id, 'hidden');
                        updateBadgeCounts();
                    } else {
                        showToast('error', data.message || 'Có lỗi xảy ra');
                    }
                })
                .catch(err => {
                    hideLoading();
            console.error('Hide error:', err);
            showToast('error', 'Có lỗi xảy ra: ' + err.message);
        });
    }

    // Show Review
    function showReview(id, event) {
        if (event) event.preventDefault();
        event.stopPropagation();
        showLoading();
        fetch(`/admin/comments/${id}/show`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(res => {
            if (!res.ok) {
                return res.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }
                });
            }
            return res.json();
        })
        .then(data => {
            hideLoading();
            if (data.status === 'success') {
                showToast('success', data.message);
                updateReviewRow(id, 'approved');
                updateBadgeCounts();
            } else {
                showToast('error', data.message || 'Có lỗi xảy ra');
            }
        })
        .catch(err => {
            hideLoading();
            console.error('Show error:', err);
            showToast('error', 'Có lỗi xảy ra: ' + err.message);
        });
    }

    // Delete Review
    function deleteReview(id, event) {
        if (event) event.preventDefault();
        event.stopPropagation();
        
        if (typeof Swal === 'undefined') {
            if (confirm('Bạn có chắc muốn xóa vĩnh viễn bình luận này? Hành động này không thể hoàn tác!')) {
                performDelete(id);
            }
            return;
        }
        
        Swal.fire({
            title: 'Xác nhận xóa',
            text: 'Bạn có chắc muốn xóa vĩnh viễn bình luận này? Hành động này không thể hoàn tác!',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                performDelete(id);
            }
        });
    }
    
    function performDelete(id) {
                showLoading();
                fetch(`/admin/comments/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
                })
        .then(res => {
            if (!res.ok) {
                return res.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }
                });
            }
            return res.json();
        })
                .then(data => {
                    hideLoading();
                    if (data.status === 'success') {
                        showToast('success', data.message);
                const row = document.querySelector(`tr[data-review-id="${id}"]`);
                if (row) {
                    row.remove();
                }
                        refreshReviewIds();
                        updateBadgeCounts();
                        if (document.querySelectorAll('.review-row').length === 0) {
                            location.reload();
                        }
                    } else {
                        showToast('error', data.message || 'Có lỗi xảy ra');
                    }
                })
                .catch(err => {
                    hideLoading();
            console.error('Delete error:', err);
            showToast('error', 'Có lỗi xảy ra: ' + err.message);
        });
    }

    // Update review row after action
    function updateReviewRow(id, newStatus) {
        const row = document.querySelector(`tr[data-review-id="${id}"]`);
        if (!row) return;

        const statusCell = row.querySelector('td:nth-child(7)');
        const statusBadges = {
            'approved': {class: 'success', text: 'Đã duyệt', icon: 'check-circle'},
            'rejected': {class: 'danger', text: 'Từ chối', icon: 'x-circle'},
            'hidden': {class: 'secondary', text: 'Ẩn', icon: 'eye-slash'},
            'pending': {class: 'warning', text: 'Chờ duyệt', icon: 'clock'}
        };

        const badge = statusBadges[newStatus];
        if (badge && statusCell) {
            statusCell.innerHTML = `
                <span class="badge bg-${badge.class}">
                    <i class="bi bi-${badge.icon}"></i> ${badge.text}
                </span>
            `;
        }

        // Update action dropdown based on new status
        const dropdown = row.querySelector(`#dropdown-menu-${id}`) || row.querySelector('.dropdown-menu');
        if (dropdown) {
            updateDropdownMenu(dropdown, newStatus, id);
        }
    }
    
    // Update dropdown menu based on status
    function updateDropdownMenu(dropdown, status, reviewId) {
        if (!dropdown) return;
        
        // Find existing action items
        const approveItem = dropdown.querySelector('.approve-action')?.closest('li');
        const rejectItem = dropdown.querySelector('.reject-action')?.closest('li');
        const hideItem = dropdown.querySelector('a[onclick*="hideReview"]')?.closest('li');
        const showItem = dropdown.querySelector('a[onclick*="showReview"]')?.closest('li');
        
        // Remove existing approve/reject items
        if (approveItem && approveItem.parentNode === dropdown) {
            approveItem.remove();
        }
        if (rejectItem && rejectItem.parentNode === dropdown) {
            rejectItem.remove();
        }
        
        // Helper function to safely insert before divider or append
        function safeInsert(element) {
            // Tìm lại divider mỗi lần để đảm bảo nó vẫn còn trong dropdown
            const currentDivider = dropdown.querySelector('.dropdown-divider');
            if (currentDivider && currentDivider.parentNode === dropdown) {
                try {
                    dropdown.insertBefore(element, currentDivider);
                } catch (e) {
                    // Nếu insertBefore thất bại, dùng appendChild
                    console.warn('insertBefore failed, using appendChild:', e);
                    dropdown.appendChild(element);
                }
            } else {
                dropdown.appendChild(element);
            }
        }
        
        if (status === 'rejected') {
            // Khi từ chối: hiển thị "Phê duyệt", ẩn "Từ chối"
            const approveLi = document.createElement('li');
            approveLi.innerHTML = `
                <a class="dropdown-item approve-action" href="#" onclick="approveReview(${reviewId}, event)">
                    <i class="bi bi-check2-circle text-success"></i> Phê duyệt
                </a>
            `;
            safeInsert(approveLi);
        } else if (status === 'approved') {
            // Khi đã duyệt: hiển thị "Từ chối", ẩn "Phê duyệt"
            const rejectLi = document.createElement('li');
            rejectLi.innerHTML = `
                <a class="dropdown-item reject-action" href="#" onclick="rejectReview(${reviewId}, event)">
                    <i class="bi bi-x-circle text-danger"></i> Từ chối
                </a>
            `;
            safeInsert(rejectLi);
        } else {
            // Khi pending hoặc hidden: hiển thị cả hai
            const approveLi = document.createElement('li');
            approveLi.innerHTML = `
                <a class="dropdown-item approve-action" href="#" onclick="approveReview(${reviewId}, event)">
                    <i class="bi bi-check2-circle text-success"></i> Phê duyệt
                </a>
            `;
            const rejectLi = document.createElement('li');
            rejectLi.innerHTML = `
                <a class="dropdown-item reject-action" href="#" onclick="rejectReview(${reviewId}, event)">
                    <i class="bi bi-x-circle text-danger"></i> Từ chối
                </a>
            `;
            safeInsert(approveLi);
            safeInsert(rejectLi);
        }
        
        // Update hide/show button
        if (status === 'hidden') {
            if (hideItem && hideItem.parentNode === dropdown) {
                hideItem.style.display = 'none';
            }
            if (!showItem || showItem.parentNode !== dropdown) {
                const showLi = document.createElement('li');
                showLi.innerHTML = `
                    <a class="dropdown-item" href="#" onclick="showReview(${reviewId}, event)">
                        <i class="bi bi-eye text-info"></i> Hiện
                    </a>
                `;
                safeInsert(showLi);
            } else {
                showItem.style.display = '';
            }
        } else {
            if (showItem && showItem.parentNode === dropdown) {
                showItem.style.display = 'none';
            }
            if (!hideItem || hideItem.parentNode !== dropdown) {
                const hideLi = document.createElement('li');
                hideLi.innerHTML = `
                    <a class="dropdown-item" href="#" onclick="hideReview(${reviewId}, event)">
                        <i class="bi bi-eye-slash text-warning"></i> Ẩn
                    </a>
                `;
                safeInsert(hideLi);
            } else {
                hideItem.style.display = '';
            }
        }
    }

    // Update badge counts in header
    function updateBadgeCounts() {
        // This would require an API endpoint to get counts
        // For now, we'll just show a message
        // You can implement this with a separate API call
    }

    // Batch Actions
    function batchAction(action) {
        const ids = Array.from(selectedReviews);
        if (ids.length === 0) {
            showToast('warning', 'Vui lòng chọn ít nhất một bình luận');
            return;
        }

        const actions = {
            'approve': {title: 'Phê duyệt', text: `Bạn có chắc muốn phê duyệt ${ids.length} bình luận?`, endpoint: 'bulk-approve'},
            'reject': {title: 'Từ chối', text: `Bạn có chắc muốn từ chối ${ids.length} bình luận?`, endpoint: 'bulk-reject'},
            'hide': {title: 'Ẩn', text: `Bạn có chắc muốn ẩn ${ids.length} bình luận?`, endpoint: 'bulk-hide'},
            'delete': {title: 'Xóa', text: `Bạn có chắc muốn xóa vĩnh viễn ${ids.length} bình luận? Hành động này không thể hoàn tác!`, endpoint: 'bulk-delete', icon: 'error'}
        };

        const config = actions[action];
        if (!config) return;

        Swal.fire({
            title: config.title,
            text: config.text,
            icon: config.icon || 'question',
            showCancelButton: true,
            confirmButtonColor: action === 'delete' ? '#ef4444' : '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: config.title,
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                fetch(`/admin/comments/${config.endpoint}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ ids })
                })
                .then(res => res.json())
                .then(data => {
                    hideLoading();
                    if (data.status === 'success') {
                        showToast('success', data.message);
                        // Remove rows or update them
                        ids.forEach(id => {
                            if (action === 'delete') {
                                document.querySelector(`tr[data-review-id="${id}"]`)?.remove();
                            } else {
                                updateReviewRow(id, action === 'approve' ? 'approved' : action === 'reject' ? 'rejected' : 'hidden');
                            }
                        });
                        refreshReviewIds();
                        clearSelection();
                        updateBadgeCounts();
                    } else {
                        showToast('error', data.message || 'Có lỗi xảy ra');
                    }
                })
                .catch(err => {
                    hideLoading();
                    showToast('error', 'Có lỗi xảy ra');
                });
            }
        });
    }

    // Export Reviews
    function exportReviews() {
        const params = new URLSearchParams(window.location.search);
        showLoading();
        window.location.href = `/admin/comments/export?${params.toString()}`;
        setTimeout(() => hideLoading(), 2000);
    }

    // Toggle Filters
    function toggleFilters() {
        const section = document.getElementById('filtersSection');
        const icon = document.getElementById('filterToggleIcon');
        if (section.style.display === 'none') {
            section.style.display = 'block';
            icon.classList.remove('bi-chevron-down');
            icon.classList.add('bi-chevron-up');
        } else {
            section.style.display = 'none';
            icon.classList.remove('bi-chevron-up');
            icon.classList.add('bi-chevron-down');
        }
    }

    // Clear Filters
    function clearFilters() {
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            filterForm.reset();
        }
        window.location.href = '{{ route('admin.comments.index') }}';
    }

    // Sort by Helpful
    function sortByHelpful() {
        const url = new URL(window.location.href);
        url.searchParams.set('sort_by', 'helpful_votes_count');
        url.searchParams.set('sort_order', 'desc');
        window.location.href = url.toString();
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });
</script>
@endpush

@push('styles')
<style>
    .review-row:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s;
    }
    
    .review-content:hover {
        color: #0d6efd;
        text-decoration: underline;
    }

    #batchActionsBar {
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .table th {
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .sortable {
        user-select: none;
        transition: background-color 0.2s;
    }

    .sortable:hover {
        background-color: #e9ecef !important;
    }

    .sort-icon {
        font-size: 0.75rem;
        opacity: 0.5;
        transition: opacity 0.2s;
    }

    .sortable:hover .sort-icon {
        opacity: 1;
    }

    .column-toggle {
        cursor: pointer;
    }

    .badge {
        font-weight: 500;
    }
</style>
@endpush
@endsection

