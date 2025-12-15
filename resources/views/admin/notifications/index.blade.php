@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-3 mb-2">
                <h2 class="mb-0">
                    <i class="bi bi-bell-fill me-2"></i>Quản lý thông báo
                </h2>
                <span class="badge bg-primary rounded-pill">{{ number_format($stats['total']) }}</span>
                @if($stats['unread'] > 0)
                    <span class="badge bg-danger rounded-pill">
                        <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>{{ number_format($stats['unread']) }} chưa đọc
                    </span>
                @endif
            </div>
            <p class="text-muted mb-0">Trung tâm vận hành và theo dõi sự kiện hệ thống</p>
        </div>
                <div class="d-flex gap-2">
                    <!-- Refresh and Mark all read buttons removed per request -->
                </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-bell-fill text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Tổng số</div>
                            <div class="h4 mb-0 fw-bold">{{ number_format($stats['total']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-bell-slash-fill text-danger fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Chưa đọc</div>
                            <div class="h4 mb-0 fw-bold text-danger">{{ number_format($stats['unread']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Đã đọc</div>
                            <div class="h4 mb-0 fw-bold text-success">{{ number_format($stats['read']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-pie-chart-fill text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Tỷ lệ đọc</div>
                            <div class="h4 mb-0 fw-bold">
                                {{ $stats['total'] > 0 ? number_format(($stats['read'] / $stats['total']) * 100, 1) : 0 }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.notifications.index') }}" id="filterForm">
                <!-- Quick Filters -->
                <div class="mb-3">
                    <label class="form-label small fw-semibold mb-2">Lọc nhanh</label>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary quick-filter" data-filter="today">
                            <i class="bi bi-calendar-day me-1"></i>Hôm nay
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary quick-filter" data-filter="7days">
                            <i class="bi bi-calendar-week me-1"></i>7 ngày qua
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary quick-filter" data-filter="30days">
                            <i class="bi bi-calendar-month me-1"></i>30 ngày qua
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger quick-filter" data-filter="unread">
                            <i class="bi bi-bell-slash me-1"></i>Chưa đọc
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning quick-filter" data-filter="danger">
                            <i class="bi bi-exclamation-triangle me-1"></i>Khẩn cấp
                        </button>
                    </div>
                </div>

                <hr class="my-3">

                <!-- Advanced Filters -->
                <div class="row g-3">
                    <div class="col-12 col-md-3">
                        <label class="form-label small fw-semibold">
                            <i class="bi bi-search me-1"></i>Tìm kiếm
                        </label>
                        <input type="text" name="search" class="form-control" 
                               value="{{ request('search') }}" 
                               placeholder="Tiêu đề, nội dung..."
                               id="searchInput"
                               style="width: 100%; box-sizing: border-box;">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-semibold">
                            <i class="bi bi-tag me-1"></i>Loại
                        </label>
                        <select name="type" class="form-select" id="typeFilter" style="width: 100%; box-sizing: border-box;">
                            <option value="">Tất cả</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-semibold">
                            <i class="bi bi-flag me-1"></i>Mức độ
                        </label>
                        <select name="level" class="form-select" id="levelFilter" style="width: 100%; box-sizing: border-box;">
                            <option value="">Tất cả</option>
                            @foreach($levels as $level)
                                <option value="{{ $level }}" {{ request('level') == $level ? 'selected' : '' }}>
                                    {{ ucfirst($level) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-semibold">
                            <i class="bi bi-envelope me-1"></i>Trạng thái
                        </label>
                        <select name="status" class="form-select" id="statusFilter" style="width: 100%; box-sizing: border-box;">
                            <option value="">Tất cả</option>
                            <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Chưa đọc</option>
                            <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Đã đọc</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label small fw-semibold">
                            <i class="bi bi-calendar-range me-1"></i>Thời gian
                        </label>
                        <div class="d-flex gap-2" style="flex-wrap: nowrap;">
                            <input type="date" name="date_from" class="form-control" 
                                   value="{{ request('date_from') }}" 
                                   placeholder="Từ"
                                   id="dateFrom"
                                   style="flex: 1 1 0; min-width: 0; box-sizing: border-box;">
                            <input type="date" name="date_to" class="form-control" 
                                   value="{{ request('date_to') }}" 
                                   placeholder="Đến"
                                   id="dateTo"
                                   style="flex: 1 1 0; min-width: 0; box-sizing: border-box;">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Áp dụng bộ lọc
                            </button>
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary" id="resetFilterBtn">
                                <i class="bi bi-x-circle me-1"></i>Xóa bộ lọc
                            </a>
                            <button type="button" class="btn btn-outline-info" id="saveFilterBtn" title="Lưu bộ lọc">
                                <i class="bi bi-bookmark me-1"></i>Lưu bộ lọc
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Notification List Section -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($notifications->count() > 0)
                <form id="bulkActionForm">
                    <!-- List Header -->
                    <div class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <input type="checkbox" id="selectAll" class="form-check-input" style="margin-top: 0;">
                            <span class="text-muted small">
                                <span id="selectedCount">0</span> / {{ $notifications->total() }} thông báo
                            </span>
                        </div>
                        <div id="bulkActionsBar" class="d-none">
                            <div class="d-flex gap-2 align-items-center">
                                <select id="bulkAction" class="form-select form-select-sm" style="width: auto;">
                                    <option value="">Chọn thao tác...</option>
                                    <option value="read">Đánh dấu đã đọc</option>
                                    <option value="unread">Đánh dấu chưa đọc</option>
                                    <option value="delete">Xóa</option>
                                </select>
                                <button type="button" class="btn btn-sm btn-primary" id="applyBulkAction">
                                    Áp dụng
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Notification List -->
                    <div class="notification-list" style="max-height: 600px; overflow-y: auto;">
                        @foreach($notifications as $notification)
                            @php
                                $levelColors = [
                                    'info' => 'border-info bg-info bg-opacity-10',
                                    'success' => 'border-success bg-success bg-opacity-10',
                                    'warning' => 'border-warning bg-warning bg-opacity-10',
                                    'danger' => 'border-danger bg-danger bg-opacity-10',
                                ];
                                $borderClass = $levelColors[$notification->level] ?? 'border-secondary';
                            @endphp
                            <div class="notification-item {{ $notification->is_read ? '' : 'notification-unread' }} {{ $borderClass }}"
                                 data-notification-id="{{ $notification->id }}"
                                 data-url="{{ $notification->url ?? '#' }}"
                                 style="cursor: pointer; border-left: 4px solid; padding: 16px; transition: all 0.2s ease;">
                                <div class="d-flex align-items-start gap-3">
                                    <!-- Checkbox -->
                                    <div class="flex-shrink-0 pt-1">
                                        <input type="checkbox" name="ids[]" value="{{ $notification->id }}" 
                                               class="form-check-input notification-checkbox"
                                               onclick="event.stopPropagation();">
                                    </div>

                                    <!-- Icon -->
                                    <div class="flex-shrink-0">
                                        <div class="notification-icon-large">
                                            <i class="bi {{ $notification->icon }} {{ $notification->icon_color }} fs-4"></i>
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-grow-1" style="min-width: 0;">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <div class="fw-semibold {{ $notification->is_read ? 'text-dark' : 'text-dark fw-bold' }} mb-1">
                                                    {{ $notification->title }}
                                                    @if(!$notification->is_read)
                                                        <span class="badge bg-danger rounded-circle ms-2" style="width: 8px; height: 8px; padding: 0;"></span>
                                                    @endif
                                                </div>
                                                <div class="text-muted small">
                                                    {{ \Illuminate\Support\Str::limit($notification->message, 120) }}
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0 ms-3">
                                                <div class="btn-group btn-group-sm" onclick="event.stopPropagation();">
                                                    @if($notification->url)
                                                        <a href="{{ $notification->url }}" 
                                                           class="btn btn-outline-primary btn-sm" 
                                                           title="Xem chi tiết"
                                                           onclick="event.stopPropagation();">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    @endif
                                                    @if($notification->is_read)
                                                        <button class="btn btn-outline-warning btn-sm mark-unread-btn" 
                                                                data-id="{{ $notification->id }}"
                                                                title="Đánh dấu chưa đọc">
                                                            <i class="bi bi-envelope"></i>
                                                        </button>
                                                    @else
                                                        <button class="btn btn-outline-success btn-sm mark-read-btn" 
                                                                data-id="{{ $notification->id }}"
                                                                title="Đánh dấu đã đọc">
                                                            <i class="bi bi-check"></i>
                                                        </button>
                                                    @endif
                                                    <button class="btn btn-outline-danger btn-sm delete-btn" 
                                                            data-id="{{ $notification->id }}"
                                                            title="Xóa">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-3 flex-wrap">
                                            <span class="badge bg-secondary">{{ ucfirst($notification->type) }}</span>
                                            <span class="badge {{ $levelColors[$notification->level] ?? 'bg-secondary' }}">
                                                {{ ucfirst($notification->level) }}
                                            </span>
                                            <span class="text-muted small">
                                                <i class="bi bi-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                            </span>
                                            <span class="text-muted small">
                                                {{ $notification->created_at->format('d/m/Y H:i') }}
                                            </span>
                                            @if($notification->is_read)
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Đã đọc
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-circle me-1"></i>Chưa đọc
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </form>

                <!-- Pagination -->
                @if($notifications->hasPages())
                    <div class="notification-pagination-wrapper">
                        <div class="notification-pagination-info">
                            <i class="bi bi-info-circle me-1"></i>
                            <span>Hiển thị <strong>{{ $notifications->firstItem() ?? 0 }}</strong> - <strong>{{ $notifications->lastItem() ?? 0 }}</strong> 
                            trong tổng số <strong>{{ number_format($notifications->total()) }}</strong> thông báo</span>
                        </div>
                        <div class="notification-pagination">
                            @php
                                $currentPage = $notifications->currentPage();
                                $lastPage = $notifications->lastPage();
                                $onEachSide = 2;
                                
                                $start = max(1, $currentPage - $onEachSide);
                                $end = min($lastPage, $currentPage + $onEachSide);
                            @endphp
                            
                            <!-- Previous Button -->
                            @if($notifications->onFirstPage())
                                <button class="pagination-btn pagination-btn-disabled" disabled>
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                            @else
                                <a href="{{ $notifications->previousPageUrl() }}" class="pagination-btn pagination-btn-nav" rel="prev">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            @endif
                            
                            <!-- First Page -->
                            @if($start > 1)
                                <a href="{{ $notifications->url(1) }}" class="pagination-btn pagination-btn-number">1</a>
                                @if($start > 2)
                                    <span class="pagination-dots">...</span>
                                @endif
                            @endif
                            
                            <!-- Page Numbers -->
                            @for($i = $start; $i <= $end; $i++)
                                @if($i == $currentPage)
                                    <span class="pagination-btn pagination-btn-number pagination-btn-active">{{ $i }}</span>
                                @else
                                    <a href="{{ $notifications->url($i) }}" class="pagination-btn pagination-btn-number">{{ $i }}</a>
                                @endif
                            @endfor
                            
                            <!-- Last Page -->
                            @if($end < $lastPage)
                                @if($end < $lastPage - 1)
                                    <span class="pagination-dots">...</span>
                                @endif
                                <a href="{{ $notifications->url($lastPage) }}" class="pagination-btn pagination-btn-number">{{ $lastPage }}</a>
                            @endif
                            
                            <!-- Next Button -->
                            @if($notifications->hasMorePages())
                                <a href="{{ $notifications->nextPageUrl() }}" class="pagination-btn pagination-btn-nav" rel="next">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            @else
                                <button class="pagination-btn pagination-btn-disabled" disabled>
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            @endif
                            
                            <!-- Go to Page Input -->
                            <div class="pagination-goto">
                                <input type="number" 
                                       id="gotoPageInput" 
                                       class="pagination-goto-input" 
                                       min="1" 
                                       max="{{ $lastPage }}" 
                                       value="{{ $currentPage }}"
                                       placeholder="Trang">
                                <button type="button" id="gotoPageBtn" class="pagination-goto-btn" title="Đi đến trang">
                                    <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @elseif($notifications->total() > 0)
                    <div class="notification-pagination-wrapper">
                        <div class="notification-pagination-info text-center">
                            <i class="bi bi-info-circle me-1"></i>
                            <span>Hiển thị tất cả <strong>{{ number_format($notifications->total()) }}</strong> thông báo</span>
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="bi bi-bell-slash fs-1 text-muted opacity-50"></i>
                    <div class="text-muted mt-3 mb-2">Không có thông báo nào</div>
                    @if(request()->hasAny(['search', 'type', 'level', 'status', 'date_from', 'date_to']))
                        <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-x-circle me-1"></i>Xóa bộ lọc
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Notification List Styles */
    .notification-list {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 #f1f5f9;
    }

    .notification-list::-webkit-scrollbar {
        width: 6px;
    }

    .notification-list::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    .notification-list::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .notification-item {
        border-bottom: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    .notification-item:hover {
        background-color: #f9fafb !important;
        transform: translateX(4px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .notification-unread {
        background-color: #fef3c7 !important;
        font-weight: 500;
    }

    .notification-unread:hover {
        background-color: #fde68a !important;
    }

    .notification-icon-large {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        transition: transform 0.2s ease;
    }

    .notification-item:hover .notification-icon-large {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .btn-group-sm .btn {
        transition: all 0.2s ease;
    }

    .btn-group-sm .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Loading state */
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .bi-hourglass-split {
        animation: spin 1s linear infinite;
    }

    .spinning {
        animation: spin 0.5s linear;
    }

    /* Checkbox indeterminate state */
    input[type="checkbox"]:indeterminate {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    /* Quick filter buttons */
    .quick-filter.active {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }

    /* Level border colors */
    .border-info { border-left-color: #0dcaf0 !important; }
    .border-success { border-left-color: #198754 !important; }
    .border-warning { border-left-color: #ffc107 !important; }
    .border-danger { border-left-color: #dc3545 !important; }

    /* Filter Section Fixes */
    #filterForm .form-control,
    #filterForm .form-select {
        width: 100%;
        box-sizing: border-box;
        max-width: 100%;
    }

    #filterForm .row {
        margin-left: 0;
        margin-right: 0;
    }

    #filterForm .row > [class*="col-"] {
        padding-left: calc(var(--bs-gutter-x) * 0.5);
        padding-right: calc(var(--bs-gutter-x) * 0.5);
    }

    #filterForm .d-flex.gap-2 {
        gap: 0.5rem !important;
    }

    /* Quick filter buttons responsive */
    .quick-filter {
        white-space: nowrap;
        flex-shrink: 0;
    }

    @media (max-width: 768px) {
        .quick-filter {
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
        }

        #filterForm .d-flex.gap-2 {
            flex-direction: column;
        }

        #filterForm .d-flex.gap-2 .btn {
            width: 100%;
        }

        .notification-item {
            padding: 12px !important;
        }

        .notification-icon-large {
            width: 40px;
            height: 40px;
        }
    }

    /* Empty state improvements */
    .text-center.py-5 {
        min-height: 300px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    /* Statistics cards hover effect */
    .card.shadow-sm {
        transition: all 0.3s ease;
    }

    .card.shadow-sm:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
    }

    /* Pagination Styles */
    .notification-pagination-wrapper {
        padding: 1.25rem 1.5rem;
        border-top: 1px solid #e5e7eb;
        background: linear-gradient(to bottom, #f9fafb, #ffffff);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .notification-pagination-info {
        display: flex;
        align-items: center;
        color: #6b7280;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .notification-pagination-info i {
        color: #3b82f6;
        font-size: 1rem;
    }

    .notification-pagination-info strong {
        color: #1f2937;
        font-weight: 600;
    }

    .notification-pagination {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .pagination-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.5rem;
        height: 2.5rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        background: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
        user-select: none;
    }

    .pagination-btn:hover:not(.pagination-btn-disabled):not(.pagination-btn-active) {
        background: #f3f4f6;
        border-color: #9ca3af;
        color: #1f2937;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .pagination-btn:active:not(.pagination-btn-disabled) {
        transform: translateY(0);
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .pagination-btn-number {
        font-weight: 500;
    }

    .pagination-btn-active {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: #ffffff;
        border-color: #2563eb;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        font-weight: 600;
        cursor: default;
    }

    .pagination-btn-active:hover {
        transform: none;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
    }

    .pagination-btn-nav {
        background: #ffffff;
        border-color: #d1d5db;
    }

    .pagination-btn-nav i {
        font-size: 1.125rem;
    }

    .pagination-btn-disabled {
        background: #f3f4f6;
        color: #9ca3af;
        border-color: #e5e7eb;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .pagination-dots {
        display: inline-flex;
        align-items: center;
        padding: 0 0.5rem;
        color: #9ca3af;
        font-weight: 500;
    }

    .pagination-goto {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-left: 0.5rem;
        padding-left: 0.5rem;
        border-left: 1px solid #e5e7eb;
    }

    .pagination-goto-input {
        width: 4rem;
        height: 2.5rem;
        padding: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        text-align: center;
        color: #374151;
        background: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
    }

    .pagination-goto-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .pagination-goto-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        padding: 0;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: #ffffff;
        border: none;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .pagination-goto-btn:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
    }

    .pagination-goto-btn:active {
        transform: translateY(0);
    }

    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        .notification-pagination-wrapper {
            background: linear-gradient(to bottom, #1f2937, #111827);
            border-top-color: #374151;
        }

        .notification-pagination-info {
            color: #9ca3af;
        }

        .notification-pagination-info strong {
            color: #f3f4f6;
        }

        .pagination-btn {
            background: #1f2937;
            border-color: #374151;
            color: #e5e7eb;
        }

        .pagination-btn:hover:not(.pagination-btn-disabled):not(.pagination-btn-active) {
            background: #374151;
            border-color: #4b5563;
            color: #f3f4f6;
        }

        .pagination-goto-input {
            background: #1f2937;
            border-color: #374151;
            color: #e5e7eb;
        }

        .pagination-goto-input:focus {
            border-color: #3b82f6;
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .notification-pagination-wrapper {
            flex-direction: column;
            align-items: stretch;
        }

        .notification-pagination-info {
            justify-content: center;
            text-align: center;
        }

        .notification-pagination {
            justify-content: center;
        }

        .pagination-goto {
            margin-left: 0;
            padding-left: 0;
            border-left: none;
            border-top: 1px solid #e5e7eb;
            padding-top: 0.5rem;
            margin-top: 0.5rem;
            justify-content: center;
            width: 100%;
        }
    }

    /* Animation */
    @keyframes paginationFadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .notification-pagination-wrapper {
        animation: paginationFadeIn 0.3s ease;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.notification-checkbox');
    const bulkActionsBar = document.getElementById('bulkActionsBar');
    const selectedCount = document.getElementById('selectedCount');
    const applyBulkAction = document.getElementById('applyBulkAction');
    const bulkAction = document.getElementById('bulkAction');
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    const refreshBtn = document.getElementById('refreshBtn');
    const filterForm = document.getElementById('filterForm');
    const saveFilterBtn = document.getElementById('saveFilterBtn');

    // Load saved filter from sessionStorage
    function loadSavedFilter() {
        const saved = sessionStorage.getItem('notification_filter');
        if (saved) {
            try {
                const filter = JSON.parse(saved);
                if (filter.search) document.getElementById('searchInput').value = filter.search;
                if (filter.type) document.getElementById('typeFilter').value = filter.type;
                if (filter.level) document.getElementById('levelFilter').value = filter.level;
                if (filter.status) document.getElementById('statusFilter').value = filter.status;
                if (filter.date_from) document.getElementById('dateFrom').value = filter.date_from;
                if (filter.date_to) document.getElementById('dateTo').value = filter.date_to;
            } catch (e) {
                console.warn('Error loading saved filter:', e);
            }
        }
    }

    // Save filter to sessionStorage
    function saveFilter() {
        const filter = {
            search: document.getElementById('searchInput').value,
            type: document.getElementById('typeFilter').value,
            level: document.getElementById('levelFilter').value,
            status: document.getElementById('statusFilter').value,
            date_from: document.getElementById('dateFrom').value,
            date_to: document.getElementById('dateTo').value
        };
        sessionStorage.setItem('notification_filter', JSON.stringify(filter));
        Swal.fire({
            icon: 'success',
            title: 'Đã lưu',
            text: 'Bộ lọc đã được lưu',
            timer: 1500,
            showConfirmButton: false
        });
    }

    // Quick filters
    document.querySelectorAll('.quick-filter').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const filter = this.getAttribute('data-filter');
            if (!filter) return;
            
            const today = new Date().toISOString().split('T')[0];
            const dateFromInput = document.getElementById('dateFrom');
            const dateToInput = document.getElementById('dateTo');
            const statusFilter = document.getElementById('statusFilter');
            const levelFilter = document.getElementById('levelFilter');
            
            // Remove active class from all
            document.querySelectorAll('.quick-filter').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Clear ALL filters first (to avoid conflicts)
            const searchInput = document.getElementById('searchInput');
            const typeFilter = document.getElementById('typeFilter');
            
            if (dateFromInput) dateFromInput.value = '';
            if (dateToInput) dateToInput.value = '';
            if (statusFilter) statusFilter.value = '';
            if (levelFilter) levelFilter.value = '';
            if (searchInput) searchInput.value = '';
            if (typeFilter) typeFilter.value = '';

            switch(filter) {
                case 'today':
                    if (dateFromInput) dateFromInput.value = today;
                    if (dateToInput) dateToInput.value = today;
                    break;
                case '7days':
                    const date7 = new Date();
                    date7.setDate(date7.getDate() - 7);
                    if (dateFromInput) dateFromInput.value = date7.toISOString().split('T')[0];
                    if (dateToInput) dateToInput.value = today;
                    break;
                case '30days':
                    const date30 = new Date();
                    date30.setDate(date30.getDate() - 30);
                    if (dateFromInput) dateFromInput.value = date30.toISOString().split('T')[0];
                    if (dateToInput) dateToInput.value = today;
                    break;
                case 'unread':
                    if (statusFilter) statusFilter.value = 'unread';
                    break;
                case 'danger':
                    if (levelFilter) levelFilter.value = 'danger';
                    break;
                default:
                    console.warn('Unknown filter:', filter);
                    return;
            }
            
            // Auto submit form
            if (filterForm) {
                // Submit form
                filterForm.submit();
            } else {
                console.error('Filter form not found');
            }
        });
    });

    // Save filter button
    if (saveFilterBtn) {
        saveFilterBtn.addEventListener('click', async function() {
            const filter = {
                search: document.getElementById('searchInput').value,
                type: document.getElementById('typeFilter').value,
                level: document.getElementById('levelFilter').value,
                status: document.getElementById('statusFilter').value,
                date_from: document.getElementById('dateFrom').value,
                date_to: document.getElementById('dateTo').value
            };
            
            try {
                const response = await fetch('{{ route("admin.notifications.save-filter") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(filter)
                });
                
                const data = await response.json();
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Đã lưu',
                        text: 'Bộ lọc đã được lưu',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Lỗi', data.message || 'Không thể lưu bộ lọc', 'error');
                }
            } catch (error) {
                Swal.fire('Lỗi', 'Không thể lưu bộ lọc', 'error');
            }
        });
    }

    // Refresh button
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            this.classList.add('spinning');
            // Reload page to get fresh data from database
            location.reload();
        });
    }

    // Preserve active state of quick filters on page load
    function setActiveQuickFilter() {
        const urlParams = new URLSearchParams(window.location.search);
        const dateFrom = urlParams.get('date_from');
        const dateTo = urlParams.get('date_to');
        const status = urlParams.get('status');
        const level = urlParams.get('level');
        
        // Check for date filters
        if (dateFrom && dateTo) {
            const today = new Date().toISOString().split('T')[0];
            const dateFromObj = new Date(dateFrom);
            const dateToObj = new Date(dateTo);
            const todayObj = new Date(today);
            
            // Check if it's "today" filter
            if (dateFrom === today && dateTo === today) {
                document.querySelectorAll('.quick-filter').forEach(btn => {
                    if (btn.getAttribute('data-filter') === 'today') {
                        btn.classList.add('active');
                    }
                });
            }
            // Check if it's "7days" or "30days" filter
            else if (dateTo === today) {
                const diffDays = Math.floor((todayObj - dateFromObj) / (1000 * 60 * 60 * 24));
                if (diffDays >= 6 && diffDays <= 7) {
                    // 7days filter
                    document.querySelectorAll('.quick-filter').forEach(btn => {
                        if (btn.getAttribute('data-filter') === '7days') {
                            btn.classList.add('active');
                        }
                    });
                } else if (diffDays >= 29 && diffDays <= 30) {
                    // 30days filter
                    document.querySelectorAll('.quick-filter').forEach(btn => {
                        if (btn.getAttribute('data-filter') === '30days') {
                            btn.classList.add('active');
                        }
                    });
                }
            }
        }
        
        // Check for status filter
        if (status === 'unread') {
            document.querySelectorAll('.quick-filter').forEach(btn => {
                if (btn.getAttribute('data-filter') === 'unread') {
                    btn.classList.add('active');
                }
            });
        }
        
        // Check for level filter
        if (level === 'danger') {
            document.querySelectorAll('.quick-filter').forEach(btn => {
                if (btn.getAttribute('data-filter') === 'danger') {
                    btn.classList.add('active');
                }
            });
        }
    }

    // Set active quick filter on page load
    setActiveQuickFilter();

    // Clear filter button
    const resetFilterBtn = document.getElementById('resetFilterBtn');
    if (resetFilterBtn) {
        resetFilterBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Clear all form inputs
            const searchInput = document.getElementById('searchInput');
            const typeFilter = document.getElementById('typeFilter');
            const levelFilter = document.getElementById('levelFilter');
            const statusFilter = document.getElementById('statusFilter');
            const dateFrom = document.getElementById('dateFrom');
            const dateTo = document.getElementById('dateTo');
            
            if (searchInput) searchInput.value = '';
            if (typeFilter) typeFilter.value = '';
            if (levelFilter) levelFilter.value = '';
            if (statusFilter) statusFilter.value = '';
            if (dateFrom) dateFrom.value = '';
            if (dateTo) dateTo.value = '';
            
            // Remove active class from quick filters
            document.querySelectorAll('.quick-filter').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Redirect to clean URL
            window.location.href = '{{ route("admin.notifications.index") }}';
        });
    }

    // Auto-refresh notifications every 30 seconds
    let autoRefreshInterval;
    function startAutoRefresh() {
        if (autoRefreshInterval) clearInterval(autoRefreshInterval);
        
        autoRefreshInterval = setInterval(async () => {
            if (document.hidden) return; // Don't refresh if page is hidden
            
            try {
                // Refresh badge count
                const badgeResponse = await fetch('{{ route("admin.notifications.unread-count") }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (badgeResponse.ok) {
                    const badgeData = await badgeResponse.json();
                    if (badgeData.success) {
                        // Update header badge if exists
                        const headerBadge = document.querySelector('.badge.bg-danger.rounded-pill');
                        if (headerBadge && badgeData.count > 0) {
                            headerBadge.innerHTML = `<i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>${badgeData.count} chưa đọc`;
                            headerBadge.style.display = 'inline-block';
                        } else if (headerBadge && badgeData.count === 0) {
                            headerBadge.style.display = 'none';
                        }
                    }
                }
            } catch (error) {
                console.warn('Auto-refresh error:', error);
            }
        }, 30000); // 30 seconds
    }

    // Start auto-refresh
    startAutoRefresh();

    // Pause auto-refresh when page is hidden
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            if (autoRefreshInterval) clearInterval(autoRefreshInterval);
        } else {
            startAutoRefresh();
        }
    });

    // Pagination: Go to page
    const gotoPageInput = document.getElementById('gotoPageInput');
    const gotoPageBtn = document.getElementById('gotoPageBtn');
    
    if (gotoPageInput && gotoPageBtn) {
        // Go to page on button click
        gotoPageBtn.addEventListener('click', function() {
            const page = parseInt(gotoPageInput.value);
            const maxPage = parseInt(gotoPageInput.getAttribute('max'));
            
            if (page && page >= 1 && page <= maxPage) {
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('page', page);
                window.location.href = currentUrl.toString();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: `Vui lòng nhập số trang từ 1 đến ${maxPage}`,
                    timer: 2000,
                    showConfirmButton: false
                });
                gotoPageInput.focus();
            }
        });

        // Go to page on Enter key
        gotoPageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                gotoPageBtn.click();
            }
        });

        // Validate input on change
        gotoPageInput.addEventListener('input', function() {
            const page = parseInt(this.value);
            const maxPage = parseInt(this.getAttribute('max'));
            
            if (page < 1) {
                this.value = 1;
            } else if (page > maxPage) {
                this.value = maxPage;
            }
        });

        // Add loading state on navigation
        document.querySelectorAll('.pagination-btn-nav, .pagination-btn-number').forEach(btn => {
            if (btn.tagName === 'A') {
                btn.addEventListener('click', function(e) {
                    // Add loading indicator
                    const wrapper = document.querySelector('.notification-pagination-wrapper');
                    if (wrapper) {
                        wrapper.style.opacity = '0.6';
                        wrapper.style.pointerEvents = 'none';
                    }
                });
            }
        });
    }

    // Click notification row to redirect
    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', function(e) {
            // Don't redirect if clicking on buttons or checkbox
            if (e.target.closest('.btn-group') || e.target.closest('.notification-checkbox')) {
                return;
            }
            
            const url = this.getAttribute('data-url');
            const id = this.getAttribute('data-notification-id');
            
            if (url && url !== '#') {
                // Mark as read first
                if (!this.classList.contains('notification-unread')) {
                    window.location.href = url;
                    return;
                }
                
                // Mark as read then redirect
                fetch(`{{ route('admin.notifications.read', ':id') }}`.replace(':id', id), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                }).then(() => {
                    window.location.href = url;
                }).catch(() => {
                    window.location.href = url;
                });
            }
        });
    });

    // Load saved filter on page load
    loadSavedFilter();

    // Select all
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkActionsBar();
        });
    }

    // Individual checkbox
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateBulkActionsBar();
            if (selectAll) {
                selectAll.checked = Array.from(checkboxes).every(c => c.checked);
                selectAll.indeterminate = !selectAll.checked && Array.from(checkboxes).some(c => c.checked);
            }
        });
    });

    function updateBulkActionsBar() {
        const selected = Array.from(checkboxes).filter(cb => cb.checked);
        if (selected.length > 0) {
            bulkActionsBar.classList.remove('d-none');
            selectedCount.textContent = selected.length;
        } else {
            bulkActionsBar.classList.add('d-none');
        }
        
        // Update selectAll state
        if (selectAll) {
            selectAll.checked = selected.length === checkboxes.length && checkboxes.length > 0;
            selectAll.indeterminate = selected.length > 0 && selected.length < checkboxes.length;
        }
    }

    // Bulk action
    if (applyBulkAction) {
        applyBulkAction.addEventListener('click', async function() {
            const action = bulkAction.value;
            if (!action) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Vui lòng chọn thao tác',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }

            const selected = Array.from(checkboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);

            if (selected.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Vui lòng chọn ít nhất một thông báo',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }

            const actionText = {
                'read': 'đánh dấu đã đọc',
                'unread': 'đánh dấu chưa đọc',
                'delete': 'xóa'
            };

            const result = await Swal.fire({
                title: 'Xác nhận',
                text: `Bạn có chắc muốn ${actionText[action]} ${selected.length} thông báo?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#aaa'
            });

            if (result.isConfirmed) {
                // Show loading
                applyBulkAction.disabled = true;
                const originalText = applyBulkAction.textContent;
                applyBulkAction.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Đang xử lý...';

                try {
                    const response = await fetch('{{ route("admin.notifications.bulk-action") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            action: action,
                            ids: selected
                        })
                    });

                    const data = await response.json();
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        applyBulkAction.disabled = false;
                        applyBulkAction.textContent = originalText;
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: data.message || 'Có lỗi xảy ra',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                } catch (error) {
                    applyBulkAction.disabled = false;
                    applyBulkAction.textContent = originalText;
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Không thể thực hiện thao tác. Vui lòng thử lại.',
                        confirmButtonColor: '#dc3545'
                    });
                }
            }
        });
    }

    // Mark all as read
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const result = await Swal.fire({
                title: 'Xác nhận',
                text: 'Bạn có chắc muốn đánh dấu tất cả thông báo đã đọc?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#aaa'
            });

            if (result.isConfirmed) {
                // Show loading
                markAllReadBtn.disabled = true;
                const originalText = markAllReadBtn.innerHTML;
                markAllReadBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Đang xử lý...';

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        throw new Error('CSRF token not found');
                    }

                    const response = await fetch('{{ route("admin.notifications.read-all") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: data.message || 'Đã đánh dấu tất cả đã đọc',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        markAllReadBtn.disabled = false;
                        markAllReadBtn.innerHTML = originalText;
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: data.message || 'Có lỗi xảy ra',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                } catch (error) {
                    console.error('Mark all read error:', error);
                    markAllReadBtn.disabled = false;
                    markAllReadBtn.innerHTML = originalText;
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Không thể thực hiện thao tác: ' + (error.message || 'Vui lòng thử lại'),
                        confirmButtonColor: '#dc3545'
                    });
                }
            }
        });
    }

    // Mark as read/unread
    document.querySelectorAll('.mark-read-btn, .mark-unread-btn').forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const id = this.getAttribute('data-id');
            const isRead = this.classList.contains('mark-read-btn');
            const endpoint = isRead 
                ? `{{ route('admin.notifications.read', ':id') }}`.replace(':id', id)
                : `{{ route('admin.notifications.unread', ':id') }}`.replace(':id', id);

            // Disable button during request
            this.disabled = true;
            const originalHTML = this.innerHTML;
            this.innerHTML = '<i class="bi bi-hourglass-split"></i>';

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (data.success) {
                    // Show success message briefly before reload
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: data.message,
                        timer: 1000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    this.disabled = false;
                    this.innerHTML = originalHTML;
                    Swal.fire('Lỗi', data.message || 'Có lỗi xảy ra', 'error');
                }
            } catch (error) {
                this.disabled = false;
                this.innerHTML = originalHTML;
                Swal.fire('Lỗi', 'Không thể thực hiện thao tác', 'error');
            }
        });
    });

    // Delete
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const id = this.getAttribute('data-id');
            const result = await Swal.fire({
                title: 'Xác nhận xóa',
                text: 'Bạn có chắc muốn xóa thông báo này?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy',
                confirmButtonColor: '#dc3545'
            });

            if (result.isConfirmed) {
                // Disable button during request
                this.disabled = true;
                const originalHTML = this.innerHTML;
                this.innerHTML = '<i class="bi bi-hourglass-split"></i>';

                try {
                    const response = await fetch(`{{ route('admin.notifications.destroy', ':id') }}`.replace(':id', id), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: data.message,
                            timer: 1000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        this.disabled = false;
                        this.innerHTML = originalHTML;
                        Swal.fire('Lỗi', data.message || 'Có lỗi xảy ra', 'error');
                    }
                } catch (error) {
                    this.disabled = false;
                    this.innerHTML = originalHTML;
                    Swal.fire('Lỗi', 'Không thể xóa thông báo', 'error');
                }
            }
        });
    });
});
</script>
@endpush
@endsection

