<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ClientWallet;
use App\Models\WalletTransaction;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\PromotionService;

class CheckoutController extends Controller
{
    /**
     * ========================================
     * CLIENT: TRANG CHECKOUT - THANH TOÁN
     * ========================================
     * Trang checkout - Thanh toán
     * URL: /checkout?product_id=xxx&variant_id=xxx&qty=1&type=buy_now (mua ngay)
     * URL: /checkout?type=cart (từ giỏ hàng)
     *
     * Chức năng:
     * - Kiểm tra đăng nhập
     * - Xử lý checkout từ giỏ hàng (nhiều sản phẩm)
     * - Xử lý checkout mua ngay (1 sản phẩm)
     * - Kiểm tra tồn kho
     * - Lưu thông tin vào session
     */
    public function index(Request $request)
    {
        // Kiểm tra đăng nhập
        if (!Auth::check()) {
            return redirect()->route('client.login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục đặt hàng');
        }

        $type = $request->get('type', 'buy_now');
        $user = Auth::user();

        // Xử lý checkout từ mua lại đơn hàng (reorder)
        $checkoutSession = session('checkout_session');
        if (
            $checkoutSession &&
            isset($checkoutSession['type']) &&
            $checkoutSession['type'] === 'reorder'
        ) {
            $cartItems    = $checkoutSession['items'];
            $subtotal     = $checkoutSession['subtotal'];
            $checkoutData = $checkoutSession;

            return view('client.checkout.cart', compact('cartItems', 'subtotal', 'user', 'checkoutData'));
        }

        // Xử lý checkout từ giỏ hàng
        if ($type === 'cart') {
            $cartItems = [];
            $subtotal  = 0;

            if (Auth::check()) {

                // User đã đăng nhập: lấy từ DB Cart
                $selectedIds = $request->query('selected', []);
                if ($selectedIds) {
                    // Nếu có selected[], lấy những CartItem được chọn
                    $cartItemModels = CartItem::with(['product', 'variant'])
                        ->whereIn('id', $selectedIds)
                        ->whereHas('cart', function ($q) {
                            $q->where('user_id', Auth::id())
                                ->where('status', 'active');
                        })
                        ->get();
                } else {

                    // Nếu không có selected[], lấy toàn bộ cart items

                    $cartModel = Cart::with(['items.product', 'items.variant'])
                        ->where('user_id', Auth::id())
                        ->where('status', 'active')
                        ->first();
                    $cartItemModels = $cartModel ? $cartModel->items : collect();
                }
                if ($cartItemModels->isEmpty()) {
                    return redirect()->route('cart.index')
                        ->with('error', 'Giỏ hàng của bạn đang trống');
                }
                foreach ($cartItemModels as $cartItem) {
                    $product = $cartItem->product;
                    $variant = $cartItem->variant;
                    if (!$product) continue;
                    $stock = $product->stock ?? 0;
                    $price = $cartItem->price;
                    if ($variant) {
                        $stock = $variant->stock ?? 0;
                        $price = $variant->price ?? $product->price;
                    }
                    // Kiểm tra tồn kho
                    if ($stock < $cartItem->quantity) {
                        return redirect()->route('cart.index')
                            ->with('error', "Sản phẩm '{$product->name}' không đủ tồn kho. Tồn kho hiện tại: {$stock}");
                    }
                    $itemSubtotal = $price * $cartItem->quantity;
                    $subtotal += $itemSubtotal;
                    $cartItems[] = [
                        'key'        => $cartItem->id,
                        'product_id' => $cartItem->product_id,
                        'variant_id' => $cartItem->variant_id,
                        'name'       => $product->name,
                        'price'      => $price,
                        'quantity'   => $cartItem->quantity,
                        'subtotal'   => $itemSubtotal,
                        'image'      => $product->image,
                        'color'      => $cartItem->color ?? ($variant?->color_name ?? null),
                        'size'       => $cartItem->size ?? null,
                        'product'    => $product,
                        'variant'    => $variant,
                    ];
                }
            } else {
                // User chưa đăng nhập: lấy từ session
                $cart = session()->get('cart', []);
                if (empty($cart)) {
                    return redirect()->route('cart.index')
                        ->with('error', 'Giỏ hàng của bạn đang trống');
                }
                foreach ($cart as $key => $item) {
                    $product = Product::find($item['product_id']);
                    if (!$product) continue;
                    $stock = $product->stock ?? 0;
                    $price = $item['price'];
                    $variant = null;
                    if (!empty($item['variant_id'])) {
                        $variant = ProductVariant::find($item['variant_id']);
                        if ($variant) {
                            $stock = $variant->stock ?? 0;
                            $price = $variant->price ?? $product->price;
                        }
                    }
                    if ($stock < $item['quantity']) {
                        return redirect()->route('cart.index')
                            ->with('error', "Sản phẩm '{$item['name']}' không đủ tồn kho. Tồn kho hiện tại: {$stock}");
                    }
                    $itemSubtotal = $price * $item['quantity'];
                    $subtotal += $itemSubtotal;
                    $cartItems[] = [

                        'key'        => $key,
                        'product_id' => $item['product_id'],
                        'variant_id' => $item['variant_id'] ?? null,
                        'name'       => $item['name'],
                        'price'      => $price,
                        'quantity'   => $item['quantity'],
                        'subtotal'   => $itemSubtotal,
                        'image'      => $item['image'] ?? $product->image,
                        'color'      => $item['color'] ?? null,
                        'size'       => $item['size'] ?? null,
                        'product'    => $product,
                        'variant'    => $variant,
                    ];
                }
            }
            if (empty($cartItems)) {
                return redirect()->route('cart.index')
                    ->with('error', 'Giỏ hàng của bạn đang trống');
            }
            // Lưu thông tin vào session
            $checkoutData = [
                'type'       => 'cart',
                'items'      => $cartItems,
                'subtotal'   => $subtotal,
                'created_at' => now(),
            ];

            session(['checkout_session' => $checkoutData]);
            return view('client.checkout.cart', compact('cartItems', 'subtotal', 'user', 'checkoutData'));
        }

        // Xử lý checkout mua ngay (1 sản phẩm)
        $productId = $request->get('product_id');
        $variantId = $request->get('variant_id');
        $qty       = max(1, (int) $request->get('qty', 1));

        if (!$productId) {
            return redirect()->route('client.home')
                ->with('error', 'Không tìm thấy sản phẩm');
        }

        // Lấy sản phẩm
        $product = Product::with(['variants', 'images'])->findOrFail($productId);

        // Lấy variant nếu có
        $variant = null;
        $price   = $product->price;
        $stock   = $product->stock ?? 0;

        if ($variantId) {
            $variant = ProductVariant::where('id', $variantId)
                ->where('product_id', $productId)
                ->first();

            if ($variant) {
                // Ưu tiên giá của variant, nếu không có thì dùng giá sản phẩm
                $price = $variant->price ?? $product->price;
                $stock = $variant->stock ?? 0;
            }
        } else {
            // Nếu không có variant, dùng giá và stock của sản phẩm
            $price = $product->price;
            $stock = $product->stock ?? 0;
        }

        // Kiểm tra tồn kho
        if ($stock < $qty) {
            return redirect()->back()
                ->with('error', 'Số lượng sản phẩm không đủ. Tồn kho hiện tại: ' . $stock);
        }

        // Lưu thông tin vào session
        $checkoutData = [
            'type'       => $type, // thường là buy_now
            'product_id' => $productId,
            'variant_id' => $variantId,
            'quantity'   => $qty,
            'price'      => $price,
            'subtotal'   => $price * $qty,
            'created_at' => now(),
        ];

        session(['checkout_session' => $checkoutData]);

        return view('client.checkout.index', compact('product', 'variant', 'qty', 'price', 'stock', 'user', 'checkoutData'));
    }

