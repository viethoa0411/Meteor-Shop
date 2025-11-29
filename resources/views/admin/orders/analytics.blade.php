@extends('admin.layouts.app')

@section('title', 'Thống kê Đơn hàng Chi tiết')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Thống kê Đơn hàng Chi tiết</h3>
            <p class="text-muted mb-0">Phân tích toàn diện về hiệu suất đơn hàng</p>
        </div>
        <a href="{{ route('admin.orders.list') }}" class="btn btn-outline-secondary">
            Danh sách đơn hàng
        </a>
    </div>

    {{-- KHỐI 1: THANH BỘ LỌC --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="{{ route('admin.orders.analytics') }}" id="analyticsFilterForm">
                <div class="row g-3">
                    <div class="col-12 col-md-3">
                        <label for="date_range" class="form-label small fw-semibold">
                            <i class="bi bi-calendar-range me-1 text-primary"></i>Khoảng thời gian
                        </label>
                        <select name="date_range" id="date_range" class="form-select" onchange="toggleCustomDate()">
                            <option value="all" {{ $dateRange == 'all' ? 'selected' : '' }}>Tất cả đơn hàng</option>
                            <option value="today" {{ $dateRange == 'today' ? 'selected' : '' }}>Hôm nay</option>
                            <option value="yesterday" {{ $dateRange == 'yesterday' ? 'selected' : '' }}>Hôm qua</option>
                            <option value="last_7_days" {{ $dateRange == 'last_7_days' ? 'selected' : '' }}>7 ngày gần nhất</option>
                            <option value="last_15_days" {{ $dateRange == 'last_15_days' ? 'selected' : '' }}>15 ngày gần nhất</option>
                            <option value="last_30_days" {{ $dateRange == 'last_30_days' ? 'selected' : '' }}>30 ngày gần nhất</option>
                            <option value="custom" {{ $dateRange == 'custom' ? 'selected' : '' }}>Tùy chọn</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-3" id="custom_date_group" style="display: {{ $dateRange == 'custom' ? 'block' : 'none' }};">
                        <label for="start_date" class="form-label small fw-semibold">Từ ngày</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                    </div>

                    <div class="col-12 col-md-3" id="custom_date_group_end" style="display: {{ $dateRange == 'custom' ? 'block' : 'none' }};">
                        <label for="end_date" class="form-label small fw-semibold">Đến ngày</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                    </div>

                    <div class="col-12 col-sm-6 col-md-2">
                        <label for="status" class="form-label small fw-semibold">
                            <i class="bi bi-funnel me-1 text-primary"></i>Trạng thái
                        </label>
                        <select name="status" id="status" class="form-select">
                            <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Tất cả</option>
                            <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                            <option value="processing" {{ $status == 'processing' ? 'selected' : '' }}>Chuẩn bị hàng</option>
                            <option value="shipping" {{ $status == 'shipping' ? 'selected' : '' }}>Đang giao</option>
                            <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Đã giao</option>
                            <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            <option value="returned" {{ $status == 'returned' ? 'selected' : '' }}>Đã đổi trả</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search d-md-none"></i>
                            <span class="d-none d-md-inline">Áp dụng</span>
                        </button>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="resetFilters()">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Đặt lại
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- KHỐI 2: KPI CARDS --}}
    {{-- Desktop: 4 cột, Mobile: Carousel --}}
    <div class="d-none d-md-block">
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <a href="{{ route('admin.orders.list') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 analytics-kpi-card" style="border-left: 4px solid #667eea !important; cursor: pointer;">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <p class="text-muted small mb-1">Tổng đơn hàng</p>
                                    <h3 class="fw-bold mb-0 text-primary">{{ number_format($kpiData['total_orders']) }}</h3>
                                </div>
                                <i class="bi bi-cart-check text-primary fs-3"></i>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                @if($kpiData['total_orders_growth'] != 0)
                                    <span class="badge bg-{{ $kpiData['total_orders_growth'] > 0 ? 'success' : 'danger' }} me-2">
                                        <i class="bi bi-arrow-{{ $kpiData['total_orders_growth'] > 0 ? 'up' : 'down' }}-short"></i>
                                        {{ abs($kpiData['total_orders_growth']) }}%
                                    </span>
                                @endif
                                <small class="text-muted">So với kỳ trước</small>
                            </div>
                            <div class="sparkline-container" style="height: 30px;">
                                <canvas class="sparkline" data-values="{{ json_encode(array_slice($timelineData['data']['pending'] ?? [], -7)) }}"></canvas>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="{{ route('admin.orders.list', ['status' => 'completed']) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 analytics-kpi-card" style="border-left: 4px solid #10b981 !important; cursor: pointer;">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <p class="text-muted small mb-1">Đơn hoàn thành</p>
                                    <h3 class="fw-bold mb-0 text-success">{{ number_format($kpiData['completed_orders']) }}</h3>
                                </div>
                                <i class="bi bi-check-circle text-success fs-3"></i>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                @if($kpiData['completed_growth'] != 0)
                                    <span class="badge bg-{{ $kpiData['completed_growth'] > 0 ? 'success' : 'danger' }} me-2">
                                        <i class="bi bi-arrow-{{ $kpiData['completed_growth'] > 0 ? 'up' : 'down' }}-short"></i>
                                        {{ abs($kpiData['completed_growth']) }}%
                                    </span>
                                @endif
                                <small class="text-muted">Tỷ lệ: {{ $kpiData['conversion_rate'] }}%</small>
                            </div>
                            <div class="sparkline-container" style="height: 30px;">
                                <canvas class="sparkline" data-values="{{ json_encode(array_slice($timelineData['data']['completed'] ?? [], -7)) }}"></canvas>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="{{ route('admin.orders.list', ['status' => 'cancelled']) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 analytics-kpi-card" style="border-left: 4px solid #ef4444 !important; cursor: pointer;">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <p class="text-muted small mb-1">Đơn đã hủy</p>
                                    <h3 class="fw-bold mb-0 text-danger">{{ number_format($kpiData['canceled_orders']) }}</h3>
                                </div>
                                <i class="bi bi-x-circle text-danger fs-3"></i>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                @if($kpiData['canceled_growth'] != 0)
                                    <span class="badge bg-{{ $kpiData['canceled_growth'] > 0 ? 'danger' : 'success' }} me-2">
                                        <i class="bi bi-arrow-{{ $kpiData['canceled_growth'] > 0 ? 'up' : 'down' }}-short"></i>
                                        {{ abs($kpiData['canceled_growth']) }}%
                                    </span>
                                @endif
                                <small class="text-muted">Tỷ lệ hủy</small>
                            </div>
                            <div class="sparkline-container" style="height: 30px;">
                                <canvas class="sparkline" data-values="{{ json_encode(array_slice($timelineData['data']['cancelled'] ?? [], -7)) }}"></canvas>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 analytics-kpi-card" style="border-left: 4px solid #8b5cf6 !important;">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <p class="text-muted small mb-1">Đơn đã đổi trả</p>
                                <h3 class="fw-bold mb-0 text-purple">{{ number_format($kpiData['refunded_orders']) }}</h3>
                            </div>
                            <i class="bi bi-arrow-counterclockwise text-purple fs-3"></i>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            @if($kpiData['refunded_growth'] != 0)
                                <span class="badge bg-{{ $kpiData['refunded_growth'] > 0 ? 'warning' : 'success' }} me-2">
                                    <i class="bi bi-arrow-{{ $kpiData['refunded_growth'] > 0 ? 'up' : 'down' }}-short"></i>
                                    {{ abs($kpiData['refunded_growth']) }}%
                                </span>
                            @endif
                            <small class="text-muted">Tỷ lệ đổi trả</small>
                        </div>
                        <div class="sparkline-container" style="height: 30px;">
                            <canvas class="sparkline" data-values="{{ json_encode(array_slice($timelineData['data']['returned'] ?? [], -7)) }}"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile: Carousel --}}
    <div class="d-md-none mb-4">
        <div id="kpiCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <a href="{{ route('admin.orders.list') }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm analytics-kpi-card" style="border-left: 4px solid #667eea !important;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <p class="text-muted small mb-1">Tổng đơn hàng</p>
                                        <h3 class="fw-bold mb-0 text-primary">{{ number_format($kpiData['total_orders']) }}</h3>
                                    </div>
                                    <i class="bi bi-cart-check text-primary fs-3"></i>
                                </div>
                                <div class="d-flex align-items-center">
                                    @if($kpiData['total_orders_growth'] != 0)
                                        <span class="badge bg-{{ $kpiData['total_orders_growth'] > 0 ? 'success' : 'danger' }} me-2">
                                            <i class="bi bi-arrow-{{ $kpiData['total_orders_growth'] > 0 ? 'up' : 'down' }}-short"></i>
                                            {{ abs($kpiData['total_orders_growth']) }}%
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="carousel-item">
                    <a href="{{ route('admin.orders.list', ['status' => 'completed']) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm analytics-kpi-card" style="border-left: 4px solid #10b981 !important;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <p class="text-muted small mb-1">Đơn hoàn thành</p>
                                        <h3 class="fw-bold mb-0 text-success">{{ number_format($kpiData['completed_orders']) }}</h3>
                                    </div>
                                    <i class="bi bi-check-circle text-success fs-3"></i>
                                </div>
                                <div class="d-flex align-items-center">
                                    <small class="text-muted">Tỷ lệ: {{ $kpiData['conversion_rate'] }}%</small>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="carousel-item">
                    <a href="{{ route('admin.orders.list', ['status' => 'cancelled']) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm analytics-kpi-card" style="border-left: 4px solid #ef4444 !important;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <p class="text-muted small mb-1">Đơn đã hủy</p>
                                        <h3 class="fw-bold mb-0 text-danger">{{ number_format($kpiData['canceled_orders']) }}</h3>
                                    </div>
                                    <i class="bi bi-x-circle text-danger fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="carousel-item">
                    <div class="card border-0 shadow-sm analytics-kpi-card" style="border-left: 4px solid #8b5cf6 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <p class="text-muted small mb-1">Đơn đã đổi trả</p>
                                    <h3 class="fw-bold mb-0 text-purple">{{ number_format($kpiData['refunded_orders']) }}</h3>
                                </div>
                                <i class="bi bi-arrow-counterclockwise text-purple fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#kpiCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#kpiCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>

    {{-- KHỐI 3: ORDERS TIMELINE CHART --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <h5 class="fw-semibold mb-0">Biểu đồ Đơn hàng theo Thời gian</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="exportChart('timeline')" title="Xuất CSV">
                    <i class="bi bi-download me-1"></i><span class="d-none d-sm-inline">Export</span>
                </button>
            </div>
        </div>
        <div class="card-body p-3 p-md-4">
            <div class="mb-3 d-flex flex-wrap gap-2" id="timelineLegend">
                <label class="form-check-label small">
                    <input type="checkbox" class="form-check-input timeline-series" value="pending" checked> Tổng đơn
                </label>
                <label class="form-check-label small">
                    <input type="checkbox" class="form-check-input timeline-series" value="completed" checked> Hoàn thành
                </label>
                <label class="form-check-label small">
                    <input type="checkbox" class="form-check-input timeline-series" value="cancelled"> Đã hủy
                </label>
                <label class="form-check-label small">
                    <input type="checkbox" class="form-check-input timeline-series" value="returned"> Đổi trả
                </label>
            </div>
            <div class="chart-wrapper" style="position: relative; height: 350px;">
                <canvas id="timelineChart"></canvas>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- KHỐI 4: ORDERS BY STATUS --}}
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="fw-semibold mb-0">Đơn hàng theo Trạng thái</h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="chart-container mb-3" style="position: relative; height: 250px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table table-sm table-hover">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Trạng thái</th>
                                    <th class="text-end">Số lượng</th>
                                    <th class="text-end">Tỷ lệ</th>
                                    <th class="text-end">Thời gian xử lý TB</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statusData as $item)
                                    <tr style="cursor: pointer;" onclick="window.location='{{ route('admin.orders.list', ['status' => $item['status']]) }}'">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @php
                                                    $badgeColors = [
                                                        'pending' => 'warning',
                                                        'processing' => 'info',
                                                        'shipping' => 'primary',
                                                        'completed' => 'success',
                                                        'cancelled' => 'danger',
                                                        'return_requested' => 'secondary',
                                                        'returned' => 'secondary',
                                                    ];
                                                    $badgeColor = $badgeColors[$item['status']] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $badgeColor }} me-2" style="width: 8px; height: 8px; padding: 0;"></span>
                                                {{ $item['label'] }}
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold">{{ number_format((int)($item['count'] ?? 0)) }}</td>
                                        <td class="text-end">{{ $item['percentage'] }}%</td>
                                        <td class="text-end text-muted small">
                                            @if($item['status'] == 'completed')
                                                {{ $kpiData['avg_processing_time'] }}h
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- KHỐI 5: ORDER FUNNEL --}}
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="fw-semibold mb-0">Phễu Chuyển đổi Đơn hàng</h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="funnel-container">
                        @php
                            // Funnel mới theo yêu cầu
                            $funnelSteps = [
                                [
                                    'key' => 'created',
                                    'label' => 'Đơn đã tạo',
                                    'count' => $funnelData['steps']['created'],
                                    'color' => '#3B82F6',
                                    'icon' => 'bi-cart-plus',
                                    'rate' => null,
                                    'drop' => null,
                                    'status' => null
                                ],
                                [
                                    'key' => 'pending',
                                    'label' => 'Chờ xác nhận',
                                    'count' => $funnelData['steps']['pending'],
                                    'color' => '#F59E0B',
                                    'icon' => 'bi-hourglass-split',
                                    'rate' => $funnelData['conversion']['pending_rate'],
                                    'drop' => $funnelData['drops']['drop_pending'],
                                    'status' => 'pending'
                                ],
                                [
                                    'key' => 'processing',
                                    'label' => 'Đang xử lý',
                                    'count' => $funnelData['steps']['processing'],
                                    'color' => '#06B6D4',
                                    'icon' => 'bi-gear',
                                    'rate' => $funnelData['conversion']['processing_rate'],
                                    'drop' => $funnelData['drops']['drop_processing'],
                                    'status' => 'processing'
                                ],
                                [
                                    'key' => 'shipping',
                                    'label' => 'Đang giao',
                                    'count' => $funnelData['steps']['shipping'],
                                    'color' => '#667eea',
                                    'icon' => 'bi-truck',
                                    'rate' => $funnelData['conversion']['shipping_rate'],
                                    'drop' => $funnelData['drops']['drop_shipping'],
                                    'status' => 'shipping'
                                ],
                                [
                                    'key' => 'completed',
                                    'label' => 'Hoàn tất',
                                    'count' => $funnelData['steps']['completed'],
                                    'color' => '#10B981',
                                    'icon' => 'bi-check-circle',
                                    'rate' => $funnelData['conversion']['completed_rate'],
                                    'drop' => $funnelData['drops']['drop_completed'],
                                    'status' => 'completed'
                                ],
                            ];
                            // Tính maxCount từ tổng số đơn ở bước đầu (created)
                            $maxCount = max($funnelData['steps']['created'], 1); // Tránh chia cho 0
                        @endphp
                        @foreach($funnelSteps as $index => $step)
                            @php
                                // Map step key to status for drill-down
                                $drillDownUrl = route('admin.orders.list');
                                if ($step['status']) {
                                    $drillDownUrl = route('admin.orders.list', ['status' => $step['status']]);
                                }
                            @endphp
                            <div class="funnel-step-wrapper mb-3" data-step="{{ $step['key'] }}">
                                <div class="funnel-step p-3 rounded border-start" style="border-left-width: 4px !important; border-left-color: {{ $step['color'] }} !important; cursor: pointer; transition: all 0.3s ease;" 
                                     onmouseover="this.style.backgroundColor='#f8f9fa'; this.style.transform='translateX(4px)';" 
                                     onmouseout="this.style.backgroundColor='transparent'; this.style.transform='translateX(0)';"
                                     onclick="window.location.href='{{ $drillDownUrl }}'">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <i class="bi {{ $step['icon'] }}" style="color: {{ $step['color'] }}; font-size: 1.2rem;"></i>
                                            <span class="fw-semibold">{{ $step['label'] }}</span>
                                            @if($step['rate'] !== null)
                                                <span class="badge" style="background-color: {{ $step['color'] }}20; color: {{ $step['color'] }}; font-size: 0.75rem;">
                                                    {{ $step['rate'] }}%
                                                </span>
                                            @endif
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge fs-6" style="background-color: {{ $step['color'] }}; color: white;">
                                                {{ number_format($step['count']) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="funnel-bar-container position-relative" style="height: 40px; background-color: #f1f5f9; border-radius: 6px; overflow: hidden;">
                                        <div class="funnel-bar h-100 d-flex align-items-center px-3" 
                                             style="width: {{ $maxCount > 0 ? ($step['count'] / $maxCount * 100) : 0 }}%; background: linear-gradient(90deg, {{ $step['color'] }} 0%, {{ $step['color'] }}dd 100%); transition: width 0.5s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                            @if($step['count'] > 0)
                                                <small class="text-white fw-semibold">
                                                    {{ number_format($step['count']) }} đơn
                                                    @if($step['rate'] !== null)
                                                        · {{ $step['rate'] }}% chuyển đổi
                                                    @endif
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                    @if($step['drop'] !== null && $step['drop'] > 0)
                                        <div class="mt-2">
                                            <small class="text-danger">
                                                <i class="bi bi-arrow-down-circle"></i> Rớt: {{ number_format((float)($step['drop'] ?? 0)) }} đơn
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        
                        {{-- Nhánh rớt: Đã hủy --}}
                        @if($funnelData['cancelled'] > 0)
                            <div class="mt-4 pt-3 border-top">
                                <div class="d-flex justify-content-between align-items-center mb-2 p-3 rounded" 
                                     style="cursor: pointer; transition: all 0.2s; border-left: 4px solid #EF4444; background-color: #fef2f2;" 
                                     onmouseover="this.style.backgroundColor='#fee2e2'; this.style.transform='translateX(4px)';" 
                                     onmouseout="this.style.backgroundColor='#fef2f2'; this.style.transform='translateX(0)';"
                                     onclick="window.location.href='{{ route('admin.orders.list', ['status' => 'cancelled']) }}'">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-x-circle text-danger fs-5"></i>
                                        <span class="fw-semibold text-danger">Đã hủy</span>
                                    </div>
                                    <span class="badge bg-danger fs-6">{{ number_format((float)($funnelData['cancelled'] ?? 0)) }}</span>
                                </div>
                            </div>
                        @endif
                        
                        @if($funnelData['refunded'] > 0)
                            <div class="mt-2">
                                <div class="d-flex justify-content-between align-items-center p-3 rounded" 
                                     style="cursor: pointer; transition: all 0.2s; border-left: 4px solid #8B5CF6; background-color: #f5f3ff;" 
                                     onmouseover="this.style.backgroundColor='#ede9fe'; this.style.transform='translateX(4px)';" 
                                     onmouseout="this.style.backgroundColor='#f5f3ff'; this.style.transform='translateX(0)';"
                                     onclick="window.location.href='{{ route('admin.orders.list', ['status' => 'returned']) }}'">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-arrow-counterclockwise text-secondary fs-5"></i>
                                        <span class="fw-semibold text-secondary">Yêu cầu đổi trả / Đã đổi trả</span>
                                    </div>
                                    <span class="badge bg-secondary fs-6">{{ number_format((float)($funnelData['refunded'] ?? 0)) }}</span>
                                </div>
                            </div>
                        @endif
                        
                        {{-- KPI Tỷ lệ chuyển đổi tổng --}}
                        <div class="mt-4 p-3 rounded" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Tỷ lệ chuyển đổi tổng</strong>
                                    <p class="mb-0 small opacity-75">Từ Đơn đã tạo → Hoàn tất</p>
                                </div>
                                <div class="text-end">
                                    <span class="display-6 fw-bold">{{ $funnelData['conversion']['final_conversion'] }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- KHỐI 6: PAYMENT METHOD STATISTICS --}}
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="fw-semibold mb-0">Phương thức Thanh toán</h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="chart-container mb-3" style="position: relative; height: 250px;">
                        <canvas id="paymentChart"></canvas>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Phương thức</th>
                                    <th class="text-end">Số lượng</th>
                                    <th class="text-end">Tỷ lệ</th>
                                    <th class="text-end">Tỷ lệ hủy</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paymentData as $item)
                                    <tr>
                                        <td>{{ $item['label'] }}</td>
                                        <td class="text-end fw-bold">{{ number_format((int)($item['count'] ?? 0)) }}</td>
                                        <td class="text-end">{{ $item['percentage'] }}%</td>
                                        <td class="text-end">
                                            <span class="badge bg-{{ $item['cancelled_rate'] > 10 ? 'danger' : 'success' }}">
                                                {{ $item['cancelled_rate'] }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- KHỐI 7: HEATMAP --}}
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="fw-semibold mb-0">Heatmap Đơn hàng (Giờ & Ngày)</h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="heatmap-container" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                        <table class="table table-sm table-bordered heatmap-table">
                            <thead>
                                <tr>
                                    <th>Giờ</th>
                                    <th>T2</th>
                                    <th>T3</th>
                                    <th>T4</th>
                                    <th>T5</th>
                                    <th>T6</th>
                                    <th>T7</th>
                                    <th>CN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $maxHeatmapValue = 0;
                                    foreach($heatmapData as $hour => $days) {
                                        foreach($days as $day => $count) {
                                            if($count > $maxHeatmapValue) $maxHeatmapValue = $count;
                                        }
                                    }
                                @endphp
                                @for($hour = 0; $hour < 24; $hour++)
                                    <tr>
                                        <td class="fw-semibold">{{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}:00</td>
                                        @for($day = 1; $day <= 7; $day++)
                                            @php
                                                $count = $heatmapData[$hour][$day] ?? 0;
                                                $intensity = $maxHeatmapValue > 0 ? ($count / $maxHeatmapValue) : 0;
                                                $opacity = min(0.3 + ($intensity * 0.7), 1);
                                            @endphp
                                            <td class="text-center heatmap-cell" 
                                                style="background-color: rgba(102, 126, 234, {{ $opacity }});"
                                                title="{{ $count }} đơn">
                                                @if($count > 0)
                                                    <strong>{{ $count }}</strong>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        @endfor
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KHỐI 8: DANH SÁCH ĐƠN THEO KẾT QUẢ LỌC --}}
    @if($status != 'all' || request()->has('date'))
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-semibold mb-0">Danh sách Đơn hàng</h5>
                    <a href="{{ route('admin.orders.list', array_filter(['status' => $status != 'all' ? $status : null])) }}" class="btn btn-sm btn-outline-primary">
                        Xem tất cả <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-3 p-md-4">
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-hover align-middle">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Ngày đặt</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $ordersQuery = \App\Models\Order::with('user');
                                if($status != 'all') {
                                    $ordersQuery->where('order_status', $status);
                                }
                                if($startDate) {
                                    $ordersQuery->whereDate('created_at', '>=', $startDate);
                                }
                                if($endDate) {
                                    $ordersQuery->whereDate('created_at', '<=', $endDate);
                                }
                                $filteredOrdersList = $ordersQuery->orderBy('created_at', 'desc')->take(20)->get();
                            @endphp
                            @forelse($filteredOrdersList as $order)
                                <tr>
                                    <td><span class="fw-semibold text-primary">{{ $order->order_code }}</span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-circle text-muted me-2"></i>
                                            <div>
                                                <div class="fw-medium">{{ $order->user->name ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ Str::limit($order->user->email ?? '', 25) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>{{ $order->created_at->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                    </td>
                                    <td><span class="fw-bold text-success">{{ number_format((float)($order->final_total ?? 0)) }} ₫</span></td>
                                    <td>
                                        @php
                                            $statusConfig = [
                                                'pending' => ['label' => 'Chờ xác nhận', 'color' => 'warning'],
                                                'processing' => ['label' => 'Chuẩn bị hàng', 'color' => 'info'],
                                                'shipping' => ['label' => 'Đang giao', 'color' => 'primary'],
                                                'completed' => ['label' => 'Đã giao', 'color' => 'success'],
                                                'cancelled' => ['label' => 'Đã hủy', 'color' => 'danger'],
                                                'returned' => ['label' => 'Đã đổi trả', 'color' => 'secondary'],
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusConfig[$order->order_status]['color'] ?? 'secondary' }}">
                                            {{ $statusConfig[$order->order_status]['label'] ?? ucfirst($order->order_status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i>Chi tiết
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                        <p class="text-muted mb-0">Không có đơn hàng nào</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Timeline Chart với nhiều series
    const timelineCtx = document.getElementById('timelineChart');
    const timelineData = @json($timelineData);
    
    // Tính tổng đơn mỗi ngày
    const totalOrdersData = [];
    for(let i = 0; i < timelineData.labels.length; i++) {
        let total = 0;
        Object.keys(timelineData.data).forEach(key => {
            total += timelineData.data[key][i] || 0;
        });
        totalOrdersData.push(total);
    }
    
    const timelineChart = new Chart(timelineCtx, {
        type: 'line',
        data: {
            labels: timelineData.labels,
            datasets: [
                {
                    label: 'Tổng đơn',
                    data: totalOrdersData,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    hidden: false
                },
                {
                    label: 'Hoàn thành',
                    data: timelineData.data.completed || [],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2,
                    hidden: false
                },
                {
                    label: 'Đã hủy',
                    data: timelineData.data.cancelled || [],
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2,
                    hidden: true
                },
                {
                    label: 'Đổi trả',
                    data: timelineData.data.returned || [],
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2,
                    hidden: true
                }
            ]
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
                    padding: 12,
                    titleFont: { size: 14 },
                    bodyFont: { size: 13 },
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' đơn';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Toggle series visibility
    document.querySelectorAll('.timeline-series').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const seriesMap = {
                'pending': 0,
                'completed': 1,
                'cancelled': 2,
                'returned': 3
            };
            const datasetIndex = seriesMap[this.value];
            if (datasetIndex !== undefined) {
                const meta = timelineChart.getDatasetMeta(datasetIndex);
                meta.hidden = !this.checked;
                timelineChart.update();
            }
        });
    });

    // Status Chart
    const statusCtx = document.getElementById('statusChart');
    const statusData = @json($statusData);
    
    const statusChart = new Chart(statusCtx, {
        type: 'bar',
        data: {
            labels: statusData.map(item => item.label),
            datasets: [{
                label: 'Số lượng đơn',
                data: statusData.map(item => item.count),
                backgroundColor: [
                    '#f59e0b',
                    '#3b82f6',
                    '#667eea',
                    '#10b981',
                    '#ef4444',
                    '#6b7280'
                ],
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: { size: 14 },
                    bodyFont: { size: 13 }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Payment Chart
    const paymentCtx = document.getElementById('paymentChart');
    const paymentData = @json($paymentData);
    
    const paymentChart = new Chart(paymentCtx, {
        type: 'doughnut',
        data: {
            labels: paymentData.map(item => item.label),
            datasets: [{
                data: paymentData.map(item => item.count),
                backgroundColor: [
                    '#667eea',
                    '#10b981',
                    '#f59e0b',
                    '#ef4444',
                    '#8b5cf6'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: { size: 14 },
                    bodyFont: { size: 13 },
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return `${label}: ${value} đơn (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Resize charts on window resize
    window.addEventListener('resize', function() {
        if (timelineChart) timelineChart.resize();
        if (statusChart) statusChart.resize();
        if (paymentChart) paymentChart.resize();
    });

    function toggleCustomDate() {
        const dateRange = document.getElementById('date_range').value;
        const customGroup = document.getElementById('custom_date_group');
        const customGroupEnd = document.getElementById('custom_date_group_end');
        
        if (dateRange === 'custom') {
            customGroup.style.display = 'block';
            customGroupEnd.style.display = 'block';
        } else {
            customGroup.style.display = 'none';
            customGroupEnd.style.display = 'none';
        }
    }

    function resetFilters() {
        document.getElementById('date_range').value = 'all';
        document.getElementById('status').value = 'all';
        document.getElementById('start_date').value = '';
        document.getElementById('end_date').value = '';
        toggleCustomDate();
        document.getElementById('analyticsFilterForm').submit();
    }

    // Helper function để chuyển tiếng Việt có dấu thành không dấu
    function removeVietnameseAccents(str) {
        if (!str) return '';
        str = str.toString();
        const accents = {
            'à': 'a', 'á': 'a', 'ạ': 'a', 'ả': 'a', 'ã': 'a', 'â': 'a', 'ầ': 'a', 'ấ': 'a', 'ậ': 'a', 'ẩ': 'a', 'ẫ': 'a',
            'ă': 'a', 'ằ': 'a', 'ắ': 'a', 'ặ': 'a', 'ẳ': 'a', 'ẵ': 'a',
            'è': 'e', 'é': 'e', 'ẹ': 'e', 'ẻ': 'e', 'ẽ': 'e', 'ê': 'e', 'ề': 'e', 'ế': 'e', 'ệ': 'e', 'ể': 'e', 'ễ': 'e',
            'ì': 'i', 'í': 'i', 'ị': 'i', 'ỉ': 'i', 'ĩ': 'i',
            'ò': 'o', 'ó': 'o', 'ọ': 'o', 'ỏ': 'o', 'õ': 'o', 'ô': 'o', 'ồ': 'o', 'ố': 'o', 'ộ': 'o', 'ổ': 'o', 'ỗ': 'o',
            'ơ': 'o', 'ờ': 'o', 'ớ': 'o', 'ợ': 'o', 'ở': 'o', 'ỡ': 'o',
            'ù': 'u', 'ú': 'u', 'ụ': 'u', 'ủ': 'u', 'ũ': 'u', 'ư': 'u', 'ừ': 'u', 'ứ': 'u', 'ự': 'u', 'ử': 'u', 'ữ': 'u',
            'ỳ': 'y', 'ý': 'y', 'ỵ': 'y', 'ỷ': 'y', 'ỹ': 'y',
            'đ': 'd',
            'À': 'A', 'Á': 'A', 'Ạ': 'A', 'Ả': 'A', 'Ã': 'A', 'Â': 'A', 'Ầ': 'A', 'Ấ': 'A', 'Ậ': 'A', 'Ẩ': 'A', 'Ẫ': 'A',
            'Ă': 'A', 'Ằ': 'A', 'Ắ': 'A', 'Ặ': 'A', 'Ẳ': 'A', 'Ẵ': 'A',
            'È': 'E', 'É': 'E', 'Ẹ': 'E', 'Ẻ': 'E', 'Ẽ': 'E', 'Ê': 'E', 'Ề': 'E', 'Ế': 'E', 'Ệ': 'E', 'Ể': 'E', 'Ễ': 'E',
            'Ì': 'I', 'Í': 'I', 'Ị': 'I', 'Ỉ': 'I', 'Ĩ': 'I',
            'Ò': 'O', 'Ó': 'O', 'Ọ': 'O', 'Ỏ': 'O', 'Õ': 'O', 'Ô': 'O', 'Ồ': 'O', 'Ố': 'O', 'Ộ': 'O', 'Ổ': 'O', 'Ỗ': 'O',
            'Ơ': 'O', 'Ờ': 'O', 'Ớ': 'O', 'Ợ': 'O', 'Ở': 'O', 'Ỡ': 'O',
            'Ù': 'U', 'Ú': 'U', 'Ụ': 'U', 'Ủ': 'U', 'Ũ': 'U', 'Ư': 'U', 'Ừ': 'U', 'Ứ': 'U', 'Ự': 'U', 'Ử': 'U', 'Ữ': 'U',
            'Ỳ': 'Y', 'Ý': 'Y', 'Ỵ': 'Y', 'Ỷ': 'Y', 'Ỹ': 'Y',
            'Đ': 'D'
        };
        return str.split('').map(char => accents[char] || char).join('');
    }

    function exportChart(type) {
        if (type === 'timeline') {
            const labels = timelineData.labels;
            const data = timelineData.data;
            // Sử dụng tiếng Việt không dấu cho header
            let csv = removeVietnameseAccents('Ngay,Tong don,Hoan thanh,Da huy,Doi tra\n');
            
            for(let i = 0; i < labels.length; i++) {
                const total = (data.completed?.[i] || 0) + (data.cancelled?.[i] || 0) + (data.returned?.[i] || 0) + (data.pending?.[i] || 0);
                csv += `${labels[i]},${total},${data.completed?.[i] || 0},${data.cancelled?.[i] || 0},${data.returned?.[i] || 0}\n`;
            }
            
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            // Tên file cũng không dấu
            const fileName = removeVietnameseAccents(`don-hang-${new Date().toISOString().split('T')[0]}.csv`);
            link.download = fileName;
            link.click();
        }
    }

    // Sparkline charts cho KPI cards
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.sparkline').forEach(canvas => {
            const values = JSON.parse(canvas.getAttribute('data-values') || '[]');
            if (values.length > 0) {
                const ctx = canvas.getContext('2d');
                const width = canvas.parentElement.offsetWidth;
                const height = 30;
                canvas.width = width;
                canvas.height = height;
                
                const max = Math.max(...values, 1);
                const stepX = width / (values.length - 1);
                
                ctx.strokeStyle = '#667eea';
                ctx.lineWidth = 2;
                ctx.beginPath();
                
                values.forEach((value, index) => {
                    const x = index * stepX;
                    const y = height - (value / max * height);
                    if (index === 0) {
                        ctx.moveTo(x, y);
                    } else {
                        ctx.lineTo(x, y);
                    }
                });
                ctx.stroke();
            }
        });
    });
</script>

<style>
    .analytics-kpi-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .analytics-kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
    }

    .funnel-bar {
        height: 40px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        color: white;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .funnel-step:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }

    .funnel-step:hover .funnel-bar {
        transform: scaleX(1.02);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .heatmap-cell {
        min-width: 45px;
        min-height: 35px;
        cursor: pointer;
        transition: all 0.2s ease;
        border-radius: 4px;
    }

    .heatmap-cell:hover {
        transform: scale(1.15);
        z-index: 10;
        position: relative;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .heatmap-table {
        font-size: 0.875rem;
    }

    .chart-wrapper {
        position: relative;
    }

    .sparkline-container {
        width: 100%;
        margin-top: 8px;
    }

    .sparkline {
        width: 100%;
        height: 30px;
    }

    /* Card body improvements */
    .card-body {
        transition: all 0.3s ease;
    }

    /* Chart container improvements */
    .chart-container {
        position: relative;
    }

    .chart-container canvas {
        max-width: 100%;
        height: auto !important;
    }

    /* Card body professional styling */
    .card-body {
        position: relative;
    }

    .card-body > *:first-child {
        margin-top: 0;
    }

    .card-body > *:last-child {
        margin-bottom: 0;
    }

    /* Filter form improvements */
    .card-body form .form-label {
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .card-body form .form-select,
    .card-body form .form-control {
        transition: all 0.2s ease;
    }

    .card-body form .form-select:focus,
    .card-body form .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        transform: translateY(-1px);
    }

    /* Funnel container improvements */
    .funnel-container {
        position: relative;
    }

    .funnel-step {
        position: relative;
        overflow: hidden;
    }

    .funnel-step::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .funnel-step:hover::before {
        opacity: 1;
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        /* Card body padding */
        .card-body {
            padding: 1rem !important;
        }

        .analytics-kpi-card {
            margin-bottom: 1rem;
        }

        .analytics-kpi-card .card-body {
            padding: 1rem !important;
        }

        /* Chart responsive */
        .chart-wrapper {
            height: 250px !important;
        }

        .chart-container {
            height: 200px !important;
        }

        /* Heatmap responsive */
        .heatmap-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin: 0 -1rem;
            padding: 0 1rem;
        }

        .heatmap-table {
            min-width: 600px;
        }

        /* Table responsive */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table thead.sticky-top {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        /* Timeline legend responsive */
        #timelineLegend {
            font-size: 0.875rem;
        }

        #timelineLegend label {
            margin-bottom: 0.5rem;
            font-size: 0.8rem;
        }

        /* Funnel responsive */
        .funnel-step {
            padding: 0.75rem !important;
            margin-bottom: 0.75rem !important;
        }

        .funnel-bar {
            height: 32px !important;
            font-size: 0.75rem;
        }

        .funnel-step .badge {
            font-size: 0.75rem;
        }

        /* Card header responsive */
        .card-header {
            padding: 0.75rem 1rem;
        }

        .card-header h5 {
            font-size: 1rem;
        }

        /* Filter form responsive */
        .card-body form .row {
            margin-bottom: 0.5rem;
        }

        .card-body form .form-label {
            font-size: 0.875rem;
            margin-bottom: 0.375rem;
        }

        .card-body form .btn {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }
    }

    @media (max-width: 576px) {
        /* Card body padding */
        .card-body {
            padding: 0.875rem !important;
        }

        /* Card header */
        .card-header {
            padding: 0.75rem;
        }

        .card-header h5 {
            font-size: 0.95rem;
        }

        /* Charts */
        .chart-wrapper {
            height: 200px !important;
        }

        .chart-container {
            height: 180px !important;
        }

        /* Funnel */
        .funnel-bar {
            height: 30px !important;
            font-size: 0.7rem;
        }

        .funnel-step {
            margin-bottom: 0.75rem !important;
            padding: 0.5rem !important;
        }

        .funnel-step .fw-semibold {
            font-size: 0.875rem;
        }

        .funnel-step .badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }

        /* KPI cards mobile */
        .analytics-kpi-card h3 {
            font-size: 1.5rem;
        }

        .analytics-kpi-card .fs-3 {
            font-size: 1.75rem !important;
        }

        .analytics-kpi-card p {
            font-size: 0.8rem;
        }

        /* Table improvements */
        .table {
            font-size: 0.8rem;
        }

        .table th,
        .table td {
            padding: 0.5rem 0.375rem;
        }

        /* Heatmap mobile */
        .heatmap-table {
            font-size: 0.7rem;
        }

        .heatmap-cell {
            min-width: 35px;
            min-height: 28px;
            padding: 0.25rem;
        }

        /* Filter form mobile */
        .card-body form .form-select,
        .card-body form .form-control {
            font-size: 0.875rem;
            padding: 0.5rem;
        }

        .card-body form .btn {
            font-size: 0.8rem;
            padding: 0.5rem 0.75rem;
        }

        /* Timeline legend mobile */
        #timelineLegend {
            font-size: 0.75rem;
        }

        #timelineLegend label {
            font-size: 0.75rem;
            margin-bottom: 0.375rem;
        }
    }

    /* Dark mode support */
    body.dark .analytics-kpi-card {
        background-color: #1f1f1f;
    }

    body.dark .funnel-step:hover {
        background-color: #2b2b2b;
    }

    body.dark .heatmap-cell {
        border-color: #444;
    }

    /* Animation */
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

    .analytics-kpi-card,
    .card {
        animation: fadeInUp 0.5s ease-out;
    }

    .analytics-kpi-card:nth-child(1) { animation-delay: 0.1s; }
    .analytics-kpi-card:nth-child(2) { animation-delay: 0.2s; }
    .analytics-kpi-card:nth-child(3) { animation-delay: 0.3s; }
    .analytics-kpi-card:nth-child(4) { animation-delay: 0.4s; }

    /* Text color utilities */
    .text-purple {
        color: #8b5cf6 !important;
    }

    /* Mobile carousel improvements */
    @media (max-width: 768px) {
        .carousel-control-prev,
        .carousel-control-next {
            width: 40px;
            height: 40px;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
        }

        .carousel-control-prev {
            left: 10px;
        }

        .carousel-control-next {
            right: 10px;
        }
    }

    /* Filter bar improvements */
    .card-body .form-select:focus,
    .card-body .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    /* Table improvements */
    .table-hover tbody tr {
        transition: all 0.2s ease;
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.01);
    }

    body.dark .table-hover tbody tr:hover {
        background-color: #2b2b2b;
    }

    /* Professional spacing and typography */
    .card-body h1, .card-body h2, .card-body h3, .card-body h4, .card-body h5, .card-body h6 {
        margin-top: 0;
        margin-bottom: 1rem;
    }

    .card-body p {
        margin-bottom: 1rem;
        line-height: 1.6;
    }

    .card-body .table {
        margin-bottom: 0;
    }

    /* Smooth transitions */
    .card-body * {
        transition: color 0.2s ease, background-color 0.2s ease;
    }

    /* Loading state */
    .card-body.loading {
        position: relative;
        min-height: 200px;
    }

    .card-body.loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
    }

    /* Focus states for accessibility */
    .card-body a:focus,
    .card-body button:focus {
        outline: 2px solid #667eea;
        outline-offset: 2px;
    }

    /* Print styles */
    @media print {
        .card-body {
            padding: 1rem;
            break-inside: avoid;
        }

        .chart-container {
            height: 300px !important;
        }
    }
</style>
@endsection


