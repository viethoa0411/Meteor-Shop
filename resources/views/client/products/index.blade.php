@extends('client.layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-center">Danh mục nổi bật</h2>
    @foreach ($categories as $category)
        <div class="mb-5">
            <h3 style="font-weight: 600">
                {{ $category->name }}
                <a href="{{ route('client.product.category', $category->slug) }}" style="font-size: 14px; color: rgb(0, 132, 221); margin-left: 10px ">
                    Xem tất cả →
                </a>
            </h3>
            <div class="grid-products" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:24px; width:100%;">
                @foreach ($category->latestProducts as $p )
                    <a href="{{ route('client.product.detail', ['slug' => $p->slug]) }}" 
                       class="product-card"
                       style="background:#fff;border-radius:8px;padding:10px;text-align:center;transition:transform .3s;">
                        <img src="{{ $p->image ? asset('storage/'.$p->image) : 'https://via.placeholder.com/400x400?text=No+Image' }}" 
                             alt="{{ $p->name }}" 
                             style="width:100%;border-radius:6px;">
                        <divclass="product-name" style="margin-top:8px;font-weight:600;color:#111">{{ $p->name }}</divclass=>
                        <div class="product-price" style="color:#d41;font-weight:600;">{{ number_format($p->price, 0, ',', '.') }} đ</div>
                    </a>                    
                @endforeach
            </div>
        </div>
        <hr>
    @endforeach
</div>

@endsection
