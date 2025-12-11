@extends('client.layouts.app')

@section('title', 'Thanh toán')

@section('content')
    <div class="container py-5">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb" style="background:transparent; padding:0;">
                <li class="breadcrumb-item"><a href="{{ route('client.home') }}">Trang chủ</a></li>
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
                            {{-- Hidden input để lưu số lượng --}}
                            <input type="hidden" name="quantity" id="quantity-hidden" value="{{ $qty }}">

                            {{-- Họ tên --}}
                            <div class="mb-3">
                                <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" class="form-control"
                                    value="{{ old('customer_name', $user->name ?? '') }}" required>
                                @error('customer_name')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                {{-- Số điện thoại --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="text" name="customer_phone" class="form-control"
                                        value="{{ old('customer_phone', $user->phone ?? '') }}" required>
                                    @error('customer_phone')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="customer_email" class="form-control"
                                        value="{{ old('customer_email', $user->email ?? '') }}" required>
                                    @error('customer_email')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Địa chỉ --}}
                            <div class="mb-3">
                                <label class="form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                <select name="shipping_city" id="shipping_city" class="form-select" required>
                                    <option value="">-- Chọn Tỉnh/Thành phố --</option>
                                </select>
                                @error('shipping_city')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Quận/Huyện <span class="text-danger">*</span></label>
                                    <select name="shipping_district" id="shipping_district" class="form-select" required disabled>
                                        <option value="">-- Chọn Quận/Huyện --</option>
                                    </select>
                                    @error('shipping_district')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phường/Xã <span class="text-danger">*</span></label>
                                    <select name="shipping_ward" id="shipping_ward" class="form-select" required disabled>
                                        <option value="">-- Chọn Phường/Xã --</option>
                                    </select>
                                    @error('shipping_ward')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Số nhà, tên đường <span class="text-danger">*</span></label>
                                <input type="text" name="shipping_address" class="form-control"
                                    value="{{ old('shipping_address') }}" required>
                                @error('shipping_address')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- Phí vận chuyển (tự động tính) --}}
                            <div class="mb-3">
                                <label class="form-label">Phí vận chuyển</label>
                                <div id="shipping-fee-display" class="alert alert-info mb-0">
                                    <i class="bi bi-truck me-2"></i>
                                    <span id="shipping-fee-text">Vui lòng chọn địa chỉ để tính phí vận chuyển</span>
                                </div>
                                <input type="hidden" name="shipping_fee" id="shipping_fee_input" value="0">
                            </div>

                            {{-- Phương thức vận chuyển --}}
                            <div class="mb-3">
                                <label class="form-label">Phương thức vận chuyển <span class="text-danger">*</span></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="shipping_method" id="shipping_standard" value="standard" checked>
                                    <label class="form-check-label" for="shipping_standard">
                                        Chuẩn (3-5 ngày)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="shipping_method" id="shipping_express" value="express">
                                    <label class="form-check-label" for="shipping_express">
                                        Nhanh (2-3 ngày)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="shipping_method" id="shipping_fast" value="fast">
                                    <label class="form-check-label" for="shipping_fast">
                                        Hỏa tốc (Trong ngày tại nội thành)
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
                        {{-- Sản phẩm --}}
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex mb-2">
                                <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/100' }}"
                                    alt="{{ $product->name }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                <div class="ms-3 flex-grow-1">
                                    <h6 class="mb-1">{{ $product->name }}</h6>
                                    @if ($variant)
                                        <small class="text-muted d-block">
                                            {{ $variant->color_name ?? '' }}
                                            @if ($variant->length && $variant->width && $variant->height)
                                                - {{ $variant->length }}x{{ $variant->width }}x{{ $variant->height }} cm
                                            @endif
                                        </small>
                                    @endif
                                    <div class="mt-2">
                                        <label class="form-label small mb-1">Số lượng:</label>
                                        <div class="input-group input-group-sm" style="max-width: 120px;">
                                            <button type="button" class="btn btn-outline-secondary" id="qty-minus">−</button>
                                            <input type="number" 
                                                id="quantity-input" 
                                                name="quantity" 
                                                class="form-control text-center" 
                                                value="{{ $qty }}" 
                                                min="1" 
                                                max="{{ $stock }}"
                                                data-price="{{ $price }}"
                                                data-stock="{{ $stock }}">
                                            <button type="button" class="btn btn-outline-secondary" id="qty-plus">+</button>
                                        </div>
                                        <small class="text-muted d-block mt-1">Tồn kho: <span id="stock-display">{{ $stock }}</span></small>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <strong class="text-danger" id="product-subtotal">
                                    {{ number_format($checkoutData['subtotal'], 0, ',', '.') }} đ
                                </strong>
                            </div>
                        </div>

                        {{-- Tổng tiền --}}
                        <div class="mb-2 d-flex justify-content-between">
                            <span>Tạm tính:</span>
                            <strong id="subtotal-display">{{ number_format($checkoutData['subtotal'], 0, ',', '.') }} đ</strong>
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
                                {{ number_format($checkoutData['subtotal'], 0, ',', '.') }} đ
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
                            <div class="form-text" id="promotion-hint">Áp dụng mã sau khi chọn số lượng.</div>
                            <div class="small mt-2" id="promotion-message"></div>
                        </div>

                        <div class="alert alert-info small mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Miễn phí vận chuyển cho đơn hàng từ 10.000.000đ
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let currentDiscount = 0;
            let appliedCode = '';

            // Load dữ liệu địa chỉ từ API
            let provinces = [];
            let districts = [];
            let wards = [];

            // Load tỉnh/thành phố
            async function loadProvinces() {
                try {
                    const response = await fetch('https://esgoo.net/api-tinhthanh/1/0.htm');
                    if (!response.ok) {
                        throw new Error('Không thể tải dữ liệu từ API');
                    }
                    const data = await response.json();
                    if (data.error === 0) {
                        provinces = data.data;
                        const citySelect = document.getElementById('shipping_city');
                        if (!citySelect) return;
                        
                        citySelect.innerHTML = '<option value="">-- Chọn Tỉnh/Thành phố --</option>';
                        provinces.forEach(province => {
                            const option = document.createElement('option');
                            option.value = province.full_name;
                            option.textContent = province.full_name;
                            option.dataset.code = province.id;
                            citySelect.appendChild(option);
                        });
                    }
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
                    const response = await fetch(`https://esgoo.net/api-tinhthanh/2/${provinceCode}.htm`);
                    if (!response.ok) {
                        throw new Error('Không thể tải dữ liệu quận/huyện');
                    }
                    const data = await response.json();
                    if (data.error === 0) {
                        districts = data.data || [];
                        const districtSelect = document.getElementById('shipping_district');
                        if (!districtSelect) return;
                        
                        districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                        districts.forEach(district => {
                            const option = document.createElement('option');
                            option.value = district.full_name;
                            option.textContent = district.full_name;
                            option.dataset.code = district.id;
                            districtSelect.appendChild(option);
                        });
                        districtSelect.disabled = false;
                        // Reset phường/xã
                        const wardSelect = document.getElementById('shipping_ward');
                        if (wardSelect) {
                            wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                            wardSelect.disabled = true;
                        }
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
                    const response = await fetch(`https://esgoo.net/api-tinhthanh/3/${districtCode}.htm`);
                    if (!response.ok) {
                        throw new Error('Không thể tải dữ liệu phường/xã');
                    }
                    const data = await response.json();
                    if (data.error === 0) {
                        wards = data.data || [];
                        const wardSelect = document.getElementById('shipping_ward');
                        if (!wardSelect) return;
                        
                        wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                        wards.forEach(ward => {
                            const option = document.createElement('option');
                            option.value = ward.full_name;
                            option.textContent = ward.full_name;
                            wardSelect.appendChild(option);
                        });
                        wardSelect.disabled = false;
                    }
                } catch (error) {
                    console.error('Lỗi khi tải danh sách phường/xã:', error);
                    const wardSelect = document.getElementById('shipping_ward');
                    if (wardSelect) {
                        wardSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
                    }
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                // Load tỉnh/thành phố khi trang load
                loadProvinces();

                // Xử lý khi chọn tỉnh/thành phố
                document.getElementById('shipping_city').addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption.dataset.code) {
                        loadDistricts(selectedOption.dataset.code);
                    }
                    setTimeout(calculateShippingFee, 500);
                });

                // Xử lý khi chọn quận/huyện
                document.getElementById('shipping_district').addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption.dataset.code) {
                        loadWards(selectedOption.dataset.code);
                    }
                    setTimeout(calculateShippingFee, 300);
                });

                const quantityInput = document.getElementById('quantity-input');
                const quantityHidden = document.getElementById('quantity-hidden');
                const qtyMinus = document.getElementById('qty-minus');
                const qtyPlus = document.getElementById('qty-plus');
                
                // Kiểm tra các element có tồn tại không
                if (!quantityInput || !quantityHidden || !qtyMinus || !qtyPlus) {
                    console.error('Không tìm thấy các element cần thiết');
                    return;
                }
                
                const shippingInputs = document.querySelectorAll('input[name="shipping_method"]');

                const price = parseFloat(quantityInput.getAttribute('data-price')) || 0;
                const maxStock = parseInt(quantityInput.getAttribute('data-stock')) || 1;

                let currentShippingFee = 0;
                let currentSubtotal = price;

                function setMessage(message, type = 'info') {
                    const el = document.getElementById('promotion-message');
                    if (!el) return;
                    el.textContent = message;
                    el.className = type === 'success' ? 'text-success' : (type === 'warning' ? 'text-warning' : 'text-danger');
                }

                function clearPromotion() {
                    const discountRow = document.getElementById('discount-row');
                    const appliedCodeEl = document.getElementById('applied-code');
                    const discountAmountEl = document.getElementById('discount-amount');
                    if (discountRow) discountRow.style.display = 'none';
                    if (appliedCodeEl) appliedCodeEl.textContent = '';
                    if (discountAmountEl) discountAmountEl.textContent = '- 0 đ';
                    currentDiscount = 0;
                    appliedCode = '';
                }

                // Hàm cập nhật số lượng và tính toán
                function updateQuantity(newQty) {
                    // Đảm bảo số lượng hợp lệ
                    newQty = Math.max(1, Math.min(newQty, maxStock));
                    quantityInput.value = newQty;
                    if (quantityHidden) {
                        quantityHidden.value = newQty;
                    }

                    // Tính lại subtotal
                    currentSubtotal = price * newQty;

                    // Cập nhật hiển thị
                    const productSubtotalEl = document.getElementById('product-subtotal');
                    const subtotalDisplayEl = document.getElementById('subtotal-display');

                    if (productSubtotalEl) {
                        productSubtotalEl.textContent = currentSubtotal.toLocaleString('vi-VN') + ' đ';
                    }
                    if (subtotalDisplayEl) {
                        subtotalDisplayEl.textContent = currentSubtotal.toLocaleString('vi-VN') + ' đ';
                    }

                    updateTotalDisplay();
                    calculateShippingFee();
                }

                // Hàm tính phí vận chuyển qua API
                function calculateShippingFee() {
                    const citySelect = document.getElementById('shipping_city');
                    const districtSelect = document.getElementById('shipping_district');

                    if (!citySelect || !districtSelect) return;

                    const cityOption = citySelect.options[citySelect.selectedIndex];
                    const districtOption = districtSelect.options[districtSelect.selectedIndex];

                    const cityName = cityOption ? cityOption.text : '';
                    const districtName = districtOption ? districtOption.text : '';

                    if (!cityName || cityName === '-- Chọn Tỉnh/Thành phố --' ||
                        !districtName || districtName === '-- Chọn Quận/Huyện --') {
                        document.getElementById('shipping-fee-text').textContent = 'Vui lòng chọn địa chỉ để tính phí vận chuyển';
                        document.getElementById('shipping-fee-display').className = 'alert alert-info mb-0';
                        updateTotalDisplay();
                        return;
                    }

                    // Gọi API tính phí vận chuyển
                    fetch('{{ route("client.checkout.calculateShipping") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            city: cityName,
                            district: districtName,
                            subtotal: currentSubtotal
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            currentShippingFee = data.fee;
                            document.getElementById('shipping_fee_input').value = data.fee;

                            if (data.is_free_shipping) {
                                document.getElementById('shipping-fee-text').innerHTML =
                                    '<strong class="text-success">🎉 Đơn hàng được MIỄN PHÍ vận chuyển!</strong>';
                                document.getElementById('shipping-fee-display').className = 'alert alert-success mb-0';
                            } else {
                                document.getElementById('shipping-fee-text').innerHTML =
                                    'Phí vận chuyển của quý khách: <strong>' + data.fee_formatted + '</strong>';
                                document.getElementById('shipping-fee-display').className = 'alert alert-warning mb-0';
                            }

                            // Cập nhật tổng tiền
                            updateTotalDisplay();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                }

                // Hàm cập nhật hiển thị tổng tiền
                function updateTotalDisplay() {
                    const total = Math.max(0, (currentSubtotal - currentDiscount) + currentShippingFee);
                    const shippingFeeEl = document.getElementById('shipping-fee');
                    const totalAmountEl = document.getElementById('total-amount');

                    if (shippingFeeEl) {
                        shippingFeeEl.textContent = currentShippingFee === 0
                            ? 'Miễn phí'
                            : currentShippingFee.toLocaleString('vi-VN') + ' đ';
                    }
                    if (totalAmountEl) {
                        totalAmountEl.textContent = total.toLocaleString('vi-VN') + ' đ';
                    }
                }

                // Lắng nghe sự kiện thay đổi địa chỉ - Đã gộp vào sự kiện change ở trên

                // Nút giảm số lượng
                qtyMinus.addEventListener('click', function(e) {
                    e.preventDefault();
                    const currentQty = parseInt(quantityInput.value) || 1;
                    if (currentQty > 1) {
                        updateQuantity(currentQty - 1);
                    }
                });

                // Nút tăng số lượng
                qtyPlus.addEventListener('click', function(e) {
                    e.preventDefault();
                    const currentQty = parseInt(quantityInput.value) || 1;
                    if (currentQty < maxStock) {
                        updateQuantity(currentQty + 1);
                    } else {
                        alert('Số lượng không được vượt quá tồn kho: ' + maxStock);
                    }
                });

                // Khi người dùng nhập trực tiếp
                quantityInput.addEventListener('change', function() {
                    let newQty = parseInt(this.value) || 1;
                    if (newQty < 1) {
                        newQty = 1;
                    } else if (newQty > maxStock) {
                        alert('Số lượng không được vượt quá tồn kho: ' + maxStock);
                        newQty = maxStock;
                    }
                    updateQuantity(newQty);
                    // Xóa mã khuyến mãi nếu đang áp dụng
                    if (currentDiscount > 0) {
                        clearPromotion();
                        setMessage('Số lượng đã thay đổi. Vui lòng áp dụng lại mã.', 'warning');
                    }

                });

                // Khi người dùng nhập từ bàn phím (real-time)
                quantityInput.addEventListener('input', function() {
                    let newQty = parseInt(this.value) || 1;
                    if (newQty < 1) {
                        newQty = 1;
                    } else if (newQty > maxStock) {
                        newQty = maxStock;
                    }
                    if (newQty !== parseInt(this.value)) {
                        this.value = newQty;
                    }
                    updateQuantity(newQty);
                    // Xóa mã khuyến mãi nếu đang áp dụng
                    if (currentDiscount > 0) {
                        clearPromotion();
                        setMessage('Số lượng đã thay đổi. Vui lòng áp dụng lại mã.', 'warning');
                    }
                });

                // Khi thay đổi phương thức vận chuyển
                shippingInputs.forEach(input => {
                    input.addEventListener('change', function() {
                        const subtotal = price * parseInt(quantityInput.value) || price;
                        currentSubtotal = subtotal;
                        calculateShippingFee();
                    });

                });

                const applyBtn = document.getElementById('apply-promotion-btn');
                const codeInput = document.getElementById('promotion-code');
                const discountRow = document.getElementById('discount-row');
                const appliedCodeEl = document.getElementById('applied-code');
                const discountAmountEl = document.getElementById('discount-amount');
                const totalAmountEl = document.getElementById('total-amount');

                if (applyBtn) {
                    applyBtn.addEventListener('click', function() {
                        const code = (codeInput?.value || '').trim();
                        if (!code) {
                            setMessage('Vui lòng nhập mã khuyến mãi.', 'danger');
                            return;
                        }

                        applyBtn.disabled = true;
                        setMessage('Đang áp dụng mã...', 'warning');

                        fetch('{{ route('client.checkout.applyPromotion') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ code })
                        })
                        .then(async res => {
                            if (res.status === 401) {
                                window.location.href = '{{ route('client.login') }}';
                                return null;
                            }
                            const data = await res.json();
                            if (!data.ok) {
                                throw new Error(data.error || 'Mã khuyến mãi không hợp lệ.');
                            }
                            return data;
                        })
                        .then(data => {
                            if (!data) return;
                            currentDiscount = Number(data.promotion.discount_amount) || 0;
                            appliedCode = data.promotion.code || '';

                        if (discountRow) discountRow.style.display = 'flex';
                        if (appliedCodeEl) appliedCodeEl.textContent = appliedCode;
                        if (discountAmountEl) discountAmountEl.textContent = '- ' + currentDiscount.toLocaleString('vi-VN') + ' đ';
                        updateTotalDisplay();

                            setMessage('Áp dụng mã thành công.', 'success');
                        })
                        .catch(err => {
                            clearPromotion();
                            setMessage(err.message || 'Không thể áp dụng mã. Vui lòng thử lại.', 'danger');
                        })
                        .finally(() => {
                            applyBtn.disabled = false;
                        });
                    });
                }

                // Khởi tạo lần đầu
                const initialQty = parseInt(quantityInput.value) || 1;
                currentSubtotal = price * initialQty;

                // Tính phí vận chuyển sau khi trang load xong
                setTimeout(calculateShippingFee, 1000);
            });
        </script>
    @endpush
@endsection
