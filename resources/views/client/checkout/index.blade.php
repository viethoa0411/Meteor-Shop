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
                                <div id="shipping-fee-display" class="alert alert-info mb-0 position-relative">
                                    <i class="bi bi-truck me-2"></i>
                                    <span id="shipping-fee-text">Vui lòng chọn địa chỉ để tính phí vận chuyển</span>
                                    <span id="shipping-loading" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                                </div>
                                <input type="hidden" name="shipping_fee" id="shipping_fee_input" value="0">
                                <input type="hidden" name="installation_fee" id="installation_fee_input" value="0">
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
                                        {{ $shippingSettings->express_label }}
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="shipping_method" id="shipping_fast" value="fast">
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
                                <label class="form-label">Phương thức thanh toán <span class="text-danger">*</span></label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" id="cash"
                                        value="cash" {{ old('payment_method', 'cash') == 'cash' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="cash">
                                        <strong>Thanh toán khi nhận hàng</strong>

                                    </label>
                                </div>

                                @auth
                                    <div class="form-check">
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

                            <button type="submit" class="btn btn-primary btn-lg w-100" id="submit-checkout-btn">
                                <i class="bi bi-arrow-right me-2"></i>
                                <span id="submit-btn-text">Tiếp tục xác nhận</span>
                                <span id="submit-btn-spinner" class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
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
                                        <div class="input-group input-group-sm quantity-control" style="max-width: 120px;">
                                            <button type="button" class="btn btn-outline-secondary" id="qty-minus" aria-label="Giảm số lượng">−</button>
                                            <input type="number"
                                                id="quantity-input"
                                                name="quantity"
                                                class="form-control text-center"
                                                value="{{ $qty }}"
                                                min="1"
                                                max="{{ $stock }}"
                                                data-price="{{ $price }}"
                                                data-stock="{{ $stock }}"
                                                aria-label="Số lượng sản phẩm">
                                            <button type="button" class="btn btn-outline-secondary" id="qty-plus" aria-label="Tăng số lượng">+</button>
                                        </div>
                                        <small class="text-muted d-block mt-1">
                                            Tồn kho: <span id="stock-display" class="fw-semibold">{{ $stock }}</span>
                                            <span id="quantity-feedback" class="ms-2"></span>
                                        </small>
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

                        <div class="mb-2 d-flex justify-content-between" id="installation-row" style="display:none;">
                            <span>Phí lắp đặt:</span>
                            <strong id="installation-fee">0 đ</strong>
                        </div>
                        <div class="mb-3 pt-2 border-top d-flex justify-content-between">
                            <span class="fs-5 fw-bold">Tổng cộng:</span>
                            <span class="fs-5 fw-bold text-danger" id="total-amount">
                                {{ number_format($checkoutData['subtotal'], 0, ',', '.') }} đ
                            </span>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="installation-checkbox" name="installation">
                                <label class="form-check-label" for="installation-checkbox">
                                    <strong>Dịch vụ lắp đặt</strong>
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1">Phí lắp đặt sẽ được cộng thêm vào tổng tiền</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mã khuyến mãi</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="promotion-code" placeholder="Nhập mã khuyến mãi" aria-label="Mã khuyến mãi">
                                <button class="btn btn-outline-primary" type="button" id="apply-promotion-btn">
                                    <span id="promotion-btn-text">Áp dụng</span>
                                    <span id="promotion-btn-spinner" class="spinner-border spinner-border-sm d-none ms-1" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                            <div class="form-text" id="promotion-hint">Áp dụng mã sau khi chọn số lượng.</div>
                            <div class="small mt-2" id="promotion-message"></div>
                        </div>

                        <div class="alert alert-info small mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Miễn phí vận chuyển cho đơn hàng từ {{ number_format($shippingSettings->free_shipping_threshold, 0, ',', '.') }}đ
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('head')
        <style>
            /* Responsive cho checkout summary */
            @media (max-width: 991.98px) {
                .checkout-summary-card {
                    position: static !important;
                    margin-top: 2rem;
                }
            }

            /* Visual feedback cho quantity input */
            .quantity-control input:focus {
                border-color: #0d6efd;
                box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            }

            .quantity-updated {
                animation: quantityPulse 0.5s ease;
            }

            @keyframes quantityPulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.05); }
            }

            /* Loading states */
            .btn:disabled {
                opacity: 0.6;
                cursor: not-allowed;
            }

            /* Error states */
            .form-control.is-invalid {
                border-color: #dc3545;
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6 .4.4.4-.4m0 4.8-.4-.4-.4.4'/%3e%3c/svg%3e");
                background-repeat: no-repeat;
                background-position: right calc(0.375em + 0.1875rem) center;
                background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
            }

            /* Success feedback */
            .text-success-feedback {
                color: #198754;
                font-weight: 500;
            }

            /* Wallet warning */
            #wallet-warning {
                transition: all 0.3s ease;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            let currentDiscount = 0;
            let appliedCode = '';
            let installationFee = 0;
            let isInstallationSelected = false;
            let shippingCalculationTimeout = null;
            let quantityUpdateTimeout = null;

            // Load dữ liệu địa chỉ từ API
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
                    const response = await fetch('https://esgoo.net/api-tinhthanh/1/0.htm');
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();

                    console.log('API Response:', data); // Debug

                    if (data.error === 0 && data.data && Array.isArray(data.data)) {
                        provinces = data.data;
                        citySelect.innerHTML = '<option value="">-- Chọn Tỉnh/Thành phố --</option>';

                        let addedCount = 0;
                        let allProvinces = [];

                        provinces.forEach(province => {
                            // Lấy tên tỉnh từ nhiều nguồn có thể
                            const provinceName = province.full_name || province.name || province.title || '';

                            if (!provinceName) return; // Bỏ qua nếu không có tên

                            // Lưu tất cả để debug (chỉ 5 tỉnh đầu)
                            if (allProvinces.length < 5) {
                                allProvinces.push(provinceName);
                            }

                            // Kiểm tra nếu là tỉnh miền Bắc
                            if (isNorthernProvince(provinceName)) {
                                const option = document.createElement('option');
                                option.value = provinceName;
                                option.textContent = provinceName;
                                option.dataset.code = province.id || province.code || province.province_id || '';
                                citySelect.appendChild(option);
                                addedCount++;
                            }
                        });

                        // Debug: Log một số tên tỉnh để kiểm tra
                        if (addedCount === 0 && allProvinces.length > 0) {
                            console.log('Mẫu tên tỉnh từ API:', allProvinces);
                            console.log('Ví dụ normalize:', allProvinces[0], '->', normalizeProvinceName(allProvinces[0]));
                        }

                        // Kích hoạt select sau khi load xong
                        citySelect.disabled = false;

                        console.log(`Đã tải ${addedCount} tỉnh/thành phố miền Bắc (tổng ${provinces.length} từ API)`);

                        // Chỉ hiển thị tỉnh miền Bắc, không có fallback
                        if (addedCount === 0) {
                            citySelect.innerHTML = '<option value="">-- Không có tỉnh miền Bắc --</option>';
                            const errorOption = document.createElement('option');
                            errorOption.value = '';
                            errorOption.textContent = '⚠ Không tìm thấy tỉnh miền Bắc. Vui lòng liên hệ hỗ trợ.';
                            errorOption.disabled = true;
                            citySelect.appendChild(errorOption);

                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Không tìm thấy tỉnh miền Bắc',
                                    text: 'Hệ thống chỉ hỗ trợ giao hàng tại khu vực miền Bắc. Vui lòng liên hệ hỗ trợ nếu cần hỗ trợ.',
                                    confirmButtonText: 'Đã hiểu'
                                });
                            }
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
                    errorOption.textContent = '⚠ Không thể tải dữ liệu. Vui lòng tải lại trang.';
                    errorOption.disabled = true;
                    citySelect.appendChild(errorOption);
                    citySelect.disabled = false;

                    // Hiển thị thông báo lỗi cho người dùng
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Không thể tải danh sách tỉnh/thành phố',
                            text: 'Vui lòng tải lại trang hoặc liên hệ hỗ trợ nếu vấn đề vẫn tiếp tục.',
                            confirmButtonText: 'Đã hiểu'
                        });
                    }
                }
            }

            // Load quận/huyện
            async function loadDistricts(provinceCode) {
                const districtSelect = document.getElementById('shipping_district');
                if (!districtSelect) {
                    console.error('Không tìm thấy element shipping_district');
                    return;
                }

                if (!provinceCode) {
                    districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                    districtSelect.disabled = true;
                    return;
                }

                // Hiển thị loading
                districtSelect.innerHTML = '<option value="">Đang tải...</option>';
                districtSelect.disabled = true;

                try {
                    const response = await fetch(`https://esgoo.net/api-tinhthanh/2/${provinceCode}.htm`);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();

                    if (data.error === 0 && data.data && Array.isArray(data.data)) {
                        districts = data.data || [];
                        districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';

                        districts.forEach(district => {
                            const option = document.createElement('option');
                            option.value = district.full_name || district.name || '';
                            option.textContent = district.full_name || district.name || '';
                            option.dataset.code = district.id || district.code || '';
                            districtSelect.appendChild(option);
                        });
                        districtSelect.disabled = false;

                        // Reset phường/xã
                        const wardSelect = document.getElementById('shipping_ward');
                        if (wardSelect) {
                            wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                            wardSelect.disabled = true;
                        }
                    } else {
                        throw new Error('Dữ liệu API không hợp lệ');
                    }
                } catch (error) {
                    console.error('Lỗi khi tải danh sách quận/huyện:', error);
                    districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                    const errorOption = document.createElement('option');
                    errorOption.value = '';
                    errorOption.textContent = '⚠ Không thể tải dữ liệu';
                    errorOption.disabled = true;
                    districtSelect.appendChild(errorOption);
                    districtSelect.disabled = false;
                }
            }

            // Load phường/xã
            async function loadWards(districtCode) {
                const wardSelect = document.getElementById('shipping_ward');
                if (!wardSelect) {
                    console.error('Không tìm thấy element shipping_ward');
                    return;
                }

                if (!districtCode) {
                    wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                    wardSelect.disabled = true;
                    return;
                }

                // Hiển thị loading
                wardSelect.innerHTML = '<option value="">Đang tải...</option>';
                wardSelect.disabled = true;

                try {
                    const response = await fetch(`https://esgoo.net/api-tinhthanh/3/${districtCode}.htm`);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();

                    if (data.error === 0 && data.data && Array.isArray(data.data)) {
                        wards = data.data || [];
                        wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';

                        wards.forEach(ward => {
                            const option = document.createElement('option');
                            option.value = ward.full_name || ward.name || '';
                            option.textContent = ward.full_name || ward.name || '';
                            wardSelect.appendChild(option);
                        });
                        wardSelect.disabled = false;
                    } else {
                        throw new Error('Dữ liệu API không hợp lệ');
                    }
                } catch (error) {
                    console.error('Lỗi khi tải danh sách phường/xã:', error);
                    wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                    const errorOption = document.createElement('option');
                    errorOption.value = '';
                    errorOption.textContent = '⚠ Không thể tải dữ liệu';
                    errorOption.disabled = true;
                    wardSelect.appendChild(errorOption);
                    wardSelect.disabled = false;
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                // Đảm bảo select không bị disabled
                const citySelect = document.getElementById('shipping_city');
                const districtSelect = document.getElementById('shipping_district');
                const wardSelect = document.getElementById('shipping_ward');

                if (citySelect) {
                    citySelect.disabled = false;
                }
                if (districtSelect) {
                    districtSelect.disabled = true; // Disabled cho đến khi chọn tỉnh
                }
                if (wardSelect) {
                    wardSelect.disabled = true; // Disabled cho đến khi chọn quận
                }

                // Load tỉnh/thành phố khi trang load
                loadProvinces();

                // Xử lý khi chọn tỉnh/thành phố
                if (citySelect) {
                    citySelect.addEventListener('change', function() {
                        const selectedOption = this.options[this.selectedIndex];
                        const provinceCode = selectedOption.dataset.code;

                        if (provinceCode && provinceCode !== '') {
                            loadDistricts(provinceCode);
                        } else {
                            // Reset quận/huyện và phường/xã nếu không chọn tỉnh
                            if (districtSelect) {
                                districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                                districtSelect.disabled = true;
                            }
                            if (wardSelect) {
                                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                                wardSelect.disabled = true;
                            }
                        }

                        // Debounce shipping calculation
                        if (shippingCalculationTimeout) {
                            clearTimeout(shippingCalculationTimeout);
                        }
                        shippingCalculationTimeout = setTimeout(() => {
                            calculateShippingFee();
                        }, 500);
                    });
                }

                // Xử lý khi chọn quận/huyện
                if (districtSelect) {
                    districtSelect.addEventListener('change', function() {
                        const selectedOption = this.options[this.selectedIndex];
                        const districtCode = selectedOption.dataset.code;

                        if (districtCode && districtCode !== '') {
                            loadWards(districtCode);
                        } else {
                            // Reset phường/xã nếu không chọn quận
                            if (wardSelect) {
                                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                                wardSelect.disabled = true;
                            }
                        }

                        // Tính phí vận chuyển ngay khi chọn quận/huyện xong
                        if (shippingCalculationTimeout) {
                            clearTimeout(shippingCalculationTimeout);
                        }
                        shippingCalculationTimeout = setTimeout(() => {
                            calculateShippingFee();
                        }, 300);
                    });
                }

                // Xử lý khi chọn phường/xã (cũng tính lại phí)
                if (wardSelect) {
                    wardSelect.addEventListener('change', function() {
                        // Debounce shipping calculation
                        if (shippingCalculationTimeout) {
                            clearTimeout(shippingCalculationTimeout);
                        }
                        shippingCalculationTimeout = setTimeout(() => {
                            calculateShippingFee();
                        }, 300);
                    });
                }

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

                // Hàm cập nhật số lượng và tính toán với visual feedback
                function updateQuantity(newQty, showFeedback = true) {
                    // Đảm bảo số lượng hợp lệ
                    newQty = Math.max(1, Math.min(newQty, maxStock));
                    const oldQty = parseInt(quantityInput.value) || 1;

                    quantityInput.value = newQty;
                    if (quantityHidden) {
                        quantityHidden.value = newQty;
                    }

                    // Visual feedback
                    if (showFeedback && oldQty !== newQty) {
                        quantityInput.classList.add('quantity-updated');
                        const feedbackEl = document.getElementById('quantity-feedback');
                        if (feedbackEl) {
                            feedbackEl.textContent = '✓ Đã cập nhật';
                            feedbackEl.className = 'ms-2 text-success-feedback';
                            setTimeout(() => {
                                feedbackEl.textContent = '';
                                feedbackEl.className = 'ms-2';
                            }, 2000);
                        }
                        setTimeout(() => {
                            quantityInput.classList.remove('quantity-updated');
                        }, 500);
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

                    // Debounce shipping calculation
                    if (shippingCalculationTimeout) {
                        clearTimeout(shippingCalculationTimeout);
                    }
                    shippingCalculationTimeout = setTimeout(() => {
                        calculateShippingFee();
                    }, 500);
                }

                // Hàm tính phí vận chuyển qua API với loading state
                function calculateShippingFee() {
                    const citySelect = document.getElementById('shipping_city');
                    const districtSelect = document.getElementById('shipping_district');
                    const wardSelect = document.getElementById('shipping_ward');
                    const addressInput = document.querySelector('input[name="shipping_address"]');
                    const selectedMethod = document.querySelector('input[name="shipping_method"]:checked')?.value || 'standard';
                    const shippingLoading = document.getElementById('shipping-loading');
                    const shippingFeeText = document.getElementById('shipping-fee-text');
                    const shippingFeeDisplay = document.getElementById('shipping-fee-display');

                    if (!citySelect || !districtSelect) return;

                    const cityOption = citySelect.options[citySelect.selectedIndex];
                    const districtOption = districtSelect.options[districtSelect.selectedIndex];
                    const wardOption = wardSelect ? wardSelect.options[wardSelect.selectedIndex] : null;

                    const cityName = cityOption ? cityOption.text : '';
                    const districtName = districtOption ? districtOption.text : '';
                    const wardName = wardOption ? wardOption.text : '';
                    const addressDetail = addressInput ? addressInput.value.trim() : '';

                    // Kiểm tra đã chọn đủ tỉnh và quận/huyện chưa
                    if (!cityName || cityName === '-- Chọn Tỉnh/Thành phố --' ||
                        !districtName || districtName === '-- Chọn Quận/Huyện --') {
                        if (shippingFeeText) {
                            shippingFeeText.textContent = 'Vui lòng chọn địa chỉ để tính phí vận chuyển';
                        }
                        if (shippingFeeDisplay) {
                            shippingFeeDisplay.className = 'alert alert-info mb-0 position-relative';
                        }
                        if (shippingLoading) {
                            shippingLoading.classList.add('d-none');
                        }
                        // Reset phí về 0
                        currentShippingFee = 0;
                        const shippingFeeInput = document.getElementById('shipping_fee_input');
                        if (shippingFeeInput) {
                            shippingFeeInput.value = 0;
                        }
                        updateTotalDisplay();
                        return;
                    }

                    // Hiển thị loading
                    if (shippingLoading) {
                        shippingLoading.classList.remove('d-none');
                    }
                    if (shippingFeeText) {
                        shippingFeeText.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Đang tính phí vận chuyển...';
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
                            ward: wardName,
                            address: addressDetail,
                            subtotal: currentSubtotal,
                            method: selectedMethod,
                            quantity: parseInt(quantityInput.value) || 1
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (shippingLoading) {
                            shippingLoading.classList.add('d-none');
                        }

                        if (data.success) {
                            currentShippingFee = data.fee;
                            const shippingFeeInput = document.getElementById('shipping_fee_input');
                            if (shippingFeeInput) {
                                shippingFeeInput.value = data.fee;
                            }
                            const label = data.method_label || 'Phí vận chuyển';

                            // Kiểm tra xem có phải miễn phí vận chuyển do đạt ngưỡng không
                            // CHỈ hiển thị "miễn phí" nếu standard_fee > 0 (có phí vận chuyển) nhưng fee = 0 (được miễn phí)
                            if (data.is_free_shipping && data.standard_fee > 0) {
                                // Miễn phí vận chuyển do đạt ngưỡng
                                if (shippingFeeText) {
                                    shippingFeeText.innerHTML =
                                        '<strong class="text-success">🎉 Đơn hàng được MIỄN PHÍ vận chuyển!</strong>';
                                }
                                if (shippingFeeDisplay) {
                                    shippingFeeDisplay.className = 'alert alert-success mb-0 position-relative';
                                }
                            } else if (data.fee === 0 && data.standard_fee === 0) {
                                // Không có phí vận chuyển do thiếu dữ liệu
                                if (shippingFeeText) {
                                    shippingFeeText.innerHTML =
                                        '<span class="text-warning">⚠️ Không thể tính phí vận chuyển. Vui lòng kiểm tra thông tin sản phẩm (kích thước, cân nặng) hoặc cài đặt phí vận chuyển trong admin.</span>';
                                }
                                if (shippingFeeDisplay) {
                                    shippingFeeDisplay.className = 'alert alert-warning mb-0 position-relative';
                                }
                            } else {
                                // Có phí vận chuyển
                                if (shippingFeeText) {
                                    shippingFeeText.innerHTML =
                                        label + ': <strong>' + data.fee_formatted + '</strong>';
                                }
                                if (shippingFeeDisplay) {
                                    shippingFeeDisplay.className = 'alert alert-warning mb-0 position-relative';
                                }
                            }

                            // Cập nhật tổng tiền ngay lập tức
                            updateTotalDisplay();
                        } else {
                            if (shippingFeeText) {
                                shippingFeeText.textContent = 'Không thể tính phí vận chuyển. Vui lòng thử lại.';
                            }
                            if (shippingFeeDisplay) {
                                shippingFeeDisplay.className = 'alert alert-danger mb-0 position-relative';
                            }
                            // Reset phí về 0 nếu lỗi
                            currentShippingFee = 0;
                            const shippingFeeInput = document.getElementById('shipping_fee_input');
                            if (shippingFeeInput) {
                                shippingFeeInput.value = 0;
                            }
                            updateTotalDisplay();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (shippingLoading) {
                            shippingLoading.classList.add('d-none');
                        }
                        if (shippingFeeText) {
                            shippingFeeText.textContent = 'Lỗi khi tính phí vận chuyển. Vui lòng thử lại.';
                        }
                        if (shippingFeeDisplay) {
                            shippingFeeDisplay.className = 'alert alert-danger mb-0 position-relative';
                        }
                        // Reset phí về 0 nếu lỗi
                        currentShippingFee = 0;
                        const shippingFeeInput = document.getElementById('shipping_fee_input');
                        if (shippingFeeInput) {
                            shippingFeeInput.value = 0;
                        }
                        updateTotalDisplay();
                    });
                }

                // Hàm cập nhật hiển thị tổng tiền
                function updateTotalDisplay() {
                    const total = Math.max(0, (currentSubtotal - currentDiscount) + currentShippingFee + installationFee);
                    const shippingFeeEl = document.getElementById('shipping-fee');
                    const totalAmountEl = document.getElementById('total-amount');
                    const installationRow = document.getElementById('installation-row');
                    const installationFeeEl = document.getElementById('installation-fee');

                    if (shippingFeeEl) {
                        shippingFeeEl.textContent = currentShippingFee === 0
                            ? 'Miễn phí'
                            : currentShippingFee.toLocaleString('vi-VN') + ' đ';
                    }
                    if (installationRow && installationFeeEl) {
                        if (isInstallationSelected && installationFee > 0) {
                            installationRow.style.display = 'flex';
                            installationFeeEl.textContent = installationFee.toLocaleString('vi-VN') + ' đ';
                        } else {
                            installationRow.style.display = 'none';
                        }
                    }
                    if (totalAmountEl) {
                        totalAmountEl.textContent = total.toLocaleString('vi-VN') + ' đ';
                    }
                }

                // Xử lý checkbox lắp đặt
                const installationCheckbox = document.getElementById('installation-checkbox');
                if (installationCheckbox) {
                    const fixedInstallationFee = 100000;

                    installationCheckbox.addEventListener('change', function() {
                        isInstallationSelected = this.checked;
                        const installationFeeInput = document.getElementById('installation_fee_input');
                        if (!isInstallationSelected) {
                            installationFee = 0;
                            if (installationFeeInput) installationFeeInput.value = 0;
                        } else {
                            installationFee = fixedInstallationFee;
                            if (installationFeeInput) installationFeeInput.value = installationFee;
                        }
                        updateTotalDisplay();
                    });
                }

                // Lắng nghe sự kiện thay đổi địa chỉ - Đã gộp vào sự kiện change ở trên

                // Nút giảm số lượng
                qtyMinus.addEventListener('click', function(e) {
                    e.preventDefault();
                    const currentQty = parseInt(quantityInput.value) || 1;
                    if (currentQty > 1) {
                        updateQuantity(currentQty - 1, true);
                        // Xóa mã khuyến mãi nếu đang áp dụng
                        if (currentDiscount > 0) {
                            clearPromotion();
                            setMessage('Số lượng đã thay đổi. Vui lòng áp dụng lại mã.', 'warning');
                        }
                    }
                });

                // Nút tăng số lượng
                qtyPlus.addEventListener('click', function(e) {
                    e.preventDefault();
                    const currentQty = parseInt(quantityInput.value) || 1;
                    if (currentQty < maxStock) {
                        updateQuantity(currentQty + 1, true);
                        // Xóa mã khuyến mãi nếu đang áp dụng
                        if (currentDiscount > 0) {
                            clearPromotion();
                            setMessage('Số lượng đã thay đổi. Vui lòng áp dụng lại mã.', 'warning');
                        }
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'Đã đạt tối đa',
                            text: `Số lượng tối đa là ${maxStock} sản phẩm.`,
                            confirmButtonText: 'Đã hiểu'
                        });
                    }
                });

                // Xử lý thay đổi số lượng với debouncing (chỉ dùng change event để tránh duplicate)
                quantityInput.addEventListener('change', function() {
                    let newQty = parseInt(this.value) || 1;
                    if (newQty < 1) {
                        newQty = 1;
                        this.value = 1;
                    } else if (newQty > maxStock) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Số lượng vượt quá tồn kho',
                            text: `Số lượng tối đa là ${maxStock}. Vui lòng chọn lại.`,
                            confirmButtonText: 'Đã hiểu'
                        });
                        newQty = maxStock;
                        this.value = maxStock;
                    }

                    // Debounce update để tránh gọi quá nhiều lần
                    if (quantityUpdateTimeout) {
                        clearTimeout(quantityUpdateTimeout);
                    }
                    quantityUpdateTimeout = setTimeout(() => {
                        updateQuantity(newQty, true);
                        // Xóa mã khuyến mãi nếu đang áp dụng
                        if (currentDiscount > 0) {
                            clearPromotion();
                            setMessage('Số lượng đã thay đổi. Vui lòng áp dụng lại mã.', 'warning');
                        }
                    }, 300);
                });

                // Validate real-time khi nhập
                quantityInput.addEventListener('input', function() {
                    let newQty = parseInt(this.value) || '';
                    if (newQty === '' || isNaN(newQty)) {
                        return; // Cho phép nhập rỗng tạm thời
                    }
                    if (newQty < 1) {
                        this.value = 1;
                    } else if (newQty > maxStock) {
                        this.value = maxStock;
                    }
                });

                // Khi thay đổi phương thức vận chuyển
                shippingInputs.forEach(input => {
                    input.addEventListener('change', function() {
                        const subtotal = price * parseInt(quantityInput.value) || price;
                        currentSubtotal = subtotal;
                        // Debounce shipping calculation
                        if (shippingCalculationTimeout) {
                            clearTimeout(shippingCalculationTimeout);
                        }
                        shippingCalculationTimeout = setTimeout(() => {
                            calculateShippingFee();
                        }, 300);
                    });
                });

                // Real-time wallet balance check
                @auth
                @php
                    $wallet = \App\Models\ClientWallet::where('user_id', auth()->id())->first();
                    $walletBalance = $wallet ? $wallet->balance : 0;
                @endphp
                const walletRadio = document.getElementById('wallet');
                const walletWarning = document.getElementById('wallet-warning');
                const walletBalance = {{ $walletBalance }};

                function checkWalletBalance() {
                    const total = Math.max(0, (currentSubtotal - currentDiscount) + currentShippingFee + installationFee);
                    if (walletRadio && walletRadio.checked && walletWarning) {
                        if (walletBalance < total) {
                            walletWarning.classList.remove('d-none');
                        } else {
                            walletWarning.classList.add('d-none');
                        }
                    }
                }

                if (walletRadio) {
                    walletRadio.addEventListener('change', function() {
                        if (this.checked) {
                            checkWalletBalance();
                        } else if (walletWarning) {
                            walletWarning.classList.add('d-none');
                        }
                    });
                }

                // Check wallet balance khi tổng tiền thay đổi
                const originalUpdateTotalDisplay = updateTotalDisplay;
                updateTotalDisplay = function() {
                    originalUpdateTotalDisplay();
                    checkWalletBalance();
                };
                @endauth

                // Form validation trước khi submit
                const checkoutForm = document.getElementById('checkoutForm');
                const submitBtn = document.getElementById('submit-checkout-btn');
                const submitBtnText = document.getElementById('submit-btn-text');
                const submitBtnSpinner = document.getElementById('submit-btn-spinner');

                if (checkoutForm && submitBtn) {
                    checkoutForm.addEventListener('submit', function(e) {
                        // Kiểm tra wallet balance nếu chọn thanh toán bằng ví
                        @auth
                        @php
                            $wallet = \App\Models\ClientWallet::where('user_id', auth()->id())->first();
                            $walletBalance = $wallet ? $wallet->balance : 0;
                        @endphp
                        const walletRadioCheck = document.getElementById('wallet');
                        const walletBalanceCheck = {{ $walletBalance }};
                        if (walletRadioCheck && walletRadioCheck.checked) {
                            const total = Math.max(0, (currentSubtotal - currentDiscount) + currentShippingFee + installationFee);
                            if (walletBalanceCheck < total) {
                                e.preventDefault();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Số dư không đủ',
                                    text: `Số dư ví của bạn (${number_format($walletBalance)} đ) không đủ để thanh toán đơn hàng (${total.toLocaleString('vi-VN')} đ). Vui lòng nạp thêm tiền hoặc chọn phương thức thanh toán khác.`,
                                    confirmButtonText: 'Đã hiểu'
                                });
                                return false;
                            }
                        }
                        @endauth

                        // Loading state khi submit
                        submitBtn.disabled = true;
                        if (submitBtnText) submitBtnText.textContent = 'Đang xử lý...';
                        if (submitBtnSpinner) submitBtnSpinner.classList.remove('d-none');
                    });
                }

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

                            setMessage('✓ Áp dụng mã thành công!', 'success');
                        })
                        .catch(err => {
                            clearPromotion();
                            setMessage(err.message || 'Không thể áp dụng mã. Vui lòng thử lại.', 'danger');
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
                }

                // Khởi tạo lần đầu
                const initialQty = parseInt(quantityInput.value) || 1;
                currentSubtotal = price * initialQty;

                // Tính phí vận chuyển sau khi trang load xong
                setTimeout(calculateShippingFee, 1000);
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const header = document.querySelector('.client-header');
                const summary = document.querySelector('.checkout-summary-card');
                const leftFirstCard = document.querySelector('.col-lg-8 .card');
                if (header && summary && leftFirstCard) {
                    const headerHeight = header.offsetHeight || 0;
                    summary.style.top = (headerHeight + 20) + 'px';
                    summary.style.zIndex = '900';
                }
            });
        </script>
    @endpush
@endsection
