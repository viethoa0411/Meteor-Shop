@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')
<<<<<<< HEAD
@section('content')

    {{-- ========== 1. HEADER DASHBOARD ========== --}}
    <section class="mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-speedometer2 text-primary"></i>
                    <span>Dashboard quản trị</span>
                </h4>
                <p class="text-muted small mb-0">
                    Tổng quan real-time về doanh thu, đơn hàng, sản phẩm và người dùng trong hệ thống Meteor-Shop.
                </p>
            </div>

            {{-- (Đã ẩn cụm search / language / notification theo yêu cầu) --}}
        </div>
    </section>

    {{-- ========== 2. KPI SUMMARY SECTION (TOP KPIs) ========== --}}
    <section class="mb-4">
        <div class="row row-cols-2 row-cols-md-3 row-cols-xl-5 g-3">
            {{-- Doanh thu tổng & tháng hiện tại --}}
            <div class="col">
                <a href="{{ route('admin.revenue.filter') ?? '#' }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm"
                         style="background: linear-gradient(135deg,#667eea,#764ba2); border-radius: 18px;">
                        <div class="card-body text-white d-flex flex-column justify-content-between">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-white-50 small mb-1">Tổng doanh thu (đã hoàn thành)</p>
                                    <h3 class="fw-bold mb-0 stat-number">
                                        {{ number_format($totalCompletedRevenue) }} ₫
                                    </h3>
                                </div>
                                <span class="badge bg-white text-primary">
                                    <i class="bi bi-cash-coin"></i>
                                </span>
                            </div>
                            <p class="small mb-0 mt-2">
                                Tháng {{ now()->month }}/{{ now()->year }}: 
                                <strong>{{ number_format($currentMonthRevenue) }} ₫</strong> · 
                                Mục tiêu: {{ number_format($monthlyTarget) }} ₫
                            </p>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Tổng đơn hàng --}}
            <div class="col">
                <a href="{{ route('admin.orders.list') }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm border-0 kpi-card"
                         style="border-left: 4px solid #22c55e;">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small mb-1">Tổng đơn hàng (tháng này)</p>
                                <h3 class="fw-bold mb-0 stat-number">{{ number_format($totalOrders) }}</h3>
                                <small class="text-muted">Tất cả: {{ number_format($totalAllOrders) }} đơn</small>
                            </div>
                            <div class="kpi-icon bg-success bg-opacity-10 text-success">
                                <i class="bi bi-cart-check-fill"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Người dùng hệ thống --}}
            <div class="col">
                <a href="{{ route('admin.account.users.list') }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm border-0 kpi-card"
                         style="border-left: 4px solid #f59e0b;">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small mb-1">Người dùng hệ thống</p>
                                <h3 class="fw-bold mb-0 stat-number">{{ number_format($totalUsers) }}</h3>
                                <div class="d-flex flex-wrap align-items-center gap-1 small mt-1">
                                    <span class="text-muted">
                                        Tháng {{ now()->month }}/{{ now()->year }}: +{{ number_format($thisMonthUsers) }} user
                                    </span>
                                    @if($userGrowth !== null)
                                        <span class="badge {{ $userGrowth > 0 ? 'bg-success' : ($userGrowth < 0 ? 'bg-danger' : 'bg-secondary') }}">
                                            {{ $userGrowth > 0 ? '+' : '' }}{{ $userGrowth }}% so với tháng trước
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="kpi-icon bg-warning bg-opacity-10 text-warning">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Sản phẩm --}}
            <div class="col">
                <a href="{{ route('admin.products.list') }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm border-0 kpi-card"
                         style="border-left: 4px solid #0ea5e9;">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small mb-1">Sản phẩm</p>
                                <h3 class="fw-bold mb-0 stat-number">{{ number_format($totalProducts) }}</h3>
                                <div class="d-flex flex-wrap align-items-center gap-1 small mt-1">
                                    <span class="text-muted">
                                        Bán trong 30 ngày: <strong>{{ number_format($soldProductsLast30Days) }}</strong> sản phẩm
                                    </span>
                                </div>
                            </div>
                            <div class="kpi-icon bg-info bg-opacity-10 text-info">
                                <i class="bi bi-box-seam"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Tỷ lệ hoàn thành đơn --}}
            <div class="col">
                @php
                    $completed = $orderStatsByStatus['completed'] ?? 0;
                    // $orderStatsByStatus là Collection, cần sum() thay vì array_sum
                    $totalForRate = $orderStatsByStatus instanceof \Illuminate\Support\Collection
                        ? $orderStatsByStatus->sum()
                        : (is_array($orderStatsByStatus) ? array_sum($orderStatsByStatus) : 0);
                    $completeRate = $totalForRate > 0 ? round(($completed / $totalForRate) * 100, 1) : 0;
                @endphp
                <a href="{{ route('admin.orders.analytics') }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm border-0 kpi-card"
                         style="border-left: 4px solid #8b5cf6;">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small mb-1">Tỷ lệ hoàn thành đơn</p>
                                <h3 class="fw-bold mb-0">{{ $completeRate }}%</h3>
                                <small class="text-muted">Hoàn thành: {{ $completed }} đơn</small>
                            </div>
                            <div class="kpi-icon bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    {{-- ========== 3. CHARTS & ANALYTICS AREA ========== --}}
    <section class="mb-4">
        <div class="row g-3">
            {{-- Control chart doanh thu theo thời gian (ApexCharts) - Bên trái --}}
            <div class="col-lg-8">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <h6 class="fw-semibold mb-0">Doanh thu theo thời gian (Control Chart)</h6>
                            <small class="text-muted">
                                Actual · Mean · UCL · LCL – phát hiện ngày doanh thu bất thường.
                            </small>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <div class="btn-group btn-group-sm" role="group" aria-label="Revenue range">
                                <button type="button" class="btn btn-outline-primary active" data-range="7">7 ngày</button>
                                <button type="button" class="btn btn-outline-primary" data-range="30">30 ngày</button>
                                <button type="button" class="btn btn-outline-primary" data-range="90">90 ngày</button>
                                <button type="button" class="btn btn-outline-primary" data-range="month">Tháng này</button>
                                <button type="button" class="btn btn-outline-secondary" data-range="custom">Tùy chọn</button>
                            </div>
                            <div class="d-flex align-items-center gap-1 control-chart-custom-range" style="display:none;">
                                <input type="date" id="revenueControlFrom" class="form-control form-control-sm" placeholder="dd/mm/yyyy">
                                <span class="small text-muted">→</span>
                                <input type="date" id="revenueControlTo" class="form-control form-control-sm" placeholder="dd/mm/yyyy">
                                <button type="button" class="btn btn-sm btn-primary" id="revenueControlApply">
                                    Áp dụng
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="revenueControlChart" style="min-height: 380px;"></div>
                    </div>
                </div>
            </div>

            {{-- Cột phải: Trạng thái đơn hàng + Top 5 sản phẩm bán chạy --}}
            <div class="col-lg-4">
                {{-- Biểu đồ trạng thái đơn hàng (donut) --}}
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="fw-semibold mb-0">Tỷ lệ trạng thái đơn hàng</h6>
                            <small class="text-muted">pending / processing / shipping / completed / cancelled</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="height: 200px;">
                            <canvas id="orderStatusChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Top 5 sản phẩm bán chạy --}}
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="fw-semibold mb-0">Top 5 Sản Phẩm bán chạy</h6>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @php
                                $topSellingProducts = array_slice($topProductsLabels, 0, 5);
                                $topSellingRevenue = array_slice($topProductsRevenue, 0, 5);
                            @endphp
                            @forelse($topSellingProducts as $index => $productName)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary rounded-pill">{{ $index + 1 }}</span>
                                        <span class="text-truncate" style="max-width: 150px;" title="{{ $productName }}">{{ $productName }}</span>
                                    </div>
                                    <span class="text-muted small">{{ number_format($topSellingRevenue[$index] ?? 0) }} ₫</span>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted">Chưa có dữ liệu</li>
                            @endforelse
                        </ul>
