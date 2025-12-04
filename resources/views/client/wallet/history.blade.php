@extends('client.layouts.app')

@section('title', 'Lịch sử giao dịch')

@push('head')
<style>
    .history-wrapper { padding: 40px 0; }
    .history-card {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        border: 1px solid #eee;
    }
    .balance-mini {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .filter-tabs .nav-link {
        border-radius: 20px;
        padding: 8px 16px;
        font-weight: 500;
        color: #666;
    }
    .filter-tabs .nav-link.active {
        background: #667eea;
        color: white;
    }
    .transaction-table th { font-weight: 600; color: #555; border-bottom: 2px solid #eee; }
    .transaction-table td { vertical-align: middle; padding: 16px 12px; }
    .type-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .type-deposit { background: #d4edda; color: #155724; }
    .type-withdraw { background: #f8d7da; color: #721c24; }
    .type-payment { background: #fff3cd; color: #856404; }
    .type-refund { background: #cce5ff; color: #004085; }
    .type-cashback { background: #d1ecf1; color: #0c5460; }
    .amount-credit { color: #28a745; font-weight: 700; }
    .amount-debit { color: #dc3545; font-weight: 700; }
</style>
@endpush

@section('content')
<div class="history-wrapper">
    <div class="mb-4">
        <a href="{{ route('client.account.wallet.index') }}" class="btn btn-link text-decoration-none p-0">
            <i class="bi bi-arrow-left me-1"></i> Quay lại ví
        </a>
    </div>

    <div class="history-card">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Lịch sử giao dịch</h4>
            <div class="balance-mini">
                <span>Số dư hiện tại:</span>
                <span class="fw-bold fs-5 ms-3">{{ $wallet->formatted_balance }}</span>
            </div>
        </div>

        <ul class="nav filter-tabs mb-4 flex-wrap gap-2">
            <li class="nav-item">
                <a class="nav-link {{ request('type', 'all') == 'all' ? 'active' : '' }}"
                   href="{{ route('client.account.wallet.history', ['type' => 'all']) }}">Tất cả</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('type') == 'deposit' ? 'active' : '' }}"
                   href="{{ route('client.account.wallet.history', ['type' => 'deposit']) }}">Nạp tiền</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('type') == 'withdraw' ? 'active' : '' }}"
                   href="{{ route('client.account.wallet.history', ['type' => 'withdraw']) }}">Rút tiền</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('type') == 'payment' ? 'active' : '' }}"
                   href="{{ route('client.account.wallet.history', ['type' => 'payment']) }}">Thanh toán</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('type') == 'refund' ? 'active' : '' }}"
                   href="{{ route('client.account.wallet.history', ['type' => 'refund']) }}">Hoàn tiền</a>
            </li>
        </ul>

        @if($transactions->count() > 0)
        <div class="table-responsive">
            <table class="table transaction-table">
                <thead>
                    <tr>
                        <th>Mã GD</th>
                        <th>Loại</th>
                        <th>Số tiền</th>
                        <th>Số dư sau GD</th>
                        <th>Mô tả</th>
                        <th>Thời gian</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $txn)
                    <tr>
                        <td><code>{{ $txn->transaction_code }}</code></td>
                        <td><span class="type-badge type-{{ $txn->type }}">{{ $txn->type_label }}</span></td>
                        <td class="{{ $txn->isCredit() ? 'amount-credit' : 'amount-debit' }}">
                            {{ $txn->formatted_amount }}
                        </td>
                        <td>{{ number_format($txn->balance_after, 0, ',', '.') }}đ</td>
                        <td>{{ $txn->description ?: '-' }}</td>
                        <td>{{ $txn->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $transactions->links() }}
        </div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
            <p class="mb-0">Chưa có giao dịch nào</p>
        </div>
        @endif
    </div>
</div>
@endsection

