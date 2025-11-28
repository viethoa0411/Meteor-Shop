<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // Lấy filter từ request
        $dateRange = $request->input('date_range', 'all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status', 'all');
        $staffId = $request->input('staff_id', 'all');

        // Xử lý date range
        $dateFilter = $this->getDateFilter($dateRange, $startDate, $endDate);

        // Lấy dữ liệu cho các khối (áp dụng filter status nếu có)
        $kpiData = $this->getKpiData($dateFilter, $status);
        $timelineData = $this->getTimelineData($dateFilter, $status);
        $statusData = $this->getStatusData($dateFilter, $status);
        $funnelData = $this->getFunnelData($dateFilter, $status);
        $paymentData = $this->getPaymentMethodData($dateFilter, $status);
        $heatmapData = $this->getHeatmapData($dateFilter, $status);

        return view('admin.orders.analytics', compact(
            'kpiData',
            'timelineData',
            'statusData',
            'funnelData',
            'paymentData',
            'heatmapData',
            'dateRange',
            'startDate',
            'endDate',
            'status',
            'staffId'
        ));
    }

    private function getDateFilter($dateRange, $startDate, $endDate)
    {
        $now = Carbon::now();

        switch ($dateRange) {
            case 'all':
                // Tất cả đơn hàng - không giới hạn thời gian (null để không filter)
                return [
                    'start' => null,
                    'end' => null
                ];
            case 'today':
                return [
                    'start' => $now->copy()->startOfDay(),
                    'end' => $now->copy()->endOfDay()
                ];
            case 'yesterday':
                return [
                    'start' => $now->copy()->subDay()->startOfDay(),
                    'end' => $now->copy()->subDay()->endOfDay()
                ];
            case 'last_7_days':
                return [
                    'start' => $now->copy()->subDays(6)->startOfDay(), // 7 ngày bao gồm hôm nay
                    'end' => $now->copy()->endOfDay()
                ];
            case 'last_15_days':
                return [
                    'start' => $now->copy()->subDays(14)->startOfDay(), // 15 ngày bao gồm hôm nay
                    'end' => $now->copy()->endOfDay()
                ];
            case 'last_30_days':
                return [
                    'start' => $now->copy()->subDays(29)->startOfDay(), // 30 ngày bao gồm hôm nay
                    'end' => $now->copy()->endOfDay()
                ];
            case 'custom':
                return [
                    'start' => $startDate ? Carbon::parse($startDate)->startOfDay() : $now->copy()->subDays(30)->startOfDay(),
                    'end' => $endDate ? Carbon::parse($endDate)->endOfDay() : $now->copy()->endOfDay()
                ];
            default:
                // Mặc định: 30 ngày gần nhất
                return [
                    'start' => $now->copy()->subDays(29)->startOfDay(),
                    'end' => $now->copy()->endOfDay()
                ];
        }
    }

    private function getKpiData($dateFilter, $status = 'all')
    {
        // Tính toán period trước để so sánh
        $previousStart = null;
        $previousEnd = null;
        
        // Chỉ tính period trước nếu có date filter
        if ($dateFilter['start'] && $dateFilter['end']) {
            $periodDays = $dateFilter['start']->diffInDays($dateFilter['end']);
            
            // Nếu period quá lớn (>365 ngày), không so sánh với period trước
            if ($periodDays <= 365) {
                $previousStart = $dateFilter['start']->copy()->subDays($periodDays + 1);
                $previousEnd = $dateFilter['start']->copy()->subSecond();
            }
        }

        // Helper function để tạo base query
        $createBaseQuery = function($start, $end) use ($status) {
            $query = Order::query();
            // Chỉ filter theo thời gian nếu có start và end
            if ($start && $end) {
                $query = $query->whereBetween('created_at', [$start, $end]);
            }
            // Filter theo status
            if ($status && $status !== 'all') {
                // Xử lý filter status giống như Order model scopeStatus
                if ($status === 'returned') {
                    $query = $query->whereIn('order_status', ['return_requested', 'returned']);
                } else {
                    $query = $query->where('order_status', $status);
                }
            }
            return $query;
        };

        // Current period counts
        $currentBase = $createBaseQuery($dateFilter['start'], $dateFilter['end']);
        $totalOrders = (clone $currentBase)->count();
        $completedOrdersCount = (clone $currentBase)->where('order_status', 'completed')->count();
        $canceledOrders = (clone $currentBase)->where('order_status', 'cancelled')->count();
        // Đếm cả return_requested và returned
        $refundedOrders = (clone $currentBase)->whereIn('order_status', ['return_requested', 'returned'])->count();

        // Previous period counts (chỉ tính nếu có period trước)
        if ($previousStart && $previousEnd) {
            $previousBase = $createBaseQuery($previousStart, $previousEnd);
            $previousTotalOrders = (clone $previousBase)->count();
            $previousCompleted = (clone $previousBase)->where('order_status', 'completed')->count();
            $previousCanceled = (clone $previousBase)->where('order_status', 'cancelled')->count();
            // Đếm cả return_requested và returned
            $previousRefunded = (clone $previousBase)->whereIn('order_status', ['return_requested', 'returned'])->count();
        } else {
            // Không có period trước để so sánh
            $previousTotalOrders = 0;
            $previousCompleted = 0;
            $previousCanceled = 0;
            $previousRefunded = 0;
        }

        // Growth calculations
        $totalOrdersGrowth = $previousTotalOrders > 0 
            ? round((($totalOrders - $previousTotalOrders) / $previousTotalOrders) * 100, 1)
            : ($totalOrders > 0 ? 100 : 0);

        $completedGrowth = $previousCompleted > 0 
            ? round((($completedOrdersCount - $previousCompleted) / $previousCompleted) * 100, 1)
            : ($completedOrdersCount > 0 ? 100 : 0);

        $canceledGrowth = $previousCanceled > 0 
            ? round((($canceledOrders - $previousCanceled) / $previousCanceled) * 100, 1)
            : ($canceledOrders > 0 ? 100 : 0);

        $refundedGrowth = $previousRefunded > 0 
            ? round((($refundedOrders - $previousRefunded) / $previousRefunded) * 100, 1)
            : ($refundedOrders > 0 ? 100 : 0);

        // Conversion rate
        $conversionRate = $totalOrders > 0 ? round(($completedOrdersCount / $totalOrders) * 100, 2) : 0;

        // Average processing time (từ created đến completed)
        $completedOrdersQuery = Order::where('order_status', 'completed')
            ->whereNotNull('delivered_at');
        
        // Chỉ filter theo thời gian nếu có
        if ($dateFilter['start'] && $dateFilter['end']) {
            $completedOrdersQuery = $completedOrdersQuery->whereBetween('created_at', [$dateFilter['start'], $dateFilter['end']]);
        }
        
        $completedOrders = $completedOrdersQuery->get();

        $totalHours = 0;
        $count = 0;
        foreach ($completedOrders as $order) {
            if ($order->delivered_at) {
                $hours = $order->created_at->diffInHours($order->delivered_at);
                $totalHours += $hours;
                $count++;
            }
        }
        $avgProcessingTime = $count > 0 ? ($totalHours / $count) : 0;

        return [
            'total_orders' => (int)$totalOrders,
            'total_orders_growth' => $totalOrdersGrowth,
            'completed_orders' => (int)$completedOrdersCount,
            'completed_growth' => $completedGrowth,
            'canceled_orders' => (int)$canceledOrders,
            'canceled_growth' => $canceledGrowth,
            'refunded_orders' => (int)$refundedOrders,
            'refunded_growth' => $refundedGrowth,
            'conversion_rate' => $conversionRate,
            'avg_processing_time' => round($avgProcessingTime, 1),
        ];
    }

    private function getTimelineData($dateFilter, $status = 'all')
    {
        $query = Order::query();
        
        // Chỉ filter theo thời gian nếu có start và end
        if ($dateFilter['start'] && $dateFilter['end']) {
            $query = $query->whereBetween('created_at', [$dateFilter['start'], $dateFilter['end']]);
        }

        if ($status !== 'all') {
            // Xử lý filter status giống như Order model scopeStatus
            if ($status === 'returned') {
                $query = $query->whereIn('order_status', ['return_requested', 'returned']);
            } else {
                $query = $query->where('order_status', $status);
            }
        }

        $data = $query->selectRaw('DATE(created_at) as date, COUNT(*) as count, order_status')
            ->groupBy('date', 'order_status')
            ->orderBy('date')
            ->get();

        $timeline = [];
        // Sử dụng đúng các status từ Order model
        $statuses = ['pending', 'processing', 'shipping', 'completed', 'cancelled', 'return_requested', 'returned'];

        foreach ($statuses as $statusKey) {
            $timeline[$statusKey] = [];
        }

        // Xử lý timeline labels và data
        if ($dateFilter['start'] && $dateFilter['end']) {
            // Có date range - tạo timeline theo ngày
            $currentDate = $dateFilter['start']->copy();
            while ($currentDate <= $dateFilter['end']) {
                $dateStr = $currentDate->format('Y-m-d');
                foreach ($statuses as $statusKey) {
                    $count = $data->where('date', $dateStr)->where('order_status', $statusKey)->sum('count');
                    $timeline[$statusKey][] = (int)$count;
                }
                $currentDate->addDay();
            }

            $labels = [];
            $currentDate = $dateFilter['start']->copy();
            while ($currentDate <= $dateFilter['end']) {
                $labels[] = $currentDate->format('d/m');
                $currentDate->addDay();
            }
        } else {
            // Không có date range (Tất cả đơn hàng) - nhóm theo tháng
            if ($data->count() > 0) {
                $dataByMonth = $data->groupBy(function($item) {
                    return Carbon::parse($item->date)->format('Y-m');
                });
                
                $months = $dataByMonth->keys()->sort()->values();
                foreach ($months as $month) {
                    foreach ($statuses as $statusKey) {
                        $count = $data->filter(function($item) use ($month, $statusKey) {
                            return Carbon::parse($item->date)->format('Y-m') == $month && $item->order_status == $statusKey;
                        })->sum('count');
                        $timeline[$statusKey][] = (int)$count;
                    }
                }
                
                $labels = $months->map(function($month) {
                    return Carbon::parse($month . '-01')->format('m/Y');
                })->toArray();
            } else {
                // Không có dữ liệu
                $labels = [];
            }
        }

        // Gộp return_requested và returned thành 'returned' cho timeline chart
        $timeline['returned'] = [];
        $lengths = array_map('count', $timeline);
        $maxLength = !empty($lengths) ? max($lengths) : 0;
        for ($i = 0; $i < $maxLength; $i++) {
            $timeline['returned'][$i] = ($timeline['return_requested'][$i] ?? 0) + ($timeline['returned'][$i] ?? 0);
        }
        unset($timeline['return_requested']);

        return [
            'labels' => $labels,
            'data' => $timeline,
        ];
    }

    private function getStatusData($dateFilter, $status = 'all')
    {
        $query = Order::query();
        
        // Chỉ filter theo thời gian nếu có start và end
        if ($dateFilter['start'] && $dateFilter['end']) {
            $query = $query->whereBetween('created_at', [$dateFilter['start'], $dateFilter['end']]);
        }
        
        if ($status && $status !== 'all') {
            if ($status === 'returned') {
                $query = $query->whereIn('order_status', ['return_requested', 'returned']);
            } else {
                $query = $query->where('order_status', $status);
            }
        }
        $data = $query->selectRaw('order_status, COUNT(*) as count')
            ->groupBy('order_status')
            ->get()
            ->pluck('count', 'order_status');

        // Sử dụng đúng STATUS_META từ Order model
        $statuses = [
            'pending' => 'Chờ xác nhận',
            'processing' => 'Chuẩn bị hàng',
            'shipping' => 'Đang giao',
            'completed' => 'Đã giao',
            'cancelled' => 'Đã hủy',
            'return_requested' => 'Yêu cầu đổi trả',
            'returned' => 'Đã đổi trả',
        ];

        $result = [];
        $total = $data->sum();

        foreach ($statuses as $key => $label) {
            $count = (int)($data[$key] ?? 0);
            $result[] = [
                'status' => $key,
                'label' => $label,
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100, 1) : 0,
            ];
        }

        return $result;
    }

    private function getFunnelData($dateFilter, $status = 'all')
    {
        // Tạo base query với filter thời gian và status
        $createBaseQuery = function() use ($dateFilter, $status) {
            $query = Order::query();
            
            // Chỉ filter theo thời gian nếu có start và end
            if ($dateFilter['start'] && $dateFilter['end']) {
                $query = $query->whereBetween('created_at', [$dateFilter['start'], $dateFilter['end']]);
            }
            
            if ($status && $status !== 'all') {
                // Xử lý filter status giống như Order model scopeStatus
                if ($status === 'returned') {
                    $query = $query->whereIn('order_status', ['return_requested', 'returned']);
                } else {
                    $query = $query->where('order_status', $status);
                }
            }
            return $query;
        };

        $base = $createBaseQuery();
        
        // Funnel 5 bước chuẩn dựa trên timestamps và status
        // 1. Created: Tất cả đơn được tạo trong khoảng thời gian
        $createdCount = (clone $base)->count();
        
        // 2. Confirmed: Đơn có confirmed_at không null
        $confirmedCount = (clone $base)->whereNotNull('confirmed_at')->count();
        
        // 3. Packed: Đơn có packed_at không null
        $packedCount = (clone $base)->whereNotNull('packed_at')->count();
        
        // 4. Shipping: Đơn có shipped_at không null hoặc order_status = 'shipping'
        $shippingCount = (clone $base)->where(function($q) {
            $q->whereNotNull('shipped_at')
              ->orWhere('order_status', 'shipping');
        })->count();
        
        // 5. Delivered: Đơn có delivered_at không null hoặc order_status = 'completed'
        $deliveredCount = (clone $base)->where(function($q) {
            $q->whereNotNull('delivered_at')
              ->orWhere('order_status', 'completed');
        })->count();
        
        // Nhánh rớt
        $cancelledCount = (clone $base)->where('order_status', 'cancelled')->count();
        $returnRequestedCount = (clone $base)->where('order_status', 'return_requested')->count();
        $returnedCount = (clone $base)->where('order_status', 'returned')->count();
        $refundedCount = $returnRequestedCount + $returnedCount;

        // Tính tỷ lệ chuyển đổi tại mỗi bước
        $confirmedRate = $createdCount > 0 ? round(($confirmedCount / $createdCount) * 100, 1) : 0;
        $packedRate = $confirmedCount > 0 ? round(($packedCount / $confirmedCount) * 100, 1) : 0;
        $shippingRate = $packedCount > 0 ? round(($shippingCount / $packedCount) * 100, 1) : 0;
        $deliveredRate = $shippingCount > 0 ? round(($deliveredCount / $shippingCount) * 100, 1) : 0;
        $finalConversion = $createdCount > 0 ? round(($deliveredCount / $createdCount) * 100, 1) : 0;

        // Tính số lượng rớt tại từng bước
        $dropConfirm = $createdCount - $confirmedCount;
        $dropPacked = $confirmedCount - $packedCount;
        $dropShipping = $packedCount - $shippingCount;
        $dropDelivered = $shippingCount - $deliveredCount;

        return [
            'steps' => [
                'created' => $createdCount,
                'confirmed' => $confirmedCount,
                'packed' => $packedCount,
                'shipping' => $shippingCount,
                'delivered' => $deliveredCount,
            ],
            'conversion' => [
                'confirmed_rate' => $confirmedRate,
                'packed_rate' => $packedRate,
                'shipping_rate' => $shippingRate,
                'delivered_rate' => $deliveredRate,
                'final_conversion' => $finalConversion,
            ],
            'drops' => [
                'drop_confirm' => $dropConfirm,
                'drop_packed' => $dropPacked,
                'drop_shipping' => $dropShipping,
                'drop_delivered' => $dropDelivered,
            ],
            'cancelled' => $cancelledCount,
            'return_requested' => $returnRequestedCount,
            'returned' => $returnedCount,
            'refunded' => $refundedCount,
        ];
    }

    private function getPaymentMethodData($dateFilter, $status = 'all')
    {
        $query = Order::query();
        
        // Chỉ filter theo thời gian nếu có start và end
        if ($dateFilter['start'] && $dateFilter['end']) {
            $query = $query->whereBetween('created_at', [$dateFilter['start'], $dateFilter['end']]);
        }
        
        if ($status && $status !== 'all') {
            // Xử lý filter status giống như Order model scopeStatus
            if ($status === 'returned') {
                $query = $query->whereIn('order_status', ['return_requested', 'returned']);
            } else {
                $query = $query->where('order_status', $status);
            }
        }
        $data = $query->selectRaw('payment_method, COUNT(*) as count, SUM(CASE WHEN order_status = "cancelled" THEN 1 ELSE 0 END) as cancelled_count')
            ->groupBy('payment_method')
            ->get();

        $total = $data->sum('count');
        $result = [];

        foreach ($data as $item) {
            $result[] = [
                'method' => $item->payment_method,
                'label' => $this->getPaymentLabel($item->payment_method),
                'count' => $item->count,
                'percentage' => $total > 0 ? round(($item->count / $total) * 100, 1) : 0,
                'cancelled_count' => $item->cancelled_count,
                'cancelled_rate' => $item->count > 0 ? round(($item->cancelled_count / $item->count) * 100, 1) : 0,
            ];
        }

        return $result;
    }

    private function getHeatmapData($dateFilter, $status = 'all')
    {
        $query = Order::query();
        
        // Chỉ filter theo thời gian nếu có start và end
        if ($dateFilter['start'] && $dateFilter['end']) {
            $query = $query->whereBetween('created_at', [$dateFilter['start'], $dateFilter['end']]);
        }
        
        if ($status && $status !== 'all') {
            // Xử lý filter status giống như Order model scopeStatus
            if ($status === 'returned') {
                $query = $query->whereIn('order_status', ['return_requested', 'returned']);
            } else {
                $query = $query->where('order_status', $status);
            }
        }
        $data = $query->selectRaw('HOUR(created_at) as hour, DAYOFWEEK(created_at) as day_of_week, COUNT(*) as count')
            ->groupBy('hour', 'day_of_week')
            ->get();

        $heatmap = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $heatmap[$hour] = [];
            for ($day = 1; $day <= 7; $day++) {
                $heatmap[$hour][$day] = 0;
            }
        }

        foreach ($data as $item) {
            $heatmap[$item->hour][$item->day_of_week] = $item->count;
        }

        return $heatmap;
    }

    private function getPaymentLabel($method)
    {
        // Sử dụng đúng PAYMENT_LABELS từ Order model
        return Order::PAYMENT_LABELS[$method] ?? ucfirst($method ?? 'N/A');
    }
}

