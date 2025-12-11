<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderRefund;
use App\Models\OrderRefundItem;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OrderRefundController extends Controller
{
    /**
     * List Refunds for an Order
     */
    public function index($orderId)
    {
        $order = Order::with('items')->findOrFail($orderId);
        $refunds = $order->refunds()->with(['payment', 'items.orderDetail', 'processor'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.orders.refunds.index', compact('order', 'refunds'));
    }

    /**
     * Create Refund
     */
    public function create($orderId)
    {
        $order = Order::with(['items.product', 'payments'])->findOrFail($orderId);

        if (!in_array($order->order_status, ['completed', 'delivered', 'cancelled'])) {
            return back()->with('error', 'Chỉ có thể hoàn tiền cho đơn hàng đã hoàn thành hoặc đã hủy');
        }

        return view('admin.orders.refunds.create', compact('order'));
    }

    /**
     * Store Refund
     */
    public function store(Request $request, $orderId)
    {
        $order = Order::with('items')->findOrFail($orderId);

        $request->validate([
            'type' => 'required|in:full,partial',
            'reason' => 'required|string|max:500',
            'notes' => 'nullable|string',
            'items' => 'required_if:type,partial|array',
            'items.*.order_detail_id' => 'required|exists:order_details,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $refundCode = 'RF' . strtoupper(Str::random(8)) . Carbon::now()->format('Ymd');

            // 计算退款金额
            if ($request->type === 'full') {
                $refundAmount = $order->final_total;
            } else {
                $refundAmount = 0;
                foreach ($request->items as $item) {
                    $refundAmount += $item['amount'];
                }
            }

            // 创建退款记录
            $refund = OrderRefund::create([
                'order_id' => $order->id,
                'order_payment_id' => $request->payment_id ?? null,
                'refund_code' => $refundCode,
                'type' => $request->type,
                'status' => 'pending',
                'amount' => $refundAmount,
                'currency' => 'VND',
                'reason' => $request->reason,
                'notes' => $request->notes,
                'processed_by' => Auth::id(),
            ]);

            // 创建退款项（如果是部分退款）
            if ($request->type === 'partial' && $request->has('items')) {
                foreach ($request->items as $item) {
                    OrderRefundItem::create([
                        'refund_id' => $refund->id,
                        'order_detail_id' => $item['order_detail_id'],
                        'quantity' => $item['quantity'],
                        'amount' => $item['amount'],
                        'reason' => $item['reason'] ?? null,
                    ]);
                }
            }

            // 更新订单状态
            if ($request->type === 'full') {
                $order->update([
                    'order_status' => 'refunded',
                    'refunded_at' => now(),
                ]);
            }

            // 添加时间线
            $order->addTimeline(
                'refund_created',
                'Tạo yêu cầu hoàn tiền',
                "Yêu cầu hoàn tiền {$refundAmount} VNĐ - Lý do: {$request->reason}",
                null,
                $refundAmount,
                ['refund_id' => $refund->id, 'type' => $request->type]
            );

            DB::commit();

            return redirect()->route('admin.orders.refunds.index', $order->id)
                ->with('success', 'Tạo yêu cầu hoàn tiền thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Update Refund Status
     */
    public function updateStatus(Request $request, $orderId, $refundId)
    {
        $refund = OrderRefund::where('order_id', $orderId)->findOrFail($refundId);
        $order = $refund->order;

        $request->validate([
            'status' => 'required|in:pending,processing,completed,failed,cancelled',
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $refund->status;
            $newStatus = $request->status;

            $updateData = ['status' => $newStatus];

            if ($newStatus === 'completed') {
                $updateData['refunded_at'] = now();

                // 更新支付记录的退款金额
                if ($refund->order_payment_id) {
                    $payment = $refund->payment;
                    $payment->increment('refunded_amount', $refund->amount);
                }
            }

            $refund->update($updateData);

            // 添加时间线
            $order->addTimeline(
                'refund_completed',
                'Hoàn tiền thành công',
                "Hoàn tiền {$refund->amount} VNĐ đã được xử lý",
                $oldStatus,
                $newStatus,
                ['refund_id' => $refund->id]
            );

            DB::commit();

            return back()->with('success', 'Cập nhật trạng thái hoàn tiền thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}

