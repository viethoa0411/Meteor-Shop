@extends('client.layouts.app')

@section('title', 'Thanh toán')

@section('content')
    <div class="container py-5">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb" style="background:transparent; padding:0;">
                <li class="breadcrumb-item"><a href="{{ route('client.home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Giỏ hàng</a></li>
                <li class="breadcrumb-item active">Thanh toán</li>
            </ol>
        </nav>

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            {{-- Form thông tin --}}
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Thông tin khách hàng</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('client.checkout.process') }}" method="POST" id="checkoutForm">
                            @csrf

                            {{-- Họ tên --}}
                            <div class="mb-3">
                                <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" class="form-control"
                                    value="{{ old('customer_name', $checkoutData['customer_name'] ?? $user->name ?? '') }}" required>
                                @error('customer_name')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                {{-- Số điện thoại --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="text" name="customer_phone" class="form-control"
                                        value="{{ old('customer_phone', $checkoutData['customer_phone'] ?? $user->phone ?? '') }}" required>
                                    @error('customer_phone')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="customer_email" class="form-control"
                                        value="{{ old('customer_email', $checkoutData['customer_email'] ?? $user->email ?? '') }}" required>
                                    @error('customer_email')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Địa chỉ --}}
                            <div class="mb-3">
                                <label class="form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                <input type="text" name="shipping_city" class="form-control"
                                    value="{{ old('shipping_city') }}" required>
                                @error('shipping_city')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Quận/Huyện <span class="text-danger">*</span></label>
                                    <input type="text" name="shipping_district" class="form-control"
                                        value="{{ old('shipping_district') }}" required>
                                    @error('shipping_district')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phường/Xã <span class="text-danger">*</span></label>
                                    <input type="text" name="shipping_ward" class="form-control"
                                        value="{{ old('shipping_ward') }}" required>
                                    @error('shipping_ward')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Số nhà, tên đường <span class="text-danger">*</span></label>
                                <input type="text" name="shipping_address" class="form-control"
                                    value="{{ old('shipping_address', $checkoutData['shipping_address'] ?? '') }}" required>
                                @error('shipping_address')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Phương thức vận chuyển --}}
                            <div class="mb-3">
                                <label class="form-label">Phương thức vận chuyển <span class="text-danger">*</span></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="shipping_method" id="standard"
                                        value="standard" {{ old('shipping_method', 'standard') == 'standard' ? 'checked' : '' }}
                                        required>
                                    <label class="form-check-label" for="standard">
                                        <strong>Giao hàng tiêu chuẩn</strong> - 30.000đ (3-5 ngày)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="shipping_method" id="express"
                                        value="express" {{ old('shipping_method') == 'express' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="express">
                                        <strong>Giao hàng nhanh</strong> - 50.000đ (1-2 ngày)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="shipping_method" id="fast"
                                        value="fast" {{ old('shipping_method') == 'fast' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="fast">
                                        <strong>Giao hàng hỏa tốc</strong> - 70.000đ (Trong ngày)
                                    </label>
                                </div>
                                @error('shipping_method')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Phương thức thanh toán --}}
                            <div class="mb-3">
                                <label class="form-label">Phương thức thanh toán <span class="text-danger">*</span></label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" id="cash"
                                        value="cash" {{ old('payment_method', 'cash') == 'cash' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="cash">
                                        <strong>Thanh toán khi nhận hàng (COD)</strong>
                                    </label>
                                </div>

                                @auth
                                    @php
                                        $wallet = \App\Models\ClientWallet::where('user_id', auth()->id())->first();
                                        $walletBalance = $wallet ? $wallet->balance : 0;
                                    @endphp
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="wallet"
                                            value="wallet" {{ old('payment_method') == 'wallet' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="wallet">
                                            <strong>Thanh toán bằng Ví</strong>
                                            <span class="text-muted ms-2">(Số dư: {{ number_format($walletBalance) }}đ)</span>
                                        </label>
                                    </div>
                                    <div id="wallet-warning" class="alert alert-warning mt-2 py-2 d-none">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        Số dư ví không đủ. <a href="{{ route('client.account.wallet.deposit') }}">Nạp thêm tiền</a>
                                    </div>
                                @endauth

                                @error('payment_method')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Ghi chú --}}
                            <div class="mb-3">
                                <label class="form-label">Ghi chú đơn hàng</label>
                                <textarea name="notes" class="form-control" rows="3"
                                    placeholder="Ghi chú thêm cho đơn hàng...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-arrow-right me-2"></i>Tiếp tục xác nhận
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Tóm tắt đơn hàng --}}
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-cart-check me-2"></i>Tóm tắt đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        {{-- Danh sách sản phẩm --}}
                        <div class="mb-3 pb-3 border-bottom" style="max-height: 400px; overflow-y: auto;">
                            @foreach ($cartItems as $item)
                                <div class="d-flex mb-3">
                                    <img src="{{ $item['image'] ? asset('storage/' . $item['image']) : 'https://via.placeholder.com/80' }}"
                                        alt="{{ $item['name'] }}"
                                        style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                    <div class="ms-3 flex-grow-1">
                                        <h6 class="mb-1" style="font-size: 0.9rem;">{{ $item['name'] }}</h6>
                                        @if ($item['color'] || $item['size'])
                                            <small class="text-muted d-block">
                                                @if ($item['color'])
                                                    Màu: {{ $item['color'] }}
                                                @endif
                                                @if ($item['size'])
                                                    @if ($item['color']) | @endif
                                                    Size: {{ $item['size'] }}
                                                @endif
                                            </small>
                                        @endif
                                        <div class="mt-1">
                                            <small class="text-muted">SL: {{ $item['quantity'] }} x
                                                {{ number_format($item['price'], 0, ',', '.') }} đ
                                            </small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <strong class="text-danger" style="font-size: 0.9rem;">
                                            {{ number_format($item['subtotal'], 0, ',', '.') }} đ
                                        </strong>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Tổng tiền --}}
                        <div class="mb-2 d-flex justify-content-between">
                            <span>Tạm tính:</span>
                            <strong id="subtotal-display">{{ number_format($subtotal, 0, ',', '.') }} đ</strong>
                        </div>
                        <div class="mb-2 d-flex justify-content-between">
                            <span>Phí vận chuyển:</span>
                            <strong id="shipping-fee">-</strong>
                        </div>
                        <div class="mb-2 d-flex justify-content-between" id="discount-row" style="display:none;">
                            <span>Giảm giá (<span id="applied-code"></span>):</span>
                            <strong class="text-success" id="discount-amount">- 0 đ</strong>
                        </div>
                        <div class="mb-3 pt-2 border-top d-flex justify-content-between">
                            <span class="fs-5 fw-bold">Tổng cộng:</span>
                            <span class="fs-5 fw-bold text-danger" id="total-amount">
                                {{ number_format($subtotal, 0, ',', '.') }} đ
                            </span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mã khuyến mãi</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="promotion-code" placeholder="Nhập mã khuyến mãi">
                                <button class="btn btn-outline-primary" type="button" id="apply-promotion-btn">
                                    Áp dụng
                                </button>
                            </div>
                            <div class="small mt-2" id="promotion-message"></div>
                        </div>

                        <div class="alert alert-info small mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Miễn phí vận chuyển cho đơn hàng từ 500.000đ
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Load dữ liệu địa chỉ từ API
            let provinces = [];
            let districts = [];
            let wards = [];

            // Load tỉnh/thành phố
            async function loadProvinces() {
                try {
                    const response = await fetch('https://provinces.open-api.vn/api/?depth=1');
                    if (!response.ok) {
                        throw new Error('Không thể tải dữ liệu từ API');
                    }
                    provinces = await response.json();
                    const citySelect = document.getElementById('shipping_city');
                    if (!citySelect) return;
                    
                    provinces.forEach(province => {
                        const option = document.createElement('option');
                        option.value = province.name;
                        option.textContent = province.name;
                        option.dataset.code = province.code;
                        citySelect.appendChild(option);
                    });
                } catch (error) {
                    console.error('Lỗi khi tải danh sách tỉnh/thành phố:', error);
                    const citySelect = document.getElementById('shipping_city');
                    if (citySelect) {
                        const errorOption = document.createElement('option');
                        errorOption.value = '';
                        errorOption.textContent = 'Không thể tải dữ liệu. Vui lòng tải lại trang.';
                        citySelect.appendChild(errorOption);
                    }
                }
            }

            // Load quận/huyện
            async function loadDistricts(provinceCode) {
                try {
                    const response = await fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`);
                    if (!response.ok) {
                        throw new Error('Không thể tải dữ liệu quận/huyện');
                    }
                    const data = await response.json();
                    districts = data.districts || [];
                    const districtSelect = document.getElementById('shipping_district');
                    if (!districtSelect) return;
                    
                    districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                    districts.forEach(district => {
                        const option = document.createElement('option');
                        option.value = district.name;
                        option.textContent = district.name;
                        option.dataset.code = district.code;
                        districtSelect.appendChild(option);
                    });
                    districtSelect.disabled = false;
                    // Reset phường/xã
                    const wardSelect = document.getElementById('shipping_ward');
                    if (wardSelect) {
                        wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                        wardSelect.disabled = true;
                    }
                } catch (error) {
                    console.error('Lỗi khi tải danh sách quận/huyện:', error);
                    const districtSelect = document.getElementById('shipping_district');
                    if (districtSelect) {
                        districtSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
                    }
                }
            }

            // Load phường/xã
            async function loadWards(districtCode) {
                try {
                    const response = await fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`);
                    if (!response.ok) {
                        throw new Error('Không thể tải dữ liệu phường/xã');
                    }
                    const data = await response.json();
                    wards = data.wards || [];
                    const wardSelect = document.getElementById('shipping_ward');
                    if (!wardSelect) return;
                    
                    wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                    wards.forEach(ward => {
                        const option = document.createElement('option');
                        option.value = ward.name;
                        option.textContent = ward.name;
                        wardSelect.appendChild(option);
                    });
                    wardSelect.disabled = false;
                } catch (error) {
                    console.error('Lỗi khi tải danh sách phường/xã:', error);
                    const wardSelect = document.getElementById('shipping_ward');
                    if (wardSelect) {
                        wardSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
                    }
                }
            }

            document.addEventListener('DOMContentLoaded', async function() {
                // Lấy thông tin địa chỉ cũ từ reorder (nếu có)
                const savedCity = @json($checkoutData['shipping_city'] ?? null);
                const savedDistrict = @json($checkoutData['shipping_district'] ?? null);
                const savedWard = @json($checkoutData['shipping_ward'] ?? null);

                // Load tỉnh/thành phố khi trang load
                await loadProvinces();

                // Nếu có địa chỉ cũ, tự động chọn
                if (savedCity) {
                    const citySelect = document.getElementById('shipping_city');
                    for (let option of citySelect.options) {
                        if (option.value === savedCity) {
                            citySelect.value = savedCity;
                            if (option.dataset.code) {
                                await loadDistricts(option.dataset.code);

                                if (savedDistrict) {
                                    const districtSelect = document.getElementById('shipping_district');
                                    for (let dOption of districtSelect.options) {
                                        if (dOption.value === savedDistrict) {
                                            districtSelect.value = savedDistrict;
                                            if (dOption.dataset.code) {
                                                await loadWards(dOption.dataset.code);

                                                if (savedWard) {
                                                    const wardSelect = document.getElementById('shipping_ward');
                                                    wardSelect.value = savedWard;
                                                }
                                            }
                                            break;
                                        }
                                    }
                                }
                            }
                            break;
                        }
                    }
                }

                // Xử lý khi chọn tỉnh/thành phố
                document.getElementById('shipping_city').addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption.dataset.code) {
                        loadDistricts(selectedOption.dataset.code);
                    }
                });

                // Xử lý khi chọn quận/huyện
                document.getElementById('shipping_district').addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption.dataset.code) {
                        loadWards(selectedOption.dataset.code);
                    }
                });

                const shippingInputs = document.querySelectorAll('input[name="shipping_method"]');
                const subtotal = {{ $subtotal }};

                let currentDiscount = 0;
                let appliedCode = '';
                const shippingFees = {
                    'standard': 30000,
                    'express': 50000,
                    'fast': 70000
                };

                // Hàm cập nhật phí vận chuyển
                function updateShippingFee() {
                    const selected = document.querySelector('input[name="shipping_method"]:checked');
                    if (!selected) return;

                    let fee = shippingFees[selected.value] || 0;

                    // Miễn phí ship cho đơn trên 500k
                    if (subtotal >= 500000) {
                        fee = 0;
                    }

                    const total = Math.max(0, subtotal - currentDiscount + fee);
                    const shippingFeeEl = document.getElementById('shipping-fee');
                    const totalAmountEl = document.getElementById('total-amount');

                    if (shippingFeeEl) {
                        shippingFeeEl.textContent = fee === 0
                            ? 'Miễn phí'
                            : fee.toLocaleString('vi-VN') + ' đ';
                    }
                    if (totalAmountEl) {
                        totalAmountEl.textContent = total.toLocaleString('vi-VN') + ' đ';
                    }
                }

                // Khi thay đổi phương thức vận chuyển
                shippingInputs.forEach(input => {
                    input.addEventListener('change', function() {
                        updateShippingFee();
                    });
                });

                // Khởi tạo lần đầu
                updateShippingFee();

                // Áp dụng mã khuyến mãi
                const applyBtn = document.getElementById('apply-promotion-btn');
                const codeInput = document.getElementById('promotion-code');
                const discountRow = document.getElementById('discount-row');
                const discountAmountEl = document.getElementById('discount-amount');
                const appliedCodeEl = document.getElementById('applied-code');
                const messageEl = document.getElementById('promotion-message');

                function setMessage(text, type = 'info') {
                    if (!messageEl) return;
                    messageEl.className = 'small mt-2 text-' + (type === 'error' ? 'danger' : type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'muted');
                    messageEl.textContent = text;
                }

                function clearPromotion() {
                    currentDiscount = 0;
                    appliedCode = '';
                    if (discountRow) discountRow.style.display = 'none';
                    if (discountAmountEl) discountAmountEl.textContent = '- 0 đ';
                    if (appliedCodeEl) appliedCodeEl.textContent = '';
                    updateShippingFee();
                }

                if (applyBtn) {
                    applyBtn.addEventListener('click', async function() {
                        const code = codeInput ? codeInput.value.trim() : '';
                        if (!code) {
                            setMessage('Vui lòng nhập mã khuyến mãi', 'error');
                            return;
                        }
                        setMessage('Đang kiểm tra mã...', 'info');
                        applyBtn.disabled = true;
                        try {
                            const res = await fetch('{{ route('client.checkout.applyPromotion') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ code })
                            });
                            const data = await res.json();
                            if (!res.ok || !data.ok) {
                                const err = data.error || 'Mã không hợp lệ';
                                setMessage(err, 'error');
                                clearPromotion();
                            } else {
                                currentDiscount = parseFloat(data.promotion.discount_amount) || 0;
                                appliedCode = data.promotion.code || code;
                                if (discountRow) discountRow.style.display = 'flex';
                                if (discountAmountEl) discountAmountEl.textContent = '- ' + currentDiscount.toLocaleString('vi-VN') + ' đ';
                                if (appliedCodeEl) appliedCodeEl.textContent = appliedCode;
                                updateShippingFee();
                                setMessage('Áp dụng mã thành công', 'success');
                            }
                        } catch (e) {
                            setMessage('Lỗi kết nối. Vui lòng thử lại.', 'error');
                            clearPromotion();
                        } finally {
                            applyBtn.disabled = false;
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
