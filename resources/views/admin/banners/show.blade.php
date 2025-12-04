@extends('admin.layouts.app')
@section('title', 'Chi tiết Banner')

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="bi bi-eye me-2"></i>Chi tiết Banner
                </h4>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Sửa
                    </a>
                    <a href="{{ route('admin.banners.list') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Cột trái: Thông tin --}}
                    <div class="col-md-8">
                        <table class="table table-bordered">
                            <tr>
                                <th width="200">ID</th>
                                <td>{{ $banner->id }}</td>
                            </tr>
                            <tr>
                                <th>Tiêu đề</th>
                                <td><strong>{{ $banner->title }}</strong></td>
                            </tr>
                            <tr>
                                <th>Mô tả</th>
                                <td>{{ $banner->description ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th>Link liên kết</th>
                                <td>
                                    @if ($banner->link)
                                        <a href="{{ $banner->link }}" target="_blank" class="text-decoration-none">
                                            <i class="bi bi-link-45deg"></i> {{ $banner->link }}
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Thứ tự</th>
                                <td><span class="badge bg-secondary">{{ $banner->sort_order }}</span></td>
                            </tr>
                            <tr>
                                <th>Trạng thái</th>
                                <td>
                                    @if ($banner->status == 'active')
                                        <span class="badge bg-success">Hoạt động</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Tạm ẩn</span>
                                    @endif
                                    @if ($banner->isActive())
                                        <span class="badge bg-primary ms-2">Đang hiển thị</span>
                                    @else
                                        <span class="badge bg-secondary ms-2">Không hiển thị</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Ngày bắt đầu</th>
                                <td>
                                    {{ $banner->start_date ? $banner->start_date->format('d/m/Y H:i') : '—' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Ngày kết thúc</th>
                                <td>
                                    {{ $banner->end_date ? $banner->end_date->format('d/m/Y H:i') : '—' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Ngày tạo</th>
                                <td>{{ $banner->created_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>Ngày cập nhật</th>
                                <td>{{ $banner->updated_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>

                    {{-- Cột phải: Ảnh --}}
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Hình ảnh Banner</h5>
                            </div>
                            <div class="card-body text-center">
                                @if ($banner->image)
                                    <img src="{{ asset('storage/' . ltrim($banner->image, '/')) }}" alt="{{ $banner->title }}"
                                        class="img-fluid rounded shadow-sm" style="max-width: 100%;"
                                        onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'bg-light p-5 rounded\'><i class=\'bi bi-image text-muted\' style=\'font-size: 3rem;\'></i><p class=\'text-muted mt-2\'>Ảnh không tồn tại</p></div>';">
                                @else
                                    <div class="bg-light p-5 rounded">
                                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                        <p class="text-muted mt-2">Không có ảnh</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Nút hành động --}}
                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                    <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa banner này?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Xóa banner
                        </button>
                    </form>
                    <div class="d-flex gap-2">
                        <form action="{{ route('admin.banners.duplicate', $banner->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Tạo bản sao banner này?');">
                            @csrf
                            <button type="submit" class="btn btn-info">
                                <i class="bi bi-files"></i> Nhân đôi
                            </button>
                        </form>
                        <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Chỉnh sửa
                        </a>
                        <a href="{{ route('admin.banners.list') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại danh sách
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

