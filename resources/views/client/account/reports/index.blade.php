@extends('client.layouts.app')

@section('title', 'Báo cáo bình luận của tôi')

@section('content')
<div class="container py-4 py-md-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h4 fw-bold mb-1">
                        <i class="bi bi-flag-fill text-danger me-2"></i>
                        Báo cáo bình luận của bạn
                    </h2>
                    <p class="text-muted mb-0">
                        Theo dõi các bình luận bạn đã báo cáo để đảm bảo cộng đồng an toàn và văn minh.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end mb-4">
                <div class="col-md-4">
                    <label for="reason" class="form-label small text-muted mb-1">Lọc theo lý do</label>
                    <select name="reason" id="reason" class="form-select">
                        <option value="">Tất cả lý do</option>
                        <option value="spam" {{ ($reason ?? '') === 'spam' ? 'selected' : '' }}>Spam</option>
                        <option value="offensive" {{ ($reason ?? '') === 'offensive' ? 'selected' : '' }}>Xúc phạm</option>
                        <option value="false_info" {{ ($reason ?? '') === 'false_info' ? 'selected' : '' }}>Thông tin sai sự thật</option>
                        <option value="inappropriate" {{ ($reason ?? '') === 'inappropriate' ? 'selected' : '' }}>Nội dung không phù hợp</option>
                        <option value="other" {{ ($reason ?? '') === 'other' ? 'selected' : '' }}>Khác</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-funnel me-1"></i>Lọc
                    </button>
                    <a href="{{ route('client.account.review-reports.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Bình luận</th>
                            <th>Lý do</th>
                            <th>Mô tả</th>
                            <th>Thời gian báo cáo</th>
                            <th>Trạng thái xử lý</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                            @php
                                $review = $report->review;
                                $product = $review?->product;
                                $statusMap = [
                                    'pending' => ['class' => 'warning', 'text' => 'Chờ xử lý'],
                                    'approved' => ['class' => 'success', 'text' => 'Đã duyệt'],
                                    'rejected' => ['class' => 'danger', 'text' => 'Đã từ chối'],
                                    'hidden' => ['class' => 'secondary', 'text' => 'Đã ẩn bình luận'],
                                ];
                                $st = $statusMap[$review->status ?? 'pending'] ?? ['class' => 'secondary', 'text' => 'Không rõ'];
                            @endphp
                            <tr>
                                <td>
                                    @if($product)
                                        <a href="{{ route('client.product.detail', $product->slug) }}" target="_blank" class="text-decoration-none">
                                            {{ \Illuminate\Support\Str::limit($product->name, 40) }}
                                        </a>
                                    @else
                                        <span class="text-muted">Sản phẩm không còn tồn tại</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="small">
                                        {{ \Illuminate\Support\Str::limit($review->content ?? $review->comment ?? '', 80) }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-danger">{{ $report->reason_label }}</span>
                                </td>
                                <td>
                                    <div class="small text-muted">
                                        {{ \Illuminate\Support\Str::limit($report->description ?? 'Không có', 60) }}
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $report->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $st['class'] }}">{{ $st['text'] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                    <p class="text-muted mb-0">Bạn chưa báo cáo bình luận nào.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($reports->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                    <small class="text-muted">
                        Hiển thị
                        <span class="fw-semibold">{{ $reports->firstItem() }}</span> -
                        <span class="fw-semibold">{{ $reports->lastItem() }}</span>
                        trên tổng
                        <span class="fw-semibold text-primary">{{ $reports->total() }}</span> báo cáo
                    </small>
                    <div>
                        {{ $reports->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection


