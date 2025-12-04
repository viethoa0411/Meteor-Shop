<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderPaymentController extends Controller
{
    /**
     * List Payments for an Order
     */
    public function index($orderId)
    {
        $order = Order::findOrFail($orderId);
        $payments = $order->payments()->with('processor')->orderBy('created_at', 'desc')->get();

        return view('admin.orders.payments.index', compact('order', 'payments'));
    }

    /**
     * Create Payment Record
     */
    public function store(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        $request->validate([
            'payment_method' => 'required|in:cash,bank,momo,paypal,stripe,zalopay',
            'amount' => 'required|numeric|min:0|max:' . $order->final_total,
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $payment = OrderPayment::create([
                'order_id' => $order->id,
                'transaction_id' => $request->transaction_id,
                'payment_method' => $request->payment_method,
                'status' => 'paid',
                'amount' => $request->amount,
                'currency' => 'VND',
                'notes' => $request->notes,
                'paid_at' => now(),
                'processed_by' => Auth::id(),
            ]);

            // 更新订单支付状态
            $totalPaid = $order->payments()->where('status', 'paid')->sum('amount');
            if ($totalPaid >= $order->final_total) {
                $order->update(['payment_status' => 'paid']);
            } else {
                $order->update(['payment_status' => 'pending']);
            }

            // 添加时间线
            $order->addTimeline(
                'payment_received',
                'Nhận thanh toán',
                "Đã nhận thanh toán {$request->amount} VNĐ qua {$request->payment_method}",
                null,
                $request->amount,
                ['payment_id' => $payment->id]
            );

            DB::commit();

            return back()->with('success', 'Thêm giao dịch thanh toán thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }
}

