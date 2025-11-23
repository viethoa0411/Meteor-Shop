<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-box-seam"></i> Sản phẩm trong đơn</h5>
        <span class="badge bg-primary">{{ $order->items->count() }} sản phẩm</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="80">Ảnh</th>
                        <th>Sản phẩm</th>
                        <th>Biến thể</th>
                        <th class="text-end">Đơn giá</th>
                        <th class="text-center">SL</th>
                        <th class="text-end">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                @if($item->image_path)
                                    <img src="{{ asset('storage/' . $item->image_path) }}" 
                                         alt="{{ $item->product_name }}" 
                                         class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                         style="width: 60px; height: 60px;">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold">{{ $item->product_name }}</div>
                                @if($item->product)
                                    <small class="text-muted">SKU: {{ $item->product->id }}</small>
                                @endif
                            </td>
                            <td>
                                @if($item->variant_name)
                                    <span class="badge bg-secondary">{{ $item->variant_name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($item->price, 0, ',', '.') }}₫</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end fw-bold">{{ number_format($item->subtotal, 0, ',', '.') }}₫</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="5" class="text-start fw-semibold" style="padding: 1rem 1.5rem; color: #0d6efd !important;">
                            Tổng tiền hàng:
                        </td>
                        <td class="text-end fw-semibold" style="padding: 1rem 1.5rem; color: #0d6efd !important;">
                            {{ number_format($order->sub_total ?? $order->total_price, 0, ',', '.') }}₫
                        </td>
                    </tr>
                    @if($order->discount_amount > 0)
                        <tr>
                            <td colspan="5" class="text-start fw-semibold" style="padding: 0.75rem 1.5rem; color: #0d6efd !important;">
                                Chiết khấu:
                            </td>
                            <td class="text-end fw-semibold" style="padding: 0.75rem 1.5rem; color: #dc3545 !important;">
                                -{{ number_format($order->discount_amount, 0, ',', '.') }}₫
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="5" class="text-start fw-semibold" style="padding: 0.75rem 1.5rem; color: #0d6efd !important;">
                            Phí vận chuyển:
                        </td>
                        <td class="text-end fw-semibold" style="padding: 0.75rem 1.5rem; color: #0d6efd !important;">
                            {{ number_format($order->shipping_fee ?? 0, 0, ',', '.') }}₫
                        </td>
                    </tr>
                    <tr class="border-top border-2" style="background-color: #e7f3ff;">
                        <td colspan="5" class="text-start fw-bold" style="padding: 1.25rem 1.5rem; font-size: 1.1rem; color: #0d6efd !important;">
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
</div>

