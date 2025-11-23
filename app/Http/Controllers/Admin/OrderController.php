<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderPayment;
use App\Models\OrderShipment;
use App\Models\OrderRefund;
use App\Models\OrderReturn;
use App\Models\OrderNote;
use App\Models\OrderTimeline;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Order List - 订单列表页（带搜索、过滤、批量操作）
     */
    public function list(Request $request)
    {
        $query = Order::with(['user', 'items.product', 'latestPayment', 'latestShipment']);

        // 搜索
        if ($keyword = $request->get('keyword')) {
            $query->where(function ($q) use ($keyword) {
                $q->where('order_code', 'LIKE', "%{$keyword}%")
                    ->orWhere('customer_name', 'LIKE', "%{$keyword}%")
                    ->orWhere('customer_email', 'LIKE', "%{$keyword}%")
                    ->orWhere('customer_phone', 'LIKE', "%{$keyword}%")
                    ->orWhereHas('user', function ($q) use ($keyword) {
                        $q->where('name', 'LIKE', "%{$keyword}%")
                            ->orWhere('email', 'LIKE', "%{$keyword}%");
            });
            });
        }

        // 过滤 - Order Status
        if ($status = $request->get('status')) {
            if ($status !== 'all') {
                $query->where('order_status', $status);
            }
        }

        // 过滤 - Payment Status
        if ($paymentStatus = $request->get('payment_status')) {
            if ($paymentStatus !== 'all') {
                $query->where('payment_status', $paymentStatus);
            }
        }

        // 过滤 - Fulfillment Status
        if ($fulfillmentStatus = $request->get('fulfillment_status')) {
            if ($fulfillmentStatus === 'pending') {
                $query->whereNull('confirmed_at');
            } elseif ($fulfillmentStatus === 'confirmed') {
                $query->whereNotNull('confirmed_at')->whereNull('packed_at');
            } elseif ($fulfillmentStatus === 'packed') {
                $query->whereNotNull('packed_at')->whereNull('shipped_at');
            } elseif ($fulfillmentStatus === 'shipped') {
                $query->whereNotNull('shipped_at')->whereNull('delivered_at');
            } elseif ($fulfillmentStatus === 'delivered') {
                $query->whereNotNull('delivered_at');
            }
        }

        // 过滤 - Shipping Method
        if ($shippingMethod = $request->get('shipping_method')) {
            if ($shippingMethod !== 'all') {
                $query->where('shipping_method', $shippingMethod);
            }
        }

        // 过滤 - Date Range
        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // 过滤 - Price Range
        if ($minPrice = $request->get('min_price')) {
            $query->where('final_total', '>=', $minPrice);
        }
        if ($maxPrice = $request->get('max_price')) {
            $query->where('final_total', '<=', $maxPrice);
        }

        // 排序
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        // 分页
        $orders = $query->paginate(20)->withQueryString();

        // 统计数据
        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('order_status', 'pending')->count(),
            'awaiting_payment' => Order::where('order_status', 'awaiting_payment')->count(),
            'paid' => Order::where('order_status', 'paid')->count(),
            'processing' => Order::whereIn('order_status', ['processing', 'confirmed', 'packed'])->count(),
            'shipping' => Order::where('order_status', 'shipping')->count(),
            'delivered' => Order::where('order_status', 'delivered')->count(),
            'completed' => Order::where('order_status', 'completed')->count(),
            'cancelled' => Order::where('order_status', 'cancelled')->count(),
            'returned' => Order::where('order_status', 'returned')->count(),
            'refunded' => Order::where('order_status', 'refunded')->count(),
        ];

        return view('admin.orders.list', compact('orders', 'stats'));
    }

    /**
     * Order Detail - 订单详情页（多标签页）
     */
    public function show($id)
    {
        $order = Order::with([
            'user.orders',
            'items.product',
            'items.product.images',
            'payments',
            'shipments',
            'refunds.items.orderDetail',
            'returns.items.orderDetail',
            'notes.creator',
            'notes.taggedUser',
            'timelines.user',
        ])->findOrFail($id);

        $tab = request()->get('tab', 'summary');

        return view('admin.orders.show', compact('order', 'tab'));
    }

    /**
     * Update Order Status - 更新订单状态（带完整验证）
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $oldStatus = $order->order_status;
        $newStatus = $request->order_status;
        $note = $request->get('note');

        // 验证状态转换是否允许
        if (!$order->canTransitionTo($newStatus)) {
            $allowedStatuses = implode(', ', $order->getAllowedNextStatuses());
            return back()->with('error', "Không thể chuyển từ trạng thái '{$order->status_meta['label']}' sang trạng thái mới. Các trạng thái hợp lệ: {$allowedStatuses}");
        }

        DB::beginTransaction();
        try {
            // 更新状态和时间戳
            $updateData = ['order_status' => $newStatus];

            // 状态对应的时间戳字段
        $statusTimestamps = [
                'awaiting_payment' => null, // 不需要时间戳
                'paid' => null,
            'processing' => 'confirmed_at',
                'confirmed' => 'confirmed_at',
                'packed' => 'packed_at',
            'shipping' => 'shipped_at',
                'delivered' => 'delivered_at',
            'completed' => 'delivered_at',
            'cancelled' => 'cancelled_at',
                'returned' => null,
                'refunded' => 'refunded_at',
            ];

            // 更新对应的时间戳
            if (isset($statusTimestamps[$newStatus]) && $statusTimestamps[$newStatus]) {
                $timestampField = $statusTimestamps[$newStatus];
                if (!$order->{$timestampField}) {
                    $updateData[$timestampField] = now();
                }
            }

            // 特殊处理：如果状态变为 paid，更新 payment_status
            if ($newStatus === 'paid') {
                $updateData['payment_status'] = 'paid';
            }

            // 特殊处理：如果状态变为 awaiting_payment，更新 payment_status
            if ($newStatus === 'awaiting_payment') {
                $updateData['payment_status'] = 'awaiting_payment';
            }

            // 特殊处理：如果状态变为 cancelled，保存取消原因
            if ($newStatus === 'cancelled' && $request->has('cancel_reason')) {
                $updateData['cancel_reason'] = $request->cancel_reason;
            }

            $order->update($updateData);

            // 添加时间线记录
            $description = "Trạng thái đã thay đổi từ '{$order->getStatusMetaAttribute()['label']}' sang '{$order->status_meta['label']}'";
            if ($note) {
                $description .= ". Ghi chú: {$note}";
            }

            $order->addTimeline(
                'status_changed',
                'Thay đổi trạng thái đơn hàng',
                $description,
                $oldStatus,
                $newStatus,
                ['note' => $note, 'changed_by' => Auth::check() ? Auth::id() : null]
            );

            // TODO: 发送邮件通知（如果需要）
            // event(new OrderStatusChanged($order, $oldStatus, $newStatus));

            DB::commit();

            return back()->with('success', 'Cập nhật trạng thái thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Bulk Actions - 批量操作
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:confirm,cancel,export',
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
        ]);

        $orderIds = $request->order_ids;
        $action = $request->action;

        DB::beginTransaction();
        try {
            switch ($action) {
                case 'confirm':
                    $count = Order::whereIn('id', $orderIds)
                        ->where('order_status', 'pending')
                        ->update([
                            'order_status' => 'confirmed',
                            'confirmed_at' => now(),
                        ]);
                    $message = "Đã xác nhận {$count} đơn hàng";
                    break;

                case 'cancel':
                    $count = Order::whereIn('id', $orderIds)
                        ->whereIn('order_status', ['pending', 'confirmed', 'packed'])
                        ->update([
                            'order_status' => 'cancelled',
                            'cancelled_at' => now(),
                        ]);
                    $message = "Đã hủy {$count} đơn hàng";
                    break;

                case 'export':
                    // Export logic sẽ在单独的方法中实现
                    return $this->export($request);
            }

            DB::commit();
            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Export Orders - 导出订单
     */
    public function export(Request $request)
    {
        // 这里可以使用 Laravel Excel 包
        // 暂时返回简单的 CSV
        $orders = Order::with(['user', 'items'])
            ->when($request->order_ids, function ($q) use ($request) {
                $q->whereIn('id', $request->order_ids);
            })
            ->get();

        $filename = 'orders_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');
            // BOM for UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Headers
            fputcsv($file, [
                'Mã đơn',
                'Ngày đặt',
                'Khách hàng',
                'Email',
                'Số điện thoại',
                'Trạng thái',
                'Tổng tiền',
                'Phương thức thanh toán',
                'Trạng thái thanh toán',
            ]);

            // Data
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_code,
                    $order->created_at->format('d/m/Y H:i'),
                    $order->customer_name ?? $order->user->name ?? '',
                    $order->customer_email ?? $order->user->email ?? '',
                    $order->customer_phone ?? $order->user->phone ?? '',
                    $order->status_meta['label'] ?? $order->order_status,
                    number_format((float)($order->final_total ?? 0), 0, ',', '.') . ' VNĐ',
                    $order->payment_label,
                    $order->payment_status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Edit Order - 编辑订单
     */
    public function edit($id)
    {
        $order = Order::with(['items.product', 'user'])->findOrFail($id);

        if (!in_array($order->order_status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Chỉ có thể chỉnh sửa đơn hàng ở trạng thái Chờ xác nhận hoặc Đã xác nhận');
        }

        $products = Product::where('status', 'active')->get();

        return view('admin.orders.edit', compact('order', 'products'));
    }

    /**
     * Update Order - 更新订单
     */
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if (!in_array($order->order_status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Chỉ có thể chỉnh sửa đơn hàng ở trạng thái Chờ xác nhận hoặc Đã xác nhận');
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'required|email|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_district' => 'required|string|max:255',
            'shipping_ward' => 'required|string|max:255',
            'shipping_address' => 'required|string|max:500',
            'shipping_method' => 'required|string',
            'payment_method' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // 更新客户信息
            $order->update([
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'shipping_city' => $request->shipping_city,
                'shipping_district' => $request->shipping_district,
                'shipping_ward' => $request->shipping_ward,
                'shipping_address' => $request->shipping_address,
                'shipping_method' => $request->shipping_method,
                'payment_method' => $request->payment_method,
            ]);

            // 删除旧订单项
            $order->items()->delete();

            // 添加新订单项
            $subtotal = 0;
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $price = $product->price;
                $quantity = $item['quantity'];
                $itemSubtotal = $price * $quantity;

                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $itemSubtotal,
                    'total_price' => $itemSubtotal,
                    'image_path' => $product->image,
                ]);

                $subtotal += $itemSubtotal;
            }

            // 重新计算总价
            $shippingFee = $order->shipping_fee ?? 0;
            $discountAmount = $order->discount_amount ?? 0;
            $finalTotal = $subtotal + $shippingFee - $discountAmount;

            $order->update([
                'sub_total' => $subtotal,
                'total_price' => $subtotal,
                'final_total' => $finalTotal,
            ]);

            // 添加时间线
            $order->addTimeline(
                'order_edited',
                'Chỉnh sửa đơn hàng',
                'Đơn hàng đã được chỉnh sửa bởi ' . (Auth::check() ? Auth::user()->name : 'System')
            );

            DB::commit();

            return redirect()->route('admin.orders.show', $order->id)
                ->with('success', 'Cập nhật đơn hàng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Create Order - 手动创建订单
     */
    public function create()
    {
        $users = User::where('role', 'user')->get();
        $products = Product::where('status', 'active')->with('variants')->get();

        return view('admin.orders.create', compact('users', 'products'));
    }

    /**
     * Store Order - 保存新订单
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'required|email|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_district' => 'required|string|max:255',
            'shipping_ward' => 'required|string|max:255',
            'shipping_address' => 'required|string|max:500',
            'shipping_method' => 'required|string',
            'payment_method' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // 生成订单号
            $orderCode = 'DH' . strtoupper(Str::random(8)) . Carbon::now()->format('Ymd');

            // 计算总价
            $subtotal = 0;
            $items = [];
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $price = $product->price;
                $quantity = $item['quantity'];
                $itemSubtotal = $price * $quantity;
                $subtotal += $itemSubtotal;

                $items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $shippingFee = $request->shipping_fee ?? 0;
            $discountAmount = $request->discount_amount ?? 0;
            $finalTotal = $subtotal + $shippingFee - $discountAmount;

            // 确定初始状态
            $initialStatus = $request->payment_method === 'cash' ? 'pending' : 'awaiting_payment';
            $paymentStatus = $request->payment_method === 'cash' ? 'pending' : 'awaiting_payment';

            // 创建订单
            $order = Order::create([
                'user_id' => $request->user_id,
                'order_code' => $orderCode,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'shipping_city' => $request->shipping_city,
                'shipping_district' => $request->shipping_district,
                'shipping_ward' => $request->shipping_ward,
                'shipping_address' => $request->shipping_address,
                'shipping_method' => $request->shipping_method,
                'shipping_fee' => $shippingFee,
                'payment_method' => $request->payment_method,
                'payment_status' => $paymentStatus,
                'sub_total' => $subtotal,
                'total_price' => $subtotal,
                'discount_amount' => $discountAmount,
                'final_total' => $finalTotal,
                'order_status' => $initialStatus,
                'order_date' => now(),
            ]);

            // 创建订单项
            foreach ($items as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'total_price' => $item['subtotal'],
                    'image_path' => $item['product']->image,
                ]);
            }

            // 添加时间线
            $order->addTimeline(
                'order_created',
                'Tạo đơn hàng thủ công',
                'Đơn hàng được tạo bởi ' . (Auth::check() ? Auth::user()->name : 'System')
            );

            DB::commit();

            return redirect()->route('admin.orders.show', $order->id)
                ->with('success', 'Tạo đơn hàng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }
}
