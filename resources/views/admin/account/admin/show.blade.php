@extends('admin.layouts.app')

@section('title', 'Chi tiết người dùng')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">Chi tiết người dùng</h1>
            <a href="{{ route('admin.account.admin.list') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-person-circle"></i> {{ $user->name }}</h5>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Email:</strong>
                        <p>{{ $user->email }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Số điện thoại:</strong>
                        <p>{{ $user->phone ?? 'Chưa cập nhật' }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Vai trò:</strong>
                        <p class="text-capitalize">{{ $user->role }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Trạng thái:</strong>
                        @if ($user->status == 'active')
                            <span class="badge bg-success">Hoạt động</span>
                        @elseif($user->status == 'inactive')
                            <span class="badge bg-warning text-dark">Không hoạt động</span>
                        @else
                            <span class="badge bg-danger">Bị cấm</span>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <strong>Địa chỉ:</strong>
                    <p>{{ $user->address ?? 'Chưa có địa chỉ' }}</p>
                </div>

                <div class="mb-3">
                    <strong>Ngày tạo:</strong>
                    <p>{{ $user->created_at->format('d/m/Y H:i') }}</p>
                </div>

                @if ($user->deleted_at)
                    <div class="mb-3">
                        <strong>Ngày bị ẩn:</strong>
                        <p class="text-danger">{{ $user->deleted_at->format('d/m/Y H:i') }}</p>
                    </div>
                @endif
            </div>

            <div class="card-footer text-end">
                <a href="{{ route('admin.account.admin.edit', $user->id) }}" class="btn btn-info">
                    <i class="bi bi-pencil-square"></i> Sửa
                </a>

      
            </div>
        </div>
    </div>
@endsection
