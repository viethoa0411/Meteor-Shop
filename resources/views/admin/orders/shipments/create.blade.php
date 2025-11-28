@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1">Tạo đơn vận chuyển</h2>
            <div class="text-muted small">
                Đơn hàng: #{{ $order->order_code }}
            </div>
        </div>
        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    {{-- Order Summary --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">Thông tin đơn hàng</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-2"><strong>Khách hàng:</strong> {{ $order->customer_name }}</p>
                    <p class="mb-2"><strong>Điện thoại:</strong> {{ $order->customer_phone }}</p>
                    <p class="mb-2"><strong>Email:</strong> {{ $order->customer_email }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-2"><strong>Địa chỉ giao hàng:</strong></p>
                    <p class="mb-2 text-muted">
                        {{ $order->shipping_address }}, 
                        {{ $order->shipping_ward }}, 
                        {{ $order->shipping_district }}, 
                        {{ $order->shipping_city }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Create Shipment Form --}}
    <form action="{{ route('admin.orders.shipments.store', $order->id) }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">Thông tin vận chuyển</h5>
                    </div>
                    <div class="card-body">
                        {{-- Carrier Selection --}}
                        <div class="mb-3">
                            <label class="form-label">Đơn vị vận chuyển <span class="text-danger">*</span></label>
                            <select name="carrier" id="carrierSelect" class="form-select" required>
                                <option value="">-- Chọn đơn vị vận chuyển --</option>
                                <option value="ghn" {{ old('carrier') === 'ghn' ? 'selected' : '' }}>Giao Hàng Nhanh (GHN)</option>
                                <option value="ghtk" {{ old('carrier') === 'ghtk' ? 'selected' : '' }}>Giao Hàng Tiết Kiệm (GHTK)</option>
                                <option value="vnpost" {{ old('carrier') === 'vnpost' ? 'selected' : '' }}>Bưu Điện Việt Nam (VNPost)</option>
                                <option value="shippo" {{ old('carrier') === 'shippo' ? 'selected' : '' }}>Shippo</option>
                                <option value="manual" {{ old('carrier') === 'manual' ? 'selected' : '' }}>Vận chuyển thủ công</option>
                                <option value="other" {{ old('carrier') === 'other' ? 'selected' : '' }}>Khác</option>
                            </select>
                            @error('carrier')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Carrier Name (for other) --}}
                        <div class="mb-3" id="carrierNameGroup" style="display: none;">
                            <label class="form-label">Tên đơn vị vận chuyển</label>
                            <input type="text" name="carrier_name" class="form-control" 
                                   value="{{ old('carrier_name') }}" 
                                   placeholder="Nhập tên đơn vị vận chuyển">
                            @error('carrier_name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tracking Number --}}
                        <div class="mb-3">
                            <label class="form-label">Mã vận đơn</label>
                            <input type="text" name="tracking_number" class="form-control" 
                                   value="{{ old('tracking_number') }}" 
                                   placeholder="Nhập mã vận đơn (nếu có)">
                            @error('tracking_number')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tracking URL --}}
                        <div class="mb-3">
                            <label class="form-label">Link theo dõi</label>
                            <input type="url" name="tracking_url" class="form-control" 
                                   value="{{ old('tracking_url') }}" 
                                   placeholder="https://...">
                            @error('tracking_url')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Shipping Cost --}}
                        <div class="mb-3">
                            <label class="form-label">Phí vận chuyển</label>
                            <div class="input-group">
                                <input type="number" name="shipping_cost" class="form-control" 
                                       value="{{ old('shipping_cost', $order->shipping_fee) }}" 
                                       min="0" step="1000" placeholder="0">
                                <span class="input-group-text">VNĐ</span>
                            </div>
                            @error('shipping_cost')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Notes --}}
                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="notes" class="form-control" rows="3" 
                                      placeholder="Ghi chú về đơn vận chuyển...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column --}}
            <div class="col-lg-4">
                {{-- Order Items Summary --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Sản phẩm</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>SL</th>
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
                                                        : 'https://via.placeholder.com/40x40?text=No+Image';
                                                @endphp
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $imageUrl }}" 
                                                         alt="{{ $item->product->name ?? $item->product_name }}" 
                                                         class="me-2 rounded border" style="width: 40px; height: 40px; object-fit: cover;">
                                                    <div>
                                                        <div class="fw-bold">{{ $item->product->name ?? 'N/A' }}</div>
                                                        <small class="text-muted">{{ number_format($item->price, 0, ',', '.') }}₫</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="card shadow-sm">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-check-circle"></i> Tạo đơn vận chuyển
                        </button>
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-x-circle"></i> Hủy
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const carrierSelect = document.getElementById('carrierSelect');
        const carrierNameGroup = document.getElementById('carrierNameGroup');

        carrierSelect.addEventListener('change', function() {
            if (this.value === 'other') {
                carrierNameGroup.style.display = 'block';
                carrierNameGroup.querySelector('input').required = true;
            } else {
                carrierNameGroup.style.display = 'none';
                carrierNameGroup.querySelector('input').required = false;
                carrierNameGroup.querySelector('input').value = '';
            }
        });

        // Trigger on page load if old value exists
        if (carrierSelect.value === 'other') {
            carrierNameGroup.style.display = 'block';
        }
    });
</script>
@endpush
@endsection