=======
@if ($showTargetAlert)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Chú ý!</strong> Bạn chưa đặt mục tiêu cho tháng {{ now()->month }} năm {{ now()->year }}.
        <a href="{{ route('admin.monthly_target.create') }}" class="btn btn-sm btn-primary ms-2">Đặt ngay</a>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@section('content')
    <div class="row g-4 mb-4 d-flex align-items-stretch">
        {{-- Cột trái 60% --}}
        <div class="col-md-7 d-flex flex-column">
            <div class="row g-4 flex-grow-1">
                {{-- Người dùng --}}
                <div class="col-6">
                    <a href="{{ route('admin.account.users.list') }}" class="text-decoration-none">
                        <div class="stat-card card p-4 shadow-sm border-start border-warning border-4 h-100 position-relative overflow-hidden">
                            <div class="stat-card-bg"></div>
                            <div class="d-flex justify-content-between align-items-center h-100 position-relative">
                                <div class="stat-info">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="stat-icon-wrapper bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                                            <i class="bi bi-people-fill text-warning fs-4"></i>
                                        </div>
                                        <div>
                                            <h6 class="text-muted mb-0 small fw-normal">Tổng người dùng</h6>
                                            <p class="text-muted mb-0 small">Tháng này: +{{ number_format($thisMonthUsers) }}</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mt-2">
                                        <h2 class="fw-bold mb-0 stat-number">{{ number_format($totalUsers) }}</h2>
                                        @if($userGrowth != 0)
                                            <span class="badge ms-2 stat-badge {{ $userGrowth > 0 ? 'bg-success' : 'bg-danger' }}">
                                                <i class="bi bi-arrow-{{ $userGrowth > 0 ? 'up' : 'down' }}-short"></i>
                                                {{ abs($userGrowth) }}%
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="stat-action">
                                    <i class="bi bi-arrow-right-circle text-muted fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Tổng số đơn hàng --}}
                <div class="col-6">
                    <a href="{{ route('admin.orders.list') }}" class="text-decoration-none">
                        <div class="stat-card card p-4 shadow-sm border-start border-success border-4 h-100 position-relative overflow-hidden">
                            <div class="stat-card-bg"></div>
                            <div class="d-flex justify-content-between align-items-center h-100 position-relative">
                                <div class="stat-info">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="stat-icon-wrapper bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                            <i class="bi bi-cart-check-fill text-success fs-4"></i>
                                        </div>
                                        <div>
                                            <h6 class="text-muted mb-0 small fw-normal">Tổng đơn hàng</h6>
                                            <p class="text-muted mb-0 small">Tháng này: {{ number_format($totalOrders) }}</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mt-2">
                                        <h2 class="fw-bold mb-0 stat-number">{{ number_format($totalAllOrders) }}</h2>
                                        @if($orderGrowth != 0)
                                            <span class="badge ms-2 stat-badge {{ $orderGrowth > 0 ? 'bg-success' : 'bg-danger' }}">
                                                <i class="bi bi-arrow-{{ $orderGrowth > 0 ? 'up' : 'down' }}-short"></i>
                                                {{ abs($orderGrowth) }}%
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="stat-action">
                                    <i class="bi bi-arrow-right-circle text-muted fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Biểu đồ doanh thu --}}
                <div class="col-12 mt-3">
                    <div class="card shadow-sm p-4 h-100">
                        <h5 class="mb-3 fw-semibold">Doanh thu theo tháng ({{ now()->year }})</h5>
                        <canvas id="revenueChart" style="height: 330px;"></canvas>
>>>>>>> origin/Trang_Chu_Client
                    </div>
                </div>
            </div>
        </div>
<<<<<<< HEAD
    </section>
