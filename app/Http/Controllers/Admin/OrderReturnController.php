<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
// use App\Models\OrderReturn; // Unused
// use App\Models\OrderReturnItem; // Unused
use App\Models\OrderStatusHistory;
use App\Models\OrderLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\ClientWallet;
use App\Models\OrderReturn;
use App\Models\WalletTransaction;


class OrderReturnController extends Controller
{
    /**
     * List Returns - Hiển thị danh sách đơn hàng có yêu cầu trả hàng
     *
     * Chức năng:
     * - Lấy danh sách các đơn hàng có `return_status` (trạng thái trả hàng) khác null và khác 'none'.
     * - Hỗ trợ lọc theo từng trạng thái cụ thể (requested, approved, refunded...).
     */
    public function index(Request $request)
    {
        // Lấy trạng thái từ bộ lọc (mặc định là 'all')
        $status = $request->get('status', 'all');

        // Khởi tạo query builder: Join bảng orders với users để lấy tên khách hàng
        $query = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select('orders.*', 'users.name as customer_name')
            // Chỉ lấy những đơn CÓ liên quan đến quy trình trả hàng
            ->whereNotNull('orders.return_status')
            ->where('orders.return_status', '!=', 'none');

        // Áp dụng bộ lọc trạng thái nếu có
        if ($status && $status !== 'all') {
            $query->where('orders.return_status', $status);
        }

        // Sắp xếp theo thời gian cập nhật mới nhất
        $orders = $query->orderBy('orders.updated_at', 'DESC')->get();

        return view('admin.orders.returns.index', compact('orders', 'status'));
    }

