@if($transactions->count() > 0)
<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Tài khoản</th>
                <th>Hành động</th>
                <th>Số tiền</th>
                <th>Thời gian</th>
                <th>Admin xử lý</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $txn)
            <tr>
                <td>
                    <div class="fw-semibold">{{ $txn->user->name }}</div>
                    <small class="text-muted">{{ $txn->user->email }}</small>
                </td>
                <td>
                    @if($txn->type == 'deposit')
                        <span class="badge bg-success"><i class="bi bi-arrow-down me-1"></i>Nạp tiền</span>
                    @elseif($txn->type == 'withdraw')
                        <span class="badge bg-danger"><i class="bi bi-arrow-up me-1"></i>Rút tiền</span>
                    @elseif($txn->type == 'payment')
                        <span class="badge bg-warning text-dark"><i class="bi bi-cart me-1"></i>Thanh toán</span>
                    @elseif($txn->type == 'refund')
                        <span class="badge bg-info"><i class="bi bi-arrow-repeat me-1"></i>Hoàn tiền</span>
                    @else
                        <span class="badge bg-primary"><i class="bi bi-gift me-1"></i>Cashback</span>
                    @endif
                </td>
                <td class="fw-bold {{ $txn->isCredit() ? 'text-success' : 'text-danger' }}">
                    {{ $txn->formatted_amount }}
                </td>
                <td>{{ $txn->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    @if($txn->processedBy)
                        {{ $txn->processedBy->name }}
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center mt-3">
    {{ $transactions->appends(['tab' => 'history'])->links() }}
</div>
@else
<div class="text-center py-5 text-muted">
    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
    <p>Chưa có giao dịch nào</p>
</div>
@endif

