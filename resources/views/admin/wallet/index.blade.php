@extends('admin.layouts.app')

@section('title', 'Quản lý Ví')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-wallet2 me-2"></i>Quản lý Ví</h4>
        <a href="{{ route('admin.wallet.settings') }}" class="btn btn-outline-secondary">
            <i class="bi bi-gear me-1"></i>Cài đặt
        </a>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ $tab == 'deposits' ? 'active' : '' }}" href="{{ route('admin.wallet.index', ['tab' => 'deposits']) }}">
                <i class="bi bi-arrow-down-circle me-1"></i>Nạp tiền
                @if($pendingDeposits > 0)
                    <span class="badge bg-danger ms-1">{{ $pendingDeposits > 99 ? '99+' : $pendingDeposits }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab == 'withdrawals' ? 'active' : '' }}" href="{{ route('admin.wallet.index', ['tab' => 'withdrawals']) }}">
                <i class="bi bi-arrow-up-circle me-1"></i>Rút tiền
                @if($pendingWithdraws > 0)
                    <span class="badge bg-warning text-dark ms-1">{{ $pendingWithdraws > 99 ? '99+' : $pendingWithdraws }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab == 'history' ? 'active' : '' }}" href="{{ route('admin.wallet.index', ['tab' => 'history']) }}">
                <i class="bi bi-clock-history me-1"></i>Lịch sử
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="card shadow-sm">
        <div class="card-body">
            @if($tab == 'deposits')
                @include('admin.wallet.partials.deposits-table')
            @elseif($tab == 'withdrawals')
                @include('admin.wallet.partials.withdrawals-table')
            @else
                @include('admin.wallet.partials.history-table')
            @endif
        </div>
    </div>
</div>
@endsection
