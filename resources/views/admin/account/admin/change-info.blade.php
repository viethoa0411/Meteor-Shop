@extends('admin.layouts.app')

@section('title', 'Thay đổi thông tin Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-gradient-primary text-white">
            <h5 class="mb-0">
                <i class="bi bi-shield-lock me-2"></i>
                Thay đổi thông tin Admin: {{ $user->name }} (ID: {{ $user->id }})
            </h5>
        </div>
        <div class="card-body">
            <!-- Thông báo thành công -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Lỗi validate -->
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Vui lòng sửa các lỗi sau:</strong>
                    <ul class="mt-2 mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Form gửi thông tin mới + gửi OTP -->
            <form action="{{ route('admin.account.admin.change-info.send-otp', $user->id) }}" method="POST">
                @csrf
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Vai trò <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="super_admin" {{ old('role', $user->role) == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Trạng thái <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Tạm khóa</option>
                            <option value="banned" {{ old('status', $user->status) == 'banned' ? 'selected' : '' }}>Cấm</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-warning btn-lg px-5">
                        <i class="bi bi-envelope-check me-2"></i>Gửi mã OTP xác nhận
                    </button>
                    <a href="{{ route('admin.account.admin.list') }}" class="btn btn-secondary btn-lg px-4">Hủy</a>
                </div>
            </form>

            <!-- Phần nhập OTP (chỉ hiện khi đã gửi OTP thành công) -->
            @if(session('admin_change_admin_info'))
                <hr class="my-5 border-secondary">
                <div class="alert alert-info p-4">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <strong>Mã OTP đã được gửi đến email:</strong> {{ $user->email }}<br>
                    <small>Mã có hiệu lực trong 10 phút. Vui lòng kiểm tra hộp thư (bao gồm cả Spam/Junk).</small>
                </div>

                <form action="{{ route('admin.account.admin.change-info.verify-otp', $user->id) }}" method="POST" class="border p-4 rounded bg-light">
                    @csrf
                    <div class="row align-items-center justify-content-center">
                        <div class="col-auto text-center">
                            <label class="form-label fw-bold fs-5 mb-3">Nhập mã OTP (6 số)</label>
                            <input type="text" name="otp" class="form-control form-control-lg text-center fw-bold" 
                                   placeholder="------" maxlength="6" style="width: 250px; letter-spacing: 10px; font-size: 24px;" required>
                            @error('otp')
                                <small class="text-danger d-block mt-2">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-auto mt-4 mt-md-0">
                            <button type="submit" class="btn btn-success btn-lg px-5">
                                <i class="bi bi-check-circle-fill me-2"></i>Xác nhận thay đổi
                            </button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection