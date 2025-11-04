<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('client.cart.index')->with('error', 'Giỏ hàng trống.');
        }
        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['qty']);
        $coupon = $request->session()->get('coupon');
        return view('client.checkout.index', compact('cart','subtotal','coupon'));
    }

    public function placeOrder(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('client.cart.index')->with('error', 'Giỏ hàng trống.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email',
            'address' => 'required|string|max:255',
            'note' => 'nullable|string',
        ]);

        $orderId = DB::transaction(function () use ($cart, $data) {
            $total = collect($cart)->sum(fn($i) => $i['price'] * $i['qty']);
            $order = Order::create([
                'user_id' => auth()->id(),
                'status' => 'pending',
                'total_price' => $total,
                'discount_amount' => 0,
                'final_total' => $total,
                'shipping_address' => $data['address'],
                'note' => $data['note'] ?? null,
                'customer_name' => $data['name'] ?? null,
                'customer_phone' => $data['phone'] ?? null,
                'customer_email' => $data['email'] ?? null,
                'payment_method' => 'cod',
            ]);
            foreach ($cart as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['qty'],
                ]);
            }
            return $order->id;
        });

        $request->session()->forget('cart');
        return redirect()->route('client.checkout.success')->with('order_id', $orderId);
    }

    public function success(Request $request)
    {
        $orderId = session('order_id');
        return view('client.checkout.success', compact('orderId'));
    }

    public function shippingFee(Request $request)
    {
        $province = (string) $request->query('province', '');
        $district = (string) $request->query('district', '');
        $base = in_array(strtolower($province), ['hcm','hồ chí minh','ho chi minh','hn','hà nội','ha noi']) ? 30000 : 50000;
        $centerDistricts = ['q1','quan 1','quận 1','ba đình','hoàn kiếm'];
        if (in_array(mb_strtolower($district), $centerDistricts, true)) {
            $base = max(0, $base - 5000);
        }
        return response()->json(['fee' => $base]);
    }
}


