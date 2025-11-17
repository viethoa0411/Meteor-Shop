@extends('admin.layouts.app')
@section('title', 'Danh sách danh mục')

@section('content')
    <div class="container-fluid py-4">

        {{-- Thông báo --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Tiêu đề --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                    {{-- Tiêu đề --}}
                    <h3 class="fw-bold text-primary mb-0">
                        <i class="bi bi-people-fill me-2"></i>Danh sách danh mục
                    </h3>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                    {{-- Bộ lọc trạng thái --}}
                    <div class="d-flex gap-1 align-items-center">
                        <a href="{{ route('admin.categories.list', ['status' => 'all'] + request()->except('status')) }}"
                            class="btn {{ request('status', 'active') == 'all' ? 'btn-primary' : 'btn-outline-primary' }} btn-sm px-2 py-1"
                            style="font-size: 0.85rem;">
                            <i class="bi bi-list-ul"></i> Tất cả
                        </a>
                        <a href="{{ route('admin.categories.list', ['status' => 'active'] + request()->except('status')) }}"
                            class="btn {{ request('status', 'active') == 'active' ? 'btn-success' : 'btn-outline-success' }} btn-sm px-2 py-1"
                            style="font-size: 0.85rem;">
                            <i class="bi bi-check-circle-fill"></i> Hoạt động
                        </a>
                        <a href="{{ route('admin.categories.list', ['status' => 'inactive'] + request()->except('status')) }}"
                            class="btn {{ request('status', 'active') == 'inactive' ? 'btn-warning' : 'btn-outline-warning' }} btn-sm px-2 py-1"
                            style="font-size: 0.85rem;">
                            <i class="bi bi-pause-circle-fill"></i> Dừng hoạt động
                        </a>
                    </div>

                    {{-- Ô tìm kiếm --}}
                    <form action="{{ route('admin.categories.list') }}" method="GET"
                        class="d-flex align-items-center flex-grow-1 mx-md-4" style="max-width: 500px;">
                        {{-- Giữ lại tham số status khi tìm kiếm --}}
                        <input type="hidden" name="status" value="{{ request('status', 'active') }}">

                        <div class="input-group w-100">
                            <input type="text" name="keyword" class="form-control"
                                placeholder="Tìm kiếm theo tên danh mục" value="{{ request('keyword') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Tìm kiếm
                            </button>
                            @if (request('keyword'))
                                <a href="{{ route('admin.account.admin.list', ['status' => request('status', 'active')]) }}"
                                    class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            @endif
                        </div>
                    </form>



                    {{-- Nút chức năng --}}
                    <div class="d-flex flex-shrink-0 gap-2">
                        <!-- Thêm danh mục -->
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Thêm danh mục
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bảng danh mục --}}
        @if ($categories->isEmpty())
            <p class="text-center">Không tìm thấy danh mục nào.</p>
        @else
            <div class="table-responsive shadow-sm rounded">
                <table class="table table-striped table-hover align-middle text-center mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên danh mục</th>
                            <th>Danh mục cha</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td data-label="ID">{{ $category->id }}</td>
                                <td data-label="Tên danh mục">{{ $category->name }}</td>
                                <td data-label="Danh mục cha">{{ $category->parent?->name ?? 'Không có' }}</td>
                                <td data-label="Trạng thái">
                                    @if ($category->status == 'active')
                                        <span class="badge bg-success">Hoạt động</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Tạm ẩn</span>
                                    @endif
                                </td>
                                <td data-label="Hành động">
                                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                                        <a href="{{ route('admin.categories.edit', $category->id) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="bi bi-pencil-square"></i> Sửa
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Bạn có chắc chắn muốn xoá danh mục này không?');"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Xóa</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Phân trang --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $categories->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        @endif

        {{-- Responsive table mobile --}}
        <style>
            @media (max-width: 768px) {
                .table thead {
                    display: none;
                }

                .table,
                .table tbody,
                .table tr,
                .table td {
                    display: block;
                    width: 100%;
                }

                .table tr {
                    margin-bottom: .75rem;
                    border-bottom: 1px dashed rgba(0, 0, 0, .1);
                }

                .table td {
                    text-align: right;
                    padding-left: 50%;
                    position: relative;
                }

                .table td::before {
                    content: attr(data-label);
                    position: absolute;
                    left: 0;
                    width: 50%;
                    padding-left: .9rem;
                    font-weight: 600;
                    text-align: left;
                }
            }
        </style>

    </div>
@endsection
