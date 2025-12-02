<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{

    public function index()
    {
        $cart = session()->get('cart', []);

        $productIds = collect($cart)->pluck('product_id')->filter()->unique()->values();
        $products = Product::whereIn('id', $productIds)->with('category')->get()->keyBy('id');

        foreach ($cart as $id => &$item) {
            $product = $products->get($item['product_id']);
            if ($product) {
                $item['image'] = $product->image;
                $item['category'] = $product->category;
            } else {
                $item['image'] = null;
                $item['category'] = null;
            }

            $item['color'] = $item['color'] ?? null;
            $item['size'] = $item['size'] ?? null;
        }

        $suggestedProducts = collect();
        if ($productIds->isNotEmpty()) {
            $categories = $products->pluck('category_id')->filter()->unique();

            if ($categories->isNotEmpty()) {
                $suggestedProducts = Product::whereIn('category_id', $categories)
                    ->whereNotIn('id', $productIds)
                    ->latest()
                    ->take(8)
                    ->get();
            }
        }

        $total = $this->getTotal();

        return view('client.cart', [
            'cart' => $cart,
            'total' => $total,
            'suggestedProducts' => $suggestedProducts,
        ]);
    }


    public function add(Request $request)
    {
        if (! Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng.',
                'requires_auth' => true,
                'redirect' => route('client.login')
            ], 401);
        }

        $productId = $request->product_id;
        $quantity = (int)($request->quantity ?? 1);
        $color = $request->color;
        $size = $request->size;

        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Sản phẩm không tồn tại']);
        }

        // --- 1. XÁC ĐỊNH TỒN KHO THỰC TẾ ---
        $currentStock = 0;
        $variantId = null;
        $price = $product->price;

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
                return response()->json(['status' => 'error', 'message' => 'Biến thể không tồn tại hoặc đã hết hàng']);
            }

            $price = $variant->price ?? $product->price;
            $variantId = $variant->id;
            $currentStock = $variant->stock; // Lấy tồn kho của variant
        } else {
            // Sản phẩm đơn giản
            $currentStock = $product->stock; // Lấy tồn kho của product cha
            $variantId = null;
        }

        // --- 2. KIỂM TRA SỐ LƯỢNG ĐÃ CÓ TRONG GIỎ ---
        $cart = session()->get('cart', []);
        $key = $variantId ?? $product->id; // Key để phân biệt item trong giỏ

        $currentQtyInCart = isset($cart[$key]) ? $cart[$key]['quantity'] : 0;

        // --- 3. SO SÁNH VỚI TỒN KHO ---
        if (($currentQtyInCart + $quantity) > $currentStock) {
            return response()->json([
                'status' => 'error', 
                'message' => "Chỉ còn $currentStock sản phẩm trong kho. Bạn đã có $currentQtyInCart trong giỏ."
            ]);
        }

        // --- 4. THÊM VÀO GIỎ ---
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
        if (! Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vui lòng đăng nhập để cập nhật giỏ hàng.',
                'requires_auth' => true,
                'redirect' => route('client.login')
            ], 401);
        }

        $id = $request->id; // Đây là key trong session cart
        $type = $request->type;

        $cart = session()->get('cart', []);

        if (!isset($cart[$id])) {
            return response()->json(['status' => 'error', 'message' => 'Sản phẩm không tìm thấy'], 404);
        }

        if ($type == 'plus') {
            // --- KIỂM TRA TỒN KHO TRƯỚC KHI TĂNG ---
            $item = $cart[$id];
            $product = Product::find($item['product_id']);
            $realStock = 0;

            if($product) {
                if($item['variant_id']) {
                    // Tìm variant theo ID
                    $variant = ProductVariant::find($item['variant_id']);
                    $realStock = $variant ? $variant->stock : 0;
                } else {
                    // Sản phẩm đơn giản
                    $realStock = $product->stock;
                }
            }

            // Nếu cộng thêm 1 mà vượt quá tồn kho -> Báo lỗi
            if (($cart[$id]['quantity'] + 1) > $realStock) {
                 return response()->json([
                    'status' => 'error', 
                    'message' => "Kho chỉ còn $realStock sản phẩm!"
                ]);
            }

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
        if (! Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vui lòng đăng nhập để cập nhật giỏ hàng.',
                'requires_auth' => true,
                'redirect' => route('client.login')
            ], 401);
        }

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