=======
        {{-- Cột phải 40% --}}
        <div class="col-md-5 d-flex flex-column">
            {{-- Mục tiêu tháng --}}
            <div class="card shadow-sm p-4 mb-4" style="border-radius:18px;">
                <h5 class="fw-semibold mb-1">Mục tiêu tháng</h5>
                <p class="text-muted" style="font-size:14px;">Mục tiêu bạn đặt ra mỗi tháng</p>

                <div class="half-gauge-wrapper">
                    <svg class="half-gauge" width="250" height="140">
                        <path class="half-gauge-bg" d="M20 120 A100 100 0 0 1 230 120" />
                        <path class="half-gauge-value" d="M20 120 A100 100 0 0 1 230 120" />
                    </svg>

                    <div class="half-gauge-text">
                        <h2 class="fw-bold mb-1" id="halfPercent">0%</h2>
                    </div>
                </div>

                <p class="mt-3 text-muted text-center">
                    Hôm nay bạn đã kiếm được {{ number_format($todayRevenue) }} ₫<br>
                    Tiếp tục làm tốt nhé!
                </p>

                <div class="d-flex justify-content-between text-center pt-3 border-top">
                    <div class="flex-fill">
                        <p class="text-muted mb-1">Mục tiêu</p>
                        <h6 class="fw-bold">{{ number_format($monthlyTarget) }} ₫</h6>
                    </div>
                    <div class="flex-fill">
                        <p class="text-muted mb-1">Doanh thu</p>
                        <h6 class="fw-bold">{{ number_format($currentMonthRevenue) }} ₫</h6>
                    </div>
                    <div class="flex-fill">
                        <p class="text-muted mb-1">Hôm nay</p>
                        <h6 class="fw-bold">{{ number_format($todayRevenue) }} ₫</h6>
                    </div>
                </div>
            </div>



            {{-- Bộ lọc doanh thu --}}
            <div class="card shadow-sm p-4 flex-grow-1">
                <h6 class="mb-3 fw-semibold">Lọc doanh thu theo khoảng thời gian</h6>
                <form method="GET" action="{{ route('admin.dashboard') }}">
                    <div class="mb-2">
                        <label for="filterStartDate" class="form-label small">Từ ngày</label>
                        <input type="date" name="start_date" id="filterStartDate" class="form-control form-control-sm"
                            value="{{ old('start_date', $startDate) }}">
                    </div>
                    <div class="mb-3">
                        <label for="filterEndDate" class="form-label small">Đến ngày</label>
                        <input type="date" name="end_date" id="filterEndDate" class="form-control form-control-sm"
                            value="{{ old('end_date', $endDate) }}">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">Lọc</button>
                </form>

                @if (!is_null($filteredRevenue))
                    <div class="mt-3 p-2 border rounded bg-light">
                        <strong>Doanh thu: </strong> {{ number_format($filteredRevenue) }} ₫
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="row mt-4 g-4">
        {{-- Bảng Đơn hàng gần nhất --}}
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="fw-semibold mb-0">Đơn hàng gần nhất</h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mã đơn</th>
                                <th>Người đặt</th>
                                <th>Ngày đặt</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td>{{ $order->order_code }}</td>
                                    <td>{{ $order->user->name ?? 'Người dùng không xác định' }}</td>
                                    <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                    <td>{{ number_format($order->final_total) }} ₫</td>
                                    <td>
                                        @php
                                            $status = [
                                                'pending' => 'warning',
                                                'processing' => 'info',
                                                'completed' => 'success',
                                                'cancelled' => 'danger',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $status[$order->order_status] ?? 'secondary' }}">
                                            {{ ucfirst($order->order_status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Không có đơn hàng nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Bảng Sản phẩm gần nhất --}}
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="fw-semibold mb-0">Sản phẩm bán gần đây nhất</h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tên sản phẩm</th>
                                <th>Danh mục</th>
                                <th>Giá</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentProducts as $item)
                                <tr>
                                    <td>
                                        @if (!empty($item['image']))
                                            <img src="{{ asset('storage/' . $item['image']) }}"
                                                alt="{{ $item['product_name'] }}" style="width:50px; height:auto;">
                                        @else
                                            -
                                        @endif
                                        {{ $item['product_name'] }}
                                    </td>
                                    <td>{{ $item['category_name'] }}</td>
                                    <td>{{ number_format($item['price'], 0, ',', '.') }} ₫</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Không có sản phẩm nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Component Thống kê Đơn hàng --}}
    <div class="row mt-4 g-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h5 class="fw-semibold mb-1">Thống kê Đơn hàng</h5>
                            <p class="text-muted small mb-0">Phân tích và theo dõi đơn hàng chi tiết</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary fs-6 px-3 py-2">
                                Tổng: {{ number_format($totalFilteredOrders) }} đơn
                            </span>
                            <a href="{{ route('admin.orders.analytics') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-graph-up-arrow me-1"></i>Thống kê chi tiết
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Bộ lọc --}}
                    <div class="order-filter-section mb-4 p-3 p-md-4 bg-light rounded shadow-sm">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-semibold mb-0">
                                <i class="bi bi-funnel-fill me-2 text-primary"></i>Bộ lọc
                            </h6>
                            <button type="button" class="btn btn-sm btn-link text-decoration-none d-md-none" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                                <i class="bi bi-chevron-down"></i>
                            </button>
                        </div>
                        
                        <form method="GET" action="{{ route('admin.dashboard') }}" id="orderFilterForm">
                            {{-- Giữ lại filter doanh thu nếu có --}}
                            @if($startDate)
                                <input type="hidden" name="start_date" value="{{ $startDate }}">
                            @endif
                            @if($endDate)
                                <input type="hidden" name="end_date" value="{{ $endDate }}">
                            @endif
                            
                            <div class="collapse d-md-block" id="filterCollapse">
                                {{-- Loại filter --}}
                                <div class="row g-3 mb-3">
                                    <div class="col-12">
                                        <label for="order_filter_type" class="form-label small fw-semibold">
                                            <i class="bi bi-filter-circle me-1 text-primary"></i>Loại lọc
                                        </label>
                                        <select name="order_filter_type" id="order_filter_type" class="form-select" onchange="toggleFilterOptions()">
                                            <option value="today" {{ ($orderFilterType ?? '') == 'today' ? 'selected' : '' }}>Hôm nay</option>
                                            <option value="last_7_days" {{ ($orderFilterType ?? '') == 'last_7_days' ? 'selected' : '' }}>7 ngày gần nhất</option>
                                            <option value="last_15_days" {{ ($orderFilterType ?? '') == 'last_15_days' ? 'selected' : '' }}>15 ngày gần nhất</option>
                                            <option value="last_30_days" {{ ($orderFilterType ?? 'last_30_days') == 'last_30_days' ? 'selected' : '' }}>30 ngày gần nhất</option>
                                            <option value="all" {{ ($orderFilterType ?? '') == 'all' ? 'selected' : '' }}>Tất cả</option>
                                            <option value="month" {{ ($orderFilterType ?? '') == 'month' ? 'selected' : '' }}>Theo tháng</option>
                                            <option value="date_range" {{ ($orderFilterType ?? '') == 'date_range' ? 'selected' : '' }}>Khoảng thời gian</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Filter theo tháng --}}
                                <div class="row g-3 mb-3" id="filter_month_group" style="display: {{ ($orderFilterType ?? 'last_30_days') == 'month' ? 'flex' : 'none' }};">
                                    <div class="col-12 col-sm-6 col-md-6">
                                        <label for="order_month" class="form-label small fw-semibold">
                                            <i class="bi bi-calendar-month me-1 text-primary"></i>Tháng
                                        </label>
                                        <select name="order_month" id="order_month" class="form-select">
                                            <option value="">-- Chọn tháng --</option>
                                            @for($m = 1; $m <= 12; $m++)
                                                <option value="{{ $m }}" {{ ($orderFilterMonth ?? '') == $m ? 'selected' : '' }}>
                                                    Tháng {{ $m }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="col-12 col-sm-6 col-md-6">
                                        <label for="order_year" class="form-label small fw-semibold">
                                            <i class="bi bi-calendar-year me-1 text-primary"></i>Năm
                                        </label>
                                        <select name="order_year" id="order_year" class="form-select">
                                            <option value="">-- Chọn năm --</option>
                                            @for($y = now()->year; $y >= now()->year - 5; $y--)
                                                <option value="{{ $y }}" {{ ($orderFilterYear ?? '') == $y ? 'selected' : '' }}>
                                                    {{ $y }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                {{-- Filter theo khoảng thời gian --}}
                                <div class="row g-3 mb-3" id="filter_date_range_group" style="display: {{ ($orderFilterType ?? '') == 'date_range' ? 'flex' : 'none' }};">
                                    <div class="col-12 col-sm-6">
                                        <label for="order_start_date" class="form-label small fw-semibold">
                                            <i class="bi bi-calendar-event me-1 text-primary"></i>Từ ngày
                                        </label>
                                        <input type="date" name="order_start_date" id="order_start_date" 
                                            class="form-control" 
                                            value="{{ $orderFilterStartDate ?? '' }}">
                                    </div>

                                    <div class="col-12 col-sm-6">
                                        <label for="order_end_date" class="form-label small fw-semibold">
                                            <i class="bi bi-calendar-event-fill me-1 text-primary"></i>Đến ngày
                                        </label>
                                        <input type="date" name="order_end_date" id="order_end_date" 
                                            class="form-control" 
                                            value="{{ $orderFilterEndDate ?? '' }}">
                                    </div>
                                </div>

                                {{-- Filter theo trạng thái --}}
                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-md-4">
                                        <label for="order_status" class="form-label small fw-semibold">
                                            <i class="bi bi-funnel me-1 text-primary"></i>Trạng thái đơn hàng
                                        </label>
                                        <select name="order_status" id="order_status" class="form-select">
                                            <option value="all" {{ ($orderFilterStatus ?? 'all') == 'all' ? 'selected' : '' }}>Tất cả trạng thái</option>
                                            <option value="pending" {{ ($orderFilterStatus ?? '') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                            <option value="processing" {{ ($orderFilterStatus ?? '') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                            <option value="shipping" {{ ($orderFilterStatus ?? '') == 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                                            <option value="completed" {{ ($orderFilterStatus ?? '') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                            <option value="cancelled" {{ ($orderFilterStatus ?? '') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-8 d-flex align-items-end gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-search me-1"></i>Áp dụng bộ lọc
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="resetOrderFilter()">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Đặt lại
                                        </button>
                                        <a href="{{ route('admin.orders.list') }}" class="btn btn-outline-primary">
                                            <i class="bi bi-list-ul me-1"></i>Xem tất cả
                                        </a>
                                        
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Thống kê theo trạng thái --}}
                    <div class="row g-3 mb-4">
                        @php
                            $statusConfig = [
                                'pending' => ['label' => 'Chờ xử lý', 'color' => 'warning', 'icon' => 'clock'],
                                'processing' => ['label' => 'Đang xử lý', 'color' => 'info', 'icon' => 'gear'],
                                'shipping' => ['label' => 'Đang giao hàng', 'color' => 'primary', 'icon' => 'truck'],
                                'completed' => ['label' => 'Hoàn thành', 'color' => 'success', 'icon' => 'check-circle'],
                                'cancelled' => ['label' => 'Đã hủy', 'color' => 'danger', 'icon' => 'x-circle'],
                            ];
                        @endphp
                        @foreach($statusConfig as $status => $config)
                            <div class="col-6 col-md-3 col-lg">
                                <div class="stat-mini-card p-3 p-md-3 rounded border border-{{ $config['color'] }} border-2 bg-{{ $config['color'] }} bg-opacity-10 h-100">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="flex-grow-1">
                                            <p class="text-muted small mb-1 fw-medium">{{ $config['label'] }}</p>
                                            <h4 class="fw-bold mb-0 stat-mini-number">{{ $orderStatsByStatus[$status] ?? 0 }}</h4>
                                        </div>
                                        <div class="stat-mini-icon bg-{{ $config['color'] }} bg-opacity-25 rounded-circle p-2 flex-shrink-0">
                                            @if($status == 'shipping')
                                                <i class="bi bi-car-front-fill text-primary fs-5"></i>
                                            @else
                                                <i class="bi bi-{{ $config['icon'] }}-fill text-{{ $config['color'] }} fs-5"></i>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Biểu đồ tăng trưởng --}}
                    <div class="row g-4 mb-4">
                        <div class="col-12 col-lg-8">
                            <div class="card border-0 bg-gradient-primary text-white shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px !important;">
                                <div class="card-body p-3 p-md-4">
                                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3 gap-2">
                                        <div>
                                            <h6 class="text-white-50 mb-1 small">Tăng trưởng đơn hàng</h6>
                                            <h3 class="fw-bold mb-0 text-white fs-4 fs-md-3">7 ngày gần nhất</h3>
                                        </div>
                                        <div class="text-start text-sm-end">
                                            @if($orderGrowthRate != 0)
                                                <span class="badge bg-{{ $orderGrowthRate > 0 ? 'success' : 'danger' }} fs-6 px-3 py-2">
                                                    <i class="bi bi-arrow-{{ $orderGrowthRate > 0 ? 'up' : 'down' }}-short"></i>
                                                    {{ abs($orderGrowthRate) }}%
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="chart-container">
                                        <canvas id="orderGrowthChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body p-3 p-md-4">
                                    <h6 class="fw-semibold mb-3 d-flex align-items-center">
                                        <i class="bi bi-graph-up-arrow me-2 text-primary"></i>Tổng quan
                                    </h6>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex justify-content-between align-items-center p-2 p-md-3 bg-light rounded stat-overview-item">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-calendar-day text-primary me-2"></i>
                                                <span class="text-muted small">Hôm nay</span>
                                            </div>
                                            <span class="fw-bold text-primary">{{ $growthChartData[6] ?? 0 }} đơn</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center p-2 p-md-3 bg-light rounded stat-overview-item">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-calendar-minus text-info me-2"></i>
                                                <span class="text-muted small">Hôm qua</span>
                                            </div>
                                            <span class="fw-bold text-info">{{ $growthChartData[5] ?? 0 }} đơn</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center p-2 p-md-3 bg-light rounded stat-overview-item">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-calendar-week text-success me-2"></i>
                                                <span class="text-muted small">7 ngày qua</span>
                                            </div>
                                            <span class="fw-bold text-success">{{ array_sum($growthChartData) }} đơn</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center p-2 p-md-3 bg-light rounded stat-overview-item">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-bar-chart text-warning me-2"></i>
                                                <span class="text-muted small">Trung bình/ngày</span>
                                            </div>
                                            <span class="fw-bold text-warning">{{ round(array_sum($growthChartData) / 7, 1) }} đơn</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Danh sách đơn hàng --}}
                    {{-- Desktop Table View --}}
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-semibold">Mã đơn</th>
                                    <th class="fw-semibold">Khách hàng</th>
                                    <th class="fw-semibold">Ngày đặt</th>
                                    <th class="fw-semibold">Tổng tiền</th>
                                    <th class="fw-semibold">Trạng thái</th>
                                    <th class="fw-semibold text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($filteredOrders as $order)
                                    <tr class="order-table-row">
                                        <td>
                                            <span class="fw-semibold text-primary">{{ $order->order_code }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    <i class="bi bi-person-circle text-muted fs-5"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-medium">{{ $order->user->name ?? 'N/A' }}</div>
                                                    <small class="text-muted">{{ Str::limit($order->user->email ?? '', 25) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-medium">{{ $order->created_at->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success fs-6">{{ number_format($order->final_total) }} ₫</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'processing' => 'info',
                                                    'shipping' => 'primary',
                                                    'completed' => 'success',
                                                    'cancelled' => 'danger',
                                                ];
                                                $statusLabels = [
                                                    'pending' => 'Chờ xử lý',
                                                    'processing' => 'Đang xử lý',
                                                    'shipping' => 'Đang giao hàng',
                                                    'completed' => 'Hoàn thành',
                                                    'cancelled' => 'Đã hủy',
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$order->order_status] ?? 'secondary' }} px-3 py-2">
                                                {{ $statusLabels[$order->order_status] ?? ucfirst($order->order_status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.orders.show', $order->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye me-1"></i>Chi tiết
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                            <p class="text-muted mb-0">Không có đơn hàng nào phù hợp với bộ lọc</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile Card View --}}
                    <div class="d-md-none">
                        @forelse($filteredOrders as $order)
                            @php
                                $statusColors = [
                                    'pending' => 'warning',
                                    'processing' => 'info',
                                    'shipping' => 'primary',
                                    'completed' => 'success',
                                    'cancelled' => 'danger',
                                ];
                                $statusLabels = [
                                    'pending' => 'Chờ xử lý',
                                    'processing' => 'Đang xử lý',
                                    'shipping' => 'Đang giao hàng',
                                    'completed' => 'Hoàn thành',
                                    'cancelled' => 'Đã hủy',
                                ];
                            @endphp
                            <div class="card mb-3 order-mobile-card shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="fw-bold text-primary mb-1">{{ $order->order_code }}</h6>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar3 me-1"></i>{{ $order->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                        <span class="badge bg-{{ $statusColors[$order->order_status] ?? 'secondary' }} px-3 py-2">
                                            {{ $statusLabels[$order->order_status] ?? ucfirst($order->order_status) }}
                                        </span>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi bi-person-circle text-muted me-2"></i>
                                            <div>
                                                <div class="fw-medium small">{{ $order->user->name ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ Str::limit($order->user->email ?? '', 30) }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                        <div>
                                            <small class="text-muted d-block">Tổng tiền</small>
                                            <span class="fw-bold text-success fs-5">{{ number_format($order->final_total) }} ₫</span>
                                        </div>
                                        <a href="{{ route('admin.orders.show', $order->id) }}" 
                                           class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye me-1"></i>Chi tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="card text-center py-5">
                                <div class="card-body">
                                    <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                    <p class="text-muted mb-0">Không có đơn hàng nào phù hợp với bộ lọc</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    {{-- Phân trang sticky --}}
                    @if($filteredOrders->hasPages())
                        <div class="sticky-pagination-wrapper">
                            <div class="sticky-pagination">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-3 bg-white border-top shadow-sm">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-muted small">
                                            Hiển thị {{ $filteredOrders->firstItem() ?? 0 }} - {{ $filteredOrders->lastItem() ?? 0 }} 
                                            trong tổng số {{ $filteredOrders->total() }} đơn hàng
                                        </span>
                                    </div>
                                    <div>
                                        {{ $filteredOrders->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
>>>>>>> origin/Trang_Chu_Client

    <style>
        /* Stat Card Styles */
        .stat-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border-radius: 12px;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        }

        body.dark .stat-card {
            background: linear-gradient(135deg, #1f1f1f 0%, #2b2b2b 100%);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
        }

        body.dark .stat-card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5) !important;
        }

        .stat-card-bg {
            position: absolute;
            top: 0;
            right: 0;
            width: 120px;
            height: 120px;
            background: radial-gradient(circle, rgba(74, 108, 247, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            transform: translate(30px, -30px);
            transition: all 0.3s ease;
        }

        .stat-card:hover .stat-card-bg {
            transform: translate(20px, -20px) scale(1.2);
        }

        .stat-icon-wrapper {
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .stat-card:hover .stat-icon-wrapper {
            transform: scale(1.1) rotate(5deg);
        }

        .stat-number {
            font-size: 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: all 0.3s ease;
        }

<<<<<<< HEAD
        /* KPI cards */
        .kpi-card {
            border-radius: 16px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .kpi-card:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }

        .kpi-icon {
            width: 42px;
            height: 42px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }

=======
>>>>>>> origin/Trang_Chu_Client
        body.dark .stat-number {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-card:hover .stat-number {
            transform: scale(1.05);
        }

        .stat-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }

        .stat-action {
            transition: all 0.3s ease;
            opacity: 0.5;
        }

        .stat-card:hover .stat-action {
            opacity: 1;
            transform: translateX(5px);
        }

        .stat-info {
            flex: 1;
        }

        /* Animation khi load */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-card {
            animation: fadeInUp 0.6s ease-out;
        }

        .stat-card:nth-child(2) {
            animation-delay: 0.1s;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stat-number {
                font-size: 1.5rem;
            }

            .stat-icon-wrapper {
                padding: 0.75rem !important;
            }

            .stat-icon-wrapper i {
                font-size: 1.25rem !important;
            }
        }

        .half-gauge-wrapper {
            position: relative;
            width: 250px;
            margin: 0 auto;
            text-align: center;
        }

        .half-gauge {
            overflow: visible;
        }

        .half-gauge-bg {
            stroke: #e5e5e5;
            stroke-width: 14;
            fill: none;
        }

        .half-gauge-value {
            stroke: #4A6CF7;
            stroke-width: 14;
            fill: none;
            stroke-linecap: round;
            stroke-dasharray: 314;
            stroke-dashoffset: 314;
            transition: stroke-dashoffset 1.2s ease;
        }


        .half-gauge-text {
            position: absolute;
            top: 55%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        /* Order Statistics Component Styles */
        .order-filter-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }

        body.dark .order-filter-section {
            background: linear-gradient(135deg, #2b2b2b 0%, #1f1f1f 100%);
            border-color: #444;
        }

        .order-filter-section:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .stat-mini-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .stat-mini-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .stat-mini-card:hover::before {
            left: 100%;
        }

        .stat-mini-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        body.dark .stat-mini-card {
            background-color: #1f1f1f !important;
            border-color: #444 !important;
        }

        .stat-mini-icon {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .stat-mini-card:hover .stat-mini-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .stat-mini-number {
            font-size: 1.75rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        body.dark .stat-mini-number {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .bg-gradient-primary {
            border-radius: 12px !important;
            overflow: hidden;
        }

        .chart-container {
            min-height: 250px;
        }

        .stat-overview-item {
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .stat-overview-item:hover {
            background-color: #e9ecef !important;
            border-color: #dee2e6;
            transform: translateX(5px);
        }

        body.dark .stat-overview-item {
            background-color: #2b2b2b !important;
        }

        body.dark .stat-overview-item:hover {
            background-color: #333 !important;
            border-color: #555;
        }

        .order-table-row {
            transition: all 0.2s ease;
        }

        .order-table-row:hover {
            background-color: #f8f9fa !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        body.dark .order-table-row:hover {
            background-color: #2b2b2b !important;
        }

        .order-mobile-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .order-mobile-card:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
        }

        body.dark .order-mobile-card {
            background-color: #1f1f1f;
            border-color: #444;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .order-filter-section,
        .stat-mini-card {
            animation: slideInRight 0.5s ease-out;
        }

        .stat-mini-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-mini-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-mini-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-mini-card:nth-child(4) { animation-delay: 0.4s; }
        .stat-mini-card:nth-child(5) { animation-delay: 0.5s; }

        /* Responsive Improvements */
        @media (max-width: 768px) {
            .stat-mini-card {
                padding: 1rem !important;
            }

            .stat-mini-number {
                font-size: 1.5rem;
            }

            .stat-mini-icon {
                width: 40px;
                height: 40px;
            }

            .stat-mini-icon i {
                font-size: 1rem !important;
            }

            .chart-container {
                height: 200px !important;
            }

            .order-filter-section {
                padding: 1rem !important;
            }

            .card-header h5 {
                font-size: 1.1rem;
            }

            .card-header .badge {
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
            }
        }

        @media (max-width: 576px) {
            .stat-mini-card {
                padding: 0.75rem !important;
            }

            .stat-mini-number {
                font-size: 1.25rem;
            }

            .stat-mini-icon {
                width: 35px;
                height: 35px;
            }

            .chart-container {
                height: 180px !important;
            }

            .bg-gradient-primary .card-body {
                padding: 1rem !important;
            }

            .stat-overview-item {
                padding: 0.75rem !important;
            }
        }

        /* Card Header Improvements */
        .card-header {
            border-radius: 12px 12px 0 0 !important;
        }

        /* Form Select & Input Improvements */
        .form-select:focus,
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        /* Badge Improvements */
        .badge {
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        /* Button Improvements */
        .btn {
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .btn:active {
            transform: translateY(0);
        }

        /* Sticky Pagination */
        .sticky-pagination-wrapper {
            position: relative;
            margin-top: 2rem;
        }

        .sticky-pagination {
            position: sticky;
            bottom: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 12px 12px 0 0;
            margin-top: 1rem;
        }

        body.dark .sticky-pagination {
            background: rgba(31, 31, 31, 0.98);
        }

        .sticky-pagination .pagination {
            margin-bottom: 0;
        }

        .sticky-pagination .page-link {
            border-radius: 8px;
            margin: 0 2px;
            border: 1px solid #dee2e6;
            color: #667eea;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .sticky-pagination .page-link:hover {
            background-color: #667eea;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
        }

        .sticky-pagination .page-item.active .page-link {
            background-color: #667eea;
            border-color: #667eea;
            color: #fff;
        }

        body.dark .sticky-pagination .page-link {
            background-color: #2b2b2b;
            border-color: #444;
            color: #4facfe;
        }

        body.dark .sticky-pagination .page-link:hover {
            background-color: #4facfe;
            color: #fff;
        }

        body.dark .sticky-pagination .page-item.active .page-link {
            background-color: #4facfe;
            border-color: #4facfe;
        }

        @media (max-width: 768px) {
            .sticky-pagination {
                position: relative;
                margin-top: 1rem;
            }

            .sticky-pagination .d-flex {
                flex-direction: column;
                gap: 1rem;
            }

            .sticky-pagination .pagination {
                justify-content: center;
                flex-wrap: wrap;
            }
        }
    </style>

<<<<<<< HEAD
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
=======
    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
>>>>>>> origin/Trang_Chu_Client
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Counter Animation cho số liệu thống kê
            function animateCounter(element, target, duration = 2000) {
                const start = 0;
                const increment = target / (duration / 16);
                let current = start;

                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    element.textContent = Math.floor(current).toLocaleString('vi-VN');
                }, 16);
            }

            // Áp dụng animation cho các số liệu
            const statNumbers = document.querySelectorAll('.stat-number');
            statNumbers.forEach(stat => {
                const originalText = stat.textContent.replace(/\D/g, '');
                const target = parseInt(originalText) || 0;
                stat.textContent = '0';
                setTimeout(() => {
                    animateCounter(stat, target, 1500);
                }, 300);
            });

            // Gauge Animation
            const percent = {{ round(($currentMonthRevenue / max($monthlyTarget, 1)) * 100, 2) }};
            const gauge = document.querySelector('.half-gauge-value');
            const text = document.getElementById('halfPercent');

            if (gauge && text) {
                const totalLength = 314;
                const offset = totalLength - (percent / 100) * totalLength;

                gauge.style.strokeDashoffset = offset;
                text.innerText = percent + "%";
            }
        });

<<<<<<< HEAD
=======


        const revenueData = @json($revenueData);
        const ctx = document.getElementById('revenueChart').getContext('2d');

>>>>>>> origin/Trang_Chu_Client
        let isDark = document.documentElement.classList.contains("dark");

        const textColor = () => isDark ? '#ffffff' : '#000000';
        const bgColor = () => isDark ? 'rgba(59,130,246,0.4)' : 'rgba(59,130,246,0.6)';
        const borderCol = 'rgb(59,130,246)';

<<<<<<< HEAD
        window.addEventListener("theme-changed", () => {
            isDark = document.documentElement.classList.contains("dark");
=======
        let chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: revenueData,
                    backgroundColor: bgColor(),
                    borderColor: borderCol,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let v = context.raw ?? 0;
                                return Math.round(v).toLocaleString('vi-VN') + ' ₫';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: textColor()
                        }
                    },
                    y: {
                        ticks: {
                            color: textColor(),
                            callback: function(value) {
                                return Math.round(value).toLocaleString('vi-VN') + ' ₫';
                            }
                        }
                    }
                }
            }
        });

        window.addEventListener("theme-changed", () => {
            isDark = document.documentElement.classList.contains("dark");
            chart.options.scales.x.ticks.color = textColor();
            chart.options.scales.y.ticks.color = textColor();
            chart.data.datasets[0].backgroundColor = bgColor();
            chart.update();
>>>>>>> origin/Trang_Chu_Client
            
            // Update order growth chart
            if (orderGrowthChart) {
                orderGrowthChart.options.scales.x.ticks.color = textColor();
                orderGrowthChart.options.scales.y.ticks.color = textColor();
                orderGrowthChart.update();
            }
        });

        // Biểu đồ tăng trưởng đơn hàng
        const orderGrowthCtx = document.getElementById('orderGrowthChart');
        let orderGrowthChart = null;

        if (orderGrowthCtx) {
            const orderGrowthData = @json($growthChartData);
            const orderGrowthLabels = @json($growthChartLabels);

            // Responsive chart configuration
            const isMobile = window.innerWidth < 768;
            const isTablet = window.innerWidth >= 768 && window.innerWidth < 992;

            orderGrowthChart = new Chart(orderGrowthCtx, {
                type: 'line',
                data: {
                    labels: orderGrowthLabels,
                    datasets: [{
                        label: 'Số đơn hàng',
                        data: orderGrowthData,
                        borderColor: 'rgba(255, 255, 255, 0.9)',
                        backgroundColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: isMobile ? 2 : 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: isMobile ? 3 : 5,
                        pointHoverRadius: isMobile ? 5 : 7,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: 'rgba(255, 255, 255, 0.2)',
                            borderWidth: 1,
                            padding: isMobile ? 8 : 12,
                            titleFont: {
                                size: isMobile ? 12 : 14
                            },
                            bodyFont: {
                                size: isMobile ? 11 : 13
                            },
                            callbacks: {
                                label: function(context) {
                                    return 'Đơn hàng: ' + context.parsed.y;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: 'rgba(255, 255, 255, 0.7)',
                                font: {
                                    size: isMobile ? 9 : 11
                                },
                                maxRotation: isMobile ? 45 : 0,
                                minRotation: 0
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)',
                                display: !isMobile
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: 'rgba(255, 255, 255, 0.7)',
                                font: {
                                    size: isMobile ? 9 : 11
                                },
                                stepSize: 1
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        }
                    }
                }
            });

            // Handle window resize
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if (orderGrowthChart) {
                        orderGrowthChart.resize();
                    }
                }, 250);
            });
        }

<<<<<<< HEAD
        // Control chart doanh thu 30 ngày
        // Control chart doanh thu theo thời gian (ApexCharts)
        let revenueControlChart = null;
        const controlChartEl = document.querySelector('#revenueControlChart');
        const controlRangeButtons = document.querySelectorAll('[data-range]');
        const controlCustomWrapper = document.querySelector('.control-chart-custom-range');
        const controlFromInput = document.getElementById('revenueControlFrom');
        const controlToInput = document.getElementById('revenueControlTo');
        const controlApplyBtn = document.getElementById('revenueControlApply');

        function formatCurrencyVN(value) {
            return Math.round(value).toLocaleString('vi-VN') + ' VND';
        }

        async function loadRevenueControlChart(range = '7') {
            if (!controlChartEl) return;

            try {
                const baseUrl = "{{ url('/admin/api/dashboard/revenue/control-chart') }}";
                const params = new URLSearchParams({ range });

                if (range === 'custom') {
                    const from = controlFromInput.value;
                    const to = controlToInput.value;
                    if (from) params.append('from', from);
                    if (to) params.append('to', to);
                }

                const response = await fetch(`${baseUrl}?${params.toString()}`);
                const data = await response.json();

                const { dates, actual, mean, ucl, lcl } = data;

                const meanSeries = dates.map(() => mean);
                const uclSeries = dates.map(() => ucl);
                const lclSeries = dates.map(() => lcl);

                const options = {
                    chart: {
                        type: 'line',
                        height: 320,
                        toolbar: {
                            show: true,
                            tools: {
                                download: true,
                                selection: true,
                                zoom: true,
                                zoomin: true,
                                zoomout: true,
                                pan: true,
                                reset: true,
                            }
                        },
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 700,
                        },
                        zoom: {
                            enabled: true,
                            type: 'x',
                            autoScaleYaxis: true,
                        },
                    },
                    stroke: {
                        width: [3, 2, 1.5, 1.5],
                        curve: 'smooth',
                        dashArray: [0, 5, 4, 4],
                    },
                    colors: [
                        '#3b82f6', // Actual
                        '#facc15', // Mean
                        '#ef4444', // UCL
                        '#fb923c', // LCL
                    ],
                    series: [
                        {
                            name: 'Doanh thu thực tế',
                            data: actual,
                        },
                        {
                            name: 'Trung bình (Mean)',
                            data: meanSeries,
                        },
                        {
                            name: 'UCL',
                            data: uclSeries,
                        },
                        {
                            name: 'LCL',
                            data: lclSeries,
                        },
                    ],
                    xaxis: {
                        categories: dates,
                        labels: {
                            rotate: -45,
                            style: {
                                colors: '#6b7280',
                                fontSize: '11px',
                            },
                        },
                    },
                    yaxis: {
                        labels: {
                            formatter: function (value) {
                                return Math.round(value).toLocaleString('vi-VN') + ' ₫';
                            },
                        },
                    },
                    tooltip: {
                        shared: true,
                        intersect: false,
                        x: {
                            format: 'dd/MM/yyyy',
                        },
                        custom: function ({ series, seriesIndex, dataPointIndex, w }) {
                            const date = w.globals.categoryLabels[dataPointIndex];
                            const actualValue = series[0][dataPointIndex] ?? 0;
                            const meanValue = series[1][dataPointIndex] ?? 0;
                            const uclValue = series[2][dataPointIndex] ?? 0;
                            const lclValue = series[3][dataPointIndex] ?? 0;

                            let status = '';
                            if (actualValue > uclValue) {
                                status = '<div class="text-danger small mt-1">❗ Giao dịch xuất sắc (vượt UCL)</div>';
                            } else if (actualValue < lclValue) {
                                status = '<div class="text-warning small mt-1">⚠ Doanh thu thấp (dưới LCL)</div>';
                            }

                            return `
                                <div class="px-2 py-1">
                                    <div class="fw-semibold mb-1">${date}</div>
                                    <div class="small">Actual: <strong>${formatCurrencyVN(actualValue)}</strong></div>
                                    <div class="small text-muted">Mean: ${formatCurrencyVN(meanValue)}</div>
                                    <div class="small text-muted">UCL: ${formatCurrencyVN(uclValue)}</div>
                                    <div class="small text-muted">LCL: ${formatCurrencyVN(lclValue)}</div>
                                    ${status}
                                </div>
                            `;
                        },
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'left',
                    },
                    grid: {
                        borderColor: '#e5e7eb',
                        strokeDashArray: 4,
                    },
                };

                if (revenueControlChart) {
                    revenueControlChart.updateOptions(options, true, true);
                } else {
                    revenueControlChart = new ApexCharts(controlChartEl, options);
                    revenueControlChart.render();
                }
            } catch (e) {
                console.error('Failed to load revenue control chart', e);
            }
        }

        if (controlChartEl) {
            // Khởi tạo mặc định 7 ngày
            loadRevenueControlChart('7');

            controlRangeButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    controlRangeButtons.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    const range = btn.getAttribute('data-range');

                    if (range === 'custom') {
                        controlCustomWrapper.style.display = 'flex';
                    } else {
                        controlCustomWrapper.style.display = 'none';
                        loadRevenueControlChart(range);
                    }
                });
            });

            if (controlApplyBtn) {
                controlApplyBtn.addEventListener('click', () => {
                    loadRevenueControlChart('custom');
                });
            }
        }

        // Biểu đồ tỷ lệ trạng thái đơn hàng (donut)
        const orderStatusCtx = document.getElementById('orderStatusChart');
        if (orderStatusCtx) {
            const rawStatusData = @json($orderStatsByStatus);
            const statusLabelsMap = {
                pending: 'Chờ xử lý',
                processing: 'Đang xử lý',
                shipping: 'Đang giao hàng',
                completed: 'Hoàn thành',
                cancelled: 'Đã hủy',
            };
            const statusColors = {
                pending: '#ffc107',
                processing: '#0dcaf0',
                shipping: '#0d6efd',
                completed: '#198754',
                cancelled: '#dc3545',
            };

            const labels = [];
            const data = [];
            const colors = [];

            Object.keys(rawStatusData).forEach((key) => {
                const value = rawStatusData[key] ?? 0;
                if (value > 0) {
                    labels.push(statusLabelsMap[key] ?? key);
                    data.push(value);
                    colors.push(statusColors[key] ?? '#6c757d');
                }
            });

            if (data.length > 0) {
                new Chart(orderStatusCtx, {
                    type: 'doughnut',
                    data: {
                        labels,
                        datasets: [{
                            data,
                            backgroundColor: colors,
                            borderWidth: 1,
                            borderColor: '#ffffff',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 14,
                                    boxHeight: 14,
                                    usePointStyle: true,
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((sum, v) => sum + v, 0);
                                        const value = context.raw ?? 0;
                                        const percent = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return `${context.label}: ${value} đơn (${percent}%)`;
                                    }
                                }
                            }
                        },
                    }
                });
            } else {
                orderStatusCtx.parentElement.style.display = 'none';
            }
        }

=======
>>>>>>> origin/Trang_Chu_Client
        // Toggle filter options based on filter type
        function toggleFilterOptions() {
            const filterType = document.getElementById('order_filter_type').value;
            const monthGroup = document.getElementById('filter_month_group');
            const dateRangeGroup = document.getElementById('filter_date_range_group');
            
            // Hide all groups first
            monthGroup.style.display = 'none';
            dateRangeGroup.style.display = 'none';
            
            // Show relevant group
            if (filterType === 'month') {
                monthGroup.style.display = 'flex';
            } else if (filterType === 'date_range') {
                dateRangeGroup.style.display = 'flex';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleFilterOptions();
        });

        // Reset filter function
        function resetOrderFilter() {
            document.getElementById('order_filter_type').value = 'last_30_days';
            document.getElementById('order_month').value = '';
            document.getElementById('order_year').value = '';
            document.getElementById('order_start_date').value = '';
            document.getElementById('order_end_date').value = '';
            document.getElementById('order_status').value = 'all';
            
            // Toggle filter options
            toggleFilterOptions();
            
            // Xóa các hidden input filter doanh thu
            const form = document.getElementById('orderFilterForm');
            const hiddenInputs = form.querySelectorAll('input[type="hidden"]');
            hiddenInputs.forEach(input => {
                if (input.name === 'start_date' || input.name === 'end_date') {
                    input.remove();
                }
            });
            form.submit();
        }
    </script>
<<<<<<< HEAD
    @endpush
=======
>>>>>>> origin/Trang_Chu_Client
@endsection
