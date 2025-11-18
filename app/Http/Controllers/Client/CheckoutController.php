<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CheckoutController extends Controller
{
    /**
     * Trang checkout - Thanh toán
     * URL: /checkout?product_id=xxx&variant_id=xxx&qty=1&type=buy_now
     */
    public function index(Request $request)
    {
        // Kiểm tra đăng nhập
        if (!Auth::check()) {
            return redirect()->route('client.login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục đặt hàng');
        }

        $productId = $request->get('product_id');
        $variantId = $request->get('variant_id');
        $qty = max(1, (int) $request->get('qty', 1));
        $type = $request->get('type', 'buy_now');

        if (!$productId) {
            return redirect()->route('client.home')
                ->with('error', 'Không tìm thấy sản phẩm');
        }

        // Lấy sản phẩm
        $product = Product::with(['variants', 'images'])->findOrFail($productId);

        // Lấy variant nếu có
        $variant = null;
        $price = $product->price;
        $stock = $product->stock ?? 0;

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
            'type' => $type,
            'product_id' => $productId,
            'variant_id' => $variantId,
            'quantity' => $qty,
            'price' => $price,
            'subtotal' => $price * $qty,
            'created_at' => now(),
        ];

        session(['checkout_session' => $checkoutData]);

        // Lấy thông tin user
        $user = Auth::user();

        return view('client.checkout.index', compact('product', 'variant', 'qty', 'price', 'stock', 'user', 'checkoutData'));
    }

    /**
     * Xử lý form checkout - chuyển đến trang xác nhận
     */
    public function process(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'required|email|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_district' => 'required|string|max:255',
            'shipping_ward' => 'required|string|max:255',
            'shipping_address' => 'required|string|max:500',
            'shipping_method' => 'required|string|in:standard,express,fast',
            'payment_method' => 'required|string|in:cash,bank,momo,paypal',
            'notes' => 'nullable|string|max:1000',
            'quantity' => 'nullable|integer|min:1',
        ]);

        // Lấy checkout session
        $checkoutSession = session('checkout_session');
        if (!$checkoutSession) {
            return redirect()->route('client.home')
                ->with('error', 'Phiên đặt hàng đã hết hạn. Vui lòng thử lại.');
        }

        // Cập nhật số lượng nếu có thay đổi
        if ($request->has('quantity')) {
            $newQuantity = max(1, (int) $request->quantity);
            
            // Kiểm tra lại tồn kho với số lượng mới
            $product = Product::findOrFail($checkoutSession['product_id']);
            $stock = $product->stock ?? 0;
            
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
        $checkoutSession['customer_name'] = $request->customer_name;
        $checkoutSession['customer_phone'] = $request->customer_phone;
        $checkoutSession['customer_email'] = $request->customer_email;
        $checkoutSession['shipping_city'] = $request->shipping_city;
        $checkoutSession['shipping_district'] = $request->shipping_district;
        $checkoutSession['shipping_ward'] = $request->shipping_ward;
        $checkoutSession['shipping_address'] = $request->shipping_address;
        $checkoutSession['shipping_method'] = $request->shipping_method;
        $checkoutSession['payment_method'] = $request->payment_method;
        $checkoutSession['shipping_fee'] = $shippingFee;
        $checkoutSession['notes'] = $request->notes;
        $checkoutSession['final_total'] = $checkoutSession['subtotal'] + $shippingFee;

        session(['checkout_session' => $checkoutSession]);

        return redirect()->route('client.checkout.confirm');
    }

    /**
     * Trang xác nhận đơn hàng
     */
    public function confirm()
    {
        $checkoutSession = session('checkout_session');
        
        if (!$checkoutSession) {
            return redirect()->route('client.home')
                ->with('error', 'Phiên đặt hàng đã hết hạn. Vui lòng thử lại.');
        }

        // Lấy lại sản phẩm và variant
        $product = Product::with(['variants', 'images'])->findOrFail($checkoutSession['product_id']);
        $variant = null;
        if ($checkoutSession['variant_id']) {
            $variant = ProductVariant::find($checkoutSession['variant_id']);
        }

        return view('client.checkout.confirm', compact('checkoutSession', 'product', 'variant'));
    }

    /**
     * Xử lý tạo đơn hàng
     */
    public function createOrder()
    {
        $checkoutSession = session('checkout_session');
        
        if (!$checkoutSession) {
            return redirect()->route('client.home')
                ->with('error', 'Phiên đặt hàng đã hết hạn. Vui lòng thử lại.');
        }

        DB::beginTransaction();
        try {
            // Kiểm tra lại tồn kho
            $product = Product::findOrFail($checkoutSession['product_id']);
            $stock = $product->stock ?? 0;
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

            // Tạo mã đơn hàng
            $orderCode = 'DH' . strtoupper(Str::random(8)) . Carbon::now()->format('Ymd');

            // Tạo order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_code' => $orderCode,
                'customer_name' => $checkoutSession['customer_name'],
                'customer_phone' => $checkoutSession['customer_phone'],
                'customer_email' => $checkoutSession['customer_email'],
                'shipping_city' => $checkoutSession['shipping_city'],
                'shipping_district' => $checkoutSession['shipping_district'],
                'shipping_ward' => $checkoutSession['shipping_ward'],
                'shipping_address' => $checkoutSession['shipping_address'],
                'shipping_method' => $checkoutSession['shipping_method'],
                'shipping_fee' => $checkoutSession['shipping_fee'],
                'payment_method' => $checkoutSession['payment_method'],
                'sub_total' => $checkoutSession['subtotal'],
                'total_price' => $checkoutSession['subtotal'],
                'discount_amount' => 0,
                'final_total' => $checkoutSession['final_total'],
                'order_status' => 'pending',
                'payment_status' => 'pending',
                'notes' => $checkoutSession['notes'] ?? null,
                'order_date' => now(),
            ]);

            // Lấy thông tin sản phẩm để lưu snapshot (đã lấy ở trên)
            // $product và $variant đã được lấy ở phần kiểm tra tồn kho

            // Lấy lại product với images để lưu snapshot
            $productForSnapshot = Product::with('images')->findOrFail($product->id);
            
            // Tạo order detail với snapshot đầy đủ
            $orderDetail = OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $productForSnapshot->id,
                'product_name' => $productForSnapshot->name,
                'variant_id' => $variant ? $variant->id : null,
                'variant_name' => $variant ? (
                    ($variant->color_name ?? '') . 
                    ($variant->length && $variant->width && $variant->height 
                        ? ' - ' . $variant->length . 'x' . $variant->width . 'x' . $variant->height . ' cm'
                        : '')
                ) : null,
                'variant_sku' => $variant ? $variant->sku : null,
                'quantity' => $checkoutSession['quantity'],
                'price' => $checkoutSession['price'],
                'subtotal' => $checkoutSession['subtotal'],
                'total_price' => $checkoutSession['subtotal'],
                'image_path' => $productForSnapshot->image ?? ($productForSnapshot->images->first()->image ?? null),
            ]);

            // Cập nhật tồn kho
            if ($variant) {
                $variant->decrement('stock', $checkoutSession['quantity']);
            } else {
                $product->decrement('stock', $checkoutSession['quantity']);
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
     * Trang đặt hàng thành công
     */
    public function success($orderCode)
    {
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

        // Miễn phí ship cho đơn trên 500k
        if ($subtotal >= 500000) {
            return 0;
        }

        return $baseFee;
    }
}

