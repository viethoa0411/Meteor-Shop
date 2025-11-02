@extends('admin.layouts.app')

@section('title', 'Danh sách người dùng')

@section('content')

    <style>
        :root {
            --accent: #0d6efd;
            --muted: #6c757d;
            --bg: #f7f9fc;
            --card: #ffffff;
            --danger: #dc3545;
        }

        body {
            background: linear-gradient(180deg, var(--bg) 0%, #f0f4fb 100%);
            color: #212529;
            font-family: "Inter", "Helvetica Neue", Arial, sans-serif;
        }

        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(14, 30, 37, 0.06);
            background: var(--card);
        }

        .table thead th {
            background: linear-gradient(90deg, rgba(13, 110, 253, 0.95), rgba(13, 110, 253, 0.85));
            color: #fff;
            border: 0;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .table tbody tr {
            transition: background .18s, transform .08s;
        }

        .table tbody tr:hover {
            background: rgba(13, 110, 253, 0.03);
            transform: translateY(-1px);
        }

        .table td,
        .table th {
            vertical-align: middle;
            padding: 0.85rem 0.75rem;
            border-top: 1px solid rgba(0, 0, 0, 0.04);
        }

        .btn-sm {
            padding: 0.35rem 0.6rem;
            font-size: 0.85rem;
            border-radius: 6px;
        }

        .btn-info {
            background: #17a2b8;
            border-color: #17a2b8;
            color: #fff;
        }

        .btn-primary {
            background: var(--accent);
            border-color: rgba(13, 110, 253, 0.95);
            color: #fff;
        }

        .btn-danger {
            background: var(--danger);
            border-color: rgba(220, 53, 69, 0.95);
            color: #fff;
        }

        .alert {
            border-radius: 8px;
            box-shadow: 0 6px 18px rgba(3, 10, 18, 0.03);
        }

        .pagination .page-link {
            border-radius: 6px;
            margin: 0 4px;
            color: var(--accent);
            border: 1px solid rgba(13, 110, 253, 0.12);
        }

        .pagination .page-item.active .page-link {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
            box-shadow: 0 6px 18px rgba(13, 110, 253, 0.08);
        }

        /* Responsive table for mobile */
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
                margin-bottom: 0.75rem;
                border-bottom: 1px dashed rgba(0, 0, 0, 0.06);
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
                padding-left: 0.9rem;
                font-weight: 600;
                text-align: left;
                color: var(--muted);
            }
        }
    </style>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                {{-- Tiêu đề --}}
                <h3 class="fw-bold text-primary mb-0">
                    <i class="bi bi-people-fill me-2"></i>Danh sách tài khoản User
                </h3>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                {{-- Nút chức năng --}}
                <div class="d-flex flex-shrink-0 gap-2">
                    <a href="{{ route('admin.account.users.trash') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-person-fill-slash"></i> Tài khoản bị ẩn
                    </a>
                    <a href="{{ route('admin.account.users.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Thêm người dùng
                    </a>
                </div>
            </div>
        </div>
    </div>


    @if ($users->isEmpty())
        <p class="text-center">Chưa có người dùng nào.</p>
    @else
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle text-center">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td data-label="STT">{{ $user->id }}</td>
                            <td data-label="Tên">{{ $user->name }}</td>
                            <td data-label="Email">{{ $user->email }}</td>
                            <td data-label="Vai trò">{{ ucfirst($user->role) }}</td>

                            {{-- Hiển thị trạng thái với màu sắc --}}
                            <td data-label="Trạng thái">
                                @if ($user->status === 'active')
                                    <span class="badge bg-success">Hoạt động</span>
                                @elseif ($user->status === 'inactive')
                                    <span class="badge bg-warning text-dark">Không hoạt động</span>
                                @elseif ($user->status === 'banned')
                                    <span class="badge bg-danger">Bị cấm</span>
                                @else
                                    <span class="badge bg-secondary">Không xác định</span>
                                @endif
                            </td>

                            <td data-label="Ngày tạo">{{ $user->created_at->format('Y-m-d') }}</td>
                                {{-- Các nút hành động --}}
                            <td data-label="Hành động">
                                <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap">
                                    <a href="{{ route('admin.account.users.edit', $user->id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-pencil-square"></i> Sửa
                                    </a>

                                    <form action="{{ route('admin.account.users.destroy', $user->id) }}" method="POST"
                                        onsubmit="return confirm('Bạn có chắc muốn ẩn người dùng này không?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-person-fill-slash"></i> Ẩn tài khoản
                                        </button>
                                    </form>
                            
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
    @endif
@endsection
