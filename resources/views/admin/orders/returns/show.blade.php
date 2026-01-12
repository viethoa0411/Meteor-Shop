@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Chi tiết yêu cầu trả hàng</h2>
        <div>
            <a href="{{ route('admin.orders.returns.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại danh sách
            </a>
            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-info">
                <i class="bi bi-file-text"></i> Xem đơn hàng
            </a>
        </div>
    </div>

    {{-- THÔNG BÁO --}}
    @if (session('success'))
        <div class="alert alert-success py-2">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger py-2">{{ session('error') }}</div>
    @endif

    <div class="row">
        {{-- THÔNG TIN ĐƠN HÀNG --}}
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Thông tin đơn hàng</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Mã đơn:</strong> {{ $order->order_code }}</p>
                            <p><strong>Khách hàng:</strong> {{ $order->customer_name }}</p>
                            <p><strong>Email:</strong> {{ $order->customer_email ?? 'N/A' }}</p>
                            <p><strong>Tổng tiền:</strong> <span class="text-danger fw-bold">{{ number_format($order->final_total, 0, ',', '.') }}₫</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Trạng thái đơn:</strong>
                                @php
                                    $orderStatusLabels = [
                                        'pending' => 'Chờ xác nhận',
                                        'processing' => 'Đang xử lý',
                                        'shipping' => 'Đang giao hàng',
                                        'completed' => 'Hoàn thành',
                                        'return_requested' => 'Yêu cầu trả hàng',
                                        'returned' => 'Đã trả hàng',
                                        'cancelled' => 'Đã hủy',
                                    ];
                                    $orderStatusColors = [
                                        'pending' => 'dark',
                                        'processing' => 'primary',
                                        'shipping' => 'info',
                                        'completed' => 'success',
                                        'return_requested' => 'warning',
                                        'returned' => 'secondary',
                                        'cancelled' => 'danger',
                                    ];
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
                                @endphp
                                <span class="badge bg-{{ $orderStatusColors[$order->order_status] ?? 'info' }}">
                                    {{ $orderStatusLabels[$order->order_status] ?? $order->order_status }}
                                </span>
                            </p>
                            <p><strong>Trạng thái trả hàng:</strong>
                                <span class="badge bg-{{ $returnStatusColors[$order->return_status] ?? 'secondary' }}">
                                    {{ $returnStatusLabels[$order->return_status] ?? $order->return_status }}
                                </span>
                            </p>
                            <p><strong>Ngày yêu cầu:</strong> {{ $order->updated_at ? date('d/m/Y H:i', strtotime($order->updated_at)) : '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- LÝ DO VÀ MÔ TẢ --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Lý do và mô tả trả hàng</h5>
                </div>
                <div class="card-body">
                    <p><strong>Lý do:</strong></p>
                    <p class="mb-3">{{ $order->return_reason ?? 'Không có' }}</p>

                    <p><strong>Mô tả chi tiết:</strong></p>
                    <p class="mb-0">{{ $order->return_note ?? 'Không có mô tả' }}</p>
                </div>
            </div>

            {{-- HÌNH ẢNH SẢN PHẨM KHÁCH GỬI VỀ --}}
            @php
                $attachments = [];
                if ($order->return_attachments) {
                    if (is_string($order->return_attachments)) {
                        // Nếu là JSON string, decode nó
                        $decoded = json_decode($order->return_attachments, true);
                        $attachments = is_array($decoded) ? $decoded : [$order->return_attachments];
                    } elseif (is_array($order->return_attachments)) {
                        $attachments = $order->return_attachments;
                    }
                }
            @endphp

            @if (count($attachments) > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-images"></i> Hình ảnh sản phẩm khách gửi về ({{ count($attachments) }})
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">
                            <i class="bi bi-info-circle"></i> Click vào hình để xem kích thước lớn
                        </p>
                        <div class="row g-3">
                            @foreach ($attachments as $index => $attachment)
                                <div class="col-md-4 col-sm-6">
                                    <div class="card border">
                                        <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="text-decoration-none">
                                            <img src="{{ asset('storage/' . $attachment) }}"
                                                 class="card-img-top img-thumbnail"
                                                 alt="Hình ảnh sản phẩm trả hàng {{ $index + 1 }}"
                                                 style="height: 250px; width: 100%; object-fit: cover; cursor: pointer; transition: transform 0.2s;"
                                                 onmouseover="this.style.transform='scale(1.05)'"
                                                 onmouseout="this.style.transform='scale(1)'"
                                                 onerror="this.src='{{ asset('images/no-image.png') }}'; this.onerror=null;">
                                        </a>
                                        <div class="card-body p-2 text-center">
                                            <small class="text-muted">Ảnh {{ $index + 1 }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="card shadow-sm mb-4 border-warning">
                    <div class="card-body">
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Chưa có hình ảnh đính kèm</strong> - Khách hàng chưa gửi hình ảnh sản phẩm.
                        </div>
                    </div>
                </div>
            @endif

            {{-- DANH SÁCH SẢN PHẨM --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Danh sách sản phẩm trong đơn</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Giá</th>
                                    <th>Số lượng</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orderDetails as $item)
                                    <tr>
                                        <td>{{ $item->product_name }}</td>
                                        <td>{{ number_format($item->price, 0, ',', '.') }}₫</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->subtotal, 0, ',', '.') }}₫</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- CỘT PHẢI: HÀNH ĐỘNG --}}
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Thao tác</h5>
                </div>
                <div class="card-body">
                    @if ($order->return_status === 'requested')
                        {{-- DUYỆT YÊU CẦU --}}
                        <form action="{{ route('admin.orders.returns.approve', $order->id) }}" method="POST" class="mb-3">
                            @csrf
                            <div class="mb-3">
                                <label for="admin_note" class="form-label">Ghi chú (tùy chọn):</label>
                                <textarea name="admin_note" id="admin_note" class="form-control" rows="3" placeholder="Nhập ghi chú nếu có..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('Bạn có chắc chắn muốn duyệt yêu cầu trả hàng này?')">
                                <i class="bi bi-check-circle"></i> Duyệt yêu cầu
                            </button>
                        </form>

                        {{-- TỪ CHỐI YÊU CẦU --}}
                        <form action="{{ route('admin.orders.returns.reject', $order->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="reject_reason" class="form-label">Lý do từ chối <span class="text-danger">*</span>:</label>
                                <textarea name="reject_reason" id="reject_reason" class="form-control" rows="3" required placeholder="Nhập lý do từ chối..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Bạn có chắc chắn muốn từ chối yêu cầu trả hàng này?')">
                                <i class="bi bi-x-circle"></i> Từ chối yêu cầu
                            </button>
                        </form>
                    @elseif ($order->return_status === 'approved')
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> <strong>Đã duyệt hoàn hàng.</strong> Tiếp theo: Nhận hàng và hoàn tiền cho khách hàng.
                        </div>
                        <form action="{{ route('admin.orders.returns.updateStatus', $order->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="return_status" value="refunded">
                            <div class="mb-3">
                                <label for="admin_note" class="form-label">Ghi chú:</label>
                                <textarea name="admin_note" id="admin_note" class="form-control" rows="3" placeholder="Nhập ghi chú về việc nhận hàng và hoàn tiền..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Xác nhận đã nhận hàng và hoàn tiền cho khách hàng?')">
                                <i class="bi bi-cash-coin"></i> Đã nhận hàng và hoàn tiền
                            </button>
                        </form>
                    @elseif ($order->return_status === 'refunded')
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill"></i> <strong>Đã nhận hàng và hoàn tiền.</strong> Quá trình trả hàng đã hoàn tất. Vui lòng cập nhật trạng thái đơn hàng sang "Đã trả hàng" tại trang chi tiết đơn hàng.
                        </div>
                    @elseif ($order->return_status === 'rejected')
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> Yêu cầu trả hàng đã bị từ chối.
                        </div>
                        @if ($order->return_note)
                            <p><strong>Lý do từ chối:</strong></p>
                            <p class="small">{{ $order->return_note }}</p>
                        @endif
                    @else
                        <div class="alert alert-secondary">
                            <i class="bi bi-info-circle"></i> Trạng thái:
                            <span class="badge bg-{{ $returnStatusColors[$order->return_status] ?? 'secondary' }}">
                                {{ $returnStatusLabels[$order->return_status] ?? $order->return_status }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
