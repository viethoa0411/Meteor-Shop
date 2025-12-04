@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="bi bi-gear-fill me-2"></i>Cài đặt Chatbox</h4>
            <p class="text-muted mb-0">Tùy chỉnh giao diện và tính năng chatbox</p>
        </div>
        <a href="{{ route('admin.chatbox.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- General Settings -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-sliders me-2"></i>Cài đặt chung</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.chatbox.settings.update') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_enabled" id="is_enabled" 
                                       {{ $settings->is_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_enabled">Bật Chatbox</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tiêu đề chatbox</label>
                            <input type="text" name="chatbox_title" class="form-control" 
                                   value="{{ $settings->chatbox_title }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phụ đề</label>
                            <input type="text" name="chatbox_subtitle" class="form-control" 
                                   value="{{ $settings->chatbox_subtitle }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tin nhắn chào mừng</label>
                            <textarea name="welcome_message" class="form-control" rows="3" required>{{ $settings->welcome_message }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tin nhắn ngoài giờ làm việc</label>
                            <textarea name="offline_message" class="form-control" rows="3" required>{{ $settings->offline_message }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Màu chính</label>
                                <input type="color" name="primary_color" class="form-control form-control-color w-100" 
                                       value="{{ $settings->primary_color }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Màu phụ</label>
                                <input type="color" name="secondary_color" class="form-control form-control-color w-100" 
                                       value="{{ $settings->secondary_color }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="show_on_mobile" id="show_on_mobile" 
                                           {{ $settings->show_on_mobile ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_on_mobile">Hiển thị trên mobile</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="play_sound" id="play_sound" 
                                           {{ $settings->play_sound ? 'checked' : '' }}>
                                    <label class="form-check-label" for="play_sound">Phát âm thanh</label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Lưu cài đặt
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quick Replies -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Câu trả lời nhanh</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.chatbox.quick-replies.update') }}" method="POST">
                        @csrf
                        <div id="quickRepliesContainer">
                            @forelse($settings->quick_replies ?? [] as $index => $qr)
                                <div class="quick-reply-item border rounded p-3 mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <strong>Câu trả lời #{{ $index + 1 }}</strong>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-qr">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    <div class="mb-2">
                                        <input type="text" name="quick_replies[{{ $index }}][text]" class="form-control form-control-sm" 
                                               placeholder="Nút hiển thị (VD: Tư vấn sản phẩm)" value="{{ $qr['text'] ?? '' }}">
                                    </div>
                                    <div>
                                        <textarea name="quick_replies[{{ $index }}][message]" class="form-control form-control-sm" rows="2" 
                                                  placeholder="Tin nhắn gửi đi">{{ $qr['message'] ?? '' }}</textarea>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">Chưa có câu trả lời nhanh nào</p>
                            @endforelse
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="addQuickReply">
                            <i class="bi bi-plus-lg me-1"></i> Thêm câu trả lời
                        </button>
                        <br>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Lưu câu trả lời nhanh
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Auto Replies -->
        <div class="col-md-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-robot me-2"></i>Tự động trả lời (Bot)</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Thiết lập các từ khóa để bot tự động trả lời khách hàng</p>
                    <form action="{{ route('admin.chatbox.auto-replies.update') }}" method="POST">
                        @csrf
                        <div id="autoRepliesContainer">
                            @forelse($settings->auto_replies ?? [] as $index => $ar)
                                <div class="auto-reply-item border rounded p-3 mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <strong>Quy tắc #{{ $index + 1 }}</strong>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-ar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small">Từ khóa (cách nhau bởi dấu phẩy)</label>
                                            <input type="text" name="auto_replies[{{ $index }}][keywords]" class="form-control" 
                                                   placeholder="giá, bao nhiêu, price" 
                                                   value="{{ is_array($ar['keywords'] ?? null) ? implode(', ', $ar['keywords']) : '' }}">
                                        </div>
                                        <div class="col-md-8 mb-2">
                                            <label class="form-label small">Câu trả lời</label>
                                            <textarea name="auto_replies[{{ $index }}][reply]" class="form-control" rows="2" 
                                                      placeholder="Tin nhắn bot sẽ gửi khi khách hàng nhắn chứa từ khóa">{{ $ar['reply'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">Chưa có quy tắc tự động trả lời nào</p>
                            @endforelse
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="addAutoReply">
                            <i class="bi bi-plus-lg me-1"></i> Thêm quy tắc
                        </button>
                        <br>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Lưu quy tắc tự động
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let qrIndex = {{ count($settings->quick_replies ?? []) }};
    let arIndex = {{ count($settings->auto_replies ?? []) }};

    // Add Quick Reply
    document.getElementById('addQuickReply').addEventListener('click', function() {
        const container = document.getElementById('quickRepliesContainer');
        const html = `
            <div class="quick-reply-item border rounded p-3 mb-3">
                <div class="d-flex justify-content-between mb-2">
                    <strong>Câu trả lời #${qrIndex + 1}</strong>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-qr">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <div class="mb-2">
                    <input type="text" name="quick_replies[${qrIndex}][text]" class="form-control form-control-sm"
                           placeholder="Nút hiển thị (VD: Tư vấn sản phẩm)">
                </div>
                <div>
                    <textarea name="quick_replies[${qrIndex}][message]" class="form-control form-control-sm" rows="2"
                              placeholder="Tin nhắn gửi đi"></textarea>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        qrIndex++;
    });

    // Add Auto Reply
    document.getElementById('addAutoReply').addEventListener('click', function() {
        const container = document.getElementById('autoRepliesContainer');
        const html = `
            <div class="auto-reply-item border rounded p-3 mb-3">
                <div class="d-flex justify-content-between mb-2">
                    <strong>Quy tắc #${arIndex + 1}</strong>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-ar">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label small">Từ khóa (cách nhau bởi dấu phẩy)</label>
                        <input type="text" name="auto_replies[${arIndex}][keywords]" class="form-control"
                               placeholder="giá, bao nhiêu, price">
                    </div>
                    <div class="col-md-8 mb-2">
                        <label class="form-label small">Câu trả lời</label>
                        <textarea name="auto_replies[${arIndex}][reply]" class="form-control" rows="2"
                                  placeholder="Tin nhắn bot sẽ gửi khi khách hàng nhắn chứa từ khóa"></textarea>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        arIndex++;
    });

    // Remove Quick Reply
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-qr')) {
            e.target.closest('.quick-reply-item').remove();
        }
        if (e.target.closest('.remove-ar')) {
            e.target.closest('.auto-reply-item').remove();
        }
    });
});
</script>
@endsection

