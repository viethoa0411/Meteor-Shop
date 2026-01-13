@extends('admin.layouts.app')
@section('title', 'Danh sách khuyến mãi')

@section('content')
    <div class="container-fluid py-4">

        <div class="card border-0 shadow-sm mb-4 bg-body">
            <div class="card-body">
                <h3 class="fw-bold text-primary mb-0">
                    <i class="bi bi-ticket-perforated me-2"></i>Danh sách khuyến mãi
                </h3>
            </div>
        </div>

        <div class="card shadow-sm bg-body mb-3">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.promotions.list', ['status' => '', 'keyword' => request('keyword')]) }}"
                        class="btn {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="bi bi-list-ul"></i> Tất cả
                    </a>
                    <a href="{{ route('admin.promotions.list', ['status' => 'active', 'keyword' => request('keyword')]) }}"
                        class="btn {{ request('status') == 'active' ? 'btn-success text-white' : 'btn-outline-success' }}">
                        <i class="bi bi-check-circle"></i> Hoạt động
                    </a>
                    <a href="{{ route('admin.promotions.list', ['status' => 'inactive', 'keyword' => request('keyword')]) }}"
                        class="btn {{ request('status') == 'inactive' ? 'btn-warning text-white' : 'btn-outline-warning' }}">
                        <i class="bi bi-pause-circle"></i> Dừng hoạt động
                    </a>
                </div>

                <form action="{{ route('admin.promotions.list') }}" method="GET" class="d-flex gap-2">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <div class="input-group">
                        <input type="text" name="keyword" class="form-control" placeholder="Tìm theo mã hoặc tên" value="{{ request('keyword') }}" style="min-width: 300px;">
                        <button class="btn btn-primary"><i class="bi bi-search"></i> Tìm kiếm</button>
                    </div>
                </form>

                <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary text-nowrap">
                    <i class="bi bi-plus-circle"></i> Thêm khuyến mãi
                </a>
            </div>
        </div>

        @if ($promotions->isEmpty())
            <p class="text-center">Không có khuyến mãi nào.</p>
        @else
            <div class="table-responsive shadow-sm rounded">
                <table class="table table-striped table-hover align-middle text-center mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Mã</th>
                            <th>Tên</th>
                            <th>Giảm</th>
                            <th>Thời gian</th>
                            <th>Phạm vi</th>
                            <th>Trạng thái</th>
                            <th>Đã dùng</th>
                            <th>Giới hạn</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($promotions as $p)
                            <tr>
                                <td>{{ $p->id }}</td>
                                <td>{{ $p->code }}</td>
                                <td>{{ $p->name }}</td>
                                <td>
                                    @if ($p->discount_type === 'percent')
                                        {{ $p->discount_value }}% @if($p->max_discount) (Tối đa {{ number_format($p->max_discount, 0, ',', '.') }}đ) @endif
                                    @else
                                        {{ number_format($p->discount_value, 0, ',', '.') }}đ
                                    @endif
                                </td>
                                <td>{{ $p->start_date->format('d/m/Y H:i') }} → {{ $p->end_date->format('d/m/Y H:i') }}</td>
                                <td>{{ strtoupper($p->scope) }}</td>
                                <td>
                                    @if ($p->status == 'active')
                                        <span class="badge bg-success">Hoạt động</span>
                                    @elseif ($p->status == 'inactive')
                                        <span class="badge bg-warning text-dark">Dừng hoạt động</span>
                                    @else
                                        <span class="badge bg-danger">Hết hạn</span>
                                    @endif
                                </td>
                                <td>{{ $p->used_count }}</td>
                                <td>
                                    @if ($p->limit_global)
                                        Tổng: {{ $p->limit_global }}
                                    @endif
                                    @if ($p->limit_per_user)
                                        <br>Per-user: {{ $p->limit_per_user }}
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.promotions.edit', $p->id) }}" class="btn btn-sm btn-info"><i class="bi bi-pencil-square"></i> Sửa</a>
                                    <form action="{{ route('admin.promotions.destroy', $p->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger btn-delete" data-name="{{ $p->code }}"><i class="bi bi-trash"></i> Xóa</button>
                                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $promotions->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
@endif

</div>
@endsection
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const form = this.closest('form');
                const name = this.getAttribute('data-name') || 'khuyến mãi này';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Xác nhận xóa?',
                        text: `Bạn có chắc chắn muốn xóa "${name}" không?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Xóa ngay',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                } else {
                    if (window.confirm(`Bạn có chắc chắn muốn xóa "${name}" không?`)) {
                        form.submit();
                    }
                }
            });
        });
    });
</script>
@endpush
