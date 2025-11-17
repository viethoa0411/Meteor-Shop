<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\MonthlyTarget;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $month = now()->month;
        $year = now()->year;

        // Thống kê người dùng và đơn hàng
        $totalUsers = User::where('role', 'user')->count();
        $totalOrders = Order::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        $todayRevenue = Order::whereDate('created_at', now()->toDateString())
            ->where('order_status', 'completed')
            ->sum('final_total');

        $currentMonthRevenue = Order::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('order_status', 'completed')
            ->sum('final_total');

        // Kiểm tra mục tiêu tháng
        $monthlyTargetModel = MonthlyTarget::where('year', $year)
            ->where('month', $month)
            ->first();

        $showTargetAlert = false;
        if (!$monthlyTargetModel) {
            $showTargetAlert = true; // hiển thị cảnh báo
        }

        // Gán giá trị cho biến monthlyTarget
        $monthlyTarget = $monthlyTargetModel ? $monthlyTargetModel->target_amount : 0;

        // Lọc doanh thu theo khoảng thời gian
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $filteredRevenue = null;
        if ($startDate || $endDate) {
            $query = Order::where('order_status', 'completed');
            if ($startDate) $query->whereDate('created_at', '>=', $startDate);
            if ($endDate) $query->whereDate('created_at', '<=', $endDate);
            $filteredRevenue = $query->sum('final_total');
        }

        // Biểu đồ doanh thu theo năm hiện tại
        $monthlyRevenue = Order::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(final_total) as revenue')
        )
            ->whereYear('created_at', $year)
            ->where('order_status', 'completed')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyRevenueMap = $monthlyRevenue->keyBy('month');
        $revenueData = [];
        for ($i = 1; $i <= 12; $i++) {
            $revenueData[] = $monthlyRevenueMap[$i]->revenue ?? 0;
        }

        // Lấy 5 đơn hàng gần nhất cùng user, sản phẩm và category
        $recentOrders = Order::with('user', 'items.product.category')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Gom tất cả sản phẩm từ các đơn gần nhất thành 1 list duy nhất
        $recentProducts = $recentOrders->flatMap(function ($order) {
            return $order->items->map(function ($item) use ($order) {
                return [
                    'product_id'    => $item->product->id ?? null,
                    'product_name'  => $item->product->name ?? 'Sản phẩm không xác định',
                    'category_name' => $item->product->category->name ?? '-',
                    'price'         => $item->price,
                    'image'         => $item->product->image ?? null,
                ];
            });
        })->unique('product_id')->values(); // loại bỏ sản phẩm trùng nhau

        // Lấy đơn hàng gần nhất
        $latestOrder = Order::with('items.product.category', 'user')
            ->latest()
            ->first();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalOrders',
            'todayRevenue',
            'currentMonthRevenue',
            'monthlyTarget',
            'revenueData',
            'filteredRevenue',
            'startDate',
            'endDate',
            'recentOrders',
            'latestOrder',
            'recentProducts',
            'showTargetAlert'
        ));
    }
}
