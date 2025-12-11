{{-- Status Change Modal --}}
<div class="modal fade" id="statusChangeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Thay đổi trạng thái đơn hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Trạng thái hiện tại</label>
                        <div class="alert alert-{{ $order->status_meta['badge'] }} mb-0">
                            <i class="bi {{ $order->status_meta['icon'] }}"></i>
                            <strong>{{ $order->status_meta['label'] }}</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Chuyển sang trạng thái <span class="text-danger">*</span></label>
                        <select name="order_status" class="form-select" id="newStatusSelect" required>
                            <option value="">-- Chọn trạng thái --</option>
                            @foreach($order->getAllowedNextStatuses() as $status)
                                @php
                                    $statusMeta = \App\Models\Order::STATUS_META[$status] ?? ['label' => ucfirst($status), 'badge' => 'secondary'];
                                @endphp
                                <option value="{{ $status }}" data-badge="{{ $statusMeta['badge'] }}">
                                    {{ $statusMeta['label'] }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Chỉ có thể chuyển sang các trạng thái hợp lệ</small>
                    </div>

                    <div class="mb-3" id="cancelReasonGroup" style="display: none;">
                        <label class="form-label">Lý do hủy <span class="text-danger">*</span></label>
                        <textarea name="cancel_reason" class="form-control" rows="3" 
                                  placeholder="Nhập lý do hủy đơn hàng..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ghi chú (tùy chọn)</label>
                        <textarea name="note" class="form-control" rows="2" 
                                  placeholder="Ghi chú về việc thay đổi trạng thái..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Xác nhận thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('newStatusSelect');
        const cancelReasonGroup = document.getElementById('cancelReasonGroup');
        const cancelReasonInput = cancelReasonGroup.querySelector('textarea[name="cancel_reason"]');

        statusSelect.addEventListener('change', function() {
            if (this.value === 'cancelled') {
                cancelReasonGroup.style.display = 'block';
                cancelReasonInput.required = true;
            } else {
                cancelReasonGroup.style.display = 'none';
                cancelReasonInput.required = false;
                cancelReasonInput.value = '';
            }
        });
    });
</script>
@endpush


