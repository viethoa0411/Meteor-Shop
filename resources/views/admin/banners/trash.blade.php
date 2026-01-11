@extends('admin.layouts.app')
@section('title', 'Thùng rác Banner')

@section('content')
    <div class="container-fluid py-4">
        {{-- Thông báo --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Tiêu đề --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="fw-bold text-primary mb-0">
                        <i class="bi bi-trash3 me-2"></i>Thùng rác Banner
                    </h3>
                    <a href="{{ route('admin.banners.list') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-left"></i> Quay lại danh sách
                    </a>
                </div>
            </div>
        </div>

        {{-- Hành động hàng loạt --}}
        @if (!$banners->isEmpty())
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form id="bulkRestoreForm" action="{{ route('admin.banners.bulkRestore') }}" method="POST" class="d-inline">
                        @csrf
                    </form>
                    <form id="bulkForceDeleteForm" action="{{ route('admin.banners.bulkForceDelete') }}" method="POST" class="d-inline">
                        @csrf
                    </form>
                    <div class="d-flex gap-2 align-items-center">
                        <button type="button" class="btn btn-sm btn-success" id="bulkRestoreBtn" disabled>
                            <i class="bi bi-arrow-counterclockwise"></i> Khôi phục đã chọn
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" id="bulkForceDeleteBtn" disabled>
                            <i class="bi bi-trash-fill"></i> Xóa vĩnh viễn đã chọn
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Bảng danh sách --}}
        @if ($banners->isEmpty())
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="text-muted mt-3">Thùng rác trống.</p>
                </div>
            </div>
        @else
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAllTrash" class="form-check-input">
                                    </th>
                                    <th width="80">Ảnh</th>
                                    <th>Tiêu đề</th>
                                    <th>Ngày xóa</th>
                                    <th width="200">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($banners as $banner)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="ids[]" value="{{ $banner->id }}"
                                                class="form-check-input trash-checkbox">
                                        </td>
                                        <td>
                                            @if ($banner->image_url)
                                                <img src="{{ $banner->image_url }}" 
                                                    alt="{{ $banner->title ?? 'Banner' }}" 
                                                    class="img-thumbnail"
                                                    style="width: 60px; height: 40px; object-fit: cover;"
                                                    onerror="this.onerror=null; this.parentElement.innerHTML='<div class=&quot;bg-light d-flex align-items-center justify-content-center&quot; style=&quot;width: 60px; height: 40px;&quot;><i class=&quot;bi bi-image text-muted&quot;></i></div>';">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center"
                                                    style="width: 60px; height: 40px;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td><strong>{{ $banner->title ?? 'N/A' }}</strong></td>
                                        <td>{{ $banner->deleted_at ? $banner->deleted_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <form action="{{ route('admin.banners.restore', $banner->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success"
                                                        onclick="return confirm('Khôi phục banner này?');">
                                                        <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.banners.forceDelete', $banner->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn banner này? Hành động này không thể hoàn tác!');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash-fill"></i> Xóa vĩnh viễn
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Phân trang --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $banners->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            // Select all checkbox
            document.getElementById('selectAllTrash')?.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.trash-checkbox');
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateBulkTrashButtons();
            });

            // Update bulk trash buttons
            document.querySelectorAll('.trash-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkTrashButtons);
            });

            function updateBulkTrashButtons() {
                const checked = document.querySelectorAll('.trash-checkbox:checked').length;
                const bulkRestoreBtn = document.getElementById('bulkRestoreBtn');
                const bulkForceDeleteBtn = document.getElementById('bulkForceDeleteBtn');
                
                const disabled = checked === 0;
                
                if (bulkRestoreBtn) {
                    bulkRestoreBtn.disabled = disabled;
                    if (checked > 0) {
                        bulkRestoreBtn.innerHTML = `<i class="bi bi-arrow-counterclockwise"></i> Khôi phục đã chọn (${checked})`;
                    } else {
                        bulkRestoreBtn.innerHTML = '<i class="bi bi-arrow-counterclockwise"></i> Khôi phục đã chọn';
                    }
                }
                
                if (bulkForceDeleteBtn) {
                    bulkForceDeleteBtn.disabled = disabled;
                    if (checked > 0) {
                        bulkForceDeleteBtn.innerHTML = `<i class="bi bi-trash-fill"></i> Xóa vĩnh viễn đã chọn (${checked})`;
                    } else {
                        bulkForceDeleteBtn.innerHTML = '<i class="bi bi-trash-fill"></i> Xóa vĩnh viễn đã chọn';
                    }
                }
            }

            // Bulk restore
            document.getElementById('bulkRestoreBtn')?.addEventListener('click', function(e) {
                e.preventDefault();
                const checked = document.querySelectorAll('.trash-checkbox:checked');
                if (checked.length === 0) {
                    alert('Vui lòng chọn ít nhất một banner để khôi phục!');
                    return;
                }

                if (confirm(`Khôi phục ${checked.length} banner đã chọn?`)) {
                    const form = document.getElementById('bulkRestoreForm');
                    // Clear form trước khi thêm input mới
                    form.querySelectorAll('input[name="ids[]"]').forEach(input => input.remove());
                    
                    checked.forEach(cb => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = cb.value;
                        form.appendChild(input);
                    });
                    form.submit();
                }
            });

            // Bulk force delete
            document.getElementById('bulkForceDeleteBtn')?.addEventListener('click', function(e) {
                e.preventDefault();
                const checked = document.querySelectorAll('.trash-checkbox:checked');
                if (checked.length === 0) {
                    alert('Vui lòng chọn ít nhất một banner để xóa vĩnh viễn!');
                    return;
                }

                if (confirm(`Bạn có chắc chắn muốn xóa vĩnh viễn ${checked.length} banner đã chọn? Hành động này không thể hoàn tác!`)) {
                    const form = document.getElementById('bulkForceDeleteForm');
                    // Clear form trước khi thêm input mới
                    form.querySelectorAll('input[name="ids[]"]').forEach(input => input.remove());
                    
                    checked.forEach(cb => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = cb.value;
                        form.appendChild(input);
                    });
                    form.submit();
                }
            });
        </script>
    @endpush
@endsection

