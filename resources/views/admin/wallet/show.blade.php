@extends('admin.layouts.app')

@section('title', 'Chi tiết Ví')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-wallet2 me-2"></i>Chi tiết Ví</h2>
            <div>
                <a href="{{ route('admin.wallet.edit', $wallet->id) }}" class="btn btn-warning">
                    <i class="bi bi-pencil me-2"></i>Chỉnh sửa
                </a>
                <a href="{{ route('admin.wallet.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Quay lại
                </a>
            </div>
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

        <!-- Thông tin ví -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Thông tin Ví</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Chủ ví:</strong></div>
                            <div class="col-md-8">{{ $wallet->user->name }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Ngân hàng:</strong></div>
                            <div class="col-md-8">{{ $wallet->bank_name }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Số tài khoản:</strong></div>
                            <div class="col-md-8"><code>{{ $wallet->bank_account }}</code></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Chủ tài khoản:</strong></div>
                            <div class="col-md-8">{{ $wallet->account_holder }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Trạng thái:</strong></div>
                            <div class="col-md-8">
                                @if ($wallet->status === 'active')
                                    <span class="badge bg-success">Hoạt động</span>
                                @else
                                    <span class="badge bg-secondary">Không hoạt động</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm bg-success text-white">
                    <div class="card-body text-center">
                        <h6 class="mb-2">Số dư hiện tại</h6>
                        <h2 class="mb-0">{{ number_format($wallet->balance, 0, ',', '.') }} đ</h2>
                    </div>
                </div>

                <div class="card shadow-sm mt-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tổng thu:</span>
                            <strong class="text-success">{{ number_format($totalIncome, 0, ',', '.') }} đ</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tổng chi:</span>
                            <strong class="text-danger">{{ number_format($totalExpense, 0, ',', '.') }} đ</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Giao dịch chờ:</span>
                            <span class="badge bg-warning">{{ $pendingTransactions }}</span>
                        </div>
                    </div>
                </div>

                
            </div>
        </div>

        <!-- Lịch sử giao dịch -->
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Lịch sử Giao dịch</h5>
            </div>
            <div class="card-body">
                <!-- Bộ lọc -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Tất cả</option>
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                            <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                            <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Loại giao dịch</label>
                        <select name="type" class="form-select" onchange="this.form.submit()">
                            <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Tất cả</option>
                            <option value="income" {{ $type === 'income' ? 'selected' : '' }}>Thu</option>
                            <option value="expense" {{ $type === 'expense' ? 'selected' : '' }}>Chi</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tìm kiếm</label>
                        <input type="text" name="keyword" class="form-control" 
                               placeholder="Mã giao dịch, mô tả..." value="{{ $keyword ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-2"></i>Lọc
                        </button>
                    </div>
                </form>

                @include('admin.wallet.partials.transactions-table')
            </div>
        </div>
    </div>

    {{-- Modal hiển thị lịch sử hành động --}}
    @foreach ($transactions as $transaction)
        @if ($transaction->logs->count() > 0)
            <div class="modal fade" id="logModal{{ $transaction->id }}" tabindex="-1" aria-labelledby="logModalLabel{{ $transaction->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="logModalLabel{{ $transaction->id }}">
                                Lịch sử hành động - Mã GD: {{ $transaction->transaction_code }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Thời gian</th>
                                            <th>Người thực hiện</th>
                                            <th>Hành động</th>
                                            <th>Mô tả</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transaction->logs as $log)
                                            <tr>
                                                <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                                <td>
                                                    <strong>{{ $log->user->name }}</strong><br>
                                                    <small class="text-muted">{{ $log->user->email }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $log->action_label }}</span>
                                                </td>
                                                <td>{{ $log->description }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection

