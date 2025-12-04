@extends('admin.layouts.app')

@section('title', 'Chi tiết nạp tiền')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('admin.wallet.index', ['tab' => 'deposits']) }}" class="btn btn-link text-decoration-none p-0">
            <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Thông tin yêu cầu nạp tiền</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Mã yêu cầu</label>
                            <div class="fw-bold"><code>{{ $deposit->request_code }}</code></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Trạng thái</label>
                            <div>
                                @if($deposit->status == 'pending')
                                    <span class="badge bg-warning text-dark fs-6">Chờ xác nhận</span>
                                @elseif($deposit->status == 'confirmed')
                                    <span class="badge bg-success fs-6">Đã xác nhận</span>
                                @elseif($deposit->status == 'rejected')
                                    <span class="badge bg-danger fs-6">Từ chối</span>
                                @else
                                    <span class="badge bg-secondary fs-6">Đã hủy</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Khách hàng</label>
                            <div class="fw-semibold">{{ $deposit->user->name }}</div>
                            <small class="text-muted">{{ $deposit->user->email }}</small>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Số dư ví hiện tại</label>
                            <div class="fw-bold text-primary fs-5">{{ $wallet->formatted_balance }}</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Số tiền yêu cầu nạp</label>
                            <div class="fw-bold text-success fs-4">{{ $deposit->formatted_amount }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Thời gian tạo</label>
                            <div>{{ $deposit->created_at->format('d/m/Y H:i:s') }}</div>
                        </div>
                    </div>
                    @if($deposit->note)
                    <div class="mb-3">
                        <label class="text-muted small">Ghi chú của khách</label>
                        <div class="p-2 bg-light rounded">{{ $deposit->note }}</div>
                    </div>
                    @endif

                    @if($deposit->isPending())
                    <hr>
                    <h6 class="fw-bold mb-3">Xác nhận nạp tiền</h6>
                    <form action="{{ route('admin.wallet.deposit.confirm', $deposit->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Số tiền xác nhận <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="confirmed_amount" class="form-control" 
                                           value="{{ $deposit->amount }}" min="1000" required>
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ghi chú admin</label>
                                <input type="text" name="admin_note" class="form-control" placeholder="Ghi chú...">
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i>Xác nhận nạp tiền
                            </button>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle me-1"></i>Từ chối
                            </button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Lịch sử giao dịch</h6>
                </div>
                <div class="card-body p-0">
                    @forelse($transactions as $txn)
                    <div class="p-3 border-bottom">
                        <div class="d-flex justify-content-between">
                            <span class="badge bg-{{ $txn->isCredit() ? 'success' : 'danger' }}">{{ $txn->type_label }}</span>
                            <span class="{{ $txn->isCredit() ? 'text-success' : 'text-danger' }} fw-bold">{{ $txn->formatted_amount }}</span>
                        </div>
                        <small class="text-muted">{{ $txn->created_at->format('d/m/Y H:i') }}</small>
                    </div>
                    @empty
                    <div class="p-3 text-center text-muted">Chưa có giao dịch</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.wallet.deposit.reject', $deposit->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Từ chối yêu cầu nạp tiền</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Lý do từ chối</label>
                        <textarea name="admin_note" class="form-control" rows="3" placeholder="Nhập lý do..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Xác nhận từ chối</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

