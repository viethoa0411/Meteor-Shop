@extends('admin.layouts.app')

@section('title', 'Cài đặt Ví')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('admin.wallet.index') }}" class="btn btn-link text-decoration-none p-0">
            <i class="bi bi-arrow-left me-1"></i> Quay lại quản lý ví
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Cài đặt thông tin ngân hàng</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.wallet.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Ngân hàng <span class="text-danger">*</span></label>
                                <select name="bank_name" class="form-select @error('bank_name') is-invalid @enderror" required>
                                    @foreach($bankCodes as $name => $code)
                                        <option value="{{ $name }}" {{ $settings->bank_name == $name ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Số tài khoản <span class="text-danger">*</span></label>
                                <input type="text" name="bank_account" class="form-control @error('bank_account') is-invalid @enderror" 
                                       value="{{ old('bank_account', $settings->bank_account) }}" required>
                                @error('bank_account')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Tên chủ tài khoản <span class="text-danger">*</span></label>
                                <input type="text" name="account_holder" class="form-control @error('account_holder') is-invalid @enderror" 
                                       value="{{ old('account_holder', $settings->account_holder) }}" required>
                                @error('account_holder')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Số điện thoại hỗ trợ <span class="text-danger">*</span></label>
                                <input type="text" name="support_phone" class="form-control @error('support_phone') is-invalid @enderror" 
                                       value="{{ old('support_phone', $settings->support_phone) }}" required>
                                @error('support_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email hỗ trợ</label>
                            <input type="email" name="support_email" class="form-control" 
                                   value="{{ old('support_email', $settings->support_email) }}">
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                       {{ $settings->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Kích hoạt chức năng ví</label>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <h6 class="fw-bold mb-2"><i class="bi bi-qr-code me-2"></i>Xem trước QR Code</h6>
                            <img src="{{ $settings->generateQrUrl(100000, 'Nap tien vi') }}" alt="QR Preview" class="rounded" style="max-width: 200px;">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Lưu cài đặt
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

