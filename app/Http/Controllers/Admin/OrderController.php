<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'promotion', 'orderDetails.product']);

        // Tìm kiếm theo ID đơn hàng
        if ($request->filled('order_id')) {
            $query->where('id', $request->order_id);
        }

        // Tìm kiếm theo mã đơn hàng
        if ($request->filled('order_code')) {
            $query->where('order_code', 'like', '%' . $request->order_code . '%');
        }

        // Tìm kiếm theo tên khách hàng
        if ($request->filled('customer_name')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->customer_name . '%');
            });
        }

        // Tìm kiếm theo email khách hàng
        if ($request->filled('customer_email')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('email', 'like', '%' . $request->customer_email . '%');
            });
        }

        // Lọc theo trạng thái đơn hàng
        if ($request->filled('order_status')) {
            $query->where('order_status', $request->order_status);
        }

        // Lọc theo trạng thái thanh toán
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Lọc theo phương thức thanh toán
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Lọc theo khoảng ngày
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Lọc theo khoảng tổng tiền
        if ($request->filled('total_from')) {
            $query->where('final_total', '>=', $request->total_from);
        }

        if ($request->filled('total_to')) {
            $query->where('final_total', '<=', $request->total_to);
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Phân trang
        $perPage = $request->get('per_page', 15);
        $orders = $query->paginate($perPage)->withQueryString();

        // Thống kê
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('order_status', 'pending')->count(),
            'processing_orders' => Order::where('order_status', 'processing')->count(),
            'completed_orders' => Order::where('order_status', 'completed')->count(),
            'cancelled_orders' => Order::where('order_status', 'cancelled')->count(),
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'today_revenue' => Order::whereDate('created_at', today())
                ->where('order_status', 'completed')
                ->sum('final_total'),
            'month_revenue' => Order::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->where('order_status', 'completed')
                ->sum('final_total'),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('role', 'user')->get();
        $products = Product::where('status', 'active')->get();
        $promotions = Promotion::available()->get();

        return view('admin.orders.create', compact('users', 'products', 'promotions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|string|max:500',
            'shipping_phone' => 'nullable|string|max:20',
            'shipping_fee' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,bank,momo,paypal',
            'promotion_id' => 'nullable|exists:promotions,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // Tạo mã đơn hàng
            $orderCode = 'ORD-' . strtoupper(Str::random(8));

            // Tính tổng tiền
            $totalPrice = 0;
            $orderDetails = [];

            foreach ($request->products as $productData) {
                $product = Product::find($productData['product_id']);
                $quantity = $productData['quantity'];
                $price = $product->price;
                $subtotal = $price * $quantity;

                $totalPrice += $subtotal;

                $orderDetails[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ];
            }

            // Tính giảm giá
            $discountAmount = 0;
            if ($request->promotion_id) {
                $promotion = Promotion::find($request->promotion_id);
                if ($promotion && $promotion->status === 'active') {
                    if ($promotion->discount_type === 'percent') {
                        $discountAmount = ($totalPrice * $promotion->discount_value) / 100;
                    } else {
                        $discountAmount = $promotion->discount_value;
                    }
                }
            }

            $shippingFee = $request->shipping_fee ?? 0;
            $finalTotal = $totalPrice - $discountAmount + $shippingFee;

            // Tạo đơn hàng
            $order = Order::create([
                'user_id' => $request->user_id,
                'promotion_id' => $request->promotion_id,
                'order_code' => $orderCode,
                'total_price' => $totalPrice,
                'discount_amount' => $discountAmount,
                'final_total' => $finalTotal,
                'shipping_fee' => $shippingFee,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'shipping_address' => $request->shipping_address,
                'shipping_phone' => $request->shipping_phone,
                'notes' => $request->notes,
            ]);

            // Tạo chi tiết đơn hàng
            foreach ($orderDetails as $detail) {
                $order->orderDetails()->create($detail);
            }

            // Cập nhật số lần sử dụng promotion
            if ($request->promotion_id) {
                Promotion::where('id', $request->promotion_id)
                    ->increment('used_count');
            }

            DB::commit();

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Đơn hàng đã được tạo thành công!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo đơn hàng: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'promotion', 'orderDetails.product']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $users = User::where('role', 'user')->get();
        $promotions = Promotion::available()->get();

        return view('admin.orders.edit', compact('order', 'users', 'promotions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'order_status' => 'required|in:pending,processing,completed,cancelled,refunded',
            'payment_status' => 'required|in:pending,paid,failed',
            'shipping_address' => 'required|string|max:500',
            'shipping_phone' => 'nullable|string|max:20',
            'shipping_fee' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $order->update([
            'order_status' => $request->order_status,
            'payment_status' => $request->payment_status,
            'shipping_address' => $request->shipping_address,
            'shipping_phone' => $request->shipping_phone,
            'shipping_fee' => $request->shipping_fee ?? 0,
            'notes' => $request->notes,
        ]);

        // Tính lại final_total nếu có thay đổi shipping_fee
        if ($request->has('shipping_fee')) {
            $finalTotal = $order->total_price - $order->discount_amount + ($request->shipping_fee ?? 0);
            $order->update(['final_total' => $finalTotal]);
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Đơn hàng đã được cập nhật thành công!');
    }


    /**
     * Restore soft deleted order
     */
    public function restore($id)
    {
        $order = Order::withTrashed()->findOrFail($id);
        $order->restore();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Đơn hàng đã được khôi phục thành công!');
    }
    /**
     * Get order statistics for dashboard
     */
    public function statistics()
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('order_status', 'pending')->count(),
            'processing_orders' => Order::where('order_status', 'processing')->count(),
            'completed_orders' => Order::where('order_status', 'completed')->count(),
            'cancelled_orders' => Order::where('order_status', 'cancelled')->count(),
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'today_revenue' => Order::whereDate('created_at', today())
                ->where('order_status', 'completed')
                ->sum('final_total'),
            'month_revenue' => Order::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->where('order_status', 'completed')
                ->sum('final_total'),
            'recent_orders' => Order::with('user')
                ->latest()
                ->limit(5)
                ->get(),
        ];

        return response()->json($stats);
    }
}
