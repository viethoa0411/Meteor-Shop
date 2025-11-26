<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class OrderController extends Controller
{
    public function list(Request $request)
    {
        $status = $request->get('status', 'all');
        $keyword = $request->get('keyword'); // từ khóa tìm kiếm

        $query = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'orders.*',
                'users.name as customer_name'
            );

        // --- Lọc trạng thái ---
        if ($status && $status !== 'all') {
            $query->where('orders.order_status', $status);
        }

        // --- Tìm kiếm theo mã đơn hoặc tên khách hàng ---
        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('orders.order_code', 'LIKE', "%{$keyword}%")
                    ->orWhere('users.name', 'LIKE', "%{$keyword}%");
            });
        }

        // Chỉ hiển thị đơn thanh toán online sau khi admin xác nhận
        $query->where(function ($q) {
            $q->whereNotIn('orders.payment_method', ['bank', 'momo'])
              ->orWhereIn('orders.payment_status', ['paid', 'refunded']);
        });

        $orders = $query->orderBy('orders.created_at', 'DESC')->get();

        return view('admin.orders.list', compact('orders', 'status', 'keyword'));
    }


    public function show($id)
    {
        // Lấy thông tin đơn
        $order = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'orders.*',
                'users.name as customer_name',
                'users.phone as customer_phone'
            )
            ->where('orders.id', $id)
            ->first();

        // Lấy danh sách sản phẩm trong đơn
        $orderDetails = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->select(
                'products.name as product_name',
                'order_details.price',
                'order_details.quantity',
                'order_details.subtotal'
            )
            ->where('order_details.order_id', $id)
            ->get();

        return view('admin.orders.detail', compact('order', 'orderDetails'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = DB::table('orders')->where('id', $id)->first();
        if (!$order) {
            return back()->with('error', 'Đơn hàng không tồn tại');
        }

        $newStatus = $request->order_status;
        $currentStatus = $order->order_status;

        // Quy tắc chuyển trạng thái hợp lệ
        $validTransitions = [
            'pending' => ['processing', 'cancelled'],
            'processing' => ['shipping', 'completed', 'cancelled'],
            'shipping' => ['completed', 'return_requested', 'cancelled'],
            'return_requested' => ['returned'],
            'completed' => [],
            'returned' => [],
            'cancelled' => []
        ];

        if (!isset($validTransitions[$currentStatus]) || !in_array($newStatus, $validTransitions[$currentStatus])) {
            return back()->with('error', 'Trạng thái không hợp lệ!');
        }
        $statusTimestamps = [
            'processing' => 'confirmed_at',
            'shipping' => 'shipped_at',
            'completed' => 'delivered_at',
            'cancelled' => 'cancelled_at',
        ];

        $updatePayload = [
            'order_status' => $newStatus,
            'updated_at' => now(),
        ];

        if (isset($statusTimestamps[$newStatus])) {
            $updatePayload[$statusTimestamps[$newStatus]] = now();
        }

        DB::table('orders')
            ->where('id', $id)
            ->update($updatePayload);

        return back()->with('success', 'Cập nhật trạng thái thành công!');
    }
}
