@extends('client.layouts.app')

@section('title', 'Hủy đơn và hoàn tiền - Đơn hàng #' . $order->order_code)

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
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="bi bi-x-circle me-2"></i>Hủy đơn hàng và hoàn tiền</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-warning mb-4">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Thông tin đơn hàng:</strong> #{{ $order->order_code }} - 
                            Tổng tiền: <strong>{{ number_format($order->final_total, 0, ',', '.') }} đ</strong><br>
                            <small>Bạn có chắc chắn muốn hủy đơn hàng này? Sau khi hủy, đơn hàng sẽ không thể khôi phục.</small>
                        </div>

                        <form action="{{ route('client.account.orders.refund.cancel.submit', $order) }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Lý do hủy đơn <span class="text-danger">*</span></label>
                                <select name="cancel_reason" id="cancel_reason" class="form-select" required>
                                    <option value="">-- Chọn lý do hủy đơn --</option>
                                    <option value="Đổi ý, không muốn mua nữa">Đổi ý, không muốn mua nữa</option>
                                    <option value="Tìm thấy sản phẩm rẻ hơn ở nơi khác">Tìm thấy sản phẩm rẻ hơn ở nơi khác</option>
                                    <option value="Thông tin đơn hàng sai">Thông tin đơn hàng sai</option>
                                    <option value="Địa chỉ giao hàng không đúng">Địa chỉ giao hàng không đúng</option>
                                    <option value="Không còn nhu cầu sử dụng">Không còn nhu cầu sử dụng</option>
                                    <option value="Thanh toán nhầm">Thanh toán nhầm</option>
                                    <option value="Khác">Khác</option>
                                </select>
                                @error('cancel_reason')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Mô tả chi tiết</label>
                                <textarea name="reason_description" class="form-control" rows="4" 
                                    placeholder="Vui lòng mô tả chi tiết lý do hủy đơn...">{{ old('reason_description') }}</textarea>
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

                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <strong>Cảnh báo quan trọng:</strong> Vui lòng nhập đúng số tài khoản nhận tiền. 
                                Người dùng nhập sai thông tin thì Đội ngũ Meteor Shop sẽ không chịu trách nhiệm.
                            </div>

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Lưu ý:</strong> Vui lòng kiểm tra kỹ thông tin tài khoản trước khi gửi. 
                                Chúng tôi sẽ xử lý yêu cầu hoàn tiền trong vòng 3-5 ngày làm việc. 
                                Đơn hàng sẽ được hủy ngay sau khi bạn gửi yêu cầu.
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-x-circle me-2"></i>Xác nhận hủy đơn và hoàn tiền
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

