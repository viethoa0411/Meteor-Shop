@extends('admin.layouts.app')

@section('title', 'Chi tiết giao dịch - ' . $transaction->transaction_code)

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-info-circle me-2"></i>Chi tiết giao dịch</h2>
            <a href="{{ route('admin.wallet.show', $transaction->wallet_id) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Quay lại
            </a>
        </div>

        <!-- Thông tin giao dịch -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Thông tin giao dịch</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Mã giao dịch:</strong></td>
                                        <td><code>{{ $transaction->transaction_code }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Số tiền:</strong></td>
                                        <td>
                                            <strong class="{{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                                {{ $transaction->type === 'income' ? '+' : '-' }}
                                                {{ number_format($transaction->amount, 0, ',', '.') }} đ
                                            </strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Loại:</strong></td>
                                        <td>
                                            @if ($transaction->type === 'income')
                                                <span class="badge bg-success">Thu</span>
                                            @else
                                                <span class="badge bg-danger">Chi</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phương thức:</strong></td>
                                        <td>{{ $transaction->payment_method === 'bank' ? 'Chuyển khoản ngân hàng' : ($transaction->payment_method === 'momo' ? 'Ví Momo' : $transaction->payment_method) }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Trạng thái:</strong></td>
                                        <td>
                                            @if ($transaction->status === 'pending')
                                                <span class="badge bg-warning">Chờ xử lý</span>
                                            @elseif ($transaction->status === 'completed')
                                                <span class="badge bg-success">Hoàn thành</span>
                                            @elseif ($transaction->status === 'cancelled')
                                                <span class="badge bg-danger">Đã hủy</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $transaction->status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ngày tạo:</strong></td>
                                        <td>{{ $transaction->created_at->format('d/m/Y H:i:s') }}</td>
                                    </tr>
                                    @if($transaction->completed_at)
                                    <tr>
                                        <td><strong>Ngày hoàn thành:</strong></td>
                                        <td>{{ $transaction->completed_at->format('d/m/Y H:i:s') }}</td>
                                    </tr>
                                    @endif
                                    @if($transaction->processor)
                                    <tr>
                                        <td><strong>Người xử lý:</strong></td>
                                        <td>{{ $transaction->processor->name }}</td>
                                    </tr>
                                    @endif
                                    @if($transaction->order)
                                    <tr>
                                        <td><strong>Đơn hàng:</strong></td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $transaction->order->id) }}">
                                                {{ $transaction->order->order_code }}
                                            </a>
                                        </td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                        @if($transaction->description)
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <strong>Mô tả:</strong>
                                <p class="mb-0">{{ $transaction->description }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Lịch sử hành động -->
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Lịch sử hành động</h5>
                    </div>
                    <div class="card-body">
                        @if($transaction->logs->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Thời gian</th>
                                            <th>Ngày giờ</th>
                                            <th>Người thực hiện</th>
                                            <th>Hành động</th>
                                            <th>Mô tả</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transaction->logs as $log)
                                            <tr>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $log->created_at->format('d/m/Y') }}<br>
                                                        {{ $log->created_at->format('H:i:s') }}
                                                    </small>
                                                </td>
                                                <td>
                                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                                </td>
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
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-3">Chưa có hành động nào được ghi nhận.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

