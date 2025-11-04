@extends('client.layout')

@section('title', 'Đặt hàng thành công')

@section('content')
<section class="max-w-3xl mx-auto px-4 py-16 text-center">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 text-green-600 text-2xl mb-4">✓</div>
    <h1 class="text-2xl font-bold mb-2">Cảm ơn bạn đã đặt hàng!</h1>
    <p class="text-gray-600 mb-6">Mã đơn của bạn: <span class="font-semibold">#{{ $orderId }}</span></p>
    <a class="btn-primary px-6 py-3 rounded-lg" href="{{ route('client.home') }}">Về trang chủ</a>
</section>
@endsection

@extends('client.layout')

@section('title', 'Đặt hàng thành công')

@section('content')
<section class="max-w-3xl mx-auto px-4 py-16 text-center">
    <div class="text-3xl font-bold text-green-600 mb-3">Đặt hàng thành công</div>
    <div class="text-gray-600">Mã đơn của bạn: <strong>#{{ $orderId }}</strong></div>
    <a href="{{ route('client.home') }}" class="btn-primary mt-6 inline-block">Tiếp tục mua sắm</a>
    <a href="{{ route('client.product.list') }}" class="ml-3 underline text-blue-600">Xem sản phẩm</a>
}</section>
@endsection


