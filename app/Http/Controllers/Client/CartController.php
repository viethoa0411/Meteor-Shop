<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function getCart(Request $request): array
    {
        return $request->session()->get('cart', []);
    }

    private function putCart(Request $request, array $cart): void
    {
        $request->session()->put('cart', $cart);
    }

    public function index(Request $request)
    {
        $cart = $this->getCart($request);
        $total = collect($cart)->sum(fn($i) => $i['price'] * $i['qty']);
        return view('client.cart.index', compact('cart','total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'qty' => 'nullable|integer|min:1'
        ]);
        $qty = max(1, (int) $request->input('qty', 1));
        $product = Product::select('id','name','slug','price','image')->findOrFail($request->product_id);
        $cart = $this->getCart($request);
        $key = (string) $product->id; // extend later with variant key
        if (!isset($cart[$key])) {
            $cart[$key] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => (float) $product->price,
                'image' => $product->image,
                'qty' => 0,
            ];
        }
        $cart[$key]['qty'] += $qty;
        $this->putCart($request, $cart);
        return redirect()->route('client.cart.index')->with('success', 'Đã thêm sản phẩm vào giỏ.');
    }

    public function update(Request $request)
    {
        $request->validate(['items' => 'required|array']);
        $cart = $this->getCart($request);
        foreach ($request->items as $key => $qty) {
            if (isset($cart[$key])) {
                $q = max(1, (int) $qty);
                $cart[$key]['qty'] = $q;
            }
        }
        $this->putCart($request, $cart);
        return back()->with('success', 'Cập nhật giỏ hàng thành công.');
    }

    public function remove(Request $request)
    {
        $key = (string) $request->input('key');
        $cart = $this->getCart($request);
        unset($cart[$key]);
        $this->putCart($request, $cart);
        return back()->with('success', 'Đã xoá sản phẩm khỏi giỏ.');
    }

    public function clear(Request $request)
    {
        $this->putCart($request, []);
        return back()->with('success', 'Đã làm trống giỏ hàng.');
    }

    /**
     * Áp mã giảm giá đơn giản bằng session (demo)
     */
    public function applyCoupon(Request $request)
    {
        $request->validate(['coupon' => 'nullable|string|max:32']);
        $code = trim((string) $request->input('coupon', ''));
        if ($code === '') {
            $request->session()->forget('coupon');
            return back()->with('success', 'Đã bỏ áp mã giảm.');
        }
        $coupon = null;
        if (strcasecmp($code, 'FIXED10') === 0) {
            $coupon = ['code' => 'FIXED10', 'type' => 'percent', 'value' => 10];
        } elseif (strcasecmp($code, 'SHIPFREE') === 0) {
            $coupon = ['code' => 'SHIPFREE', 'type' => 'shipping_free', 'value' => 0];
        }
        if (!$coupon) {
            return back()->with('error', 'Mã giảm giá không hợp lệ.');
        }
        $request->session()->put('coupon', $coupon);
        return back()->with('success', 'Đã áp dụng mã: ' . $coupon['code']);
    }
}


