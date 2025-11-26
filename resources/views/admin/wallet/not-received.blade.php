@extends('admin.layouts.app')

@section('title', 'Thông tin đơn hàng - Chưa Nhận')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-exclamation-circle me-2"></i>Thông tin đơn hàng - Chưa Nhận</h2>
            <a href="{{ route('admin.wallet.show', $transaction->wallet_id) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Quay lại
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Thông tin giao dịch -->
            <div class="col-md-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Thông tin giao dịch</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Mã giao dịch:</strong></td>
                                        <td><code>{{ $transaction->transaction_code }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Số tiền:</strong></td>
                                        <td><strong class="text-danger">{{ number_format($transaction->amount, 0, ',', '.') }} đ</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phương thức:</strong></td>
                                        <td>{{ $transaction->payment_method === 'bank' ? 'Chuyển khoản ngân hàng' : 'Ví Momo' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Trạng thái:</strong></td>
                                        <td><span class="badge bg-warning">Chờ xử lý</span></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Ngày tạo:</strong></td>
                                        <td>{{ $transaction->created_at->format('d/m/Y H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Mô tả:</strong></td>
                                        <td>{{ $transaction->description ?? 'Không có mô tả' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin đơn hàng -->
            <div class="col-md-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-cart me-2"></i>Thông tin đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Mã đơn hàng:</strong></td>
                                        <td><code>{{ $transaction->order->order_code }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Trạng thái:</strong></td>
                                        <td>
                                            @php
                                                $statusLabels = [
                                                    'pending' => 'Chờ xác nhận',
                                                    'processing' => 'Đang xử lý',
                                                    'shipping' => 'Đang giao hàng',
                                                    'completed' => 'Hoàn thành',
                                                    'cancelled' => 'Đã hủy',
                                                ];
                                                $statusColors = [
                                                    'pending' => 'dark',
                                                    'processing' => 'primary',
                                                    'shipping' => 'info',
                                                    'completed' => 'success',
                                                    'cancelled' => 'danger',
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$transaction->order->order_status] ?? 'secondary' }}">
                                                {{ $statusLabels[$transaction->order->order_status] ?? $transaction->order->order_status }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tổng tiền:</strong></td>
                                        <td><strong class="text-primary">{{ number_format($transaction->order->final_total, 0, ',', '.') }} đ</strong></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Ngày đặt:</strong></td>
                                        <td>{{ $transaction->order->created_at->format('d/m/Y H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phương thức thanh toán:</strong></td>
                                        <td>{{ $transaction->order->payment_method === 'bank' ? 'Chuyển khoản ngân hàng' : 'Ví Momo' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Thông tin khách hàng -->
                        <hr>
                        <h6 class="fw-bold mb-3"><i class="bi bi-person me-2"></i>Thông tin người đặt hàng</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Họ tên:</strong></td>
                                        <td>{{ $transaction->order->customer_name ?? $transaction->order->user->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Số điện thoại:</strong></td>
                                        <td>{{ $transaction->order->customer_phone ?? $transaction->order->shipping_phone ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $transaction->order->customer_email ?? $transaction->order->user->email ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Địa chỉ giao hàng:</strong></td>
                                        <td>{{ $transaction->order->shipping_address ?? 'N/A' }}</td>
                                    </tr>
                                    @if($transaction->order->shipping_city)
                                    <tr>
                                        <td><strong>Thành phố/Tỉnh:</strong></td>
                                        <td>{{ $transaction->order->shipping_city }}</td>
                                    </tr>
                                    @endif
                                    @if($transaction->order->shipping_district)
                                    <tr>
                                        <td><strong>Quận/Huyện:</strong></td>
                                        <td>{{ $transaction->order->shipping_district }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        <!-- Chi tiết sản phẩm -->
                        <hr>
                        <h6 class="fw-bold mb-3"><i class="bi bi-box-seam me-2"></i>Chi tiết sản phẩm</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Số lượng</th>
                                        <th>Đơn giá</th>
                                        <th>Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transaction->order->items as $item)
                                    <tr>
                                        <td>{{ $item->product_name ?? 'N/A' }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->price, 0, ',', '.') }} đ</td>
                                        <td>{{ number_format($item->quantity * $item->price, 0, ',', '.') }} đ</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nút hành động -->
            <div class="col-md-12">
                <div class="card shadow-sm border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Hành động</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Nếu đơn hàng không được xác nhận hoặc có vấn đề, bạn có thể hủy đơn hàng này. Hành động này sẽ:</p>
                        <ul class="text-muted mb-4">
                            <li>Chuyển trạng thái giao dịch sang <strong>"Đã hủy"</strong></li>
                            <li>Chuyển trạng thái đơn hàng sang <strong>"Đã hủy"</strong></li>
                        </ul>
                        <a href="#">Hủy đơn hàng vì chưa nhận tiền</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