    /**
     * ========================================
     * CLIENT: CHỌN ĐỊA CHỈ NGƯỜI NHẬN + VALIDATE
     * ========================================
     */
    public function process(Request $request)
    {
        // Kiểm tra đăng nhập
        if (!Auth::check()) {
            return redirect()->route('client.login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục đặt hàng');
        }

        $request->validate([
            'customer_name'    => 'required|string|max:255|regex:/^[\p{L}\s]+$/u',
            'customer_phone'   => 'required|string|regex:/^[0-9]{10,11}$/',
            'customer_email'   => 'required|email|max:255',
            'shipping_city'    => 'required|string|max:255',
            'shipping_district' => 'required|string|max:255',
            'shipping_ward'    => 'required|string|max:255',
            'shipping_address' => 'required|string|max:500',
            'shipping_method'  => 'required|string|in:standard,express,fast',
            'payment_method'   => 'required|string|in:cash,wallet', // 'cash' là phương thức COD
            'notes'            => 'nullable|string|max:1000',
            'quantity'         => 'nullable|integer|min:1',
        ], [
            'customer_name.required'     => 'Vui lòng nhập họ tên',
            'customer_name.regex'        => 'Họ tên chỉ được chứa chữ cái và khoảng trắng',
            'customer_phone.required'    => 'Vui lòng nhập số điện thoại',
            'customer_phone.regex'       => 'Số điện thoại phải có 10-11 chữ số',
            'customer_email.required'    => 'Vui lòng nhập email',
            'customer_email.email'       => 'Email không hợp lệ',
            'shipping_city.required'     => 'Vui lòng chọn tỉnh/thành phố',
            'shipping_district.required' => 'Vui lòng chọn quận/huyện',
            'shipping_ward.required'     => 'Vui lòng chọn phường/xã',
            'shipping_address.required'  => 'Vui lòng nhập địa chỉ chi tiết',
        ]);

        // Lấy checkout session
        $checkoutSession = session('checkout_session');
        if (!$checkoutSession) {
            return redirect()->route('client.home')
                ->with('error', 'Phiên đặt hàng đã hết hạn. Vui lòng thử lại.');
        }

        // Cập nhật số lượng cho buy_now (nếu có)
        if ($checkoutSession['type'] === 'buy_now' && $request->has('quantity')) {
            $newQuantity = max(1, (int) $request->quantity);

            // Kiểm tra lại tồn kho với số lượng mới
            $product = Product::findOrFail($checkoutSession['product_id']);
            $stock   = $product->stock ?? 0;

            if (!empty($checkoutSession['variant_id'])) {
                $variant = ProductVariant::where('id', $checkoutSession['variant_id'])
                    ->where('product_id', $product->id)
                    ->first();
                if ($variant) {
                    $stock = $variant->stock ?? 0;
                }
            }

            if ($stock < $newQuantity) {
                return redirect()->back()
                    ->with('error', 'Số lượng sản phẩm không đủ. Tồn kho hiện tại: ' . $stock)
                    ->withInput();
            }

            // Cập nhật số lượng và subtotal
            $checkoutSession['quantity'] = $newQuantity;
            $checkoutSession['subtotal'] = $checkoutSession['price'] * $newQuantity;
        }

        // =========================================================================
        // [YÊU CẦU 1] KIỂM TRA GIỚI HẠN COD TRÊN 10 TRIỆU
        $paymentMethod = $request->payment_method;
        $subtotal = $checkoutSession['subtotal'] ?? 0;

        if ($paymentMethod === 'cash' && $subtotal > 10000000) {
            // Nếu là COD VÀ tổng tiền > 10 triệu
            return redirect()->back()
                ->with('error', 'Đơn hàng có tổng giá trị trên 10.000.000 VNĐ không được phép thanh toán COD (Tiền mặt). Vui lòng chọn phương thức khác.')
                ->withInput(); // Thêm withInput() để giữ lại dữ liệu form
        }
        // =========================================================================

        // Tính phí vận chuyển
        // [ĐÃ SỬA LỖI TYPEERROR] Đổi tên hàm gọi nội bộ
        $shippingFee = $this->_getShippingFeeLogic(
            $request->shipping_method,
            $request->shipping_city,
            $checkoutSession['subtotal']
        );

        // Lấy số tiền giảm (nếu đã áp dụng promotion trước đó)
        $discountAmount = isset($checkoutSession['discount_amount'])
            ? (float) $checkoutSession['discount_amount']
            : 0;

        // Cập nhật checkout session với thông tin form
        $checkoutSession['customer_name']     = $request->customer_name;
        $checkoutSession['customer_phone']    = $request->customer_phone;
        $checkoutSession['customer_email']    = $request->customer_email;
        $checkoutSession['shipping_city']     = $request->shipping_city;
        $checkoutSession['shipping_district'] = $request->shipping_district;
        $checkoutSession['shipping_ward']     = $request->shipping_ward;
        $checkoutSession['shipping_address']  = $request->shipping_address;
        $checkoutSession['shipping_method']   = $request->shipping_method;
        $checkoutSession['payment_method']    = $request->payment_method;
        $checkoutSession['shipping_fee']      = $shippingFee;
        $checkoutSession['notes']             = $request->notes;
        $checkoutSession['discount_amount']   = $discountAmount;

        // Tổng cuối cùng sau giảm giá + phí ship
        $checkoutSession['final_total'] = max(
            0,
            $checkoutSession['subtotal'] - $discountAmount + $shippingFee
        );

        // Kiểm tra số dư ví nếu thanh toán bằng ví
        if ($request->payment_method === 'wallet') {
            $wallet = ClientWallet::where('user_id', Auth::id())->first();
            if (!$wallet || !$wallet->hasEnoughBalance($checkoutSession['final_total'])) {
                return back()->with('error', 'Số dư ví không đủ để thanh toán. Vui lòng nạp thêm tiền hoặc chọn phương thức thanh toán khác.');
            }
        }

        session(['checkout_session' => $checkoutSession]);

        return redirect()->route('client.checkout.confirm');
    }
    /**
     * ========================================
     * CLIENT: TRANG XÁC NHẬN ĐƠN HÀNG
     * ========================================
     */
    public function confirm()
    {
        // Kiểm tra đăng nhập
        if (!Auth::check()) {
            return redirect()->route('client.login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục đặt hàng');
        }

        $checkoutSession = session('checkout_session');

        if (!$checkoutSession) {
            return redirect()->route('client.home')
                ->with('error', 'Phiên đặt hàng đã hết hạn. Vui lòng thử lại.');
        }

        // Nếu là checkout từ cart hoặc reorder
        if (in_array($checkoutSession['type'], ['cart', 'reorder'])) {
            return view('client.checkout.confirm-cart', compact('checkoutSession'));
        }

        // Lấy lại sản phẩm và variant cho buy_now
        $product = Product::with(['variants', 'images'])->findOrFail($checkoutSession['product_id']);
        $variant = null;

        if (!empty($checkoutSession['variant_id'])) {
            $variant = ProductVariant::find($checkoutSession['variant_id']);
        }

        return view('client.checkout.confirm', compact('checkoutSession', 'product', 'variant'));
    }

