@extends('client.layouts.app')
@section('title', $product->name . ' | Meteor Shop')
@section('meta_description', Str::limit(strip_tags($product->description), 160))
@section('content')


<nav aria-label="breadcrumb" class="bg-light py-3 mb-4">
    <div class="container">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('client.home') }}" class="text-decoration-none text-muted">Trang chủ</a></li>
            {{-- Link về danh mục (Dùng slug) --}}
            @if($product->category)
                <li class="breadcrumb-item">
                    <a href="{{ route('client.product.category', ['slug' => $product->category->slug]) }}" class="text-decoration-none text-muted">
                        {{ $product->category->name }}
                    </a>
                </li>
            @endif
            <li class="breadcrumb-item active text-dark" aria-current="page">{{ $product->name }}</li>
        </ol>
    </div>
</nav>

<div class="container pb-5">
    <div class="row g-4">
        {{-- Cột trái: Ảnh sản phẩm --}}
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm">
                <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/500x500?text=No+Image' }}" 
                     class="card-img-top img-fluid" 
                     alt="{{ $product->name }} - Meteor Shop"
                     style="object-fit: cover; aspect-ratio: 1/1;">
            </div>
        </div>

        {{-- Cột phải: Thông tin chi tiết --}}
        <div class="col-md-6 col-lg-7">
            <div class="ps-lg-4">
                <h1 class="fw-bold text-dark mb-3">{{ $product->name }}</h1>

                <div class="mb-3">
                    <span class="badge bg-primary me-2">
                        {{ $product->category->name ?? 'Chưa phân loại' }}
                    </span>
                    {{-- Trạng thái còn hàng/hết hàng --}}
                    @if($product->stock > 0)
                        <span class="badge bg-success">Còn hàng ({{ $product->stock }})</span>
                    @else
                        <span class="badge bg-secondary">Hết hàng</span>
                    @endif
                </div>

                <h2 class="text-danger fw-bold mb-4">
                    {{ number_format($product->price, 0, ',', '.') }}₫
                </h2>

                <div class="fs-6 text-secondary mb-4">
                    {!! nl2br(e($product->description)) !!}
                </div>
                
                <hr class="my-4 opacity-25">

                {{-- Form thêm vào giỏ hàng --}}
                <form action="#" method="POST" class="d-flex align-items-center">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    
                    <div class="me-3" style="width: 100px;">
                        <label for="quantity" class="form-label visually-hidden">Số lượng</label>
                        {{-- ĐÃ SỬA: max="{{ $product->stock }}" và thêm oninput để chặn nhập tay quá số lượng --}}
                        <input type="number" id="quantity" name="quantity" 
                               class="form-control text-center fw-bold" 
                               value="1" min="1" max="{{ $product->stock }}"
                               {{ $product->stock <= 0 ? 'disabled' : '' }}
                               oninput="validity.valid||(value=''); if(parseInt(value) > parseInt(max)) value = max; if(parseInt(value) < parseInt(min)) value = min;">
                    </div>

                    {{-- Nút thêm vào giỏ hàng sẽ bị vô hiệu hóa nếu hết hàng --}}
                    <button type="submit" class="btn btn-dark btn-lg flex-grow-1" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                        <i class="bi bi-cart-plus me-2"></i> 
                        {{ $product->stock > 0 ? 'Thêm vào giỏ hàng' : 'Tạm hết hàng' }}
                    </button>
                </form>

                 <div class="mt-4 d-flex gap-3 text-muted small">
                    <span class="cursor-pointer"><i class="bi bi-heart me-1"></i> Yêu thích</span>
                    <span class="cursor-pointer"><i class="bi bi-share me-1"></i> Chia sẻ</span>
                 </div>
            </div>
        </div>
    </div>

    {{-- Sản phẩm liên quan --}}
    @if($relatedProducts->count() > 0)
    <div class="mt-5 pt-4 border-top">
        <h3 class="fw-bold mb-4">Sản phẩm tương tự</h3>
        
        <div class="row g-3 row-cols-2 row-cols-md-4">
            @foreach($relatedProducts as $item)
            <div class="col">
                <div class="card h-100 border-0 shadow-sm product-card-hover">
                    <div class="position-relative overflow-hidden">
                        {{-- Link sản phẩm liên quan (Dùng slug) --}}
                        <a href="{{ route('client.product.detail', ['slug' => $item->slug]) }}">
                             <img src="{{ $item->image ? asset('storage/'.$item->image) : 'https://via.placeholder.com/300x300' }}" 
                                  class="card-img-top" 
                                  alt="{{ $item->name }}"
                                  style="height: 200px; object-fit: cover;">
                        </a>
                    </div>
                    <div class="card-body p-3 d-flex flex-column">
                        <h6 class="card-title text-truncate-2 mb-2" style="min-height: 40px;">
                            {{-- Link tên sản phẩm (Dùng slug) --}}
                            <a href="{{ route('client.product.detail', ['slug' => $item->slug]) }}" class="text-decoration-none text-dark stretched-link">
                                {{ $item->name }}
                            </a>
                        </h6>
                        <p class="card-text text-danger fw-bold mt-auto mb-0">
                            {{ number_format($item->price, 0, ',', '.') }}₫
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('head')
<style>
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .product-card-hover {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .product-card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .cursor-pointer { cursor: pointer; }
</style>
@endpush

@endsection