<div class="row">
    {{-- Left Column --}}
    <div class="col-lg-8">
        {{-- Order Items --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-box-seam"></i> Sản phẩm trong đơn</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0 order-view-table">
                    <thead>
                        <tr>
                            <th class="fw-semibold">Sản phẩm</th>
                            <th class="fw-semibold">Giá</th>
                            <th class="fw-semibold">SL</th>
                            <th class="text-end fw-semibold">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>
                                    @php
                                        $product = $item->product;
                                        $imagePath = $item->image_path
                                            ?? ($product?->image ?? null)
                                            ?? ($product?->images?->first()?->image);
                                        $imageUrl = $imagePath
                                            ? asset('storage/' . ltrim($imagePath, '/'))
                                            : 'https://via.placeholder.com/60x60?text=No+Image';
                                    @endphp
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $imageUrl }}"
                                             alt="{{ $item->product_name }}"
                                             class="me-3 rounded border"
                                             style="width: 55px; height: 55px; object-fit: cover;">
                                        <div>
                                            <div class="fw-bold order-product-name">{{ $item->product_name }}</div>
                                            @if($item->variant_name)
                                                <small class="text-muted">{{ $item->variant_name }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ number_format($item->price, 0, ',', '.') }}₫</td>
                                <td>{{ $item->quantity }}</td>
                                <td class="text-end fw-bold">{{ number_format($item->subtotal, 0, ',', '.') }}₫</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-start fw-semibold">
                                Tổng tiền hàng:
                            </td>
                            <td class="text-end fw-semibold">
                                {{ number_format($order->sub_total ?? $order->total_price, 0, ',', '.') }}₫
                            </td>
                        </tr>
                        @if($order->discount_amount > 0)
                            <tr>
                                <td colspan="3" class="text-start fw-semibold">
                                    Chiết khấu:
                                </td>
                                <td class="text-end fw-semibold order-discount-value">
                                    -{{ number_format($order->discount_amount, 0, ',', '.') }}₫
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="3" class="text-start fw-semibold">
                                Phí vận chuyển:
                            </td>
                            <td class="text-end fw-semibold">
                                {{ number_format($order->shipping_fee ?? 0, 0, ',', '.') }}₫
                            </td>
                        </tr>
                        <tr class="order-total-row order-divider">
                            <td colspan="3" class="text-start fw-bold">
                                TỔNG CỘNG:
                            </td>
                            <td class="text-end fw-bold">
                                {{ number_format($order->final_total, 0, ',', '.') }}₫
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Timeline --}}
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Lịch sử gần đây</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach(($order->timelines ?? collect())->take(5) as $timeline)
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="fw-bold">{{ $timeline->title }}</div>
                                <div class="text-muted small">{{ $timeline->description }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    {{ $timeline->created_at->format('d/m/Y H:i') }}
                                    @if($timeline->user)
                                        - {{ $timeline->user->name }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column --}}
    <div class="col-lg-4">
        {{-- Order Summary --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Thông tin đơn hàng</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">Mã đơn:</td>
                        <td class="fw-bold">{{ $order->order_code }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Ngày đặt:</td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Trạng thái:</td>
                        <td>
                            <span class="badge bg-{{ $order->status_meta['badge'] }}">
                                {{ $order->status_meta['label'] }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Thanh toán:</td>
                        <td>
                            @php
                                $paymentStatusLabels = [
                                    'paid' => 'Đã thanh toán',
                                    'pending' => 'Chưa thanh toán',
                                    'failed' => 'Thanh toán thất bại',
                                    'refunded' => 'Đã hoàn tiền',
                                ];
                                $paymentBadge = match($order->payment_status) {
                                    'paid' => 'success',
                                    'failed' => 'danger',
                                    'refunded' => 'secondary',
                                    default => 'warning',
                                };
                            @endphp
                            <span class="badge bg-{{ $paymentBadge }}">
                                {{ $paymentStatusLabels[$order->payment_status] ?? ucfirst($order->payment_status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Phương thức:</td>
                        <td>{{ $order->payment_label }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Customer Info --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person"></i> Khách hàng</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <strong>{{ $order->customer_name ?? $order->user->name ?? 'N/A' }}</strong>
                </div>
                <div class="small text-muted mb-1">
                    <i class="bi bi-envelope"></i> {{ $order->customer_email ?? $order->user->email ?? 'N/A' }}
                </div>
                <div class="small text-muted">
                    <i class="bi bi-telephone"></i> {{ $order->customer_phone ?? $order->user->phone ?? 'N/A' }}
                </div>
            </div>
        </div>

        {{-- Shipping Info --}}
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-truck"></i> Vận chuyển</h5>
            </div>
            <div class="card-body">
                <div class="small">
                    <div class="mb-2">
                        <strong>Địa chỉ:</strong><br>
                        {{ $order->shipping_address }},<br>
                        {{ $order->shipping_ward }}, {{ $order->shipping_district }}, {{ $order->shipping_city }}
                    </div>
                    @if($order->tracking_code)
                        <div class="mb-2">
                            <strong>Mã tracking:</strong><br>
                            <a href="{{ $order->tracking_url ?? '#' }}" target="_blank" class="text-decoration-none">
                                {{ $order->tracking_code }}
                            </a>
                        </div>
                    @endif
                    <div>
                        <strong>Phương thức:</strong> {{ $order->shipping_method }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<<<<<<< HEAD
=======

>>>>>>> origin/Trang_Chu_Client
