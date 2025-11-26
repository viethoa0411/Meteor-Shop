@extends('admin.layouts.app')

@section('title', 'Hoàn tiền - ' . $transaction->transaction_code)

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-arrow-counterclockwise me-2"></i>Hoàn tiền cho đơn hàng</h2>
            <a href="{{ route('admin.wallet.show', $transaction->wallet_id) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Quay lại
            </a>
        </div>

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Thông tin đơn hàng -->
            <div class="col-md-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-cart me-2"></i>Thông tin đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Mã đơn hàng:</strong></td>
                                        <td><code>{{ $transaction->order->order_code }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tổng tiền đơn hàng:</strong></td>
                                        <td><strong class="text-primary">{{ number_format($transaction->order->final_total, 0, ',', '.') }} đ</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Số tiền giao dịch:</strong></td>
                                        <td><strong class="text-danger">{{ number_format($transaction->amount, 0, ',', '.') }} đ</strong></td>
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
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form hoàn tiền -->
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="bi bi-person me-2"></i>Thông tin người nhận hoàn tiền</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.wallet.transaction.refund-process', $transaction->id) }}" method="POST">
                            @csrf
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label"><strong>Họ tên người đặt hàng:</strong></label>
                                    <input type="text" class="form-control" 
                                           value="{{ $transaction->order->customer_name ?? $transaction->order->user->name ?? 'N/A' }}" 
                                           readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><strong>Số điện thoại:</strong></label>
                                    <input type="text" class="form-control" 
                                           value="{{ $transaction->order->customer_phone ?? $transaction->order->shipping_phone ?? 'N/A' }}" 
                                           readonly>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label"><strong>Email:</strong></label>
                                    <input type="text" class="form-control" 
                                           value="{{ $transaction->order->customer_email ?? $transaction->order->user->email ?? 'N/A' }}" 
                                           readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><strong>Số tiền hoàn lại <span class="text-danger">*</span>:</strong></label>
                                    <input type="number" 
                                           class="form-control @error('refund_amount') is-invalid @enderror" 
                                           name="refund_amount" 
                                           value="{{ old('refund_amount', $transaction->amount) }}" 
                                           min="0" 
                                           max="{{ $transaction->amount }}" 
                                           step="1000" 
                                           required>
                                    <small class="text-muted">Tối đa: {{ number_format($transaction->amount, 0, ',', '.') }} đ</small>
                                    @error('refund_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr>

                            <h6 class="fw-bold mb-3">Thông tin tài khoản nhận hoàn tiền</h6>

                            @if($refundRequest)
                                <div class="alert alert-info mb-3">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Thông tin từ khách hàng:</strong> Khách hàng đã cung cấp thông tin tài khoản khi yêu cầu hủy đơn.
                                </div>
                            @endif

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label"><strong>Ngân hàng <span class="text-danger">*</span>:</strong></label>
                                    <input type="text" 
                                           class="form-control @error('bank_name') is-invalid @enderror" 
                                           name="bank_name" 
                                           value="{{ old('bank_name', $refundRequest->bank_name ?? '') }}" 
                                           placeholder="VD: Vietcombank, Techcombank..." 
                                           required>
                                    @error('bank_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label"><strong>Số tài khoản <span class="text-danger">*</span>:</strong></label>
                                    <input type="text" 
                                           class="form-control @error('bank_account') is-invalid @enderror" 
                                           name="bank_account" 
                                           value="{{ old('bank_account', $refundRequest->bank_account ?? '') }}" 
                                           placeholder="Nhập số tài khoản" 
                                           required>
                                    @error('bank_account')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label"><strong>Chủ tài khoản <span class="text-danger">*</span>:</strong></label>
                                    <input type="text" 
                                           class="form-control @error('account_holder') is-invalid @enderror" 
                                           name="account_holder" 
                                           value="{{ old('account_holder', $refundRequest->account_holder ?? '') }}" 
                                           placeholder="Tên chủ tài khoản" 
                                           required>
                                    @error('account_holder')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Lưu ý:</strong> Sau khi xác nhận hoàn tiền, đơn hàng sẽ được chuyển sang trạng thái "Đã hủy" và khách hàng sẽ nhận được thông báo hoàn tiền thành công.
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.wallet.show', $transaction->wallet_id) }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Hủy
                                </a>
                                <button type="submit" class="btn btn-danger" 
                                        onclick="return confirm('Bạn có chắc chắn muốn hoàn tiền cho đơn hàng này?');">
                                    <i class="bi bi-check-circle me-2"></i>Xác nhận hoàn tiền
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

