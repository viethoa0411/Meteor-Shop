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

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
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
                                    value="{{ old('customer_name', $checkoutData['customer_name'] ?? ($user->name ?? '')) }}"
                                    required>
                                @error('customer_name')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                {{-- Số điện thoại --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="text" name="customer_phone" class="form-control"
                                        value="{{ old('customer_phone', $checkoutData['customer_phone'] ?? ($user->phone ?? '')) }}"
                                        required>
                                    @error('customer_phone')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="customer_email" class="form-control"
                                        value="{{ old('customer_email', $checkoutData['customer_email'] ?? ($user->email ?? '')) }}"
                                        required>
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
                                    <select name="shipping_district" id="shipping_district" class="form-select" required
                                        disabled>
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
                                    value="{{ old('shipping_address', $checkoutData['shipping_address'] ?? '') }}"
                                    required>
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
                                <input type="hidden" name="installation_fee" id="installation_fee_input" value="0">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phương thức vận chuyển <span
                                        class="text-danger">*</span></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="shipping_method"
                                        id="shipping_standard" value="standard" checked>
                                    <label class="form-check-label" for="shipping_standard">
                                        Chuẩn (3-5 ngày)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="shipping_method"
                                        id="shipping_express" value="express">
                                    <label class="form-check-label" for="shipping_express">
                                        {{ $shippingSettings->express_label }}
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="shipping_method"
                                        id="shipping_fast" value="fast">
                                    <label class="form-check-label" for="shipping_fast">
                                        {{ $shippingSettings->fast_label }}
                                    </label>
                                </div>
                                @error('shipping_method')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Phương thức thanh toán --}}
                            <div class="mb-3">
                                <label class="form-label">Phương thức thanh toán <span
                                        class="text-danger">*</span></label>
                                <div class="form-check mb-2" id="cod-payment-option">
                                    <input class="form-check-input" type="radio" name="payment_method" id="cash"
                                        value="cash" {{ old('payment_method', 'cash') == 'cash' ? 'checked' : '' }}
                                        required>
                                    <label class="form-check-label" for="cash">
                                        <strong>Thanh toán khi nhận hàng</strong>

                                    </label>
                                </div>

                                @auth
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="radio" name="payment_method" id="momo"
                                            value="momo" {{ old('payment_method') == 'momo' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="momo">
                                            <strong>Thanh toán bằng Momo</strong>
                                        </label>
                                    </div>

                                @endauth

                                @error('payment_method')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                                <div id="cod-restriction-message" class="alert alert-warning mt-2" style="display:none;">
                                    <small><i class="bi bi-info-circle me-1"></i>Đơn hàng trên 10 triệu chỉ được thanh toán
                                        online.</small>
                                </div>
                            </div>

                            {{-- Ghi chú --}}
                            <div class="mb-3">
                                <label class="form-label">Ghi chú đơn hàng</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Ghi chú thêm cho đơn hàng...">{{ old('notes') }}</textarea>
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
                <div class="card shadow-sm sticky-top checkout-summary-card" style="top: 20px;">
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
                                                    @if ($item['color'])
                                                        |
                                                    @endif
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
                        <div class="mb-2 d-flex justify-content-between align-items-center" id="discount-row">
                            <div class="d-flex align-items-center">
                                <span class="me-2">Giảm giá:</span>
                                <span id="voucher-badge"
                                    class="badge bg-light text-primary border {{ isset($checkoutData['promotion']) && !empty($checkoutData['promotion']['code']) ? 'd-flex' : 'd-none' }} align-items-center py-2 px-2">
                                    <i class="bi bi-ticket-perforated me-1"></i>
                                    <span id="applied-code"
                                        class="me-1">{{ $checkoutData['promotion']['code'] ?? '' }}</span>
                                    <span id="remove-promotion-btn" class="ms-2 text-danger hover-opacity-75"
                                        style="cursor: pointer;" title="Hủy mã">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </span>
                                </span>
                            </div>
                            <strong class="text-success" id="discount-amount">-
                                {{ number_format($checkoutData['discount_amount'] ?? 0, 0, ',', '.') }} đ</strong>
                        </div>
                        @php
                            $defaultInstallationFee = $shippingSettings->installation_fee ?? 0;
                        @endphp
                        @if ($defaultInstallationFee > 0)
                            <div class="mb-2 d-flex justify-content-between" id="installation-row">
                                <span>Phí lắp đặt:</span>
                                <strong id="installation-fee">{{ number_format($defaultInstallationFee, 0, ',', '.') }}
                                    đ</strong>
                            </div>
                        @else
                            <div class="mb-2 d-flex justify-content-between" id="installation-row" style="display:none;">
                                <span>Phí lắp đặt:</span>
                                <strong id="installation-fee">0 đ</strong>
                            </div>
                        @endif
                        <div class="mb-3 pt-2 border-top d-flex justify-content-between">
                            <span class="fs-5 fw-bold">Tổng cộng:</span>
                            <span class="fs-5 fw-bold text-danger" id="total-amount">
                                {{ number_format($subtotal, 0, ',', '.') }} đ
                            </span>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="installation-checkbox"
                                    name="installation">
                                <label class="form-check-label" for="installation-checkbox">
                                    <strong>Dịch vụ lắp đặt</strong>
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1">Phí lắp đặt sẽ được cộng thêm vào tổng tiền</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mã khuyến mãi</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="promotion-code"
                                    placeholder="Nhập mã khuyến mãi" aria-label="Mã khuyến mãi">
                                <button class="btn btn-outline-primary" type="button" id="apply-promotion-btn">
                                    <span id="promotion-btn-text">Áp dụng</span>
                                    <span id="promotion-btn-spinner" class="spinner-border spinner-border-sm d-none ms-1"
                                        role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                            <div class="form-text" id="promotion-hint">Áp dụng mã sau khi chọn số lượng.</div>
                            <div class="small mt-2" id="promotion-message"></div>

                            {{-- Danh sách voucher --}}
                            @if (isset($promotions) && $promotions->count() > 0)
                                <div class="mt-3">
                                    <label class="form-label fw-bold small">Mã giảm giá khả dụng:</label>
                                    <div class="list-group" id="voucher-list"
                                        style="max-height: 200px; overflow-y: auto;">
                                        @foreach ($promotions as $promo)
                                            <button type="button"
                                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center voucher-item p-2"
                                                data-code="{{ $promo->code }}">
                                                <div class="me-2">
                                                    <div class="fw-bold text-primary small">{{ $promo->code }}</div>
                                                    <small class="text-muted"
                                                        style="font-size: 0.75rem;">{{ $promo->description ?? $promo->name }}</small>
                                                </div>
                                                <span class="badge bg-light text-dark border small">Áp dụng</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="alert alert-info small mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Miễn phí vận chuyển cho đơn hàng từ
                            {{ number_format($shippingSettings->free_shipping_threshold, 0, ',', '.') }}đ
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('head')
        <style>
            /* Ẩn mũi tên tăng giảm của input number */
            input[type=number]::-webkit-inner-spin-button,
            input[type=number]::-webkit-outer-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
            input[type=number] {
                -moz-appearance: textfield;
            }

            /* Style cho quantity control nhỏ gọn */
            .quantity-control .btn {
                padding: 0.15rem 0.4rem;
                font-size: 0.8rem;
                line-height: 1.2;
            }
            .quantity-control input {
                font-size: 0.9rem;
                height: auto;
                padding: 0.15rem 0;
            }
        </style>
        <script>
            // Load dữ liệu địa chỉ từ API (Esgoo)
            let provinces = [];
            let districts = [];
            let wards = [];

            // Danh sách tỉnh/thành phố miền Bắc (Set lower-case để so khớp chắc chắn)
            // Bao gồm cả các biến thể tên (có dấu, không dấu, viết hoa/thường)
            const northernProvincesSet = new Set([
                'hà nội', 'ha noi', 'hanoi',
                'hải phòng', 'hai phong', 'haiphong',
                'hải dương', 'hai duong', 'haiduong',
                'hưng yên', 'hung yen', 'hungyen',
                'hà nam', 'ha nam', 'hanam',
                'nam định', 'nam dinh', 'namdinh',
                'thái bình', 'thai binh', 'thaibinh',
                'ninh bình', 'ninh binh', 'ninhbinh',
                'bắc ninh', 'bac ninh', 'bacninh',
                'bắc giang', 'bac giang', 'bacgiang',
                'quảng ninh', 'quang ninh', 'quangninh',
                'lào cai', 'lao cai', 'laocai',
                'yên bái', 'yen bai', 'yenbai',
                'tuyên quang', 'tuyen quang', 'tuyenquang',
                'lạng sơn', 'lang son', 'langson',
                'cao bằng', 'cao bang', 'caobang',
                'bắc kạn', 'bac kan', 'backan',
                'thái nguyên', 'thai nguyen', 'thainguyen',
                'phú thọ', 'phu tho', 'phutho',
                'vĩnh phúc', 'vinh phuc', 'vinhphuc',
                'điện biên', 'dien bien', 'dienbien',
                'lai châu', 'lai chau', 'laichau',
                'sơn la', 'son la', 'sonla',
                'hòa bình', 'hoa binh', 'hoabinh'
            ]);

            // Hàm normalize tên tỉnh để so sánh (loại bỏ dấu, khoảng trắng, chuyển lowercase)
            function normalizeProvinceName(name) {
                if (!name) return '';
                return name.toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '') // Loại bỏ dấu
                    .replace(/^(tinh|thanh pho|tp\.?)\s+/i, '') // Loại bỏ prefix "Tỉnh", "Thành phố", "TP."
                    .replace(/\s+/g, ' ') // Chuẩn hóa khoảng trắng
                    .trim();
            }

            // Hàm kiểm tra xem tên tỉnh có chứa tên tỉnh miền Bắc không
            function isNorthernProvince(name) {
                if (!name) return false;

                const normalized = normalizeProvinceName(name);
                const normalizedNoSpace = normalized.replace(/\s+/g, '');

                // Check trực tiếp
                if (northernProvincesSet.has(normalized) || northernProvincesSet.has(normalizedNoSpace)) {
                    return true;
                }

                // Check nếu tên tỉnh chứa tên tỉnh miền Bắc (cho trường hợp "Tỉnh Hà Nội")
                for (const provinceName of northernProvincesSet) {
                    if (normalized.includes(provinceName) || normalizedNoSpace.includes(provinceName.replace(/\s+/g, ''))) {
                        return true;
                    }
                }

                return false;
            }

            // Load tỉnh/thành phố - Chỉ hiển thị miền Bắc
            async function loadProvinces() {
                const citySelect = document.getElementById('shipping_city');
                if (!citySelect) {
                    console.error('Không tìm thấy element shipping_city');
                    return;
                }

                // Đảm bảo select không bị disabled
                citySelect.disabled = false;

                // Hiển thị loading state
                citySelect.innerHTML = '<option value="">Đang tải dữ liệu...</option>';
                citySelect.disabled = true;

                try {
                    console.log('Bắt đầu tải danh sách tỉnh/thành phố...');
                    const response = await fetch('https://esgoo.net/api-tinhthanh/1/0.htm');
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();

                    console.log('API Response:', data);
                    debugApiResponse('https://esgoo.net/api-tinhthanh/1/0.htm', data);

                    if (data.error === 0 && data.data && Array.isArray(data.data)) {
                        provinces = data.data;
                        citySelect.innerHTML = '<option value="">-- Chọn Tỉnh/Thành phố --</option>';

                        let addedCount = 0;
                        provinces.forEach(province => {
                            // Lấy tên tỉnh từ nhiều nguồn có thể
                            const provinceName = province.full_name || province.name || province.title || '';

                            if (!provinceName) return; // Bỏ qua nếu không có tên

                            // Chỉ thêm các tỉnh miền Bắc
                            if (isNorthernProvince(provinceName)) {
                                const option = document.createElement('option');
                                option.value = provinceName; // Giữ nguyên tên gốc hiển thị
                                option.textContent = provinceName;

                                // Cải thiện việc lấy provinceCode với nhiều fallback
                                const provinceCode = province.id || province.code || province.province_id || province.matp || '';

                                if (provinceCode) {
                                    option.dataset.code = provinceCode;
                                    option.setAttribute('data-code', provinceCode);
                                    option.setAttribute('data-province-code', provinceCode);
                                    console.log(`Province "${provinceName}" assigned code: ${provinceCode}`);
                                } else {
                                    // Nếu không có code từ API, tạo một fallback code dựa trên index
                                    const fallbackCode = `province_${addedCount + 1}`;
                                    option.dataset.code = fallbackCode;
                                    option.setAttribute('data-code', fallbackCode);
                                    option.setAttribute('data-province-code', fallbackCode);
                                    console.warn(`Province "${provinceName}" has no code, using fallback: ${fallbackCode}`, province);
                                }

                                citySelect.appendChild(option);
                                addedCount++;
                            }
                        });

                        // Kích hoạt select sau khi load xong
                        citySelect.disabled = false;

                        console.log(`Đã tải ${addedCount} tỉnh/thành phố miền Bắc (tổng ${provinces.length} từ API)`);

                        if (addedCount === 0) {
                            const errorOption = document.createElement('option');
                            errorOption.value = '';
                            errorOption.textContent = '⚠ Không tìm thấy tỉnh miền Bắc (API changed?)';
                            citySelect.appendChild(errorOption);
                        }
                    } else {
                        console.error('Dữ liệu API không hợp lệ:', data);
                        throw new Error('Dữ liệu API không hợp lệ');
                    }
                } catch (error) {
                    console.error('Lỗi khi tải danh sách tỉnh/thành phố:', error);
                    citySelect.innerHTML = '<option value="">-- Chọn Tỉnh/Thành phố --</option>';
                    const errorOption = document.createElement('option');
                    errorOption.value = '';
                    errorOption.textContent = 'Không thể tải dữ liệu. Vui lòng tải lại trang.';
                    errorOption.disabled = true;
                    citySelect.appendChild(errorOption);
                    citySelect.disabled = false;
                }
            }

            // Load quận/huyện
            async function loadDistricts(provinceCode) {
                console.log('loadDistricts called with provinceCode:', provinceCode);
                const districtSelect = document.getElementById('shipping_district');
                if (!districtSelect) {
                    console.error('Không tìm thấy element shipping_district');
                    return;
                }

                // Cải thiện điều kiện kiểm tra - chỉ reset nếu thực sự không có code hợp lệ
                if (!provinceCode || provinceCode === '' || provinceCode === '0' || provinceCode === 'undefined' || provinceCode === 'null') {
                    console.log('Invalid provinceCode, resetting districts');
                    districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                    districtSelect.disabled = true;
                    const wardSelect = document.getElementById('shipping_ward');
                    if (wardSelect) {
                        wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                        wardSelect.disabled = true;
                    }
                    return;
                }

                try {
                    console.log('Fetching districts from API...');
                    districtSelect.innerHTML = '<option value="">Đang tải...</option>';
                    districtSelect.disabled = true;

                    const response = await fetch(`https://esgoo.net/api-tinhthanh/2/${provinceCode}.htm`);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();
                    console.log('Districts API Response:', data);
                    debugApiResponse(`https://esgoo.net/api-tinhthanh/2/${provinceCode}.htm`, data);

                    if (data.error === 0 && data.data && Array.isArray(data.data)) {
                        districts = data.data || [];
                        console.log(`Loaded ${districts.length} districts`);
                        districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                        districts.forEach(district => {
                            const option = document.createElement('option');
                            option.value = district.full_name || district.name || '';
                            option.textContent = district.full_name || district.name || '';

                            // Cải thiện việc gán districtCode
                            const districtCode = district.id || district.code || district.district_id || district.maqh || '';
                            if (districtCode) {
                                option.dataset.code = districtCode;
                                option.setAttribute('data-code', districtCode);
                                option.setAttribute('data-district-code', districtCode);
                            } else {
                                console.warn('District has no code:', district);
                            }

                            districtSelect.appendChild(option);
                        });
                        districtSelect.disabled = false;
                        console.log('Districts loaded and select enabled');
                        // Reset phường/xã
                        const wardSelect = document.getElementById('shipping_ward');
                        if (wardSelect) {
                            wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                            wardSelect.disabled = true;
                        }
                    } else {
                        console.error('API returned invalid data:', data);
                        throw new Error('API trả về lỗi hoặc dữ liệu không hợp lệ');
                    }
                } catch (error) {
                    console.error('Lỗi khi tải danh sách quận/huyện:', error);
                    districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                    const errorOption = document.createElement('option');
                    errorOption.value = '';
                    errorOption.textContent = 'Lỗi tải dữ liệu. Vui lòng thử lại.';
                    errorOption.disabled = true;
                    districtSelect.appendChild(errorOption);
                    districtSelect.disabled = false;
                }
            }

            // Load phường/xã
            async function loadWards(districtCode) {
                console.log('loadWards called with districtCode:', districtCode);
                const wardSelect = document.getElementById('shipping_ward');
                if (!wardSelect) {
                    console.error('Không tìm thấy element shipping_ward');
                    return;
                }

                // Cải thiện điều kiện kiểm tra
                if (!districtCode || districtCode === '' || districtCode === '0' || districtCode === 'undefined' || districtCode === 'null') {
                    console.log('Invalid districtCode, resetting wards');
                    wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                    wardSelect.disabled = true;
                    return;
                }

                try {
                    wardSelect.innerHTML = '<option value="">Đang tải...</option>';
                    wardSelect.disabled = true;

                    const response = await fetch(`https://esgoo.net/api-tinhthanh/3/${districtCode}.htm`);
                    if (!response.ok) {
                        throw new Error('Không thể tải dữ liệu phường/xã');
                    }
                    const data = await response.json();
                    console.log('Wards API Response:', data);
                    debugApiResponse(`https://esgoo.net/api-tinhthanh/3/${districtCode}.htm`, data);

                    if (data.error === 0 && data.data && Array.isArray(data.data)) {
                        wards = data.data || [];
                        console.log(`Loaded ${wards.length} wards`);
                        wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                        wards.forEach(ward => {
                            const option = document.createElement('option');
                            option.value = ward.full_name || ward.name || '';
                            option.textContent = ward.full_name || ward.name || '';

                            // Thêm ward code nếu có (để tương lai sử dụng)
                            const wardCode = ward.id || ward.code || ward.ward_id || ward.xaid || '';
                            if (wardCode) {
                                option.dataset.code = wardCode;
                                option.setAttribute('data-code', wardCode);
                                option.setAttribute('data-ward-code', wardCode);
                            }

                            wardSelect.appendChild(option);
                        });
                        wardSelect.disabled = false;
                        console.log('Wards loaded and select enabled');
                    } else {
                        console.error('API returned invalid ward data:', data);
                        throw new Error('API trả về lỗi hoặc dữ liệu không hợp lệ');
                    }
                } catch (error) {
                    console.error('Lỗi khi tải danh sách phường/xã:', error);
                    wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                    const errorOption = document.createElement('option');
                    errorOption.value = '';
                    errorOption.textContent = 'Lỗi tải dữ liệu. Vui lòng thử lại.';
                    errorOption.disabled = true;
                    wardSelect.appendChild(errorOption);
                    wardSelect.disabled = false;
                }
            }

            // Hàm debug để kiểm tra API response
            function debugApiResponse(apiUrl, data) {
                console.log(`=== DEBUG API: ${apiUrl} ===`);
                console.log('Full response:', data);
                if (data && data.data && Array.isArray(data.data)) {
                    console.log(`Found ${data.data.length} items`);
                    if (data.data.length > 0) {
                        console.log('First item structure:', data.data[0]);
                        console.log('Available keys:', Object.keys(data.data[0]));
                    }
                } else {
                    console.log('Invalid data structure');
                }
                console.log('=== END DEBUG ===');
            }

            document.addEventListener('DOMContentLoaded', function() {
                console.log('DOMContentLoaded triggered - Cart checkout');

                // Đảm bảo select không bị disabled
                const citySelect = document.getElementById('shipping_city');
                const districtSelect = document.getElementById('shipping_district');
                const wardSelect = document.getElementById('shipping_ward');

                console.log('Elements found:', {
                    citySelect: !!citySelect,
                    districtSelect: !!districtSelect,
                    wardSelect: !!wardSelect
                });

                if (!citySelect || !districtSelect || !wardSelect) {
                    console.error('Missing address dropdown elements - cannot initialize');
                    return;
                }

                if (citySelect) {
                    citySelect.disabled = false;
                }
                if (districtSelect) {
                    districtSelect.disabled = true; // Disabled cho đến khi chọn tỉnh
                }
                if (wardSelect) {
                    wardSelect.disabled = true; // Disabled cho đến khi chọn quận
                }

                // Gắn event listener NGAY từ đầu (trước khi load provinces)
                // Xử lý khi chọn tỉnh/thành phố
                if (citySelect) {
                    console.log('Attaching event listener to shipping_city (early)');
                    citySelect.addEventListener('change', function() {
                        console.log('City changed:', this.value);
                        const selectedOption = this.options[this.selectedIndex];

                        // Cải thiện việc lấy provinceCode với nhiều fallback
                        let provinceCode = selectedOption.dataset.code ||
                                         selectedOption.getAttribute('data-code') ||
                                         selectedOption.getAttribute('data-province-code') ||
                                         '';

                        console.log('Province code from dataset:', provinceCode);
                        console.log('Selected option attributes:', {
                            'dataset.code': selectedOption.dataset.code,
                            'data-code': selectedOption.getAttribute('data-code'),
                            'value': selectedOption.value,
                            'text': selectedOption.textContent
                        });

                        // Nếu không có provinceCode, thử tìm trong danh sách provinces đã load
                        if (!provinceCode && this.value && provinces && provinces.length > 0) {
                            const foundProvince = provinces.find(p => {
                                const provinceName = p.full_name || p.name || p.title || '';
                                return provinceName === this.value;
                            });
                            if (foundProvince) {
                                provinceCode = foundProvince.id || foundProvince.code || foundProvince.province_id || foundProvince.matp || '';
                                console.log('Found provinceCode from provinces array:', provinceCode);
                            }
                        }

                        if (provinceCode && provinceCode !== '' && provinceCode !== '0') {
                            console.log('Loading districts for province code:', provinceCode);
                            loadDistricts(provinceCode);
                        } else {
                            console.log('No valid province code found, resetting districts and wards');
                            // Reset quận/huyện và phường/xã nếu không chọn tỉnh
                            const districtSelectEl = document.getElementById('shipping_district');
                            const wardSelectEl = document.getElementById('shipping_ward');
                            if (districtSelectEl) {
                                districtSelectEl.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                                districtSelectEl.disabled = true;
                            }
                            if (wardSelectEl) {
                                wardSelectEl.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                                wardSelectEl.disabled = true;
                            }
                        }

                        setTimeout(calculateShippingFee, 500);
                    });
                }

                // Xử lý khi chọn quận/huyện
                if (districtSelect) {
                    console.log('Attaching event listener to shipping_district (early)');
                    districtSelect.addEventListener('change', function() {
                        console.log('District changed:', this.value);
                        const selectedOption = this.options[this.selectedIndex];
                        const districtCode = selectedOption.dataset.code ||
                                            selectedOption.getAttribute('data-code') ||
                                            '';
                        console.log('District code from dataset:', districtCode);

                        if (districtCode && districtCode !== '' && districtCode !== '0') {
                            console.log('Loading wards for district code:', districtCode);
                            loadWards(districtCode);
                        } else {
                            console.log('No district code found, resetting wards');
                            // Reset phường/xã nếu không chọn quận
                            const wardSelectEl = document.getElementById('shipping_ward');
                            if (wardSelectEl) {
                                wardSelectEl.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                                wardSelectEl.disabled = true;
                            }
                        }

                        setTimeout(calculateShippingFee, 300);
                    });
                }

                // Lấy thông tin địa chỉ cũ từ reorder (nếu có)
                const savedCity = @json($checkoutData['shipping_city'] ?? null);
                const savedDistrict = @json($checkoutData['shipping_district'] ?? null);
                const savedWard = @json($checkoutData['shipping_ward'] ?? null);

                // Load tỉnh/thành phố khi trang load (async)
                console.log('Calling loadProvinces()...');
                loadProvinces().then(() => {
                    console.log('loadProvinces() completed');

                    // Nếu có địa chỉ cũ, tự động chọn sau khi load xong
                    if (savedCity) {
                        const citySelectAfterLoad = document.getElementById('shipping_city');
                        if (citySelectAfterLoad) {
                            for (let option of citySelectAfterLoad.options) {
                                if (option.value === savedCity) {
                                    citySelectAfterLoad.value = savedCity;
                                    const provinceCode = option.dataset.code || option.getAttribute('data-code') || option.getAttribute('data-province-code');
                                    if (provinceCode) {
                                        loadDistricts(provinceCode).then(() => {
                                            if (savedDistrict) {
                                                const districtSelectAfterLoad = document.getElementById('shipping_district');
                                                if (districtSelectAfterLoad) {
                                                    for (let dOption of districtSelectAfterLoad.options) {
                                                        if (dOption.value === savedDistrict) {
                                                            districtSelectAfterLoad.value = savedDistrict;
                                                            const districtCode = dOption.dataset.code || dOption.getAttribute('data-code') || dOption.getAttribute('data-district-code');
                                                            if (districtCode) {
                                                                loadWards(districtCode).then(() => {
                                                                    if (savedWard) {
                                                                        const wardSelectAfterLoad = document.getElementById('shipping_ward');
                                                                        if (wardSelectAfterLoad) {
                                                                            wardSelectAfterLoad.value = savedWard;
                                                                        }
                                                                    }
                                                                }).catch(err => {
                                                                    console.error('Error loading wards:', err);
                                                                });
                                                            }
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                        }).catch(err => {
                                            console.error('Error loading districts:', err);
                                        });
                                    }
                                    break;
                                }
                            }
                        }
                    }
                }).catch(error => {
                    console.error('Error loading provinces:', error);
                });

                const subtotal = {{ $subtotal }};
                let currentShippingFee = 0;
                let fixedInstallationFee = {{ $shippingSettings->installation_fee ?? 0 }};
                if (fixedInstallationFee <= 0) {
                    fixedInstallationFee = 100000;
                }
                let installationFee = 0;
                let isInstallationSelected = false;
                const COD_LIMIT = 10000000; // 10 triệu

                // Hàm tính phí vận chuyển qua API
                function calculateShippingFee() {
                    const citySelect = document.getElementById('shipping_city');
                    const districtSelect = document.getElementById('shipping_district');
                    const selectedMethod = document.querySelector('input[name="shipping_method"]:checked')?.value ||
                        'standard';

                    if (!citySelect || !districtSelect) return;

                    const cityOption = citySelect.options[citySelect.selectedIndex];
                    const districtOption = districtSelect.options[districtSelect.selectedIndex];

                    const cityName = cityOption ? cityOption.text : '';
                    const districtName = districtOption ? districtOption.text : '';

                    if (!cityName || cityName === '-- Chọn Tỉnh/Thành phố --' ||
                        !districtName || districtName === '-- Chọn Quận/Huyện --') {
                        document.getElementById('shipping-fee-text').textContent =
                            'Vui lòng chọn địa chỉ để tính phí vận chuyển';
                        document.getElementById('shipping-fee-display').className = 'alert alert-info mb-0';
                        updateTotalDisplay();
                        return;
                    }

                    // Gọi API tính phí vận chuyển
                    fetch('{{ route('client.checkout.calculateShipping') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                city: cityName,
                                district: districtName,
                                subtotal: subtotal,
                                method: selectedMethod
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                currentShippingFee = data.fee;
                                document.getElementById('shipping_fee_input').value = data.fee;
                                const label = data.method_label || 'Phí vận chuyển';

                                if (data.is_free_shipping) {
                                    document.getElementById('shipping-fee-text').innerHTML =
                                        '<strong class="text-success">🎉 Đơn hàng được MIỄN PHÍ vận chuyển!</strong>';
                                    document.getElementById('shipping-fee-display').className =
                                        'alert alert-success mb-0';
                                } else {
                                    document.getElementById('shipping-fee-text').innerHTML =
                                        label + ': <strong>' + data.fee_formatted +
                                        '</strong>';
                                    document.getElementById('shipping-fee-display').className =
                                        'alert alert-warning mb-0';
                                }

                                // Cập nhật tổng tiền
                                updateTotalDisplay();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                }

                let currentDiscount = 0;
                let appliedCode = '';

                // Hàm kiểm tra phương thức thanh toán dựa trên tổng tiền
                function checkPaymentMethodAvailability(total) {
                    const cashRadio = document.getElementById('cash');
                    const cashLabel = document.querySelector('label[for="cash"]');
                    const cashContainer = document.getElementById('cod-payment-option');
                    const momoRadio = document.getElementById('momo');
                    const codMessage = document.getElementById('cod-restriction-message');

                    if (!cashRadio) return;

                    // Ngưỡng 10.000.000 đ
                    const threshold = 10000000;

                    if (total > threshold) {
                        // 1. Chuyển sang Momo nếu đang chọn COD
                        if (cashRadio.checked) {
                            if (momoRadio) {
                                momoRadio.checked = true;
                                cashRadio.required = false;
                            } else {
                                cashRadio.checked = false;
                            }
                        }

                        // 2. Ẩn COD và hiển thị thông báo
                        if (cashContainer) cashContainer.style.display = 'none';
                        if (codMessage) codMessage.style.display = 'block';

                    } else {
                        // Enable lại
                        cashRadio.disabled = false;
                        cashRadio.required = true;

                        if (cashContainer) cashContainer.style.display = 'block';
                        if (codMessage) codMessage.style.display = 'none';
                    }
                }

                function updateTotalDisplay() {
                    const total = Math.max(0, subtotal - currentDiscount + currentShippingFee + installationFee);

                    // Kiểm tra phương thức thanh toán
                    checkPaymentMethodAvailability(total);

                    const shippingFeeEl = document.getElementById('shipping-fee');
                    const totalAmountEl = document.getElementById('total-amount');
                    const installationRow = document.getElementById('installation-row');
                    const installationFeeEl = document.getElementById('installation-fee');

                    if (shippingFeeEl) {
                        shippingFeeEl.textContent = currentShippingFee === 0 ?
                            'Miễn phí' :
                            currentShippingFee.toLocaleString('vi-VN') + ' đ';
                    }
                    if (installationRow && installationFeeEl) {
                        // Hiển thị phí lắp đặt nếu được chọn
                        if (isInstallationSelected && installationFee > 0) {
                            installationRow.classList.remove('d-none');
                            installationRow.classList.add('d-flex');
                            installationRow.style.display = ''; // Clear inline style
                            installationFeeEl.textContent = installationFee.toLocaleString('vi-VN') + ' đ';
                        } else {
                            installationRow.classList.remove('d-flex');
                            installationRow.classList.add('d-none');
                            installationRow.style.display = ''; // Clear inline style
                        }
                    }
                    if (totalAmountEl) {
                        totalAmountEl.textContent = total.toLocaleString('vi-VN') + ' đ';
                    }
                }

                // Xử lý checkbox lắp đặt (giữ lại để tương thích, nhưng phí lắp đặt luôn được tính)
                const installationCheckbox = document.getElementById('installation-checkbox');
                if (installationCheckbox) {
                    // Mặc định không chọn
                    installationCheckbox.checked = false;
                    isInstallationSelected = false;
                    installationFee = 0;

                    const installationFeeInput = document.getElementById('installation_fee_input');
                    if (installationFeeInput) {
                        installationFeeInput.value = 0;
                    }

                    installationCheckbox.addEventListener('change', function() {
                        isInstallationSelected = this.checked;
                        const installationFeeInput = document.getElementById('installation_fee_input');
                        if (!isInstallationSelected) {
                            installationFee = 0;
                            if (installationFeeInput) installationFeeInput.value = 0;
                        } else {
                            // Ensure fallback here too
                            let fee = fixedInstallationFee;
                            if (fee <= 0) fee = 100000;

                            installationFee = fee;
                            if (installationFeeInput) installationFeeInput.value = installationFee;
                        }
                        updateTotalDisplay();
                    });
                }

                // Khởi tạo hiển thị khi trang load
                updateTotalDisplay();

                // Event listeners đã được gắn ở đầu DOMContentLoaded, không cần gắn lại

                const shippingInputs = document.querySelectorAll('input[name="shipping_method"]');
                shippingInputs.forEach(input => {
                    input.addEventListener('change', () => setTimeout(calculateShippingFee, 200));
                });

                // Áp dụng mã khuyến mãi
                const applyBtn = document.getElementById('apply-promotion-btn');
                const codeInput = document.getElementById('promotion-code');
                const discountRow = document.getElementById('discount-row');
                const appliedCodeEl = document.getElementById('applied-code');
                const discountAmountEl = document.getElementById('discount-amount');
                const messageEl = document.getElementById('promotion-message');

                function setMessage(text, type = 'info') {
                    if (!messageEl) return;
                    messageEl.className = 'small mt-2 text-' + (type === 'error' || type === 'danger' ? 'danger' :
                        type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'muted');
                    messageEl.textContent = text;
                }

                function clearPromotion() {
                    currentDiscount = 0;
                    appliedCode = '';

                    const voucherBadge = document.getElementById('voucher-badge');
                    if (voucherBadge) {
                        voucherBadge.classList.remove('d-flex');
                        voucherBadge.classList.add('d-none');
                    }
                    if (appliedCodeEl) appliedCodeEl.textContent = '';
                    if (discountAmountEl) discountAmountEl.textContent = '- 0 đ';
                    if (codeInput) codeInput.value = '';

                    updateTotalDisplay();
                }

                if (applyBtn) {
                    applyBtn.addEventListener('click', function() {
                        const code = (codeInput?.value || '').trim();
                        if (!code) {
                            setMessage('Vui lòng nhập mã khuyến mãi.', 'danger');
                            codeInput.focus();
                            return;
                        }

                        // Loading state
                        applyBtn.disabled = true;
                        const btnText = document.getElementById('promotion-btn-text');
                        const btnSpinner = document.getElementById('promotion-btn-spinner');
                        if (btnText) btnText.textContent = 'Đang xử lý...';
                        if (btnSpinner) btnSpinner.classList.remove('d-none');
                        setMessage('Đang kiểm tra mã khuyến mãi...', 'warning');

                        fetch('{{ route('client.checkout.applyPromotion') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    code
                                })
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

                                const voucherBadge = document.getElementById('voucher-badge');
                                if (voucherBadge) {
                                    voucherBadge.classList.remove('d-none');
                                    voucherBadge.classList.add('d-flex');
                                }
                                if (appliedCodeEl) appliedCodeEl.textContent = appliedCode;
                                if (discountAmountEl) discountAmountEl.textContent = '- ' +
                                    currentDiscount.toLocaleString('vi-VN') + ' đ';
                                updateTotalDisplay();

                                setMessage('✓ Áp dụng mã thành công!', 'success');
                            })
                            .catch(err => {
                                clearPromotion();
                                setMessage(err.message || 'Không thể áp dụng mã. Vui lòng thử lại.',
                                    'danger');
                            })
                            .finally(() => {
                                applyBtn.disabled = false;
                                if (btnText) btnText.textContent = 'Áp dụng';
                                if (btnSpinner) btnSpinner.classList.add('d-none');
                            });
                    });

                    // Cho phép nhấn Enter để áp dụng mã
                    if (codeInput) {
                        codeInput.addEventListener('keypress', function(e) {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                applyBtn.click();
                            }
                        });
                    }

                    // Xử lý chọn voucher từ danh sách
                    const voucherItems = document.querySelectorAll('.voucher-item');
                    voucherItems.forEach(item => {
                        item.addEventListener('click', function() {
                            const code = this.dataset.code;
                            if (codeInput) {
                                codeInput.value = code;
                                applyBtn.click();
                            }
                        });
                    });
                }

                // Event delegation cho nút hủy voucher
                document.addEventListener('click', function(e) {
                    if (e.target && (e.target.id === 'remove-promotion-btn' || e.target.closest(
                            '#remove-promotion-btn'))) {
                        e.preventDefault();
                        setMessage('Đang hủy mã...', 'warning');

                        fetch('{{ route('client.checkout.removePromotion') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(async res => {
                                if (res.status === 401) {
                                    window.location.href = '{{ route('client.login') }}';
                                    return null;
                                }
                                const data = await res.json();
                                if (!data.ok) {
                                    throw new Error(data.error || 'Có lỗi xảy ra.');
                                }
                                return data;
                            })
                            .then(data => {
                                if (!data) return;
                                clearPromotion();
                                setMessage('Đã hủy mã giảm giá.', 'info');
                            })
                            .catch(err => {
                                setMessage(err.message || 'Không thể hủy mã.', 'danger');
                            });
                    }
                });

                // Khởi tạo lần đầu - tính phí sau khi trang load
                updateTotalDisplay();
                setTimeout(calculateShippingFee, 1000);
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const header = document.querySelector('.client-header');
                const summary = document.querySelector('.checkout-summary-card');
                if (header && summary) {
                    const headerHeight = header.offsetHeight || 0;
                    summary.style.top = (headerHeight + 20) + 'px';
                    summary.style.zIndex = '900';
                }
            });
        </script>
    @endpush
@endsection
