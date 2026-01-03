@extends('admin.layouts.app')
@section('title', 'Cài đặt vận chuyển')

@push('styles')
<style>
    #distancesTable tbody tr:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s;
    }
    .badge {
        font-size: 0.9em;
        padding: 0.5em 0.75em;
    }
    .pagination .page-link {
        color: #0d6efd;
    }
    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    /* Đảm bảo hai card có cùng chiều cao */
    .row > .col-lg-6 > .card {
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .row > .col-lg-6 > .card > .card-body {
        flex: 1;
        overflow-y: auto;
    }
</style>
@endpush

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

    {{-- ============================================
         CÀI ĐẶT ĐỊA CHỈ KHO HÀNG GỐC
         ============================================ --}}
    <form action="{{ route('admin.shipping.update') }}" method="POST" id="originAddressForm">
        @csrf
        @method('PUT')

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Cài đặt địa chỉ kho hàng gốc</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                        <select id="origin_city_select" class="form-select">
                            <option value="">-- Chọn Tỉnh/Thành phố --</option>
                        </select>
                        <input type="hidden" id="origin_city" name="origin_city" value="{{ old('origin_city', $settings->origin_city) }}" required>
                        @error('origin_city')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Quận/Huyện <span class="text-danger">*</span></label>
                        <select id="origin_district_select" class="form-select">
                            <option value="">-- Chọn Quận/Huyện --</option>
                        </select>
                        <input type="hidden" id="origin_district" name="origin_district" value="{{ old('origin_district', $settings->origin_district) }}" required>
                        @error('origin_district')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phường/Xã <span class="text-danger">*</span></label>
                        <select id="origin_ward_select" class="form-select">
                            <option value="">-- Chọn Phường/Xã --</option>
                        </select>
                        <input type="hidden" id="origin_ward" name="origin_ward" value="{{ old('origin_ward', $settings->origin_ward) }}" required>
                        @error('origin_ward')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Địa chỉ chi tiết</label>
                        <input type="text" name="origin_address" class="form-control"
                            value="{{ old('origin_address', $settings->origin_address) }}"
                            placeholder="Số nhà, tên đường...">
                    </div>
                </div>

                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Lưu địa chỉ kho hàng
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- ============================================
         CÀI ĐẶT PHÍ VẬN CHUYỂN
         ============================================ --}}
    <form action="{{ route('admin.shipping.update') }}" method="POST" id="shippingFeeForm">
        @csrf
        @method('PUT')

        <div class="row">
            {{-- Phí vận chuyển --}}
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Cài đặt phí vận chuyển</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold">Khoảng cách mặc định</h6>
                        <div class="mb-3">
                            <label class="form-label">Khoảng cách mặc định (km) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="default_distance_km" class="form-control"
                                    value="{{ old('default_distance_km', $settings->default_distance_km ?? 10) }}"
                                    min="0" step="0.01" required>
                                <span class="input-group-text">km</span>
                            </div>
                            <div class="form-text">Khoảng cách này sẽ được sử dụng khi không tìm thấy địa chỉ khách hàng trong bảng cài đặt khoảng cách</div>
                        </div>

                        <hr>
                        <h6 class="fw-bold">Phí lắp đặt</h6>
                        <div class="mb-3">
                            <label class="form-label">Phí lắp đặt <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="installation_fee" class="form-control"
                                    value="{{ old('installation_fee', $settings->installation_fee ?? 0) }}"
                                    min="0" required>
                                <span class="input-group-text">đ</span>
                            </div>
                            <div class="form-text">Phí lắp đặt sẽ được cộng thêm vào tổng tiền khi khách hàng chọn dịch vụ lắp đặt</div>
                        </div>

                        <hr>
                        <h6 class="fw-bold">Phí kích thước (Chiều dài, Rộng, Cao)</h6>
                        <div class="alert alert-info small mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Công thức:</strong> Tính phí theo block cm (cm đầu + cm tiếp theo) cho từng chiều, sau đó nhân với khoảng cách và số lượng
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-12">
                                <h6 class="text-primary">Chiều dài</h6>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Block cm đầu <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="length_block_cm" class="form-control"
                                        value="{{ old('length_block_cm', $settings->length_block_cm ?? 200) }}" min="1" max="1000" required>
                                    <span class="input-group-text">cm</span>
                                </div>
                                <div class="form-text">Ví dụ: 200cm đầu</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Phí block cm đầu <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="first_length_price" class="form-control"
                                        value="{{ old('first_length_price', $settings->first_length_price ?? 10000) }}" min="0" required>
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Phí block cm tiếp theo <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="next_length_price" class="form-control"
                                        value="{{ old('next_length_price', $settings->next_length_price ?? 5000) }}" min="0" required>
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-12">
                                <h6 class="text-primary">Chiều rộng</h6>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Block cm đầu <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="width_block_cm" class="form-control"
                                        value="{{ old('width_block_cm', $settings->width_block_cm ?? 200) }}" min="1" max="1000" required>
                                    <span class="input-group-text">cm</span>
                                </div>
                                <div class="form-text">Ví dụ: 200cm đầu</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Phí block cm đầu <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="first_width_price" class="form-control"
                                        value="{{ old('first_width_price', $settings->first_width_price ?? 8000) }}" min="0" required>
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Phí block cm tiếp theo <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="next_width_price" class="form-control"
                                        value="{{ old('next_width_price', $settings->next_width_price ?? 4000) }}" min="0" required>
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-12">
                                <h6 class="text-primary">Chiều cao</h6>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Block cm đầu <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="height_block_cm" class="form-control"
                                        value="{{ old('height_block_cm', $settings->height_block_cm ?? 200) }}" min="1" max="1000" required>
                                    <span class="input-group-text">cm</span>
                                </div>
                                <div class="form-text">Ví dụ: 200cm đầu</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Phí block cm đầu <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="first_height_price" class="form-control"
                                        value="{{ old('first_height_price', $settings->first_height_price ?? 8000) }}" min="0" required>
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Phí block cm tiếp theo <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="next_height_price" class="form-control"
                                        value="{{ old('next_height_price', $settings->next_height_price ?? 4000) }}" min="0" required>
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h6 class="fw-bold">Phí cân nặng</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Block kg đầu <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="weight_block_kg" class="form-control"
                                        value="{{ old('weight_block_kg', $settings->weight_block_kg ?? 10) }}" min="1" max="100" required>
                                    <span class="input-group-text">kg</span>
                                </div>
                                <div class="form-text">Ví dụ: 10kg đầu</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Phí block kg đầu <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="first_weight_price" class="form-control"
                                        value="{{ old('first_weight_price', $settings->first_weight_price ?? 15000) }}" min="0" required>
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Phí block kg tiếp theo <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="next_weight_price" class="form-control"
                                        value="{{ old('next_weight_price', $settings->next_weight_price ?? 7000) }}" min="0" required>
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h6 class="fw-bold">Phí tối thiểu</h6>
                        <div class="mb-3">
                            <label class="form-label">Phí vận chuyển tối thiểu <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="min_shipping_fee" class="form-control"
                                    value="{{ old('min_shipping_fee', $settings->min_shipping_fee ?? 30000) }}" min="0" required>
                                <span class="input-group-text">đ</span>
                            </div>
                            <div class="form-text">Phí tối thiểu khi tính ra quá thấp</div>
                        </div>

                        <hr>
                        <h6 class="fw-bold">Giảm giá sản phẩm cùng loại</h6>
                        <div class="alert alert-info small mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Cách tính:</strong> Mỗi sản phẩm được tính độc lập. Sản phẩm đầu tiên tính đủ phí, các sản phẩm tiếp theo được giảm % phí. Công thức: <strong>Phí = F + (q - 1) × F × r</strong> (với F = phí 1 sản phẩm, q = số lượng, r = tỷ lệ tính phí cho sản phẩm tiếp theo). Sau đó cộng tất cả các sản phẩm lại với nhau.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phần trăm giảm giá (%) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="same_product_discount_percent" class="form-control"
                                    value="{{ old('same_product_discount_percent', $settings->same_product_discount_percent ?? 0) }}"
                                    min="0" max="100" step="0.01" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <div class="form-text">
                                <strong>Ví dụ 1:</strong> Sản phẩm A có quantity = 4, phí ban đầu 1 cái = 100,000đ, giảm giá = 50%<br>
                                → r = (100 - 50) / 100 = 0.5<br>
                                → Phí A = 100,000 + (4 - 1) × 100,000 × 0.5 = 100,000 + 150,000 = 250,000đ<br><br>
                                <strong>Ví dụ 2:</strong> Sản phẩm B có quantity = 3, phí ban đầu 1 cái = 60,000đ, giảm giá = 10%<br>
                                → r = (100 - 10) / 100 = 0.9<br>
                                → Phí B = 60,000 + (3 - 1) × 60,000 × 0.9 = 60,000 + 108,000 = 168,000đ<br><br>
                                → <strong>Tổng phí đơn hàng = 250,000 + 168,000 = 418,000đ</strong>
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

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle me-1"></i>Lưu cài đặt phí vận chuyển
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quản lý khoảng cách vận chuyển --}}
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-map me-2"></i>Quản lý khoảng cách vận chuyển
                    </h5>
                    <div>
                        <button type="button" class="btn btn-success btn-sm me-1" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="bi bi-file-earmark-excel me-1"></i>Import Excel
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#distanceModal" onclick="openCreateModal()">
                            <i class="bi bi-plus-circle me-1"></i>Thêm mới
                        </button>
                    </div>
                </div>
                    <div class="card-body">
                        {{-- Bộ lọc --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Lọc theo tỉnh/thành phố:</label>
                                <select id="provinceFilter" class="form-select">
                                    <option value="">-- Tất cả tỉnh/thành phố --</option>
                                    @foreach($provinces ?? [] as $province)
                                        <option value="{{ $province }}">{{ $province }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="button" class="btn btn-secondary w-100" onclick="resetFilters()">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Làm mới
                                </button>
                            </div>
                        </div>

                        {{-- Bảng dữ liệu --}}
                        <div class="table-responsive">
                            <table id="distancesTable" class="table table-striped table-hover align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th width="5%">ID</th>
                                        <th width="25%">Tỉnh/Thành phố</th>
                                        <th width="30%">Quận/Huyện/Thị Xã</th>
                                        <th width="15%">Khoảng cách (Km)</th>
                                        <th width="25%" class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Đang tải...</span>
                                            </div>
                                            <p class="mt-2 text-muted">Đang tải dữ liệu...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div id="paginationContainer" class="mt-3">
                            {{-- Pagination sẽ được load bằng AJAX --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

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
                        <li><strong>Kích thước:</strong> Tính phí theo block cm cho từng chiều (Dài, Rộng, Cao)
                            <ul>
                                <li>Chiều dài: {{ $settings->length_block_cm ?? 200 }}cm đầu = {{ number_format($settings->first_length_price ?? 10000) }}đ, tiếp theo = {{ number_format($settings->next_length_price ?? 5000) }}đ</li>
                                <li>Chiều rộng: {{ $settings->width_block_cm ?? 200 }}cm đầu = {{ number_format($settings->first_width_price ?? 8000) }}đ, tiếp theo = {{ number_format($settings->next_width_price ?? 4000) }}đ</li>
                                <li>Chiều cao: {{ $settings->height_block_cm ?? 200 }}cm đầu = {{ number_format($settings->first_height_price ?? 8000) }}đ, tiếp theo = {{ number_format($settings->next_height_price ?? 4000) }}đ</li>
                            </ul>
                        </li>
                        <li><strong>Cân nặng:</strong> {{ $settings->weight_block_kg ?? 10 }}kg đầu = {{ number_format($settings->first_weight_price ?? 15000) }}đ, tiếp theo = {{ number_format($settings->next_weight_price ?? 7000) }}đ</li>
                        <li><strong>Công thức:</strong> Phí mỗi item = (Phí dài + Phí rộng + Phí cao + Phí cân nặng) × Khoảng cách (km) × Số lượng</li>
                        <li><strong>Phí tối thiểu:</strong> {{ number_format($settings->min_shipping_fee ?? 30000) }}đ</li>
                        @if($settings->same_product_discount_percent > 0)
                        <li><strong>Giảm giá sản phẩm cùng loại</strong>: Giảm {{ number_format($settings->same_product_discount_percent, 2) }}% cho các sản phẩm tiếp theo khi mua nhiều sản phẩm cùng loại (quantity > 1). Công thức: <strong>Phí = F + (q - 1) × F × r</strong> (với F = phí 1 sản phẩm, q = số lượng, r = {{ number_format((100 - $settings->same_product_discount_percent) / 100, 2) }}). Mỗi sản phẩm được tính độc lập, sau đó cộng tất cả lại với nhau.</li>
                        @endif
                        <li><strong>{{ $settings->express_label }}</strong>: Phụ phí {{ $settings->express_surcharge_type === 'percent' ? $settings->express_surcharge_value . '%' : number_format($settings->express_surcharge_value) . 'đ' }} trên phí tiêu chuẩn</li>
                        <li><strong>{{ $settings->fast_label }}</strong>: Phụ phí {{ $settings->fast_surcharge_type === 'percent' ? $settings->fast_surcharge_value . '%' : number_format($settings->fast_surcharge_value) . 'đ' }} trên phí tiêu chuẩn</li>
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

{{-- Modal Thêm mới --}}
<div class="modal fade" id="distanceModal" tabindex="-1" aria-labelledby="distanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="distanceModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Thêm khoảng cách mới
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="distanceForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="modalProvinceSelect" class="form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                        <select id="modalProvinceSelect" class="form-select" required>
                            <option value="">-- Chọn Tỉnh/Thành phố --</option>
                        </select>
                        <input type="hidden" id="provinceName" name="province_name">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="modalDistrictSelect" class="form-label">Quận/Huyện/Thị Xã <span class="text-danger">*</span></label>
                        <select id="modalDistrictSelect" class="form-select" required disabled>
                            <option value="">-- Chọn Quận/Huyện --</option>
                        </select>
                        <input type="hidden" id="districtName" name="district_name">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="distanceKm" class="form-label">Khoảng cách (Km) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="distanceKm" name="distance_km"
                                step="0.01" min="0" required placeholder="0.00">
                            <span class="input-group-text">Km</span>
                        </div>
                        <div class="form-text">Khoảng cách từ Hà Nội - Nam Từ Liêm đến địa chỉ này</div>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Đóng
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Import Excel --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="bi bi-file-earmark-excel me-2"></i>Import Excel Khoảng Cách Vận Chuyển
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="importForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Hướng dẫn:</strong>
                        <ol class="mb-0 mt-2">
                            <li>Tải file Excel mẫu bên dưới</li>
                            <li>Điền thông tin theo đúng cột: <code>tinh_thanh_pho</code>, <code>quan_huyen</code>, <code>khoang_cach_km</code></li>
                            <li>Upload file đã điền thông tin</li>
                        </ol>
                    </div>

                    <div class="mb-3">
                        <a href="{{ route('admin.shipping.distances.template') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-download me-1"></i>Tải file Excel mẫu
                        </a>
                    </div>

                    <div class="mb-3">
                        <label for="excelFile" class="form-label">Chọn file Excel <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="excelFile" name="file" accept=".xlsx,.xls" required>
                        <div class="form-text">Chỉ chấp nhận file .xlsx hoặc .xls, tối đa 2MB</div>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div id="importProgress" class="d-none">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                        </div>
                        <p class="text-center mt-2 mb-0">Đang xử lý...</p>
                    </div>

                    <div id="importResult" class="d-none mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Đóng
                    </button>
                    <button type="submit" class="btn btn-success" id="importBtn">
                        <i class="bi bi-upload me-1"></i>Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<!-- jQuery và SweetAlert2 -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Danh sách tỉnh miền Bắc (Set để tìm kiếm nhanh)
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



    // Đảm bảo các select không bị disabled
    if (citySelect) {
        citySelect.disabled = false;
    }
    if (districtSelect) {
        districtSelect.disabled = true; // Disabled cho đến khi chọn tỉnh
    }
    if (wardSelect) {
        wardSelect.disabled = true; // Disabled cho đến khi chọn quận
    }

    // Sử dụng lại northernProvincesSet, normalizeProvinceName, isNorthernProvince từ global scope
    
    // Load tỉnh/thành phố (Esgoo) - Chỉ hiển thị miền Bắc
    if (citySelect) {
        citySelect.innerHTML = '<option value="">Đang tải dữ liệu...</option>';
        citySelect.disabled = true;
    }

    if (!citySelect) {
        console.error('Không tìm thấy citySelect element');
        return;
    }

    fetch('https://esgoo.net/api-tinhthanh/1/0.htm')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('API Response:', data); // Debug
            
            if (!citySelect) return;
            
            if (data.error === 0 && data.data && Array.isArray(data.data)) {
                citySelect.innerHTML = '<option value="">-- Chọn Tỉnh/Thành phố --</option>';
                
                let addedCount = 0;
                const northernProvinces = [];
                const otherProvinces = [];
                
                data.data.forEach(province => {
                    const provinceName = province.full_name || province.name || province.title || '';
                    if (!provinceName) return;
                    
                    if (isNorthernProvince(provinceName)) {
                        northernProvinces.push(province);
                    } else {
                        otherProvinces.push(province);
                    }
                });
                
                // Thêm tỉnh miền Bắc trước
                northernProvinces.forEach(province => {
                    const provinceName = province.full_name || province.name || province.title || '';
                    const option = document.createElement('option');
                    option.value = province.id || province.code || province.province_id || '';
                    option.textContent = provinceName;
                    option.dataset.name = provinceName;
                    if (provinceName === savedCity || savedCity === provinceName) {
                        option.selected = true;
                        loadDistricts(option.value, savedDistrict, savedWard);
                    }
                    citySelect.appendChild(option);
                    addedCount++;
                });
                
                if (citySelect) {
                    citySelect.disabled = false;
                }
                console.log(`Đã tải ${addedCount} tỉnh/thành phố miền Bắc`);
                
                // Chỉ hiển thị tỉnh miền Bắc, không có fallback
                if (northernProvinces.length === 0 && citySelect) {
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
                            text: 'Hệ thống chỉ hỗ trợ địa chỉ kho hàng tại khu vực miền Bắc.',
                            confirmButtonText: 'Đã hiểu'
                        });
                    }
                }
            } else {
                throw new Error('Dữ liệu API không hợp lệ');
            }
        })
        .catch(error => {
            console.error('Lỗi load tỉnh/thành phố:', error);
            if (citySelect) {
                citySelect.innerHTML = '<option value="">-- Chọn Tỉnh/Thành phố --</option>';
                const errorOption = document.createElement('option');
                errorOption.value = '';
                errorOption.textContent = '⚠ Không thể tải dữ liệu. Vui lòng tải lại trang.';
                errorOption.disabled = true;
                citySelect.appendChild(errorOption);
                citySelect.disabled = false;
            }
        });

    // Khi chọn tỉnh/thành phố
    if (citySelect) {
        citySelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const cityName = selectedOption.dataset.name || selectedOption.textContent || '';
            cityInput.value = cityName;
            districtInput.value = '';
            wardInput.value = '';
            
            // Đảm bảo hidden input có giá trị để validation
            if (!cityName) {
                cityInput.removeAttribute('value');
            } else {
                cityInput.setAttribute('value', cityName);
            }
            
            if (this.value && this.value !== '') {
                loadDistricts(this.value);
            } else {
                districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                districtSelect.disabled = true;
                districtSelect.removeAttribute('required');
                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                wardSelect.disabled = true;
                wardSelect.removeAttribute('required');
            }
        });
    }

    // Khi chọn quận/huyện
    if (districtSelect) {
        districtSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const districtName = selectedOption.dataset.name || selectedOption.textContent || '';
            districtInput.value = districtName;
            wardInput.value = '';
            
            // Đảm bảo hidden input có giá trị để validation
            if (!districtName) {
                districtInput.removeAttribute('value');
            } else {
                districtInput.setAttribute('value', districtName);
            }
            
            if (this.value && this.value !== '') {
                loadWards(this.value);
            } else {
                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                wardSelect.disabled = true;
                wardSelect.removeAttribute('required');
            }
        });
    }

    // Khi chọn phường/xã
    if (wardSelect) {
        wardSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const wardName = selectedOption.dataset.name || selectedOption.textContent || '';
            wardInput.value = wardName;
            
            // Đảm bảo hidden input có giá trị để validation
            if (!wardName) {
                wardInput.removeAttribute('value');
            } else {
                wardInput.setAttribute('value', wardName);
            }
        });
    }
    
    // Custom validation cho form địa chỉ kho hàng
    const originAddressForm = document.getElementById('originAddressForm');
    if (originAddressForm) {
        originAddressForm.addEventListener('submit', function(e) {
            // Kiểm tra hidden inputs trước khi submit
            if (!cityInput || !cityInput.value || cityInput.value.trim() === '') {
                e.preventDefault();
                e.stopPropagation();
                if (citySelect) {
                    citySelect.classList.add('is-invalid');
                }
                alert('Vui lòng chọn Tỉnh/Thành phố');
                return false;
            }

            if (!districtInput || !districtInput.value || districtInput.value.trim() === '') {
                e.preventDefault();
                e.stopPropagation();
                if (districtSelect) {
                    districtSelect.classList.add('is-invalid');
                }
                alert('Vui lòng chọn Quận/Huyện');
                return false;
            }

            if (!wardInput || !wardInput.value || wardInput.value.trim() === '') {
                e.preventDefault();
                e.stopPropagation();
                if (wardSelect) {
                    wardSelect.classList.add('is-invalid');
                }
                alert('Vui lòng chọn Phường/Xã');
                return false;
            }
        });
    }

    function loadDistricts(provinceCode, savedDistrict = null, savedWard = null) {
        if (!provinceCode || !districtSelect) {
            if (districtSelect) {
                districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                districtSelect.disabled = true;
            }
            if (wardSelect) {
                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                wardSelect.disabled = true;
            }
            return;
        }

        districtSelect.innerHTML = '<option value="">Đang tải...</option>';
        districtSelect.disabled = true;
        if (wardSelect) {
            wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
            wardSelect.disabled = true;
        }

        fetch(`https://esgoo.net/api-tinhthanh/2/${provinceCode}.htm`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';

                if (data.error === 0 && data.data && Array.isArray(data.data)) {
                    data.data.forEach(district => {
                        const option = document.createElement('option');
                        option.value = district.id || district.code || '';
                        option.textContent = district.full_name || district.name || '';
                        option.dataset.name = district.full_name || district.name || '';
                        if ((district.full_name || district.name) === savedDistrict) {
                            option.selected = true;
                            districtInput.value = district.full_name || district.name || '';
                            loadWards(option.value, savedWard);
                        }
                        districtSelect.appendChild(option);
                    });
                    districtSelect.disabled = false;
                } else {
                    throw new Error('Dữ liệu API không hợp lệ');
                }
            })
            .catch(error => {
                console.error('Lỗi load quận/huyện:', error);
                districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                districtSelect.disabled = false;
            });
    }

    function loadWards(districtCode, savedWard = null) {
        if (!districtCode || !wardSelect) {
            if (wardSelect) {
                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                wardSelect.disabled = true;
            }
            return;
        }

        wardSelect.innerHTML = '<option value="">Đang tải...</option>';
        wardSelect.disabled = true;

        fetch(`https://esgoo.net/api-tinhthanh/3/${districtCode}.htm`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';

                if (data.error === 0 && data.data && Array.isArray(data.data)) {
                    data.data.forEach(ward => {
                        const option = document.createElement('option');
                        option.value = ward.id || ward.code || '';
                        option.textContent = ward.full_name || ward.name || '';
                        option.dataset.name = ward.full_name || ward.name || '';
                        if ((ward.full_name || ward.name) === savedWard) {
                            option.selected = true;
                            wardInput.value = ward.full_name || ward.name || '';
                        }
                        wardSelect.appendChild(option);
                    });
                    wardSelect.disabled = false;
                } else {
                    throw new Error('Dữ liệu API không hợp lệ');
                }
            })
            .catch(error => {
                console.error('Lỗi load phường/xã:', error);
                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                wardSelect.disabled = false;
            });
    }
});

