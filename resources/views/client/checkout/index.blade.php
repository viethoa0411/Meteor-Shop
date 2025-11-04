@extends('client.layout')

@section('title', 'Thanh toán')

@section('content')
<section class="max-w-7xl mx-auto px-4 py-6 md:py-10">
    <h1 class="text-2xl font-bold mb-6">Thanh toán</h1>

    @if(empty($cart))
        <div class="text-gray-600">Giỏ hàng trống. <a class="text-blue-600" href="{{ route('client.product.list') }}">Tiếp tục mua sắm</a></div>
    @else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <form class="lg:col-span-2 space-y-4" method="post" action="{{ route('client.checkout.place') }}">
            @csrf
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h2 class="font-semibold mb-4">Thông tin giao hàng</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Họ tên</label>
                        <input name="name" class="w-full border rounded-lg px-3 py-2" required />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Số điện thoại</label>
                        <input name="phone" class="w-full border rounded-lg px-3 py-2" required />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Email</label>
                        <input name="email" type="email" class="w-full border rounded-lg px-3 py-2" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm text-gray-600 mb-1">Địa chỉ</label>
                        <input name="address" class="w-full border rounded-lg px-3 py-2" required />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm text-gray-600 mb-1">Ghi chú</label>
                        <textarea name="note" class="w-full border rounded-lg px-3 py-2" rows="3"></textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h2 class="font-semibold mb-4">Phương thức thanh toán</h2>
                <div class="space-y-3">
                    <label class="flex items-center gap-3">
                        <input type="radio" name="payment" value="cod" checked>
                        <span>Tiền mặt (COD)</span>
                    </label>
                    <label class="flex items-center gap-3">
                        <input type="radio" name="payment" value="bank">
                        <span>Chuyển khoản</span>
                    </label>
                </div>
            </div>

            <button class="btn-primary px-6 py-3 rounded-lg" type="submit">Đặt hàng</button>
        </form>

        <aside class="lg:col-span-1">
            <div class="bg-white border border-gray-200 rounded-xl p-5 sticky top-24">
                <h3 class="font-semibold mb-4">Tóm tắt đơn hàng</h3>
                <div class="space-y-3 text-sm mb-4 max-h-[280px] overflow-auto">
                    @foreach($cart as $item)
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <img class="w-10 h-10 rounded object-cover" src="{{ $item['image'] ? asset('storage/'.$item['image']) : 'https://via.placeholder.com/40x40' }}" alt="{{ $item['name'] }}">
                                <div class="truncate">{{ $item['name'] }}</div>
                                <div class="text-gray-500">× {{ $item['qty'] }}</div>
                            </div>
                            <div class="font-medium">{{ number_format($item['price']*$item['qty'], 0, ',', '.') }} đ</div>
                        </div>
                    @endforeach
                </div>
                <div class="border-t pt-3 text-sm space-y-2">
                    <div class="flex items-center justify-between">
                        <span>Tạm tính</span>
                        <span class="font-medium">{{ number_format($subtotal, 0, ',', '.') }} đ</span>
                    </div>
                    @if($coupon)
                    <div class="flex items-center justify-between text-green-700">
                        <span>Giảm ({{ $coupon['code'] }})</span>
                        <span>-</span>
                    </div>
                    @endif
                    <div class="flex items-center justify-between text-base pt-2">
                        <span class="font-semibold">Tổng</span>
                        <span class="font-semibold">{{ number_format($subtotal, 0, ',', '.') }} đ</span>
                    </div>
                </div>
            </div>
        </aside>
    </div>
    @endif
</section>
@endsection

@extends('client.layout')

@section('title', 'Thanh toán')

