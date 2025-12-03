@extends('client.layouts.app')

@section('title', 'Nạp tiền vào ví')

@push('head')
<style>
    .deposit-wrapper { padding: 40px 0; }
    .deposit-card {
        background: #fff;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        border: 1px solid #eee;
    }
    .qr-section {
        text-align: center;
        padding: 24px;
        background: #f8f9fa;
        border-radius: 12px;
    }
    .qr-section img { max-width: 250px; border-radius: 8px; }
    .bank-info { background: #fff3cd; border-radius: 12px; padding: 20px; margin-top: 20px; }
    .bank-info-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #e0c36a; }
    .bank-info-item:last-child { border-bottom: none; }
    .bank-info-label { color: #856404; font-weight: 500; }
    .bank-info-value { font-weight: 600; color: #333; }
    .pending-deposits { background: #e7f3ff; border-radius: 12px; padding: 20px; margin-bottom: 24px; }
    .pending-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #cce5ff; }
    .pending-item:last-child { border-bottom: none; }
    .balance-display { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px; padding: 20px; margin-bottom: 24px; }
</style>
@endpush

@section('content')
<div class="deposit-wrapper">
    <div class="mb-4">
        <a href="{{ route('client.account.wallet.index') }}" class="btn btn-link text-decoration-none p-0">
            <i class="bi bi-arrow-left me-1"></i> Quay lại ví
        </a>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="deposit-card">
                <h4 class="fw-bold mb-4"><i class="bi bi-plus-circle text-success me-2"></i>Nạp tiền vào ví</h4>

                <div class="balance-display">
                    <div class="small opacity-75">Số dư hiện tại</div>
                    <div class="fs-4 fw-bold">{{ $wallet->formatted_balance }}</div>
                </div>

                @if($pendingDeposits->count() > 0)
                <div class="pending-deposits">
                    <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-2"></i>Yêu cầu đang chờ xác nhận</h6>
                    @foreach($pendingDeposits as $pending)
                    <div class="pending-item">
                        <div>
                            <div class="fw-semibold">{{ $pending->formatted_amount }}</div>
                            <small class="text-muted">{{ $pending->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                        <form action="#" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hủy yêu cầu này?')">Hủy</button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @endif

                <form action="{{ route('client.account.wallet.deposit.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Số tiền muốn nạp <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="amount" class="form-control form-control-lg @error('amount') is-invalid @enderror" 
                                   placeholder="Nhập số tiền" min="10000" step="1000" value="{{ old('amount') }}" required>
                            <span class="input-group-text">VNĐ</span>
                        </div>
                        @error('amount')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Tối thiểu: 10,000đ</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Ghi chú (tùy chọn)</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Ghi chú thêm...">{{ old('note') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100">
                        <i class="bi bi-send me-2"></i>Gửi yêu cầu nạp tiền
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="deposit-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-qr-code me-2"></i>Thông tin chuyển khoản</h5>
                
                <div class="qr-section">
                    <img src="{{ $settings->generateQrUrl(0, 'Nap tien vi ' . Auth::user()->name) }}" alt="QR Code" class="mb-3">
                    <p class="text-muted small mb-0">Quét mã QR để chuyển khoản</p>
                </div>

                <div class="bank-info">
                    <div class="bank-info-item">
                        <span class="bank-info-label">Ngân hàng</span>
                        <span class="bank-info-value">{{ $settings->bank_name }}</span>
                    </div>
                    <div class="bank-info-item">
                        <span class="bank-info-label">Số tài khoản</span>
                        <span class="bank-info-value">{{ $settings->bank_account }}</span>
                    </div>
                    <div class="bank-info-item">
                        <span class="bank-info-label">Chủ tài khoản</span>
                        <span class="bank-info-value">{{ $settings->account_holder }}</span>
                    </div>
                    <div class="bank-info-item">
                        <span class="bank-info-label">Nội dung CK</span>
                        <span class="bank-info-value text-primary">NAP {{ Auth::user()->id }}</span>
                    </div>
                </div>

                <div class="alert alert-info mt-3 mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Sau khi chuyển khoản, vui lòng gửi yêu cầu nạp tiền. Admin sẽ xác nhận trong vòng 24h.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

