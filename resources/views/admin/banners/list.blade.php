@extends('admin.layouts.app')
@section('title', 'Quản lý Banner')

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
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <h3 class="fw-bold text-primary mb-0">
                        <i class="bi bi-image-fill me-2"></i>Quản lý Banner
                    </h3>
                </div>
            </div>
        </div>

        {{-- Toolbar --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('admin.banners.list') }}" method="GET" class="row g-3 align-items-end">
                    {{-- Tìm kiếm --}}
                    <div class="col-md-4">
                        <div class="input-group toolbar-input-group">
                            <input type="text" name="keyword" class="form-control" placeholder="Tìm kiếm theo tiêu đề, mô tả..."
                                value="{{ request('keyword') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Lọc trạng thái --}}
                    <div class="col-md-3">
                        <select name="status" class="form-select toolbar-select">
                            <option value="all">Tất cả trạng thái</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tạm ẩn</option>
                        </select>
                    </div>

                    {{-- Nút hành động --}}
                    <div class="col-md-5 d-flex gap-2 align-items-end">
                        <button type="submit" class="btn btn-outline-primary toolbar-btn">
                            <i class="bi bi-funnel"></i> Lọc
                        </button>
                        <a href="{{ route('admin.banners.list') }}" class="btn btn-outline-secondary toolbar-btn">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                        <a href="{{ route('admin.banners.create') }}" class="btn btn-primary toolbar-btn">
                            <i class="bi bi-plus-circle"></i> Thêm mới
                        </a>
                    </div>
                </form>

                {{-- Hành động hàng loạt --}}
                <form id="bulkActionForm" action="{{ route('admin.banners.bulkDelete') }}" method="POST" class="mt-3">
                    @csrf
                    <div class="d-flex gap-2 align-items-center">
                        <button type="button" class="btn btn-sm btn-danger" id="bulkDeleteBtn" disabled>
                            <i class="bi bi-trash"></i> Xóa đã chọn
                        </button>
                        <a href="{{ route('admin.banners.trash') }}" class="btn btn-sm btn-secondary">
                            <i class="bi bi-trash3"></i> Thùng rác
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Bảng danh sách --}}
        @if ($banners->isEmpty())
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="text-muted mt-3">Không tìm thấy banner nào.</p>
                    <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Thêm banner đầu tiên
                    </a>
                </div>
            </div>
        @else
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="bannersTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th width="80">Ảnh</th>
                                    <th>Tiêu đề</th>
                                    <th>Link</th>
                                    <th width="100">Thứ tự</th>
                                    <th width="120">Trạng thái</th>
                                    <th width="180">Thời gian</th>
                                    <th width="150">Hành động</th>
                                </tr>
                            </thead>
                            <tbody id="sortable">
                                @foreach ($banners as $banner)
                                    <tr data-id="{{ $banner->id }}" style="cursor: move;">
                                        <td>
                                            <input type="checkbox" name="ids[]" value="{{ $banner->id }}"
                                                class="form-check-input banner-checkbox">
                                        </td>
                                        <td>
                                            @if (!empty($banner->image))
                                                <img src="{{ asset($banner->image) }}" alt="{{ $banner->title }}"
                                                    class="img-thumbnail" style="width: 60px; height: 40px; object-fit: cover;"
                                                    onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'bg-light d-flex align-items-center justify-content-center\' style=\'width: 60px; height: 40px;\'><i class=\'bi bi-image text-muted\'></i></div>';">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center"
                                                    style="width: 60px; height: 40px;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $banner->title }}</strong>
                                            @if ($banner->description)
                                                <br>
                                                <small class="text-muted">{{ Str::limit($banner->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($banner->link)
                                                <a href="{{ $banner->link }}" target="_blank" class="text-decoration-none">
                                                    <i class="bi bi-link-45deg"></i> Xem link
                                                </a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $banner->sort_order }}</span>
                                            <i class="bi bi-grip-vertical text-muted ms-1"></i>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input status-toggle" type="checkbox"
                                                    data-id="{{ $banner->id }}"
                                                    {{ $banner->status == 'active' ? 'checked' : '' }}>
                                                <label class="form-check-label">
                                                    {{ $banner->status == 'active' ? 'Hoạt động' : 'Tạm ẩn' }}
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($banner->start_date || $banner->end_date)
                                                <small>
                                                    <strong>Từ:</strong> {{ $banner->start_date ? $banner->start_date->format('d/m/Y') : '—' }}<br>
                                                    <strong>Đến:</strong> {{ $banner->end_date ? $banner->end_date->format('d/m/Y') : '—' }}
                                                </small>
                                            @else
                                                <span class="text-muted">Luôn hiển thị</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.banners.show', $banner->id) }}"
                                                    class="btn btn-sm btn-info" title="Xem chi tiết">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.banners.edit', $banner->id) }}"
                                                    class="btn btn-sm btn-warning" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('admin.banners.destroy', $banner->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa banner này?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                        <i class="bi bi-trash"></i>
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

    @push('styles')
        <style>
            /* Đảm bảo tất cả các phần tử trong toolbar có cùng chiều cao */
            .toolbar-input-group,
            .toolbar-select,
            .toolbar-btn {
                height: 38px;
            }
            
            .toolbar-input-group .form-control,
            .toolbar-input-group .btn {
                height: 100%;
            }
            
            .toolbar-select {
                display: flex;
                align-items: center;
            }
            
            /* Responsive: trên mobile, các phần tử sẽ tự điều chỉnh */
            @media (max-width: 768px) {
                .toolbar-input-group,
                .toolbar-select,
                .toolbar-btn {
                    height: auto;
                    min-height: 38px;
                }
                
                .col-md-5.d-flex {
                    flex-wrap: wrap;
                }
                
                .toolbar-btn {
                    flex: 1;
                    min-width: 100px;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
        <script>
            // Select all checkbox
            document.getElementById('selectAll')?.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.banner-checkbox');
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateBulkDeleteBtn();
            });

            // Update bulk delete button
            document.querySelectorAll('.banner-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkDeleteBtn);
            });

            function updateBulkDeleteBtn() {
                const checked = document.querySelectorAll('.banner-checkbox:checked').length;
                const btn = document.getElementById('bulkDeleteBtn');
                if (btn) {
                    btn.disabled = checked === 0;
                    if (checked > 0) {
                        btn.textContent = `Xóa đã chọn (${checked})`;
                    } else {
                        btn.innerHTML = '<i class="bi bi-trash"></i> Xóa đã chọn';
                    }
                }
            }

            // Bulk delete
            document.getElementById('bulkDeleteBtn')?.addEventListener('click', function() {
                const checked = document.querySelectorAll('.banner-checkbox:checked');
                if (checked.length === 0) return;

                if (confirm(`Bạn có chắc chắn muốn xóa ${checked.length} banner đã chọn?`)) {
                    const form = document.getElementById('bulkActionForm');
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

            // Status toggle
            document.querySelectorAll('.status-toggle').forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const id = this.dataset.id;
                    const status = this.checked ? 'active' : 'inactive';

                    fetch(`/admin/banners/${id}/status`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ status })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra!');
                        this.checked = !this.checked;
                    });
                });
            });

            // Drag & Drop Sortable
            const sortable = document.getElementById('sortable');
            if (sortable) {
                new Sortable(sortable, {
                    handle: '.bi-grip-vertical',
                    animation: 150,
                    onEnd: function(evt) {
                        const items = Array.from(sortable.querySelectorAll('tr[data-id]'));
                        const banners = items.map((item, index) => ({
                            id: item.dataset.id,
                            sort_order: index + 1
                        }));

                        fetch('{{ route("admin.banners.updateSortOrder") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ banners })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Có lỗi xảy ra khi cập nhật thứ tự!');
                        });
                    }
                });
            }
        </script>
    @endpush
@endsection

