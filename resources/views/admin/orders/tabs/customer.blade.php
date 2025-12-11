<div class="row">
    <div class="col-lg-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person"></i> Thông tin khách hàng</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="150" class="text-muted">Tên:</td>
                        <td class="fw-bold">{{ $order->customer_name ?? $order->user->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email:</td>
                        <td>{{ $order->customer_email ?? $order->user->email ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Số điện thoại:</td>
                        <td>{{ $order->customer_phone ?? $order->user->phone ?? 'N/A' }}</td>
                    </tr>
                    @if($order->user)
                        <tr>
                            <td class="text-muted">Tài khoản:</td>
                            <td>
                                <a href="{{ route('admin.account.users.show', $order->user->id) }}" 
                                   class="text-decoration-none">
                                    {{ $order->user->name }} (ID: {{ $order->user->id }})
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Vai trò:</td>
                            <td>
                                <span class="badge bg-secondary">{{ ucfirst($order->user->role) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Trạng thái:</td>
                            <td>
                                @if($order->user->status === 'active')
                                    <span class="badge bg-success">Hoạt động</span>
                                @elseif($order->user->status === 'banned')
                                    <span class="badge bg-danger">Bị cấm</span>
                                @else
                                    <span class="badge bg-warning">Ngưng</span>
                                @endif
                            </td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Địa chỉ giao hàng</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Người nhận:</strong>
                    <div class="mt-1">{{ $order->customer_name }}</div>
                </div>
                <div class="mb-3">
                    <strong>Số điện thoại:</strong>
                    <div class="mt-1">{{ $order->customer_phone }}</div>
                </div>
                <div>
                    <strong>Địa chỉ:</strong>
                    <div class="mt-1">
                        {{ $order->shipping_address }},<br>
                        {{ $order->shipping_ward }}, {{ $order->shipping_district }}, {{ $order->shipping_city }}
                    </div>
                </div>
            </div>
        </div>
        @if($order->user)
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-cart"></i> Lịch sử đơn hàng</h5>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div>Tổng đơn hàng: <strong>{{ $order->user->orders->count() }}</strong></div>
                        <div>Tổng giá trị: <strong>{{ number_format($order->user->orders->sum('final_total'), 0, ',', '.') }}₫</strong></div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

