@extends('client.layout')

@section('title', 'Wishlist')

@section('content')
<section class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Wishlist</h1>
        @auth
            <form method="post" action="{{ route('client.wishlist.sync') }}">@csrf<button class="text-sm underline">Đồng bộ với tài khoản</button></form>
        @endauth
    </div>
    @if($products->isEmpty())
        <div class="bg-white border rounded-xl p-6 text-sm text-gray-600">Chưa có sản phẩm yêu thích.</div>
    @else
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($products as $p)
                <div class="border rounded-xl p-3">
                    <a href="{{ route('client.product.detail', ['slug'=>$p->slug]) }}" class="block">
                        <img class="w-full aspect-[4/3] object-cover rounded" src="{{ $p->image ? asset('storage/'.$p->image) : 'https://via.placeholder.com/400x300?text=No+Image' }}" alt="{{ $p->name }}">
                        <div class="mt-2 text-sm font-semibold line-clamp-2">{{ $p->name }}</div>
                        <div class="text-red-600 font-bold">{{ number_format($p->price, 0, ',', '.') }} đ</div>
                    </a>
                    <form method="post" action="{{ route('client.wishlist.toggle') }}" class="mt-2">@csrf<input type="hidden" name="product_id" value="{{ $p->id }}"><button class="text-xs underline">Bỏ yêu thích</button></form>
                </div>
            @endforeach
        </div>
    @endif
</section>
@endsection


