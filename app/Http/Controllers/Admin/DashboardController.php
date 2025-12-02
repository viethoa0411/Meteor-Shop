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
        $totalAllOrders = Order::count();

        // Tính toán so sánh với tháng trước
        $lastMonth = $month - 1;
        $lastMonthYear = $year;
        if ($lastMonth < 1) {
            $lastMonth = 12;
            $lastMonthYear = $year - 1;
        }

        // So sánh người dùng
        $lastMonthUsers = User::where('role', 'user')
            ->whereYear('created_at', $lastMonthYear)
            ->whereMonth('created_at', $lastMonth)
            ->count();
        $thisMonthUsers = User::where('role', 'user')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();
        $userGrowth = $lastMonthUsers > 0
            ? round((($thisMonthUsers - $lastMonthUsers) / $lastMonthUsers) * 100, 1)
            : ($thisMonthUsers > 0 ? 100 : 0);

        // So sánh đơn hàng
        $lastMonthOrders = Order::whereYear('created_at', $lastMonthYear)
            ->whereMonth('created_at', $lastMonth)
            ->count();
        $orderGrowth = $lastMonthOrders > 0
            ? round((($totalOrders - $lastMonthOrders) / $lastMonthOrders) * 100, 1)
            : ($totalOrders > 0 ? 100 : 0);

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

        // ========== THỐNG KÊ ĐƠN HÀNG ==========
        // Lấy filter từ request
        $orderFilterType = $request->input('order_filter_type', 'last_30_days'); // today, last_7_days, last_15_days, last_30_days, all, month, date_range
        $orderFilterMonth = $request->input('order_month');
        $orderFilterYear = $request->input('order_year');
        $orderFilterStartDate = $request->input('order_start_date');
        $orderFilterEndDate = $request->input('order_end_date');
        $orderFilterStatus = $request->input('order_status', 'all');

        // Query đơn hàng với filter
        $ordersQuery = Order::with('user');

        // Áp dụng filter theo loại
        switch ($orderFilterType) {
            case 'today':
                // Filter hôm nay
                $ordersQuery->whereDate('created_at', now()->toDateString());
                break;

            case 'last_7_days':
                // Filter 7 ngày gần nhất
                $ordersQuery->whereDate('created_at', '>=', now()->subDays(7)->toDateString());
                break;

            case 'last_15_days':
                // Filter 15 ngày gần nhất
                $ordersQuery->whereDate('created_at', '>=', now()->subDays(15)->toDateString());
                break;

            case 'all':
                // Hiển thị tất cả - không filter thời gian
                break;

            case 'month':
                // Filter theo tháng/năm
                if ($orderFilterMonth && $orderFilterYear) {
                    $ordersQuery->whereYear('created_at', $orderFilterYear)
                        ->whereMonth('created_at', $orderFilterMonth);
                } else {
                    // Mặc định tháng hiện tại
                    $orderFilterMonth = $orderFilterMonth ?? $month;
                    $orderFilterYear = $orderFilterYear ?? $year;
                    $ordersQuery->whereYear('created_at', $orderFilterYear)
                        ->whereMonth('created_at', $orderFilterMonth);
                }
                break;

            case 'date_range':
                // Filter theo khoảng thời gian
                if ($orderFilterStartDate) {
                    $ordersQuery->whereDate('created_at', '>=', $orderFilterStartDate);
                }
                if ($orderFilterEndDate) {
                    $ordersQuery->whereDate('created_at', '<=', $orderFilterEndDate);
                }
                break;

            case 'last_30_days':
            default:
                // Mặc định: 30 ngày gần nhất
                $ordersQuery->whereDate('created_at', '>=', now()->subDays(30)->toDateString());
                break;
        }

        // Filter theo trạng thái
        if ($orderFilterStatus && $orderFilterStatus !== 'all') {
            $ordersQuery->where('order_status', $orderFilterStatus);
        }

        // Phân trang đơn hàng
        $perPage = 15;
        $filteredOrders = $ordersQuery->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // Thống kê đơn hàng theo trạng thái (dùng cùng filter với danh sách)
        $statsQuery = Order::select('order_status', DB::raw('count(*) as count'));

        // Áp dụng cùng filter thời gian
        switch ($orderFilterType) {
            case 'today':
                $statsQuery->whereDate('created_at', now()->toDateString());
                break;
            case 'last_7_days':
                $statsQuery->whereDate('created_at', '>=', now()->subDays(7)->toDateString());
                break;
            case 'last_15_days':
                $statsQuery->whereDate('created_at', '>=', now()->subDays(15)->toDateString());
                break;
            case 'all':
                break;
            case 'month':
                if ($orderFilterMonth && $orderFilterYear) {
                    $statsQuery->whereYear('created_at', $orderFilterYear)
                        ->whereMonth('created_at', $orderFilterMonth);
                } else {
                    $statsQuery->whereYear('created_at', $orderFilterYear ?? $year)
                        ->whereMonth('created_at', $orderFilterMonth ?? $month);
                }
                break;
            case 'date_range':
                if ($orderFilterStartDate) {
                    $statsQuery->whereDate('created_at', '>=', $orderFilterStartDate);
                }
                if ($orderFilterEndDate) {
                    $statsQuery->whereDate('created_at', '<=', $orderFilterEndDate);
                }
                break;
            case 'last_30_days':
            default:
                $statsQuery->whereDate('created_at', '>=', now()->subDays(30)->toDateString());
                break;
        }

        $orderStatsByStatus = $statsQuery->groupBy('order_status')
            ->get()
            ->pluck('count', 'order_status');

        // Đảm bảo tất cả trạng thái đều có trong thống kê (mặc định 0)
        $allStatuses = ['pending', 'processing', 'shipping', 'completed', 'cancelled'];
        foreach ($allStatuses as $status) {
            if (!isset($orderStatsByStatus[$status])) {
                $orderStatsByStatus[$status] = 0;
            }
        }

        // Dữ liệu biểu đồ tăng trưởng đơn hàng (7 ngày gần nhất)
        // Biểu đồ luôn hiển thị 7 ngày gần nhất, không phụ thuộc vào filter tháng/ngày
        $growthChartData = [];
        $growthChartLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->format('d/m');
            $growthChartLabels[] = $dateStr;

            $countQuery = Order::whereDate('created_at', $date->toDateString());

            // Chỉ áp dụng filter trạng thái nếu có
            if ($orderFilterStatus && $orderFilterStatus !== 'all') {
                $countQuery->where('order_status', $orderFilterStatus);
            }

            $growthChartData[] = $countQuery->count();
        }

        // Tính tăng trưởng so với ngày trước
        $todayOrders = $growthChartData[6] ?? 0;
        $yesterdayOrders = $growthChartData[5] ?? 0;
        $orderGrowthRate = $yesterdayOrders > 0
            ? round((($todayOrders - $yesterdayOrders) / $yesterdayOrders) * 100, 1)
            : ($todayOrders > 0 ? 100 : 0);

        // Tổng số đơn hàng theo filter
        $totalFilteredOrders = $ordersQuery->count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalOrders',
            'totalAllOrders',
            'userGrowth',
            'orderGrowth',
            'thisMonthUsers',
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
            'showTargetAlert',
            // Thống kê đơn hàng
            'filteredOrders',
            'orderFilterType',
            'orderFilterMonth',
            'orderFilterYear',
            'orderFilterStartDate',
            'orderFilterEndDate',
            'orderFilterStatus',
            'orderStatsByStatus',
            'growthChartData',
            'growthChartLabels',
            'orderGrowthRate',
            'totalFilteredOrders'
        ));
    }
}