// ============================================
// SHIPPING DISTANCES CRUD
// ============================================
// Đảm bảo các biến và functions ở global scope để có thể gọi từ onclick
let currentPage = 1;
let currentProvince = '';
let currentSearch = '';

// Đảm bảo jQuery đã load trước khi chạy
if (typeof jQuery === 'undefined') {
    console.error('jQuery chưa được load!');
} else {
    $(document).ready(function() {
        // Khởi tạo DataTable
        initDistancesTable();

        // Event listeners
        $('#provinceFilter').on('change', function() {
            currentProvince = $(this).val();
            currentPage = 1;
            loadDistances();
        });

        // Form submit - Chỉ bind 1 lần
        $('#distanceForm').off('submit').on('submit', function(e) {
            console.log('🔥 Form submit event triggered!');
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            saveDistance();
            return false;
        });
    });
}

function initDistancesTable() {
    loadDistances();
}

function loadDistances() {
    if (typeof jQuery === 'undefined') {
        console.error('jQuery chưa được load!');
        return;
    }

    const params = {
        draw: 1,
        start: (currentPage - 1) * 10,
        length: 10,
        province: currentProvince,
        search: currentSearch,
        'order[0][column]': 0,
        'order[0][dir]': 'asc'
    };

    // Hiển thị loading state
    const tbody = $('#distancesTable tbody');
    if (tbody.length) {
        tbody.html(`
            <tr>
                <td colspan="5" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="mt-2 text-muted">Đang tải dữ liệu...</p>
                </td>
            </tr>
        `);
    }

    $.ajax({
        url: '{{ route("admin.shipping.distances.data") }}',
        method: 'GET',
        data: params,
        success: function(response) {
            if (response && response.data) {
                renderTable(response.data);
                renderPagination(response.recordsFiltered || 0, 10, currentPage);
                
                // Nếu trang hiện tại không còn dữ liệu và không phải trang 1, về trang trước
                if (response.data.length === 0 && currentPage > 1) {
                    currentPage = Math.max(1, currentPage - 1);
                    loadDistances();
                    return;
                }
            } else {
                console.error('Response không hợp lệ:', response);
                renderTable([]);
            }
        },
        error: function(xhr) {
            console.error('Lỗi khi tải dữ liệu:', xhr);
            renderTable([]);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Không thể tải dữ liệu. Vui lòng thử lại.'
                });
            } else {
                alert('Lỗi khi tải dữ liệu. Vui lòng thử lại.');
            }
        }
    });
}

