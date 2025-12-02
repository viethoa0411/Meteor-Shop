@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="bi bi-chat-dots-fill me-2"></i>Quản lý Chatbox</h4>
            <p class="text-muted mb-0">Trả lời tin nhắn và hỗ trợ khách hàng</p>
        </div>
        <div class="d-flex gap-2">
            <a href="#" class="btn btn-outline-primary">
                <i class="bi bi-gear me-1"></i> Cài đặt
            </a>
            <form action="#" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn {{ $settings->is_enabled ? 'btn-success' : 'btn-secondary' }}">
                    <i class="bi bi-{{ $settings->is_enabled ? 'toggle-on' : 'toggle-off' }} me-1"></i>
                    {{ $settings->is_enabled ? 'Đang bật' : 'Đang tắt' }}
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-chat-dots text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                            <small class="text-muted">Tổng cuộc hội thoại</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-chat-left-text text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-0">{{ $stats['active'] }}</h3>
                            <small class="text-muted">Đang hoạt động</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-envelope-exclamation text-danger fs-4"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-0">{{ $stats['unread'] }}</h3>
                            <small class="text-muted">Chưa đọc</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-calendar-event text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-0">{{ $stats['today'] }}</h3>
                            <small class="text-muted">Hôm nay</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Tìm kiếm</label>
                    <input type="text" name="search" class="form-control" placeholder="Tên, email, SĐT..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang mở</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Đã đóng</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tin nhắn</label>
                    <select name="unread" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="1" {{ request('unread') == '1' ? 'selected' : '' }}>Chưa đọc</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Lọc
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Conversations List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="bi bi-chat-square-text me-2"></i>Danh sách hội thoại</h5>
        </div>
        <div class="card-body p-0">
            @forelse($sessions as $session)
                <a href="#" 
                   class="d-block border-bottom p-3 text-decoration-none chat-item {{ $session->unread_count > 0 ? 'bg-light' : '' }}">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                 style="width: 48px; height: 48px; font-size: 18px;">
                                {{ strtoupper(substr($session->customer_name, 0, 1)) }}
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 overflow-hidden">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1 {{ $session->unread_count > 0 ? 'fw-bold' : '' }}">
                                        {{ $session->customer_name }}
                                        @if($session->user_id)
                                            <span class="badge bg-info ms-1">Thành viên</span>
                                        @endif
                                    </h6>
                                    <small class="text-muted">{{ $session->customer_email ?? $session->ip_address }}</small>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block">{{ $session->last_message_at?->diffForHumans() }}</small>
                                    @if($session->unread_count > 0)
                                        <span class="badge bg-danger">{{ $session->unread_count }} mới</span>
                                    @endif
                                </div>
                            </div>
                            <p class="mb-0 text-muted text-truncate small mt-1">
                                {{ Str::limit($session->last_message, 80) }}
                            </p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-chat-dots text-muted" style="font-size: 48px;"></i>
                    <p class="text-muted mt-3">Chưa có cuộc hội thoại nào</p>
                </div>
            @endforelse
        </div>
        @if($sessions->hasPages())
            <div class="card-footer bg-white">
                {{ $sessions->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

<style>
.chat-item:hover {
    background-color: #f8f9fa !important;
}
body.dark .chat-item:hover {
    background-color: #2a2a2a !important;
}
body.dark .bg-light {
    background-color: #252525 !important;
}
</style>
@endsection

