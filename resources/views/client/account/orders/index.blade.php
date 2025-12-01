@extends('client.layouts.app')

@section('title', 'Đơn hàng của tôi')

@push('head')
    <style>
        .account-wrapper {
            padding: 40px 0;
        }

        .orders-tabs .nav-link {
            border-radius: 30px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .orders-tabs .badge {
            font-size: 12px;
        }

        .order-card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
            border: 1px solid #ececec;
        }

        .order-products img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #eee;
        }

        .filter-card {
            border-radius: 16px;
            border: 1px dashed #cbd5f5;
            background: #fdfbff;
        }

        .empty-state {
            text-align: center;
            padding: 60px 0;
        }

        .empty-state img {
            max-width: 260px;
            margin-bottom: 24px;
        }

        .orders-pagination-wrapper {
            margin-top: 32px;
            padding: 24px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
            border: 1px solid #ececec;
        }

        .orders-pagination-wrapper .pagination {
            gap: 6px;
            margin: 0;
        }

        .orders-pagination-wrapper .page-link {
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            color: #374151;
            font-weight: 500;
            padding: 8px 12px;
            min-width: 40px;
            text-align: center;
            transition: all 0.2s ease;
            background: #fff;
        }

        .orders-pagination-wrapper .page-link:hover:not(.disabled) {
            background: #f3f4f6;
            border-color: #d1d5db;
            color: #111;
            transform: translateY(-1px);
        }

        .orders-pagination-wrapper .page-item.active .page-link {
            background: #111;
            border-color: #111;
            color: #fff;
            box-shadow: 0 4px 12px rgba(17, 17, 17, 0.15);
        }

        .orders-pagination-wrapper .page-item.disabled .page-link {
            background: #f9fafb;
            border-color: #e5e7eb;
            color: #9ca3af;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .orders-pagination-wrapper .page-link i {
            font-size: 14px;
            line-height: 1;
        }

        .orders-pagination-wrapper .small {
            font-size: 13px;
            color: #6b7280;
        }
    </style>
@endpush