function renderTable(data) {
    if (typeof jQuery === 'undefined') {
        console.error('jQuery chưa được load!');
        return;
    }

    const tbody = $('#distancesTable tbody');
    if (!tbody.length) {
        console.error('Không tìm thấy bảng #distancesTable');
        return;
    }

    tbody.empty();

    if (!data || data.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="5" class="text-center py-4">
                    <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                    <span class="text-muted">Không có dữ liệu</span>
                </td>
            </tr>
        `);
        return;
    }

    data.forEach(function(item) {
        const row = `
            <tr>
                <td>${item.id || ''}</td>
                <td><strong>${item.province_name || ''}</strong></td>
                <td>${item.district_name || ''}</td>
                <td><span class="badge bg-info">${item.distance_km || '0'} km</span></td>
                <td class="text-center">
                    <a href="{{ url('admin/shipping/distances') }}/${item.id}/detail" class="btn btn-sm btn-primary" title="Xem chi tiết">
                        <i class="bi bi-eye me-1"></i>Xem chi tiết
                    </a>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

function renderPagination(total, perPage, current) {
    const totalPages = Math.ceil(total / perPage);
    const container = $('#paginationContainer');
    container.empty();

    if (totalPages <= 1) return;

    let pagination = '<nav><ul class="pagination justify-content-center">';
    
    // Previous
    pagination += `<li class="page-item ${current === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="changePage(${current - 1}); return false;">Trước</a>
    </li>`;

    // Pages
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= current - 2 && i <= current + 2)) {
            pagination += `<li class="page-item ${i === current ? 'active' : ''}">
                <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
            </li>`;
        } else if (i === current - 3 || i === current + 3) {
            pagination += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    // Next
    pagination += `<li class="page-item ${current === totalPages ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="changePage(${current + 1}); return false;">Sau</a>
    </li>`;

    pagination += '</ul></nav>';
    container.html(pagination);
}

// Đảm bảo changePage ở global scope
window.changePage = function(page) {
    if (!page || page < 1) return;
    currentPage = page;
    loadDistances();
    if (typeof jQuery !== 'undefined') {
        $('html, body').animate({ scrollTop: $('#distancesTable').offset().top - 100 }, 300);
    }
};

// Đảm bảo resetFilters ở global scope
window.resetFilters = function() {
    if (typeof jQuery !== 'undefined') {
        $('#provinceFilter').val('');
    }
    currentProvince = '';
    currentSearch = '';
    currentPage = 1;
    loadDistances();
};

// Flag để ngăn auto-submit khi đang load dữ liệu
let isLoadingModalData = false;

// Đảm bảo openCreateModal ở global scope
window.openCreateModal = function() {
    isLoadingModalData = true;

    $('#distanceModalLabel').html('<i class="bi bi-plus-circle me-2"></i>Thêm khoảng cách mới');
    $('#distanceForm')[0].reset();
    $('#provinceName').val('');
    $('#districtName').val('');
    $('#modalProvinceSelect').val('');
    $('#modalDistrictSelect').val('');
    $('#modalDistrictSelect').prop('disabled', true);
    $('#modalDistrictSelect').html('<option value="">-- Chọn Quận/Huyện --</option>');
    $('#distanceForm').removeClass('was-validated');
    $('.invalid-feedback').text('');
    $('.is-invalid').removeClass('is-invalid');

    // Load tỉnh khi mở modal
    loadModalProvinces();

    // Cho phép submit sau 500ms
    setTimeout(function() {
        isLoadingModalData = false;
    }, 500);
}

// Load tỉnh cho modal
function loadModalProvinces() {
    const provinceSelect = document.getElementById('modalProvinceSelect');
    if (!provinceSelect) return;
    
    provinceSelect.innerHTML = '<option value="">Đang tải...</option>';
    provinceSelect.disabled = true;
    
    fetch('https://esgoo.net/api-tinhthanh/1/0.htm')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            provinceSelect.innerHTML = '<option value="">-- Chọn Tỉnh/Thành phố --</option>';
            
            if (data.error === 0 && data.data && Array.isArray(data.data)) {
                data.data.forEach(province => {
                    const provinceName = province.full_name || province.name || province.title || '';
                    if (!provinceName) return;
                    
                    if (isNorthernProvince(provinceName)) {
                        const option = document.createElement('option');
                        option.value = province.id || province.code || '';
                        option.textContent = provinceName;
                        option.dataset.name = provinceName;
                        provinceSelect.appendChild(option);
                    }
                });
            }
            provinceSelect.disabled = false;
        })
        .catch(error => {
            console.error('Lỗi load tỉnh:', error);
            provinceSelect.innerHTML = '<option value="">-- Chọn Tỉnh/Thành phố --</option>';
            provinceSelect.disabled = false;
        });
}

