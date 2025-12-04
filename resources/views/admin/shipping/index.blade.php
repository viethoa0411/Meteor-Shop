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

                        <div class="mb-3">
                            <label class="form-label">Phí nội thành (cùng quận/huyện) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="inner_city_fee" class="form-control" 
                                    value="{{ old('inner_city_fee', $settings->inner_city_fee) }}" 
                                    min="0" required>
                                <span class="input-group-text">đ</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phí ngoại thành (khác quận/huyện, cùng tỉnh) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="outer_city_fee" class="form-control" 
                                    value="{{ old('outer_city_fee', $settings->outer_city_fee) }}" 
                                    min="0" required>
                                <span class="input-group-text">đ</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phí tỉnh khác <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="other_province_fee" class="form-control"
                                    value="{{ old('other_province_fee', $settings->other_province_fee) }}"
                                    min="0" required>
                                <span class="input-group-text">đ</span>
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
                            <li>Đơn hàng <strong>≥ {{ number_format($settings->free_shipping_threshold) }}đ</strong>: <span class="text-success fw-bold">Miễn phí vận chuyển</span></li>
                            <li>Cùng quận/huyện: <strong>{{ number_format($settings->inner_city_fee) }}đ</strong></li>
                            <li>Khác quận/huyện (cùng tỉnh): <strong>{{ number_format($settings->outer_city_fee) }}đ</strong></li>
                            <li>Khác tỉnh/thành phố: <strong>{{ number_format($settings->other_province_fee) }}đ</strong></li>
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

    // Load tỉnh/thành phố
    fetch('https://provinces.open-api.vn/api/p/')
        .then(response => response.json())
        .then(data => {
            data.forEach(province => {
                const option = document.createElement('option');
                option.value = province.code;
                option.textContent = province.name;
                option.dataset.name = province.name;
                if (province.name === savedCity) {
                    option.selected = true;
                    loadDistricts(province.code, savedDistrict, savedWard);
                }
                citySelect.appendChild(option);
            });
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

        fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`)
            .then(response => response.json())
            .then(data => {
                districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';

                data.districts.forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.code;
                    option.textContent = district.name;
                    option.dataset.name = district.name;
                    if (district.name === savedDistrict) {
                        option.selected = true;
                        districtInput.value = district.name;
                        loadWards(district.code, savedWard);
                    }
                    districtSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Lỗi load quận/huyện:', error);
                districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
            });
    }

    function loadWards(districtCode, savedWard = null) {
        wardSelect.innerHTML = '<option value="">Đang tải...</option>';

        fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
            .then(response => response.json())
            .then(data => {
                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';

                data.wards.forEach(ward => {
                    const option = document.createElement('option');
                    option.value = ward.code;
                    option.textContent = ward.name;
                    option.dataset.name = ward.name;
                    if (ward.name === savedWard) {
                        option.selected = true;
                        wardInput.value = ward.name;
                    }
                    wardSelect.appendChild(option);
                });
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

