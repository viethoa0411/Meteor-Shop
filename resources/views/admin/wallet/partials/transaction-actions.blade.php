@if ($transaction->status === 'pending' && $transaction->order && in_array($transaction->payment_method, ['bank', 'momo']))
    <div class="d-flex flex-wrap gap-1">
        @if(!$transaction->marked_as_received_at)
            <form action="#"
                  method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-success"
                        onclick="return confirm('Đánh dấu giao dịch này là đã nhận?')"
                        title="Đã nhận - Chờ chốt">
                    <i class="bi bi-check-circle"></i> Đã nhận
                </button>
            </form>
            <a href="#"
               class="btn btn-sm btn-warning"
               title="Chưa Nhận - Xem thông tin đơn hàng">
                <i class="bi bi-exclamation-circle"></i> Chưa Nhận
            </a>
        @else
            <div class="d-flex flex-column">
                <span class="badge bg-warning text-dark">Đang chờ chốt</span>
                <small class="text-muted">
                    Bởi {{ $transaction->marker->name ?? 'Không xác định' }}
                </small>
            </div>
        @endif

        <a href="#"
           class="btn btn-sm btn-info"
           title="Hiển thị chi tiết giao dịch">
            <i class="bi bi-info-circle"></i> Chi Tiết
        </a>

        @php
            $hasPendingRefund = $transaction->order
                ? $transaction->order->refunds
                    ->where('refund_type', 'cancel')
                    ->where('status', 'pending')
                    ->isNotEmpty()
                : false;
        @endphp
        @if($hasPendingRefund)
            <a href="{{ route('admin.wallet.transaction.refund-form', $transaction->id) }}"
               class="btn btn-sm btn-danger"
               title="Hoàn tiền cho đơn hàng đã hủy">
                <i class="bi bi-arrow-counterclockwise"></i> Hoàn Tiền
            </a>
        @endif
    </div>
@elseif ($transaction->status === 'pending')
    <form action="#"
          method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-sm btn-success"
                onclick="return confirm('Xác nhận giao dịch này?')">
            <i class="bi bi-check-circle"></i> Xác nhận
        </button>
    </form>
    <form action="#"
          method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-sm btn-danger"
                onclick="return confirm('Hủy giao dịch này?')">
            <i class="bi bi-x-circle"></i> Hủy
        </button>
    </form>
    @if ($transaction->refund && $transaction->refund->status === 'pending')
        <a href="{{ route('admin.wallet.transaction.refund', $transaction->id) }}"
           class="btn btn-sm btn-warning"
           title="Xem chi tiết hoàn tiền">
            <i class="bi bi-arrow-counterclockwise"></i> Hoàn tiền
        </a>
    @endif

@endif

