@extends('client.layouts.app')

@section('title', 'Trả hàng hoàn tiền - Đơn hàng #' . $order->order_code)

@section('content')
    <div class="py-5">
        <div class="mb-4">
            <a href="{{ route('client.account.orders.show', $order) }}" class="btn btn-link text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i> Quay lại chi tiết đơn hàng
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-arrow-counterclockwise me-2"></i>Yêu cầu trả hàng hoàn tiền</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Thông tin đơn hàng:</strong> #{{ $order->order_code }} - 
                            Tổng tiền: <strong>{{ number_format($order->final_total, 0, ',', '.') }} đ</strong>
                        </div>

                        <form action="{{ route('client.account.orders.refund.return.submit', $order) }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Lý do trả hàng <span class="text-danger">*</span></label>
                                <select name="cancel_reason" id="cancel_reason" class="form-select" required>
                                    <option value="">-- Chọn lý do trả hàng --</option>
                                    <option value="Sản phẩm không đúng mô tả">Sản phẩm không đúng mô tả</option>
                                    <option value="Sản phẩm bị lỗi/hỏng">Sản phẩm bị lỗi/hỏng</option>
                                    <option value="Sản phẩm không đúng kích thước">Sản phẩm không đúng kích thước</option>
                                    <option value="Sản phẩm không đúng màu sắc">Sản phẩm không đúng màu sắc</option>
                                    <option value="Không hài lòng với chất lượng">Không hài lòng với chất lượng</option>
                                    <option value="Đổi ý, không muốn mua nữa">Đổi ý, không muốn mua nữa</option>
                                    <option value="Khác">Khác</option>
                                </select>
                                @error('cancel_reason')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Mô tả chi tiết</label>
                                <textarea name="reason_description" class="form-control" rows="4" 
                                    placeholder="Vui lòng mô tả chi tiết lý do trả hàng...">{{ old('reason_description') }}</textarea>
                                @error('reason_description')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-4">

                            <h6 class="fw-bold mb-3">Thông tin tài khoản nhận hoàn tiền</h6>

                            <div class="mb-3">
                                <label class="form-label">Tên ngân hàng <span class="text-danger">*</span></label>
                                <input type="text" name="bank_name" class="form-control" 
                                    value="{{ old('bank_name') }}" 
                                    placeholder="Ví dụ: Vietcombank, Techcombank, BIDV..." required>
                                @error('bank_name')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Số tài khoản <span class="text-danger">*</span></label>
                                <input type="text" name="bank_account" class="form-control" 
                                    value="{{ old('bank_account') }}" 
                                    placeholder="Nhập số tài khoản ngân hàng" required>
                                @error('bank_account')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Tên chủ tài khoản <span class="text-danger">*</span></label>
                                <input type="text" name="account_holder" class="form-control" 
                                    value="{{ old('account_holder') }}" 
                                    placeholder="Nhập tên chủ tài khoản (viết hoa, không dấu)" required>
                                @error('account_holder')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Lưu ý:</strong> Vui lòng kiểm tra kỹ thông tin tài khoản trước khi gửi. 
                                Chúng tôi sẽ xử lý yêu cầu hoàn tiền trong vòng 3-5 ngày làm việc.
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send me-2"></i>Gửi yêu cầu
                                </button>
                                <a href="{{ route('client.account.orders.show', $order) }}" class="btn btn-outline-secondary">
                                    Hủy
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

