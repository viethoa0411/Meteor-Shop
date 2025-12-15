<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Review;
use App\Models\ReviewAuditLog;
use App\Models\MonthlyTarget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // Constants cho Review Status
    const REVIEW_STATUS_PENDING = 'pending';
    const REVIEW_STATUS_APPROVED = 'approved';
    const REVIEW_STATUS_REJECTED = 'rejected';
    const REVIEW_STATUS_HIDDEN = 'hidden';

    // Constants cho Content Preview và Cache
    const CONTENT_PREVIEW_LENGTH = 100;
    const COMMENTS_CACHE_TTL = 60; // 60 giây
    const COMMENTS_PER_PAGE = 10;
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

    /**
     * API Combined Chart: Doanh thu & Đơn hàng theo thời gian.
     * GET /admin/api/dashboard/revenue-orders-chart?range=7|30|90|month|custom&from&to&group_by=day|week|month
     */
    public function revenueOrdersChartApi(Request $request)
    {
        $range = $request->query('range', '30');
        $from = $request->query('from');
        $to = $request->query('to');
        $groupBy = $request->query('group_by', 'day'); // day, week, month

        $now = now();
        switch ($range) {
            case 'today':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
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
                try {
                    $start = $from ? \Carbon\Carbon::parse($from)->startOfDay() : $now->copy()->subDays(29)->startOfDay();
                    $end = $to ? \Carbon\Carbon::parse($to)->endOfDay() : $now->copy()->endOfDay();
                    // Validate: start phải <= end
                    if ($start->gt($end)) {
                        $temp = $start;
                        $start = $end;
                        $end = $temp;
                    }
                } catch (\Exception $e) {
                    // Nếu parse lỗi, dùng mặc định 30 ngày
                    $start = $now->copy()->subDays(29)->startOfDay();
                    $end = $now->copy()->endOfDay();
                }
                break;
            case '30':
            default:
                $start = $now->copy()->subDays(29)->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
        }

        // Tính period trước để so sánh
        $periodDays = $start->diffInDays($end);
        // Chỉ so sánh nếu period hợp lý (không quá lớn)
        if ($periodDays > 365) {
            $previousStart = null;
            $previousEnd = null;
        } else {
            $previousStart = $start->copy()->subDays($periodDays + 1);
            $previousEnd = $start->copy()->subSecond();
        }

        // Query doanh thu và đơn hàng theo group_by
        $labels = [];
        $revenue = [];
        $orders = [];

        if ($groupBy === 'day') {
            // Group by day
            $revenueData = Order::selectRaw('DATE(created_at) as label, SUM(final_total) as revenue')
                ->whereBetween('created_at', [$start, $end])
                ->where(function ($q) {
                    $q->where('payment_status', 'paid')
                        ->orWhere('order_status', 'completed');
                })
                ->groupBy('label')
                ->orderBy('label')
                ->get()
                ->pluck('revenue', 'label');

            $ordersData = Order::selectRaw('DATE(created_at) as label, COUNT(*) as orders')
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('label')
                ->orderBy('label')
                ->get()
                ->pluck('orders', 'label');

            $cursor = $start->copy();
            while ($cursor->lte($end)) {
                $dateStr = $cursor->toDateString();
                $labels[] = $cursor->format('d/m');
                $revenue[] = (float) ($revenueData[$dateStr] ?? 0);
                $orders[] = (int) ($ordersData[$dateStr] ?? 0);
                $cursor->addDay();
            }
        } elseif ($groupBy === 'week') {
            // Group by week - sử dụng YEARWEEK của MySQL (mode 1 = Monday as first day)
            // YEARWEEK trả về format YYYYWW (ví dụ: 202501 cho tuần 1 năm 2025)
            $revenueData = Order::selectRaw('YEARWEEK(created_at, 1) as week, SUM(final_total) as revenue')
                ->whereBetween('created_at', [$start, $end])
                ->where(function ($q) {
                    $q->where('payment_status', 'paid')
                        ->orWhere('order_status', 'completed');
                })
                ->groupBy('week')
                ->orderBy('week')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [(string)$item->week => $item->revenue];
                });

            $ordersData = Order::selectRaw('YEARWEEK(created_at, 1) as week, COUNT(*) as orders')
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('week')
                ->orderBy('week')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [(string)$item->week => $item->orders];
                });

            // Lặp qua từng tuần trong khoảng thời gian
            $cursor = $start->copy()->startOfWeek();
            while ($cursor->lte($end)) {
                // Tính YEARWEEK tương tự MySQL: year * 100 + week number
                // Carbon week() trả về ISO week number (1-53)
                $year = (int)$cursor->format('Y');
                $weekNum = (int)$cursor->format('W'); // ISO week number
                $yearWeek = $year * 100 + $weekNum;
                
                $labels[] = 'W' . $weekNum;
                $revenue[] = (float) ($revenueData[(string)$yearWeek] ?? 0);
                $orders[] = (int) ($ordersData[(string)$yearWeek] ?? 0);
                $cursor->addWeek();
            }
        } else {
            // Group by month
            $revenueData = Order::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(final_total) as revenue')
                ->whereBetween('created_at', [$start, $end])
                ->where(function ($q) {
                    $q->where('payment_status', 'paid')
                        ->orWhere('order_status', 'completed');
                })
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->pluck('revenue', 'month');

            $ordersData = Order::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as orders')
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->pluck('orders', 'month');

            $cursor = $start->copy()->startOfMonth();
            while ($cursor->lte($end)) {
                $monthStr = $cursor->format('Y-m');
                $labels[] = 'T' . $cursor->month;
                $revenue[] = (float) ($revenueData[$monthStr] ?? 0);
                $orders[] = (int) ($ordersData[$monthStr] ?? 0);
                $cursor->addMonth();
            }
        }

        // Tính tổng và so sánh với period trước
        $totalRevenue = array_sum($revenue);
        $totalOrders = array_sum($orders);

        // Tính doanh thu và đơn hàng period trước (nếu có)
        if ($previousStart && $previousEnd) {
            $previousRevenue = Order::whereBetween('created_at', [$previousStart, $previousEnd])
                ->where(function ($q) {
                    $q->where('payment_status', 'paid')
                        ->orWhere('order_status', 'completed');
                })
                ->sum('final_total');

            $previousOrders = Order::whereBetween('created_at', [$previousStart, $previousEnd])
                ->count();

            // Tính % thay đổi
            $changeRevenue = $previousRevenue > 0
                ? round((($totalRevenue - $previousRevenue) / $previousRevenue) * 100, 1)
                : ($totalRevenue > 0 ? 100 : 0);

            $changeOrders = $previousOrders > 0
                ? round((($totalOrders - $previousOrders) / $previousOrders) * 100, 1)
                : ($totalOrders > 0 ? 100 : 0);
        } else {
            // Không có period trước để so sánh
            $changeRevenue = 0;
            $changeOrders = 0;
        }

        return response()->json([
            'labels' => $labels,
            'revenue' => $revenue,
            'orders' => $orders,
            'total_revenue' => round($totalRevenue, 0),
            'total_orders' => $totalOrders,
            'change_revenue' => $changeRevenue,
            'change_orders' => $changeOrders,
        ]);
    }

    /**
     * API Order Status Ratio với trend.
     * GET /admin/api/dashboard/order-status-ratio?from=2025-12-01&to=2025-12-31
     */
    public function orderStatusRatioApi(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $range = $request->query('range', '7'); // today, 7, 30, 90, month, custom

        $now = now();
        switch ($range) {
            case 'today':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case '30':
                $start = $now->copy()->subDays(29)->startOfDay();
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
                try {
                    $start = $from ? \Carbon\Carbon::parse($from)->startOfDay() : $now->copy()->subDays(6)->startOfDay();
                    $end = $to ? \Carbon\Carbon::parse($to)->endOfDay() : $now->copy()->endOfDay();
                    if ($start->gt($end)) {
                        $temp = $start;
                        $start = $end;
                        $end = $temp;
                    }
                } catch (\Exception $e) {
                    $start = $now->copy()->subDays(6)->startOfDay();
                    $end = $now->copy()->endOfDay();
                }
                break;
            case '7':
            default:
                $start = $now->copy()->subDays(6)->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
        }

        // Tính period trước để so sánh
        $periodDays = $start->diffInDays($end);
        $previousStart = null;
        $previousEnd = null;
        if ($periodDays <= 365) {
            $previousStart = $start->copy()->subDays($periodDays + 1);
            $previousEnd = $start->copy()->subSecond();
        }

        // Query số lượng theo trạng thái (kỳ hiện tại)
        $currentData = Order::selectRaw('order_status, COUNT(*) as total')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('order_status')
            ->get()
            ->pluck('total', 'order_status');

        // Query số lượng theo trạng thái (kỳ trước)
        $previousData = [];
        if ($previousStart && $previousEnd) {
            $previousData = Order::selectRaw('order_status, COUNT(*) as total')
                ->whereBetween('created_at', [$previousStart, $previousEnd])
                ->groupBy('order_status')
                ->get()
                ->pluck('total', 'order_status');
        }

        // Tổng số đơn
        $totalOrders = $currentData->sum();

        // Mapping trạng thái
        $statusMap = [
            'pending' => ['label' => 'Chờ xử lý', 'color' => '#F59E0B'],
            'processing' => ['label' => 'Đang xử lý', 'color' => '#3B82F6'],
            'shipping' => ['label' => 'Đang giao', 'color' => '#6366F1'],
            'delivered' => ['label' => 'Đã giao', 'color' => '#10B981'],
            'completed' => ['label' => 'Hoàn thành', 'color' => '#10B981'],
            'cancelled' => ['label' => 'Hủy', 'color' => '#EF4444'],
            'return_requested' => ['label' => 'Yêu cầu đổi trả', 'color' => '#6B7280'],
            'returned' => ['label' => 'Đã đổi trả', 'color' => '#6B7280'],
        ];

        // Tạo dữ liệu response - chỉ hiển thị status có count > 0
        $data = [];
        foreach ($statusMap as $status => $info) {
            $count = (int) ($currentData[$status] ?? 0);
            // Chỉ thêm vào data nếu có đơn hàng (count > 0)
            if ($count > 0) {
                $ratio = $totalOrders > 0 ? round(($count / $totalOrders) * 100, 1) : 0;
                
                // Tính trend
                $previousCount = (int) ($previousData[$status] ?? 0);
                $trend = 0;
                if ($previousCount > 0) {
                    $trend = round((($count - $previousCount) / $previousCount) * 100, 1);
                } elseif ($count > 0 && $previousCount == 0) {
                    $trend = 100;
                }

                $data[] = [
                    'status' => $status,
                    'label' => $info['label'],
                    'color' => $info['color'],
                    'count' => $count,
                    'ratio' => $ratio,
                    'trend' => $trend,
                ];
            }
        }

        // Sắp xếp theo số lượng giảm dần
        usort($data, function ($a, $b) {
            return $b['count'] <=> $a['count'];
        });

        return response()->json([
            'total_orders' => $totalOrders,
            'from' => $start->toDateString(),
            'to' => $end->toDateString(),
            'data' => $data,
        ]);
    }

    /**
     * API Category Revenue - Doanh thu theo danh mục
     * GET /admin/api/dashboard/category-revenue?range=30&from=2025-12-01&to=2025-12-31
     */
    public function categoryRevenueApi(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $range = $request->query('range', '30'); // 7, 30, 90, custom

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
            case 'custom':
                try {
                    $start = $from ? \Carbon\Carbon::parse($from)->startOfDay() : $now->copy()->subDays(29)->startOfDay();
                    $end = $to ? \Carbon\Carbon::parse($to)->endOfDay() : $now->copy()->endOfDay();
                    if ($start->gt($end)) {
                        $temp = $start;
                        $start = $end;
                        $end = $temp;
                    }
                } catch (\Exception $e) {
                    $start = $now->copy()->subDays(29)->startOfDay();
                    $end = $now->copy()->endOfDay();
                }
                break;
            case '30':
            default:
                $start = $now->copy()->subDays(29)->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
        }

        // Query doanh thu theo danh mục - DỰ TÍNH và THỰC TẾ
        // Logic:
        // - Dự tính: Tất cả đơn hàng không bị hủy và trả lại (order_status NOT IN ('cancelled', 'refunded', 'returned', 'return_requested'))
        // - Thực tế: Tổng tiền của tất cả sản phẩm thuộc danh mục nằm trong đơn hàng đã hoàn thành (completed) → đã thu được tiền thật
        //   Tính từ final_total của đơn hàng, phân bổ theo tỷ lệ subtotal của từng danh mục trong đơn hàng
        $categoryRevenue = DB::table('categories as c')
            ->join('products as p', 'p.category_id', '=', 'c.id')
            ->join('order_details as od', 'od.product_id', '=', 'p.id')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->whereBetween(DB::raw('COALESCE(o.order_date, o.created_at)'), [$start, $end])
            ->select(
                'c.id',
                'c.name as category',
                // Doanh thu dự tính: Tất cả đơn hàng không bị hủy và trả lại
                DB::raw("SUM(CASE 
                    WHEN o.order_status NOT IN ('cancelled', 'refunded', 'returned', 'return_requested') 
                    THEN od.subtotal 
                    ELSE 0 
                    END) AS estimated_revenue"),
                // Doanh thu thực tế: Tổng tiền của tất cả sản phẩm thuộc danh mục nằm trong đơn hàng đã hoàn thành
                // Tính từ final_total của đơn hàng, phân bổ theo tỷ lệ subtotal của từng danh mục
                // Công thức: (od.subtotal / tổng subtotal của đơn hàng) * o.final_total
                DB::raw("SUM(CASE 
                    WHEN o.order_status = 'completed' 
                    THEN (od.subtotal / NULLIF((SELECT SUM(od2.subtotal) FROM order_details od2 WHERE od2.order_id = o.id), 0)) * o.final_total
                    ELSE 0 
                    END) AS actual_revenue")
            )
            ->groupBy('c.id', 'c.name')
            ->orderBy('estimated_revenue', 'desc')
            ->get();

        // Tính tổng doanh thu dự tính và thực tế
        // Lưu ý: 
        // - Tổng doanh thu thực tế = Tổng final_total của các đơn hàng đã hoàn thành (đồng bộ với dashboard index)
        // - Đảm bảo tổng doanh thu thực tế theo danh mục = tổng final_total của các đơn hàng completed
        $totalEstimated = $categoryRevenue->sum('estimated_revenue');
        
        // Tính tổng doanh thu thực tế từ orders.final_total để đồng bộ với dashboard index
        $totalActual = DB::table('orders')
            ->where('order_status', 'completed')
            ->whereBetween(DB::raw('COALESCE(order_date, created_at)'), [$start, $end])
            ->sum('final_total');
        
        $categories = [];
        $estimatedValues = [];
        $actualValues = [];
        $estimatedPercent = [];
        $actualPercent = [];

        foreach ($categoryRevenue as $item) {
            $categories[] = $item->category;
            $estimatedValues[] = (int)$item->estimated_revenue;
            $actualValues[] = (int)$item->actual_revenue;
            $estimatedPercent[] = $totalEstimated > 0 ? round(($item->estimated_revenue / $totalEstimated) * 100, 1) : 0;
            $actualPercent[] = $totalActual > 0 ? round(($item->actual_revenue / $totalActual) * 100, 1) : 0;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $categories,
                'estimated_values' => $estimatedValues,
                'actual_values' => $actualValues,
                'estimated_percent' => $estimatedPercent,
                'actual_percent' => $actualPercent,
                'total_estimated' => (int)$totalEstimated,
                'total_actual' => (int)$totalActual,
            ],
            'from' => $start->toDateString(),
            'to' => $end->toDateString(),
        ]);
    }

    /**
     * API Top Customers - Top khách hàng mua nhiều nhất
     * GET /admin/api/dashboard/top-customers?range=30&from=2025-12-01&to=2025-12-31
     */
    public function topCustomersApi(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $range = $request->query('range', '30'); // 7, 30, 90, custom

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
            case 'custom':
                try {
                    $start = $from ? \Carbon\Carbon::parse($from)->startOfDay() : $now->copy()->subDays(29)->startOfDay();
                    $end = $to ? \Carbon\Carbon::parse($to)->endOfDay() : $now->copy()->endOfDay();
                    if ($start->gt($end)) {
                        $temp = $start;
                        $start = $end;
                        $end = $temp;
                    }
                } catch (\Exception $e) {
                    $start = $now->copy()->subDays(29)->startOfDay();
                    $end = $now->copy()->endOfDay();
                }
                break;
            case '30':
            default:
                $start = $now->copy()->subDays(29)->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
        }

        // Query top customers
        // Đồng bộ với các API khác: dùng created_at và filter payment_status = 'paid' OR order_status = 'completed'
        $topCustomers = DB::table('users as u')
            ->join('orders as o', 'o.user_id', '=', 'u.id')
            ->where(function ($q) {
                $q->where('o.payment_status', 'paid')
                    ->orWhere('o.order_status', 'completed');
            })
            ->whereBetween(DB::raw('COALESCE(o.order_date, o.created_at)'), [$start, $end])
            ->select(
                'u.id',
                'u.name',
                'u.email',
                DB::raw('COUNT(o.id) as orders_count'),
                DB::raw('SUM(o.final_total) as total_spent')
            )
            ->groupBy('u.id', 'u.name', 'u.email')
            ->orderBy('total_spent', 'desc')
            ->limit(5)
            ->get();

        // Tính tổng doanh thu để tính tỉ lệ (đồng bộ filter)
        $totalRevenue = DB::table('orders')
            ->where(function ($q) {
                $q->where('payment_status', 'paid')
                    ->orWhere('order_status', 'completed');
            })
            ->whereBetween(DB::raw('COALESCE(order_date, created_at)'), [$start, $end])
            ->sum('final_total');

        $users = [];
        foreach ($topCustomers as $customer) {
            $percent = $totalRevenue > 0 ? round(($customer->total_spent / $totalRevenue) * 100, 1) : 0;
            
            $users[] = [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'orders' => (int)$customer->orders_count,
                'total' => (int)$customer->total_spent,
                'percent' => $percent,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'users' => $users,
                'totalRevenue' => (int)$totalRevenue,
            ],
            'from' => $start->toDateString(),
            'to' => $end->toDateString(),
        ]);
    }

    /**
     * API Comments - Danh sách bình luận với pagination
     * GET /admin/api/dashboard/comments?status=pending&search=abc&page=1
     */
    public function commentsApi(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('search');
        $page = $request->query('page', 1);
        $perPage = self::COMMENTS_PER_PAGE;
        $cacheBust = $request->query('_t'); // Cache busting parameter

        // Cache key dựa trên parameters
        $cacheKey = "dashboard_comments_" . ($status ?: 'all') . "_" . ($search ?: '') . "_page_" . $page;
        
        // Chỉ cache khi không có search và không có cache busting (vì search thay đổi thường xuyên)
        if (!$search && !$cacheBust) {
            $cached = Cache::get($cacheKey);
            if ($cached) {
                return response()->json($cached);
            }
        }

        // Optimize query: chỉ load first image thay vì tất cả images
        $query = Review::with([
            'user:id,name,email',
            'product:id,name',
            'product.images' => function($q) {
                $q->select('id', 'product_id', 'image')->limit(1);
            }
        ])
            ->select('reviews.*')
            ->join('users', 'users.id', '=', 'reviews.user_id')
            ->join('products', 'products.id', '=', 'reviews.product_id')
            ->distinct()
            ->orderBy('reviews.created_at', 'desc');

        // Filter by status
        if ($status) {
            $query->where('reviews.status', $status);
        }

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%")
                    ->orWhere('reviews.content', 'like', "%{$search}%")
                    ->orWhere('products.name', 'like', "%{$search}%");
            });
        }

        $reviews = $query->paginate($perPage);

        // Format data
        $data = $reviews->map(function ($review) {
            // Lấy product image (first image từ relationship)
            $productImage = null;
            if ($review->product && $review->product->images && $review->product->images->count() > 0) {
                $productImage = $review->product->images->first()->image ?? null;
            }
            
            return [
                'id' => $review->id,
                'user_id' => $review->user_id,
                'user_name' => $review->user->name ?? 'N/A',
                'user_email' => $review->user->email ?? '',
                'product_id' => $review->product_id,
                'product_name' => $review->product->name ?? 'N/A',
                'product_image' => $productImage,
                'content' => $review->content ?? $review->comment ?? '',
                'rating' => $review->rating ?? 0,
                'status' => $review->status ?? self::REVIEW_STATUS_PENDING,
                'created_at' => $review->created_at->format('Y-m-d H:i'),
                'created_at_ago' => $review->created_at->diffForHumans(),
            ];
        });

        $response = [
            'success' => true,
            'data' => [
                'data' => $data,
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ],
        ];

        // Cache response nếu không có search và không có cache busting
        if (!$search && !$cacheBust) {
            Cache::put($cacheKey, $response, self::COMMENTS_CACHE_TTL);
        }

        return response()->json($response);
    }

    /**
     * Helper function để log audit và clear cache
     */
    private function logReviewAction($reviewId, $action, $oldStatus, $newStatus)
    {
        try {
            ReviewAuditLog::create([
                'review_id' => $reviewId,
                'admin_id' => Auth::id(),
                'action' => $action,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Log error nhưng không fail request
            Log::error('Failed to create audit log: ' . $e->getMessage());
        }

        // Clear all comments cache - clear tất cả cache keys liên quan
        $this->clearCommentsCache();
    }

    /**
     * Clear all comments cache
     */
    private function clearCommentsCache()
    {
        // Clear cache với pattern matching
        $statuses = ['all', self::REVIEW_STATUS_PENDING, self::REVIEW_STATUS_APPROVED, self::REVIEW_STATUS_REJECTED, self::REVIEW_STATUS_HIDDEN];
        
        foreach ($statuses as $status) {
            // Clear cache cho tất cả các pages (1-10 pages)
            for ($page = 1; $page <= 10; $page++) {
                Cache::forget("dashboard_comments_{$status}_page_{$page}");
                Cache::forget("dashboard_comments_{$status}__page_{$page}");
            }
            // Clear cache không có page
            Cache::forget("dashboard_comments_{$status}_");
            Cache::forget("dashboard_comments_{$status}");
        }
        
        // Clear cache với search (nếu có)
        Cache::forget("dashboard_comments_all__page_1");
    }

    /**
     * API Approve Comment
     * POST /admin/api/dashboard/comments/{id}/approve
     */
    public function approveComment($id)
    {
        try {
            $review = Review::findOrFail($id);
            $oldStatus = $review->status;
            
            // Validate status transition
            $allowedStatuses = [self::REVIEW_STATUS_PENDING, self::REVIEW_STATUS_REJECTED, self::REVIEW_STATUS_HIDDEN];
            if (!in_array($review->status, $allowedStatuses)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể duyệt bình luận ở trạng thái này',
                ], 400);
            }
            
            $review->status = self::REVIEW_STATUS_APPROVED;
            $review->save();

            // Log audit và clear cache
            $this->logReviewAction($review->id, 'approve', $oldStatus, self::REVIEW_STATUS_APPROVED);

            return response()->json([
                'success' => true,
                'message' => 'Bình luận đã được duyệt',
                'old_status' => $oldStatus, // Để có thể undo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API Reject Comment
     * POST /admin/api/dashboard/comments/{id}/reject
     */
    public function rejectComment($id)
    {
        try {
            $review = Review::findOrFail($id);
            $oldStatus = $review->status;
            
            // Validate status transition
            $allowedStatuses = [self::REVIEW_STATUS_PENDING, self::REVIEW_STATUS_APPROVED, self::REVIEW_STATUS_HIDDEN];
            if (!in_array($review->status, $allowedStatuses)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể từ chối bình luận ở trạng thái này',
                ], 400);
            }
            
            $review->status = self::REVIEW_STATUS_REJECTED;
            $review->save();

            // Log audit và clear cache
            $this->logReviewAction($review->id, 'reject', $oldStatus, self::REVIEW_STATUS_REJECTED);

            return response()->json([
                'success' => true,
                'message' => 'Bình luận đã bị từ chối',
                'old_status' => $oldStatus, // Để có thể undo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API Delete Comment
     * DELETE /admin/api/dashboard/comments/{id}
     */
    public function deleteComment($id)
    {
        try {
            $review = Review::findOrFail($id);
            $oldStatus = $review->status;
            
            // Log audit trước khi xóa
            $this->logReviewAction($review->id, 'delete', $oldStatus, 'deleted');
            
            // Soft delete nếu có deleted_at, nếu không thì hard delete
            if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($review))) {
                $review->delete(); // Soft delete
            } else {
                // Hard delete nhưng log lại
                $review->delete();
            }

            // Clear all comments cache
            $this->clearCommentsCache();

            return response()->json([
                'success' => true,
                'message' => 'Bình luận đã được xóa',
                'old_status' => $oldStatus, // Để có thể undo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API Undo Comment Action
     * POST /admin/api/dashboard/comments/{id}/undo
     */
    public function undoCommentAction($id, Request $request)
    {
        try {
            $review = Review::findOrFail($id);
            $oldStatus = $request->input('old_status');
            
            if (!$oldStatus) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có trạng thái cũ để hoàn tác',
                ], 400);
            }
            
            $currentStatus = $review->status;
            $review->status = $oldStatus;
            $review->save();

            // Log audit
            $this->logReviewAction($review->id, 'undo', $currentStatus, $oldStatus);

            return response()->json([
                'success' => true,
                'message' => 'Đã hoàn tác thao tác',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API Users - Danh sách người dùng với pagination
     * GET /admin/api/dashboard/users?status=active&search=abc&page=1
     */
    public function usersApi(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('search');
        $perPage = 10;

        $query = User::select('users.*')
            ->selectRaw('COUNT(orders.id) as orders_count')
            ->selectRaw('COALESCE(SUM(orders.final_total), 0) as total_spent')
            ->leftJoin('orders', function ($join) {
                $join->on('orders.user_id', '=', 'users.id')
                    ->where(function ($q) {
                        $q->where('orders.payment_status', 'paid')
                            ->orWhere('orders.order_status', 'completed');
                    });
            })
            ->where('users.role', 'user')
            ->groupBy('users.id');

        // Filter by status (giả sử có field status trong users table, nếu không thì bỏ qua)
        // if ($status) {
        //     $query->where('users.status', $status);
        // }

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        $query->orderBy('users.created_at', 'desc');

        $users = $query->paginate($perPage);

        // Format data
        $data = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'orders_count' => (int)($user->orders_count ?? 0),
                'total_spent' => (int)($user->total_spent ?? 0),
                'status' => $user->status ?? 'active',
                'created_at' => $user->created_at->format('Y-m-d'),
                'created_at_ago' => $user->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'data' => $data,
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    /**
     * API Ban User
     * POST /admin/api/dashboard/users/{id}/ban
     */
    public function banUser($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Không cho phép ban admin
            if ($user->role === 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể khóa tài khoản admin',
                ], 400);
            }
            
            $user->status = 'banned';
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Người dùng đã bị khóa',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API Unban User
     * POST /admin/api/dashboard/users/{id}/unban
     */
    public function unbanUser($id)
    {
        try {
            $user = User::findOrFail($id);
            
            $user->status = 'active';
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Người dùng đã được mở khóa',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API Top Products - Top sản phẩm bán chạy
     * GET /admin/api/dashboard/top-products?range=30&from=2025-12-01&to=2025-12-30&limit=5
     */
    public function topProductsApi(Request $request)
    {
        try {
            $from = $request->query('from');
            $to = $request->query('to');
            $range = $request->query('range', '30'); // 7, 30, 90, custom
            $limit = (int) $request->query('limit', 5);

        // Tính date range - đồng bộ với categoryRevenueApi và topCustomersApi
        $now = Carbon::now();
        switch ($range) {
            case '7':
                $start = $now->copy()->subDays(6)->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case '90':
                $start = $now->copy()->subDays(89)->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case 'custom':
                try {
                    $start = $from ? Carbon::parse($from)->startOfDay() : $now->copy()->subDays(29)->startOfDay();
                    $end = $to ? Carbon::parse($to)->endOfDay() : $now->copy()->endOfDay();
                    if ($start->gt($end)) {
                        $temp = $start;
                        $start = $end;
                        $end = $temp;
                    }
                } catch (\Exception $e) {
                    $start = $now->copy()->subDays(29)->startOfDay();
                    $end = $now->copy()->endOfDay();
                }
                break;
            case '30':
            default:
                $start = $now->copy()->subDays(29)->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
        }

        // Query top products - DỰ TÍNH (tất cả đơn hàng chưa bị hủy và trả lại)
        // Logic: Toàn bộ sản phẩm được đặt chưa bị hủy và trả lại
        // Filter: order_status != 'cancelled' AND != 'refunded' AND != 'returned' AND != 'return_requested'
        $topProducts = DB::table('order_details as od')
            ->join('products as p', 'p.id', '=', 'od.product_id')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->where('o.order_status', '!=', 'cancelled')
            ->where('o.order_status', '!=', 'refunded')
            ->where('o.order_status', '!=', 'returned')
            ->where('o.order_status', '!=', 'return_requested')
            ->whereBetween(DB::raw('COALESCE(o.order_date, o.created_at)'), [$start, $end])
            ->select(
                'p.id',
                'p.name',
                'p.image',
                'p.stock',
                'c.name as category_name',
                DB::raw('SUM(od.quantity) as sold_quantity'),
                DB::raw('SUM(od.subtotal) as revenue')
            )
            ->groupBy('p.id', 'p.name', 'p.image', 'p.stock', 'c.name')
            ->orderBy('sold_quantity', 'desc')
            ->limit($limit)
            ->get();

        // Query số lượng và doanh thu THỰC TẾ (chỉ completed) cho từng sản phẩm
        // Logic: Toàn bộ sản phẩm cùng loại (cùng product_id) trong các đơn hàng đã hoàn thành
        // Group by p.id để tính tổng số lượng và doanh thu của từng sản phẩm trong các đơn hàng completed
        $completedData = DB::table('order_details as od')
            ->join('products as p', 'p.id', '=', 'od.product_id')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->where('o.order_status', 'completed')
            ->whereBetween(DB::raw('COALESCE(o.order_date, o.created_at)'), [$start, $end])
            ->select(
                'p.id',
                DB::raw('SUM(od.quantity) as sold_completed'),  // Tổng số lượng sản phẩm cùng loại đã hoàn thành
                DB::raw('SUM(od.subtotal) as revenue_completed')  // Tổng doanh thu sản phẩm cùng loại đã hoàn thành
            )
            ->groupBy('p.id')  // Group by product_id để tính tổng cho từng sản phẩm
            ->get()
            ->keyBy('id');

        // Tính tổng doanh thu DỰ TÍNH từ order_details.subtotal (tất cả đơn hàng chưa bị hủy và trả lại)
        // Logic: Toàn bộ sản phẩm được đặt chưa bị hủy và trả lại
        // Dùng subtotal để đồng bộ với doanh thu từng sản phẩm (tính theo sản phẩm)
        // Filter: order_status != 'cancelled' AND != 'refunded' AND != 'returned' AND != 'return_requested'
        $totalRevenue = DB::table('order_details as od')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->where('o.order_status', '!=', 'cancelled')
            ->where('o.order_status', '!=', 'refunded')
            ->where('o.order_status', '!=', 'returned')
            ->where('o.order_status', '!=', 'return_requested')
            ->whereBetween(DB::raw('COALESCE(o.order_date, o.created_at)'), [$start, $end])
            ->sum('od.subtotal');

        // Tính tổng doanh thu THỰC TẾ từ orders.final_total (chỉ completed)
        // Logic: Đồng bộ với các bảng/biểu đồ khác (dashboard index, revenueControlChartApi, etc.)
        // Dùng final_total để đồng bộ với các bảng khác
        $totalRevenueCompleted = DB::table('orders')
            ->where('order_status', 'completed')
            ->whereBetween(DB::raw('COALESCE(order_date, created_at)'), [$start, $end])
            ->sum('final_total');

        // Format data
        $items = [];
        foreach ($topProducts as $product) {
            // Dữ liệu dự tính
            $soldEstimated = (int) $product->sold_quantity;
            $revenueEstimated = (int) $product->revenue;
            $percentEstimated = $totalRevenue > 0 ? round(($revenueEstimated / $totalRevenue) * 100, 1) : 0;
            
            // Dữ liệu thực tế (completed)
            $completed = $completedData->get($product->id);
            $soldCompleted = $completed ? (int) $completed->sold_completed : 0;
            $revenueCompleted = $completed ? (int) $completed->revenue_completed : 0;
            $percentCompleted = $totalRevenueCompleted > 0 ? round(($revenueCompleted / $totalRevenueCompleted) * 100, 1) : 0;
            
            // Tỷ lệ chuyển đổi
            $conversionRate = $revenueEstimated > 0 ? round(($revenueCompleted / $revenueEstimated) * 100, 1) : 0;
            
            // Lấy product image - ưu tiên product.image, nếu không có thì lấy từ product_images
            $productImage = $product->image;
            if (!$productImage) {
                $firstImage = DB::table('product_images')
                    ->where('product_id', $product->id)
                    ->orderBy('id', 'asc')
                    ->value('image');
                $productImage = $firstImage;
            }
            
            // Format image URL
            $imageUrl = '/images/placeholder.png';
            if ($productImage) {
                if (str_starts_with($productImage, 'http')) {
                    $imageUrl = $productImage;
                } else {
                    $imagePath = ltrim($productImage, '/');
                    $imageUrl = asset('storage/' . $imagePath);
                }
            }
            
            // Tính stock: nếu có variants thì tính tổng stock từ variants, nếu không thì dùng product.stock
            $stock = (int) ($product->stock ?? 0);
            $variantCount = DB::table('product_variants')
                ->where('product_id', $product->id)
                ->count();
            
            if ($variantCount > 0) {
                // Nếu có variants, tính tổng stock từ variants
                $stock = (int) DB::table('product_variants')
                    ->where('product_id', $product->id)
                    ->sum('stock');
            }
            
            $items[] = [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category_name ?? 'N/A',
                'image' => $imageUrl,
                // Dự tính
                'sold_estimated' => $soldEstimated,
                'revenue_estimated' => $revenueEstimated,
                'percent_estimated' => $percentEstimated,
                // Thực tế
                'sold_completed' => $soldCompleted,
                'revenue_completed' => $revenueCompleted,
                'percent_completed' => $percentCompleted,
                // Tỷ lệ chuyển đổi
                'conversion_rate' => $conversionRate,
                // Stock
                'stock' => $stock,
            ];
        }

            return response()->json([
                'success' => true,
                'data' => [
                    'total_revenue_estimated' => (int) $totalRevenue,
                    'total_revenue_completed' => (int) $totalRevenueCompleted,
                    'items' => $items,
                ],
                'from' => $start->toDateString(),
                'to' => $end->toDateString(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error in topProductsApi: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tải dữ liệu: ' . $e->getMessage(),
                'data' => [
                    'total_revenue_estimated' => 0,
                    'total_revenue_completed' => 0,
                    'items' => [],
                ],
            ], 500);
        }
    }

    /**
     * API Inventory Summary - Thống kê tồn kho
     * GET /admin/api/dashboard/inventory?search=&status=
     */
    public function inventoryApi(Request $request)
    {
        try {
            $search = $request->query('search', '');
            $statusFilter = $request->query('status', '');

            // Query sản phẩm với tồn kho và số lượng đã bán
            $query = DB::table('products as p')
                ->join('categories as c', 'c.id', '=', 'p.category_id')
                ->leftJoin('order_details as od', 'od.product_id', '=', 'p.id')
                ->leftJoin('orders as o', function ($join) {
                    $join->on('o.id', '=', 'od.order_id')
                        ->where('o.order_status', '=', 'completed');
                })
                ->select(
                    'p.id',
                    'p.name',
                    'p.image',
                    'p.sku',
                    'p.stock',
                    'c.name as category',
                    DB::raw('COALESCE(SUM(od.quantity), 0) as sold_quantity')
                )
                ->groupBy('p.id', 'p.name', 'p.image', 'p.sku', 'p.stock', 'c.name');

            // Search filter
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('p.name', 'LIKE', "%{$search}%")
                        ->orWhere('p.sku', 'LIKE', "%{$search}%")
                        ->orWhere('c.name', 'LIKE', "%{$search}%");
                });
            }

            // Status filter
            if ($statusFilter) {
                switch ($statusFilter) {
                    case 'in_stock':
                        $query->having('p.stock', '>', 20);
                        break;
                    case 'low':
                        $query->having('p.stock', '>', 5)->having('p.stock', '<=', 20);
                        break;
                    case 'very_low':
                        $query->having('p.stock', '>', 0)->having('p.stock', '<=', 5);
                        break;
                    case 'out_of_stock':
                        $query->having('p.stock', '=', 0);
                        break;
                }
            }

            // Sắp xếp theo tồn kho tăng dần (sản phẩm ít tồn kho trước)
            $products = $query->orderBy('p.stock', 'asc')
                ->orderBy('sold_quantity', 'desc')
                ->get();

            $items = [];
            foreach ($products as $product) {
                // Tính stock: nếu có variants thì tính tổng stock từ variants, nếu không thì dùng product.stock
                $stock = (int) ($product->stock ?? 0);
                $variantCount = DB::table('product_variants')
                    ->where('product_id', $product->id)
                    ->count();
                
                if ($variantCount > 0) {
                    // Nếu có variants, tính tổng stock từ variants
                    $stock = (int) DB::table('product_variants')
                        ->where('product_id', $product->id)
                        ->sum('stock');
                }
                
                $soldQuantity = (int) $product->sold_quantity;

                // Stock status
                $stockStatus = 'in_stock';
                $stockStatusLabel = '🟢 Còn hàng';
                $stockStatusColor = '#10B981';
                
                if ($stock === 0) {
                    $stockStatus = 'out_of_stock';
                    $stockStatusLabel = '🔥 Hết hàng';
                    $stockStatusColor = '#B91C1C';
                } elseif ($stock <= 5) {
                    $stockStatus = 'very_low';
                    $stockStatusLabel = '🔴 Hết hàng / Cực thấp';
                    $stockStatusColor = '#EF4444';
                } elseif ($stock <= 20) {
                    $stockStatus = 'low';
                    $stockStatusLabel = '🟡 Sắp hết';
                    $stockStatusColor = '#F59E0B';
                }

                // Image URL
                $imageUrl = '/images/placeholder.png';
                if ($product->image) {
                    if (str_starts_with($product->image, 'http')) {
                        $imageUrl = $product->image;
                    } else {
                        $imagePath = ltrim($product->image, '/');
                        $imageUrl = asset('storage/' . $imagePath);
                    }
                }

                $items[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku ?? '',
                    'image' => $imageUrl,
                    'category' => $product->category,
                    'stock' => $stock,
                    'sold_quantity' => $soldQuantity,
                    'stock_status' => $stockStatus,
                    'stock_status_label' => $stockStatusLabel,
                    'stock_status_color' => $stockStatusColor,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'items' => $items,
                    'total' => count($items),
                ],
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error loading inventory:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tải dữ liệu: ' . $e->getMessage(),
                'data' => [
                    'items' => [],
                    'total' => 0,
                ],
            ], 500);
        }
    }

}