// Load huyện cho modal
function loadModalDistricts(provinceCode, savedDistrict = null) {
    const districtSelect = document.getElementById('modalDistrictSelect');
    if (!districtSelect) return;
    
    if (!provinceCode) {
        districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
        districtSelect.disabled = true;
        $('#districtName').val('');
        return;
    }
    
    districtSelect.innerHTML = '<option value="">Đang tải...</option>';
    districtSelect.disabled = true;
    
    fetch(`https://esgoo.net/api-tinhthanh/2/${provinceCode}.htm`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
            
            if (data.error === 0 && data.data && Array.isArray(data.data)) {
                data.data.forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.id || district.code || '';
                    option.textContent = district.full_name || district.name || '';
                    option.dataset.name = district.full_name || district.name || '';
                    if ((district.full_name || district.name) === savedDistrict) {
                        option.selected = true;
                        $('#districtName').val(district.full_name || district.name || '');
                    }
                    districtSelect.appendChild(option);
                });
            }
            districtSelect.disabled = false;
        })
        .catch(error => {
            console.error('Lỗi load quận/huyện:', error);
            districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
            districtSelect.disabled = false;
        });
}

function saveDistance() {
    console.log('saveDistance được gọi, isLoadingModalData =', isLoadingModalData);

    // Ngăn auto-submit khi đang load dữ liệu
    if (isLoadingModalData) {
        console.log('❌ Đang load dữ liệu, chưa cho phép submit');
        return;
    }

    console.log('✅ Cho phép submit');

    const form = $('#distanceForm')[0];
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        console.log('❌ Form validation failed');
        return;
    }

    // Lấy giá trị từ select dropdown
    const provinceSelect = document.getElementById('modalProvinceSelect');
    const districtSelect = document.getElementById('modalDistrictSelect');
    
    const provinceName = provinceSelect.options[provinceSelect.selectedIndex]?.dataset.name || 
                         provinceSelect.options[provinceSelect.selectedIndex]?.textContent || '';
    const districtName = districtSelect.options[districtSelect.selectedIndex]?.dataset.name || 
                        districtSelect.options[districtSelect.selectedIndex]?.textContent || '';

    if (!provinceName || !districtName) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Cảnh báo',
                text: 'Vui lòng chọn đầy đủ tỉnh/thành phố và quận/huyện.'
            });
        }
        return;
    }

    const formData = {
        province_name: provinceName,
        district_name: districtName,
        distance_km: parseFloat($('#distanceKm').val()),
    };

    // Chỉ dùng cho Thêm mới (POST)
    const url = '{{ route("admin.shipping.distances.store") }}';
    const method = 'POST';

    $.ajax({
        url: url,
        method: method,
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#distanceModal').modal('hide');
                
                // Reset về trang 1 và reload dữ liệu ngay lập tức
                currentPage = 1;
                loadDistances();
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            } else {
                // Xử lý trường hợp response.success = false
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: response.message || 'Có lỗi xảy ra. Vui lòng thử lại.'
                    });
                }
            }
        },
        error: function(xhr) {
            const errors = xhr.responseJSON?.errors || {};
            let errorMessage = xhr.responseJSON?.message || 'Có lỗi xảy ra. Vui lòng thử lại.';
            
            // Hiển thị lỗi validation
            Object.keys(errors).forEach(function(key) {
                let input;
                if (key === 'province_name') {
                    input = $('#modalProvinceSelect');
                } else if (key === 'district_name') {
                    input = $('#modalDistrictSelect');
                } else {
                    input = $(`[name="${key}"]`);
                }
                
                if (input.length) {
                    input.addClass('is-invalid');
                    const feedback = input.siblings('.invalid-feedback');
                    if (feedback.length) {
                        feedback.text(Array.isArray(errors[key]) ? errors[key][0] : errors[key]);
                    } else {
                        // Tạo invalid-feedback nếu chưa có
                        input.after('<div class="invalid-feedback">' + (Array.isArray(errors[key]) ? errors[key][0] : errors[key]) + '</div>');
                    }
                }
            });

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: errorMessage
                });
            }
        }
    });
}

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Reset form validation khi đóng modal
if (typeof jQuery !== 'undefined') {
    $(document).ready(function() {
        $('#distanceModal').on('hidden.bs.modal', function() {
            $('#distanceForm').removeClass('was-validated');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            // Xóa các invalid-feedback được tạo động
            $('.invalid-feedback').filter(function() {
                return !$(this).prev().hasClass('form-control') && !$(this).prev().hasClass('form-select');
            }).remove();
        });
        
        // Xử lý khi chọn tỉnh trong modal - Unbind trước để tránh duplicate
        $('#modalProvinceSelect').off('change').on('change', function(e) {
            e.stopPropagation();
            const selectedOption = this.options[this.selectedIndex];
            const provinceName = selectedOption.dataset.name || selectedOption.textContent || '';
            $('#provinceName').val(provinceName);

            if (this.value) {
                loadModalDistricts(this.value);
            } else {
                $('#modalDistrictSelect').html('<option value="">-- Chọn Quận/Huyện --</option>');
                $('#modalDistrictSelect').prop('disabled', true);
                $('#districtName').val('');
            }
            return false;
        });

        // Xử lý khi chọn huyện trong modal - Unbind trước để tránh duplicate
        $('#modalDistrictSelect').off('change').on('change', function(e) {
            e.stopPropagation();
            const selectedOption = this.options[this.selectedIndex];
            const districtName = selectedOption.dataset.name || selectedOption.textContent || '';
            $('#districtName').val(districtName);
            return false;
        });

        // Xử lý Import Excel
        $('#importForm').on('submit', function(e) {
            e.preventDefault();

            const fileInput = $('#excelFile')[0];
            if (!fileInput.files || !fileInput.files[0]) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Cảnh báo',
                    text: 'Vui lòng chọn file Excel'
                });
                return;
            }

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            formData.append('_token', '{{ csrf_token() }}');

            // Hiển thị progress
            $('#importProgress').removeClass('d-none');
            $('#importResult').addClass('d-none');
            $('#importBtn').prop('disabled', true);

            $.ajax({
                url: '{{ route("admin.shipping.distances.import") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#importProgress').addClass('d-none');
                    $('#importBtn').prop('disabled', false);

                    if (response.success) {
                        // Hiển thị kết quả thành công
                        let resultHtml = `
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle me-2"></i>
                                <strong>Import thành công!</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Thêm mới: ${response.data.success_count} bản ghi</li>
                                    <li>Cập nhật: ${response.data.update_count} bản ghi</li>
                                </ul>
                            </div>
                        `;
                        $('#importResult').html(resultHtml).removeClass('d-none');

                        // Reload table sau 2 giây
                        setTimeout(function() {
                            loadDistances();
                            $('#importModal').modal('hide');
                            $('#importForm')[0].reset();
                            $('#importResult').addClass('d-none');
                        }, 2000);

                    } else {
                        // Hiển thị lỗi
                        let errorHtml = `
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Import hoàn tất với lỗi:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Thành công: ${response.data.success_count} bản ghi</li>
                                    <li>Cập nhật: ${response.data.update_count} bản ghi</li>
                                    <li>Lỗi: ${response.data.failure_count} bản ghi</li>
                                </ul>
                        `;

                        if (response.data.errors && response.data.errors.length > 0) {
                            errorHtml += '<hr><strong>Chi tiết lỗi:</strong><ul class="small">';
                            response.data.errors.slice(0, 5).forEach(function(error) {
                                errorHtml += `<li>Dòng ${error.row}: ${error.errors.join(', ')}</li>`;
                            });
                            if (response.data.errors.length > 5) {
                                errorHtml += `<li>... và ${response.data.errors.length - 5} lỗi khác</li>`;
                            }
                            errorHtml += '</ul>';
                        }

                        errorHtml += '</div>';
                        $('#importResult').html(errorHtml).removeClass('d-none');

                        // Reload table nếu có bản ghi thành công
                        if (response.data.success_count > 0 || response.data.update_count > 0) {
                            loadDistances();
                        }
                    }
                },
                error: function(xhr) {
                    $('#importProgress').addClass('d-none');
                    $('#importBtn').prop('disabled', false);

                    let errorMessage = 'Có lỗi xảy ra khi import';
                    let errorDetails = '';

                    if (xhr.responseJSON) {
                        errorMessage = xhr.responseJSON.message || errorMessage;

                        if (xhr.responseJSON.data && xhr.responseJSON.data.errors) {
                            errorDetails = '<ul class="small mt-2 mb-0">';
                            xhr.responseJSON.data.errors.slice(0, 5).forEach(function(error) {
                                errorDetails += `<li>Dòng ${error.row}: ${error.errors.join(', ')}</li>`;
                            });
                            errorDetails += '</ul>';
                        }
                    }

                    let errorHtml = `
                        <div class="alert alert-danger">
                            <i class="bi bi-x-circle me-2"></i>
                            <strong>${errorMessage}</strong>
                            ${errorDetails}
                        </div>
                    `;
                    $('#importResult').html(errorHtml).removeClass('d-none');
                }
            });
        });

        // Reset import modal khi đóng
        $('#importModal').on('hidden.bs.modal', function() {
            $('#importForm')[0].reset();
            $('#importResult').addClass('d-none');
            $('#importProgress').addClass('d-none');
            $('#importBtn').prop('disabled', false);
        });
    });
}
</script>
@endpush
@endsection