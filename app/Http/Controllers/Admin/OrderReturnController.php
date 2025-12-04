<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\OrderReturnItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class OrderReturnController extends Controller
{
    /**
     * List Returns for an Order
     */
    public function index($orderId)
    {
        $order = Order::findOrFail($orderId);
        $returns = $order->returns()->with(['items.orderDetail', 'processor', 'exchangeProduct'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.orders.returns.index', compact('order', 'returns'));
    }

    /**
     * Show Return Detail
     */
    public function show($orderId, $returnId)
    {
        $order = Order::findOrFail($orderId);
        $return = $order->returns()->with(['items.orderDetail', 'processor', 'exchangeProduct'])->findOrFail($returnId);

        return view('admin.orders.returns.show', compact('order', 'return'));
    }

    /**
     * Update Return Status
     */
    public function updateStatus(Request $request, $orderId, $returnId)
    {
        $return = OrderReturn::where('order_id', $orderId)->findOrFail($returnId);
        $order = $return->order;

        $request->validate([
            'status' => 'required|in:requested,approved,rejected,in_transit,received,processed,completed,cancelled',
            'admin_notes' => 'nullable|string',
            'resolution' => 'nullable|in:refund,exchange,repair,reject',
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $return->status;
            $newStatus = $request->status;

            $updateData = [
                'status' => $newStatus,
                'admin_notes' => $request->admin_notes ?? $return->admin_notes,
            ];

            // 更新时间戳
            $statusTimestamps = [
                'approved' => 'approved_at',
                'rejected' => 'rejected_at',
                'received' => 'received_at',
                'processed' => 'processed_at',
            ];

            if (isset($statusTimestamps[$newStatus]) && !$return->{$statusTimestamps[$newStatus]}) {
                $updateData[$statusTimestamps[$newStatus]] = now();
            }

            if ($request->resolution) {
                $updateData['resolution'] = $request->resolution;
            }

            if ($request->exchange_product_id) {
                $updateData['exchange_product_id'] = $request->exchange_product_id;
            }

            $return->update($updateData);

            // 如果已批准，更新订单状态
            if ($newStatus === 'approved' && $order->order_status !== 'return_requested') {
                $order->update(['order_status' => 'return_requested']);
            }

            // 如果已完成，更新订单状态
            if ($newStatus === 'completed') {
                $order->update(['order_status' => 'returned']);
            }

            // 添加时间线
            $order->addTimeline(
                'return_approved',
                'Cập nhật trạng thái trả hàng',
                "Trạng thái trả hàng đã thay đổi từ {$oldStatus} sang {$newStatus}",
                $oldStatus,
                $newStatus,
                ['return_id' => $return->id]
            );

            DB::commit();

            return back()->with('success', 'Cập nhật trạng thái trả hàng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}

