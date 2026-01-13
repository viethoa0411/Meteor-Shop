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
    // Các trạng thái mà Admin được phép chỉnh sửa thủ công
    private const ADMIN_EDITABLE_STATUSES = ['processing', 'shipping', 'delivered', 'returned'];

    // Quy tắc chuyển trạng thái (State Machine)
    // Ví dụ: Từ 'pending' chỉ được sang 'processing', không được nhảy cóc sang 'delivered'
    private const STATUS_TRANSITIONS = [
        'pending' => ['processing'],            // Chờ xác nhận -> Đang xử lý
        'processing' => ['shipping', 'returned'], // Đang xử lý -> Đang giao hoặc Đổi trả (nếu hủy ngang)
        'shipping' => ['delivered'],            // Đang giao -> Đã giao
        'delivered' => [],                      // Đã giao -> (Chờ khách xác nhận hoặc auto complete)
        'return_requested' => ['returned'],     // Yêu cầu trả hàng -> Đã trả hàng
        'returned' => [],
        'completed' => [],
        'cancelled' => [],
    ];

    /**
     * Hiển thị danh sách đơn hàng
     * @param Request $request - Chứa các tham số lọc (status, keyword) từ URL
     */
    public function list(Request $request)
    {
        // 1. Lấy tham số lọc từ URL
        $status = $request->get('status', 'all'); // Mặc định là 'all' nếu không có
        $keyword = $request->get('keyword');      // Từ khóa tìm kiếm

        // 2. Bắt đầu xây dựng câu truy vấn (Query Builder)
        $query = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id') // Nối bảng users để lấy tên khách
            ->select(
                'orders.*',
                'users.name as customer_name' // Đổi tên cột name thành customer_name để tránh trùng
            );

        // 3. Áp dụng bộ lọc trạng thái nếu có
        if ($status && $status !== 'all') {
            $query->where('orders.order_status', $status);
        }

        // 4. Áp dụng tìm kiếm (theo mã đơn hoặc tên khách)
        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('orders.order_code', 'LIKE', "%{$keyword}%")
                    ->orWhere('users.name', 'LIKE', "%{$keyword}%");
            });
        }

        // 5. Logic lọc đơn hàng rác (Junk Filter)
        // Vấn đề: Nhiều khách chọn thanh toán Online (Bank/Momo) nhưng lại tắt tab, không thanh toán -> Tạo ra đơn rác.
        // Giải pháp: Chỉ hiển thị đơn hàng Online KHI VÀ CHỈ KHI họ đã thanh toán ('paid') hoặc đã hoàn tiền ('refunded').
        // Đối với đơn COD (thanh toán khi nhận hàng), hiển thị bình thường vì chưa cần thanh toán ngay.
        $query->where(function ($q) {
            // Điều kiện 1: Đơn hàng KHÔNG PHẢI là thanh toán online (tức là COD, cash...) -> Lấy hết.
            $q->whereNotIn('orders.payment_method', ['bank', 'momo'])
            
            // Điều kiện 2: HOẶC nếu là Online, thì bắt buộc trạng thái thanh toán phải là 'paid' hoặc 'refunded'.
                ->orWhereIn('orders.payment_status', ['paid', 'refunded']);
        });

        // 6. Thực hiện truy vấn và lấy kết quả (sắp xếp mới nhất trước)
        $orders = $query->orderBy('orders.created_at', 'DESC')->get();

        // 7. Đếm số đơn đang yêu cầu trả hàng (để hiện thông báo đỏ)
        $pendingReturnCount = DB::table('orders')
            ->whereNotNull('return_status')
            ->where('return_status', 'requested')
            ->count();

        // 8. Trả về View kèm dữ liệu
        return view('admin.orders.list', compact('orders', 'status', 'keyword', 'pendingReturnCount'));
    }


    /**
     * Show Order Detail - Xem chi tiết một đơn hàng cụ thể
     *
     * @param int $id - ID của đơn hàng cần xem
     * Logic:
     * 1. Lấy thông tin chung của đơn hàng (người mua, ngày đặt, trạng thái...).
     * 2. Lấy chi tiết từng sản phẩm trong đơn (tên, giá, số lượng...).
     * 3. Lấy lịch sử thay đổi trạng thái (để biết đơn hàng đã đi qua những bước nào).
     * 4. Kiểm tra xem đơn này có đang yêu cầu trả hàng hay không.
     */
    public function show($id)
    {
        // 1. Lấy thông tin đơn hàng bằng Eloquent
        // Sử dụng `with(['user'])` để lấy luôn thông tin người dùng (Eager Loading), giúp giảm số lượng query
        // `findOrFail($id)`: Tìm theo ID, nếu không thấy thì tự động trả về lỗi 404
        $order = Order::with(['user'])->findOrFail($id);


        // 2. Lấy danh sách sản phẩm (Order Details)
        // Sử dụng Query Builder (DB::table) để Join các bảng lại với nhau
        // Lý do dùng Query Builder ở đây: Có thể tối ưu tốc độ khi chỉ cần lấy các cột cụ thể
        $orderDetails = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id') // Join với bảng products để lấy tên/ảnh sản phẩm
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id') // Join với bảng categories để lấy tên danh mục
            ->select(
                'products.name as product_name',
                'categories.name as category_name',
                'categories.image as category_image',
                'order_details.price',    // Giá lúc mua (quan trọng: giá này có thể khác giá hiện tại của SP)
                'order_details.quantity', // Số lượng mua
                'order_details.subtotal'  // Thành tiền (price * quantity)
            )
            ->where('order_details.order_id', $id)
            ->get();

        // 3. Lấy lịch sử cập nhật trạng thái (Audit Log)
        $statusHistory = collect();
        $orderLogs = collect();

        // Kiểm tra bảng 'order_status_history' có tồn tại không (Phòng trường hợp chưa chạy migration)
        if (Schema::hasTable('order_status_history')) {
            $statusHistory = OrderStatusHistory::where('order_id', $id)
                ->with('admin:id,name,email') // Lấy thông tin Admin đã thực hiện thao tác
                ->orderBy('created_at', 'desc') // Sắp xếp giảm dần: Mới nhất hiển thị lên đầu
                ->get();
        }

        // Lấy log hệ thống (nếu có)
        if (Schema::hasTable('order_logs')) {
            $orderLogs = OrderLog::where('order_id', $id)
                ->with('admin:id,name,email,role')
                ->orderBy('created_at', 'desc') // Mới nhất lên đầu
                ->get();
        }

        // 4. Kiểm tra trạng thái trả hàng (Returns)
        // Logic: Đơn hàng có yêu cầu trả hàng nếu cột return_status có dữ liệu, khác 'none' và chưa bị từ chối
        $hasReturnRequest = $order->return_status && $order->return_status !== 'none' && $order->return_status !== 'rejected';

        // Trả về view 'admin.orders.detail' với tất cả dữ liệu đã lấy
        return view('admin.orders.detail', compact('order', 'orderDetails', 'statusHistory', 'orderLogs', 'hasReturnRequest'));

    }

    /**
     * Cập nhật trạng thái đơn hàng
     * Hàm này xử lý logic phức tạp nhất: kiểm tra điều kiện, cập nhật DB, ghi log, gửi thông báo
     */
    public function updateStatus(Request $request, $id)
    {
        // 1. Tìm đơn hàng
        $order = DB::table('orders')->where('id', $id)->first();
        if (!$order) {
            return back()->with('error', 'Đơn hàng không tồn tại');
        }

        $newStatus     = $request->order_status; // Trạng thái mới muốn chuyển sang
        $currentStatus = $order->order_status;   // Trạng thái hiện tại

        // 2. Validate: Chỉ cho phép admin sửa sang các trạng thái cho phép
        if (!in_array($newStatus, self::ADMIN_EDITABLE_STATUSES, true)) {
            return back()->with('error', 'Trạng thái này do khách hàng thao tác, không thể sửa từ Admin.');
        }

        // 3. Logic đặc biệt cho đổi trả: Không cho cập nhật trực tiếp sang 'returned'
        // nếu chưa xử lý xong yêu cầu trả hàng
        if ($newStatus === 'returned' && $currentStatus === 'return_requested') {
            $returnStatus = $order->return_status ?? 'none';
            if ($returnStatus !== 'refunded') {
                return back()->with('error', 'Không thể cập nhật trực tiếp sang "Đã trả hàng". Vui lòng duyệt yêu cầu trả hàng và hoàn tiền tại trang quản lý return.');
            }
        }

        // 4. Kiểm tra quy tắc chuyển trạng thái (State Transition Check)
        // Đảm bảo đơn hàng đi đúng quy trình: Pending -> Processing -> Shipping -> Delivered
        $validTransitions = self::STATUS_TRANSITIONS[$currentStatus] ?? [];

        if (!in_array($newStatus, $validTransitions, true)) {
            return back()->with('error', 'Trạng thái không hợp lệ! Không thể chuyển từ ' . $currentStatus . ' sang ' . $newStatus);
        }

        // 5. Chuẩn bị dữ liệu cập nhật
        // Mapping cột thời gian tương ứng với trạng thái để lưu dấu mốc thời gian
        $statusTimestamps = [
            'processing' => 'confirmed_at', // Khi chuyển sang 'processing' thì ghi nhận thời gian 'confirmed_at'
            'shipping'   => 'shipped_at',
            'delivered'  => 'delivered_at',
            'completed'  => 'completed_at',
            'returned'   => 'returned_at',
        ];

        $updatePayload = [
            'order_status' => $newStatus,
            'updated_at'   => now(),
        ];

        // Gán thời gian hiện tại cho cột timestamp tương ứng
        if (isset($statusTimestamps[$newStatus])) {
            $updatePayload[$statusTimestamps[$newStatus]] = now();
        }

        // Logic phụ: Tự động cập nhật 'đã thanh toán' nếu là COD và đã giao hàng thành công
        if ($newStatus === 'delivered' && $order->payment_method === 'cash') {
            $updatePayload['payment_status'] = 'paid';
        }

        // 6. Thực hiện cập nhật vào Database
        DB::table('orders')
            ->where('id', $id)
            ->update($updatePayload);

        // 7. Ghi lại lịch sử thay đổi (Audit Trail)
        // Để biết ai (admin nào) đã sửa, sửa lúc nào, từ trạng thái gì sang gì
        if (Schema::hasTable('order_status_history')) {
            OrderStatusHistory::create([
                'order_id'   => $id,
                'admin_id'   => Auth::id(),
                'old_status' => $currentStatus,
                'new_status' => $newStatus,
                'note'       => $request->note ?? null,
            ]);
        }

        // 8. Ghi log hệ thống (chung cho cả staff/admin)
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

        // 9. Gửi thông báo (Notification) nếu cần thiết
        // Ví dụ: Báo động nếu đơn bị hủy
        try {
            // ... Logic gửi thông báo (đã lược bớt comment chi tiết phần này để tập trung vào luồng chính) ...
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
            // Không dừng flow nếu tạo notification thất bại (chỉ log lỗi)
            Log::error('Error creating order status notification: ' . $e->getMessage());
        }

        return back()->with('success', 'Cập nhật trạng thái thành công!');
    }

}
