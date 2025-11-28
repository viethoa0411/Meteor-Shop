@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">

    {{-- HEADER & TÊN ĐƠN HÀNG --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Chi tiết đơn hàng: <span class="text-primary">{{ $order->order_code }}</span></h2>
        <a href="{{ route('admin.orders.list') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    {{-- THÔNG BÁO --}}
    @if (session('success'))
        <div class="alert alert-success py-2">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger py-2">{{ session('error') }}</div>
    @endif

    {{-- PHÂN CHIA BỐ CỤC CHÍNH --}}
    <div class="row">
        {{-- CỘT TRÁI: HÀNH ĐỘNG, TRẠNG THÁI & LỊCH SỬ GỌN --}}
        <div class="col-lg-5">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Cập nhật & Lịch sử Đơn hàng</h5>
                </div>
                <div class="card-body">

                    {{-- TRẠNG THÁI HIỆN TẠI --}}
                    @php
                        // Logic dịch và màu trạng thái
                        $colors = [
                            'pending' => 'dark',
                            'processing' => 'primary',
                            'shipping' => 'info',
                            'completed' => 'success',
                            'cancelled' => 'danger',
                            'return_requested' => 'warning',
                            'returned' => 'secondary',
                        ];
                        $labels = [
                            'pending' => 'Chờ xác nhận',
                            'processing' => 'Đang xử lý',
                            'shipping' => 'Đang giao hàng',
                            'completed' => 'Hoàn thành',
                            'cancelled' => 'Đã hủy',
                            'return_requested' => 'Yêu cầu hoàn hàng',
                            'returned' => 'Đã trả hàng',
                        ];
                        $currentStatusColor = $colors[$order->order_status] ?? 'light';
                        $currentStatusLabel = $labels[$order->order_status] ?? ucfirst($order->order_status);
                    @endphp

                    <p class="h4 d-flex justify-content-between align-items-center">
                        <strong>Trạng thái:</strong>
                        <span class="badge bg-{{ $currentStatusColor }} py-2 px-3">{{ $currentStatusLabel }}</span>
                    </p>

                    {{-- FORM UPDATE TRẠNG THÁI --}}
                    @php
                        // Admin chỉ được cập nhật các trạng thái xử lý - không gồm hủy/hoàn thành
                        $adminAllowedStatuses = ['processing', 'shipping', 'returned'];

                        // Quy tắc chuyển trạng thái đồng bộ với backend
                        $validTransitions = [
                            'pending' => ['processing'],
                            'processing' => ['shipping', 'returned'],
                            'shipping' => ['returned'],
                            'return_requested' => ['returned'],
                            'returned' => [],
                            'completed' => [],
                            'cancelled' => [],
                        ];

                        $statusLabels = [
                            'processing' => 'Đang xử lý',
                            'shipping' => 'Đang giao hàng',
                            'returned' => 'Đã trả hàng',
                        ];

                        // Lấy các trạng thái tiếp theo có thể chuyển
                        $nextStatuses = isset($validTransitions[$order->order_status])
                            ? $validTransitions[$order->order_status]
                            : [];

                        // Lọc chỉ lấy các trạng thái admin được phép cập nhật
                        $allowedNextStatuses = array_filter($nextStatuses, function($status) use ($adminAllowedStatuses) {
                            return in_array($status, $adminAllowedStatuses);
                        });
                    @endphp

                    @if (!empty($allowedNextStatuses))
                        @php
                            $nextStatus = $allowedNextStatuses[0]; // Lấy trạng thái duy nhất
                            $nextStatusLabel = $statusLabels[$nextStatus] ?? ucfirst($nextStatus);
                        @endphp

                        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="mt-3">
                            @csrf
                            @method('PUT')

                            <p class="h6 mb-2">Trạng thái tiếp theo:</p>
                            <div class="d-flex align-items-center gap-3">
                                {{-- THẺ TRẠNG THÁI TO HƠN --}}
                                <span class="badge bg-info text-dark fs-5 py-2 px-3 fw-bold">
                                    {{ $nextStatusLabel }}
                                </span>

                                <input type="hidden" name="order_status" value="{{ $nextStatus }}">

                                {{-- NÚT CẬP NHẬT RÕ RÀNG HƠN --}}
                                <button type="submit" class="btn btn-primary btn-md">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Cập nhật ngay
                                </button>
                            </div>
                        </form>
                    @endif

                    <hr>

                    {{-- LỊCH SỬ THỜI GIAN --}}
                    <h6><i class="bi bi-calendar-check"></i> Mốc thời gian quan trọng</h6>
                    <ul class="list-group list-group-flush small">
                        @if ($order->created_at)
                            <li class="list-group-item d-flex justify-content-between">
                                Ngày đặt: <span>{{ date('d/m/Y H:i', strtotime($order->created_at)) }}</span>
                            </li>
                        @endif
                        @if ($order->confirmed_at)
                            <li class="list-group-item d-flex justify-content-between">
                                Xác nhận: <span class="text-success">{{ date('d/m/Y H:i', strtotime($order->confirmed_at)) }}</span>
                            </li>
                        @endif
                        @if ($order->shipped_at)
                            <li class="list-group-item d-flex justify-content-between">
                                Bắt đầu giao: <span class="text-info">{{ date('d/m/Y H:i', strtotime($order->shipped_at)) }}</span>
                            </li>
                        @endif
                        @if ($order->delivered_at)
                            <li class="list-group-item d-flex justify-content-between">
                                Giao thành công: <span class="text-primary">{{ date('d/m/Y H:i', strtotime($order->delivered_at)) }}</span>
                            </li>
                        @endif
                        @if ($order->order_status == 'returned' && isset($orderLogs) && $orderLogs->count() > 0)
                            @php
                                $returnedLog = $orderLogs->firstWhere('status', 'returned');
                            @endphp
                            @if ($returnedLog)
                                <li class="list-group-item d-flex justify-content-between text-secondary">
                                    Đã trả hàng: <span>{{ date('d/m/Y H:i', strtotime($returnedLog->created_at)) }}</span>
                                </li>
                            @endif
                        @endif
                    </ul>
                </div>
            </div>

            {{-- GHI CHÚ ĐƠN HÀNG --}}
            @if ($order->notes)
                <div class="card shadow-sm mb-4 border-secondary">
                    <div class="card-header bg-secondary text-white py-2">
                        <h6 class="mb-0"><i class="bi bi-chat-dots"></i> Ghi chú của Khách hàng</h6>
                    </div>
                    <div class="card-body py-2">
                        <p class="mb-0 small">{{ $order->notes }}</p>
                    </div>
                </div>
            @endif

            {{-- LỊCH SỬ CẬP NHẬT TRẠNG THÁI --}}
            <div class="card shadow-sm mb-4 border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-clock-history"></i> Lịch sử cập nhật trạng thái</h6>
                </div>
                <div class="card-body">
                    @php
                        $statusLabels = [
                            'pending' => 'Chờ xác nhận',
                            'processing' => 'Đang xử lý',
                            'shipping' => 'Đang giao hàng',
                            'completed' => 'Hoàn thành',
                            'return_requested' => 'Yêu cầu hoàn hàng',
                            'returned' => 'Đã trả hàng',
                            'cancelled' => 'Đã hủy',
                        ];
                        $statusColors = [
                            'pending' => 'dark',
                            'processing' => 'primary',
                            'shipping' => 'info',
                            'completed' => 'success',
                            'return_requested' => 'warning',
                            'returned' => 'secondary',
                            'cancelled' => 'danger',
                        ];
                    @endphp

                    @if (isset($orderLogs) && $orderLogs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Thời gian</th>
                                        <th>Trạng thái mới</th>
                                        <th>Người cập nhật</th>
                                        <th>Vai trò</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orderLogs as $log)
                                        <tr>
                                            <td class="small">{{ date('d/m/Y H:i', strtotime($log->created_at)) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $statusColors[$log->status] ?? 'light' }}">
                                                    {{ $statusLabels[$log->status] ?? $log->status }}
                                                </span>
                                            </td>
                                            <td class="small">
                                                @if ($log->admin)
                                                    <div>
                                                        <strong>{{ $log->admin->name }}</strong>
                                                        <br>
                                                        <span class="text-muted">{{ $log->admin->email }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary text-uppercase">{{ $log->role }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif (isset($statusHistory) && $statusHistory->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Thời gian</th>
                                        <th>Trạng thái cũ</th>
                                        <th>Trạng thái mới</th>
                                        <th>Người cập nhật</th>
                                        <th>Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($statusHistory as $history)
                                        <tr>
                                            <td class="small">{{ date('d/m/Y H:i', strtotime($history->created_at)) }}</td>
                                            <td>
                                                @if ($history->old_status)
                                                    <span class="badge bg-{{ $statusColors[$history->old_status] ?? 'light' }}">
                                                        {{ $statusLabels[$history->old_status] ?? $history->old_status }}
                                                    </span>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $statusColors[$history->new_status] ?? 'light' }}">
                                                    {{ $statusLabels[$history->new_status] ?? $history->new_status }}
                                                </span>
                                            </td>
                                            <td class="small">
                                                @if ($history->admin)
                                                    <div>
                                                        <strong>{{ $history->admin->name }}</strong>
                                                        <br>
                                                        <span class="text-muted">{{ $history->admin->email }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Đã xóa</span>
                                                @endif
                                            </td>
                                            <td class="small">
                                                @if ($history->note)
                                                    <span class="text-muted">{{ $history->note }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted small mb-0">Chưa có lịch sử cập nhật trạng thái.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- CỘT PHẢI: THÔNG TIN KHÁCH HÀNG, VẬN CHUYỂN & THANH TOÁN --}}
        <div class="col-lg-7">
            {{-- THÔNG TIN KHÁCH HÀNG & VẬN CHUYỂN --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Thông tin Khách hàng & Giao nhận</h5>
                </div>
                <div class="card-body">
                    <div class="row">

                        {{-- THÔNG TIN NGƯỜI NHẬN --}}
                        <div class="col-md-6">
                            <h6><i class="bi bi-person-fill"></i> Người nhận (Shipping)</h6>
                            <ul class="list-unstyled mb-2 small">
                                <li><strong>Tên:</strong> {{ $order->customer_name }}</li>
                                <li><strong>SĐT:</strong> {{ $order->customer_phone }}</li>
                                <li><strong>Email:</strong> {{ $order->customer_email ?? 'Không có' }}</li>
                            </ul>
                        </div>

                        {{-- THÔNG TIN USER (ĐẶT HÀNG) --}}
                        <div class="col-md-6 border-start">
                            <h6><i class="bi bi-person-circle"></i> Tài khoản đặt (User)</h6>
                            @if (isset($order->user_id) && $order->user_id)
                                @if (isset($order->user))
                                    <ul class="list-unstyled mb-2 small">
                                        <li><strong>Tên User:</strong> {{ $order->user->name ?? 'N/A' }}</li>
                                        <li><strong>Email:</strong> {{ $order->user->email ?? 'N/A' }}</li>
                                        <li><strong>SĐT:</strong> {{ $order->user->phone ?? 'N/A' }}</li>
                                        <li>
                                            <strong>Vai trò:</strong> <span class="badge bg-secondary me-1">{{ ucfirst($order->user->role) ?? 'N/A' }}</span>
                                            <strong>Status:</strong> @if ($order->user->status == 'active')
                                                <span class="badge bg-success">Hoạt động</span>
                                            @elseif ($order->user->status == 'banned')
                                                <span class="badge bg-danger">Bị cấm</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Ngưng</span>
                                            @endif
                                        </li>
                                    </ul>
                                @else
                                    <p class="small mb-2">Người dùng đã bị xóa (ID: {{ $order->user_id }})</p>
                                @endif
                            @else
                                <p class="small mb-2">Khách vãng lai</p>
                            @endif
                        </div>
                    </div>

                    <hr class="my-2">

                    {{-- ĐỊA CHỈ GIAO HÀNG --}}
                    <h6><i class="bi bi-geo-alt-fill"></i> Địa chỉ Giao hàng</h6>
                    <p class="mb-2 small">
                        {{ $order->shipping_address }},
                        {{ $order->shipping_ward ? $order->shipping_ward . ', ' : '' }}
                        {{ $order->shipping_district ? $order->shipping_district . ', ' : '' }}
                        {{ $order->shipping_city }}
                    </p>

                    {{-- THÔNG TIN VẬN CHUYỂN --}}
                    @if ($order->tracking_code)
                        <hr class="my-2">
                        <h6><i class="bi bi-truck"></i> Thông tin Tracking</h6>
                        <ul class="list-unstyled small mb-0">
                            <li><strong>ĐVVC:</strong> {{ $order->shipping_provider }} ({{ $order->shipping_method }})</li>
                            <li><strong>Mã Tracking:</strong> {{ $order->tracking_code }}</li>
                        </ul>
                    @endif
                </div>
            </div>

            {{-- THANH TOÁN & GIÁ TRỊ --}}
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Thanh toán & Giá trị</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="bi bi-credit-card-fill"></i> Thanh toán</h6>
                            <p class="mb-1 small">
                                <strong>P.Thức:</strong> {{ strtoupper($order->payment_method) }}
                                @if ($order->voucher_code)
                                    <br><strong>Voucher:</strong> <span class="badge bg-primary">{{ $order->voucher_code }}</span>
                                @endif
                            </p>
                            <p><strong>Trạng thái:</strong>
                                @if ($order->payment_status == 'paid')
                                    <span class="badge bg-success">Đã thanh toán</span>
                                @elseif ($order->payment_status == 'failed')
                                    <span class="badge bg-danger">Thất bại</span>
                                @else
                                    <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 border-start">
                            <h6><i class="bi bi-currency-dollar"></i> Tổng kết</h6>
                            <table class="table table-borderless table-sm small">
                                <tbody>
                                    <tr>
                                        <td>Tổng tiền hàng:</td>
                                        <td class="text-end">{{ number_format($order->total_price, 0, ',', '.') }}₫</td>
                                    </tr>
                                    <tr>
                                        <td>Chiết khấu:</td>
                                        <td class="text-end text-danger">-{{ number_format($order->discount_amount, 0, ',', '.') }}₫</td>
                                    </tr>
                                    <tr>
                                        <td>Phí vận chuyển:</td>
                                        <td class="text-end">{{ number_format($order->shipping_fee, 0, ',', '.') }}₫</td>
                                    </tr>
                                    <tr class="table-dark">
                                        <td><strong>TỔNG CUỐI:</strong></td>
                                        <td class="text-end">
                                            <strong>{{ number_format($order->final_total, 0, ',', '.') }}₫</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-4">

    {{-- DANH SÁCH SẢN PHẨM ĐÃ ĐẶT --}}
    <h5><i class="bi bi-cart-fill"></i> Danh sách sản phẩm đã đặt</h5>
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Sản phẩm</th>
                <th>Giá</th>
                <th>SL</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($orderDetails) && count($orderDetails) > 0)
                @foreach ($orderDetails as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ number_format($item->price, 0, ',', '.') }}₫</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->subtotal, 0, ',', '.') }}₫</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4" class="text-center text-muted">Không tìm thấy chi tiết sản phẩm.</td>
                </tr>
            @endif
        </tbody>
    </table>

</div>
@endsection
