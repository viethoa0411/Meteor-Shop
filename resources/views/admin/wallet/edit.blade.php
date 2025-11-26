@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa Ví')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-wallet2 me-2"></i>Chỉnh sửa Ví</h2>
            <a href="{{ route('admin.wallet.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Quay lại
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="#" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Chủ ví</label>
                        <input type="text" class="form-control" value="{{ $wallet->user->name }}" disabled>
                        <small class="text-muted">Không thể thay đổi chủ ví</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Số dư hiện tại</label>
                        <input type="text" class="form-control" 
                               value="{{ number_format($wallet->balance, 0, ',', '.') }} đ" disabled>
                        <small class="text-muted">Số dư được cập nhật tự động qua giao dịch</small>
                    </div>

                    <div class="mb-3">
                        <label for="bank_name" class="form-label">Tên ngân hàng <span class="text-danger">*</span></label>
                        <select name="bank_name" id="bank_name" class="form-select @error('bank_name') is-invalid @enderror" required>
                            <option value="">-- Chọn ngân hàng --</option>
                            <option value="Vietcombank" {{ old('bank_name', $wallet->bank_name) == 'Vietcombank' ? 'selected' : '' }}>Vietcombank</option>
                            <option value="VietinBank" {{ old('bank_name', $wallet->bank_name) == 'VietinBank' ? 'selected' : '' }}>VietinBank</option>
                            <option value="BIDV" {{ old('bank_name', $wallet->bank_name) == 'BIDV' ? 'selected' : '' }}>BIDV</option>
                            <option value="Agribank" {{ old('bank_name', $wallet->bank_name) == 'Agribank' ? 'selected' : '' }}>Agribank</option>
                            <option value="Techcombank" {{ old('bank_name', $wallet->bank_name) == 'Techcombank' ? 'selected' : '' }}>Techcombank</option>
                            <option value="MB Bank" {{ old('bank_name', $wallet->bank_name) == 'MB Bank' ? 'selected' : '' }}>MB Bank</option>
                            <option value="ACB" {{ old('bank_name', $wallet->bank_name) == 'ACB' ? 'selected' : '' }}>ACB</option>
                            <option value="VPBank" {{ old('bank_name', $wallet->bank_name) == 'VPBank' ? 'selected' : '' }}>VPBank</option>
                            <option value="TPBank" {{ old('bank_name', $wallet->bank_name) == 'TPBank' ? 'selected' : '' }}>TPBank</option>
                            <option value="Sacombank" {{ old('bank_name', $wallet->bank_name) == 'Sacombank' ? 'selected' : '' }}>Sacombank</option>
                        </select>
                        @error('bank_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="bank_account" class="form-label">Số tài khoản <span class="text-danger">*</span></label>
                        <input type="text" name="bank_account" id="bank_account" 
                               class="form-control @error('bank_account') is-invalid @enderror" 
                               value="{{ old('bank_account', $wallet->bank_account) }}" required>
                        @error('bank_account')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="account_holder" class="form-label">Chủ tài khoản <span class="text-danger">*</span></label>
                        <input type="text" name="account_holder" id="account_holder" 
                               class="form-control @error('account_holder') is-invalid @enderror" 
                               value="{{ old('account_holder', $wallet->account_holder) }}" required>
                        @error('account_holder')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Tên chủ tài khoản ngân hàng (viết hoa không dấu)</small>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="active" {{ old('status', $wallet->status) == 'active' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="inactive" {{ old('status', $wallet->status) == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.wallet.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-2"></i>Hủy
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

