<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Cart;
use App\Models\CartItem;

class CartController extends Controller
{
    /**
     * Trang giỏ hàng
     */
    public function index()
    {
        // Với user đăng nhập, sử dụng giỏ hàng trong DB để đồng bộ với header/offcanvas
        if (auth()->check()) {
            $cartModel = Cart::with(['items.product', 'items.variant'])
                ->where('user_id', auth()->id())
                ->where('status', 'active')
                ->first();

            $cart = [];
            $suggestedProducts = collect();

            if ($cartModel) {
                foreach ($cartModel->items as $ci) {
                    // Load sản phẩm kể cả đã bị xóa (soft delete)
                    $product = Product::withTrashed()->find($ci->product_id);
                    $variant = $ci->variant;
                    
                    // Kiểm tra sản phẩm đã bị xóa chưa
                    $isDeleted = $product && $product->trashed();

                    $stock = $variant ? ($variant->stock ?? 0) : ($product ? ($product->stock ?? 0) : 0);

                    $cart[$ci->id] = [
                        'product_id' => $ci->product_id,
                        'variant_id' => $ci->variant_id,
                        'name'       => $product ? $product->name : ($ci->product_id ?? 'Sản phẩm'),
                        'price'      => (float) $ci->price,
                        'quantity'   => (int) $ci->quantity,
                        'max_stock'  => $isDeleted ? 0 : (int) $stock, // Nếu đã xóa thì stock = 0
                        'color'      => $ci->color ?? ($variant->color_name ?? null),
                        'size'       => $ci->size ?? (
                            $variant && $variant->length && $variant->width && $variant->height
                                ? ($variant->length . 'x' . $variant->width . 'x' . $variant->height)
                                : null
                        ),
                        'image'      => $product?->image,
                        'category'   => $product?->category,
                        'is_deleted' => $isDeleted, // Đánh dấu sản phẩm đã bị xóa
                    ];
                }

                $productIds = collect($cart)->pluck('product_id')->filter()->unique()->values();
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
            }

            $total = (float) ($cartModel?->items->sum('subtotal') ?? 0);

            return view('client.cart', [
                'cart'             => $cart,
                'total'            => $total,
                'suggestedProducts'=> $suggestedProducts,
            ]);
        }

        // Khách chưa đăng nhập: dùng session như cũ
        $cart = session()->get('cart', []);

        foreach ($cart as $id => &$item) {
            // Load sản phẩm kể cả đã bị xóa (soft delete)
            $product = Product::withTrashed()->with('category')->find($item['product_id'] ?? null);
            $stock = 0;
            $isDeleted = false;

            if ($product) {
                $isDeleted = $product->trashed(); // Kiểm tra sản phẩm đã bị xóa chưa
                $stock = $product->stock ?? 0;
                $item['image']    = $product->image;
                $item['category'] = $product->category ?? null;
                $item['name']     = $item['name'] ?? $product->name;

                if (!empty($item['variant_id'])) {
                    $variant = ProductVariant::find($item['variant_id']);
                    if ($variant) {
                        $stock = $variant->stock ?? 0;
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

            $item['max_stock'] = $isDeleted ? 0 : $stock; // Nếu đã xóa thì stock = 0
            $item['is_deleted'] = $isDeleted; // Đánh dấu sản phẩm đã bị xóa

            $item['color'] = $item['color'] ?? null;
            $item['size']  = $item['size'] ?? null;
        }
        unset($item);

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

        // --- 2. LƯU GIỎ HÀNG TUỲ THEO TÌNH TRẠNG ĐĂNG NHẬP ---
        if (auth()->check()) {
            // Dùng giỏ hàng trong DB cho user đăng nhập
            $cartModel = Cart::firstOrCreate(
                ['user_id' => auth()->id(), 'status' => 'active'],
                ['total_price' => 0]
            );

            // Tìm item trùng (product + variant)
            $existing = $cartModel->items()
                ->where('product_id', $productId)
                ->where('variant_id', $variantId)
                ->first();

            $currentQtyInCart = $existing ? (int) $existing->quantity : 0;

            // --- Limit logic: Max 10 products (REMOVED) ---
            // --- Limit logic: Max 100 million total (REMOVED) ---
            $newTotal = $cartModel->total_price + ($quantity * $price);

            $targetQty = min($currentQtyInCart + $quantity, $currentStock);

            try {
                if ($existing) {
                    $existing->quantity = $targetQty;
                    $existing->price    = $price;
                    $existing->subtotal = (float) $existing->quantity * (float) $existing->price;
                    $existing->save();
                } else {
                    if ($currentStock <= 0) {
                        return response()->json([
                            'status'  => 'error',
                            'message' => 'Sản phẩm đã hết hàng.',
                        ]);
                    }
                    $item = new CartItem([
                        'product_id' => $product->id,
                        'variant_id' => $variantId,
                        'color'      => $color ?? ($variant->color_name ?? null),
                        'size'       => $size ?? (
                            isset($variant) && $variant->length && $variant->width && $variant->height
                                ? $variant->length . 'x' . $variant->width . 'x' . $variant->height
                                : null
                        ),
                        'quantity'   => min($quantity, $currentStock),
                        'price'      => $price,
                        'subtotal'   => (float) min($quantity, $currentStock) * (float) $price,
                    ]);
                    $cartModel->items()->save($item);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Lỗi khi thêm vào giỏ: ' . $e->getMessage(),
                ]);
            }

            $cartModel->recalculateTotals();
            $cartCount = (int) $cartModel->items()->sum('quantity');

            return response()->json([
                'status'    => 'success',
                'cartCount' => $cartCount,
            ]);
        }

        // Khách: giỏ hàng trong session
        $cart = session()->get('cart', []);

        $key = $variantId
            ? 'p' . $productId . '_v' . $variantId
            : 'p' . $productId;

        $currentQtyInCart = isset($cart[$key]) ? (int)$cart[$key]['quantity'] : 0;

        // --- Limit logic: Max 10 products (REMOVED) ---
        // --- Limit logic: Max 100 million total (REMOVED) ---
        $currentTotal = $this->getTotal();

        $targetQty = min($currentQtyInCart + $quantity, $currentStock);

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = $targetQty;
            $cart[$key]['price']     = $price;
        } else {
            if ($currentStock <= 0) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Sản phẩm đã hết hàng.',
                ]);
            }
            $cart[$key] = [
                'product_id' => $product->id,
                'variant_id' => $variantId,
                'name'       => $product->name,
                'price'      => $price,
                'quantity'   => min($quantity, $currentStock),
                'color'      => $color ?? ($variant->color_name ?? null),
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
        $id   = $request->id;   // key trong session cart hoặc id CartItem khi đăng nhập
        $type = $request->type; // plus | minus

        if (auth()->check()) {
            $cartModel = Cart::where('user_id', auth()->id())
                ->where('status', 'active')
                ->first();
            if (!$cartModel) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Giỏ hàng không tồn tại',
                ], 404);
            }

            $ci = $cartModel->items()->where('id', $id)->first();
            if (!$ci) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Sản phẩm không tìm thấy',
                ], 404);
            }

            $product  = Product::find($ci->product_id);
            $realStock = 0;
            if ($product) {
                if (!empty($ci->variant_id)) {
                    $variant = ProductVariant::find($ci->variant_id);
                    $realStock = (int)($variant->stock ?? 0);
                } else {
                    $realStock = (int)($product->stock ?? 0);
                }
            }

            if ($type === 'plus') {
                // --- Limit logic: Max 3 products (REMOVED) ---
                // --- Limit logic: Max 100 million total (REMOVED) ---

                if (($ci->quantity + 1) > $realStock) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => "Kho chỉ còn $realStock sản phẩm.",
                        'max_stock' => $realStock,
                    ]);
                }
                $ci->quantity = $ci->quantity + 1;
            } elseif ($type === 'minus' && $ci->quantity > 1) {
                $ci->quantity = $ci->quantity - 1;
            }

            $ci->subtotal = (float) $ci->quantity * (float) $ci->price;
            $ci->save();
            $cartModel->recalculateTotals();

            return response()->json([
                'status'    => 'success',
                'quantity'  => (int)$ci->quantity,
                'subtotal'  => (float)$ci->subtotal,
                'total'     => (float)$cartModel->total_price,
                'max_stock' => $realStock ?: null,
            ]);
        }

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
                $variant = ProductVariant::find($item['variant_id']);
                $realStock = (int)($variant->stock ?? 0);
            } else {
                $realStock = (int)($product->stock ?? 0);
            }
        }

        if ($type === 'plus') {
            if (($item['quantity'] + 1) > $realStock) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Kho chỉ còn $realStock sản phẩm.",
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
        $id = $request->id;

        if (auth()->check()) {
            $cartModel = Cart::where('user_id', auth()->id())
                ->where('status', 'active')
                ->first();
            if ($cartModel) {
                $cartModel->items()->where('id', $id)->delete();
                $cartModel->recalculateTotals();
            }

            $cartCount = (int) ($cartModel?->items()->sum('quantity') ?? 0);
            return response()->json([
                'status'    => 'success',
                'cartCount' => $cartCount,
                'total'     => (float) ($cartModel?->total_price ?? 0),
            ]);
        }

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
