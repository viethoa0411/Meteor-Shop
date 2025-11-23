@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Quản lý đơn hàng</h2>
        <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tạo đơn hàng
        </a>
    </div>

    {{-- Statistics Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Tổng đơn</div>
                    <div class="h4 mb-0">{{ number_format($stats['total']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-start border-warning border-3">
                <div class="card-body">
                    <div class="text-muted small">Chờ xác nhận</div>
                    <div class="h4 mb-0 text-warning">{{ number_format($stats['pending']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-start border-info border-3">
                <div class="card-body">
                    <div class="text-muted small">Đang xử lý</div>
                    <div class="h4 mb-0 text-info">{{ number_format($stats['processing']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-start border-primary border-3">
                <div class="card-body">
                    <div class="text-muted small">Đang giao</div>
                    <div class="h4 mb-0 text-primary">{{ number_format($stats['shipping']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-start border-success border-3">
                <div class="card-body">
                    <div class="text-muted small">Hoàn thành</div>
                    <div class="h4 mb-0 text-success">{{ number_format($stats['completed']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-start border-danger border-3">
                <div class="card-body">
                    <div class="text-muted small">Đã hủy</div>
                    <div class="h4 mb-0 text-danger">{{ number_format($stats['cancelled']) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.orders.list') }}" id="filterForm">
                <div class="row g-3">
                    {{-- Search --}}
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Tìm kiếm</label>
                        <input type="text" name="keyword" value="{{ request('keyword') }}" 
                               class="form-control form-control-sm" 
                               placeholder="Mã đơn, tên, email, SĐT...">
                    </div>

                    {{-- Order Status --}}
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Trạng thái đơn</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="all">Tất cả</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                            <option value="awaiting_payment" {{ request('status') == 'awaiting_payment' ? 'selected' : '' }}>Chờ thanh toán</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                            <option value="packed" {{ request('status') == 'packed' ? 'selected' : '' }}>Đã đóng gói</option>
                            <option value="shipping" {{ request('status') == 'shipping' ? 'selected' : '' }}>Đang giao</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Giao thành công</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Trả hàng</option>
                            <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Đã hoàn tiền</option>
                        </select>
                    </div>

                    {{-- Payment Status --}}
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Trạng thái thanh toán</label>
                        <select name="payment_status" class="form-select form-select-sm">
                            <option value="all">Tất cả</option>
                            <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Chờ thanh toán</option>
                            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                            <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Thất bại</option>
                        </select>
                    </div>

                    {{-- Date Range --}}
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Từ ngày</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" 
                               class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Đến ngày</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" 
                               class="form-control form-control-sm">
                    </div>

                    {{-- Actions --}}
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-search"></i> Lọc
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Bulk Actions Bar (sticky when items selected) --}}
    <div id="bulkActionsBar" class="card shadow-sm mb-3" style="display: none;">
        <div class="card-body py-2">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span id="selectedCount" class="fw-bold">0</span> đơn hàng đã chọn
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success" onclick="bulkAction('confirm')">
                        <i class="bi bi-check-circle"></i> Xác nhận
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="bulkAction('cancel')">
                        <i class="bi bi-x-circle"></i> Hủy
                    </button>
                    <button type="button" class="btn btn-sm btn-info" onclick="bulkAction('export')">
                        <i class="bi bi-download"></i> Xuất Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th width="40">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                            </th>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thanh toán</th>
                            <th>Vận chuyển</th>
                            <th width="120">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>
                                    <input type="checkbox" class="order-checkbox" value="{{ $order->id }}" 
                                           onchange="updateBulkActionsBar()">
                                </td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-decoration-none fw-bold">
                                        {{ $order->order_code }}
                                    </a>
                                </td>
                                <td>
                                    <div>{{ $order->customer_name ?? $order->user->name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $order->customer_email ?? $order->user->email ?? '' }}</small>
                                </td>
                                <td>
                                    <div>{{ $order->created_at->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                </td>
                                <td class="fw-bold">
                                    {{ number_format($order->final_total, 0, ',', '.') }}₫
                                </td>
                                <td>
                                    @php
                                        $statusMeta = $order->status_meta;
                                    @endphp
                                    <span class="badge bg-{{ $statusMeta['badge'] }}">
                                        <i class="bi {{ $statusMeta['icon'] ?? '' }}"></i>
                                        {{ $statusMeta['label'] }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $paymentStatusColors = [
                                            'pending' => 'warning',
                                            'paid' => 'success',
                                            'failed' => 'danger',
                                        ];
                                        $paymentColor = $paymentStatusColors[$order->payment_status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $paymentColor }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($order->latestShipment)
                                        <span class="badge bg-info">
                                            {{ $order->latestShipment->status_meta['label'] }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" 
                                           class="btn btn-outline-primary" title="Xem chi tiết">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if(in_array($order->order_status, ['pending', 'confirmed']))
                                            <a href="{{ route('admin.orders.edit', $order->id) }}" 
                                               class="btn btn-outline-secondary" title="Chỉnh sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                    <div class="mt-2">Không có đơn hàng nào</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($orders->hasPages())
            <div class="card-footer">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.order-checkbox');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateBulkActionsBar();
    }

    function updateBulkActionsBar() {
        const checked = document.querySelectorAll('.order-checkbox:checked');
        const count = checked.length;
        const bar = document.getElementById('bulkActionsBar');
        const countSpan = document.getElementById('selectedCount');
        
        if (count > 0) {
            bar.style.display = 'block';
            countSpan.textContent = count;
        } else {
            bar.style.display = 'none';
        }
    }

    function bulkAction(action) {
        const checked = document.querySelectorAll('.order-checkbox:checked');
        const orderIds = Array.from(checked).map(cb => cb.value);
        
        if (orderIds.length === 0) {
            alert('Vui lòng chọn ít nhất một đơn hàng');
            return;
        }

        if (action === 'export') {
            // Export
            const params = new URLSearchParams();
            orderIds.forEach(id => params.append('order_ids[]', id));
            window.location.href = '{{ route("admin.orders.export") }}?' + params.toString();
            return;
        }

        if (!confirm(`Bạn có chắc muốn ${action === 'confirm' ? 'xác nhận' : 'hủy'} ${orderIds.length} đơn hàng?`)) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.orders.bulkAction") }}';
        
        form.innerHTML = `
            @csrf
            <input type="hidden" name="action" value="${action}">
            ${orderIds.map(id => `<input type="hidden" name="order_ids[]" value="${id}">`).join('')}
        `;
        
        document.body.appendChild(form);
        form.submit();
    }
</script>
@endpush
@endsection
