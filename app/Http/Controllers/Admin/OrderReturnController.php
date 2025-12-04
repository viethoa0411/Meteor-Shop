<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\OrderReturnItem;
use App\Models\OrderStatusHistory;
use App\Models\OrderLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class OrderReturnController extends Controller
{
    /**
     * List Returns - Hiển thị danh sách đơn hàng có yêu cầu trả hàng
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select('orders.*', 'users.name as customer_name')
            ->whereNotNull('orders.return_status')
            ->where('orders.return_status', '!=', 'none');

        if ($status && $status !== 'all') {
            $query->where('orders.return_status', $status);
        }

        $orders = $query->orderBy('orders.updated_at', 'DESC')->get();

        return view('admin.orders.returns.index', compact('orders', 'status'));
    }

    /**
     * Show Return Detail for an Order (using old system with return_status)
     */
    public function show($orderId)
    {
        $order = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select('orders.*', 'users.name as customer_name', 'users.email as customer_email')
            ->where('orders.id', $orderId)
            ->first();

        if (!$order) {
            return redirect()->route('admin.orders.list')->with('error', 'Đơn hàng không tồn tại');
        }

        // Lấy chi tiết sản phẩm
        $orderDetails = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->select(
                'products.name as product_name',
                'products.image as product_image',
                'order_details.price',
                'order_details.quantity',
                'order_details.subtotal'
            )
            ->where('order_details.order_id', $orderId)
            ->get();

        // Xử lý return_attachments - decode JSON nếu cần
        if ($order->return_attachments) {
            if (is_string($order->return_attachments)) {
                $decoded = json_decode($order->return_attachments, true);
                $order->return_attachments = is_array($decoded) ? $decoded : [];
            } elseif (!is_array($order->return_attachments)) {
                $order->return_attachments = [];
            }
        } else {
            $order->return_attachments = [];
        }

        return view('admin.orders.returns.show', compact('order', 'orderDetails'));
    }

    /**
     * Approve Return Request
     */
    public function approve(Request $request, $orderId)
    {
        $order = DB::table('orders')->where('id', $orderId)->first();

        if (!$order) {
            return back()->with('error', 'Đơn hàng không tồn tại');
        }

        if ($order->return_status !== 'requested') {
            return back()->with('error', 'Chỉ có thể duyệt yêu cầu trả hàng ở trạng thái "requested"');
        }

        DB::beginTransaction();
        try {
            $oldStatus = $order->return_status;

            // Cập nhật return_status và order_status
            DB::table('orders')
                ->where('id', $orderId)
                ->update([
                    'return_status' => 'approved',
                    'order_status' => 'return_requested',
                    'updated_at' => now(),
                ]);

            // Lưu lịch sử
            if (Schema::hasTable('order_status_history')) {
                OrderStatusHistory::create([
                    'order_id' => $orderId,
                    'admin_id' => Auth::id(),
                    'old_status' => $order->order_status,
                    'new_status' => 'return_requested',
                    'note' => 'Duyệt yêu cầu trả hàng: ' . ($request->admin_note ?? ''),
                ]);
            }

            if (Schema::hasTable('order_logs')) {
                $authUser = Auth::user();
                OrderLog::create([
                    'order_id' => $orderId,
                    'status' => 'return_requested',
                    'updated_by' => $authUser?->id,
                    'role' => ($authUser?->role === 'staff') ? 'staff' : 'admin',
                    'created_at' => now(),
                ]);
            }

            DB::commit();

            // Redirect về trang danh sách returns để admin thấy trạng thái đã cập nhật
            return redirect()->route('admin.orders.returns.index', ['status' => 'approved'])
                ->with('success', 'Đã duyệt yêu cầu trả hàng thành công! Trạng thái đã được cập nhật thành "Đã duyệt".');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Reject Return Request
     */
    public function reject(Request $request, $orderId)
    {
        $order = DB::table('orders')->where('id', $orderId)->first();

        if (!$order) {
            return back()->with('error', 'Đơn hàng không tồn tại');
        }

        if ($order->return_status !== 'requested') {
            return back()->with('error', 'Chỉ có thể từ chối yêu cầu trả hàng ở trạng thái "requested"');
        }

        $request->validate([
            'reject_reason' => 'required|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // Cập nhật return_status
            DB::table('orders')
                ->where('id', $orderId)
                ->update([
                    'return_status' => 'rejected',
                    'return_note' => $request->reject_reason,
                    'updated_at' => now(),
                ]);

            // Nếu order_status là return_requested, chuyển về completed
            if ($order->order_status === 'return_requested') {
                DB::table('orders')
                    ->where('id', $orderId)
                    ->update(['order_status' => 'completed']);
            }

            // Lưu lịch sử
            if (Schema::hasTable('order_status_history')) {
                OrderStatusHistory::create([
                    'order_id' => $orderId,
                    'admin_id' => Auth::id(),
                    'old_status' => $order->order_status,
                    'new_status' => $order->order_status === 'return_requested' ? 'completed' : $order->order_status,
                    'note' => 'Từ chối yêu cầu trả hàng: ' . $request->reject_reason,
                ]);
            }

            DB::commit();

            // Redirect về trang danh sách returns
            return redirect()->route('admin.orders.returns.index', ['status' => 'rejected'])
                ->with('success', 'Đã từ chối yêu cầu trả hàng!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Update Return Status (for new OrderReturn model if exists)
     */
    public function updateStatus(Request $request, $orderId, $returnId = null)
    {
        // Nếu không có returnId, xử lý với hệ thống cũ
        if (!$returnId) {
            return $this->updateOldReturnStatus($request, $orderId);
        }

        // Xử lý với OrderReturn model mới
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

            // Đồng bộ với order status
            if ($newStatus === 'approved' && $order->order_status !== 'return_requested') {
                $order->update(['order_status' => 'return_requested']);
            }

            if ($newStatus === 'completed') {
                $order->update(['order_status' => 'returned', 'returned_at' => now()]);
            }

            DB::commit();
            return back()->with('success', 'Cập nhật trạng thái trả hàng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Update old return status system
     */
    private function updateOldReturnStatus(Request $request, $orderId)
    {
        $order = DB::table('orders')->where('id', $orderId)->first();

        if (!$order) {
            return back()->with('error', 'Đơn hàng không tồn tại');
        }

        $request->validate([
            'return_status' => 'required|in:approved,rejected,refunded,completed',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $newReturnStatus = $request->return_status;
            $updateData = [
                'return_status' => $newReturnStatus,
                'updated_at' => now(),
            ];

            // Cập nhật order_status tương ứng
            if ($newReturnStatus === 'approved') {
                $updateData['order_status'] = 'return_requested';
            } elseif ($newReturnStatus === 'refunded') {
                // Đã nhận hàng và hoàn lại tiền - giữ nguyên order_status
                $updateData['order_status'] = $order->order_status;
                if ($order->return_status !== 'refunded') {
                    $items = DB::table('order_details')
                        ->select('product_id', 'variant_id', 'quantity')
                        ->where('order_id', $orderId)
                        ->get();

                    foreach ($items as $it) {
                        if (!empty($it->variant_id)) {
                            DB::table('product_variants')
                                ->where('id', $it->variant_id)
                                ->increment('stock', (int) $it->quantity);
                        } else {
                            DB::table('products')
                                ->where('id', $it->product_id)
                                ->increment('stock', (int) $it->quantity);
                        }
                    }
                }
            } elseif ($newReturnStatus === 'completed') {
                // Hoàn hàng thành công - trạng thái cuối cùng
                $updateData['order_status'] = 'returned';
                $updateData['returned_at'] = now();
            } elseif ($newReturnStatus === 'rejected' && $order->order_status === 'return_requested') {
                $updateData['order_status'] = 'completed';
            }

            if ($request->admin_note) {
                $updateData['return_note'] = $request->admin_note;
            }

            DB::table('orders')->where('id', $orderId)->update($updateData);

            // Lưu lịch sử
            if (Schema::hasTable('order_status_history')) {
                OrderStatusHistory::create([
                    'order_id' => $orderId,
                    'admin_id' => Auth::id(),
                    'old_status' => $order->order_status,
                    'new_status' => $updateData['order_status'] ?? $order->order_status,
                    'note' => 'Cập nhật trạng thái trả hàng: ' . ($request->admin_note ?? ''),
                ]);
            }

            DB::commit();

            // Redirect về danh sách với filter tương ứng
            $statusFilter = null;
            $message = 'Cập nhật trạng thái trả hàng thành công!';

            if ($newReturnStatus === 'refunded') {
                $statusFilter = 'refunded';
                $message = 'Đã cập nhật trạng thái thành "Đã nhận hàng và hoàn tiền"!';
            } elseif ($newReturnStatus === 'completed') {
                $statusFilter = 'completed';
                $message = 'Đã cập nhật trạng thái thành "Hoàn hàng thành công"!';
            }

            if ($statusFilter) {
                return redirect()->route('admin.orders.returns.index', ['status' => $statusFilter])
                    ->with('success', $message);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
