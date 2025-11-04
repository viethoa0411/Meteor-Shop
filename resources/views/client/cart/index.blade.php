@extends('client.layout')

@section('title', 'Giỏ hàng')

@section('content')
<section class="max-w-7xl mx-auto px-4 py-6 md:py-10">
    <h1 class="text-2xl font-bold mb-6">Giỏ hàng</h1>

    @if(empty($cart))
        <div class="text-gray-600">Giỏ hàng trống. <a class="text-blue-600" href="{{ route('client.product.list') }}">Tiếp tục mua sắm</a></div>
    @else
        <form method="post" action="{{ route('client.cart.update') }}">
            @csrf
            <div class="overflow-hidden rounded-xl border border-gray-200">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left p-3">Sản phẩm</th>
                            <th class="text-center p-3 w-32">Số lượng</th>
                            <th class="text-right p-3 w-32">Đơn giá</th>
                            <th class="text-right p-3 w-32">Tạm tính</th>
                            <th class="p-3 w-12"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cart as $key => $item)
                            <tr class="border-t">
                                <td class="p-3">
                                    <div class="flex items-center gap-3">
                                        <img class="w-16 h-16 rounded object-cover" src="{{ $item['image'] ? asset('storage/'.$item['image']) : 'https://via.placeholder.com/64x64?text=No+Image' }}" alt="{{ $item['name'] }}">
                                        <a href="{{ route('client.product.detail', $item['slug']) }}" class="font-semibold text-gray-800 hover:text-blue-600">{{ $item['name'] }}</a>
                                    </div>
                                </td>
                                <td class="p-3 text-center">
                                    <input type="number" min="1" name="items[{{ $key }}]" value="{{ $item['qty'] }}" class="w-20 border rounded px-2 py-1 text-center" />
                                </td>
                                <td class="p-3 text-right">{{ number_format($item['price'], 0, ',', '.') }} đ</td>
                                <td class="p-3 text-right">{{ number_format($item['price']*$item['qty'], 0, ',', '.') }} đ</td>
                                <td class="p-3 text-center">
                                    <button class="text-red-600" title="Xoá" name="key" value="{{ $key }}" formaction="{{ route('client.cart.remove') }}" formmethod="post">×</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between mt-6">
                <a href="{{ route('client.product.list') }}" class="text-blue-600">Tiếp tục mua sắm</a>
                <div class="flex items-center gap-3">
                    <button class="px-4 py-2 border rounded" formaction="{{ route('client.cart.clear') }}" formmethod="post">@csrf Làm trống giỏ</button>
                    <button class="btn-primary" type="submit">Cập nhật giỏ</button>
                </div>
            </div>
        </form>

        <div class="mt-8 ml-auto max-w-sm">
            <div class="border rounded-xl p-5 bg-white space-y-4">
                <form class="flex items-center gap-2" method="post" action="{{ route('client.cart.applyCoupon') }}">
                    @csrf
                    <input name="coupon" placeholder="Mã giảm giá" class="flex-1 border rounded-lg px-3 py-2" />
                    <button class="px-4 py-2 rounded-lg bg-gray-900 text-white">Áp dụng</button>
                </form>
                <div class="flex items-center justify-between text-sm">
                    <div>Tạm tính</div>
                    <div class="font-semibold">{{ number_format($total, 0, ',', '.') }} đ</div>
                </div>
                <a href="{{ route('client.checkout.index') }}" class="btn-primary w-full inline-flex justify-center">Thanh toán</a>
            </div>
        </div>
    @endif
</section>
@endsection


