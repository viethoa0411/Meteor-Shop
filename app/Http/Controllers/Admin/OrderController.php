<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\OrderLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class OrderController extends Controller
{
    private const ADMIN_EDITABLE_STATUSES = ['processing', 'shipping', 'delivered', 'returned'];


    private const STATUS_TRANSITIONS = [
        'pending' => ['processing'],
        'processing' => ['shipping', 'returned'],
        'shipping' => ['delivered'],
        'delivered' => [],

        'return_requested' => ['returned'],
        'returned' => [],
        'completed' => [],
        'cancelled' => [],
    ];

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
        // Lấy thông tin đơn bằng Eloquent và load quan hệ user
        $order = Order::with(['user'])->findOrFail($id);


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

        // Lấy lịch sử cập nhật trạng thái
        $statusHistory = collect();
        $orderLogs = collect();
        if (Schema::hasTable('order_status_history')) {
            $statusHistory = OrderStatusHistory::where('order_id', $id)
                ->with('admin:id,name,email')
                ->orderBy('created_at', 'desc')
                ->get();
        }
        if (Schema::hasTable('order_logs')) {
            $orderLogs = OrderLog::where('order_id', $id)
                ->with('admin:id,name,email,role')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Kiểm tra xem có yêu cầu trả hàng không
        $hasReturnRequest = $order->return_status && $order->return_status !== 'none' && $order->return_status !== 'rejected';

        return view('admin.orders.detail', compact('order', 'orderDetails', 'statusHistory', 'orderLogs', 'hasReturnRequest'));

    }

    public function updateStatus(Request $request, $id)
    {
        $order = DB::table('orders')->where('id', $id)->first();
        if (!$order) {
            return back()->with('error', 'Đơn hàng không tồn tại');
        }

        $newStatus     = $request->order_status;
        $currentStatus = $order->order_status;

        // 1. Chỉ cho phép admin sửa sang các trạng thái cho phép
        if (!in_array($newStatus, self::ADMIN_EDITABLE_STATUSES, true)) {
            return back()->with('error', 'Trạng thái này do khách hàng thao tác, không thể sửa từ Admin.');
        }

        // 2. Không cho phép cập nhật trực tiếp sang "returned" khi có return request
        // Trừ khi return_status đã là 'refunded' (đã nhận hàng và hoàn tiền)
        if ($newStatus === 'returned' && $currentStatus === 'return_requested') {
            // Kiểm tra return_status
            $returnStatus = $order->return_status ?? 'none';
            if ($returnStatus !== 'refunded') {
                return back()->with('error', 'Không thể cập nhật trực tiếp sang "Đã trả hàng". Vui lòng duyệt yêu cầu trả hàng và hoàn tiền tại trang quản lý return.');
            }
            // Nếu return_status = 'refunded' thì cho phép cập nhật sang 'returned'
        }

        // 3. Kiểm tra rule chuyển trạng thái hợp lệ

        $validTransitions = self::STATUS_TRANSITIONS[$currentStatus] ?? [];

        if (!in_array($newStatus, $validTransitions, true)) {
            return back()->with('error', 'Trạng thái không hợp lệ!');
        }

        // 3. Mapping cột thời gian tương ứng với trạng thái
        $statusTimestamps = [
            'processing' => 'confirmed_at',
            'shipping'   => 'shipped_at',
            'delivered'  => 'delivered_at',
            'returned'   => 'returned_at',
        ];

        $updatePayload = [
            'order_status' => $newStatus,
            'updated_at'   => now(),
        ];

        if (isset($statusTimestamps[$newStatus])) {
            $updatePayload[$statusTimestamps[$newStatus]] = now();
        }

        // Tự động cập nhật trạng thái thanh toán nếu là COD và đã giao hàng
        if ($newStatus === 'delivered' && $order->payment_method === 'cash') {
            $updatePayload['payment_status'] = 'paid';
        }

        // 4. Cập nhật đơn hàng
        DB::table('orders')
            ->where('id', $id)
            ->update($updatePayload);

        // 5. Lưu lịch sử cập nhật trạng thái
        if (Schema::hasTable('order_status_history')) {
            OrderStatusHistory::create([
                'order_id'   => $id,
                'admin_id'   => Auth::id(),
                'old_status' => $currentStatus,
                'new_status' => $newStatus,
                'note'       => $request->note ?? null,
            ]);
        }

        // 6. Ghi log
        if (Schema::hasTable('order_logs')) {
            $authUser = Auth::user();
            OrderLog::create([
                'order_id'   => $id,
                'status'     => $newStatus,
                'updated_by' => $authUser?->id,
                'role'       => ($authUser?->role === 'staff') ? 'staff' : 'admin',
                'created_at' => now(),
            ]);
        }

        // 7. Tạo thông báo cho các trạng thái đặc biệt
        try {
            $order = DB::table('orders')->where('id', $id)->first();
            if ($order) {
                // Tạo object order tạm để truyền vào notification
                $orderObj = (object) [
                    'id' => $order->id,
                    'order_code' => $order->order_code ?? 'N/A',
                ];

                // Thông báo khi thanh toán thất bại
                if ($newStatus === 'cancelled' && $currentStatus !== 'cancelled') {
                    \App\Services\NotificationService::createForAdmins([
                        'type' => 'order',
                        'level' => 'warning',
                        'title' => 'Đơn hàng bị hủy',
                        'message' => 'Đơn hàng #' . ($order->order_code ?? $order->id) . ' đã bị hủy',
                        'url' => route('admin.orders.show', $id),
                        'metadata' => ['order_id' => $id, 'status' => $newStatus]
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Không dừng flow nếu tạo notification thất bại
            Log::error('Error creating order status notification: ' . $e->getMessage());
        }

        return back()->with('success', 'Cập nhật trạng thái thành công!');
    }


    // Thêm method này để lấy danh sách trạng thái cho dropdown (không hiển thị pending)
    public function getAvailableStatuses($id)
    {
        $order = DB::table('orders')->where('id', $id)->first();

        if (!$order) {
            return response()->json(['error' => 'Đơn hàng không tồn tại'], 404);
        }

        $statusLabels = [
            'processing' => 'Đang xử lý',
            'shipping' => 'Đang giao hàng',
            'delivered' => 'Đã giao',
            'returned' => 'Đã trả hàng',
        ];

        $availableStatuses = self::STATUS_TRANSITIONS[$order->order_status] ?? [];
        $allowedStatuses = array_values(array_filter(
            $availableStatuses,
            fn($status) => in_array($status, self::ADMIN_EDITABLE_STATUSES, true)
        ));

        return response()->json(array_map(function ($status) use ($statusLabels) {
            return [
                'value' => $status,
                'label' => $statusLabels[$status] ?? ucfirst($status),
            ];
        }, $allowedStatuses));
    }
}
