@extends('admin.layouts.app')
@section('title', 'Quản lý sản phẩm yêu thích')

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
                    <h3 class="fw-bold text-primary mb-0">
                        <i class="bi bi-heart-fill me-2"></i>Quản lý sản phẩm yêu thích
                    </h3>
                </div>
            </div>
        </div>

        {{-- Bảng sản phẩm yêu thích --}}
        @if ($products->isEmpty())
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <p class="text-muted mt-3">Chưa có sản phẩm nào được yêu thích.</p>
                </div>
            </div>
        @else
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle text-center mb-0">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Hình ảnh</th>
                                    <th>Tên</th>
                                    <th>Số Lượng Yêu thích</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $index => $product)
                                    <tr>
                                        <td>{{ $products->firstItem() + $index }}</td>
                                        <td>
                                            <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/80x80?text=No+Image' }}"
                                                alt="{{ $product->name }}"
                                                style="width: 80px; height: 80px; object-fit: cover; border-radius: 6px;">
                                        </td>
                                        <td class="text-start">
                                            <a href="{{ route('admin.products.show', $product->id) }}" 
                                               class="text-dark text-decoration-none fw-semibold">
                                                {{ $product->name }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger">
                                                <i class="bi bi-heart-fill"></i> {{ $product->favorite_count ?? 0 }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.wishlist.show', $product->id) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye"></i> Xem chi tiết
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Phân trang --}}
                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection