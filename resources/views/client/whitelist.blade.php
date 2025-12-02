@extends('client.layouts.app')

@section('title', 'Sản phẩm yêu thích')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4">Sản phẩm yêu thích</h2>

        @if ($items->isEmpty())
            <p>Bạn chưa thêm sản phẩm nào vào danh sách yêu thích.</p>
            <a href="{{ route('client.products.index') }}" class="btn btn-primary mt-2">Xem sản phẩm</a>
        @else
            <div class="row g-3">
                @foreach ($items as $item)
                    @php $product = $item->product; @endphp
                    @if ($product)
                        <div class="col-6 col-md-3">
                            <div class="card h-100">
                                <a href="{{ route('client.product.detail', $product->slug) }}"
                                    class="text-decoration-none text-dark">
                                    <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/300x300?text=No+Image' }}"
                                        class="card-img-top" alt="{{ $product->name }}"
                                        style="object-fit:cover; aspect-ratio:1/1;">
                                    <div class="card-body">
                                        <h6 class="card-title" style="min-height: 40px;">{{ $product->name }}</h6>
                                        <p class="mb-0 text-danger fw-semibold">
                                            {{ number_format($product->price, 0, ',', '.') }} đ
                                        </p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
@endsection


