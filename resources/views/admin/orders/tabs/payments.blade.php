<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-credit-card"></i> Lịch sử thanh toán</h5>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                    <i class="bi bi-plus-circle"></i> Thêm thanh toán
                </button>
            </div>
            <div class="card-body p-0">
                @if($order->payments && $order->payments->count() > 0)
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mã giao dịch</th>
                                <th>Phương thức</th>
                                <th>Số tiền</th>
                                <th>Trạng thái</th>
                                <th>Ngày</th>
                                <th>Người xử lý</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->payments as $payment)
                                <tr>
                                    <td>
                                        @if($payment->transaction_id)
                                            <code>{{ $payment->transaction_id }}</code>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ ucfirst($payment->payment_method) }}</td>
                                    <td class="fw-bold">{{ number_format($payment->amount, 0, ',', '.') }}₫</td>
                                    <td>
                                        @php
                                            $statusMeta = $payment->status_meta;
                                        @endphp
                                        <span class="badge bg-{{ $statusMeta['badge'] }}">
                                            {{ $statusMeta['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($payment->paid_at)
                                            {{ $payment->paid_at->format('d/m/Y H:i') }}
                                        @else
                                            {{ $payment->created_at->format('d/m/Y H:i') }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->processor)
                                            {{ $payment->processor->name }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-credit-card" style="font-size: 3rem;"></i>
                        <div class="mt-2">Chưa có giao dịch thanh toán</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Tổng kết thanh toán</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td>Tổng đơn hàng:</td>
                        <td class="text-end fw-bold">{{ number_format($order->final_total, 0, ',', '.') }}₫</td>
                    </tr>
                    @php
                        $paidSum = ($order->payments ? $order->payments->where('status', 'paid')->sum('amount') : 0);
                        if ($order->payment_method === 'cash' && $order->order_status === 'completed') {
                            $paidSum = $order->final_total;
                        }
                    @endphp
                    <tr>
                        <td>Đã thanh toán:</td>
                        <td class="text-end text-success">
                            {{ number_format($paidSum, 0, ',', '.') }}₫
                        </td>
                    </tr>
                    <tr>
                        <td>Đã hoàn tiền:</td>
                        <td class="text-end text-danger">
                            {{ number_format(($order->payments ? $order->payments->sum('refunded_amount') : 0), 0, ',', '.') }}₫
                        </td>
                    </tr>
                    <tr class="border-top">
                        <td><strong>Còn lại:</strong></td>
                        <td class="text-end">
                            @php
                                $remaining = max(0, $order->final_total - $paidSum);
                            @endphp
                            <strong class="text-{{ $remaining == 0 ? 'success' : 'warning' }}">
                                {{ number_format($remaining, 0, ',', '.') }}₫
                            </strong>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Add Payment Modal --}}
<div class="modal fade" id="addPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.orders.payments.store', $order->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Thêm thanh toán</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Phương thức thanh toán</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="cash">Tiền mặt</option>
                            <option value="bank">Chuyển khoản</option>
                            <option value="momo">Ví Momo</option>
                            <option value="paypal">PayPal</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số tiền</label>
                        <input type="number" name="amount" class="form-control" 
                               value="{{ $order->final_total }}" 
                               max="{{ $order->final_total }}" 
                               min="0" step="1000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mã giao dịch (nếu có)</label>
                        <input type="text" name="transaction_id" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>
