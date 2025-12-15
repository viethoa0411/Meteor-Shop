@extends('admin.layouts.app')

@section('title', 'Chi tiết khoảng cách vận chuyển')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="bi bi-geo-alt me-2"></i>Chi tiết khoảng cách vận chuyển
                </h2>
                <a href="{{ route('admin.shipping.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Quay lại
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>Thông tin khoảng cách
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.shipping.distances.update-detail', $distance->id) }}" method="POST" id="detailForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label fw-bold">ID</label>
                            <input type="text" class="form-control" value="{{ $distance->id }}" disabled>
                        </div>

                        <div class="mb-4">
                            <label for="province_name" class="form-label fw-bold">
                                Tỉnh/Thành phố <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('province_name') is-invalid @enderror" 
                                id="province_name" name="province_name" 
                                value="{{ old('province_name', $distance->province_name) }}" 
                                required>
                            @error('province_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Tên tỉnh/thành phố (VD: Hà Nội, Hải Phòng)</div>
                        </div>

                        <div class="mb-4">
                            <label for="district_name" class="form-label fw-bold">
                                Quận/Huyện/Thị xã <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('district_name') is-invalid @enderror" 
                                id="district_name" name="district_name" 
                                value="{{ old('district_name', $distance->district_name) }}" 
                                required>
                            @error('district_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Tên quận/huyện/thị xã (VD: Quận Ba Đình, Huyện Gia Lâm)</div>
                        </div>

                        <div class="mb-4">
                            <label for="distance_km" class="form-label fw-bold">
                                Khoảng cách (km) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('distance_km') is-invalid @enderror" 
                                    id="distance_km" name="distance_km" 
                                    value="{{ old('distance_km', $distance->distance_km) }}" 
                                    step="0.01" min="0" required>
                                <span class="input-group-text">km</span>
                                @error('distance_km')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">Khoảng cách từ Hà Nội - Nam Từ Liêm đến địa chỉ này</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Ngày tạo</label>
                            <input type="text" class="form-control" value="{{ $distance->created_at->format('d/m/Y H:i:s') }}" disabled>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Ngày cập nhật</label>
                            <input type="text" class="form-control" value="{{ $distance->updated_at->format('d/m/Y H:i:s') }}" disabled>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Lưu thay đổi
                            </button>
                            <!-- <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                <i class="bi bi-trash me-1"></i>Xóa
                            </button> -->
                            <a href="{{ route('admin.shipping.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i>Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-lightbulb me-2"></i>Hướng dẫn
                    </h5>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold">Cách sử dụng:</h6>
                    <ol class="small">
                        <li>Xem thông tin chi tiết khoảng cách vận chuyển</li>
                        <li>Sửa trực tiếp các trường cần thay đổi</li>
                        <li>Nhấn "Lưu thay đổi" để cập nhật</li>
                        <li>Nhấn "Xóa" để xóa bản ghi này</li>
                    </ol>

                    <hr>

                    <h6 class="fw-bold">Lưu ý:</h6>
                    <ul class="small mb-0">
                        <li>Tỉnh/Thành phố và Quận/Huyện không được trùng lặp</li>
                        <li>Khoảng cách phải là số dương</li>
                        <li>Xóa bản ghi sẽ không thể khôi phục</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Form xóa ẩn --}}
<form id="deleteForm" action="{{ route('admin.shipping.distances.destroy', $distance->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function confirmDelete() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Xác nhận xóa',
            text: 'Bạn có chắc chắn muốn xóa khoảng cách này? Hành động này không thể hoàn tác!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteForm').submit();
            }
        });
    } else {
        if (confirm('Bạn có chắc chắn muốn xóa khoảng cách này?')) {
            document.getElementById('deleteForm').submit();
        }
    }
}
</script>
@endpush
@endsection

