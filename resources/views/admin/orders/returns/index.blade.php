@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Quản lý yêu cầu trả hàng</h2>
        <a href="{{ route('admin.orders.list') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách đơn hàng
        </a>
    </div>

    {{-- BỘ LỌC TRẠNG THÁI --}}
    <form method="GET" action="{{ route('admin.orders.returns.index') }}" class="row g-2 mb-3">
        <div class="col-md-3">
            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="all" {{ ($status == 'all' || $status == null) ? 'selected' : '' }}>Tất cả</option>
                <option value="requested" {{ $status == 'requested' ? 'selected' : '' }}>Đã yêu cầu</option>
                <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Đã duyệt hoàn hàng</option>
                <option value="refunded" {{ $status == 'refunded' ? 'selected' : '' }}>Đã nhận hàng và hoàn tiền</option>
                <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Hoàn hàng thành công</option>
                <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
            </select>
        </div>
    </form>

    {{-- DANH SÁCH ĐƠN HÀNG CÓ YÊU CẦU TRẢ HÀNG --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái đơn</th>
                            <th>Trạng thái trả hàng</th>
                            <th>Lý do</th>
                            <th>Ngày yêu cầu</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            @php
                                $returnStatusLabels = [
                                    'requested' => 'Đã yêu cầu',
                                    'approved' => 'Đã duyệt hoàn hàng',
                                    'rejected' => 'Đã từ chối',
                                    'refunded' => 'Đã nhận hàng và hoàn tiền',
                                    'completed' => 'Hoàn hàng thành công',
                                ];
                                $returnStatusColors = [
                                    'requested' => 'warning',
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    'refunded' => 'primary',
                                    'completed' => 'success',
                                ];
                                $orderStatusLabels = [
                                    'pending' => 'Chờ xác nhận',
                                    'processing' => 'Đang xử lý',
                                    'shipping' => 'Đang giao hàng',
                                    'completed' => 'Hoàn thành',
                                    'return_requested' => 'Yêu cầu trả hàng',
                                    'returned' => 'Đã trả hàng',
                                    'cancelled' => 'Đã hủy',
                                ];
                            @endphp
                            <tr>
                                <td><strong>{{ $order->order_code }}</strong></td>
                                <td>{{ $order->customer_name }}</td>
                                <td>{{ number_format($order->final_total, 0, ',', '.') }}₫</td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $orderStatusLabels[$order->order_status] ?? $order->order_status }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $returnStatusColors[$order->return_status] ?? 'secondary' }}">
                                        {{ $returnStatusLabels[$order->return_status] ?? $order->return_status }}
                                    </span>
                                </td>
                                <td class="small">
                                    {{ Str::limit($order->return_reason ?? 'Không có', 50) }}
                                </td>
                                <td class="small">
                                    {{ $order->updated_at ? date('d/m/Y H:i', strtotime($order->updated_at)) : '-' }}
                                </td>
                                <td>
                                    <a href="{{ route('admin.orders.returns.show', $order->id) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> Xem chi tiết
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Không có yêu cầu trả hàng nào
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
