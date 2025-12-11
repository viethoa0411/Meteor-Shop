@if($withdrawals->count() > 0)
<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Mã YC</th>
                <th>Tài khoản</th>
                <th>Số tiền</th>
                <th>Ngân hàng</th>
                <th>Trạng thái</th>
                <th>Thời gian</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($withdrawals as $withdraw)
            <tr class="{{ in_array($withdraw->status, ['pending', 'processing']) ? 'table-warning' : '' }}">
                <td><code>{{ $withdraw->request_code }}</code></td>
                <td>
                    <div class="fw-semibold">{{ $withdraw->user->name }}</div>
                    <small class="text-muted">{{ $withdraw->user->email }}</small>
                </td>
                <td class="fw-bold text-danger">{{ $withdraw->formatted_amount }}</td>
                <td>
                    <div>{{ $withdraw->bank_name }}</div>
                    <small class="text-muted">{{ $withdraw->account_number }}</small>
                </td>
                <td>
                    @if($withdraw->status == 'pending')
                        <span class="badge bg-warning text-dark">Chờ xử lý</span>
                    @elseif($withdraw->status == 'processing')
                        <span class="badge bg-info">Đang xử lý</span>
                    @elseif($withdraw->status == 'completed')
                        <span class="badge bg-success">Hoàn thành</span>
                    @elseif($withdraw->status == 'rejected')
                        <span class="badge bg-danger">Từ chối</span>
                    @else
                        <span class="badge bg-secondary">Đã hủy</span>
                    @endif
                </td>
                <td>{{ $withdraw->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <a href="{{ route('admin.wallet.withdraw.detail', $withdraw->id) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-eye"></i> Chi tiết
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center mt-3">
    {{ $withdrawals->appends(['tab' => 'withdrawals'])->links() }}
</div>
@else
<div class="text-center py-5 text-muted">
    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
    <p>Không có yêu cầu rút tiền nào</p>
</div>
@endif