@section('content')
    <div class="account-wrapper">
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('client.home') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item">Tài khoản</li>
                    <li class="breadcrumb-item active" aria-current="page">Đơn hàng</li>
                </ol>
            </nav>
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h2 class="fw-bold mb-1">Đơn hàng của tôi</h2>
                    <p class="text-muted mb-0">Theo dõi và quản lý toàn bộ đơn hàng một cách dễ dàng.</p>
                </div>

            </div>
        </div>

        <ul class="nav nav-pills orders-tabs gap-2 flex-wrap mb-4">
            @php
                $tabConfig = [
                    'all' => ['label' => 'Tất cả', 'icon' => 'bi-collection'],
                    'pending' => ['label' => 'Chờ xác nhận', 'icon' => 'bi-hourglass-split'],
                    'processing' => ['label' => 'Chuẩn bị hàng', 'icon' => 'bi-box'],
                    'shipping' => ['label' => 'Đang giao', 'icon' => 'bi-truck'],
                    'completed' => ['label' => 'Đã giao', 'icon' => 'bi-check2-circle'],
                    'cancelled' => ['label' => 'Đã hủy', 'icon' => 'bi-x-circle'],
                    'returned' => ['label' => 'Trả hàng', 'icon' => 'bi-arrow-repeat'],
                ];
            @endphp
            @foreach ($tabConfig as $tabKey => $tab)
                <li class="nav-item">
                    <a class="nav-link {{ $status === $tabKey ? 'active' : '' }}"
                        href="{{ route('client.account.orders.index', array_merge(request()->except('page'), ['status' => $tabKey])) }}">
                        <i class="bi {{ $tab['icon'] }}"></i>
                        {{ $tab['label'] }}
                        <span
                            class="badge bg-{{ $status === $tabKey ? 'light text-dark' : 'secondary' }}">{{ $statusCounts[$tabKey] ?? 0 }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="accordion mb-4" id="orderFilterAccordion">
            <div class="accordion-item filter-card">
                <h2 class="accordion-header" id="headingFilters">
                    <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse"
                        data-bs-target="#filtersCollapse" aria-expanded="true" aria-controls="filtersCollapse">
                        <i class="bi bi-funnel me-2 text-primary"></i> Bộ lọc nâng cao
                    </button>
                </h2>
                <div id="filtersCollapse" class="accordion-collapse collapse show" aria-labelledby="headingFilters"
                    data-bs-parent="#orderFilterAccordion">
                    <div class="accordion-body">
                        <form class="row g-3" method="GET"
                            action="{{ route('client.account.orders.index', ['status' => $status]) }}">
                            <div class="col-md-3">
                                <label class="form-label">Mã đơn hàng</label>
                                <input type="text" name="order_code" class="form-control"
                                    value="{{ $filters['order_code'] ?? '' }}" placeholder="Nhập mã đơn...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Từ ngày</label>
                                <input type="date" name="from_date" class="form-control"
                                    value="{{ $filters['from_date'] ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Đến ngày</label>
                                <input type="date" name="to_date" class="form-control"
                                    value="{{ $filters['to_date'] ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Phương thức thanh toán</label>
                                <select name="payment_method" class="form-select">
                                    <option value="">Tất cả</option>
                                    @foreach (\App\Models\Order::PAYMENT_LABELS as $method => $label)
                                        <option value="{{ $method }}"
                                            @selected(($filters['payment_method'] ?? '') === $method)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Giá tối thiểu</label>
                                <input type="number" min="0" name="min_total" class="form-control"
                                    value="{{ $filters['min_total'] ?? '' }}" placeholder="Từ 0 VNĐ">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Giá tối đa</label>
                                <input type="number" min="0" name="max_total" class="form-control"
                                    value="{{ $filters['max_total'] ?? '' }}" placeholder="Đến ... VNĐ">
                            </div>
                            <div class="col-md-6 d-flex align-items-end justify-content-end gap-3">
                                <a href="{{ route('client.account.orders.index', ['status' => $status]) }}"
                                    class="btn btn-light">Xóa lọc</a>
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-search me-1"></i> Áp dụng
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-octagon-fill me-2"></i>
                {{ session('error') }}
            </div>
        @endif

        @forelse ($orders as $order)
            <div class="order-card mb-4">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                    <div>
                        <div class="d-flex flex-wrap align-items-center gap-3 mb-2">
                            <span class="fw-semibold text-muted">#{{ $order->order_code }}</span>
                            <span class="badge bg-{{ $order->status_badge }}">
                                <i class="bi {{ $order->status_icon }} me-1"></i>{{ $order->status_label }}
                            </span>
                        </div>
                        <div class="text-muted small">
                            <span class="me-3"><i class="bi bi-calendar3 me-1"></i>
                                {{ optional($order->display_order_date)->format('d/m/Y H:i') }}</span>
                            <span class="me-3"><i class="bi bi-credit-card me-1"></i>{{ $order->payment_label }}</span>
                            <span><i class="bi bi-geo-alt me-1"></i>{{ $order->shipping_city ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="text-lg-end">
                        <div class="fw-bold fs-4 text-primary">{{ number_format($order->final_total, 0, ',', '.') }} đ</div>
                        <div class="text-muted small">Tổng tiền đã thanh toán</div>
                    </div>
                </div>

                <hr class="my-3">

                <div class="order-products">
                    @forelse ($order->items->take(2) as $item)
                        <div class="d-flex align-items-center gap-3 mb-3">
                            @php
                                $product = $item->product;
                                $productSlug = $product->slug ?? null;
                                $productName = $item->product_name ?? optional($product)->name ?? 'Sản phẩm không xác định';
                            @endphp
                            @if ($productSlug)
                                <a href="{{ route('client.product.detail', $productSlug) }}" class="text-decoration-none">
                                    <img src="{{ $item->image_path ? asset('storage/' . $item->image_path) : 'https://via.placeholder.com/80x80?text=No+Image' }}"
                                        alt="{{ $productName }}" style="cursor: pointer; transition: transform 0.2s;">
                                </a>
                            @else
                                <img src="{{ $item->image_path ? asset('storage/' . $item->image_path) : 'https://via.placeholder.com/80x80?text=No+Image' }}"
                                    alt="{{ $productName }}">
                            @endif
                            <div class="flex-grow-1">
                                @if ($productSlug)
                                    <a href="{{ route('client.product.detail', $productSlug) }}"
                                       class="text-decoration-none text-dark fw-semibold d-inline-block"
                                       style="transition: color 0.2s;">
                                        {{ $productName }}
                                    </a>
                                @else
                                    <div class="fw-semibold">{{ $productName }}</div>
                                @endif
                                <div class="text-muted small">
                                    @if ($item->variant_name)
                                        Biến thể: {{ $item->variant_name }} |
                                    @endif
                                    SL: {{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }} đ
                                </div>
                            </div>
                            <div class="fw-semibold">
                                {{ number_format($item->subtotal, 0, ',', '.') }} đ
                            </div>
                        </div>
                    @empty
                        <div class="text-muted text-center py-3">
                            <i class="bi bi-inbox"></i> Đơn hàng này chưa có sản phẩm
                        </div>
                    @endforelse

                    @php
                        $extraItems = $order->items->slice(2);
                    @endphp

                    @if ($extraItems->isNotEmpty())
                        <div class="collapse mt-2" id="order-extra-{{ $order->id }}">
                            @foreach ($extraItems as $item)
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    @php
                                        $product = $item->product;
                                        $productSlug = $product->slug ?? null;
                                        $productName = $item->product_name ?? optional($product)->name ?? 'Sản phẩm không xác định';
                                    @endphp
                                    @if ($productSlug)
                                        <a href="{{ route('client.product.detail', $productSlug) }}" class="text-decoration-none">
                                            <img src="{{ $item->image_path ? asset('storage/' . $item->image_path) : 'https://via.placeholder.com/80x80?text=No+Image' }}"
                                                alt="{{ $productName }}" style="cursor: pointer; transition: transform 0.2s;">
                                        </a>
                                    @else
                                        <img src="{{ $item->image_path ? asset('storage/' . $item->image_path) : 'https://via.placeholder.com/80x80?text=No+Image' }}"
                                            alt="{{ $productName }}">
                                    @endif
                                    <div class="flex-grow-1">
                                        @if ($productSlug)
                                            <a href="{{ route('client.product.detail', $productSlug) }}"
                                               class="text-decoration-none text-dark fw-semibold d-inline-block"
                                               style="transition: color 0.2s;">
                                                {{ $productName }}
                                            </a>
                                        @else
                                            <div class="fw-semibold">{{ $productName }}</div>
                                        @endif
                                        <div class="text-muted small">
                                            @if ($item->variant_name)
                                                Biến thể: {{ $item->variant_name }} |
                                            @endif
                                            SL: {{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }} đ
                                        </div>
                                    </div>
                                    <div class="fw-semibold">
                                        {{ number_format($item->subtotal, 0, ',', '.') }} đ
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                            <div class="text-muted fst-italic">
                                + {{ $extraItems->count() }} sản phẩm khác
                            </div>
                            <button class="btn btn-sm btn-outline-primary btn-toggle-products"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#order-extra-{{ $order->id }}"
                                data-open-text="Ẩn bớt sản phẩm"
                                data-close-text="Xem toàn bộ sản phẩm">
                                Xem toàn bộ sản phẩm
                            </button>
                        </div>
                    @endif
                </div>

                <hr class="my-3">

                {{-- BẮT ĐẦU PHẦN ĐÃ SỬA: CÁC NÚT HÀNH ĐỘNG --}}
                {{-- Đã thêm align-items-start để căn chỉnh các nút lên trên cùng --}}
                <div class="d-flex flex-wrap gap-2 align-items-start">
                    {{-- Nút cơ bản: Xem chi tiết --}}
                    <a class="btn btn-outline-secondary" href="{{ route('client.account.orders.show', $order) }}">
                        <i class="bi bi-eye me-1"></i> Xem chi tiết
                    </a>

                    {{-- Nút: Đã nhận hàng --}}
                    @if ($order->canReceive())
                        <form action="{{ route('client.account.orders.markReceived', $order) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success"
                                    onclick="return confirm('Bạn đã nhận được hàng? Xác nhận này sẽ cập nhật trạng thái đơn hàng sang \"Giao hàng thành công\".');">
                                <i class="bi bi-check-circle me-1"></i> Đã nhận hàng
                            </button>
                        </form>
                    @endif

                    {{-- Nút: Theo dõi vận đơn --}}
                    @if ($order->canTrack())
                        <a class="btn btn-outline-primary" href="{{ route('client.account.orders.tracking', $order) }}">
                            <i class="bi bi-truck me-1"></i> Theo dõi vận đơn
                        </a>
                    @endif

                    {{-- Nút: Hủy đơn --}}
                    @if ($order->canCancel())
                        <button class="btn btn-outline-danger btn-cancel-order" data-order-id="{{ $order->id }}"
                            data-order-code="{{ $order->order_code }}"
                            data-action="{{ route('client.account.orders.cancel', $order) }}">
                            <i class="bi bi-x-circle me-1"></i> Hủy đơn
                        </button>
                    @endif

                    {{-- Nút: Mua lại --}}
                    @if ($order->canReorder())
                        <form action="{{ route('client.account.orders.reorder', $order) }}" method="POST">
                            @csrf
                            <button class="btn btn-outline-dark" type="submit">
                                <i class="bi bi-arrow-repeat me-1"></i> Mua lại
                            </button>
                        </form>
                    @endif

                    {{-- Nút: Đánh giá --}}
                    @if ($order->canReview())
                        <button class="btn btn-outline-success" type="button">
                            <i class="bi bi-star me-1"></i> Đánh giá
                        </button>
                    @endif

                    {{-- Logic Yêu cầu đổi trả (Sử dụng flex-column và min-height để tránh lệch nút) --}}
                    @if ($order->canReturn())
                        @php
                            $daysRemaining = $order->getReturnDaysRemaining();
                        @endphp
                        {{-- Bọc nút và thông báo ngày còn lại trong d-flex flex-column --}}
                        <div class="d-flex flex-column">
                            <button class="btn btn-outline-warning btn-return-order" data-order-id="{{ $order->id }}"
                                data-order-code="{{ $order->order_code }}"
                                data-action="{{ route('client.account.orders.return', $order) }}">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Yêu cầu đổi trả
                            </button>
                            @if ($daysRemaining !== null && $daysRemaining > 0)
                                {{-- Thẻ small với thông tin ngày còn lại --}}
                                <small class="text-muted text-center mt-1" style="min-height: 18px;">
                                    (Còn {{ $daysRemaining }} ngày)
                                </small>
                            @else
                                {{-- Thẻ small rỗng với min-height để giữ khoảng trống, giúp nút không bị đẩy lên --}}
                                <small style="min-height: 18px;"></small>
                            @endif
                        </div>
                    @elseif ($order->order_status === 'completed' && $order->isReturnExpired() && in_array($order->return_status, ['none', 'rejected']))
                        {{-- Thông báo hết hạn đổi trả, dùng d-flex align-items-center để căn giữa theo chiều dọc --}}
                        {{-- Lưu ý: Với align-items-start ở container cha, khối này sẽ căn trên cùng --}}
                        <div class="d-flex align-items-start">
                            <div class="alert alert-warning small mb-0 py-2">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Đã quá thời hạn 7 ngày để yêu cầu đổi trả
                            </div>
                        </div>
                    @endif
                </div>
                {{-- KẾT THÚC PHẦN ĐÃ SỬA --}}
            </div>
        @empty
            <div class="order-card">
                <div class="empty-state">
                    <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png" alt="Empty">
                    <h4 class="fw-semibold">Chưa có đơn hàng nào</h4>
                    <p class="text-muted">Bạn chưa thực hiện giao dịch nào. Hãy tiếp tục mua sắm để lấp đầy danh sách này.</p>
                    <a href="{{ route('client.products.index') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-bag-plus me-1"></i> Tiếp tục mua sắm
                    </a>
                </div>
            </div>
        @endforelse

        @if ($orders->total() > 0)
            {{ $orders->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
        @endif
    </div>

    {{-- MODAL HỦY ĐƠN --}}
    <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" class="modal-content" id="cancelOrderForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelOrderModalLabel">Hủy đơn hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Vui lòng chọn lý do hủy đơn <span class="fw-semibold" id="cancelOrderCode"></span></p>
                    <div class="mb-3">
                        <label class="form-label">Lý do</label>
                        <select name="reason" class="form-select" required>
                            <option value="">-- Chọn lý do --</option>
                            <option value="Đổi ý, đặt nhầm">Đổi ý, đặt nhầm</option>
                            <option value="Muốn thay đổi địa chỉ">Muốn thay đổi địa chỉ</option>
                            <option value="Giá quá cao">Giá quá cao</option>
                            <option value="Khác">Khác</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Ghi chú</label>
                        <textarea class="form-control" rows="3" name="notes" placeholder="Bạn có thể mô tả thêm lý do hủy..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger">Xác nhận hủy</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL YÊU CẦU ĐỔI TRẢ --}}
    <div class="modal fade" id="returnOrderModal" tabindex="-1" aria-labelledby="returnOrderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form method="POST" class="modal-content" id="returnOrderForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="returnOrderModalLabel">Yêu cầu đổi trả</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Nhập thông tin cho đơn <span class="fw-semibold"
                                id="returnOrderCode"></span></p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Lý do đổi trả</label>
                            <select name="reason" class="form-select" required>
                                <option value="">-- Chọn lý do --</option>
                                <option value="Sản phẩm lỗi / hư hại">Sản phẩm lỗi / hư hại</option>
                                <option value="Giao sai sản phẩm">Giao sai sản phẩm</option>
                                <option value="Thiếu phụ kiện">Thiếu phụ kiện</option>
                                <option value="Khác">Khác</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Mô tả vấn đề</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Vui lòng mô tả chi tiết tình trạng sản phẩm..."
                                required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Ảnh minh họa (tối đa 3 ảnh)</label>
                            <input type="file" name="attachments[]" class="form-control" multiple accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-warning">Gửi yêu cầu</button>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cancelModal = new bootstrap.Modal(document.getElementById('cancelOrderModal'));
            const returnModal = new bootstrap.Modal(document.getElementById('returnOrderModal'));
            const cancelForm = document.getElementById('cancelOrderForm');
            const returnForm = document.getElementById('returnOrderForm');

            // Xử lý nút Hủy đơn
            document.querySelectorAll('.btn-cancel-order').forEach(btn => {
                btn.addEventListener('click', () => {
                    cancelForm.action = btn.dataset.action;
                    document.getElementById('cancelOrderCode').innerText = '#' + btn.dataset.orderCode;
                    cancelModal.show();
                });
            });

            // Xử lý nút Yêu cầu đổi trả
            document.querySelectorAll('.btn-return-order').forEach(btn => {
                btn.addEventListener('click', () => {
                    returnForm.action = btn.dataset.action;
                    document.getElementById('returnOrderCode').innerText = '#' + btn.dataset.orderCode;
                    returnModal.show();
                });
            });

            // Xử lý nút xem thêm sản phẩm
            document.querySelectorAll('.btn-toggle-products').forEach(btn => {
                const targetSelector = btn.getAttribute('data-bs-target');
                const target = document.querySelector(targetSelector);

                if (!target) return;

                // Thiết lập văn bản ban đầu
                btn.textContent = btn.dataset.closeText;

                // Cập nhật văn bản khi mở
                target.addEventListener('show.bs.collapse', () => {
                    btn.textContent = btn.dataset.openText;
                });

                // Cập nhật văn bản khi đóng
                target.addEventListener('hide.bs.collapse', () => {
                    btn.textContent = btn.dataset.closeText;
                });
            });
        });
    </script>
@endsection
