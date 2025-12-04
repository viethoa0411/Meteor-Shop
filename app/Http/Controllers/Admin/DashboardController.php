<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\MonthlyTarget;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $month = now()->month;
        $year = now()->year;

        // Thống kê người dùng, đơn hàng, sản phẩm
        $totalUsers = User::where('role', 'user')->count();
        $totalOrders = Order::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();
        $totalAllOrders = Order::count();
        $totalProducts = Product::count();

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

        // Tổng doanh thu tất cả thời gian (đơn hoàn thành)
        $totalCompletedRevenue = Order::where('order_status', 'completed')
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
        $allStatuses = ['pending', 'processing', 'shipping', 'delivered', 'completed', 'cancelled'];
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

        // ========== BIỂU ĐỒ & KPI SẢN PHẨM ==========
        // Thống kê cho 30 ngày gần nhất, chỉ tính đơn hoàn thành
        $productOrders = Order::with('items.product.category')
            ->where('order_status', 'completed')
            ->whereDate('created_at', '>=', now()->subDays(30)->toDateString())
            ->get();

        // Top sản phẩm theo doanh thu
        $productAggregates = [];
        foreach ($productOrders as $order) {
            foreach ($order->items as $item) {
                if (!$item->product) {
                    continue;
                }
                $pid = $item->product->id;
                if (!isset($productAggregates[$pid])) {
                    $productAggregates[$pid] = [
                        'name' => $item->product->name ?? 'Sản phẩm',
                        'revenue' => 0,
                        'quantity' => 0,
                    ];
                }
                $productAggregates[$pid]['revenue'] += ($item->price ?? 0) * ($item->quantity ?? 1);
                $productAggregates[$pid]['quantity'] += ($item->quantity ?? 1);
            }
        }

        // Sắp xếp theo doanh thu giảm dần, lấy top 7
        usort($productAggregates, function ($a, $b) {
            return $b['revenue'] <=> $a['revenue'];
        });
        $topProducts = array_slice($productAggregates, 0, 7);

        $topProductsLabels = array_map(fn ($p) => $p['name'], $topProducts);
        $topProductsRevenue = array_map(fn ($p) => round($p['revenue']), $topProducts);

        // Số sản phẩm đã bán (ít nhất 1 lần) trong 30 ngày gần nhất
        $soldProductsLast30Days = count($productAggregates);

        // Doanh thu theo danh mục
        $categoryAggregates = [];
        foreach ($productOrders as $order) {
            foreach ($order->items as $item) {
                $category = $item->product->category ?? null;
                $cid = $category->id ?? null;
                if (!$cid) {
                    continue;
                }
                if (!isset($categoryAggregates[$cid])) {
                    $categoryAggregates[$cid] = [
                        'name' => $category->name ?? 'Danh mục',
                        'revenue' => 0,
                    ];
                }
                $categoryAggregates[$cid]['revenue'] += ($item->price ?? 0) * ($item->quantity ?? 1);
            }
        }

        usort($categoryAggregates, function ($a, $b) {
            return $b['revenue'] <=> $a['revenue'];
        });
        $topCategories = array_slice($categoryAggregates, 0, 7);

        $categoryLabels = array_map(fn ($c) => $c['name'], $topCategories);
        $categoryRevenue = array_map(fn ($c) => round($c['revenue']), $topCategories);

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalOrders',
            'totalAllOrders',
            'userGrowth',
            'orderGrowth',
            'thisMonthUsers',
            'todayRevenue',
            'currentMonthRevenue',
            'totalCompletedRevenue',
            'totalProducts',
            'soldProductsLast30Days',
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
            'totalFilteredOrders',
            'topProductsLabels',
            'topProductsRevenue',
            'categoryLabels',
            'categoryRevenue'
        ));
    }

    /**
     * API Control Chart doanh thu theo ngày (Actual, Mean, UCL, LCL).
     * GET /admin/api/dashboard/revenue/control-chart?range=7|30|90|month|custom&from&to
     */
    public function revenueControlChartApi(Request $request)
    {
        $range = $request->query('range', '30');
        $from = $request->query('from');
        $to = $request->query('to');

        $now = now();
        switch ($range) {
            case '7':
                $start = $now->copy()->subDays(6)->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case '90':
                $start = $now->copy()->subDays(89)->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case 'month':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'custom':
                $start = $from ? now()->parse($from)->startOfDay() : $now->copy()->subDays(29)->startOfDay();
                $end = $to ? now()->parse($to)->endOfDay() : $now->copy()->endOfDay();
                break;
            case '30':
            default:
                $start = $now->copy()->subDays(29)->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
        }

        // Query doanh thu theo ngày trong khoảng thời gian
        $rows = Order::selectRaw('DATE(created_at) as date, SUM(final_total) as revenue')
            ->whereBetween('created_at', [$start, $end])
            ->where(function ($q) {
                $q->where('payment_status', 'paid')
                    ->orWhere('order_status', 'completed');
            })
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Map doanh thu theo ngày, fill 0 cho ngày không có đơn
        $revenueByDate = $rows->pluck('revenue', 'date');
        $dates = [];
        $actual = [];

        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $dateStr = $cursor->toDateString();
            $dates[] = $dateStr;
            $actual[] = (float) ($revenueByDate[$dateStr] ?? 0);
            $cursor->addDay();
        }

        $count = count($actual);
        if ($count === 0) {
            return response()->json([
                'dates' => [],
                'actual' => [],
                'mean' => 0,
                'ucl' => 0,
                'lcl' => 0,
            ]);
        }

        $mean = array_sum($actual) / $count;

        // Độ lệch chuẩn mẫu
        $variance = 0;
        foreach ($actual as $value) {
            $variance += pow($value - $mean, 2);
        }
        $variance = $variance / ($count ?: 1);
        $stdDev = sqrt($variance);

        $ucl = $mean + 3 * $stdDev;
        $lcl = max(0, $mean - 3 * $stdDev);

        return response()->json([
            'dates' => $dates,
            'actual' => $actual,
            'mean' => round($mean, 2),
            'ucl' => round($ucl, 2),
            'lcl' => round($lcl, 2),
        ]);
    }
}
