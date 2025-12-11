<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-sticky"></i> Ghi chú</h5>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                    <i class="bi bi-plus-circle"></i> Thêm ghi chú
                </button>
            </div>
            <div class="card-body">
                @php
                    // 直接查询数据库以确保获取最新数据
                    $notes = \App\Models\OrderNote::where('order_id', $order->id)
                        ->with(['creator', 'taggedUser'])
                        ->orderBy('is_pinned', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->get();
                @endphp
                @if($notes && $notes->count() > 0)
                    @foreach($notes as $note)
                        <div class="card mb-3 {{ $note->is_pinned ? 'border-warning' : '' }}">
                            <div class="card-body">
                                @if($note->is_pinned)
                                    <span class="badge bg-warning text-dark mb-2">
                                        <i class="bi bi-pin-angle-fill"></i> Đã ghim
                                    </span>
                                @endif
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <div class="fw-bold">{{ $note->note }}</div>
                                        <div class="small text-muted mt-1">
                                            <span class="badge bg-{{ $note->type === 'internal' ? 'primary' : ($note->type === 'customer' ? 'info' : 'secondary') }}">
                                                {{ $note->type === 'internal' ? 'Nội bộ' : ($note->type === 'customer' ? 'Khách hàng' : 'Hệ thống') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editNoteModal{{ $note->id }}">
                                            <i class="bi bi-pencil"></i> Sửa
                                        </button>
                                        <form action="{{ route('admin.orders.notes.destroy', [$order->id, $note->id]) }}" 
                                              method="POST" class="d-inline" 
                                              onsubmit="return confirm('Bạn có chắc muốn xóa ghi chú này?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @if($note->tagged_user_id && $note->taggedUser)
                                    <div class="small text-muted">
                                        <i class="bi bi-at"></i> Tagged: {{ $note->taggedUser->name }}
                                    </div>
                                @endif
                                @if($note->attachments && count($note->attachments) > 0)
                                    <div class="mt-2">
                                        @foreach($note->attachments as $attachment)
                                            <a href="{{ asset('storage/' . $attachment) }}" target="_blank" 
                                               class="btn btn-sm btn-outline-secondary me-1">
                                                <i class="bi bi-paperclip"></i> File {{ $loop->iteration }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="small text-muted mt-2">
                                    {{ $note->created_at->format('d/m/Y H:i') }}
                                    @if($note->creator)
                                        bởi {{ $note->creator->name }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-sticky" style="font-size: 3rem;"></i>
                        <div class="mt-2">Chưa có ghi chú</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Edit Note Modals --}}
@if($notes && $notes->count() > 0)
    @foreach($notes as $note)
<div class="modal fade" id="editNoteModal{{ $note->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.orders.notes.update', [$order->id, $note->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Sửa ghi chú</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Loại ghi chú</label>
                        <select name="type" class="form-select" required>
                            <option value="internal" {{ $note->type === 'internal' ? 'selected' : '' }}>Nội bộ</option>
                            <option value="customer" {{ $note->type === 'customer' ? 'selected' : '' }}>Khách hàng</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nội dung</label>
                        <textarea name="note" class="form-control" rows="4" required>{{ $note->note }}</textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_pinned" value="1" class="form-check-input" id="is_pinned_edit{{ $note->id }}" {{ $note->is_pinned ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_pinned_edit{{ $note->id }}">Ghim ghi chú</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tag người dùng (tùy chọn)</label>
                        <select name="tagged_user_id" class="form-select">
                            <option value="">Không tag</option>
                            @foreach(\App\Models\User::where('role', 'admin')->orWhere('role', 'staff')->get() as $user)
                                <option value="{{ $user->id }}" {{ $note->tagged_user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Đính kèm file mới (tùy chọn)</label>
                        <input type="file" name="attachments[]" class="form-control" multiple 
                               accept="image/*,.pdf,.doc,.docx">
                        <small class="text-muted">Có thể chọn nhiều file (sẽ thêm vào file hiện có)</small>
                        @if($note->attachments && count($note->attachments) > 0)
                            <div class="mt-2">
                                <small class="text-muted">File hiện có:</small>
                                @foreach($note->attachments as $attachment)
                                    <div class="small">
                                        <i class="bi bi-paperclip"></i> 
                                        <a href="{{ asset('storage/' . $attachment) }}" target="_blank">{{ basename($attachment) }}</a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>
    @endforeach
@endif

{{-- Add Note Modal --}}
<div class="modal fade" id="addNoteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.orders.notes.store', $order->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Thêm ghi chú</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Loại ghi chú</label>
                        <select name="type" class="form-select" required>
                            <option value="internal">Nội bộ</option>
                            <option value="customer">Khách hàng</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nội dung</label>
                        <textarea name="note" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_pinned" value="1" class="form-check-input" id="is_pinned">
                            <label class="form-check-label" for="is_pinned">Ghim ghi chú</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tag người dùng (tùy chọn)</label>
                        <select name="tagged_user_id" class="form-select">
                            <option value="">Không tag</option>
                            @foreach(\App\Models\User::where('role', 'admin')->orWhere('role', 'staff')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Đính kèm file (tùy chọn)</label>
                        <input type="file" name="attachments[]" class="form-control" multiple 
                               accept="image/*,.pdf,.doc,.docx">
                        <small class="text-muted">Có thể chọn nhiều file</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

