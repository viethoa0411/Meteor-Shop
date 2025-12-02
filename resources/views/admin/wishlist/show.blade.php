@extends('admin.layouts.app')
@section('title', 'Chi tiết sản phẩm yêu thích')

@section('content')
    <div class="container-fluid py-4">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.wishlist.index') }}">Sản phẩm yêu thích</a>
                </li>
                <li class="breadcrumb-item active">{{ $product->name }}</li>
            </ol>
        </nav>

        {{-- Thông tin sản phẩm --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/300x300?text=No+Image' }}"
                            alt="{{ $product->name }}"
                            class="img-fluid rounded">
                    </div>
                    <div class="col-md-9">
                        <h3 class="fw-bold">{{ $product->name }}</h3>
                        <p class="text-muted mb-2">
                            <strong>Danh mục:</strong> {{ $product->category->name ?? 'N/A' }}
                        </p>
                        <p class="text-muted mb-2">
                            <strong>Giá:</strong> {{ number_format($product->price, 0, ',', '.') }} đ
                        </p>
                        <p class="mb-0">
                            <strong>Tổng lượt yêu thích:</strong>
                            <span class="badge bg-danger fs-6">
                                <i class="bi bi-heart-fill"></i> {{ $favoriteCount }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Danh sách khách hàng đã yêu thích --}}
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-people-fill"></i> Danh sách khách hàng đã yêu thích ({{ $favoriteCount }})
                </h5>
            </div>
            <div class="card-body">
                @if ($wishlists->isEmpty())
                    <p class="text-center text-muted py-4">Chưa có khách hàng nào yêu thích sản phẩm này.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Khách hàng</th>
                                    <th>Email</th>
                                    <th>Điện thoại</th>
                                    <th>Ngày yêu thích</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($wishlists as $index => $wishlist)
                                    <tr>
                                        <td>{{ $wishlists->firstItem() + $index }}</td>
                                        <td>
                                            <a href="{{ route('admin.account.users.show', $wishlist->user->id) }}" 
                                               class="text-dark text-decoration-none">
                                                {{ $wishlist->user->name }}
                                            </a>
                                        </td>
                                        <td>{{ $wishlist->user->email }}</td>
                                        <td>{{ $wishlist->user->phone ?? 'N/A' }}</td>
                                        <td>{{ $wishlist->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Phân trang --}}
                    <div class="mt-4">
                        {{ $wishlists->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Nút quay lại --}}
        <div class="mt-4">
            <a href="{{ route('admin.wishlist.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
@endsection