@extends('admin.layouts.app')

@section('title', 'Lịch sử rút tiền - ' . $wallet->user->name)

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-journal-text me-2"></i>Lịch sử rút tiền</h2>
            <div>
                <a href="{{ route('admin.wallet.withdraw.form', $wallet->id) }}" class="btn btn-success">
                    <i class="bi bi-cash-coin me-2"></i>Rút tiền
                </a>
                <a href="{{ route('admin.wallet.show', $wallet->id) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Quay lại ví
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Ví: {{ $wallet->user->name }} - Số dư: {{ number_format($wallet->balance, 0, ',', '.') }} đ</h5>
            </div>
            <div class="card-body">
                @if ($withdrawals->count())
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Số tiền</th>
                                    <th>Ngân hàng</th>
                                    <th>Số tài khoản</th>
                                    <th>Người rút</th>
                                    <th>Xử lý bởi</th>
                                    <th>Thời gian</th>
                                    <th>Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($withdrawals as $withdrawal)
                                    <tr>
                                        <td>{{ $withdrawal->id }}</td>
                                        <td class="text-danger fw-bold">-{{ number_format($withdrawal->amount, 0, ',', '.') }} đ</td>
                                        <td>{{ $withdrawal->bank_name }}</td>
                                        <td><code>{{ $withdrawal->bank_account }}</code></td>
                                        <td>{{ $withdrawal->requester->name ?? 'N/A' }}</td>
                                        <td>{{ $withdrawal->processor->name ?? '-' }}</td>
                                        <td>
                                            {{ $withdrawal->processed_at?->format('d/m/Y H:i') ?? $withdrawal->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>{{ $withdrawal->note ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $withdrawals->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3">Chưa có lịch sử rút tiền.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

