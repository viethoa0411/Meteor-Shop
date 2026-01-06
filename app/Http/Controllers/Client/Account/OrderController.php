<?php

namespace App\Http\Controllers\Client\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Order\CancelOrderRequest;
use App\Http\Requests\Client\Order\ReturnOrderRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\ClientWallet;
use App\Models\WalletTransaction;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


class OrderController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $status = $request->get('status', 'all');
        $ordersQuery = Order::with(['items.product' => function($query) {
            $query->withTrashed(); // Load cả sản phẩm đã bị xóa
        }, 'walletTransactions'])
            ->ownedBy($userId)
            ->status($status)
            ->latest('order_date')
            ->latest();

        $ordersQuery = $this->applyFilters($ordersQuery, $request);

        $orders = $ordersQuery->paginate(6)->withQueryString();

        $counts = $this->getStatusCounts($userId);

        return view('client.account.orders.index', [
            'orders' => $orders,
            'status' => $status,
            'statusCounts' => $counts,
            'filters' => $request->only([
                'order_code',
                'from_date',
                'to_date',
                'min_total',
                'max_total',
                'payment_method',
            ]),
        ]);
    }

    public function show(Request $request, Order $order)
    {
        $this->authorizeOwnership($request->user()->id, $order);
        $order->loadMissing(['items.product' => function($query) {
            $query->withTrashed(); // Load cả sản phẩm đã bị xóa
        }, 'walletTransactions']);

        return view('client.account.orders.show', compact('order'));
    }

    public function tracking(Request $request, Order $order)
    {
        $this->authorizeOwnership($request->user()->id, $order);
        $order->loadMissing(['walletTransactions']);

        $timeline = [
            'order_date' => $order->display_order_date,
            'confirmed_at' => $order->confirmed_at,
            'packed_at' => $order->packed_at,
            'shipped_at' => $order->shipped_at,
            'delivered_at' => $order->delivered_at,
        ];

        return view('client.account.orders.tracking', compact('order', 'timeline'));
    }

    public function cancel(CancelOrderRequest $request, Order $order)
    {
        $this->authorizeOwnership($request->user()->id, $order);

        if (! $order->canCancel()) {
            return back()->with('error', 'Đơn hàng không thể hủy ở trạng thái hiện tại.');
        }

        DB::beginTransaction();
        try {
            // Hoàn tiền vào ví nếu đã thanh toán bằng wallet
            $refundMessage = '';
            if ($order->payment_method === 'wallet' && $order->payment_status === 'paid') {
                $wallet = ClientWallet::where('user_id', $order->user_id)->first();

                if ($wallet) {
                    $refundAmount = $order->final_total;
                    $balanceBefore = $wallet->balance;

                    // Hoàn tiền vào ví
                    $wallet->addBalance($refundAmount);

                    // Tạo transaction log
                    WalletTransaction::create([
                        'wallet_id' => $wallet->id,
                        'user_id' => $order->user_id,
                        'type' => 'refund',
                        'amount' => $refundAmount,
                        'balance_before' => $balanceBefore,
                        'balance_after' => $wallet->balance,
                        'description' => 'Hoàn tiền hủy đơn hàng ' . $order->order_code,
                        'order_id' => $order->id,
                    ]);

                    $refundMessage = ' Đã hoàn ' . number_format($refundAmount, 0, ',', '.') . 'đ vào ví của bạn.';
                }
            }

            $order->update([
                'order_status' => 'cancelled',
                'cancel_reason' => $request->reason,
                'notes' => $request->notes,
                'cancelled_at' => now(),
                'payment_status' => $order->payment_method === 'wallet' ? 'refunded' : $order->payment_status,
            ]);
            $this->logStatusChange($order, 'cancelled', $request->user()->id);

            OrderLog::create([
                'order_id' => $order->id,
                'status' => 'cancelled',
                'updated_by' => $request->user()->id,
                'role' => 'customer',
                'created_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'Đơn hàng đã được hủy thành công.' . $refundMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi hủy đơn hàng: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request, Order $order)
    {
        $this->authorizeOwnership($request->user()->id, $order);

        if (! $order->canReorder()) {
            return back()->with('error', 'Chỉ có thể mua lại đơn đã hoàn tất hoặc đã hủy.');
        }

        $user = $request->user();
        $cartItems = [];
        $subtotal = 0;

        foreach ($order->items as $item) {
            $product = \App\Models\Product::find($item->product_id);
            if (!$product) {
                continue;
            }

            $itemSubtotal = $item->price * $item->quantity;
            $subtotal += $itemSubtotal;

            $cartItems[] = [
                'product_id' => $item->product_id,
                'variant_id' => $item->variant_id ?? null,
                'name' => $product->name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'subtotal' => $itemSubtotal,
                'image' => $product->image,
                'color' => $item->color ?? null,
                'size' => $item->size ?? null,
            ];
        }

        if (empty($cartItems)) {
            return back()->with('error', 'Không có sản phẩm nào còn tồn tại để mua lại.');
        }

        // Tạo checkout session
        $checkoutSession = [
            'type' => 'reorder',
            'order_id' => $order->id,
            'items' => $cartItems,
            'subtotal' => $subtotal,
            'customer_name' => $user->name,
            'customer_phone' => $user->phone ?? $order->customer_phone,
            'customer_email' => $user->email,
            'shipping_city' => $order->shipping_city,
            'shipping_district' => $order->shipping_district,
            'shipping_ward' => $order->shipping_ward,
            'shipping_address' => $order->shipping_address,
        ];

        session(['checkout_session' => $checkoutSession]);

        // Chuyển hướng sang trang thanh toán
        return redirect()->route('client.checkout.index');
    }

    public function markAsReceived(Request $request, Order $order)
    {
        $this->authorizeOwnership($request->user()->id, $order);

        if ($order->order_status !== 'delivered') {
            return back()->with('error', 'Đơn hàng chưa được giao hoặc trạng thái không hợp lệ.');
        }

        DB::beginTransaction();
        try {
            $order->update([
                'order_status' => 'completed',
                'updated_at' => now(), // Cập nhật thời gian hoàn thành
                'completed_at' => now(),
                'payment_status' => $order->payment_method === 'cash' ? 'paid' : $order->payment_status,
            ]);

            // Log status change
            OrderLog::create([
                'order_id' => $order->id,
                'status' => 'completed',
                'updated_by' => $request->user()->id,
                'role' => 'customer',
                'created_at' => now(),
            ]);

            // Cập nhật history nếu có bảng history
             if (Schema::hasTable('order_status_history')) {
                \App\Models\OrderStatusHistory::create([
                    'order_id'   => $order->id,
                    'admin_id'   => null, // Customer action
                    'old_status' => 'delivered',
                    'new_status' => 'completed',
                    'note'       => 'Khách hàng xác nhận đã nhận hàng',
                ]);
            }

            DB::commit();

            return back()->with('success', 'Cảm ơn bạn đã mua sắm! Đơn hàng đã được hoàn tất.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function returnRequest(ReturnOrderRequest $request, Order $order)
    {
        $this->authorizeOwnership($request->user()->id, $order);

        if (! $order->canReturn()) {
            $errorMessage = 'Đơn hàng không đủ điều kiện đổi trả.';

            // Kiểm tra các điều kiện cụ thể để đưa ra thông báo chi tiết
            if ($order->order_status !== 'completed') {
                $errorMessage = 'Chỉ có thể yêu cầu đổi trả khi đơn hàng đã được giao thành công.';
            } elseif (!$order->delivered_at) {
                $errorMessage = 'Đơn hàng chưa được xác nhận đã nhận hàng.';
            } elseif ($order->isReturnExpired()) {
                $daysSinceDelivery = now()->diffInDays($order->delivered_at);
                $errorMessage = "Đơn hàng đã quá hạn để yêu cầu đổi trả. Thời gian cho phép là 7 ngày kể từ khi nhận hàng (đã qua {$daysSinceDelivery} ngày).";
            } elseif (!in_array($order->return_status, ['none'])) {
                $errorMessage = 'Đơn hàng này đã có yêu cầu đổi trả đang được xử lý.';
            } elseif ($order->return_status === 'rejected') {
                $errorMessage = 'Yêu cầu đổi trả đã bị từ chối trước đó. Bạn không thể gửi lại yêu cầu cho đơn này.';
            }

            return back()->with('error', $errorMessage);

        }

        $attachments = $order->return_attachments ?? [];

        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments');
            // Giới hạn tối đa 3 ảnh
            $files = array_slice($files, 0, 3);
            foreach ($files as $file) {
                $attachments[] = $file->store('returns', 'public');
            }
        }

        $order->update([
            'order_status' => 'return_requested',
            'return_status' => 'requested',
            'return_reason' => $request->reason,
            'return_note' => $request->description,
            'return_attachments' => $attachments,
        ]);


        OrderLog::create([
            'order_id' => $order->id,
            'status' => 'return_requested',
            'updated_by' => $request->user()->id,
            'role' => 'customer',
            'created_at' => now(),
        ]);


        return back()->with('success', 'Yêu cầu đổi trả đã được gửi. Chúng tôi sẽ liên hệ sớm nhất.');
    }

    protected function applyFilters($query, Request $request)
    {
        if ($code = $request->get('order_code')) {
            $query->where('order_code', 'like', '%' . $code . '%');
        }

        if ($from = $request->get('from_date')) {
            $query->whereDate(DB::raw('COALESCE(order_date, created_at)'), '>=', $from);
        }

        if ($to = $request->get('to_date')) {
            $query->whereDate(DB::raw('COALESCE(order_date, created_at)'), '<=', $to);
        }

        if ($min = $request->get('min_total')) {
            $query->where('final_total', '>=', $min);
        }

        if ($max = $request->get('max_total')) {
            $query->where('final_total', '<=', $max);
        }

        if ($payment = $request->get('payment_method')) {
            $query->where('payment_method', $payment);
        }

        return $query;
    }

    protected function getStatusCounts(int $userId): array
    {
        $baseCounts = Order::ownedBy($userId)
            ->select('order_status', DB::raw('COUNT(*) as total'))
            ->groupBy('order_status')
            ->pluck('total', 'order_status')
            ->toArray();

        // Đảm bảo tất cả các trạng thái đều có giá trị mặc định
        $allStatuses = ['pending', 'processing', 'shipping', 'delivered', 'completed', 'cancelled', 'return_requested', 'returned'];

        $counts = [];
        foreach ($allStatuses as $status) {
            $counts[$status] = $baseCounts[$status] ?? 0;
        }

        $counts['all'] = array_sum($counts);
        $counts['returned'] = ($counts['return_requested'] ?? 0) + ($counts['returned'] ?? 0);

        return $counts;
    }

    protected function authorizeOwnership(int $userId, Order $order): void
    {
        abort_if($order->user_id !== $userId, 403, 'Bạn không có quyền truy cập đơn hàng này.');
    }

    protected function logStatusChange(Order $order, string $status, int $userId): void
    {
        if (!Schema::hasTable('order_logs')) {
            return;
        }

        OrderLog::create([
            'order_id' => $order->id,
            'status' => $status,
            'updated_by' => $userId,
            'role' => 'customer',
            'created_at' => now(),
        ]);
    }
}
