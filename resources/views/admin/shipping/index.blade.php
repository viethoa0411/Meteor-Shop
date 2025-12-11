@extends('admin.layouts.app')
@section('title', 'Cài đặt vận chuyển')

@section('content')
<div class="container-fluid py-4">
    {{-- Thông báo --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Tiêu đề --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h3 class="fw-bold text-primary mb-0">
                <i class="bi bi-truck me-2"></i>Cài đặt vận chuyển
            </h3>
        </div>
    </div>

    <form action="{{ route('admin.shipping.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            {{-- Địa chỉ gốc (Kho hàng) --}}
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Địa chỉ kho hàng (Gốc)</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                            <select id="origin_city_select" class="form-select" required>
                                <option value="">-- Chọn Tỉnh/Thành phố --</option>
                            </select>
                            <input type="hidden" id="origin_city" name="origin_city" value="{{ old('origin_city', $settings->origin_city) }}">
                            @error('origin_city')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Quận/Huyện <span class="text-danger">*</span></label>
                            <select id="origin_district_select" class="form-select" required>
                                <option value="">-- Chọn Quận/Huyện --</option>
                            </select>
                            <input type="hidden" id="origin_district" name="origin_district" value="{{ old('origin_district', $settings->origin_district) }}">
                            @error('origin_district')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phường/Xã <span class="text-danger">*</span></label>
                            <select id="origin_ward_select" class="form-select" required>
                                <option value="">-- Chọn Phường/Xã --</option>
                            </select>
                            <input type="hidden" id="origin_ward" name="origin_ward" value="{{ old('origin_ward', $settings->origin_ward) }}">
                            @error('origin_ward')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Địa chỉ chi tiết</label>
                            <input type="text" name="origin_address" class="form-control"
                                value="{{ old('origin_address', $settings->origin_address) }}"
                                placeholder="Số nhà, tên đường...">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Phí vận chuyển --}}
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Cài đặt phí vận chuyển</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Ngưỡng miễn phí vận chuyển <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="free_shipping_threshold" class="form-control" 
                                    value="{{ old('free_shipping_threshold', $settings->free_shipping_threshold) }}" 
                                    min="0" required>
                                <span class="input-group-text">đ</span>
                            </div>
                            <div class="form-text">Đơn hàng từ mức này trở lên sẽ được miễn phí vận chuyển</div>
                        </div>

                        <hr>
                        <h6 class="fw-bold">Phí kích thước (tính theo mét)</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Chiều dài - Mét đầu <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="first_length_price" class="form-control"
                                        value="{{ old('first_length_price', $settings->first_length_price) }}" min="0" required>
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Chiều dài - Mét tiếp theo <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="next_length_price" class="form-control"
                                        value="{{ old('next_length_price', $settings->next_length_price) }}" min="0" required>
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label">Chiều rộng - Mét đầu <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="first_width_price" class="form-control"
                                        value="{{ old('first_width_price', $settings->first_width_price) }}" min="0" required>
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Chiều rộng - Mét tiếp theo <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="next_width_price" class="form-control"
                                        value="{{ old('next_width_price', $settings->next_width_price) }}" min="0" required>
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label">Chiều cao - Mét đầu <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="first_height_price" class="form-control"
                                        value="{{ old('first_height_price', $settings->first_height_price) }}" min="0" required>
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Chiều cao - Mét tiếp theo <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="next_height_price" class="form-control"
                                        value="{{ old('next_height_price', $settings->next_height_price) }}" min="0" required>
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h6 class="fw-bold">Phí theo cân nặng</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Cân nặng đầu tiên (kg) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="first_weight_price" class="form-control"
                                        value="{{ old('first_weight_price', $settings->first_weight_price) }}" min="0" required>
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mỗi kg tiếp theo <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="next_weight_price" class="form-control"
                                        value="{{ old('next_weight_price', $settings->next_weight_price) }}" min="0" required>
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h6 class="fw-bold">Phụ phí theo phương thức</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Tên hiển thị giao nhanh</label>
                                <input type="text" name="express_label" class="form-control"
                                    value="{{ old('express_label', $settings->express_label) }}" required>
                                <div class="input-group mt-2">
                                    <select name="express_surcharge_type" class="form-select" style="max-width: 120px;">
                                        <option value="percent" {{ $settings->express_surcharge_type === 'percent' ? 'selected' : '' }}>%</option>
                                        <option value="fixed" {{ $settings->express_surcharge_type === 'fixed' ? 'selected' : '' }}>đ</option>
                                    </select>
                                    <input type="number" name="express_surcharge_value" class="form-control"
                                        value="{{ old('express_surcharge_value', $settings->express_surcharge_value) }}" min="0" required>
                                </div>
                                <div class="form-text">Áp dụng trên phí tiêu chuẩn</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tên hiển thị giao hỏa tốc</label>
                                <input type="text" name="fast_label" class="form-control"
                                    value="{{ old('fast_label', $settings->fast_label) }}" required>
                                <div class="input-group mt-2">
                                    <select name="fast_surcharge_type" class="form-select" style="max-width: 120px;">
                                        <option value="percent" {{ $settings->fast_surcharge_type === 'percent' ? 'selected' : '' }}>%</option>
                                        <option value="fixed" {{ $settings->fast_surcharge_type === 'fixed' ? 'selected' : '' }}>đ</option>
                                    </select>
                                    <input type="number" name="fast_surcharge_value" class="form-control"
                                        value="{{ old('fast_surcharge_value', $settings->fast_surcharge_value) }}" min="0" required>
                                </div>
                                <div class="form-text">Áp dụng trên phí tiêu chuẩn</div>
                            </div>
                        </div>

                        {{-- Hidden fields for base_fee and fee_per_km --}}
                        <input type="hidden" name="base_fee" value="{{ $settings->base_fee }}">
                        <input type="hidden" name="fee_per_km" value="{{ $settings->fee_per_km }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- Thông tin tóm tắt --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Thông tin tóm tắt</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Quy tắc tính phí vận chuyển:</strong></p>
                        <ul>
                            <li><strong>Tiêu chuẩn</strong> = (Dài + Rộng + Cao) theo mét đầu + mét tiếp theo + phí cân nặng đầu + mỗi kg tiếp theo, nhân với số lượng.</li>
                            <li><strong>{{ $settings->express_label }}</strong>: Phụ phí {{ $settings->express_surcharge_type === 'percent' ? $settings->express_surcharge_value . '%' : number_format($settings->express_surcharge_value) . 'đ' }} trên phí tiêu chuẩn.</li>
                            <li><strong>{{ $settings->fast_label }}</strong>: Phụ phí {{ $settings->fast_surcharge_type === 'percent' ? $settings->fast_surcharge_value . '%' : number_format($settings->fast_surcharge_value) . 'đ' }} trên phí tiêu chuẩn.</li>
                            <li>Đơn hàng từ <strong>{{ number_format($settings->free_shipping_threshold) }}đ</strong> trở lên: <span class="text-success fw-bold">Miễn phí vận chuyển</span></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Địa chỉ kho hàng hiện tại:</strong></p>
                        <p class="text-muted">
                            {{ $settings->origin_address ? $settings->origin_address . ', ' : '' }}
                            {{ $settings->origin_ward }}, {{ $settings->origin_district }}, {{ $settings->origin_city }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-check-circle me-2"></i>Lưu cài đặt
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const savedCity = "{{ $settings->origin_city }}";
    const savedDistrict = "{{ $settings->origin_district }}";
    const savedWard = "{{ $settings->origin_ward }}";

    const citySelect = document.getElementById('origin_city_select');
    const districtSelect = document.getElementById('origin_district_select');
    const wardSelect = document.getElementById('origin_ward_select');

    const cityInput = document.getElementById('origin_city');
    const districtInput = document.getElementById('origin_district');
    const wardInput = document.getElementById('origin_ward');

    // Load tỉnh/thành phố (Esgoo)
    fetch('https://esgoo.net/api-tinhthanh/1/0.htm')
        .then(response => response.json())
        .then(data => {
            if (data.error === 0) {
                data.data.forEach(province => {
                    const option = document.createElement('option');
                    option.value = province.id;
                    option.textContent = province.full_name;
                    option.dataset.name = province.full_name;
                    if (province.full_name === savedCity) {
                        option.selected = true;
                        loadDistricts(province.id, savedDistrict, savedWard);
                    }
                    citySelect.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Lỗi load tỉnh/thành phố:', error);
        });

    // Khi chọn tỉnh/thành phố
    citySelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        cityInput.value = selectedOption.dataset.name || selectedOption.textContent || '';
        districtInput.value = '';
        wardInput.value = '';
        if (this.value) {
            loadDistricts(this.value);
        } else {
            districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
            wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
        }
    });

    // Khi chọn quận/huyện
    districtSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        districtInput.value = selectedOption.dataset.name || selectedOption.textContent || '';
        wardInput.value = '';
        if (this.value) {
            loadWards(this.value);
        } else {
            wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
        }
    });

    // Khi chọn phường/xã
    wardSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        wardInput.value = selectedOption.dataset.name || selectedOption.textContent || '';
    });

    function loadDistricts(provinceCode, savedDistrict = null, savedWard = null) {
        districtSelect.innerHTML = '<option value="">Đang tải...</option>';
        wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';

        fetch(`https://esgoo.net/api-tinhthanh/2/${provinceCode}.htm`)
            .then(response => response.json())
            .then(data => {
                districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';

                if (data.error === 0) {
                    data.data.forEach(district => {
                        const option = document.createElement('option');
                        option.value = district.id;
                        option.textContent = district.full_name;
                        option.dataset.name = district.full_name;
                        if (district.full_name === savedDistrict) {
                            option.selected = true;
                            districtInput.value = district.full_name;
                            loadWards(district.id, savedWard);
                        }
                        districtSelect.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Lỗi load quận/huyện:', error);
                districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
            });
    }

    function loadWards(districtCode, savedWard = null) {
        wardSelect.innerHTML = '<option value="">Đang tải...</option>';

        fetch(`https://esgoo.net/api-tinhthanh/3/${districtCode}.htm`)
            .then(response => response.json())
            .then(data => {
                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';

                if (data.error === 0) {
                    data.data.forEach(ward => {
                        const option = document.createElement('option');
                        option.value = ward.id;
                        option.textContent = ward.full_name;
                        option.dataset.name = ward.full_name;
                        if (ward.full_name === savedWard) {
                            option.selected = true;
                            wardInput.value = ward.full_name;
                        }
                        wardSelect.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Lỗi load phường/xã:', error);
                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
            });
    }
});
</script>
@endpush
@endsection