    /**
     * Show Return Detail - Xem chi tiết yêu cầu trả hàng
     *
     * Chức năng:
     * - Hiển thị thông tin đơn hàng, lý do trả hàng, hình ảnh bằng chứng (nếu có).
     * - Hiển thị danh sách sản phẩm trong đơn để admin đối chiếu.
     */
    public function show($orderId)
    {
        // 1. Lấy thông tin đơn hàng và người dùng
        $order = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select('orders.*', 'users.name as customer_name', 'users.email as customer_email')
            ->where('orders.id', $orderId)
            ->first();

        if (!$order) {
            return redirect()->route('admin.orders.list')->with('error', 'Đơn hàng không tồn tại');
        }

        // 2. Lấy chi tiết các sản phẩm trong đơn hàng đó
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

        // 3. Xử lý hình ảnh bằng chứng (return_attachments)
        // Dữ liệu trong DB có thể là chuỗi JSON hoặc mảng, cần chuẩn hóa về mảng
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
     * Approve Return Request - Duyệt yêu cầu trả hàng
     *
     * Logic nghiệp vụ:
     * 1. Kiểm tra đơn hàng có tồn tại và đang ở trạng thái 'requested' (đã yêu cầu) hay không.
     * 2. Cập nhật `return_status` thành 'approved' (đã duyệt).
     * 3. Cập nhật `order_status` thành 'return_requested' (đang trong quy trình trả hàng).
     * 4. Ghi lịch sử thay đổi trạng thái (OrderStatusHistory).
     */
    public function approve(Request $request, $orderId)
    {
        $order = DB::table('orders')->where('id', $orderId)->first();

        if (!$order) {
            return back()->with('error', 'Đơn hàng không tồn tại');
        }

        // Chỉ được duyệt khi đang ở trạng thái 'requested'
        if ($order->return_status !== 'requested') {
            return back()->with('error', 'Chỉ có thể duyệt yêu cầu trả hàng ở trạng thái "requested"');
        }

        // Bắt đầu Transaction để đảm bảo tính toàn vẹn dữ liệu
        DB::beginTransaction();
        try {
            $oldStatus = $order->return_status;

            // Cập nhật trạng thái
            DB::table('orders')
                ->where('id', $orderId)
                ->update([
                    'return_status' => 'approved',       // Đã duyệt hoàn hàng
                    'order_status' => 'return_requested', // Đánh dấu đơn đang trả hàng
                    'updated_at' => now(),
                ]);

            // Ghi lịch sử thao tác của Admin
            if (Schema::hasTable('order_status_history')) {
                OrderStatusHistory::create([
                    'order_id' => $orderId,
                    'admin_id' => Auth::id(),
                    'old_status' => $order->order_status,
                    'new_status' => 'return_requested',
                    'note' => 'Duyệt yêu cầu trả hàng: ' . ($request->admin_note ?? ''),
                ]);
            }

            DB::commit(); // Lưu thay đổi vào DB

            // Quay lại trang danh sách với thông báo thành công
            return redirect()->route('admin.orders.returns.index', ['status' => 'approved'])
                ->with('success', 'Đã chấp nhận hoàn hàng từ người dùng thành công.');
        } catch (\Exception $e) {
            DB::rollBack(); // Hoàn tác nếu có lỗi
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Reject Return Request - Từ chối yêu cầu trả hàng
     *
     * Logic nghiệp vụ:
     * 1. Admin phải nhập lý do từ chối.
     * 2. Cập nhật `return_status` thành 'rejected'.
     * 3. Nếu đơn hàng đang bị treo ở 'return_requested', trả về trạng thái 'completed' (coi như đơn thành công vì không cho trả).
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

        // Bắt buộc phải có lý do từ chối
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
                    'return_note' => $request->reject_reason, // Lưu lý do từ chối vào note
                    'updated_at' => now(),
                ]);

            // Logic phục hồi trạng thái đơn hàng:
            // Nếu đơn hàng đang ở trạng thái 'return_requested', nghĩa là quy trình trả hàng thất bại -> đơn hàng coi như thành công (completed)
            if ($order->order_status === 'return_requested') {
                DB::table('orders')
                    ->where('id', $orderId)
                    ->update([
                        'order_status' => 'completed',
                        'completed_at' => now(),
                    ]);
            }

            // Ghi lịch sử
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

    /**
     * Update old return status system
     *
     * Logic Hoàn tiền (Refund) và Kết thúc quy trình:
     * 1. Khi admin chọn 'refunded' (đã hoàn tiền):
     *    - Cập nhật payment_status = 'refunded'.
     *    - Hoàn trả lại số lượng tồn kho (stock) cho sản phẩm.
     *    - Cộng tiền lại vào Ví khách hàng (ClientWallet).
     *    - Ghi log giao dịch ví (WalletTransaction).
     *
     * 2. Khi admin chọn 'completed' (hoàn tất):
     *    - Cập nhật order_status = 'returned'.
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
                // QUAN TRỌNG: Xử lý Hoàn tiền
                // 1. Giữ nguyên trạng thái đơn hàng nhưng đánh dấu thanh toán là 'refunded'
                $updateData['order_status'] = $order->order_status;
                $updateData['payment_status'] = 'refunded';

                if ($order->return_status !== 'refunded') {
                    // 2. Hoàn lại tồn kho (Stock Restock)
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

                    // 3. Hoàn tiền vào ví khách hàng (Wallet Refund)
                    $wallet = ClientWallet::getOrCreateForUser((int) $order->user_id);
                    $balanceBefore = $wallet->balance;
                    $wallet->addBalance((float) $order->final_total); // Cộng tiền vào ví

                    // Ghi lịch sử giao dịch ví
                    WalletTransaction::create([
                        'wallet_id' => $wallet->id,
                        'user_id' => (int) $order->user_id,
                        'type' => WalletTransaction::TYPE_REFUND,
                        'amount' => (float) $order->final_total,
                        'balance_before' => $balanceBefore,
                        'balance_after' => $wallet->balance,
                        'description' => 'Hoàn tiền trả hàng đơn #' . ($order->order_code ?? $orderId),
                        'order_id' => (int) $orderId,
                        'processed_by' => Auth::id(),
                        'status' => 'completed',
                    ]);
                }
            } elseif ($newReturnStatus === 'completed') {
                // Hoàn hàng thành công -> Đơn hàng coi như đã trả (returned)
                $updateData['order_status'] = 'returned';
                $updateData['returned_at'] = now();
            } elseif ($newReturnStatus === 'rejected' && $order->order_status === 'return_requested') {
                // Nếu từ chối sau khi đã request -> Quay về completed
                $updateData['order_status'] = 'completed';
                $updateData['completed_at'] = now();
                if (($order->payment_method ?? null) === 'cash') {
                    $updateData['payment_status'] = 'paid';
                }
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
