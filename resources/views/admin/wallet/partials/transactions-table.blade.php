@if ($transactions->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Mã GD</th>
                    <th>Đơn hàng</th>
                    <th>Loại</th>
                    <th>Số tiền</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
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
                        <td>
                            @if ($transaction->type === 'income')
                                <span class="badge bg-success">Thu</span>
                            @else
                                <span class="badge bg-danger">Chi</span>
                            @endif
                        </td>
                        <td>
                            <strong class="{{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                {{ $transaction->type === 'income' ? '+' : '-' }}
                                {{ number_format($transaction->amount, 0, ',', '.') }} đ
                            </strong>
                        </td>
                        <td>
                            @if ($transaction->status === 'pending')
                                <span class="badge bg-warning">Chờ xử lý</span>
                            @elseif ($transaction->status === 'completed')
                                <span class="badge bg-success">Hoàn thành</span>
                            @else
                                <span class="badge bg-secondary">Đã hủy</span>
                            @endif
                        </td>
                        <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @include('admin.wallet.partials.transaction-actions')
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $transactions->appends(request()->query())->links() }}
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
        <p class="text-muted mt-3">Chưa có giao dịch nào.</p>
    </div>
@endif



Hiển thị tất cả các mã giao dịch
