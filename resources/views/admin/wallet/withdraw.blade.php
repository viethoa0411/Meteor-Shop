@extends('admin.layouts.app')

@section('title', 'Rút tiền - ' . $wallet->user->name)

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-cash-coin me-2"></i>Rút tiền khỏi ví</h2>
            <a href="{{ route('admin.wallet.show', $wallet->id) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Quay lại
            </a>
        </div>

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-wallet2 me-2"></i>Thông tin ví</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Chủ ví:</strong> {{ $wallet->user->name }}</p>
                        <p class="mb-2"><strong>Số dư hiện tại:</strong>
                            <span class="text-success fw-bold">{{ number_format($wallet->balance, 0, ',', '.') }} đ</span>
                        </p>
                        <p class="mb-2"><strong>Ngân hàng:</strong> {{ $wallet->bank_name }}</p>
                        <p class="mb-2"><strong>Số tài khoản:</strong> <code>{{ $wallet->bank_account }}</code></p>
                        <p class="mb-0"><strong>Chủ tài khoản:</strong> {{ $wallet->account_holder }}</p>
                    </div>
                </div>

                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Hãy đảm bảo số dư ví đủ để thực hiện yêu cầu rút tiền này.
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Thông tin rút tiền</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.wallet.withdraw.process', $wallet->id) }}" method="POST">
                            @csrf
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Số tiền rút <span class="text-danger">*</span></label>
                                    <input type="number"
                                           name="amount"
                                           class="form-control @error('amount') is-invalid @enderror"
                                           min="1000"
                                           step="1000"
                                           value="{{ old('amount') }}"
                                           placeholder="Nhập số tiền cần rút"
                                           required>
                                    <small class="text-muted">Tối đa: {{ number_format($wallet->balance, 0, ',', '.') }} đ</small>
                                    @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Ngân hàng <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="bank_name"
                                           class="form-control @error('bank_name') is-invalid @enderror"
                                           value="{{ old('bank_name', $wallet->bank_name) }}"
                                           placeholder="Ví dụ: MB Bank, Vietcombank..."
                                           required>
                                    @error('bank_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Số tài khoản <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="bank_account"
                                           class="form-control @error('bank_account') is-invalid @enderror"
                                           value="{{ old('bank_account', $wallet->bank_account) }}"
                                           placeholder="Nhập số tài khoản nhận tiền"
                                           required>
                                    @error('bank_account') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tên người nhận <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="account_holder"
                                           class="form-control @error('account_holder') is-invalid @enderror"
                                           value="{{ old('account_holder', $wallet->account_holder) }}"
                                           placeholder="VD: TRAN TIEN DAT"
                                           required>
                                    @error('account_holder') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Ghi chú</label>
                                <textarea name="note"
                                          class="form-control @error('note') is-invalid @enderror"
                                          rows="3"
                                          placeholder="Thông tin bổ sung...">{{ old('note') }}</textarea>
                                @error('note') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.wallet.show', $wallet->id) }}" class="btn btn-outline-secondary">
                                    Hủy
                                </a>
                                <button type="submit" class="btn btn-success"
                                        onclick="return confirm('Xác nhận rút tiền khỏi ví?');">
                                    <i class="bi bi-check-circle me-2"></i>Xác nhận rút
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

