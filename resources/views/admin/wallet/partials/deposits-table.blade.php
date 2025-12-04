@if($deposits->count() > 0)
<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Mã YC</th>
                <th>Tài khoản</th>
                <th>Số tiền</th>
                <th>Trạng thái</th>
                <th>Thời gian</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($deposits as $deposit)
            <tr class="{{ $deposit->status == 'pending' ? 'table-warning' : '' }}">
                <td><code>{{ $deposit->request_code }}</code></td>
                <td>
                    <div class="fw-semibold">{{ $deposit->user->name }}</div>
                    <small class="text-muted">{{ $deposit->user->email }}</small>
                </td>
                <td class="fw-bold text-success">{{ $deposit->formatted_amount }}</td>
                <td>
                    @if($deposit->status == 'pending')
                        <span class="badge bg-warning text-dark">Chờ xác nhận</span>
                    @elseif($deposit->status == 'confirmed')
                        <span class="badge bg-success">Đã xác nhận</span>
                    @elseif($deposit->status == 'rejected')
                        <span class="badge bg-danger">Từ chối</span>
                    @else
                        <span class="badge bg-secondary">Đã hủy</span>
                    @endif
                </td>
                <td>{{ $deposit->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <a href="{{ route('admin.wallet.deposit.detail', $deposit->id) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-eye"></i> Chi tiết
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center mt-3">
    {{ $deposits->appends(['tab' => 'deposits'])->links() }}
</div>
@else
<div class="text-center py-5 text-muted">
    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
    <p>Không có yêu cầu nạp tiền nào</p>
</div>
@endif

