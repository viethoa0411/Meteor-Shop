@extends('client.layouts.app')

@section('title', 'Rút tiền từ ví')

@push('head')
<style>
    .withdraw-wrapper { padding: 40px 0; }
    .withdraw-card {
        background: #fff;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        border: 1px solid #eee;
    }
    .balance-display {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
    }
    .pending-withdraws {
        background: #fff3cd;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
    }
    .pending-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #e0c36a;
    }
    .pending-item:last-child { border-bottom: none; }
    .bank-select { cursor: pointer; }
</style>
@endpush

@section('content')
<div class="withdraw-wrapper">
    <div class="mb-4">
        <a href="{{ route('client.account.wallet.index') }}" class="btn btn-link text-decoration-none p-0">
            <i class="bi bi-arrow-left me-1"></i> Quay lại ví
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="withdraw-card">
                <h4 class="fw-bold mb-4"><i class="bi bi-arrow-up-circle text-danger me-2"></i>Rút tiền từ ví</h4>

                <div class="balance-display">
                    <div class="small opacity-75">Số dư khả dụng</div>
                    <div class="fs-4 fw-bold">{{ $wallet->formatted_balance }}</div>
                </div>

                @if($pendingWithdraws->count() > 0)
                <div class="pending-withdraws">
                    <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-2"></i>Yêu cầu đang xử lý</h6>
                    @foreach($pendingWithdraws as $pending)
                    <div class="pending-item">
                        <div>
                            <div class="fw-semibold">{{ $pending->formatted_amount }}</div>
                            <small class="text-muted">{{ $pending->bank_name }} - {{ $pending->account_number }}</small>
                        </div>
                        <span class="badge bg-{{ $pending->status == 'pending' ? 'warning' : 'info' }}">
                            {{ $pending->status_label }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @endif

                <form action="{{ route('client.account.wallet.withdraw.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Tên chủ tài khoản <span class="text-danger">*</span></label>
                            <input type="text" name="account_holder" class="form-control @error('account_holder') is-invalid @enderror" 
                                   placeholder="NGUYEN VAN A" value="{{ old('account_holder') }}" required>
                            @error('account_holder')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Ngân hàng <span class="text-danger">*</span></label>
                            <select name="bank_name" class="form-select @error('bank_name') is-invalid @enderror" required>
                                <option value="">-- Chọn ngân hàng --</option>
                                @foreach(\App\Models\WalletSetting::BANK_CODES as $name => $code)
                                    <option value="{{ $name }}" {{ old('bank_name') == $name ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Số tài khoản <span class="text-danger">*</span></label>
                            <input type="text" name="account_number" class="form-control @error('account_number') is-invalid @enderror" 
                                   placeholder="0123456789" value="{{ old('account_number') }}" required>
                            @error('account_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                   placeholder="0912345678" value="{{ old('phone', Auth::user()->phone) }}" required>
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Số tiền rút <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="amount" class="form-control form-control-lg @error('amount') is-invalid @enderror" 
                                   placeholder="Nhập số tiền" min="10000" max="{{ $wallet->balance }}" step="1000" value="{{ old('amount') }}" required>
                            <span class="input-group-text">VNĐ</span>
                        </div>
                        @error('amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        <small class="text-muted">Tối thiểu: 10,000đ | Tối đa: {{ $wallet->formatted_balance }}</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Ghi chú (tùy chọn)</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Ghi chú thêm...">{{ old('note') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-danger btn-lg w-100">
                        <i class="bi bi-send me-2"></i>Gửi yêu cầu rút tiền
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

