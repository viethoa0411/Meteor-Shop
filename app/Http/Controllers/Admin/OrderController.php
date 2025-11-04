<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Hiển thị danh sách đơn hàng
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $keyword = $request->get('keyword');

        $query = Order::with('user:id,name,phone')
            ->select('orders.*');

        // Lọc trạng thái
        if ($status && $status !== 'all') {
            $query->where('order_status', $status);
        }

        // Tìm kiếm theo mã đơn hoặc tên khách hàng
        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('order_code', 'LIKE', "%{$keyword}%")
                    ->orWhereHas('user', function ($userQuery) use ($keyword) {
                        $userQuery->where('name', 'LIKE', "%{$keyword}%");
                    });
            });
        }

        $orders = $query->orderBy('created_at', 'DESC')->paginate(15)->withQueryString();

        return view('admin.orders.list', compact('orders', 'status', 'keyword'));
    }

    /**
     * Hiển thị chi tiết đơn hàng
     */
    public function show(Order $order)
    {
        $order->load([
            'user:id,name,email,phone,address',
            'details.product:id,name,image'
        ]);

        return view('admin.orders.detail', compact('order'));
    }

    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'order_status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $newStatus = $request->order_status;
        $currentStatus = $order->order_status;

        // Quy tắc chuyển trạng thái hợp lệ
        $validTransitions = [
            'pending' => ['processing', 'cancelled'],
            'processing' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];

        if (!isset($validTransitions[$currentStatus]) || !in_array($newStatus, $validTransitions[$currentStatus])) {
            return back()->with('error', 'Trạng thái không hợp lệ! Không thể chuyển từ ' . $currentStatus . ' sang ' . $newStatus);
        }

        $order->update([
            'order_status' => $newStatus,
        ]);

        return back()->with('success', 'Cập nhật trạng thái thành công!');
    }
}
