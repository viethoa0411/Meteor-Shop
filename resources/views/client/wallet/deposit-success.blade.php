@extends('client.layouts.app')

@section('title', 'Yêu cầu nạp tiền thành công')

@push('head')
<style>
    .success-wrapper { padding: 60px 0; }
    .success-card {
        background: #fff;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.08);
        text-align: center;
        max-width: 500px;
        margin: 0 auto;
    }
    .success-icon {
        width: 80px; height: 80px;
        background: #d4edda;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 24px;
        font-size: 2.5rem;
        color: #28a745;
    }
    .deposit-info {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin: 24px 0;
        text-align: left;
    }
    .deposit-info-item {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px dashed #dee2e6;
    }
    .deposit-info-item:last-child { border-bottom: none; }
</style>
@endpush

@section('content')
<div class="success-wrapper">
    <div class="success-card">
        <div class="success-icon">
            <i class="bi bi-check-lg"></i>
        </div>
        
        <h3 class="fw-bold mb-2">Yêu cầu đã được gửi!</h3>
        <p class="text-muted">Yêu cầu nạp tiền của bạn đang chờ xác nhận từ admin.</p>

        <div class="deposit-info">
            <div class="deposit-info-item">
                <span class="text-muted">Mã yêu cầu</span>
                <span class="fw-bold text-primary">{{ $deposit->request_code }}</span>
            </div>
            <div class="deposit-info-item">
                <span class="text-muted">Số tiền</span>
                <span class="fw-bold">{{ $deposit->formatted_amount }}</span>
            </div>
            <div class="deposit-info-item">
                <span class="text-muted">Trạng thái</span>
                <span class="badge bg-warning">{{ $deposit->status_label }}</span>
            </div>
            <div class="deposit-info-item">
                <span class="text-muted">Thời gian</span>
                <span>{{ $deposit->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>

        <div class="alert alert-info text-start">
            <i class="bi bi-telephone me-2"></i>
            Nếu cần hỗ trợ, vui lòng liên hệ: <strong>{{ $settings->support_phone }}</strong>
        </div>

        <div class="d-flex gap-3 mt-4">
            <a href="{{ route('client.account.wallet.index') }}" class="btn btn-primary flex-fill">
                <i class="bi bi-wallet2 me-2"></i>Về ví của tôi
            </a>
            <a href="{{ route('client.account.wallet.history') }}" class="btn btn-outline-secondary flex-fill">
                <i class="bi bi-clock-history me-2"></i>Lịch sử
            </a>
        </div>
    </div>
</div>
@endsection

