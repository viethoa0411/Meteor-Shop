@extends('admin.layouts.app')
@section('title', 'Cài đặt bình luận')

@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h3 class="fw-bold text-primary mb-0">
                <i class="bi bi-gear me-2"></i>Cài đặt bình luận
            </h3>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.comments.settings.save') }}">
        @csrf
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">Cài đặt chung</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="auto_approve" value="1"
                               id="auto_approve" {{ $settings['auto_approve'] ? 'checked' : '' }}>
                        <label class="form-check-label" for="auto_approve">
                            Tự động phê duyệt bình luận
                        </label>
                        <small class="d-block text-muted">Bình luận sẽ được tự động phê duyệt khi người dùng gửi</small>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="auto_verify_buyer" value="1"
                               id="auto_verify_buyer" {{ $settings['auto_verify_buyer'] ? 'checked' : '' }}>
                        <label class="form-check-label" for="auto_verify_buyer">
                            Tự động gắn nhãn "Đã mua hàng"
                        </label>
                        <small class="d-block text-muted">Tự động xác minh người dùng đã mua sản phẩm</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">Hình ảnh & Video</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="allow_images" value="1"
                               id="allow_images" {{ $settings['allow_images'] ? 'checked' : '' }}>
                        <label class="form-check-label" for="allow_images">
                            Cho phép đính kèm hình ảnh
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Số lượng ảnh tối đa</label>
                    <input type="number" name="max_images" class="form-control" 
                           value="{{ $settings['max_images'] }}" min="1" max="10">
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">Lọc nội dung</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="auto_hide_blacklist" value="1"
                               id="auto_hide_blacklist" {{ $settings['auto_hide_blacklist'] ? 'checked' : '' }}>
                        <label class="form-check-label" for="auto_hide_blacklist">
                            Tự động ẩn bình luận chứa từ nhạy cảm
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Danh sách từ cấm (mỗi dòng một từ)</label>
                    <textarea name="blacklist_keywords" class="form-control" rows="10" 
                              placeholder="spam&#10;xúc phạm&#10;quảng cáo">{{ $settings['blacklist_keywords'] ?? '' }}</textarea>
                    <small class="text-muted">Nhập các từ khóa cần chặn, mỗi từ một dòng</small>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.comments.index') }}" class="btn btn-secondary">Hủy</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Lưu cài đặt
            </button>
        </div>
    </form>
</div>
@endsection

