<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;

class CartController extends Controller
{

    public function index()
    {
        $cart = session()->get('cart', []);

        // Lấy thông tin đầy đủ từ database, bao gồm ảnh
        foreach ($cart as $id => &$item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                // Lấy ảnh từ public/storage/products
                $item['image'] = $product->image;
            } else {
                $item['image'] = null;
            }

            // Nếu chưa có color hoặc size trong session, set mặc định
            $item['color'] = $item['color'] ?? null;
            $item['size'] = $item['size'] ?? null;
        }

        $total = $this->getTotal(); // phương thức tính tổng tiền
        return view('client.cart', compact('cart', 'total'));
    }



    public function add(Request $request)
    {
        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;
        $color = $request->color;
        $size = $request->size;

        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Sản phẩm không tồn tại']);
        }

        // Nếu sản phẩm có variant
        if ($product->variants->count() > 0) {
            $variant = $product->variants()
                ->when($color, fn($q) => $q->where('color_name', $color))
                ->when($size, function ($q) use ($size) {
                    [$l, $w, $h] = explode('x', $size);
                    return $q->where('length', $l)
                        ->where('width', $w)
                        ->where('height', $h);
                })
                ->first();

            if (!$variant) {
                return response()->json(['status' => 'error', 'message' => 'Variant không tồn tại']);
            }

            $price = $variant->price ?? $product->price;
            $variantId = $variant->id;
        } else {
            $price = $product->price;
            $variantId = null;
        }

        $cart = session()->get('cart', []);

        // Key để phân biệt variant
        $key = $variantId ?? $product->id;

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $quantity;
        } else {
            $cart[$key] = [
                'product_id' => $product->id,
                'variant_id' => $variantId,
                'name' => $product->name,
                'price' => $price,
                'quantity' => $quantity,
                'color' => $color,
                'size' => $size,
                'image' => $product->image,
            ];
        }

        session()->put('cart', $cart);

        $cartCount = array_sum(array_map(fn($i) => $i['quantity'], $cart));

        return response()->json(['status' => 'success', 'cartCount' => $cartCount]);
    }

    public function updateQty(Request $request)
    {
        $id = $request->id;
        $type = $request->type;

        $cart = session()->get('cart', []);

        if (!isset($cart[$id])) {
            return response()->json(['status' => 'error'], 404);
        }

        if ($type == 'plus') {
            $cart[$id]['quantity']++;
        } elseif ($type == 'minus' && $cart[$id]['quantity'] > 1) {
            $cart[$id]['quantity']--;
        }

        session()->put('cart', $cart);

        return response()->json([
            'status' => 'success',
            'quantity' => $cart[$id]['quantity'],
            'subtotal' => $cart[$id]['quantity'] * $cart[$id]['price'],
            'total' => array_sum(array_map(fn($i) => $i['quantity'] * $i['price'], $cart))
        ]);
    }



    public function remove(Request $request)
    {
        $id = $request->id;
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        // trả về tổng số lượng còn lại
        $cartCount = array_sum(array_map(fn($i) => $i['quantity'], $cart));

        return response()->json(['status' => 'success', 'cartCount' => $cartCount]);
    }


    private function getTotal()
    {
        $cart = session()->get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['quantity'] * $item['price'];
        }

        return $total;
    }
}
