<?php

namespace App\Http\Controllers\Client\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Order\CancelOrderRequest;
use App\Http\Requests\Client\Order\ReturnOrderRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $status = $request->get('status', 'all');

        $ordersQuery = Order::with(['items.product'])
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

        $order->loadMissing(['items.product']);

        return view('client.account.orders.show', compact('order'));
    }

    public function tracking(Request $request, Order $order)
    {
        $this->authorizeOwnership($request->user()->id, $order);

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

        $order->update([
            'order_status' => 'cancelled',
            'cancel_reason' => $request->reason,
            'notes' => $request->notes,
            'cancelled_at' => now(),
        ]);

        return back()->with('success', 'Đơn hàng đã được hủy thành công.');
    }

    public function reorder(Request $request, Order $order)
    {
        $this->authorizeOwnership($request->user()->id, $order);

        if (! $order->canReorder()) {
            return back()->with('error', 'Chỉ có thể mua lại đơn đã hoàn tất hoặc đã hủy.');
        }

        DB::transaction(function () use ($order, $request) {
            $cart = Cart::firstOrCreate(
                ['user_id' => $request->user()->id, 'status' => 'active'],
                ['total_price' => 0]
            );

            $cart->items()->delete();

            foreach ($order->items as $item) {
                // Chỉ thêm sản phẩm còn tồn tại
                if ($item->product_id && \App\Models\Product::find($item->product_id)) {
                    CartItem::create([
                        'cart_id' => $cart->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'subtotal' => $item->subtotal,
                    ]);
                }
            }

            $cart->recalculateTotals();
        });

        return back()->with('success', 'Đã sao chép sản phẩm vào giỏ hàng. Vui lòng kiểm tra giỏ hàng của bạn.');
    }

    public function returnRequest(ReturnOrderRequest $request, Order $order)
    {
        $this->authorizeOwnership($request->user()->id, $order);

        if (! $order->canReturn()) {
            return back()->with('error', 'Đơn hàng không đủ điều kiện đổi trả.');
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
            'return_status' => 'requested',
            'return_reason' => $request->reason,
            'return_note' => $request->description,
            'return_attachments' => $attachments,
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
        $allStatuses = ['pending', 'processing', 'shipping', 'completed', 'cancelled', 'return_requested', 'returned'];
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
}

