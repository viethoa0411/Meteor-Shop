@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row" style="height: calc(100vh - 200px);">
        <!-- Chat Area -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100 d-flex flex-column">
                <!-- Header -->
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('admin.chatbox.index') }}" class="btn btn-sm btn-outline-secondary me-3">
                                <i class="bi bi-arrow-left"></i>
                            </a>
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" 
                                 style="width: 45px; height: 45px;">
                                {{ strtoupper(substr($session->customer_name, 0, 1)) }}
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $session->customer_name }}</h6>
                                <small class="text-muted">
                                    {{ $session->customer_email ?? 'Khách' }}
                                    <span class="badge bg-{{ $session->status == 'active' ? 'success' : 'secondary' }} ms-1">
                                        {{ $session->status == 'active' ? 'Đang mở' : 'Đã đóng' }}
                                    </span>
                                </small>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if($session->status == 'active')
                                <li>
                                    <form action="{{ route('admin.chatbox.close', $session->id) }}" method="POST">
                                        @csrf
                                        <button class="dropdown-item"><i class="bi bi-x-circle me-2"></i>Đóng hội thoại</button>
                                    </form>
                                </li>
                                @endif
                                <li>
                                    <form action="{{ route('admin.chatbox.delete', $session->id) }}" method="POST" 
                                          onsubmit="return confirm('Bạn có chắc muốn xóa cuộc hội thoại này?')">
                                        @csrf @method('DELETE')
                                        <button class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Xóa</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <div class="card-body flex-grow-1 overflow-auto p-3" id="chatMessages" style="background: #f5f7fb;">
                    @foreach($session->messages as $message)
                        <div class="d-flex mb-3 {{ $message->sender_type == 'client' ? '' : 'justify-content-end' }}">
                            @if($message->sender_type == 'client')
                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center flex-shrink-0 me-2"
                                     style="width: 32px; height: 32px; font-size: 12px;">
                                    {{ strtoupper(substr($session->customer_name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="chat-bubble {{ $message->sender_type == 'client' ? 'chat-bubble--received' : 'chat-bubble--sent' }}"
                                 style="max-width: 70%;">
                                <div class="p-2 px-3 rounded-3 {{ $message->sender_type == 'client' ? 'bg-white' : ($message->sender_type == 'bot' ? 'bg-info text-white' : 'bg-primary text-white') }}">
                                    @if($message->sender_type != 'client')
                                        <small class="d-block mb-1 {{ $message->sender_type == 'bot' ? 'text-white-50' : 'text-white-50' }}">
                                            {{ $message->sender_type == 'bot' ? '🤖 Bot' : $message->sender->name ?? 'Admin' }}
                                        </small>
                                    @endif
                                    @if($message->message_type == 'image' && $message->attachment_url)
                                        <a href="{{ $message->attachment_url }}" target="_blank" data-bs-toggle="modal" data-bs-target="#imageModal" data-image="{{ $message->attachment_url }}">
                                            <img src="{{ $message->attachment_url }}" class="img-fluid rounded mb-2" style="max-width: 200px; max-height: 200px; cursor: pointer;" alt="Image">
                                        </a>
                                        @if($message->message && $message->message != '[Hình ảnh]')
                                            <p class="mb-0" style="white-space: pre-wrap;">{!! nl2br(e($message->message)) !!}</p>
                                        @endif
                                    @else
                                        <p class="mb-0" style="white-space: pre-wrap;">{!! nl2br(e($message->message)) !!}</p>
                                    @endif
                                    <small class="d-block mt-1 {{ $message->sender_type == 'client' ? 'text-muted' : 'text-white-50' }}">
                                        {{ $message->created_at->format('H:i') }}
                                        @if($message->sender_type != 'client' && $message->is_read)
                                            <i class="bi bi-check2-all ms-1"></i>
                                        @endif
                                    </small>
                                </div>
                            </div>
                            @if($message->sender_type != 'client')
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0 ms-2"
                                     style="width: 32px; height: 32px; font-size: 12px;">
                                    <i class="bi bi-headset"></i>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Input -->
                @if($session->status == 'active')
                <div class="card-footer bg-white border-top p-3">
                    <!-- Image Preview -->
                    <div id="imagePreviewContainer" class="mb-2" style="display: none;">
                        <div class="position-relative d-inline-block">
                            <img src="" id="imagePreview" class="rounded" style="max-width: 150px; max-height: 100px;">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" id="removeImage" style="transform: translate(50%, -50%);">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                    <form action="{{ route('admin.chatbox.send', $session->id) }}" method="POST" id="chatForm" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="image" id="imageInput" accept="image/*" style="display: none;">
                        <div class="input-group">
                            <button type="button" class="btn btn-outline-secondary" id="attachBtn" title="Gửi hình ảnh">
                                <i class="bi bi-image"></i>
                            </button>
                            <textarea name="message" class="form-control" rows="1" placeholder="Nhập tin nhắn..."
                                      id="messageInput" style="resize: none;"></textarea>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </div>
                    </form>
                </div>
                @else
                <div class="card-footer bg-light text-center py-3">
                    <span class="text-muted"><i class="bi bi-lock me-1"></i>Cuộc hội thoại đã đóng</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Customer Info -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="bi bi-person-circle me-2"></i>Thông tin khách hàng</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small">Tên</label>
                        <p class="mb-0 fw-semibold">{{ $session->customer_name }}</p>
                    </div>
                    @if($session->customer_email)
                    <div class="mb-3">
                        <label class="form-label text-muted small">Email</label>
                        <p class="mb-0">{{ $session->customer_email }}</p>
                    </div>
                    @endif
                    @if($session->guest_phone)
                    <div class="mb-3">
                        <label class="form-label text-muted small">Số điện thoại</label>
                        <p class="mb-0">{{ $session->guest_phone }}</p>
                    </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label text-muted small">IP Address</label>
                        <p class="mb-0"><code>{{ $session->ip_address }}</code></p>
                    </div>
                    @if($session->page_url)
                    <div class="mb-3">
                        <label class="form-label text-muted small">Trang đang xem</label>
                        <p class="mb-0 text-truncate"><a href="{{ $session->page_url }}" target="_blank">{{ $session->page_url }}</a></p>
                    </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label text-muted small">Bắt đầu chat</label>
                        <p class="mb-0">{{ $session->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label text-muted small">Số tin nhắn</label>
                        <p class="mb-0">{{ $session->messages->count() }} tin</p>
                    </div>
                </div>
            </div>

            <!-- Quick Replies -->
            @if(!empty($settings->quick_replies))
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Trả lời nhanh</h6>
                </div>
                <div class="card-body p-2">
                    @foreach($settings->quick_replies as $qr)
                        <button type="button" class="btn btn-sm btn-outline-primary m-1 quick-reply-btn"
                                data-message="{{ $qr['message'] ?? $qr['text'] }}">
                            {{ $qr['text'] }}
                        </button>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
#chatMessages {
    scroll-behavior: smooth;
}
.chat-bubble--received .rounded-3 {
    border-bottom-left-radius: 4px !important;
}
.chat-bubble--sent .rounded-3 {
    border-bottom-right-radius: 4px !important;
}
body.dark #chatMessages {
    background: #1a1a1a !important;
}
body.dark .bg-white {
    background-color: #2a2a2a !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll to bottom
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // Auto-resize textarea
    const messageInput = document.getElementById('messageInput');
    const imageInput = document.getElementById('imageInput');
    const attachBtn = document.getElementById('attachBtn');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const imagePreview = document.getElementById('imagePreview');
    const removeImage = document.getElementById('removeImage');
    const chatForm = document.getElementById('chatForm');

    if (messageInput) {
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });

        // Submit on Enter (Shift+Enter for new line)
        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                chatForm.submit();
            }
        });
    }

    // Image upload handling
    if (attachBtn && imageInput) {
        attachBtn.addEventListener('click', () => imageInput.click());

        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    alert('Hình ảnh không được vượt quá 5MB');
                    imageInput.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreviewContainer.style.display = 'block';
                    messageInput.removeAttribute('required');
                };
                reader.readAsDataURL(file);
            }
        });

        removeImage.addEventListener('click', function() {
            imageInput.value = '';
            imagePreviewContainer.style.display = 'none';
            if (!messageInput.value.trim()) {
                messageInput.setAttribute('required', 'required');
            }
        });
    }

    // Quick reply buttons
    document.querySelectorAll('.quick-reply-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const message = this.dataset.message;
            if (messageInput) {
                messageInput.value = message;
                messageInput.focus();
            }
        });
    });

    // Image modal for viewing full size
    document.querySelectorAll('[data-image]').forEach(img => {
        img.addEventListener('click', function(e) {
            e.preventDefault();
            const imageUrl = this.dataset.image;
            const modal = document.createElement('div');
            modal.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center';
            modal.style.cssText = 'background: rgba(0,0,0,0.9); z-index: 9999;';
            modal.innerHTML = `
                <button class="btn btn-light position-absolute top-0 end-0 m-3" onclick="this.parentElement.remove()">
                    <i class="bi bi-x-lg"></i>
                </button>
                <img src="${imageUrl}" style="max-width: 90%; max-height: 90%; object-fit: contain;">
            `;
            modal.addEventListener('click', function(e) {
                if (e.target === modal) modal.remove();
            });
            document.body.appendChild(modal);
        });
    });

    // Polling for new messages
    let lastMessageId = {{ $session->messages->last()?->id ?? 0 }};
    setInterval(function() {
        fetch('{{ route("admin.chatbox.messages", $session->id) }}?last_id=' + lastMessageId)
            .then(res => res.json())
            .then(data => {
                if (data.messages && data.messages.length > 0) {
                    location.reload();
                }
            });
    }, 5000);
});
</script>
@endsection@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row" style="height: calc(100vh - 200px);">
        <!-- Chat Area -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100 d-flex flex-column">
                <!-- Header -->
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('admin.chatbox.index') }}" class="btn btn-sm btn-outline-secondary me-3">
                                <i class="bi bi-arrow-left"></i>
                            </a>
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" 
                                 style="width: 45px; height: 45px;">
                                {{ strtoupper(substr($session->customer_name, 0, 1)) }}
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $session->customer_name }}</h6>
                                <small class="text-muted">
                                    {{ $session->customer_email ?? 'Khách' }}
                                    <span class="badge bg-{{ $session->status == 'active' ? 'success' : 'secondary' }} ms-1">
                                        {{ $session->status == 'active' ? 'Đang mở' : 'Đã đóng' }}
                                    </span>
                                </small>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if($session->status == 'active')
                                <li>
                                    <form action="{{ route('admin.chatbox.close', $session->id) }}" method="POST">
                                        @csrf
                                        <button class="dropdown-item"><i class="bi bi-x-circle me-2"></i>Đóng hội thoại</button>
                                    </form>
                                </li>
                                @endif
                                <li>
                                    <form action="{{ route('admin.chatbox.delete', $session->id) }}" method="POST" 
                                          onsubmit="return confirm('Bạn có chắc muốn xóa cuộc hội thoại này?')">
                                        @csrf @method('DELETE')
                                        <button class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Xóa</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <div class="card-body flex-grow-1 overflow-auto p-3" id="chatMessages" style="background: #f5f7fb;">
                    @foreach($session->messages as $message)
                        <div class="d-flex mb-3 {{ $message->sender_type == 'client' ? '' : 'justify-content-end' }}">
                            @if($message->sender_type == 'client')
                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center flex-shrink-0 me-2"
                                     style="width: 32px; height: 32px; font-size: 12px;">
                                    {{ strtoupper(substr($session->customer_name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="chat-bubble {{ $message->sender_type == 'client' ? 'chat-bubble--received' : 'chat-bubble--sent' }}"
                                 style="max-width: 70%;">
                                <div class="p-2 px-3 rounded-3 {{ $message->sender_type == 'client' ? 'bg-white' : ($message->sender_type == 'bot' ? 'bg-info text-white' : 'bg-primary text-white') }}">
                                    @if($message->sender_type != 'client')
                                        <small class="d-block mb-1 {{ $message->sender_type == 'bot' ? 'text-white-50' : 'text-white-50' }}">
                                            {{ $message->sender_type == 'bot' ? '🤖 Bot' : $message->sender->name ?? 'Admin' }}
                                        </small>
                                    @endif
                                    @if($message->message_type == 'image' && $message->attachment_url)
                                        <a href="{{ $message->attachment_url }}" target="_blank" data-bs-toggle="modal" data-bs-target="#imageModal" data-image="{{ $message->attachment_url }}">
                                            <img src="{{ $message->attachment_url }}" class="img-fluid rounded mb-2" style="max-width: 200px; max-height: 200px; cursor: pointer;" alt="Image">
                                        </a>
                                        @if($message->message && $message->message != '[Hình ảnh]')
                                            <p class="mb-0" style="white-space: pre-wrap;">{!! nl2br(e($message->message)) !!}</p>
                                        @endif
                                    @else
                                        <p class="mb-0" style="white-space: pre-wrap;">{!! nl2br(e($message->message)) !!}</p>
                                    @endif
                                    <small class="d-block mt-1 {{ $message->sender_type == 'client' ? 'text-muted' : 'text-white-50' }}">
                                        {{ $message->created_at->format('H:i') }}
                                        @if($message->sender_type != 'client' && $message->is_read)
                                            <i class="bi bi-check2-all ms-1"></i>
                                        @endif
                                    </small>
                                </div>
                            </div>
                            @if($message->sender_type != 'client')
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0 ms-2"
                                     style="width: 32px; height: 32px; font-size: 12px;">
                                    <i class="bi bi-headset"></i>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Input -->
                @if($session->status == 'active')
                <div class="card-footer bg-white border-top p-3">
                    <!-- Image Preview -->
                    <div id="imagePreviewContainer" class="mb-2" style="display: none;">
                        <div class="position-relative d-inline-block">
                            <img src="" id="imagePreview" class="rounded" style="max-width: 150px; max-height: 100px;">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" id="removeImage" style="transform: translate(50%, -50%);">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                    <form action="{{ route('admin.chatbox.send', $session->id) }}" method="POST" id="chatForm" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="image" id="imageInput" accept="image/*" style="display: none;">
                        <div class="input-group">
                            <button type="button" class="btn btn-outline-secondary" id="attachBtn" title="Gửi hình ảnh">
                                <i class="bi bi-image"></i>
                            </button>
                            <textarea name="message" class="form-control" rows="1" placeholder="Nhập tin nhắn..."
                                      id="messageInput" style="resize: none;"></textarea>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </div>
                    </form>
                </div>
                @else
                <div class="card-footer bg-light text-center py-3">
                    <span class="text-muted"><i class="bi bi-lock me-1"></i>Cuộc hội thoại đã đóng</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Customer Info -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="bi bi-person-circle me-2"></i>Thông tin khách hàng</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small">Tên</label>
                        <p class="mb-0 fw-semibold">{{ $session->customer_name }}</p>
                    </div>
                    @if($session->customer_email)
                    <div class="mb-3">
                        <label class="form-label text-muted small">Email</label>
                        <p class="mb-0">{{ $session->customer_email }}</p>
                    </div>
                    @endif
                    @if($session->guest_phone)
                    <div class="mb-3">
                        <label class="form-label text-muted small">Số điện thoại</label>
                        <p class="mb-0">{{ $session->guest_phone }}</p>
                    </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label text-muted small">IP Address</label>
                        <p class="mb-0"><code>{{ $session->ip_address }}</code></p>
                    </div>
                    @if($session->page_url)
                    <div class="mb-3">
                        <label class="form-label text-muted small">Trang đang xem</label>
                        <p class="mb-0 text-truncate"><a href="{{ $session->page_url }}" target="_blank">{{ $session->page_url }}</a></p>
                    </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label text-muted small">Bắt đầu chat</label>
                        <p class="mb-0">{{ $session->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label text-muted small">Số tin nhắn</label>
                        <p class="mb-0">{{ $session->messages->count() }} tin</p>
                    </div>
                </div>
            </div>

            <!-- Quick Replies -->
            @if(!empty($settings->quick_replies))
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Trả lời nhanh</h6>
                </div>
                <div class="card-body p-2">
                    @foreach($settings->quick_replies as $qr)
                        <button type="button" class="btn btn-sm btn-outline-primary m-1 quick-reply-btn"
                                data-message="{{ $qr['message'] ?? $qr['text'] }}">
                            {{ $qr['text'] }}
                        </button>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
#chatMessages {
    scroll-behavior: smooth;
}
.chat-bubble--received .rounded-3 {
    border-bottom-left-radius: 4px !important;
}
.chat-bubble--sent .rounded-3 {
    border-bottom-right-radius: 4px !important;
}
body.dark #chatMessages {
    background: #1a1a1a !important;
}
body.dark .bg-white {
    background-color: #2a2a2a !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll to bottom
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // Auto-resize textarea
    const messageInput = document.getElementById('messageInput');
    const imageInput = document.getElementById('imageInput');
    const attachBtn = document.getElementById('attachBtn');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const imagePreview = document.getElementById('imagePreview');
    const removeImage = document.getElementById('removeImage');
    const chatForm = document.getElementById('chatForm');

    if (messageInput) {
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });

        // Submit on Enter (Shift+Enter for new line)
        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                chatForm.submit();
            }
        });
    }

    // Image upload handling
    if (attachBtn && imageInput) {
        attachBtn.addEventListener('click', () => imageInput.click());

        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    alert('Hình ảnh không được vượt quá 5MB');
                    imageInput.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreviewContainer.style.display = 'block';
                    messageInput.removeAttribute('required');
                };
                reader.readAsDataURL(file);
            }
        });

        removeImage.addEventListener('click', function() {
            imageInput.value = '';
            imagePreviewContainer.style.display = 'none';
            if (!messageInput.value.trim()) {
                messageInput.setAttribute('required', 'required');
            }
        });
    }

    // Quick reply buttons
    document.querySelectorAll('.quick-reply-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const message = this.dataset.message;
            if (messageInput) {
                messageInput.value = message;
                messageInput.focus();
            }
        });
    });

    // Image modal for viewing full size
    document.querySelectorAll('[data-image]').forEach(img => {
        img.addEventListener('click', function(e) {
            e.preventDefault();
            const imageUrl = this.dataset.image;
            const modal = document.createElement('div');
            modal.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center';
            modal.style.cssText = 'background: rgba(0,0,0,0.9); z-index: 9999;';
            modal.innerHTML = `
                <button class="btn btn-light position-absolute top-0 end-0 m-3" onclick="this.parentElement.remove()">
                    <i class="bi bi-x-lg"></i>
                </button>
                <img src="${imageUrl}" style="max-width: 90%; max-height: 90%; object-fit: contain;">
            `;
            modal.addEventListener('click', function(e) {
                if (e.target === modal) modal.remove();
            });
            document.body.appendChild(modal);
        });
    });

    // Polling for new messages
    let lastMessageId = {{ $session->messages->last()?->id ?? 0 }};
    setInterval(function() {
        fetch('{{ route("admin.chatbox.messages", $session->id) }}?last_id=' + lastMessageId)
            .then(res => res.json())
            .then(data => {
                if (data.messages && data.messages.length > 0) {
                    location.reload();
                }
            });
    }, 5000);
});
</script>
@endsection


