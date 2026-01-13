@extends('admin.layouts.app')

@section('title', 'Cấu hình liên hệ nhanh')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-gradient-primary text-white">
            <h5 class="mb-0">
                <i class="bi bi-telephone-forward-fill me-2"></i>
                Cấu hình liên hệ nhanh (Zalo, Messenger, SĐT)
            </h5>
        </div>
        <div class="card-body p-5">
            <form action="{{ route('admin.contact-info.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <!-- Zalo -->
                    <div class="col-lg-8">
                        <label class="form-label fw-bold text-primary">
                            <i class="bi bi-chat-dots-fill me-2"></i>Link Zalo (zalo.me/số hoặc Zalo OA)
                        </label>
                        <input type="url" name="zalo_link" class="form-control form-control-lg" 
                               value="{{ old('zalo_link', $contact->zalo_link) }}" 
                               placeholder="https://zalo.me/0123456789">
                        <small class="text-muted">Ví dụ: https://zalo.me/0123456789 hoặc link Zalo OA</small>
                    </div>
                    <div class="col-lg-4 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="show_zalo" value="1" 
                                   id="showZalo" {{ $contact->show_zalo ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="showZalo">
                                Hiển thị nút Zalo
                            </label>
                        </div>
                    </div>

                    <!-- Messenger -->
                    <div class="col-lg-8">
                        <label class="form-label fw-bold text-info">
                            <i class="bi bi-messenger me-2"></i>Link Messenger (m.me/fanpage)
                        </label>
                        <input type="url" name="messenger_link" class="form-control form-control-lg" 
                               value="{{ old('messenger_link', $contact->messenger_link) }}" 
                               placeholder="https://m.me/meteorfanpage">
                        <small class="text-muted">Ví dụ: https://m.me/meteorfanpage</small>
                    </div>
                    <div class="col-lg-4 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="show_messenger" value="1" 
                                   id="showMessenger" {{ $contact->show_messenger ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="showMessenger">
                                Hiển thị nút Messenger
                            </label>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="col-lg-8">
                        <label class="form-label fw-bold text-success">
                            <i class="bi bi-telephone-fill me-2"></i>Số điện thoại gọi trực tiếp
                        </label>
                        <input type="text" name="phone_number" class="form-control form-control-lg" 
                               value="{{ old('phone_number', $contact->phone_number) }}" 
                               placeholder="0123456789">
                        <small class="text-muted">Khách click sẽ gọi ngay</small>
                    </div>
                    <div class="col-lg-4 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="show_phone" value="1" 
                                   id="showPhone" {{ $contact->show_phone ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="showPhone">
                                Hiển thị nút gọi điện
                            </label>
                        </div>
                    </div>
                </div>

                <hr class="my-5">

                <div class="text-end">
                    <button type="submit" class="btn btn-primary btn-lg px-4 shadow">
                        <i class="bi bi-save-fill me-2"></i>
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
