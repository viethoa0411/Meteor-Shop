@extends('admin.layouts.app')

@section('title', 'Chi tiết hoàn tiền - ' . $transaction->transaction_code)

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-arrow-counterclockwise me-2"></i>Chi tiết hoàn tiền</h2>
            <div>
                <a href="{{ route('admin.wallet.show', $transaction->wallet_id) }}" class="btn btn-secondary">
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

        <div class="row">
            {{-- Thông tin giao dịch --}}
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Thông tin giao dịch</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Mã giao dịch:</strong></div>
                            <div class="col-md-8"><code>{{ $transaction->transaction_code }}</code></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Đơn hàng:</strong></div>
                            <div class="col-md-8">
                                @if ($transaction->order)
                                    <a href="{{ route('admin.orders.show', $transaction->order->id) }}">
                                        {{ $transaction->order->order_code }}
                                    </a>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Loại giao dịch:</strong></div>
                            <div class="col-md-8">
                                @if ($transaction->type === 'income')
                                    <span class="badge bg-success">Thu</span>
                                @else
                                    <span class="badge bg-danger">Chi</span>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Số tiền:</strong></div>
                            <div class="col-md-8">
                                <strong class="{{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                    {{ $transaction->type === 'income' ? '+' : '-' }}
                                    {{ number_format($transaction->amount, 0, ',', '.') }} đ
                                </strong>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Trạng thái:</strong></div>
                            <div class="col-md-8">
                                @if ($transaction->status === 'pending')
                                    <span class="badge bg-warning">Chờ xử lý</span>
                                @elseif ($transaction->status === 'completed')
                                    <span class="badge bg-success">Hoàn thành</span>
                                @else
                                    <span class="badge bg-secondary">Đã hủy</span>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Phương thức thanh toán:</strong></div>
                            <div class="col-md-8">{{ $transaction->payment_method ?? '-' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Mô tả:</strong></div>
                            <div class="col-md-8">{{ $transaction->description ?? '-' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Ngày tạo:</strong></div>
                            <div class="col-md-8">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</div>
                        </div>
                        @if ($transaction->completed_at)
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Ngày hoàn thành:</strong></div>
                                <div class="col-md-8">{{ $transaction->completed_at->format('d/m/Y H:i:s') }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Thông tin yêu cầu hoàn tiền --}}
                @if ($transaction->refund)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Thông tin yêu cầu hoàn tiền</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Loại hoàn tiền:</strong></div>
                                <div class="col-md-8">
                                    <span class="badge bg-info">{{ $transaction->refund->refund_type_label }}</span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Lý do:</strong></div>
                                <div class="col-md-8">{{ $transaction->refund->cancel_reason }}</div>
                            </div>
                            @if ($transaction->refund->reason_description)
                                <div class="row mb-3">
                                    <div class="col-md-4"><strong>Mô tả chi tiết:</strong></div>
                                    <div class="col-md-8">{{ $transaction->refund->reason_description }}</div>
                                </div>
                            @endif
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Số tiền hoàn:</strong></div>
                                <div class="col-md-8">
                                    <strong class="text-danger">
                                        {{ number_format($transaction->refund->refund_amount, 0, ',', '.') }} đ
                                    </strong>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Trạng thái:</strong></div>
                                <div class="col-md-8">
                                    @if ($transaction->refund->status === 'pending')
                                        <span class="badge bg-warning">Chờ xử lý</span>
                                    @elseif ($transaction->refund->status === 'approved')
                                        <span class="badge bg-info">Đã duyệt</span>
                                    @elseif ($transaction->refund->status === 'completed')
                                        <span class="badge bg-success">Hoàn thành</span>
                                    @else
                                        <span class="badge bg-danger">Từ chối</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Thông tin tài khoản nhận hoàn tiền --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="bi bi-bank me-2"></i>Thông tin tài khoản nhận hoàn tiền</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Ngân hàng:</strong></div>
                                <div class="col-md-8">{{ $transaction->refund->bank_name }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Số tài khoản:</strong></div>
                                <div class="col-md-8"><code>{{ $transaction->refund->bank_account }}</code></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Tên chủ tài khoản:</strong></div>
                                <div class="col-md-8">{{ $transaction->refund->account_holder }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Khách hàng:</strong></div>
                                <div class="col-md-8">
                                    {{ $transaction->refund->user->name }}<br>
                                    <small class="text-muted">{{ $transaction->refund->user->email }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Lịch sử hành động --}}
                @if ($transaction->logs->count() > 0)
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Lịch sử hành động</h5>
                        </div>
                        <div class="card-body">
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
                    </div>
                @endif
            </div>

            {{-- Sidebar - Thông tin ví và hành động --}}
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-wallet2 me-2"></i>Thông tin ví</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Ngân hàng:</strong><br>
                            {{ $transaction->wallet->bank_name }}
                        </div>
                        <div class="mb-3">
                            <strong>Số tài khoản:</strong><br>
                            <code>{{ $transaction->wallet->bank_account }}</code>
                        </div>
                        <div class="mb-3">
                            <strong>Số dư hiện tại:</strong><br>
                            <h4 class="text-success mb-0">
                                {{ number_format($transaction->wallet->balance, 0, ',', '.') }} đ
                            </h4>
                        </div>
                    </div>
                </div>

                {{-- Hành động --}}
                @if ($transaction->refund && $transaction->refund->status === 'pending')
                    <div class="card shadow-sm border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Xác nhận hoàn tiền</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <strong>Lưu ý:</strong> Khi xác nhận hoàn tiền:
                                <ul class="mb-0 mt-2">
                                    <li>Số dư ví sẽ bị trừ: <strong>{{ number_format($transaction->refund->refund_amount, 0, ',', '.') }} đ</strong></li>
                                    <li>Tạo giao dịch chi mới</li>
                                    <li>Trạng thái giao dịch gốc sẽ thay đổi</li>
                                </ul>
                            </div>
                            <a href="#">Xác nhận hoàn tiền</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

