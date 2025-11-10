@extends('client.layouts.app')
@section('content')


<div class="container py-5">
    <h2>Danh mục: {{ $category->name }}</h2>
    <div class="row mt-4">
        @forelse($products as $product)
            <div class="col-6 col-md-3 mb-4">
                <div class="card h-100">
                     {{-- Giả sử đường dẫn ảnh lưu trong DB là 'uploads/sp1.jpg' --}}
                          <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/400x400?text=No+Image' }}" 
                             alt="{{ $product->name }}">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="text-danger fw-bold">{{ number_format($product->price, 0, ',', '.') }} đ</p>
                        <a href="{{ route('client.product.detail', ['slug' => $product->slug]) }}" class="btn btn-primary mt-auto">Xem chi tiết</a>
                    </div>
                </div>
            </div>
        @empty
            <p>Chưa có sản phẩm nào.</p>
        @endforelse
    </div>
    <div class="d-flex justify-content-center">
        {{ $products->links() }}
    </div>
</div>
@endsection