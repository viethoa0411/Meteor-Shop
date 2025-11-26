@extends('admin.layouts.app')

@section('title', 'Xác nhận đã nhận tiền - ' . $wallet->user->name)

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="bi bi-cash-stack me-2"></i>Xác nhận đã nhận tiền</h2>
                <p class="text-muted mb-0">Có {{ $totalCount }} giao dịch đang chờ chốt – Tổng tiền:
                    <strong class="text-success">{{ number_format($totalAmount, 0, ',', '.') }} đ</strong>
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.wallet.withdraw.history', $wallet->id) }}" class="btn btn-outline-primary">
                    <i class="bi bi-journal-text me-1"></i>Lịch sử rút tiền
                </a>
                <a href="{{ route('admin.wallet.show', $wallet->id) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Quay lại ví
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">Danh sách giao dịch đã đánh dấu "Đã nhận"</h5>
            </div>
            <div class="card-body">
                @if ($transactions->count())
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã giao dịch</th>
                                    <th>Đơn hàng</th>
                                    <th>Số tiền</th>
                                    <th>Đánh dấu bởi</th>
                                    <th>Thời gian đánh dấu</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td><code>{{ $transaction->transaction_code }}</code></td>
                                        <td>
                                            @if ($transaction->order)
                                                <a href="{{ route('admin.orders.show', $transaction->order->id) }}">
                                                    {{ $transaction->order->order_code }}
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-success fw-bold">
                                            + {{ number_format($transaction->amount, 0, ',', '.') }} đ
                                        </td>
                                        <td>
                                            {{ $transaction->marker->name ?? 'Không xác định' }}<br>
                                            <small class="text-muted">{{ $transaction->marker->email ?? '' }}</small>
                                        </td>
                                        <td>{{ $transaction->marked_as_received_at?->format('d/m/Y H:i') }}</td>
                                        <td class="d-flex gap-2 flex-wrap">
                                            <form action="{{ route('admin.wallet.transaction.settle', $transaction->id) }}"
                                                  method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success"
                                                        onclick="return confirm('Chốt giao dịch này?')">
                                                    <i class="bi bi-check-circle me-1"></i>Chốt
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.wallet.transaction.unmark', $transaction->id) }}"
                                                  method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-secondary"
                                                        onclick="return confirm('Hoàn tác đánh dấu đã nhận?')">
                                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Chưa nhận
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $transactions->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3">Không có giao dịch nào đang chờ chốt.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

