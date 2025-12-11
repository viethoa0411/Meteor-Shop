<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-box-seam"></i> Sản phẩm trong đơn</h5>
        <span class="badge bg-primary">{{ $order->items->count() }} sản phẩm</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 order-view-table">
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
                                @php
                                    $product = $item->product;
                                    $imagePath = $item->image_path
                                        ?? ($product?->image ?? null)
                                        ?? ($product?->images?->first()?->image);
                                    $imageUrl = $imagePath
                                        ? asset('storage/' . ltrim($imagePath, '/'))
                                        : 'https://via.placeholder.com/60x60?text=No+Image';
                                @endphp
                                <img src="{{ $imageUrl }}" 
                                     alt="{{ $item->product_name }}" 
                                     class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
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
                        <td colspan="5" class="text-start fw-semibold">
                            Tổng tiền hàng:
                        </td>
                        <td class="text-end fw-semibold">
                            {{ number_format($order->sub_total ?? $order->total_price, 0, ',', '.') }}₫
                        </td>
                    </tr>
                    @if($order->discount_amount > 0)
                        <tr>
                            <td colspan="5" class="text-start fw-semibold">
                                Chiết khấu:
                            </td>
                            <td class="text-end fw-semibold order-discount-value">
                                -{{ number_format($order->discount_amount, 0, ',', '.') }}₫
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="5" class="text-start fw-semibold">
                            Phí vận chuyển:
                        </td>
                        <td class="text-end fw-semibold">
                            {{ number_format($order->shipping_fee ?? 0, 0, ',', '.') }}₫
                        </td>
                    </tr>
                    <tr class="order-total-row order-divider">
                        <td colspan="5" class="text-start fw-bold">
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
</div>

