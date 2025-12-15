<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ClientWallet;
use App\Models\WalletTransaction;
use App\Models\OrderPayment;
use App\Models\MomoPayment;
use App\Models\ShippingSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\PromotionService;
use App\Helpers\ShippingHelper;

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
        \Illuminate\Support\Facades\Log::info('Checkout Index called', $request->all());

        // Kiểm tra đăng nhập
        if (!Auth::check()) {
            \Illuminate\Support\Facades\Log::info('Checkout: User not logged in');
            return redirect()->route('client.login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục đặt hàng');
        }

        $type = $request->get('type', 'buy_now');
        $user = Auth::user();
        $shippingSettings = ShippingSetting::getSettings();

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

            return view('client.checkout.cart', compact('cartItems', 'subtotal', 'user', 'checkoutData', 'shippingSettings'));
        }

        // Xử lý checkout từ giỏ hàng
        if ($type === 'cart') {
            // 1. Lấy giỏ hàng
            $cart = [];
            $selectedIds = $request->input('selected', []);

            if (Auth::check()) {
                // User đã đăng nhập: Lấy từ DB
                $cartModel = \App\Models\Cart::with(['items.product', 'items.variant'])
                    ->where('user_id', Auth::id())
                    ->where('status', 'active')
                    ->first();

                if ($cartModel) {
                    foreach ($cartModel->items as $ci) {
                        $cart[$ci->id] = [
                            'product_id' => $ci->product_id,
                            'variant_id' => $ci->variant_id,
                            'name'       => $ci->product->name ?? 'Sản phẩm',
                            'price'      => $ci->price,
                            'quantity'   => $ci->quantity,
                            'image'      => $ci->product->image ?? null,
                            'color'      => $ci->color,
                            'size'       => $ci->size,
                        ];
                    }
                }
            } else {
                // Fallback (nếu sau này cho phép guest checkout): Lấy từ Session
                $cart = session()->get('cart', []);
            }

            \Illuminate\Support\Facades\Log::info('Checkout Cart', ['cart_count' => count($cart), 'selected' => $selectedIds]);

            if (empty($cart)) {
                return redirect()->route('cart.index')
                    ->with('error', 'Giỏ hàng của bạn đang trống');
            }

            if (empty($selectedIds)) {
                return redirect()->route('cart.index')
                    ->with('error', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán');
            }

            // Kiểm tra tồn kho cho tất cả sản phẩm trong giỏ
            $cartItems = [];
            $subtotal  = 0;

            foreach ($cart as $key => $item) {
                // Nếu có danh sách chọn, chỉ lấy những item được chọn
                if (!empty($selectedIds) && !in_array($key, $selectedIds)) {
                    continue;
                }

                $product = Product::find($item['product_id']);
                if (!$product) {
                    continue; // Bỏ qua sản phẩm không tồn tại
                }

                $stock   = $product->stock ?? 0;
                $price   = $item['price'];
                $variant = null;

                if (!empty($item['variant_id'])) {
                    $variant = ProductVariant::find($item['variant_id']);
                    if ($variant) {
                        $stock = $variant->stock ?? 0;
                        $price = $variant->price ?? $product->price;
                    }
                }

                // Kiểm tra tồn kho
                if ($stock < $item['quantity']) {
                    \Illuminate\Support\Facades\Log::info('Checkout: Out of stock', ['item' => $item['name'], 'stock' => $stock, 'qty' => $item['quantity']]);
                    return redirect()->route('cart.index')
                        ->with('error', "Sản phẩm '{$item['name']}' không đủ tồn kho. Tồn kho hiện tại: {$stock}");
                }

                $itemSubtotal = $price * $item['quantity'];
                $subtotal    += $itemSubtotal;

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

            // Lưu thông tin vào session
            $checkoutData = [
                'type'       => 'cart',
                'items'      => $cartItems,
                'subtotal'   => $subtotal,
                'created_at' => now(),
            ];

            session(['checkout_session' => $checkoutData]);

            return view('client.checkout.cart', compact('cartItems', 'subtotal', 'user', 'checkoutData', 'shippingSettings'));
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

        return view('client.checkout.index', compact('product', 'variant', 'qty', 'price', 'stock', 'user', 'checkoutData', 'shippingSettings'));
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
            'payment_method'   => 'required|string|in:cash,wallet,momo',

            'notes'            => 'nullable|string|max:1000',
            'quantity'         => 'nullable|integer|min:1',
            'installation'     => 'nullable|boolean',
            'installation_fee' => 'nullable|numeric|min:0',
        ], [
            'customer_name.required'    => 'Vui lòng nhập họ tên',
            'customer_name.regex'       => 'Họ tên chỉ được chứa chữ cái và khoảng trắng',
            'customer_phone.required'   => 'Vui lòng nhập số điện thoại',
            'customer_phone.regex'      => 'Số điện thoại phải có 10-11 chữ số',
            'customer_email.required'   => 'Vui lòng nhập email',
            'customer_email.email'      => 'Email không hợp lệ',
            'shipping_city.required'    => 'Vui lòng chọn tỉnh/thành phố',
            'shipping_district.required' => 'Vui lòng chọn quận/huyện',


            'shipping_ward.required'    => 'Vui lòng chọn phường/xã',
            'shipping_address.required' => 'Vui lòng nhập địa chỉ chi tiết',
        ]);

        // Kiểm tra tỉnh/thành phố phải là miền Bắc
        if (!ShippingHelper::isNorthernProvince($request->shipping_city)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Hệ thống chỉ hỗ trợ giao hàng tại khu vực miền Bắc. Vui lòng chọn tỉnh/thành phố miền Bắc.');
        }

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

        // Tính phí vận chuyển
        $shippingCalculation = $this->calculateShippingTotal(
            $request->shipping_method,
            $checkoutSession
        );
        $shippingFee = $shippingCalculation['fee'];

        // Lấy phí lắp đặt
        $installationFee = 0;
        if ($request->has('installation') && $request->installation) {
            $shippingSettings = ShippingSetting::getSettings();
            $installationFee = (float)($request->installation_fee ?? $shippingSettings->installation_fee ?? 0);
        }

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
        
        \Illuminate\Support\Facades\Log::info('Process: Saved Payment Method', ['method' => $request->payment_method]);


        $checkoutSession['shipping_fee']      = $shippingFee;
        $checkoutSession['installation_fee']  = $installationFee;
        $checkoutSession['has_installation']  = $request->has('installation') && $request->installation;
        $checkoutSession['notes']             = $request->notes;
        $checkoutSession['discount_amount']   = $discountAmount;

        // Tổng cuối cùng sau giảm giá + phí ship + phí lắp đặt
        $checkoutSession['final_total'] = max(
            0,
            $checkoutSession['subtotal'] - $discountAmount + $shippingFee + $installationFee
        );

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
        
        \Illuminate\Support\Facades\Log::info('Confirm: Checkout Session', ['payment_method' => $checkoutSession['payment_method'] ?? 'N/A']);

        $shippingSettings = ShippingSetting::getSettings();


        if (!$checkoutSession) {
            return redirect()->route('client.home')
                ->with('error', 'Phiên đặt hàng đã hết hạn. Vui lòng thử lại.');
        }


        // Nếu thanh toán Momo, tạo đơn và chuyển hướng ngay (Skip confirm view)
        if (isset($checkoutSession['payment_method']) && $checkoutSession['payment_method'] === 'momo') {
            return $this->createOrder();
        }


        // Nếu là checkout từ cart hoặc reorder
        if (in_array($checkoutSession['type'], ['cart', 'reorder'])) {
            return view('client.checkout.confirm-cart', compact('checkoutSession', 'shippingSettings'));
        }

        // Lấy lại sản phẩm và variant cho buy_now
        $product = Product::with(['variants', 'images'])->findOrFail($checkoutSession['product_id']);
        $variant = null;

        if (!empty($checkoutSession['variant_id'])) {
            $variant = ProductVariant::find($checkoutSession['variant_id']);
        }

        return view('client.checkout.confirm', compact('checkoutSession', 'product', 'variant', 'shippingSettings'));
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
                'installation_fee' => $checkoutSession['installation_fee'] ?? 0,
                'payment_method'   => $checkoutSession['payment_method'],
                'sub_total'        => $checkoutSession['subtotal'],
                // tổng cuối cùng khách phải trả (sau giảm giá + phí ship + phí lắp đặt)
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
            \Illuminate\Support\Facades\Log::info('CreateOrder: Check Payment Method', ['method' => $checkoutSession['payment_method']]);


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
            // Xử lý thanh toán Momo
            elseif (isset($checkoutSession['payment_method']) && trim($checkoutSession['payment_method']) == 'momo') {
                \Illuminate\Support\Facades\Log::info('CreateOrder: Processing Momo Payment (API)');
                
                // Gọi hàm tạo thanh toán Momo
                $payUrl = $this->_createMomoPayment($order);

                if ($payUrl) {
                    // Commit transaction & Xóa session
                    DB::commit(); 
                    session()->forget('checkout_session');
                    
                    // Chuyển hướng đến trang thanh toán Momo
                    return redirect($payUrl);
                } else {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Không thể tạo giao dịch Momo. Vui lòng thử lại sau.');
                }
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
        $installationFee = $checkoutSession['installation_fee'] ?? 0;
        $checkoutSession['final_total'] = max(
            0,
            $checkoutSession['subtotal'] - $promotionData['discount_amount'] + $shippingFee + $installationFee
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
    public function success(Request $request, $orderCode)

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

        // Xử lý callback từ Momo (nếu có)
        // Lưu ý: Momo có thể không trả về accessKey trong redirectUrl, nên ta dùng accessKey từ cấu hình
        if ($request->has('partnerCode') && $request->has('requestId') && 
            $request->has('amount') && $request->has('orderId') && $request->has('orderInfo') && 
            $request->has('orderType') && $request->has('transId') && $request->has('resultCode') && 
            $request->has('message') && $request->has('payType') && $request->has('responseTime') && 
            $request->has('extraData') && $request->has('signature')) {
            
            \Illuminate\Support\Facades\Log::info('Momo Return Callback', $request->all());

            $partnerCode = $request->partnerCode;
            $requestId = $request->requestId;
            $amount = $request->amount;
            $orderId = $request->orderId;
            $orderInfo = $request->orderInfo;
            $orderType = $request->orderType;
            $transId = $request->transId;
            $resultCode = $request->resultCode;
            $message = $request->message;
            $payType = $request->payType;
            $responseTime = $request->responseTime;
            $extraData = $request->extraData;
            $signature = $request->signature;

            // Cấu hình Key (Khớp với _createMomoPayment)
            $accessKey = 'F8BBA842ECF85';
            $secretKey = 'K951B6PE1waDMi640xX08PD3vg6EkVlz';

            // Tạo chữ ký để kiểm tra
            // Thứ tự tham số khi tạo chữ ký: accessKey, amount, extraData, message, orderId, orderInfo, orderType, partnerCode, payType, requestId, responseTime, resultCode, transId
            $rawHash = "accessKey=" . $accessKey .
                   "&amount=" . $amount .
                   "&extraData=" . $extraData .
                   "&message=" . $message .
                   "&orderId=" . $orderId .
                   "&orderInfo=" . $orderInfo .
                   "&orderType=" . $orderType .
                   "&partnerCode=" . $partnerCode .
                   "&payType=" . $payType .
                   "&requestId=" . $requestId .
                   "&responseTime=" . $responseTime .
                   "&resultCode=" . $resultCode .
                   "&transId=" . $transId;

            $checkSignature = hash_hmac("sha256", $rawHash, $secretKey);

            if ($checkSignature == $signature) {
                if ($resultCode == '0') {
                     if ($order->payment_status !== 'paid') {
                         $order->payment_status = 'paid';
                         $order->save();
                         session()->flash('success', 'Thanh toán Momo thành công!');

                         // Tạo bản ghi thanh toán vào bảng order_payments
                         try {
                             OrderPayment::create([
                                 'order_id'       => $order->id,
                                 'transaction_id' => $transId,
                                 'payment_method' => 'momo',
                                 'status'         => 'paid',
                                 'amount'         => $amount,
                                 'currency'       => 'VND',
                                 'payment_data'   => json_encode($request->all()),
                                 'notes'          => 'Thanh toán qua cổng Momo',
                                 'paid_at'        => now(),
                             ]);
                         } catch (\Exception $e) {
                             \Illuminate\Support\Facades\Log::error('OrderPayment Save Error: ' . $e->getMessage());
                         }
                     }
                } else {
                     session()->flash('error', 'Thanh toán Momo thất bại: ' . $message);
                }

                // Lưu thông tin thanh toán vào bảng momo_payments
                try {
                    MomoPayment::create([
                        'order_id'      => $order->id,
                        'partner_code'  => $partnerCode,
                        'request_id'    => $requestId,
                        'order_id_momo' => $orderId, // orderId của Momo
                        'trans_id'      => $transId,
                        'pay_type'      => $payType,
                        'amount'        => $amount,
                        'result_code'   => $resultCode,
                        'message'       => $message,
                        'response_time' => $responseTime,
                        'extra_data'    => $extraData,
                        'signature'     => $signature,
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Momo Payment Save Error: ' . $e->getMessage());
                }

            } else {
                \Illuminate\Support\Facades\Log::error('Momo Signature Mismatch', ['calculated' => $checkSignature, 'received' => $signature]);
            }
        }
        // Lấy sản phẩm liên quan
        $relatedProducts = Product::where('category_id', $order->items->first()->product->category_id ?? null)
            ->where('id', '!=', $order->items->first()->product_id ?? 0)
            ->take(4)
            ->get();

        return view('client.checkout.success', compact('order', 'relatedProducts'));
    }

    /**
     * API Tính phí vận chuyển (AJAX)
     */
    public function calculateShippingFee(Request $request)
    {
        $request->validate([
            'method' => 'nullable|in:standard,express,fast',
            'quantity' => 'nullable|integer|min:1',
            'city' => 'nullable|string',
            'district' => 'nullable|string',
            'ward' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $city = $request->input('city');
        $district = $request->input('district');
        $ward = $request->input('ward');
        $address = $request->input('address');
        $method = $request->input('method', 'standard');
        $quantity = $request->input('quantity');

        $checkoutSession = session('checkout_session');
        if (!$checkoutSession) {
            return response()->json([
                'success' => false,
                'message' => 'Phiên tính phí đã hết hạn. Vui lòng tải lại trang.'
            ], 400);
        }

        // Cập nhật thông tin địa chỉ đích vào session nếu có
        if ($city) {
            $checkoutSession['shipping_city'] = trim($city);
        }
        if ($district) {
            $checkoutSession['shipping_district'] = trim($district);
        }
        if ($ward) {
            $checkoutSession['shipping_ward'] = trim($ward);
        }
        if ($address) {
            $checkoutSession['shipping_address'] = trim($address);
        }
        session(['checkout_session' => $checkoutSession]);

        // Log để debug
        Log::info('Checkout: Tính phí vận chuyển', [
            'city' => $checkoutSession['shipping_city'] ?? null,
            'district' => $checkoutSession['shipping_district'] ?? null,
            'ward' => $checkoutSession['shipping_ward'] ?? null,
            'address' => $checkoutSession['shipping_address'] ?? null,
            'method' => $method,
            'quantity' => $quantity,
        ]);

        $shippingCalculation = $this->calculateShippingTotal(
            $method,
            $checkoutSession,
            $quantity ? (int) $quantity : null
        );

        $fee = $shippingCalculation['fee'];
        $settings = $shippingCalculation['settings'];

        // Kiểm tra xem có phải miễn phí vận chuyển do đạt ngưỡng không
        $isFreeShipping = false;
        if ($fee === 0 && $shippingCalculation['standard_fee'] > 0) {
            // Nếu fee = 0 nhưng standard_fee > 0, có nghĩa là được miễn phí do đạt ngưỡng
            $isFreeShipping = true;
        } elseif ($fee === 0 && $shippingCalculation['standard_fee'] === 0) {
            // Nếu cả fee và standard_fee đều = 0, có thể do không có dữ liệu kích thước/cân nặng
            Log::warning('Checkout: Phí vận chuyển = 0, có thể do thiếu dữ liệu kích thước/cân nặng', [
                'standard_fee' => $shippingCalculation['standard_fee'],
                'surcharge' => $shippingCalculation['surcharge'],
            ]);
        }

        // Gọi hàm tính phí nội bộ
        $fee = $this->getShippingFeeValue($method, $city, $subtotal);

        return response()->json([
            'success' => true,
            'fee' => $fee,
            'fee_formatted' => number_format($fee, 0, ',', '.') . ' đ',
            'is_free_shipping' => $isFreeShipping,
            'standard_fee' => $shippingCalculation['standard_fee'],
            'surcharge' => $shippingCalculation['surcharge'],
            'method_label' => $this->getMethodLabel($method, $settings),
        ]);
    }

    /**
     * Helper tính phí vận chuyển (Internal)
     */
    private function calculateShippingTotal(string $method, array $checkoutSession, ?int $quantityOverride = null): array
    {
        $settings = ShippingSetting::getSettings();
        $items = $this->buildShippingItems($checkoutSession, $quantityOverride);
        $subtotal = $this->resolveSubtotal($checkoutSession, $quantityOverride);

        // Lấy thông tin địa chỉ đích từ checkout session
        $destinationCity = isset($checkoutSession['shipping_city']) ? trim($checkoutSession['shipping_city']) : null;
        $destinationDistrict = isset($checkoutSession['shipping_district']) ? trim($checkoutSession['shipping_district']) : null;
        $destinationWard = isset($checkoutSession['shipping_ward']) ? trim($checkoutSession['shipping_ward']) : null;
        $destinationAddress = isset($checkoutSession['shipping_address']) ? trim($checkoutSession['shipping_address']) : null;

        // Log để debug
        Log::info('Checkout: Thông tin tính phí', [
            'origin' => [
                'city' => $settings->origin_city,
                'district' => $settings->origin_district,
                'ward' => $settings->origin_ward,
                'address' => $settings->origin_address,
            ],
            'destination' => [
                'city' => $destinationCity,
                'district' => $destinationDistrict,
                'ward' => $destinationWard,
                'address' => $destinationAddress,
            ],
            'items_count' => count($items),
            'subtotal' => $subtotal,
        ]);

        $feeData = $settings->calculateShippingFee(
            $items, 
            $method, 
            $subtotal, 
            $destinationCity, 
            $destinationDistrict,
            $destinationWard,
            $destinationAddress
        );

        return [
            'fee' => $feeData['total'],
            'standard_fee' => $feeData['standard_fee'],
            'surcharge' => $feeData['surcharge'],
            'settings' => $settings,
            'subtotal' => $subtotal,
        ];
    }

    private function buildShippingItems(array $checkoutSession, ?int $quantityOverride = null): array
    {
        $items = [];

        if (in_array($checkoutSession['type'] ?? '', ['cart', 'reorder']) && isset($checkoutSession['items'])) {
            foreach ($checkoutSession['items'] as $item) {
                $product = Product::find($item['product_id'] ?? null);
                $variant = !empty($item['variant_id']) ? ProductVariant::find($item['variant_id']) : null;
                $items[] = $this->mapShippingItem($product, $variant, (int)($item['quantity'] ?? 1));
            }
        } elseif (!empty($checkoutSession['product_id'])) {
            $product = Product::find($checkoutSession['product_id']);
            $variant = !empty($checkoutSession['variant_id'])
                ? ProductVariant::where('id', $checkoutSession['variant_id'])
                    ->where('product_id', $checkoutSession['product_id'])
                    ->first()
                : null;
            $qty = $quantityOverride ?? (int)($checkoutSession['quantity'] ?? 1);
            $items[] = $this->mapShippingItem($product, $variant, $qty);
        }

        return array_values(array_filter($items));
    }

    private function mapShippingItem(?Product $product, ?ProductVariant $variant, int $quantity): ?array
    {
        if (!$product) {
            return null;
        }

        $length = $variant->length ?? $product->length ?? 0;
        $width = $variant->width ?? $product->width ?? 0;
        $height = $variant->height ?? $product->height ?? 0;
        $weight = $variant->weight ?? $product->weight ?? 0;

        return [
            'length_cm' => (float) $length,
            'width_cm' => (float) $width,
            'height_cm' => (float) $height,
            'weight_kg' => (float) $weight,
            'quantity' => max(1, $quantity),
        ];
    }

    private function resolveSubtotal(array $checkoutSession, ?int $quantityOverride = null): float
    {
        if (($checkoutSession['type'] ?? '') === 'buy_now' && $quantityOverride) {
            $price = (float) ($checkoutSession['price'] ?? 0);
            return $price * $quantityOverride;
        }

        return (float) ($checkoutSession['subtotal'] ?? 0);
    }

    private function getMethodLabel(string $method, ShippingSetting $settings): string
    {
        return match ($method) {
            'express' => $settings->express_label,
            'fast' => $settings->fast_label,
            default => 'Giao tiêu chuẩn',
        };
    }

    private function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            )
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }

    public function momo_payment(Request $request)
    {
        return redirect()->route('client.home');
    }

    public function processMomoPayment($orderCode)
    {
        if (!Auth::check()) {
            return redirect()->route('client.login');
        }

        $order = Order::where('order_code', $orderCode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($order->payment_status === 'paid') {
            return redirect()->route('client.checkout.success', ['order_code' => $orderCode])
                ->with('success', 'Đơn hàng này đã được thanh toán.');
        }

        $payUrl = $this->_createMomoPayment($order);

        if ($payUrl) {
            return redirect($payUrl);
        }

        return back()->with('error', 'Không thể tạo giao dịch Momo. Vui lòng thử lại sau.');
    }

    /**
     * Trang hiển thị mã QR thanh toán Momo (Trung gian)
     */
    public function showMomoPayment($orderCode)
    {
        // Kiểm tra đăng nhập
        if (!Auth::check()) {
            return redirect()->route('client.login');
        }

        $order = Order::where('order_code', $orderCode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Nếu đơn hàng đã thanh toán rồi thì chuyển về success
        if ($order->payment_status === 'paid') {
            return redirect()->route('client.checkout.success', ['order_code' => $orderCode]);
        }

        // Tự động chuyển hướng đến trang thanh toán Momo
        $payUrl = $this->_createMomoPayment($order);

        if ($payUrl) {
            return redirect($payUrl);
        }

        return redirect()->back()->with('error', 'Không thể tạo giao dịch Momo. Vui lòng thử lại sau.');
    }

    private function _createMomoPayment($order)
    {
        // Sử dụng endpoint create cho thanh toán Web (captureWallet)
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

        // Thông tin cấu hình từ user cung cấp
        $partnerCode = 'MOMO';
        $accessKey = 'F8BBA842ECF85';
        $secretKey = 'K951B6PE1waDMi640xX08PD3vg6EkVlz';

        $orderInfo = "Thanh toan don " . $order->order_code . "(Dung App Momo Test)";
        $amount = (string)(int)$order->final_total;
        $orderId = $order->order_code . '_' . uniqid(); // Dùng uniqid để tránh trùng lặp tốt hơn time()
        $redirectUrl = route('client.checkout.success', ['order_code' => $order->order_code]);
        $ipnUrl = "https://webhook.site/b3088a6a-2d17-4f8d-a383-71389a6c600b";
        $extraData = "";
        $requestId = time() . "";
        $requestType = "captureWallet"; // Dùng captureWallet cho thanh toán QR/App

        // Tạo chữ ký (Signature) theo chuẩn Momo
        $rawHash = "accessKey=" . $accessKey .
                   "&amount=" . $amount .
                   "&extraData=" . $extraData .
                   "&ipnUrl=" . $ipnUrl .
                   "&orderId=" . $orderId .
                   "&orderInfo=" . $orderInfo .
                   "&partnerCode=" . $partnerCode .
                   "&redirectUrl=" . $redirectUrl .
                   "&requestId=" . $requestId .
                   "&requestType=" . $requestType;

        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $data = array(
            'partnerCode' => $partnerCode,
            'partnerName' => "Test Store",
            "storeId" => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        );
        
        \Illuminate\Support\Facades\Log::info('Momo Request Data', $data);

        $result = $this->execPostRequest($endpoint, json_encode($data));
        
        \Illuminate\Support\Facades\Log::info('Momo Response', ['response' => $result]);
        
        $jsonResult = json_decode($result, true);

        if (isset($jsonResult['errorCode']) && $jsonResult['errorCode'] != 0) {
            \Illuminate\Support\Facades\Log::error('Momo Payment Error: ' . ($jsonResult['localMessage'] ?? $jsonResult['message'] ?? 'Unknown Error'));
        }

        return $jsonResult['payUrl'] ?? null;
    }
}
