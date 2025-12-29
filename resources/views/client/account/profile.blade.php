@extends('client.layouts.app')

@section('title', 'Thông tin tài khoản')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Thông tin tài khoản</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form action="{{ route('client.account.profile.update') }}" method="POST">
                        @csrf
                        <h5 class="mb-4">Thông tin cá nhân</h5>
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', auth()->user()->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control bg-light" value="{{ auth()->user()->email }}" disabled>
                            <small class="form-text text-muted">Email không thể thay đổi</small>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror"
                                value="{{ old('phone', auth()->user()->phone ?? '') }}">
                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle me-2"></i>Cập nhật thông tin
                        </button>
                    </form>

                    <hr class="my-5">
                    <h5 class="mb-4">Đổi mật khẩu</h5>

                    <!-- Hiển thị tất cả lỗi validate chung (nếu có) -->
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

                    <form action="{{ route('client.account.password.update') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-bold">
                                Mật khẩu hiện tại <span class="text-danger">*</span>
                            </label>
                            <input type="password"
                                name="current_password"
                                id="current_password"
                                class="form-control @error('current_password') is-invalid @enderror"
                                required
                                autocomplete="current-password">
                            @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">
                                Mật khẩu mới <span class="text-danger">*</span>
                                <small class="text-muted">(Tối thiểu 8 ký tự)</small>
                            </label>
                            <input type="password"
                                name="password"
                                id="password"
                                class="form-control @error('password') is-invalid @enderror"
                                required
                                minlength="8"
                                autocomplete="new-password">
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-bold">
                                Xác nhận mật khẩu mới <span class="text-danger">*</span>
                            </label>
                            <input type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                required
                                autocomplete="new-password">
                            @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-danger px-5">
                            <i class="bi bi-shield-lock me-2"></i>Đổi mật khẩu
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Thành công!',
        text: "{{ session('success') }}",
        timer: 4000,
        showConfirmButton: false
    });
</script>
@endif
@endsection