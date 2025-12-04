<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{

    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('client.login');
        }

        $cartModel = Cart::with(['items.product.category', 'items.variant'])
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->first();

        $items = $cartModel ? $cartModel->items : collect();

        $cart = [];
        foreach ($items as $item) {
            $product = $item->product;
            $variant = $item->variant;
            $size = null;
            if ($variant && $variant->length && $variant->width && $variant->height) {
                $size = $variant->length . 'x' . $variant->width . 'x' . $variant->height;
            }
            $maxStock = 0;
            if ($variant) {
                $maxStock = $variant->stock ?? 0;
            } else {
                $maxStock = $product->stock ?? 0;
            }
            $cart[$item->id] = [
                'product_id' => $product ? $product->id : null,
                'variant_id' => $variant ? $variant->id : null,
                'name' => $product ? $product->name : '',
                'price' => (float) $item->price,
                'quantity' => (int) $item->quantity,
                'color' => $item->color ?? ($variant ? $variant->color_name : null),
                'size' => $item->size ?? $size,
                'image' => $product ? $product->image : null,
                'category' => $product ? $product->category : null,
                'max_stock' => $maxStock,
            ];
        }

        $productIds = collect($cart)->pluck('product_id')->filter()->unique()->values();
        $suggestedProducts = collect();
        if ($productIds->isNotEmpty()) {
            $categories = collect($cart)->pluck('category.id')->filter()->unique();
            if ($categories->isNotEmpty()) {
                $suggestedProducts = Product::whereIn('category_id', $categories)
                    ->whereNotIn('id', $productIds)
                    ->latest()
                    ->take(8)
                    ->get();
            }
        }

        $total = 0;
        foreach ($cart as $ci) {
            $total += $ci['quantity'] * $ci['price'];
        }

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
        $variantIdInput = $request->variant_id;

        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Sản phẩm không tồn tại']);
        }

        // --- 1. XÁC ĐỊNH TỒN KHO THỰC TẾ ---
        $currentStock = 0;
        $variantId = null;
        $price = $product->price;

        if ($product->variants->count() > 0) {
            if (!empty($variantIdInput)) {
                $variant = ProductVariant::where('id', $variantIdInput)
                    ->where('product_id', $productId)
                    ->first();
            }
            if (!isset($variant) || !$variant) {
                $variant = $product->variants()
                    ->when($color, fn($q) => $q->where('color_name', $color))
                    ->when($size, function ($q) use ($size) {
                        [$l, $w, $h] = explode('x', $size);
                        return $q->where('length', $l)
                            ->where('width', $w)
                            ->where('height', $h);
                    })
                    ->first();
            }

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
        $cartModel = Cart::firstOrCreate([
            'user_id' => Auth::id(),
            'status' => 'active',
        ], [
            'total_price' => 0,
        ]);

        $existingItem = CartItem::where('cart_id', $cartModel->id)
            ->where('product_id', $product->id)
            ->when($variantId, fn($q) => $q->where('variant_id', $variantId))
            ->when(!$variantId, fn($q) => $q->whereNull('variant_id'))
            ->first();

        $currentQtyInCart = $existingItem ? $existingItem->quantity : 0;

        // --- 3. SO SÁNH VỚI TỒN KHO ---
        if (($currentQtyInCart + $quantity) > $currentStock) {
            return response()->json([
                'status' => 'error', 
                'message' => "Chỉ còn $currentStock sản phẩm trong kho. Bạn đã có $currentQtyInCart trong giỏ."
            ]);
        }

        // --- 4. THÊM VÀO GIỎ ---
        if ($existingItem) {
            $existingItem->quantity += $quantity;
            $existingItem->price = $price;
            $existingItem->subtotal = $existingItem->quantity * $price;
            $existingItem->save();
        } else {
            $newItem = CartItem::create([
                'cart_id' => $cartModel->id,
                'product_id' => $product->id,
                'variant_id' => $variantId,
                'color' => $color,
                'size' => $size,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $quantity * $price,
            ]);
        }

        $cartModel->recalculateTotals();

        $cartCount = CartItem::where('cart_id', $cartModel->id)->sum('quantity');

        return response()->json(['status' => 'success', 'cartCount' => (int) $cartCount]);
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

        $id = $request->id;
        $type = $request->type;

        $cartItem = CartItem::whereHas('cart', function ($q) {
                $q->where('user_id', Auth::id())->where('status', 'active');
            })->where('id', $id)->first();

        if (!$cartItem) {
            return response()->json(['status' => 'error', 'message' => 'Sản phẩm không tìm thấy'], 404);
        }

        if ($type == 'plus') {
            // --- KIỂM TRA TỒN KHO TRƯỚC KHI TĂNG ---
            $product = Product::find($cartItem->product_id);
            $realStock = 0;

            if($product) {
                if($cartItem->variant_id) {
                    $variant = ProductVariant::find($cartItem->variant_id);
                    $realStock = $variant ? $variant->stock : 0;
                } else {
                    $realStock = $product->stock;
                }
            }

            if (($cartItem->quantity + 1) > $realStock) {
                 return response()->json([
                    'status' => 'error', 
                    'message' => "Kho chỉ còn $realStock sản phẩm!"
                ]);
            }

            $cartItem->quantity++;

        } elseif ($type == 'minus' && $cartItem->quantity > 1) {
            $cartItem->quantity--;
        }
        $cartItem->subtotal = $cartItem->quantity * $cartItem->price;
        $cartItem->save();

        $cartModel = $cartItem->cart;
        $cartModel->recalculateTotals();

        return response()->json([
            'status' => 'success',
            'quantity' => (int) $cartItem->quantity,
            'subtotal' => (float) ($cartItem->quantity * $cartItem->price),
            'total' => (float) $cartModel->total_price,
            'max_stock' => isset($realStock) ? (int) $realStock : null,
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
        $cartItem = CartItem::whereHas('cart', function ($q) {
                $q->where('user_id', Auth::id())->where('status', 'active');
            })->where('id', $id)->first();

        if ($cartItem) {
            $cartModel = $cartItem->cart;
            $cartItem->delete();
            $cartModel->recalculateTotals();
            $cartCount = CartItem::where('cart_id', $cartModel->id)->sum('quantity');
            return response()->json(['status' => 'success', 'cartCount' => (int) $cartCount, 'total' => (float) $cartModel->total_price]);
        }

        return response()->json(['status' => 'success', 'cartCount' => 0, 'total' => 0]);
    }


    private function getTotal()
    {
        if (!Auth::check()) {
            return 0;
        }
        $cartModel = Cart::where('user_id', Auth::id())->where('status', 'active')->first();
        return $cartModel ? (float) $cartModel->total_price : 0;
    }
}
