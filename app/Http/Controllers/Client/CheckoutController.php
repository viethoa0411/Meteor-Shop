<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Promotion;
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

        // Xử lý checkout từ giỏ hàng
        if ($type === 'cart') {
            $cart = \App\Models\Cart::with(['items.product', 'items.variant'])
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if (!$cart || $cart->items->isEmpty()) {
                return redirect()->route('cart.index')
                    ->with('error', 'Giỏ hàng của bạn đang trống');
            }

            $cartItems = [];
            $subtotal = 0;

            foreach ($cart->items as $ci) {
                $product = $ci->product;
                if (!$product) {
                    continue;
                }

                $stock = $product->stock ?? 0;
                $price = (float) ($ci->variant ? ($ci->variant->price ?? $product->price) : $ci->price);
                $variant = $ci->variant;

                if ($variant) {
                    $stock = $variant->stock ?? 0;
                }

                if ($stock < $ci->quantity) {
                    return redirect()->route('cart.index')
                        ->with('error', "Sản phẩm '{$product->name}' không đủ tồn kho. Tồn kho hiện tại: {$stock}");
                }

                $itemSubtotal = $price * $ci->quantity;
                $subtotal += $itemSubtotal;

                $cartItems[] = [
                    'key'        => $ci->id,
                    'product_id' => $product->id,
                    'variant_id' => $variant ? $variant->id : null,
                    'name'       => $product->name,
                    'price'      => $price,
                    'quantity'   => (int) $ci->quantity,
                    'subtotal'   => $itemSubtotal,
                    'image'      => $product->image,
                    'color'      => $ci->color ?? ($variant ? $variant->color_name : null),
                    'size'       => $ci->size ?? ($variant && $variant->length && $variant->width && $variant->height ? ($variant->length.'x'.$variant->width.'x'.$variant->height) : null),
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
            'type'       => $type,
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
            'shipping_district'=> 'required|string|max:255',
            'shipping_ward'    => 'required|string|max:255',
            'shipping_address' => 'required|string|max:500',
            'shipping_method'  => 'required|string|in:standard,express,fast',
            'payment_method'   => 'required|string|in:cash,bank,momo,paypal',
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

        // Tính phí vận chuyển
        $shippingFee = $this->calculateShippingFee(
            $request->shipping_method,
            $request->shipping_city,
            $checkoutSession['subtotal']
        );

        // Cập nhật checkout session với thông tin form
        $checkoutSession['customer_name']    = $request->customer_name;
        $checkoutSession['customer_phone']   = $request->customer_phone;
        $checkoutSession['customer_email']   = $request->customer_email;
        $checkoutSession['shipping_city']    = $request->shipping_city;
        $checkoutSession['shipping_district']= $request->shipping_district;
        $checkoutSession['shipping_ward']    = $request->shipping_ward;
        $checkoutSession['shipping_address'] = $request->shipping_address;
        $checkoutSession['shipping_method']  = $request->shipping_method;
        $checkoutSession['payment_method']   = $request->payment_method;
        $checkoutSession['shipping_fee']     = $shippingFee;
        $checkoutSession['notes']            = $request->notes;

        $discountAmount = isset($checkoutSession['discount_amount'])
            ? (float) $checkoutSession['discount_amount']
            : 0;

        $checkoutSession['final_total'] = max(
            0,
            $checkoutSession['subtotal'] - $discountAmount + $shippingFee
        );

        session(['checkout_session' => $checkoutSession]);

        return redirect()->route('client.checkout.confirm');
    }

    /**
     * ========================================
     * CLIENT: TRANG XÁC NHẬN ĐƠN HÀNG + THANH TOÁN ONLINE
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

        // Tạo QR code nếu thanh toán bằng chuyển khoản
        $qrCodeUrl  = null;
        $walletInfo = null;

        if (isset($checkoutSession['payment_method']) && $checkoutSession['payment_method'] === 'bank') {
            // Lấy thông tin ví admin (ví đầu tiên có status active)
            $wallet = Wallet::where('status', 'active')->first();

            if ($wallet) {
                $walletInfo = $wallet;
                $amount     = $checkoutSession['final_total'];
                $orderCode  = 'ORDER' . strtoupper(Str::random(8));

                // Tạo QR code sử dụng API VietQR
                $bankId        = $this->getBankId($wallet->bank_name);
                $accountNumber = $wallet->bank_account;
                $template      = 'compact2'; // hoặc 'compact', 'qr_only', 'print'
                $addInfo       = urlencode("Thanh toan don hang " . $orderCode);
                $accountName   = urlencode($wallet->account_holder);

                $qrCodeUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNumber}-{$template}.png"
                    . "?amount={$amount}&addInfo={$addInfo}&accountName={$accountName}";

                // Lưu QR code URL vào session
                $checkoutSession['qr_code_url']     = $qrCodeUrl;
                $checkoutSession['temp_order_code'] = $orderCode;
                session(['checkout_session' => $checkoutSession]);
            }
        }

        // Nếu là checkout từ cart
        if ($checkoutSession['type'] === 'cart') {
            return view('client.checkout.confirm-cart', compact('checkoutSession', 'qrCodeUrl', 'walletInfo'));
        }

        // Lấy lại sản phẩm và variant cho buy_now
        $product = Product::with(['variants', 'images'])->findOrFail($checkoutSession['product_id']);
        $variant = null;
        if (!empty($checkoutSession['variant_id'])) {
            $variant = ProductVariant::find($checkoutSession['variant_id']);
        }

        return view('client.checkout.confirm', compact('checkoutSession', 'product', 'variant', 'qrCodeUrl', 'walletInfo'));
    }

    /**
     * Lấy Bank ID cho VietQR API
     */
    private function getBankId($bankName)
    {
        $bankMapping = [
            'Vietcombank'     => 'VCB',
            'Techcombank'     => 'TCB',
            'BIDV'            => 'BIDV',
            'VietinBank'      => 'CTG',
            'Agribank'        => 'AGR',
            'ACB'             => 'ACB',
            'MB Bank'         => 'MB',
            'VPBank'          => 'VPB',
            'TPBank'          => 'TPB',
            'Sacombank'       => 'STB',
            'HDBank'          => 'HDB',
            'VIB'             => 'VIB',
            'SHB'             => 'SHB',
            'Eximbank'        => 'EIB',
            'MSB'             => 'MSB',
            'OCB'             => 'OCB',
            'SeABank'         => 'SEAB',
            'VietCapitalBank' => 'VCCB',
            'SCB'             => 'SCB',
            'VietBank'        => 'VietBank',
            'PVcomBank'       => 'PVCB',
            'Oceanbank'       => 'Oceanbank',
            'NCB'             => 'NCB',
            'BacABank'        => 'BAB',
            'LienVietPostBank'=> 'LPB',
            'KienLongBank'    => 'KLB',
            'VietABank'       => 'VAB',
            'NamABank'        => 'NAB',
            'PGBank'          => 'PGB',
            'GPBank'          => 'GPB',
            'ABBank'          => 'ABB',
            'BaoVietBank'     => 'BVB',
            'Cake'            => 'CAKE',
            'Ubank'           => 'Ubank',
            'Timo'            => 'Timo',
            'ViettelMoney'    => 'VTLMONEY',
            'VNPTMoney'       => 'VNPTMONEY',
        ];

        // Mặc định là Vietcombank nếu không map được
        return $bankMapping[$bankName] ?? 'VCB';
    }

    /**
     * ========================================
     * CLIENT: TẠO ĐƠN HÀNG + HIỂN THỊ TẤT CẢ MÃ GIAO DỊCH
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
                'user_id'         => Auth::id(),
                'order_code'      => $orderCode,
                'customer_name'   => $checkoutSession['customer_name'],
                'customer_phone'  => $checkoutSession['customer_phone'],
                'customer_email'  => $checkoutSession['customer_email'],
                'shipping_city'   => $checkoutSession['shipping_city'],
                'shipping_district'=> $checkoutSession['shipping_district'],
                'shipping_ward'   => $checkoutSession['shipping_ward'],
                'shipping_address'=> $checkoutSession['shipping_address'],
                'shipping_method' => $checkoutSession['shipping_method'],
                'shipping_fee'    => $checkoutSession['shipping_fee'],
                'payment_method'  => $checkoutSession['payment_method'],
                'sub_total'       => $checkoutSession['subtotal'],
                // tổng cuối cùng khách phải trả (sau giảm giá + phí ship)
                'total_price'     => $checkoutSession['final_total'],
                'discount_amount' => $checkoutSession['discount_amount'] ?? 0,
                'promotion_id'    => $checkoutSession['promotion']['promotion_id'] ?? null,
                'final_total'     => $checkoutSession['final_total'],
                'order_status'    => 'pending',
                'payment_status'  => 'pending',
                'notes'           => $checkoutSession['notes'] ?? null,
                'order_date'      => now(),
            ]);

            // Xử lý checkout từ cart (nhiều sản phẩm)
            if ($checkoutSession['type'] === 'cart' && isset($checkoutSession['items'])) {
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
                        'order_id'    => $order->id,
                        'product_id'  => $productForSnapshot->id,
                        'product_name'=> $item['name'],
                        'variant_id'  => $variant ? $variant->id : null,
                        'variant_name'=> $variant ? (
                            ($variant->color_name ?? '') .
                            ($variant->length && $variant->width && $variant->height
                                ? ' - ' . $variant->length . 'x' . $variant->width . 'x' . $variant->height . ' cm'
                                : '')
                        ) : null,
                        'variant_sku' => $variant ? $variant->sku : null,
                        'quantity'    => $item['quantity'],
                        'price'       => $item['price'],
                        'subtotal'    => $item['subtotal'],
                        'total_price' => $item['subtotal'],
                        'image_path'  => $item['image']
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

                $userCart = \App\Models\Cart::with('items')
                    ->where('user_id', Auth::id())
                    ->where('status', 'active')
                    ->first();
                if ($userCart) {
                    foreach ($userCart->items as $ci) {
                        $ci->delete();
                    }
                    $userCart->status = 'checked_out';
                    $userCart->total_price = 0;
                    $userCart->save();
                }
            } else {
                // Xử lý checkout mua ngay (1 sản phẩm)
                // Kiểm tra lại tồn kho
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
                    'order_id'    => $order->id,
                    'product_id'  => $productForSnapshot->id,
                    'product_name'=> $productForSnapshot->name,
                    'variant_id'  => $variant ? $variant->id : null,
                    'variant_name'=> $variant ? (
                        ($variant->color_name ?? '') .
                        ($variant->length && $variant->width && $variant->height
                            ? ' - ' . $variant->length . 'x' . $variant->width . 'x' . $variant->height . ' cm'
                            : '')
                    ) : null,
                    'variant_sku' => $variant ? $variant->sku : null,
                    'quantity'    => $checkoutSession['quantity'],
                    'price'       => $checkoutSession['price'],
                    'subtotal'    => $checkoutSession['subtotal'],
                    'total_price' => $checkoutSession['subtotal'],
                    'image_path'  => $productForSnapshot->image
                        ?? ($productForSnapshot->images->first()->image ?? null),
                ]);

                // Cập nhật tồn kho
                if ($variant) {
                    $variant->decrement('stock', $checkoutSession['quantity']);
                } else {
                    $product->decrement('stock', $checkoutSession['quantity']);
                }
            }

            // Tạo transaction nếu thanh toán qua chuyển khoản
            if ($checkoutSession['payment_method'] === 'bank') {
                // Lấy ví admin đầu tiên (hoặc ví mặc định)
                $wallet = Wallet::where('status', 'active')->first();

                if ($wallet) {
                    // Tạo transaction với trạng thái pending
                    Transaction::create([
                        'order_id'        => $order->id,
                        'wallet_id'       => $wallet->id,
                        'amount'          => $checkoutSession['final_total'],
                        'type'            => 'income',
                        'status'          => 'pending', // Sẽ được cập nhật thành 'completed' khi admin xác nhận
                        'payment_method'  => 'bank',
                        'transaction_code'=> $checkoutSession['temp_order_code'] ?? $orderCode,
                        'qr_code_url'     => $checkoutSession['qr_code_url'] ?? null,
                        'description'     => "Thanh toán đơn hàng {$orderCode} - {$checkoutSession['customer_name']}",
                    ]);

                    // Đơn hàng sẽ ở trạng thái "Chờ thanh toán"
                    // Admin sẽ xác nhận thủ công sau khi kiểm tra chuyển khoản
                }
            }

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
            'code'          => $result['code'],
            'promotion_id'  => $result['promotion_id'],
            'discount_amount'=> $result['discount'],
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
     * Tính phí vận chuyển
     */
    private function calculateShippingFee($method, $city, $subtotal)
    {
        // Logic tính phí vận chuyển đơn giản
        $baseFee = 30000; // Phí cơ bản

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

        // Miễn phí ship cho đơn trên 10.000.000
        if ($subtotal >= 10000000) {
            return 0;
        }

        return $baseFee;
    }
}
