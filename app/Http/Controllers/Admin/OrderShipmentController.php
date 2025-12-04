<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderShipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OrderShipmentController extends Controller
{
    /**
     * List Shipments for an Order
     */
    public function index($orderId)
    {
        $order = Order::findOrFail($orderId);
        $shipments = $order->shipments()->with('creator')->orderBy('created_at', 'desc')->get();

        return view('admin.orders.shipments.index', compact('order', 'shipments'));
    }

    /**
     * Create Shipment
     */
    public function create($orderId)
    {
        $order = Order::with(['items.product', 'items.product.images'])->findOrFail($orderId);

        if ($order->order_status === 'cancelled' || $order->order_status === 'completed') {
            return back()->with('error', 'Không thể tạo đơn vận chuyển cho đơn hàng này');
        }

        return view('admin.orders.shipments.create', compact('order'));
    }

    /**
     * Store Shipment
     */
    public function store(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        $request->validate([
            'carrier' => 'required|in:ghn,ghtk,vnpost,shippo,manual,other',
            'carrier_name' => 'nullable|string|max:255',
            'tracking_number' => 'nullable|string|max:255',
            'tracking_url' => 'nullable|url|max:500',
            'shipping_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $shipmentCode = 'SH' . strtoupper(Str::random(8)) . Carbon::now()->format('Ymd');

            $shipment = OrderShipment::create([
                'order_id' => $order->id,
                'shipment_code' => $shipmentCode,
                'carrier' => $request->carrier,
                'carrier_name' => $request->carrier_name,
                'tracking_number' => $request->tracking_number,
                'tracking_url' => $request->tracking_url,
                'status' => 'pending',
                'shipping_cost' => $request->shipping_cost ?? 0,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
            ]);

            // 更新订单状态
            if ($order->order_status === 'packed' || $order->order_status === 'confirmed') {
                $order->update([
                    'order_status' => 'shipping',
                    'shipped_at' => now(),
                    'tracking_code' => $request->tracking_number,
                    'tracking_url' => $request->tracking_url,
                    'shipping_provider' => $request->carrier_name ?? $request->carrier,
                ]);
            }

            // 添加时间线
            $order->addTimeline(
                'shipment_created',
                'Tạo đơn vận chuyển',
                "Đơn vận chuyển {$shipmentCode} đã được tạo",
                null,
                $shipmentCode,
                ['shipment_id' => $shipment->id]
            );

            DB::commit();

            return redirect()->route('admin.orders.shipments.index', $order->id)
                ->with('success', 'Tạo đơn vận chuyển thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Update Shipment Status
     */
    public function updateStatus(Request $request, $orderId, $shipmentId)
    {
        $shipment = OrderShipment::where('order_id', $orderId)->findOrFail($shipmentId);
        $order = $shipment->order;

        $request->validate([
            'status' => 'required|in:pending,label_created,picked_up,in_transit,out_for_delivery,delivered,failed,returned',
        ]);

        $oldStatus = $shipment->status;
        $newStatus = $request->status;

        DB::beginTransaction();
        try {
            $updateData = ['status' => $newStatus];

            // 更新时间戳
            $statusTimestamps = [
                'picked_up' => 'picked_up_at',
                'in_transit' => 'in_transit_at',
                'out_for_delivery' => 'out_for_delivery_at',
                'delivered' => 'delivered_at',
                'failed' => 'failed_at',
            ];

            if (isset($statusTimestamps[$newStatus]) && !$shipment->{$statusTimestamps[$newStatus]}) {
                $updateData[$statusTimestamps[$newStatus]] = now();
            }

            $shipment->update($updateData);

            // 如果已送达，更新订单状态
            if ($newStatus === 'delivered' && $order->order_status === 'shipping') {
                $order->update([
                    'order_status' => 'delivered',
                    'delivered_at' => now(),
                ]);

                $order->addTimeline(
                    'status_changed',
                    'Đơn hàng đã được giao',
                    'Đơn hàng đã được giao thành công',
                    'shipping',
                    'delivered'
                );
            }

            // 添加时间线
            $order->addTimeline(
                'shipment_updated',
                'Cập nhật trạng thái vận chuyển',
                "Trạng thái vận chuyển đã thay đổi từ {$oldStatus} sang {$newStatus}",
                $oldStatus,
                $newStatus,
                ['shipment_id' => $shipment->id]
            );

            DB::commit();

            return back()->with('success', 'Cập nhật trạng thái thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}

