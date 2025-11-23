<div class="row">
    {{-- Left Column --}}
    <div class="col-lg-8">
        {{-- Order Items --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-box-seam"></i> Sản phẩm trong đơn</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th class="fw-semibold" style="padding: 1rem 1.5rem; color: #212529 !important;">Sản phẩm</th>
                            <th class="fw-semibold" style="padding: 1rem 1.5rem; color: #212529 !important;">Giá</th>
                            <th class="fw-semibold" style="padding: 1rem 1.5rem; color: #212529 !important;">SL</th>
                            <th class="text-end fw-semibold" style="padding: 1rem 1.5rem; color: #212529 !important;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td style="color: #212529 !important;">
                                    <div class="d-flex align-items-center">
                                        @if($item->image_path)
                                            <img src="{{ asset('storage/' . $item->image_path) }}" 
                                                 alt="{{ $item->product_name }}" 
                                                 class="me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                        @endif
                                        <div>
                                            <div class="fw-bold" style="color: #212529 !important;">{{ $item->product_name }}</div>
                                            @if($item->variant_name)
                                                <small style="color: #6c757d !important;">{{ $item->variant_name }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td style="color: #212529 !important;">{{ number_format($item->price, 0, ',', '.') }}₫</td>
                                <td style="color: #212529 !important;">{{ $item->quantity }}</td>
                                <td class="text-end fw-bold" style="color: #212529 !important;">{{ number_format($item->subtotal, 0, ',', '.') }}₫</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-top">
                            <td colspan="3" class="text-start fw-semibold" style="padding: 1rem 1.5rem; background-color: #f8f9fa; color: #212529 !important;">
                                Tổng tiền hàng:
                            </td>
                            <td class="text-end fw-semibold" style="padding: 1rem 1.5rem; background-color: #f8f9fa; color: #212529 !important;">
                                {{ number_format($order->sub_total ?? $order->total_price, 0, ',', '.') }}₫
                            </td>
                        </tr>
                        @if($order->discount_amount > 0)
                            <tr>
                                <td colspan="3" class="text-start fw-semibold" style="padding: 0.75rem 1.5rem; background-color: #f8f9fa; color: #212529 !important;">
                                    Chiết khấu:
                                </td>
                                <td class="text-end fw-semibold" style="padding: 0.75rem 1.5rem; background-color: #f8f9fa; color: #dc3545 !important;">
                                    -{{ number_format($order->discount_amount, 0, ',', '.') }}₫
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="3" class="text-start fw-semibold" style="padding: 0.75rem 1.5rem; background-color: #f8f9fa; color: #212529 !important;">
                                Phí vận chuyển:
                            </td>
                            <td class="text-end fw-semibold" style="padding: 0.75rem 1.5rem; background-color: #f8f9fa; color: #212529 !important;">
                                {{ number_format($order->shipping_fee ?? 0, 0, ',', '.') }}₫
                            </td>
                        </tr>
                        <tr class="border-top border-2" style="background-color: #e7f3ff;">
                            <td colspan="3" class="text-start fw-bold" style="padding: 1.25rem 1.5rem; font-size: 1.1rem; color: #212529 !important;">
                                TỔNG CỘNG:
                            </td>
                            <td class="text-end fw-bold" style="padding: 1.25rem 1.5rem; font-size: 1.1rem; color: #0d6efd !important;">
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
                            <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                                {{ ucfirst($order->payment_status) }}
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

