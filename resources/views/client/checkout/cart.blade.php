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
                                <div class="form-check mb-2">
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
                                <span id="voucher-badge" class="badge bg-light text-primary border {{ (isset($checkoutData['promotion']) && !empty($checkoutData['promotion']['code'])) ? 'd-flex' : 'd-none' }} align-items-center py-2 px-2">
                                    <i class="bi bi-ticket-perforated me-1"></i>
                                    <span id="applied-code" class="me-1">{{ $checkoutData['promotion']['code'] ?? '' }}</span>
                                    <span id="remove-promotion-btn" class="ms-2 text-danger hover-opacity-75" style="cursor: pointer;" title="Hủy mã">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </span>
                                </span>
                            </div>
                            <strong class="text-success" id="discount-amount">- {{ number_format($checkoutData['discount_amount'] ?? 0, 0, ',', '.') }} đ</strong>
                        </div>
                        <div class="mb-2 d-flex justify-content-between" id="installation-row" style="display:none;">
                            <span>Phí lắp đặt:</span>
                            <strong id="installation-fee">0 đ</strong>
                        </div>
                        <div class="mb-3 pt-2 border-top d-flex justify-content-between">
                            <span class="fs-5 fw-bold">Tổng cộng:</span>
                            <span class="fs-5 fw-bold text-danger" id="total-amount">
                                {{ number_format($subtotal, 0, ',', '.') }} đ
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

                            {{-- Danh sách voucher --}}
                            @if(isset($promotions) && $promotions->count() > 0)
                                <div class="mt-3">
                                    <label class="form-label fw-bold small">Mã giảm giá khả dụng:</label>
                                    <div class="list-group" id="voucher-list" style="max-height: 200px; overflow-y: auto;">
                                        @foreach($promotions as $promo)
                                            <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center voucher-item p-2"
                                                    data-code="{{ $promo->code }}">
                                                <div class="me-2">
                                                    <div class="fw-bold text-primary small">{{ $promo->code }}</div>
                                                    <small class="text-muted" style="font-size: 0.75rem;">{{ $promo->description ?? $promo->name }}</small>
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
                            Miễn phí vận chuyển cho đơn hàng từ {{ number_format($shippingSettings->free_shipping_threshold, 0, ',', '.') }}đ
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
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

                        let addedCount = 0;
                        provinces.forEach(province => {
                            // Lấy tên tỉnh từ nhiều nguồn có thể
                            const provinceName = province.full_name || province.name || province.title || '';

                            // Chỉ thêm các tỉnh miền Bắc
                            if (isNorthernProvince(provinceName)) {
                                const option = document.createElement('option');
                                option.value = provinceName; // Giữ nguyên tên gốc hiển thị
                                option.textContent = provinceName;
                                option.dataset.code = province.id || province.code || province.province_id;
                                citySelect.appendChild(option);
                                addedCount++;
                            }
                        });

                        if (addedCount === 0) {
                            const errorOption = document.createElement('option');
                            errorOption.value = '';
                            errorOption.textContent = '⚠ Không tìm thấy tỉnh miền Bắc (API changed?)';
                            citySelect.appendChild(errorOption);
                        }
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

                const subtotal = {{ $subtotal }};
                let currentShippingFee = 0;
                let installationFee = 0;
                let isInstallationSelected = false;

                // Hàm tính phí vận chuyển qua API
                function calculateShippingFee() {
                    const citySelect = document.getElementById('shipping_city');
                    const districtSelect = document.getElementById('shipping_district');
                    const selectedMethod = document.querySelector('input[name="shipping_method"]:checked')?.value || 'standard';

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
                    const cashContainer = cashRadio ? cashRadio.closest('.form-check') : null;
                    const momoRadio = document.getElementById('momo');
                    const warningId = 'cod-warning-text';

                    if (!cashRadio) return;

                    // Ngưỡng 5.000.000 đ
                    const threshold = 5000000;

                    if (total > threshold) {
                        // 1. Chuyển sang Momo nếu đang chọn COD
                        if (cashRadio.checked) {
                            if (momoRadio) {
                                momoRadio.checked = true;
                            } else {
                                cashRadio.checked = false;
                            }
                        }

                        // 2. Disable và style lại COD
                        cashRadio.disabled = true;
                        if (cashContainer) {
                            cashContainer.classList.add('opacity-50');
                            cashContainer.title = "Không hỗ trợ thanh toán khi nhận hàng cho đơn trên 5 triệu";
                        }

                        // 3. Thêm dòng thông báo nhỏ ngay dưới label (thay vì alert box to)
                        let warningText = document.getElementById(warningId);
                        if (!warningText && cashLabel) {
                            warningText = document.createElement('div');
                            warningText.id = warningId;
                            warningText.className = 'alert alert-danger py-1 px-2 mt-2 mb-0 d-inline-block small fw-bold';
                            warningText.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> Chỉ hỗ trợ đơn hàng dưới 5.000.000đ';
                            cashLabel.parentNode.appendChild(warningText);
                        } else if (warningText) {
                            warningText.style.display = 'block';
                        }

                        // Xóa style cũ nếu có (đề phòng)
                        if (cashLabel) {
                            cashLabel.style.textDecoration = 'none';
                            cashLabel.classList.remove('text-muted'); // opacity ở container đã đủ làm mờ
                        }

                        // Xóa alert box cũ (nếu còn từ code trước)
                        const oldMsg = document.getElementById('cod-disabled-msg');
                        if (oldMsg) oldMsg.remove();

                    } else {
                        // Enable lại
                        cashRadio.disabled = false;
                        if (cashContainer) {
                            cashContainer.classList.remove('opacity-50');
                            cashContainer.removeAttribute('title');
                        }

                        // Ẩn warning text
                        const warningText = document.getElementById(warningId);
                        if (warningText) warningText.style.display = 'none';

                        // Xóa alert box cũ
                        const oldMsg = document.getElementById('cod-disabled-msg');
                        if (oldMsg) oldMsg.remove();
                    }
                }

                // Hàm cập nhật hiển thị tổng tiền
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
                    messageEl.className = 'small mt-2 text-' + (type === 'error' || type === 'danger' ? 'danger' : type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'muted');
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

                            const voucherBadge = document.getElementById('voucher-badge');
                            if (voucherBadge) {
                                voucherBadge.classList.remove('d-none');
                                voucherBadge.classList.add('d-flex');
                            }
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
                    if (e.target && (e.target.id === 'remove-promotion-btn' || e.target.closest('#remove-promotion-btn'))) {
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
