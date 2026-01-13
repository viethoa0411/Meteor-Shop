@extends('client.layouts.app')

@section('title', 'Ví của tôi')

@push('head')
<style>
    .wallet-wrapper { padding: 40px 0; }
    .wallet-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 30px;
        color: white;
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);
    }
    .wallet-balance { font-size: 2.5rem; font-weight: 700; }
    .wallet-label { opacity: 0.9; font-size: 0.95rem; }
    .wallet-actions { display: flex; gap: 12px; margin-top: 24px; }
    .wallet-btn {
        flex: 1;
        padding: 14px 20px;
        border-radius: 12px;
        font-weight: 600;
        text-align: center;
        text-decoration: none;
        transition: all 0.3s;
    }
    .wallet-btn-deposit { background: rgba(255,255,255,0.2); color: white; border: 2px solid rgba(255,255,255,0.3); }
    .wallet-btn-deposit:hover { background: rgba(255,255,255,0.3); color: white; }
    .wallet-btn-withdraw { background: white; color: #667eea; }
    .wallet-btn-withdraw:hover { background: #f8f9fa; color: #764ba2; }
    .transaction-card {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        border: 1px solid #eee;
    }
    .transaction-item {
        display: flex;
        align-items: center;
        padding: 16px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .transaction-item:last-child { border-bottom: none; }
    .transaction-icon {
        width: 48px; height: 48px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
        margin-right: 16px;
    }
    .transaction-icon.deposit { background: #d4edda; color: #28a745; }
    .transaction-icon.withdraw { background: #f8d7da; color: #dc3545; }
    .transaction-icon.payment { background: #fff3cd; color: #ffc107; }
    .transaction-icon.refund { background: #cce5ff; color: #007bff; }
    .transaction-info { flex: 1; }
    .transaction-type { font-weight: 600; color: #333; }
    .transaction-date { font-size: 0.85rem; color: #888; }
    .transaction-amount { font-weight: 700; font-size: 1.1rem; }
    .transaction-amount.credit { color: #28a745; }
    .transaction-amount.debit { color: #dc3545; }
</style>
@endpush

@section('content')
<div class="wallet-wrapper">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('client.home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active">Ví của tôi</li>
            </ol>
        </nav>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-5 mb-4">
            <div class="wallet-card">
                <div class="wallet-label"><i class="bi bi-wallet2 me-2"></i>Số dư ví</div>
                <div class="wallet-balance">{{ $wallet->formatted_balance }}</div>
                <div class="wallet-actions">
                    {{-- <a href="{{ route('client.account.wallet.deposit') }}" class="wallet-btn wallet-btn-deposit">
                        <i class="bi bi-plus-circle me-2"></i>Nạp tiền
                    </a> --}}
                    <a href="{{ route('client.account.wallet.withdraw') }}" class="wallet-btn wallet-btn-withdraw">
                        <i class="bi bi-arrow-up-circle me-2"></i>Rút tiền
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="transaction-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 fw-bold">Giao dịch gần đây</h5>
                    <a href="{{ route('client.account.wallet.history') }}" class="btn btn-sm btn-outline-primary">
                        Xem tất cả <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>

                @forelse($recentTransactions as $txn)
                    <div class="transaction-item">
                        <div class="transaction-icon {{ $txn->type }}">
                            @if($txn->type == 'deposit') <i class="bi bi-arrow-down"></i>
                            @elseif($txn->type == 'withdraw') <i class="bi bi-arrow-up"></i>
                            @elseif($txn->type == 'payment') <i class="bi bi-cart"></i>
                            @else <i class="bi bi-arrow-repeat"></i>
                            @endif
                        </div>
                        <div class="transaction-info">
                            <div class="transaction-type">{{ $txn->type_label }}</div>
                            <div class="transaction-date">{{ $txn->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="transaction-amount {{ $txn->isCredit() ? 'credit' : 'debit' }}">
                            {{ $txn->formatted_amount }}
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        Chưa có giao dịch nào
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

