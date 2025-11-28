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
        $q = trim((string) $request->get('q'));

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

        // Tổng số đơn (tất cả trạng thái) trong khoảng (nếu có filter)
        $filteredOrdersCount = null;
        if ($startDate || $endDate) {
            $countQueryAll = Order::query();
            if ($startDate) $countQueryAll->whereDate('created_at', '>=', $startDate);
            if ($endDate) $countQueryAll->whereDate('created_at', '<=', $endDate);
            $filteredOrdersCount = $countQueryAll->count();
        }

        // Biểu đồ doanh thu: nếu có filter ngày thì hiển thị theo tháng trong khoảng đó,
        // ngược lại hiển thị theo 12 tháng của năm hiện tại
        $revenueLabels = [];
        $revenueData = [];

        if ($startDate || $endDate) {
            // chuẩn hoá range
            $start = \Carbon\Carbon::parse($startDate ?? ($year . '-01-01'))->startOfMonth();
            $end = \Carbon\Carbon::parse($endDate ?? ($year . '-12-31'))->endOfMonth();

            $monthlyRevenue = Order::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(final_total) as revenue')
            )
                ->where('order_status', 'completed')
                ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();

            $monthlyMap = $monthlyRevenue->keyBy(function ($r) {
                return $r->year . '-' . str_pad($r->month, 2, '0', STR_PAD_LEFT);
            });

            // iterate months between start and end
            $cursor = $start->copy();
            while ($cursor->lte($end)) {
                $key = $cursor->format('Y-m');
                $revenueLabels[] = $cursor->format('m/Y');
                $revenueData[] = $monthlyMap[$key]->revenue ?? 0;
                $cursor->addMonth();
            }
        } else {
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
            for ($i = 1; $i <= 12; $i++) {
                $revenueLabels[] = 'T' . $i;
                $revenueData[] = $monthlyRevenueMap[$i]->revenue ?? 0;
            }
        }

        // Lấy 5 đơn hàng gần nhất cùng user, sản phẩm và category
        $recentOrders = Order::with('user', 'items.product.category')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qBuilder) use ($q) {
                    $qBuilder->where('order_code', 'like', "%{$q}%")
                        ->orWhereHas('user', function ($u) use ($q) {
                            $u->where('name', 'like', "%{$q}%");
                        })
                        ->orWhereHas('items.product', function ($p) use ($q) {
                            $p->where('name', 'like', "%{$q}%");
                        });
                });
            })
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
            'showTargetAlert',
            'q',
            'filteredOrdersCount'
        ));
    }
}