    /**
     * ========================================
     * CLIENT: TẠO ĐƠN HÀNG + THANH TOÁN
     * ========================================
     */
    public function createOrder()
    {
        // Kiểm tra đăng nhập
        if (!Auth::check()) {
            return redirect()->route('client.login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục đặt hàng');
        }

        $checkoutSession = session('checkout_session');

        if (!$checkoutSession) {
            return redirect()->route('client.home')
                ->with('error', 'Phiên đặt hàng đã hết hạn. Vui lòng thử lại.');
        }

        DB::beginTransaction();
        try {
            // Tạo mã đơn hàng
            $orderCode = 'DH' . strtoupper(Str::random(8)) . Carbon::now()->format('Ymd');

            // Tạo order
            $order = Order::create([
                'user_id'          => Auth::id(),
                'order_code'       => $orderCode,
                'customer_name'    => $checkoutSession['customer_name'],
                'customer_phone'   => $checkoutSession['customer_phone'],
                'customer_email'   => $checkoutSession['customer_email'],
                'shipping_city'    => $checkoutSession['shipping_city'],
                'shipping_district' => $checkoutSession['shipping_district'],
                'shipping_ward'    => $checkoutSession['shipping_ward'],
                'shipping_address' => $checkoutSession['shipping_address'],
                'shipping_method'  => $checkoutSession['shipping_method'],
                'shipping_fee'     => $checkoutSession['shipping_fee'],
                'payment_method'   => $checkoutSession['payment_method'],
                'sub_total'        => $checkoutSession['subtotal'],
                // tổng cuối cùng khách phải trả (sau giảm giá + phí ship)
                'total_price'      => $checkoutSession['final_total'],
                'discount_amount'  => $checkoutSession['discount_amount'] ?? 0,
                'promotion_id'     => $checkoutSession['promotion']['promotion_id'] ?? null,
                'final_total'      => $checkoutSession['final_total'],
                'order_status'     => 'pending',
                'payment_status'   => 'pending',
                'notes'            => $checkoutSession['notes'] ?? null,
                'order_date'       => now(),
            ]);

            // Xử lý checkout từ cart hoặc reorder (nhiều sản phẩm)
            if (in_array($checkoutSession['type'], ['cart', 'reorder']) && isset($checkoutSession['items'])) {
                foreach ($checkoutSession['items'] as $item) {
                    // Kiểm tra lại tồn kho
                    $product = Product::findOrFail($item['product_id']);
                    $stock   = $product->stock ?? 0;
                    $variant = null;

                    if (!empty($item['variant_id'])) {
                        $variant = ProductVariant::where('id', $item['variant_id'])
                            ->where('product_id', $product->id)
                            ->first();
                        if ($variant) {
                            $stock = $variant->stock ?? 0;
                        }
                    }

                    if ($stock < $item['quantity']) {
                        DB::rollBack();
                        return redirect()->back()
                            ->with('error', "Sản phẩm '{$item['name']}' không đủ tồn kho. Tồn kho hiện tại: {$stock}");
                    }

                    // Lấy product với images để lưu snapshot
                    $productForSnapshot = Product::with('images')->findOrFail($product->id);

                    // Tạo order detail
                    OrderDetail::create([
                        'order_id'     => $order->id,
                        'product_id'   => $productForSnapshot->id,
                        'product_name' => $item['name'],
                        'variant_id'   => $variant ? $variant->id : null,
                        'variant_name' => $variant ? (
                            ($variant->color_name ?? '') .
                            ($variant->length && $variant->width && $variant->height
                                ? ' - ' . $variant->length . 'x' . $variant->width . 'x' . $variant->height . ' cm'
                                : '')
                        ) : null,
                        'variant_sku'  => $variant ? $variant->sku : null,
                        'quantity'     => $item['quantity'],
                        'price'        => $item['price'],
                        'subtotal'     => $item['subtotal'],
                        'total_price'  => $item['subtotal'],
                        'image_path'   => $item['image']
                            ?? ($productForSnapshot->image
                                ?? ($productForSnapshot->images->first()->image ?? null)),
                    ]);

                    // Cập nhật tồn kho
                    if ($variant) {
                        $variant->decrement('stock', $item['quantity']);
                    } else {
                        $product->decrement('stock', $item['quantity']);
                    }
                }

                // Xóa giỏ hàng sau khi checkout (nếu có model Cart)
                $userCart = \App\Models\Cart::with('items')
                    ->where('user_id', Auth::id())
                    ->where('status', 'active')
                    ->first();

                if ($userCart) {
                    foreach ($userCart->items as $ci) {
                        $ci->delete();
                    }
                    $userCart->status      = 'checked_out';
                    $userCart->total_price = 0;
                    $userCart->save();
                }
            } else {
                // Xử lý checkout mua ngay (1 sản phẩm)
                $product = Product::findOrFail($checkoutSession['product_id']);
                $stock   = $product->stock ?? 0;
                $variant = null;

                if (!empty($checkoutSession['variant_id'])) {
                    $variant = ProductVariant::where('id', $checkoutSession['variant_id'])
                        ->where('product_id', $product->id)
                        ->first();
                    if ($variant) {
                        $stock = $variant->stock ?? 0;
                    }
                }

                if ($stock < $checkoutSession['quantity']) {
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', 'Số lượng sản phẩm không đủ. Tồn kho hiện tại: ' . $stock);
                }

                // Lấy lại product với images để lưu snapshot
                $productForSnapshot = Product::with('images')->findOrFail($product->id);

                // Tạo order detail với snapshot đầy đủ
                OrderDetail::create([
                    'order_id'     => $order->id,
                    'product_id'   => $productForSnapshot->id,
                    'product_name' => $productForSnapshot->name,
                    'variant_id'   => $variant ? $variant->id : null,
                    'variant_name' => $variant ? (
                        ($variant->color_name ?? '') .
                        ($variant->length && $variant->width && $variant->height
                            ? ' - ' . $variant->length . 'x' . $variant->width . 'x' . $variant->height . ' cm'
                            : '')
                    ) : null,
                    'variant_sku'  => $variant ? $variant->sku : null,
                    'quantity'     => $checkoutSession['quantity'],
                    'price'        => $checkoutSession['price'],
                    'subtotal'     => $checkoutSession['subtotal'],
                    'total_price'  => $checkoutSession['subtotal'],
                    'image_path'   => $productForSnapshot->image
                        ?? ($productForSnapshot->images->first()->image ?? null),
                ]);

                // Cập nhật tồn kho
                if ($variant) {
                    $variant->decrement('stock', $checkoutSession['quantity']);
                } else {
                    $product->decrement('stock', $checkoutSession['quantity']);
                }
            }

            // Xử lý thanh toán bằng ví
            if ($checkoutSession['payment_method'] === 'wallet') {
                $clientWallet = ClientWallet::where('user_id', Auth::id())->first();

                if (!$clientWallet || !$clientWallet->hasEnoughBalance($checkoutSession['final_total'])) {
                    throw new \Exception('Số dư ví không đủ để thanh toán');
                }

                $balanceBefore = $clientWallet->balance;
                $clientWallet->subtractBalance($checkoutSession['final_total']);

                // Tạo transaction log
                WalletTransaction::create([
                    'wallet_id'      => $clientWallet->id,
                    'user_id'        => Auth::id(),
                    'type'           => 'payment',
                    'amount'         => $checkoutSession['final_total'],
                    'balance_before' => $balanceBefore,
                    'balance_after'  => $clientWallet->balance,
                    'description'    => 'Thanh toán đơn hàng ' . $orderCode,
                    'order_id'       => $order->id,
                ]);

                // Cập nhật trạng thái đơn hàng thành đã thanh toán
                $order->update(['payment_status' => 'paid']);
            }
            // Nếu là tiền mặt, có thể giữ payment_status = pending để shipper thu hộ

            // Tăng lượt dùng mã khuyến mãi nếu có
            if (!empty($checkoutSession['promotion']['promotion_id'])) {
                try {
                    $service = new PromotionService();
                    $service->incrementUsage(Auth::id(), $checkoutSession['promotion']['promotion_id']);
                } catch (\Throwable $e) {
                    // Không dừng tạo đơn nếu tăng lượt dùng thất bại
                }
            }

            DB::commit();

            // Xóa checkout session
            session()->forget('checkout_session');

            return redirect()->route('client.checkout.success', ['order_code' => $orderCode])
                ->with('success', 'Đặt hàng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi tạo đơn hàng: ' . $e->getMessage());
        }
    }

