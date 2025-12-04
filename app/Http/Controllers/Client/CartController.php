<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;

class CartController extends Controller
{
    /**
     * Trang giỏ hàng
     */
    public function index()
    {
        $cart = session()->get('cart', []);

        // Lấy thêm thông tin đầy đủ từ DB cho từng item (ảnh, category, v.v.)
        foreach ($cart as $id => &$item) {
            $product = Product::with('category')->find($item['product_id'] ?? null);

            if ($product) {
                $item['image']    = $product->image;
                $item['category'] = $product->category ?? null;
                $item['name']     = $item['name'] ?? $product->name;

                // nếu có variant thì cố gắng lấy thêm thông tin biến thể
                if (!empty($item['variant_id'])) {
                    $variant = ProductVariant::find($item['variant_id']);
                    if ($variant) {
                        $item['color'] = $item['color'] ?? $variant->color_name;
                        if ($variant->length && $variant->width && $variant->height) {
                            $item['size'] = $item['size'] ?? ($variant->length . 'x' . $variant->width . 'x' . $variant->height);
                        }
                    }
                }
            } else {
                $item['image']    = $item['image'] ?? null;
                $item['category'] = $item['category'] ?? null;
            }

            // đảm bảo có color/size key
            $item['color'] = $item['color'] ?? null;
            $item['size']  = $item['size'] ?? null;
        }
        unset($item);

        // Gợi ý sản phẩm (theo category giống HEAD)
        $productIds = collect($cart)->pluck('product_id')->filter()->unique()->values();
        $suggestedProducts = collect();

        if ($productIds->isNotEmpty()) {
            $categories = collect($cart)
                ->pluck('category.id')
                ->filter()
                ->unique();

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
            'cart'             => $cart,
            'total'            => $total,
            'suggestedProducts'=> $suggestedProducts,
        ]);
    }

    /**
     * Thêm sản phẩm vào giỏ hàng (AJAX)
     */
    public function add(Request $request)
    {
        $productId      = $request->product_id;
        $quantity       = (int)($request->quantity ?? 1);
        $color          = $request->color;
        $size           = $request->size;
        $variantIdInput = $request->variant_id;

        $product = Product::with('variants')->find($productId);
        if (!$product) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Sản phẩm không tồn tại',
            ]);
        }

        // --- 1. XÁC ĐỊNH TỒN KHO THỰC TẾ & VARIANT ---
        $currentStock = 0;
        $variantId    = null;
        $price        = $product->price;

        if ($product->variants->count() > 0) {
            // Nếu truyền sẵn variant_id thì ưu tiên
            if (!empty($variantIdInput)) {
                $variant = ProductVariant::where('id', $variantIdInput)
                    ->where('product_id', $productId)
                    ->first();
            }

            // Nếu chưa tìm được variant theo id thì fallback sang color + size
            if (!isset($variant) || !$variant) {
                $variant = $product->variants()
                    ->when($color, fn($q) => $q->where('color_name', $color))
                    ->when($size, function ($q) use ($size) {
                        if (strpos($size, 'x') !== false) {
                            [$l, $w, $h] = explode('x', $size);
                            return $q->where('length', $l)
                                ->where('width', $w)
                                ->where('height', $h);
                        }
                        return $q;
                    })
                    ->first();
            }

            if (!$variant) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Biến thể không tồn tại hoặc đã hết hàng',
                ]);
            }

            $price        = $variant->price ?? $product->price;
            $variantId    = $variant->id;
            $currentStock = (int)($variant->stock ?? 0);
        } else {
            // Sản phẩm không có biến thể
            $currentStock = (int)($product->stock ?? 0);
            $variantId    = null;
        }

        // --- 2. LẤY GIỎ HÀNG TỪ SESSION ---
        $cart = session()->get('cart', []);

        // Tạo key duy nhất để phân biệt item trong giỏ
        // tránh đụng key giữa các sản phẩm/biến thể
        $key = $variantId
            ? 'p' . $productId . '_v' . $variantId
            : 'p' . $productId;

        $currentQtyInCart = isset($cart[$key]) ? (int)$cart[$key]['quantity'] : 0;

        // --- 3. SO SÁNH VỚI TỒN KHO ---
        if (($currentQtyInCart + $quantity) > $currentStock) {
            return response()->json([
                'status'  => 'error',
                'message' => "Chỉ còn $currentStock sản phẩm trong kho. Bạn đã có $currentQtyInCart trong giỏ.",
            ]);
        }

        // --- 4. THÊM HOẶC CỘNG DỒN VÀO GIỎ ---
        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $quantity;
            $cart[$key]['price']     = $price; // cập nhật giá mới nhất
        } else {
            $cart[$key] = [
                'product_id' => $product->id,
                'variant_id' => $variantId,
                'name'       => $product->name,
                'price'      => $price,
                'quantity'   => $quantity,
                'color'      => $color ?? ($variant->color_name ?? null ?? null),
                'size'       => $size ?? (
                    isset($variant) && $variant->length && $variant->width && $variant->height
                        ? $variant->length . 'x' . $variant->width . 'x' . $variant->height
                        : null
                ),
                'image'      => $product->image,
            ];
        }

        session()->put('cart', $cart);

        $cartCount = array_sum(array_map(fn($i) => (int)$i['quantity'], $cart));

        return response()->json([
            'status'    => 'success',
            'cartCount' => $cartCount,
        ]);
    }

    /**
     * Cập nhật số lượng trong giỏ
     */
    public function updateQty(Request $request)
    {
        $id   = $request->id;   // key trong session cart
        $type = $request->type; // plus | minus

        $cart = session()->get('cart', []);

        if (!isset($cart[$id])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Sản phẩm không tìm thấy',
            ], 404);
        }

        $item    = $cart[$id];
        $product = Product::find($item['product_id']);
        $realStock = 0;

        if ($product) {
            if (!empty($item['variant_id'])) {
                // Sản phẩm có biến thể
                $variant = ProductVariant::find($item['variant_id']);
                $realStock = (int)($variant->stock ?? 0);
            } else {
                // Sản phẩm đơn giản
                $realStock = (int)($product->stock ?? 0);
            }
        }

        if ($type === 'plus') {
            // Nếu cộng thêm 1 mà vượt quá tồn kho -> Báo lỗi
            if (($item['quantity'] + 1) > $realStock) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Kho chỉ còn $realStock sản phẩm!",
                    'max_stock' => $realStock,
                ]);
            }

            $item['quantity']++;
        } elseif ($type === 'minus' && $item['quantity'] > 1) {
            $item['quantity']--;
        }

        $cart[$id] = $item;
        session()->put('cart', $cart);

        $subtotal = (float)$item['quantity'] * (float)$item['price'];
        $total    = $this->getTotal();

        return response()->json([
            'status'    => 'success',
            'quantity'  => (int)$item['quantity'],
            'subtotal'  => $subtotal,
            'total'     => (float)$total,
            'max_stock' => $realStock ?: null,
        ]);
    }

    /**
     * Xoá sản phẩm khỏi giỏ
     */
    public function remove(Request $request)
    {
        $id   = $request->id;
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        $cartCount = array_sum(array_map(fn($i) => (int)$i['quantity'], $cart));
        $total     = $this->getTotal();

        return response()->json([
            'status'    => 'success',
            'cartCount' => $cartCount,
            'total'     => (float)$total,
        ]);
    }

    /**
     * Tính tổng tiền giỏ hàng
     */
    private function getTotal()
    {
        $cart  = session()->get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += (float)$item['quantity'] * (float)$item['price'];
        }

        return (float)$total;
    }
}
