@extends('admin.layouts.app')

@section('title', 'Sửa người dùng')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Sửa thông tin người dùng</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.account.users.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- Tên --}}
                            <div class="mb-3">
                                <label class="form-label">Tên</label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $user->name) }}">
                            </div>

                            {{-- Email --}}
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ old('email', $user->email) }}">
                            </div>

                            {{-- Số điện thoại --}}
                            <div class="mb-3">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" name="phone" class="form-control"
                                    value="{{ old('phone', $user->phone) }}">
                            </div>

                            {{-- Mật khẩu --}}
                            <div class="mb-3">
                                <label class="form-label">Mật khẩu mới (bỏ trống nếu không đổi)</label>
                                <input type="password" name="password" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Xác nhận mật khẩu</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>

                            {{-- Vai trò --}}
                            <div class="mb-3">
                                <label class="form-label">Vai trò</label>
                                <select name="role" class="form-select">
                                    <!-- <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option> -->
                                    <!-- <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>Staff</option> -->
                                    <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                </select>
                            </div>

                            {{-- Trạng thái --}}
                            <div class="mb-3">
                                <label class="form-label">Trạng thái</label>
                                <select name="status" class="form-select">
                                    <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Hoạt động
                                    </option>
                                    <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Không hoạt
                                        động</option>
                                    <option value="banned" {{ $user->status == 'banned' ? 'selected' : '' }}>Bị cấm</option>
                                </select>
                            </div>

                            {{-- Địa chỉ --}}
                            <div class="mb-3">
                                <label class="form-label">Địa chỉ</label>
                                <textarea name="address" class="form-control">{{ old('address', $user->address) }}</textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.account.users.list') }}" class="btn btn-secondary">Quay lại</a>
                                <button type="submit" class="btn btn-primary">Cập nhật</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection