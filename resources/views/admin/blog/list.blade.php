@extends('admin.layouts.app')

@section('title', 'Danh sách blog')

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

        .thumbnail-img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
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
                    <i class="bi bi-journal-text me-2"></i>Danh sách bài viết
                </h3>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

                {{-- Nút thêm blog mới --}}
                <div class="d-flex flex-shrink-0 gap-2">
                    <a href="#" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Thêm blog mới
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if ($blogs->isEmpty())
        <p class="text-center">Chưa có bài viết nào.</p>
    @else
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle text-center">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Ảnh</th>
                        <th>Tiêu đề</th>
                        <th>Người tạo</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($blogs as $index => $blog)
                        <tr>
                            <td data-label="STT">{{ $blogs->firstItem() + $index }}</td>
                            <td data-label="Ảnh">
                                @if ($blog->thumbnail)
                                    <img src="{{ asset('blog/images/' . $blog->thumbnail) }}" alt="{{ $blog->title }}" class="thumbnail-img">
                                @else
                                    <span class="text-muted">Không có ảnh</span>
                                @endif
</td>
                            <td data-label="Tiêu đề">{{ \Illuminate\Support\Str::limit($blog->title, 50) }}</td>
                            <td data-label="Người tạo">{{ $blog->user->name ?? 'N/A' }}</td>

                            {{-- Hiển thị trạng thái với màu sắc --}}
                            <td data-label="Trạng thái">
                                @if ($blog->status === 'published')
                                    <span class="badge bg-success">Hoạt động</span>
                                @else
                                    <span class="badge bg-warning text-dark">Dừng hoạt động</span>
                                @endif
                            </td>

                            <td data-label="Ngày tạo">{{ $blog->created_at->format('Y-m-d') }}</td>
                            {{-- Các nút hành động --}}
                            <td data-label="Hành động">
                                <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap">
                                    <a href="#" class="btn btn-sm btn-info">
                                        <i class="bi bi-pencil-square"></i> Sửa
                                    </a>

                                    <button>Xóa</button>
                                    <a href="#" class="btn btn-sm btn-secondary">
                                        <i class="bi bi-eye"></i> Xem Chi Tiết
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
    @endif
@endsection