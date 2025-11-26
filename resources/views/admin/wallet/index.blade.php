@extends('admin.layouts.app')

@section('title', 'Quản lý Ví')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-wallet2 me-2"></i>Quản lý Ví</h2>
            <a href="#" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Tạo ví mới
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                @if ($wallets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Chủ ví</th>
                                    <th>Ngân hàng</th>
                                    <th>Số tài khoản</th>
                                    <th>Chủ tài khoản</th>
                                    <th>Số dư</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($wallets as $wallet)
                                    <tr>
                                        <td>{{ $wallet->id }}</td>
                                        <td>
                                            <i class="bi bi-person-circle me-1"></i>
                                            {{ $wallet->user->name }}
                                        </td>
                                        <td>{{ $wallet->bank_name }}</td>
                                        <td><code>{{ $wallet->bank_account }}</code></td>
                                        <td>{{ $wallet->account_holder }}</td>
                                        <td>
                                            <strong class="text-success">
                                                {{ number_format($wallet->balance, 0, ',', '.') }} đ
                                            </strong>
                                        </td>
                                        <td>
                                            @if ($wallet->status === 'active')
                                                <span class="badge bg-success">Hoạt động</span>
                                            @else
                                                <span class="badge bg-secondary">Không hoạt động</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="#" 
                                                   class="btn btn-sm btn-info" title="Xem chi tiết">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="#" 
                                                   class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $wallets->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-wallet2 text-muted" style="font-size: 4rem;"></i>
                        <p class="text-muted mt-3">Chưa có ví nào. Hãy tạo ví mới!</p>
                        <a href="#" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Tạo ví mới
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