@section('content')
<section class="max-w-7xl mx-auto px-4 py-6 md:py-10">
    <h1 class="text-2xl font-bold mb-6">Thanh toán</h1>

    <div class="grid md:grid-cols-12 gap-6">
        <div class="md:col-span-7">
            <form method="post" action="{{ route('client.checkout.place') }}" class="space-y-4">
                @csrf
                <div>
                    <div class="text-sm font-semibold mb-2">Thông tin nhận hàng</div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <input name="name" class="border rounded px-3 py-2" placeholder="Họ và tên" required>
                        <input name="phone" class="border rounded px-3 py-2" placeholder="Số điện thoại" required>
                        <input name="email" class="border rounded px-3 py-2 md:col-span-2" placeholder="Email (không bắt buộc)">
                        <div class="grid grid-cols-2 gap-3 md:col-span-2">
                            <input id="provinceInput" name="province" class="border rounded px-3 py-2" placeholder="Tỉnh/Thành">
                            <input id="districtInput" name="district" class="border rounded px-3 py-2" placeholder="Quận/Huyện">
                        </div>
                        <input name="address" class="border rounded px-3 py-2 md:col-span-2" placeholder="Địa chỉ nhận hàng" required>
                    </div>
                </div>
                <div>
                    <div class="text-sm font-semibold mb-2">Ghi chú</div>
                    <textarea name="note" class="border rounded px-3 py-2 w-full" rows="3" placeholder="Ghi chú cho đơn hàng"></textarea>
                </div>
                <div>
                    <div class="text-sm font-semibold mb-2">Phương thức thanh toán</div>
                    <label class="flex items-center gap-2 text-sm"><input type="radio" name="payment" value="cod" checked> <span>Thanh toán khi nhận hàng (COD)</span></label>
                    <label class="flex items-center gap-2 text-sm"><input type="radio" name="payment" value="online"> <span>Thanh toán Online (VNPay/Momo)</span></label>
                </div>
                <button class="btn-primary">Đặt hàng</button>
            </form>
        </div>
        <div class="md:col-span-5">
            <div class="border rounded-xl p-4">
                <div class="text-sm font-semibold mb-2">Đơn hàng</div>
                <div class="space-y-3 max-h-72 overflow-auto pr-1">
                    @foreach($cart as $item)
                        <div class="flex items-center gap-3">
                            <img class="w-12 h-12 rounded object-cover" src="{{ $item['image'] ? asset('storage/'.$item['image']) : 'https://via.placeholder.com/48x48?text=No+Image' }}" alt="{{ $item['name'] }}">
                            <div class="text-sm flex-1">
                                <div class="font-semibold text-gray-800">{{ $item['name'] }}</div>
                                <div class="text-gray-500">x{{ $item['qty'] }}</div>
                            </div>
                            <div class="text-sm font-semibold">{{ number_format($item['price']*$item['qty'], 0, ',', '.') }} đ</div>
                        </div>
                    @endforeach
                </div>
                <form action="{{ route('client.cart.applyCoupon') }}" method="POST" class="flex items-center gap-2 mt-3">
                    @csrf
                    <input name="coupon" class="flex-1 border rounded px-3 py-2 text-sm" placeholder="Mã giảm (FIXED10/SHIPFREE)">
                    <button class="px-3 py-2 border rounded text-sm">Áp dụng</button>
                </form>
                <hr class="my-3">
                <div class="text-sm space-y-2">
                    <div class="flex items-center justify-between"><span>Tạm tính</span><span id="ckSubtotal">{{ number_format(($subtotal ?? ($total ?? 0)), 0, ',', '.') }} đ</span></div>
                    <div class="flex items-center justify-between"><span>Phí vận chuyển</span><span id="shipFee">—</span></div>
                    <div class="flex items-center justify-between"><span>Giảm giá</span><span id="ckDiscount">@if(($coupon['type']??'')==='percent') -{{ $coupon['value'] }}% @elseif(($coupon['type']??'')==='shipping_free') Miễn phí ship @else — @endif</span></div>
                    <div class="flex items-center justify-between font-bold text-base"><span>Tổng cộng</span><span id="grandTotal">—</span></div>
                </div>
            </div>
        </div>
    </div>
</section>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const feeEl = document.getElementById('shipFee');
  const totalEl = document.getElementById('grandTotal');
  const province = document.getElementById('provinceInput');
  const district = document.getElementById('districtInput');
  const subtotal = {{ (int) ($subtotal ?? ($total ?? 0)) }};
  const coupon = @json($coupon ?? null);
  function calc(){
    const params = new URLSearchParams({province: province?.value||'', district: district?.value||''});
    fetch(`{{ route('client.shipping.fee') }}?`+params.toString()).then(r=>r.json()).then(data=>{
      let fee = data.fee || 0;
      if (coupon && coupon.type === 'shipping_free') fee = 0;
      feeEl.textContent = fee.toLocaleString('vi-VN') + ' đ';
      let discount = 0;
      if (coupon && coupon.type === 'percent') discount = Math.round(subtotal * coupon.value/100);
      const grand = Math.max(0, subtotal + fee - discount);
      totalEl.textContent = grand.toLocaleString('vi-VN') + ' đ';
    }).catch(()=>{
      feeEl.textContent = '—'; totalEl.textContent = (subtotal).toLocaleString('vi-VN') + ' đ';
    });
  }
  calc();
  province?.addEventListener('input', ()=> setTimeout(calc, 100));
  district?.addEventListener('input', ()=> setTimeout(calc, 100));
});
</script>
@endpush
@endsection