    /**
     * Áp dụng mã khuyến mãi
     */
    public function applyPromotion(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['ok' => false, 'error' => 'Vui lòng đăng nhập'], 401);
        }

        $request->validate(['code' => 'required|string']);

        $checkoutSession = session('checkout_session');
        if (!$checkoutSession) {
            return response()->json(['ok' => false, 'error' => 'Phiên đặt hàng đã hết hạn'], 400);
        }

        $items = [];
        if ($checkoutSession['type'] === 'cart' && isset($checkoutSession['items'])) {
            $items = $checkoutSession['items'];
        } else {
            $product = Product::find($checkoutSession['product_id']);
            if (!$product) {
                return response()->json(['ok' => false, 'error' => 'Sản phẩm không hợp lệ'], 400);
            }

            $items = [[
                'product_id' => $product->id,
                'product'    => $product,
                'subtotal'   => $checkoutSession['subtotal'],
            ]];
        }

        $service = new PromotionService();
        $result  = $service->validateAndCalculate(
            Auth::user(),
            $request->code,
            $items,
            $checkoutSession['subtotal']
        );

        if (!$result['ok']) {
            return response()->json(['ok' => false, 'error' => $result['error']], 400);
        }

        $promotionData = [
            'code'            => $result['code'],
            'promotion_id'    => $result['promotion_id'],
            'discount_amount' => $result['discount'],
        ];

        $checkoutSession['promotion']       = $promotionData;
        $checkoutSession['discount_amount'] = $promotionData['discount_amount'];

        $shippingFee = $checkoutSession['shipping_fee'] ?? 0;
        $checkoutSession['final_total'] = max(
            0,
            $checkoutSession['subtotal'] - $promotionData['discount_amount'] + $shippingFee
        );

        session(['checkout_session' => $checkoutSession]);

        return response()->json([
            'ok'          => true,
            'promotion'   => $promotionData,
            'final_total' => $checkoutSession['final_total'],
        ]);
    }

    /**
     * Trang đặt hàng thành công
     */
    public function success($orderCode)
    {
        // Kiểm tra đăng nhập
        if (!Auth::check()) {
            return redirect()->route('client.login')
                ->with('error', 'Vui lòng đăng nhập để xem đơn hàng');
        }

        $order = Order::with(['items.product', 'user'])
            ->where('order_code', $orderCode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Lấy sản phẩm liên quan
        $relatedProducts = Product::where('category_id', $order->items->first()->product->category_id ?? null)
            ->where('id', '!=', $order->items->first()->product_id ?? 0)
            ->take(4)
            ->get();

        return view('client.checkout.success', compact('order', 'relatedProducts'));
    }
    /**
     * CLIENT: Tính phí vận chuyển (AJAX endpoint)
     * Hàm PUBLIC được gọi qua Route.
     */
    public function calculateShippingFee(Request $request)
    {
        // 1. Kiểm tra đăng nhập
        if (!Auth::check()) {
            return response()->json(['ok' => false, 'error' => 'Vui lòng đăng nhập để tính phí'], 401);
        }

        // 2. Validation đầu vào
        $request->validate([
            'shipping_method' => 'required|string|in:standard,express,fast',
            'shipping_city'   => 'required|string|max:255',
            'subtotal'        => 'required|numeric|min:0',
        ]);

        try {
            // 3. Gọi hàm private để tính logic
            $shippingFee = $this->_getShippingFeeLogic(
                $request->shipping_method,
                $request->shipping_city,
                (float) $request->subtotal
            );

            // 4. Trả về JSON thành công
            return response()->json([
                'ok'            => true,
                'shipping_fee'  => $shippingFee,
                'message'       => 'Tính phí vận chuyển thành công'
            ]);
        } catch (\Exception $e) {
            // Trả về JSON lỗi 500 nếu có lỗi không mong muốn
            return response()->json([
                'ok'    => false,
                'error' => 'Lỗi tính phí server: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Logic tính phí vận chuyển cốt lõi (PRIVATE)
     * PHẢI ĐẶT SAU HÀM calculateShippingFee
     */
    private function _getShippingFeeLogic($method, $city, $subtotal) // [ĐÃ ĐỔI TÊN]
    {
        // 1. Xác định phí cơ bản
        $baseFee = 30000;

        switch ($method) {
            case 'express':
                $baseFee = 50000;
                break;
            case 'fast':
                $baseFee = 70000;
                break;
            case 'standard':
            default:
                $baseFee = 30000;
                break;
        }

        // 2. Kiểm tra MIỄN PHÍ SHIP (Ngưỡng 1.000.000 VNĐ)
        // Giả định logic của bạn là 1.000.000 VNĐ, không phải 500.000 VNĐ như trong snippet cũ
        if ($subtotal >= 1000000) {
            return 0;
        }

        $shippingFee = $baseFee;

        // 3. Xử lý PHỤ PHÍ KHU VỰC: +10.000 VNĐ nếu không phải Hà Nội hoặc Hồ Chí Minh
        $normalizedCity = Str::slug($city, ' ');
        $isMajorCity = Str::contains($normalizedCity, ['ha noi', 'ho chi minh']);

        if (!$isMajorCity) {
            $shippingFee += 10000;
        }

        return $shippingFee;
    }
}