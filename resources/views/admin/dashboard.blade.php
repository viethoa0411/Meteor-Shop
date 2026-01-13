@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')
@section('content')
    <div class="container-fluid py-4">

    {{-- ========== 1. HEADER DASHBOARD ========== --}}
    <section class="dashboard-section">
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
    <section class="dashboard-section">

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3">
            {{-- 🔵 1. KPI Tổng Doanh Thu (Premium Design) --}}
            <div class="col">
                <a href="{{ route('admin.revenue.filter') ?? '#' }}" class="text-decoration-none kpi-card-link">
                    <div class="card h-100 border-0 shadow-sm kpi-card-premium kpi-revenue"
                         style="background: linear-gradient(135deg, #6C47FF 0%, #3A86FF 100%); border-radius: 20px; position: relative; overflow: hidden;">
                        <div class="card-body text-white d-flex flex-column justify-content-between p-4 kpi-card-body-responsive">
                            {{-- Header với Icon --}}
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <p class="text-white-50 small mb-0" style="font-size: 0.65rem; opacity: 0.9; line-height: 1.2;">
                                        Tổng doanh thu (đã hoàn thành)
                                    </p>
                                </div>
                                <div class="kpi-icon-glow kpi-icon-revenue">
                                    <i class="bi bi-cash-coin" style="font-size: 1.3rem;"></i>
                                </div>
                            </div>

                            {{-- Main Value --}}
                            <div class="mb-2">
                                <h2 class="fw-bold mb-0 kpi-main-value">
                                        {{ number_format($totalCompletedRevenue) }} ₫
                                </h2>
                                </div>

                            {{-- Sub Info --}}
                            <div class="d-flex flex-column gap-0" style="font-size: 0.65rem;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-white-50">Tháng {{ now()->month }}/{{ now()->year }}:</span>
                                    <strong>{{ number_format($currentMonthRevenue) }} ₫</strong>
                            </div>

                            {{-- Tooltip trigger --}}
                            <div class="kpi-tooltip-trigger" data-bs-toggle="tooltip" data-bs-placement="top"
                                 title="Doanh thu đã hoàn thành từ tất cả đơn hàng có trạng thái 'completed'">
                                <i class="bi bi-info-circle" style="font-size: 0.65rem; opacity: 0.7;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                </a>
            </div>

            {{-- 🛒 2. KPI Tổng đơn hàng (Premium Design) --}}
            <div class="col">
                <a href="{{ route('admin.orders.list') }}" class="text-decoration-none kpi-card-link">
                    <div class="card h-100 shadow-sm border-0 kpi-card-premium kpi-orders"
                         style="border-radius: 20px; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); position: relative; overflow: hidden;">
                        <div class="card-body text-white d-flex flex-column justify-content-between p-4 kpi-card-body-responsive">
                            {{-- Header với Icon Circle Glow --}}
                            <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                    <p class="text-white small mb-0" style="font-size: 0.65rem; opacity: 0.95; line-height: 1.2;">
                                        Tổng đơn hàng (tháng này)
                                    </p>
                            </div>
                                <div class="kpi-icon-circle-glow kpi-icon-orders">
                                    <i class="bi bi-cart-check-fill" style="font-size: 1.2rem;"></i>
                                </div>
                            </div>

                            {{-- Main Value --}}
                            <div class="mb-2">
                                <h2 class="fw-bold mb-0 kpi-main-value text-white" style="font-size: 1.5rem; line-height: 1.2;">
                                    {{ number_format($totalOrders) }}
                                </h2>
                            </div>

                            {{-- Sub Info --}}
                            <div class="d-flex flex-column gap-0" style="font-size: 0.65rem;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-white" style="opacity: 0.9;">Tất cả:</span>
                                    <strong class="text-white">{{ number_format($totalAllOrders) }} đơn</strong>
                                </div>
                                @if(isset($orderGrowth))
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-white" style="opacity: 0.9;">Thay đổi:</span>
                                    <span class="badge bg-white text-success" style="font-size: 0.6rem; font-weight: 600; padding: 0.2rem 0.4rem;">
                                        {{ $orderGrowth > 0 ? '+' : '' }}{{ $orderGrowth }}% so với tháng trước
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- 👥 3. KPI Người dùng hệ thống (Premium Design) --}}
            <div class="col">
                <a href="{{ route('admin.account.users.list') }}" class="text-decoration-none kpi-card-link">
                    <div class="card h-100 shadow-sm border-0 kpi-card-premium kpi-users"
                         style="border-radius: 20px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); position: relative; overflow: hidden;">
                        <div class="card-body text-white d-flex flex-column justify-content-between p-4 kpi-card-body-responsive">
                            {{-- Header với Icon Animation --}}
                            <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                    <p class="text-white small mb-0" style="font-size: 0.65rem; opacity: 0.95; line-height: 1.2;">
                                        Người dùng hệ thống
                                    </p>
                                </div>
                                <div class="kpi-icon-animated kpi-icon-users">
                                    <i class="bi bi-people-fill" style="font-size: 1.2rem;"></i>
                                </div>
                            </div>

                            {{-- Main Value --}}
                            <div class="mb-2">
                                <h2 class="fw-bold mb-0 kpi-main-value text-white" style="font-size: 1.5rem; line-height: 1.2;">
                                    {{ number_format($totalUsers) }}
                                </h2>
                            </div>

                            {{-- Sub Info với Badge Gradient --}}
                            <div class="d-flex flex-column gap-0" style="font-size: 0.65rem;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-white" style="opacity: 0.9;">Tháng {{ now()->month }}/{{ now()->year }}:</span>
                                    <strong class="text-white">+{{ number_format($thisMonthUsers) }} user</strong>
                                </div>
                                    @if($userGrowth !== null)
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-white" style="opacity: 0.9;">Thay đổi:</span>
                                    <span class="badge bg-white {{ $userGrowth > 0 ? 'text-success' : ($userGrowth < 0 ? 'text-danger' : 'text-secondary') }}"
                                          style="font-size: 0.6rem; font-weight: 600; padding: 0.2rem 0.4rem;"
                                          data-bs-toggle="tooltip"
                                          data-bs-placement="top"
                                          title="Người dùng mới trong tháng này">
                                            {{ $userGrowth > 0 ? '+' : '' }}{{ $userGrowth }}% so với tháng trước
                                        </span>
                                </div>
                                    @endif
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- 📦 4. KPI Sản phẩm (Premium Design) --}}
            <div class="col">
                <a href="{{ route('admin.products.list') }}" class="text-decoration-none kpi-card-link">
                    <div class="card h-100 shadow-sm border-0 kpi-card-premium kpi-products"
                         style="border-radius: 20px; background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); position: relative; overflow: hidden;">
                        <div class="card-body text-white d-flex flex-column justify-content-between p-4 kpi-card-body-responsive">
                            {{-- Header với Icon Neon --}}
                            <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                    <p class="text-white small mb-0" style="font-size: 0.65rem; opacity: 0.95; line-height: 1.2;">
                                        Sản phẩm
                                    </p>
                                </div>
                                <div class="kpi-icon-neon kpi-icon-products">
                                    <i class="bi bi-box-seam" style="font-size: 1.2rem;"></i>
                                </div>
                            </div>

                            {{-- Main Value --}}
                            <div class="mb-2">
                                <h2 class="fw-bold mb-0 kpi-main-value text-white" style="font-size: 1.5rem; line-height: 1.2;">
                                    {{ number_format($totalProducts) }}
                                </h2>
                            </div>

                            {{-- Sub Info với Badge Xanh --}}
                            <div class="d-flex flex-column gap-0" style="font-size: 0.65rem;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-white text-info" style="font-size: 0.6rem; font-weight: 600; padding: 0.2rem 0.4rem;">
                                        Bán 30 ngày qua
                                    </span>
                                    <strong class="text-white">{{ number_format($soldProductsLast30Days) }} sản phẩm</strong>
                                </div>
                                @php
                                    $soldPercentage = $totalProducts > 0 ? round(($soldProductsLast30Days / $totalProducts) * 100, 1) : 0;
                                @endphp
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-white" style="opacity: 0.9;">% sản phẩm bán được:</span>
                                    <strong class="text-white">{{ $soldPercentage }}%</strong>
                            </div>

                            </div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- ✔ 5. KPI Tỷ lệ hoàn thành đơn (Premium Design với Progress Donut) --}}

            <div class="col">
                @php
                    $completed = $orderStatsByStatus['completed'] ?? 0;
                    // $orderStatsByStatus là Collection, cần sum() thay vì array_sum
                    $totalForRate = $orderStatsByStatus instanceof \Illuminate\Support\Collection
                        ? $orderStatsByStatus->sum()
                        : (is_array($orderStatsByStatus) ? array_sum($orderStatsByStatus) : 0);
                    $completeRate = $totalForRate > 0 ? round(($completed / $totalForRate) * 100, 1) : 0;
                @endphp
                <a href="{{ route('admin.orders.list', ['status' => 'completed']) }}" class="text-decoration-none kpi-card-link">
                    <div class="card h-100 shadow-sm border-0 kpi-card-premium kpi-completion"
                         style="border-radius: 20px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); position: relative; overflow: hidden;">
                        <div class="card-body text-white d-flex flex-column justify-content-between p-4 kpi-card-body-responsive">
                            {{-- Header với Icon Check trong Circle --}}
                            <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                    <p class="text-white small mb-0" style="font-size: 0.65rem; opacity: 0.95; line-height: 1.2;">
                                        Tỷ lệ hoàn thành đơn
                                    </p>
                            </div>
                                <div class="kpi-icon-check-circle kpi-icon-completion">
                                    <i class="bi bi-check-circle-fill" style="font-size: 1.2rem;"></i>
                                </div>
                            </div>

                            {{-- Main Value với Progress Donut Mini --}}
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="kpi-donut-mini" style="width: 45px; height: 45px; position: relative;">
                                    <svg width="45" height="45" style="transform: rotate(-90deg);">
                                        <circle cx="22.5" cy="22.5" r="18" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="4"></circle>
                                        <circle cx="22.5" cy="22.5" r="18" fill="none" stroke="#fff" stroke-width="4"
                                                stroke-dasharray="{{ 2 * 3.14159 * 18 }}"
                                                stroke-dashoffset="{{ 2 * 3.14159 * 18 * (1 - $completeRate / 100) }}"
                                                style="transition: stroke-dashoffset 0.5s ease;"></circle>
                                    </svg>
                                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 0.6rem; font-weight: bold; color: #fff;">
                                        {{ $completeRate }}%
                                    </div>
                                </div>
                                <div>
                                    <h2 class="fw-bold mb-0 kpi-main-value text-white" style="font-size: 1.5rem; line-height: 1.2;">
                                        {{ $completeRate }}%
                                    </h2>
                                </div>
                            </div>

                            {{-- Sub Info --}}
                            <div class="d-flex flex-column gap-0" style="font-size: 0.65rem;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-white" style="opacity: 0.9;">Hoàn thành:</span>
                                    <strong class="text-white">{{ $completed }} đơn</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-white" style="opacity: 0.9;">Tổng đơn:</span>
                                    <strong class="text-white">{{ $totalForRate }} đơn</strong>
                                </div>

                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    {{-- ========== 2.5. QUICK ACTIONS SECTION ========== --}}
    <section class="dashboard-section">
        <div class="card shadow-sm border-0 quick-actions-wrapper">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4 quick-actions-title" style="font-size: 1.1rem;">Thao tác nhanh</h5>
                <div class="row g-3">
                    {{-- 1. Tạo sản phẩm --}}
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <a href="{{ route('admin.products.create') }}"
                           class="quick-action-card text-decoration-none"
                           data-bs-toggle="tooltip"
                           data-bs-placement="top"
                           title="Thêm sản phẩm mới vào cửa hàng">
                            <div class="quick-action-icon quick-action-blue">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div class="quick-action-title">Tạo sản phẩm</div>
                            <div class="quick-action-subtitle">Thêm sản phẩm mới</div>
                        </a>
                    </div>

                    {{-- 2. Tạo danh mục --}}
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <a href="{{ route('admin.categories.create') }}"
                           class="quick-action-card text-decoration-none"
                           data-bs-toggle="tooltip"
                           data-bs-placement="top"
                           title="Tạo danh mục mới">
                            <div class="quick-action-icon quick-action-purple">
                                <i class="bi bi-folder-plus"></i>
                            </div>
                            <div class="quick-action-title">Tạo danh mục</div>
                            <div class="quick-action-subtitle">Thêm danh mục mới</div>
                        </a>
                    </div>

                    {{-- 3. Thêm người dùng --}}
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <a href="{{ route('admin.account.users.create') }}"
                           class="quick-action-card text-decoration-none"
                           data-bs-toggle="tooltip"
                           data-bs-placement="top"
                           title="Thêm tài khoản người dùng hệ thống">
                            <div class="quick-action-icon quick-action-yellow">
                                <i class="bi bi-person-plus-fill"></i>
                            </div>
                            <div class="quick-action-title">Thêm người dùng</div>
                            <div class="quick-action-subtitle">Thêm user mới</div>
                        </a>
                    </div>

                    {{-- 4. Quản lý bình luận --}}
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <a href="{{ route('admin.comments.index') }}"
                           class="quick-action-card text-decoration-none"
                           data-bs-toggle="tooltip"
                           data-bs-placement="top"
                           title="Xem và duyệt bình luận">
                            <div class="quick-action-icon quick-action-cyan">
                                <i class="bi bi-chat-dots-fill"></i>
                            </div>
                            <div class="quick-action-title">Quản lý bình luận</div>
                            <div class="quick-action-subtitle">Duyệt / xóa bình luận</div>
                        </a>
                    </div>

                    {{-- 5. Quản lý voucher --}}
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <a href="{{ route('admin.promotions.list') }}"
                           class="quick-action-card text-decoration-none"
                           data-bs-toggle="tooltip"
                           data-bs-placement="top"
                           title="Tạo và quản lý mã giảm giá">
                            <div class="quick-action-icon quick-action-pink">
                                <i class="bi bi-ticket-perforated-fill"></i>
                            </div>
                            <div class="quick-action-title">Quản lý voucher</div>
                            <div class="quick-action-subtitle">Mã giảm giá</div>
                        </a>
                    </div>

                    {{-- 6. Quản lý ví --}}
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <a href="{{ route('admin.wallet.index') }}"
                           class="quick-action-card text-decoration-none"
                           data-bs-toggle="tooltip"
                           data-bs-placement="top"
                           title="Quản lý ví người dùng / tiền hoàn">
                            <div class="quick-action-icon quick-action-green">
                                <i class="bi bi-wallet-fill"></i>
                            </div>
                            <div class="quick-action-title">Quản lý ví</div>
                            <div class="quick-action-subtitle">Ví người dùng</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========== 3. CHARTS & ANALYTICS AREA ========== --}}
    <section class="dashboard-section">
        <div class="row g-3">
            {{-- Combined Chart: Doanh thu & Đơn hàng theo thời gian --}}
            <div class="col-12 col-lg-8">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                        <div>
                                <h6 class="fw-semibold mb-0">Doanh thu & Đơn hàng theo thời gian</h6>
                                <small class="text-muted">Biểu đồ kết hợp doanh thu (line) và số lượng đơn hàng (bar)</small>
                        </div>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2 revenue-orders-filters">
                            {{-- Date Range Picker --}}
                            <div class="btn-group btn-group-sm revenue-orders-btn-group" role="group" aria-label="Date range">
                                <button type="button" class="btn btn-outline-primary" data-range="today">Hôm nay</button>
                                <button type="button" class="btn btn-outline-primary" data-range="7">7 ngày</button>
                                <button type="button" class="btn btn-outline-primary active" data-range="30">30 ngày</button>
                                <button type="button" class="btn btn-outline-primary" data-range="90">90 ngày</button>
                                <button type="button" class="btn btn-outline-primary" data-range="month">Tháng này</button>
                                <button type="button" class="btn btn-outline-secondary" data-range="custom">Tùy chọn</button>
                            </div>
                            {{-- Granularity Dropdown --}}
                            <div class="d-flex align-items-center gap-2">
                                <label for="revenueOrdersGroupBy" class="small text-muted mb-0">Step:</label>
                                <select id="revenueOrdersGroupBy" class="form-select form-select-sm revenue-orders-select">
                                    <option value="day" selected>Ngày</option>
                                    <option value="week">Tuần</option>
                                    <option value="month">Tháng</option>
                                </select>
                            </div>
                            {{-- Custom Date Range --}}
                            <div class="d-flex align-items-center gap-1 revenue-orders-custom-range custom-date-range-picker" style="display:none; visibility: hidden;">
                                <input type="date" id="revenueOrdersFrom" class="form-control form-control-sm custom-date-input" placeholder="Từ ngày">
                                <span class="small text-muted custom-date-separator">→</span>
                                <input type="date" id="revenueOrdersTo" class="form-control form-control-sm custom-date-input" placeholder="Đến ngày">
                                <button type="button" class="btn btn-sm btn-primary" id="revenueOrdersApply">
                                    Áp dụng
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="revenueOrdersChart" class="chart-responsive"></div>
                    </div>
                    {{-- Footer Summary --}}
                    <div class="card-footer bg-white border-top">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-cash-coin text-primary fs-5"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <small class="text-muted d-block">Tổng doanh thu</small>
                                        <div class="d-flex align-items-center gap-2">
                                            <strong class="mb-0" id="summaryTotalRevenue">0 ₫</strong>
                                            <span class="badge" id="summaryRevenueChange">0%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-cart-check text-success fs-5"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <small class="text-muted d-block">Tổng đơn hàng</small>
                                        <div class="d-flex align-items-center gap-2">
                                            <strong class="mb-0" id="summaryTotalOrders">0 đơn</strong>
                                            <span class="badge" id="summaryOrdersChange">0%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cột phải: Trạng thái đơn hàng + Top 5 sản phẩm bán chạy --}}
            <div class="col-12 col-lg-4">
                {{-- Tỷ lệ trạng thái đơn hàng --}}
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                        <div>
                            <h6 class="fw-semibold mb-0">Tỷ lệ trạng thái đơn hàng</h6>
                                <small class="text-muted" id="orderStatusDateRange">(30 ngày gần nhất)</small>
                            </div>
                            <div class="btn-group btn-group-sm order-status-btn-group" role="group" aria-label="Status date range">
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-status-range="today">Hôm nay</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-status-range="7">7 ngày</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm active" data-status-range="30">30 ngày</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-status-range="month">Tháng này</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            {{-- Donut Chart --}}
                            <div class="col-12 col-md-5">
                                <div style="height: 200px; position: relative;">
                            <canvas id="orderStatusChart"></canvas>
                        </div>
                            </div>
                            {{-- Bảng trạng thái --}}
                            <div class="col-12 col-md-7">
                                <div class="order-status-table-wrapper">
                                    <table class="table table-sm table-hover mb-0 order-status-table" id="orderStatusTable">
                                        <thead>
                                            <tr>
                                                <th style="width: 20px; min-width: 20px;"></th>
                                                <th style="min-width: 100px;">Trạng thái</th>
                                                <th class="text-end" style="width: 50px; min-width: 50px;">SL</th>
                                                <th class="text-end" style="width: 60px; min-width: 60px;">Tỷ lệ</th>
                                                <th class="text-end" style="width: 70px; min-width: 70px;">Xu hướng</th>
                                            </tr>
                                        </thead>
                                        <tbody id="orderStatusTableBody">
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3">
                                                    <div class="spinner-border spinner-border-sm" role="status"></div>
                                                    <span class="ms-2">Đang tải...</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top">
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i>
                            Tổng số đơn: <strong id="orderStatusTotal">0</strong> đơn |
                            Khoảng thời gian: <span id="orderStatusPeriod">-</span>
                        </small>
                    </div>
                </div>

                {{-- Thống kê tồn kho --}}
                <div class="card shadow-sm">
                    <div class="card-header bg-white" style="overflow: hidden;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2" style="width: 100%;">
                            <h6 class="fw-semibold mb-0" style="flex: 0 0 auto;">Thống kê tồn kho</h6>
                            <div class="d-flex gap-2 align-items-center inventory-filter-wrapper" style="flex: 1 1 auto; min-width: 0; flex-wrap: nowrap; max-width: 100%;">
                                {{-- Search --}}
                                <div class="input-group input-group-sm table-search-input" style="flex: 1 1 auto; min-width: 0; max-width: 100%;">
                                    <input type="text" class="form-control" id="inventorySearch" placeholder="Tìm kiếm sản phẩm..." style="min-width: 0;">
                                </div>
                                {{-- Filter trạng thái tồn kho --}}
                                <select class="form-select form-select-sm table-filter-select" id="inventoryStatusFilter" style="flex: 0 0 auto; min-width: 120px; max-width: 180px; white-space: nowrap;">
                                    <option value="">Tất cả</option>
                                    <option value="in_stock">🟢 Còn hàng</option>
                                    <option value="low">🟡 Sắp hết</option>
                                    <option value="very_low">🔴 Hết hàng / Cực thấp</option>
                                    <option value="out_of_stock">🔥 Hết hàng</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        {{-- Bảng dữ liệu --}}
                        <div class="inventory-table-wrapper">
                            <table class="table table-sm table-hover mb-0" id="inventoryTable">
                                <thead style="position: sticky; top: 0; background: white; z-index: 10;">
                                    <tr>
                                        <th style="min-width: 300px;">Sản phẩm</th>
                                        <th style="min-width: 120px;" class="text-center">Tồn kho</th>
                                        <th style="min-width: 180px;" class="text-center">Trạng thái tồn</th>
                                        <th style="min-width: 150px;" class="text-center">Số lượng đã bán</th>
                                        <th style="min-width: 150px;" class="text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody id="inventoryTableBody">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <div class="spinner-border spinner-border-sm" role="status"></div>
                                            <span class="ms-2">Đang tải...</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                                    </div>
                        {{-- Footer --}}
                        <div class="card-footer bg-white border-top" style="padding: 12px 20px;">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div class="text-muted">
                                    <small>
                                        Tổng số sản phẩm: <strong id="inventoryTotal">0</strong>
                                    </small>
                                </div>
                                <div class="text-muted">
                                    <small id="inventoryFilterInfo">
                                        <i class="bi bi-info-circle"></i> Vuốt chuột lên xuống để xem tất cả sản phẩm
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========== 4. CATEGORY REVENUE & TOP CUSTOMERS SECTION (1:1) ========== --}}
    <section class="dashboard-section">
        <div class="row g-3">
            {{-- Card A: Biểu đồ Doanh thu theo Danh mục --}}
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm category-revenue-card category-revenue-card-responsive" style="border-radius: 16px;">
                    <div class="card-header bg-white card-header-responsive" style="border-bottom: 1px solid #e5e7eb;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <h6 class="fw-semibold mb-0">Doanh thu theo danh mục</h6>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Category date range">
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-category-range="7">7d</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm active" data-category-range="30">30d</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-category-range="90">90d</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-category-range="custom">Tùy chọn</button>
                            </div>
                        </div>
                        {{-- Custom date range picker (ẩn mặc định) --}}
                        <div id="categoryDateRangePicker" class="mt-3 custom-date-range-picker" style="display: none;">
                            <div class="d-flex gap-2 align-items-end custom-date-range-wrapper">
                                <div class="flex-grow-1">
                                    <label class="form-label small mb-1">Từ ngày</label>
                                    <input type="date" class="form-control form-control-sm custom-date-input" id="categoryDateFrom">
                                </div>
                                <div class="flex-grow-1">
                                    <label class="form-label small mb-1">Đến ngày</label>
                                    <input type="date" class="form-control form-control-sm custom-date-input" id="categoryDateTo">
                                </div>
                                <button type="button" class="btn btn-primary btn-sm" id="categoryDateRangeApply">Áp dụng</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="padding: 20px 24px; height: calc(100% - 80px); display: flex; flex-direction: column;">
                        {{-- Biểu đồ Bar Chart ngang --}}
                        <div class="flex-grow-1" style="min-height: 0;">
                            <canvas id="categoryRevenueChart"></canvas>
                        </div>
                        {{-- Footer Summary --}}
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <small class="text-muted d-block">Tổng doanh thu dự tính</small>
                                    <strong class="text-primary" id="categoryTotalRevenueEstimated">0 ₫</strong>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Tổng doanh thu thực tế</small>
                                    <strong class="text-success" id="categoryTotalRevenueActual">0 ₫</strong>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block">Danh mục bán chạy nhất</small>
                                    <div>
                                        <strong class="text-primary" id="categoryTopCategoryEstimated">-</strong>
                                        <br>
                                        <strong class="text-success" id="categoryTopCategoryActual">-</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card B: Bảng Top người dùng mua nhiều nhất --}}
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm top-customers-card top-customers-card-responsive" style="border-radius: 16px;">
                    <div class="card-header bg-white card-header-responsive" style="border-bottom: 1px solid #e5e7eb;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <h6 class="fw-semibold mb-0">Top khách hàng mua nhiều nhất</h6>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Customers date range">
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-customers-range="7">7d</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm active" data-customers-range="30">30d</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-customers-range="90">90d</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-customers-range="custom">Tùy chọn</button>
                            </div>
                        </div>
                        {{-- Custom date range picker (ẩn mặc định) --}}
                        <div id="customersDateRangePicker" class="mt-3 custom-date-range-picker" style="display: none;">
                            <div class="d-flex gap-2 align-items-end custom-date-range-wrapper">
                                <div class="flex-grow-1">
                                    <label class="form-label small mb-1">Từ ngày</label>
                                    <input type="date" class="form-control form-control-sm custom-date-input" id="customersDateFrom">
                                </div>
                                <div class="flex-grow-1">
                                    <label class="form-label small mb-1">Đến ngày</label>
                                    <input type="date" class="form-control form-control-sm custom-date-input" id="customersDateTo">
                                </div>
                                <button type="button" class="btn btn-primary btn-sm" id="customersDateRangeApply">Áp dụng</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0" style="height: calc(100% - 80px); display: flex; flex-direction: column;">
                        {{-- Bảng dữ liệu --}}
                        <div class="top-customers-table-wrapper" style="flex: 1; overflow-y: auto; overflow-x: hidden;">
                            <table class="table table-sm table-hover mb-0" id="topCustomersTable">
                                <thead style="position: sticky; top: 0; background: white; z-index: 10;">
                                    <tr>
                                        <th style="min-width: 200px;">Khách hàng</th>
                                        <th class="text-center" style="min-width: 80px;">Số đơn</th>
                                        <th class="text-end" style="min-width: 120px;">Tổng tiền</th>
                                        <th class="text-end" style="min-width: 80px;">Tỉ lệ (%)</th>
                                    </tr>
                                </thead>
                                <tbody id="topCustomersTableBody">
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <div class="spinner-border spinner-border-sm" role="status"></div>
                                            <span class="ms-2">Đang tải...</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        {{-- Footer --}}
                        <div class="border-top p-3 bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    Tổng số khách hàng: <strong id="customersTotalCount">0</strong> người
                                </small>
                                <small class="text-muted" id="customersDateRangeText">Lọc theo: 30 ngày gần nhất</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========== 5. TOP PRODUCTS SECTION ========== --}}
    <section class="dashboard-section">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm" style="border-radius: 16px;">
                    <div class="card-header bg-white card-header-responsive" style="border-bottom: 1px solid #e5e7eb;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <h6 class="fw-semibold mb-0">Top sản phẩm bán chạy</h6>
                            <div class="d-flex gap-2 align-items-center">
                                {{-- Date Range Picker --}}
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary" data-range="7">7 ngày</button>
                                    <button type="button" class="btn btn-outline-primary" data-range="30">30 ngày</button>
                                    <button type="button" class="btn btn-outline-primary" data-range="90">90 ngày</button>
                                    <button type="button" class="btn btn-outline-primary" id="topProductsCustomRangeBtn">Tùy chọn</button>
                                </div>
                                {{-- Custom Date Range (ẩn mặc định) --}}
                                <div id="topProductsDateRangeGroup" class="d-none d-flex align-items-center gap-2 custom-date-range-picker">
                                    <input type="date" class="form-control form-control-sm custom-date-input" id="topProductsDateFrom">
                                    <span class="text-muted custom-date-separator">đến</span>
                                    <input type="date" class="form-control form-control-sm custom-date-input" id="topProductsDateTo">
                                    <button type="button" class="btn btn-sm btn-primary" id="topProductsDateRangeApply">Áp dụng</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        {{-- Bảng dữ liệu --}}
                        <div class="top-products-table-wrapper">
                            <table class="table table-sm table-hover mb-0 top-products-table-compact" id="topProductsTable">
                                <thead style="position: sticky; top: 0; background: white; z-index: 10;">
                                    <tr>
                                        <th class="top-products-th-stt" style="vertical-align: middle; width: 50px;">STT</th>
                                        <th class="top-products-th-image" style="vertical-align: middle;">Hình ảnh</th>
                                        <th class="top-products-th-name" style="vertical-align: middle;">Tên sản phẩm & danh mục</th>
                                        <th class="text-end top-products-th-revenue" style="vertical-align: middle;">Doanh thu</th>
                                        <th class="text-center top-products-th-stock" style="vertical-align: middle;">Tồn kho</th>
                                    </tr>
                                </thead>
                                <tbody id="topProductsTableBody">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <div class="spinner-border spinner-border-sm" role="status"></div>
                                            <span class="ms-2">Đang tải...</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        {{-- Footer --}}
                        <div class="border-top p-3 bg-light">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div class="text-muted">
                                    <small id="topProductsTotalRevenue">
                                        <div>Dự tính: <strong>0 đ</strong></div>
                                        <div>Thực tế: <strong>0 đ</strong></div>
                                    </small>
                                </div>
                                <small class="text-muted" id="topProductsDateRangeText">
                                    Lọc theo: 30 ngày gần nhất
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <style>
        /* ========== RESPONSIVE FOUNDATION - PROFESSIONAL SYSTEM ========== */

        /* Root Variables for Responsive Design */
        :root {
            --base-font-size: 16px;
            --scale-factor: 1;
            --container-padding: clamp(12px, 2vw, 24px);
            --section-spacing: clamp(1rem, 2vw, 2rem);
            --card-padding: clamp(12px, 1.5vw, 24px);
            --border-radius: clamp(12px, 1.5vw, 20px);
            --kpi-card-min-height: 150px;
            --chart-min-height: clamp(250px, 40vw, 380px);
        }

        /* Fluid Typography System */
        .kpi-main-value {
            font-size: clamp(1rem, 2vw + 0.3rem, 1.5rem) !important;
        }

        .card-header-responsive h6 {
            font-size: clamp(0.9rem, 1.2vw + 0.5rem, 1.1rem);
        }

        .card-header-responsive small {
            font-size: clamp(0.7rem, 0.8vw + 0.3rem, 0.875rem);
        }

        /* Fluid Spacing */
        .dashboard-section {
            margin-bottom: var(--section-spacing);
        }

        .card-header-responsive {
            padding: var(--card-padding) !important;
        }

        /* Zoom-Aware Responsive Design */
        @media (min-resolution: 1.5dppx) {
            /* High DPI displays */
            :root {
                --base-font-size: 15px;
            }
        }

        @media (min-resolution: 2dppx) {
            /* Retina displays */
            :root {
                --base-font-size: 14px;
            }
        }

        /* Container Queries Support (Progressive Enhancement) */
        /* Note: Container queries are experimental, using media queries as fallback */
        .kpi-cards-container {
            /* Future: container-type: inline-size; */
        }

        /* Comprehensive Breakpoint System */

        /* Extra Extra Small (xxs) - < 320px - Very small phones */
        @media (max-width: 319.98px) {
            :root {
                --container-padding: 8px;
                --section-spacing: 0.75rem;
                --card-padding: 10px;
            }

            .kpi-main-value {
                font-size: 0.9rem !important;
            }

            .kpi-card-premium .card-body {
                padding: 0.75rem !important;
                min-height: 130px !important;
                max-height: 130px !important;
            }

            .card-header-responsive {
                padding: 10px !important;
            }

            .btn-group-sm .btn {
                font-size: 0.55rem;
                padding: 0.1rem 0.2rem;
            }

            #revenueOrdersChart {
                min-height: 200px !important;
            }
        }

        /* Extra Small (xs) - 320px - 374px - Small phones */
        @media (min-width: 320px) and (max-width: 374.98px) {
            :root {
                --container-padding: 10px;
                --section-spacing: 0.875rem;
                --card-padding: 12px;
            }
        }

        /* Small (sm) - 375px - 575px - Phones */
        @media (min-width: 375px) and (max-width: 575.98px) {
            :root {
                --container-padding: 12px;
                --section-spacing: 1rem;
                --card-padding: 14px;
            }
        }

        /* Medium (md) - 576px - 767px - Large phones / Small tablets portrait */
        @media (min-width: 576px) and (max-width: 767.98px) {
            :root {
                --container-padding: 16px;
                --section-spacing: 1.25rem;
                --card-padding: 18px;
            }
        }

        /* Large (lg) - 768px - 991px - Tablets */
        @media (min-width: 768px) and (max-width: 991.98px) {
            :root {
                --container-padding: 20px;
                --section-spacing: 1.5rem;
                --card-padding: 20px;
            }
        }

        /* Extra Large (xl) - 992px - 1199px - Small desktops */
        @media (min-width: 992px) and (max-width: 1199.98px) {
            :root {
                --container-padding: 22px;
                --section-spacing: 1.75rem;
                --card-padding: 22px;
            }
        }

        /* Extra Extra Large (xxl) - >= 1200px - Large desktops */
        @media (min-width: 1200px) {
            :root {
                --container-padding: 24px;
                --section-spacing: 2rem;
                --card-padding: 24px;
            }
        }

        /* Ultra Wide - >= 1400px - Ultra wide monitors */
        @media (min-width: 1400px) {
            :root {
                --container-padding: 28px;
                --section-spacing: 2.25rem;
                --card-padding: 28px;
            }

            .container-fluid,
            main {
                max-width: 1600px;
                margin: 0 auto;
            }
        }

        /* Zoom Level Responsive Design */

        /* Zoom Out Scenarios */
        @media (min-width: 1400px) {
            /* Large screens with zoom out */
            .kpi-card-premium .card-body {
                min-height: 150px;
                max-height: 150px;
            }

            #revenueOrdersChart {
                min-height: 420px;
            }
        }

        /* Zoom In Scenarios - Smaller effective viewport */
        @media (max-width: 991.98px) {
            /* When zoomed in, ensure content doesn't break */
            .kpi-card-premium {
                min-width: 0;
                flex: 1 1 auto;
            }

            .table {
                font-size: clamp(0.75rem, 1vw + 0.5rem, 0.875rem);
            }
        }

        /* Height-based Media Queries for Landscape */
        @media (max-height: 600px) and (orientation: landscape) {
            .category-revenue-card-responsive,
            .top-customers-card-responsive {
                height: auto !important;
                min-height: 280px;
            }

            #revenueOrdersChart {
                min-height: 220px !important;
            }

            .comments-table-wrapper,
            .users-table-wrapper {
                max-height: 200px;
                height: 200px;
            }
        }

        @media (max-height: 500px) and (orientation: landscape) {
            .kpi-card-premium .card-body {
                min-height: 100px !important;
                padding: 0.75rem !important;
            }

            .card-header-responsive {
                padding: 10px !important;
            }
        }

        /* Aspect Ratio Based Queries */
        @media (aspect-ratio: 16/9) {
            /* Wide screens */
            .row-cols-xl-5 > * {
                flex: 0 0 auto;
                width: 20%;
            }
        }

        @media (aspect-ratio: 4/3) {
            /* Traditional monitors */
            .row-cols-lg-4 > * {
                flex: 0 0 auto;
                width: 25%;
            }
        }

        /* Touch Device Optimizations */
        @media (hover: none) and (pointer: coarse) {
            /* Touch devices */
            .btn,
            .form-control,
            .form-select,
            .dropdown-toggle {
                min-height: 44px;
                min-width: 44px;
            }

            .btn-sm {
                min-height: 36px;
            }

            .kpi-card-premium:hover {
                transform: none;
            }

            /* Larger touch targets */
            .table th,
            .table td {
                padding: 0.75rem 0.5rem;
            }
        }

        /* Mouse Device Optimizations */
        @media (hover: hover) and (pointer: fine) {
            /* Mouse devices - enable hover effects */
            .kpi-card-premium:hover {
                transform: translateY(-5px) scale(1.02);
            }
        }

        /* Reduced Motion */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }

        /* High Contrast Mode */
        @media (prefers-contrast: high) {
            .kpi-card-premium {
                border: 2px solid currentColor;
            }

            .btn-outline-primary,
            .btn-outline-secondary {
                border-width: 2px;
            }

            .table {
                border: 1px solid;
            }
        }

        /* Dark Mode with Reduced Motion */
        @media (prefers-color-scheme: dark) and (prefers-reduced-motion: reduce) {
            body.dark .kpi-card-premium {
                transition: none;
            }
        }

        /* Print Optimizations */
        @media print {
            * {
                background: white !important;
                color: black !important;
                box-shadow: none !important;
            }

            .kpi-card-premium {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .card {
                border: 1px solid #000;
            }

            .btn-group,
            .dropdown,
            .table-search-input,
            .table-filter-select {
                display: none;
            }
        }

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

        /* ========== KPI CARDS PREMIUM DESIGN ========== */
        .kpi-card-link {
            text-decoration: none !important;
            color: inherit;
        }

        .kpi-card-premium {
            border-radius: 20px !important;
            transition: transform 0.25s ease-out, box-shadow 0.25s ease-out;
            position: relative;
            overflow: hidden;
        }

        .kpi-card-premium::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
            opacity: 0;
            transition: opacity 0.25s ease-out;
            pointer-events: none;
        }

        .kpi-card-premium:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2) !important;
        }

        .kpi-card-premium:hover::before {
            opacity: 1;
        }

        /* Main Value Styling - Using Fluid Typography */
        .kpi-main-value {
            font-size: clamp(1rem, 2vw + 0.3rem, 1.5rem);
            line-height: 1.2;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        /* ========== KPI CARD BODY RESPONSIVE ========== */
        .kpi-card-body-responsive {
            min-height: var(--kpi-card-min-height);
            max-height: var(--kpi-card-min-height);
            height: var(--kpi-card-min-height);
            padding: 1rem 1.25rem !important;
            overflow: hidden;
        }

        /* ========== ICON STYLES ========== */
        /* Icon Glow cho Revenue - Fluid Sizing */
        .kpi-icon-glow {
            width: clamp(28px, 3vw + 15px, 38px);
            height: clamp(28px, 3vw + 15px, 38px);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .kpi-icon-glow i {
            font-size: clamp(0.9rem, 1.2vw + 0.3rem, 1.3rem) !important;
        }

        .kpi-card-premium:hover .kpi-icon-glow {
            box-shadow: 0 0 30px rgba(255, 255, 255, 0.5);
            transform: scale(1.1);
        }

        /* Icon Circle Glow cho Orders - Fluid Sizing */
        .kpi-icon-circle-glow {
            width: clamp(28px, 3vw + 15px, 38px);
            height: clamp(28px, 3vw + 15px, 38px);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .kpi-icon-circle-glow i {
            font-size: clamp(0.85rem, 1.1vw + 0.3rem, 1.2rem) !important;
        }

        .kpi-card-premium:hover .kpi-icon-circle-glow {
            box-shadow: 0 0 30px rgba(255, 255, 255, 0.5);
            transform: scale(1.1) rotate(5deg);
        }

        /* Icon Animated cho Users - Fluid Sizing */
        .kpi-icon-animated {
            width: clamp(28px, 3vw + 15px, 38px);
            height: clamp(28px, 3vw + 15px, 38px);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
            animation: kpi-icon-pulse 2s ease-in-out infinite;
        }

        .kpi-icon-animated i {
            font-size: clamp(0.85rem, 1.1vw + 0.3rem, 1.2rem) !important;
        }

        @keyframes kpi-icon-pulse {
            0%, 100% {
                box-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
            }
            50% {
                box-shadow: 0 0 30px rgba(255, 255, 255, 0.5);
            }
        }

        .kpi-card-premium:hover .kpi-icon-animated {
            transform: scale(1.1);
            animation: none;
            box-shadow: 0 0 35px rgba(255, 255, 255, 0.6);
        }

        /* Icon Neon cho Products - Fluid Sizing */
        .kpi-icon-neon {
            width: clamp(28px, 3vw + 15px, 38px);
            height: clamp(28px, 3vw + 15px, 38px);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.3), 0 0 30px rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .kpi-icon-neon i {
            font-size: clamp(0.85rem, 1.1vw + 0.3rem, 1.2rem) !important;
        }

        .kpi-card-premium:hover .kpi-icon-neon {
            box-shadow: 0 0 30px rgba(255, 255, 255, 0.5), 0 0 60px rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        /* Icon Check Circle cho Completion - Fluid Sizing */
        .kpi-icon-check-circle {
            width: clamp(28px, 3vw + 15px, 38px);
            height: clamp(28px, 3vw + 15px, 38px);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .kpi-icon-check-circle i {
            font-size: clamp(0.85rem, 1.1vw + 0.3rem, 1.2rem) !important;
        }

        .kpi-card-premium:hover .kpi-icon-check-circle {
            box-shadow: 0 0 30px rgba(255, 255, 255, 0.5);
            transform: scale(1.1);
        }

        /* Icon Colors */
        .kpi-icon-revenue {
            color: #fff;
        }

        .kpi-icon-orders {
            color: #fff;
        }

        .kpi-icon-users {
            color: #fff;
        }

        .kpi-icon-products {
            color: #fff;
        }

        .kpi-icon-completion {
            color: #fff;
        }

        /* ========== QUICK ACTIONS CARDS ========== */
        :root {
            --qa-wrapper-bg: #ffffff;
            --qa-wrapper-border: rgba(0, 0, 0, 0.06);
            --qa-card-bg: #ffffff;
            --qa-card-border: rgba(0, 0, 0, 0.06);
            --qa-card-hover-bg: #f8fafc;
            --qa-title-color: #0f172a;
            --qa-subtitle-color: #475569;
        }

        body.dark {
            --qa-wrapper-bg: #1F1F23;
            --qa-wrapper-border: rgba(255, 255, 255, 0.08);
            --qa-card-bg: #1F1F23;
            --qa-card-border: rgba(255, 255, 255, 0.1);
            --qa-card-hover-bg: #27272a;
            --qa-title-color: #f8fafc;
            --qa-subtitle-color: #cbd5e1;
        }

        .quick-actions-wrapper {
            border-radius: 20px;
            background: var(--qa-wrapper-bg);
            border: 1px solid var(--qa-wrapper-border);
            transition: background 0.3s ease, border-color 0.3s ease;
        }

        .quick-actions-title {
            color: var(--qa-title-color);
            transition: color 0.3s ease;
        }

        .quick-action-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 1.5rem 1rem;
            background: var(--qa-card-bg);
            border: 1px solid var(--qa-card-border);
            border-radius: 20px;
            transition: all 0.25s ease-out;
            cursor: pointer;
            text-decoration: none !important;
            color: inherit;
        }

        .quick-action-card:hover {
            transform: scale(1.05);
            background: var(--qa-card-hover-bg);
            border-color: var(--qa-card-border);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .quick-action-card:focus {
            outline: 2px solid rgba(59, 130, 246, 0.5);
            outline-offset: 2px;
        }

        .quick-action-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 0.75rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .quick-action-card:hover .quick-action-icon {
            transform: scale(1.1);
        }

        /* Icon Colors với Neon Glow */
        .quick-action-blue {
            color: #60a5fa;
            background: rgba(96, 165, 250, 0.1);
            box-shadow: 0 0 20px rgba(96, 165, 250, 0.3);
        }

        .quick-action-card:hover .quick-action-blue {
            box-shadow: 0 0 30px rgba(96, 165, 250, 0.5);
        }

        .quick-action-purple {
            color: #a78bfa;
            background: rgba(167, 139, 250, 0.1);
            box-shadow: 0 0 20px rgba(167, 139, 250, 0.3);
        }

        .quick-action-card:hover .quick-action-purple {
            box-shadow: 0 0 30px rgba(167, 139, 250, 0.5);
        }

        .quick-action-yellow {
            color: #fbbf24;
            background: rgba(251, 191, 36, 0.1);
            box-shadow: 0 0 20px rgba(251, 191, 36, 0.3);
        }

        .quick-action-card:hover .quick-action-yellow {
            box-shadow: 0 0 30px rgba(251, 191, 36, 0.5);
        }

        .quick-action-cyan {
            color: #22d3ee;
            background: rgba(34, 211, 238, 0.1);
            box-shadow: 0 0 20px rgba(34, 211, 238, 0.3);
        }

        .quick-action-card:hover .quick-action-cyan {
            box-shadow: 0 0 30px rgba(34, 211, 238, 0.5);
        }

        .quick-action-pink {
            color: #f472b6;
            background: rgba(244, 114, 182, 0.1);
            box-shadow: 0 0 20px rgba(244, 114, 182, 0.3);
        }

        .quick-action-card:hover .quick-action-pink {
            box-shadow: 0 0 30px rgba(244, 114, 182, 0.5);
        }

        .quick-action-green {
            color: #34d399;
            background: rgba(52, 211, 153, 0.1);
            box-shadow: 0 0 20px rgba(52, 211, 153, 0.3);
        }

        .quick-action-card:hover .quick-action-green {
            box-shadow: 0 0 30px rgba(52, 211, 153, 0.5);
        }

        .quick-action-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--qa-title-color);
            margin-bottom: 0.25rem;
        }

        .quick-action-subtitle {
            font-size: 0.7rem;
            color: var(--qa-subtitle-color);
        }

        /* ========== BADGE GRADIENTS ========== */
        .kpi-badge-gradient {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            padding: 0.25rem 0.5rem;
            font-weight: 600;
        }

        .kpi-badge-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .kpi-badge-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .kpi-badge-secondary {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        }

        /* ========== PROGRESS DONUT MINI ========== */
        .kpi-donut-mini {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .kpi-donut-mini svg circle {
            transition: stroke-dashoffset 0.5s ease;
        }

        /* ========== TOOLTIP ========== */
        .kpi-tooltip-trigger {
            position: absolute;
            bottom: 10px;
            right: 10px;
            cursor: help;
            opacity: 0.6;
            transition: opacity 0.2s ease;
        }

        .kpi-tooltip-trigger:hover {
            opacity: 1;
        }

        /* ========== FADE IN ANIMATION ========== */
        @keyframes kpi-fade-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .kpi-card-premium {
            animation: kpi-fade-in 0.5s ease-out;
        }

        .kpi-card-premium:nth-child(1) { animation-delay: 0.1s; }
        .kpi-card-premium:nth-child(2) { animation-delay: 0.2s; }
        .kpi-card-premium:nth-child(3) { animation-delay: 0.3s; }
        .kpi-card-premium:nth-child(4) { animation-delay: 0.4s; }
        .kpi-card-premium:nth-child(5) { animation-delay: 0.5s; }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 576px) {
            .kpi-card-premium .card-body {
                padding: 1rem !important;
                min-height: 150px !important;
            }

            .kpi-icon-glow,
            .kpi-icon-circle-glow,
            .kpi-icon-animated,
            .kpi-icon-neon,
            .kpi-icon-check-circle {
                width: 40px;
                height: 40px;
                font-size: 1.2rem !important;
            }

            .quick-action-card {
                padding: 1rem 0.5rem;
            }

            .quick-action-icon {
                width: 50px;
                height: 50px;
                font-size: 1.5rem;
            }

            .quick-action-title {
                font-size: 0.8rem;
            }

            .quick-action-subtitle {
                font-size: 0.65rem;
            }

            .kpi-donut-mini {
                width: 50px !important;
                height: 50px !important;
            }

            .kpi-donut-mini svg {
                width: 50px;
                height: 50px;
            }
        }

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

        /* Order Status Table Styles - Đẹp và chuyên nghiệp */
        .order-status-table-wrapper {
            display: flex;
            flex-direction: column;
            overflow-y: hidden;
            overflow-x: auto;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background-color: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        /* Custom Scrollbar cho wrapper - chỉ thanh kéo ngang */
        .order-status-table-wrapper::-webkit-scrollbar {
            width: 0;
            height: 8px;
        }

        .order-status-table-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .order-status-table-wrapper::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .order-status-table-wrapper::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Firefox scrollbar - chỉ thanh kéo ngang */
        .order-status-table-wrapper {
            scrollbar-width: thin;
            scrollbar-color: #c1c1c1 #f1f1f1;
        }

        /* Category Revenue Chart Styles */
        .category-revenue-card {
            background: #ffffff;
        }

        body.dark .category-revenue-card {
            background: #1f1f1f;
        }

        /* Top Products Table Styles */
        .top-products-table-wrapper {
            max-height: 500px;
            overflow-y: auto;
            overflow-x: auto;
        }

        .top-products-table-wrapper::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .top-products-table-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .top-products-table-wrapper::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .top-products-table-wrapper::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Top Products Table Compact - Giảm font và kích thước */
        .top-products-table-compact {
            width: 100% !important;
            min-width: 100% !important;
            font-size: 0.8rem;
        }

        .top-products-table-compact th {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-weight: 600;
            color: #495057;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #dee2e6;
        }

        .top-products-table-compact th,
        .top-products-table-compact td {
            vertical-align: middle;
            padding: 10px 12px;
            white-space: nowrap;
        }

        /* Xử lý text quá dài trong Top Products Table */
        .top-products-table-compact td {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Cột tên sản phẩm - cho phép wrap nhưng có max-width */
        .top-products-table-compact td:nth-child(2) {
            white-space: normal;
            max-width: 200px;
        }

        .product-name-top {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
        }

        .product-category-top {
            max-width: 200px;
        }

        /* Các cột số - đảm bảo không bị overflow */
        .top-products-table-compact td:nth-child(n+3) {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Row styling với better visual hierarchy */
        .top-products-table-compact tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid #f1f3f5;
        }

        .top-products-table-compact tbody tr:hover {
            background: linear-gradient(90deg, #f8f9ff 0%, #ffffff 100%);
            transform: translateX(2px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .top-products-table-compact tbody tr:last-child {
            border-bottom: none;
        }

        body.dark .top-products-table-compact th {
            background: linear-gradient(135deg, #2b2b2b 0%, #1f1f1f 100%);
            color: #e9ecef;
            border-bottom-color: #444;
        }

        body.dark .top-products-table-compact tbody tr {
            border-bottom-color: #333;
        }

        body.dark .top-products-table-compact tbody tr:hover {
            background: linear-gradient(90deg, #2b2b2b 0%, #1f1f1f 100%);
        }

        /* Giảm min-width cho các cột */
        .top-products-th-image {
            min-width: 90px !important;
            width: 90px;
        }

        .top-products-th-name {
            min-width: 150px !important;
            max-width: 200px;
        }

        .top-products-th-quantity {
            min-width: 140px !important;
        }

        .top-products-th-revenue {
            min-width: 160px !important;
        }

        .top-products-th-contribution {
            min-width: 180px !important;
        }

        .top-products-th-conversion {
            min-width: 80px !important;
        }

        .top-products-th-stock {
            min-width: 70px !important;
        }

        .top-products-th-sub {
            min-width: 70px !important;
        }

        /* Ranking Badge */
        .product-rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            font-weight: 700;
            font-size: 0.75rem;
            margin-right: 8px;
            flex-shrink: 0;
        }

        .product-rank-badge.rank-1 {
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            color: #92400e;
            box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
        }

        .product-rank-badge.rank-2 {
            background: linear-gradient(135deg, #c0c0c0 0%, #e8e8e8 100%);
            color: #374151;
            box-shadow: 0 2px 8px rgba(192, 192, 192, 0.3);
        }

        .product-rank-badge.rank-3 {
            background: linear-gradient(135deg, #cd7f32 0%, #e6a057 100%);
            color: #78350f;
            box-shadow: 0 2px 8px rgba(205, 127, 50, 0.3);
        }

        .product-rank-badge.rank-other {
            background: linear-gradient(135deg, #e5e7eb 0%, #f3f4f6 100%);
            color: #6b7280;
        }

        /* Product Image với better styling */
        .product-thumbnail-top {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid #e5e7eb;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .product-thumbnail-top:hover {
            transform: scale(1.1);
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        /* Product Name với better typography */
        .product-name-top {
            font-weight: 600;
            color: #1f2937;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.875rem;
            line-height: 1.4;
            margin-bottom: 4px;
        }

        .product-name-top:hover {
            color: #3b82f6;
            text-decoration: underline;
        }

        .product-category-top {
            font-size: 0.7rem;
            color: #6b7280;
            margin-top: 2px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .product-category-top::before {
            content: "📁";
            font-size: 0.65rem;
        }

        /* Product Info Container */
        .product-info-container {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .product-info-text {
            flex: 1;
            min-width: 0;
        }


        /* Stock Badge với better styling */
        .stock-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.75rem;
            white-space: nowrap;
        }

        .stock-badge.high {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 1px solid #10b981;
        }

        .stock-badge.medium {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border: 1px solid #f59e0b;
        }

        .stock-badge.low {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 1px solid #ef4444;
        }

        /* Conversion Badge */
        .conversion-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .conversion-badge.high {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            border: 1px solid #3b82f6;
        }

        .conversion-badge.medium {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border: 1px solid #f59e0b;
        }

        .conversion-badge.low {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 1px solid #ef4444;
        }

        /* Progress Bar cho Tỷ lệ đóng góp */
        .contribution-progress {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-width: 80px;
        }

        .contribution-progress-bar-wrapper {
            flex: 1;
            height: 6px;
            background: #e5e7eb;
            border-radius: 3px;
            overflow: hidden;
            position: relative;
        }

        .contribution-progress-bar {
            height: 100%;
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        .contribution-progress-bar.estimated {
            background: linear-gradient(90deg, #3b82f6 0%, #60a5fa 100%);
        }

        .contribution-progress-bar.completed {
            background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
        }

        .contribution-progress-text {
            font-weight: 600;
            font-size: 0.75rem;
            color: #495057;
            min-width: 35px;
            text-align: right;
        }

        /* Revenue với better styling */
        .revenue-value {
            font-weight: 700;
            font-size: 0.85rem;
            color: #059669;
        }

        .revenue-value.estimated {
            color: #2563eb;
        }

        /* Quantity với icons */
        .quantity-value {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .quantity-value::before {
            content: "📦";
            font-size: 0.7rem;
        }

        body.dark .product-name-top {
            color: #f3f4f6;
        }

        body.dark .product-name-top:hover {
            color: #60a5fa;
        }

        body.dark .product-category-top {
            color: #9ca3af;
        }

        body.dark .top-products-table-compact tbody tr:hover {
            background: linear-gradient(90deg, #2b2b2b 0%, #1f1f1f 100%);
        }

        body.dark .revenue-value {
            color: #34d399;
        }

        body.dark .revenue-value.estimated {
            color: #60a5fa;
        }

        body.dark .contribution-progress-bar-wrapper {
            background: #374151;
        }

        body.dark .contribution-progress-text {
            color: #d1d5db;
        }

        body.dark .quantity-value {
            color: #e9ecef;
        }

        /* Responsive adjustments cho ranking badge */
        @media (max-width: 1199.98px) {
            .product-rank-badge {
                width: 24px;
                height: 24px;
                font-size: 0.7rem;
                margin-right: 6px;
            }

            .product-thumbnail-top {
                width: 45px;
                height: 45px;
            }
        }

        @media (max-width: 767.98px) {
            .product-rank-badge {
                width: 22px;
                height: 22px;
                font-size: 0.65rem;
                margin-right: 4px;
            }

            .product-thumbnail-top {
                width: 40px;
                height: 40px;
            }

            .contribution-progress {
                min-width: 60px;
            }

            .contribution-progress-text {
                min-width: 30px;
                font-size: 0.7rem;
            }
        }

        /* Animation cho progress bars */
        @keyframes progressBarAnimation {
            from {
                width: 0;
            }
        }

        .contribution-progress-bar {
            animation: progressBarAnimation 0.8s ease-out;
        }

        /* Inventory Table Styles */
        .inventory-table-wrapper {
            overflow-x: auto !important;
            overflow-y: auto !important;
            max-height: 240px; /* Đủ để hiển thị 4 dòng sản phẩm (mỗi dòng ~60px) */
            height: 240px; /* Cố định chiều cao cho 4 dòng */
            width: 100%;
            max-width: 100%;
            position: relative;
            -webkit-overflow-scrolling: touch;
            /* Ẩn scrollbar dọc cho Firefox */
            scrollbar-width: thin;
            scrollbar-color: #3b82f6 transparent;
            box-sizing: border-box;
        }

        /* Thiết kế thanh kéo ngang đẹp - WebKit browsers (Chrome, Safari, Edge) */
        .inventory-table-wrapper::-webkit-scrollbar {
            height: 12px;
            width: 0; /* Ẩn hoàn toàn scrollbar dọc */
        }

        /* Ẩn scrollbar dọc nhưng vẫn cho phép scroll */
        .inventory-table-wrapper::-webkit-scrollbar:vertical {
            width: 0 !important;
            display: none;
        }

        /* Chỉ hiển thị scrollbar ngang */
        .inventory-table-wrapper::-webkit-scrollbar:horizontal {
            height: 12px;
        }

        .inventory-table-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
            margin: 0 10px;
        }

        /* Chỉ hiển thị track cho scrollbar ngang */
        .inventory-table-wrapper::-webkit-scrollbar-track:horizontal {
            background: #f1f1f1;
        }

        .inventory-table-wrapper::-webkit-scrollbar-thumb {
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
            border-radius: 10px;
            transition: background 0.3s;
            min-width: 50px;
        }

        /* Chỉ hiển thị thumb cho scrollbar ngang */
        .inventory-table-wrapper::-webkit-scrollbar-thumb:horizontal {
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
        }

        .inventory-table-wrapper::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(90deg, #2563eb, #3b82f6);
        }

        /* Đảm bảo table có min-width để kích hoạt scrollbar ngang */
        #inventoryTable {
            min-width: 900px !important;
            width: 100%;
            max-width: 100%;
            table-layout: auto;
            box-sizing: border-box;
        }

        #inventoryTable th,
        #inventoryTable td {
            padding: 10px 16px;
        }

        /* Xử lý tên sản phẩm quá dài - cột đầu tiên */
        #inventoryTable td:first-child {
            max-width: 300px;
            white-space: normal;
            word-wrap: break-word;
        }

        #inventoryTable td:first-child .fw-bold {
            display: block;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            line-height: 1.4;
        }

        /* Các cột khác giữ nguyên nowrap */
        #inventoryTable td:not(:first-child) {
            white-space: nowrap;
        }

        /* Đảm bảo mỗi dòng có chiều cao cố định */
        #inventoryTable tbody tr {
            height: 60px;
        }

        .inventory-row:hover {
            background-color: #f8f9fa;
        }

        body.dark .inventory-row:hover {
            background-color: #374151;
        }


        body.dark .stock-badge.high {
            background-color: #064e3b;
            color: #6ee7b7;
        }

        body.dark .stock-badge.medium {
            background-color: #78350f;
            color: #fcd34d;
        }

        body.dark .stock-badge.low {
            background-color: #7f1d1d;
            color: #fca5a5;
        }

        @media (max-width: 768px) {
            .product-thumbnail-top {
                width: 40px;
                height: 40px;
            }

            #topProductsTable th:nth-child(5),
            #topProductsTable td:nth-child(5) {
                display: none; /* Ẩn cột tỷ lệ đóng góp trên mobile */
            }
        }

        /* Top Customers Table Styles */
        .top-customers-card {
            background: #ffffff;
        }

        body.dark .top-customers-card {
            background: #1f1f1f;
        }

        .top-customers-table-wrapper {
            max-height: 280px;
        }

        .top-customers-table-wrapper::-webkit-scrollbar {
            width: 6px;
        }

        .top-customers-table-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .top-customers-table-wrapper::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .top-customers-table-wrapper::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .top-customers-table-wrapper {
            scrollbar-width: thin;
            scrollbar-color: #c1c1c1 #f1f1f1;
        }

        .customer-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 12px;
            flex-shrink: 0;
        }

        .customer-row {
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .customer-row:hover {
            background-color: #f8f9fa !important;
        }

        body.dark .customer-row:hover {
            background-color: #2b2b2b !important;
        }

        /* Responsive cho mobile */
        @media (max-width: 991.98px) {
            .category-revenue-card,
            .top-customers-card {
                height: auto !important;
                min-height: 420px;
            }
        }

        /* Comments Table Styles */
        .comments-table-card,
        .users-table-card {
            background: #ffffff;
        }

        body.dark .comments-table-card,
        body.dark .users-table-card {
            background: #1f1f1f;
        }

        .comments-table-wrapper,
        .users-table-wrapper {
            overflow-x: auto !important;
            overflow-y: auto !important;
            position: relative;
            max-height: 360px; /* Đủ để hiển thị 6 dòng (6 dòng x ~60px/dòng) */
            height: 360px;
            width: 100%;
            -webkit-overflow-scrolling: touch;
            /* Ẩn scrollbar dọc cho Firefox */
            scrollbar-width: thin;
            scrollbar-color: #c1c1c1 transparent;
        }

        /* Thiết kế thanh kéo ngang - WebKit browsers (Chrome, Safari, Edge) */
        .comments-table-wrapper::-webkit-scrollbar,
        .users-table-wrapper::-webkit-scrollbar {
            height: 8px;
            width: 0; /* Ẩn hoàn toàn scrollbar dọc */
        }

        /* Ẩn scrollbar dọc nhưng vẫn cho phép scroll */
        .comments-table-wrapper::-webkit-scrollbar:vertical,
        .users-table-wrapper::-webkit-scrollbar:vertical {
            width: 0 !important;
            display: none;
        }

        /* Chỉ hiển thị scrollbar ngang */
        .comments-table-wrapper::-webkit-scrollbar:horizontal,
        .users-table-wrapper::-webkit-scrollbar:horizontal {
            height: 8px;
        }

        .comments-table-wrapper::-webkit-scrollbar-track,
        .users-table-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        /* Chỉ hiển thị track cho scrollbar ngang */
        .comments-table-wrapper::-webkit-scrollbar-track:horizontal,
        .users-table-wrapper::-webkit-scrollbar-track:horizontal {
            background: #f1f1f1;
        }

        .comments-table-wrapper::-webkit-scrollbar-thumb,
        .users-table-wrapper::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
            transition: background 0.2s;
        }

        /* Chỉ hiển thị thumb cho scrollbar ngang */
        .comments-table-wrapper::-webkit-scrollbar-thumb:horizontal,
        .users-table-wrapper::-webkit-scrollbar-thumb:horizontal {
            background: #c1c1c1;
        }

        .comments-table-wrapper::-webkit-scrollbar-thumb:hover,
        .users-table-wrapper::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Đảm bảo table có width đủ để trigger scrollbar ngang */
        #commentsTable,
        #usersTable {
            width: 100%;
            min-width: 900px; /* Tổng min-width của các cột */
        }

        /* ========== XỬ LÝ TEXT QUÁ DÀI TRONG CÁC BẢNG ========== */

        /* Comments & Users Tables - Xử lý text overflow */
        #commentsTable th,
        #commentsTable td,
        #usersTable th,
        #usersTable td {
            white-space: nowrap;
        }

        /* Comments Table - Cột User (cột 1) */
        #commentsTable td:nth-child(1) {
            max-width: 180px;
            min-width: 180px;
        }

        .comment-user-name {
            max-width: 140px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
        }

        .comment-user-email {
            max-width: 140px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
        }

        /* Comments Table - Cột Nội dung (cột 2) */
        #commentsTable td:nth-child(2) {
            max-width: 200px;
            min-width: 200px;
            white-space: normal;
        }

        .comment-content-preview {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            word-wrap: break-word;
        }

        /* Comments Table - Cột Sản phẩm (cột 3) */
        #commentsTable td:nth-child(3) {
            max-width: 150px;
            min-width: 150px;
        }

        .product-name-comment {
            max-width: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
        }

        /* Users Table - Cột Tên (cột 2) */
        #usersTable td:nth-child(2) {
            max-width: 150px;
            min-width: 150px;
        }

        .user-name {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
        }

        /* Users Table - Cột Email (cột 3) */
        #usersTable td:nth-child(3) {
            max-width: 150px;
            min-width: 150px;
        }

        .user-email {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
        }

        /* Top Products Table - Xử lý tên sản phẩm */
        .top-products-th-name {
            max-width: 200px;
        }

        .product-name-top {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
        }

        /* Top Customers Table - Xử lý tên khách hàng */
        #topCustomersTable td:nth-child(1) {
            max-width: 200px;
            min-width: 200px;
        }

        .customer-name,
        .customer-email {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
        }

        .customer-info-container {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .customer-info-text {
            flex: 1;
            min-width: 0;
        }

        /* Inventory Table - Đã có xử lý, nhưng cải thiện thêm */
        #inventoryTable td:first-child {
            max-width: 300px;
            min-width: 300px;
        }

        #inventoryTable td:first-child .fw-bold {
            max-width: 280px;
        }

        /* Revenue values - Xử lý số quá dài */
        .revenue-value {
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: inline-block;
        }

        /* Quantity values */
        .quantity-value {
            max-width: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: inline-block;
        }

        /* Status badges - Đảm bảo không bị overflow */
        .status-badge {
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Product category - Xử lý text dài */
        .product-category-top {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
        }

        /* SKU trong Inventory */
        #inventoryTable td:first-child .small {
            max-width: 280px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
        }

        /* Top Customers - Revenue và Percent */
        #topCustomersTable td:nth-child(3) {
            max-width: 120px;
        }

        #topCustomersTable td:nth-child(4) {
            max-width: 80px;
        }

        /* Đảm bảo tất cả các ô có tooltip khi text bị truncate */
        [title] {
            cursor: help;
        }

        /* Responsive cho Top Customers */
        @media (max-width: 767.98px) {
            #topCustomersTable td:nth-child(1) {
                max-width: 150px;
                min-width: 150px;
            }

            .customer-name,
            .customer-email {
                max-width: 100px;
            }
        }

        @media (max-width: 575.98px) {
            #topCustomersTable td:nth-child(1) {
                max-width: 120px;
                min-width: 120px;
            }

            .customer-name,
            .customer-email {
                max-width: 80px;
            }
        }

        /* Action Button Styling */
        .table-action-btn {
            transition: all 0.2s ease;
        }

        .table-action-btn:hover {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            transform: scale(1.05);
        }

        /* Dark mode adjustments */
        body.dark .user-name,
        body.dark .comment-user-name {
            color: #f3f4f6;
        }

        body.dark .user-email,
        body.dark .comment-user-email {
            color: #9ca3af;
        }

        body.dark .comment-content-preview {
            color: #d1d5db;
        }

        body.dark .comment-content-preview:hover {
            color: #60a5fa;
        }

        body.dark .product-name-comment {
            color: #e9ecef;
        }

        body.dark .product-name-comment:hover {
            color: #60a5fa;
        }

        body.dark .table-action-btn:hover {
            background-color: #2b2b2b;
            border-color: #444;
        }

        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .comment-avatar,
            .user-avatar {
                width: 32px;
                height: 32px;
                font-size: 12px;
            }

            .user-avatar {
                width: 36px;
                height: 36px;
                font-size: 13px;
            }

            /* Giảm max-width cho mobile */
            #commentsTable td:nth-child(1) {
                max-width: 120px;
                min-width: 120px;
            }

            .comment-user-name,
            .comment-user-email {
                max-width: 80px;
            }

            #commentsTable td:nth-child(2) {
                max-width: 150px;
                min-width: 150px;
            }

            .comment-content-preview {
                max-width: 150px;
                font-size: 0.75rem;
            }

            #commentsTable td:nth-child(3) {
                max-width: 120px;
                min-width: 120px;
            }

            .product-name-comment {
                max-width: 80px;
            }

            #usersTable td:nth-child(2) {
                max-width: 120px;
                min-width: 120px;
            }

            .user-name {
                max-width: 120px;
            }

            #usersTable td:nth-child(3) {
                max-width: 120px;
                min-width: 120px;
            }

            .user-email {
                max-width: 120px;
            }

            .product-thumbnail {
                width: 35px;
                height: 35px;
            }

            .status-badge {
                font-size: 0.65rem;
                padding: 4px 8px;
                max-width: 80px;
            }

            .revenue-value {
                max-width: 90px;
                font-size: 0.75rem;
            }

            .quantity-value {
                max-width: 70px;
                font-size: 0.75rem;
            }
        }

        /* Small Mobile */
        @media (max-width: 575.98px) {
            .comment-user-name,
            .comment-user-email {
                max-width: 60px;
            }

            .comment-content-preview {
                max-width: 120px;
            }

            .product-name-comment {
                max-width: 60px;
            }

            .user-name,
            .user-email {
                max-width: 100px;
            }

            .revenue-value {
                max-width: 70px;
                font-size: 0.7rem;
            }
        }

        /* ========== COMMENTS & USERS TABLES UI/UX IMPROVEMENTS ========== */

        /* Table Headers với better styling */
        #commentsTable thead th,
        #usersTable thead th {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-weight: 600;
            color: #495057;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #dee2e6;
            padding: 12px 10px;
        }

        #commentsTable thead th i,
        #usersTable thead th i {
            color: #6b7280;
            font-size: 0.7rem;
        }

        body.dark #commentsTable thead th,
        body.dark #usersTable thead th {
            background: linear-gradient(135deg, #2b2b2b 0%, #1f1f1f 100%);
            color: #e9ecef;
            border-bottom-color: #444;
        }

        body.dark #commentsTable thead th i,
        body.dark #usersTable thead th i {
            color: #9ca3af;
        }

        /* Row styling với better visual hierarchy */
        #commentsTable tbody tr,
        #usersTable tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid #f1f3f5;
        }

        #commentsTable tbody tr:hover,
        #usersTable tbody tr:hover {
            background: linear-gradient(90deg, #f8f9ff 0%, #ffffff 100%);
            transform: translateX(2px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        body.dark #commentsTable tbody tr:hover,
        body.dark #usersTable tbody tr:hover {
            background: linear-gradient(90deg, #2b2b2b 0%, #1f1f1f 100%);
        }

        /* Avatar với better styling */
        .comment-avatar,
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 13px;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }

        .comment-avatar:hover,
        .user-avatar:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.5);
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            font-size: 15px;
        }

        /* User Info Container */
        .user-info-container,
        .comment-user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-info-text,
        .comment-user-text {
            flex: 1;
            min-width: 0;
        }

        .user-name,
        .comment-user-name {
            font-weight: 600;
            font-size: 0.875rem;
            color: #1f2937;
            margin-bottom: 2px;
            line-height: 1.3;
        }

        .user-email,
        .comment-user-email {
            font-size: 0.7rem;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .user-email::before,
        .comment-user-email::before {
            content: "✉";
            font-size: 0.65rem;
        }

        /* Comment Content Preview với better styling */
        .comment-content-preview {
            max-width: 220px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            cursor: pointer;
            font-size: 0.8rem;
            line-height: 1.4;
            color: #374151;
            transition: color 0.2s ease;
        }

        .comment-content-preview:hover {
            color: #3b82f6;
        }

        /* Product Thumbnail trong Comments */
        .product-thumbnail {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid #e5e7eb;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .product-thumbnail:hover {
            transform: scale(1.1);
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        /* Product Info trong Comments */
        .product-info-comment {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .product-name-comment {
            font-weight: 600;
            font-size: 0.8rem;
            color: #1f2937;
            cursor: pointer;
            transition: color 0.2s ease;
            line-height: 1.3;
        }

        .product-name-comment:hover {
            color: #3b82f6;
            text-decoration: underline;
        }

        /* Status Badge với icons và better styling */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        .status-badge::before {
            font-size: 0.65rem;
        }

        .status-approved {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 1px solid #10b981;
        }

        .status-approved::before {
            content: "✓";
        }

        .status-pending {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border: 1px solid #f59e0b;
        }

        .status-pending::before {
            content: "⏱";
        }

        .status-rejected {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 1px solid #ef4444;
        }

        .status-rejected::before {
            content: "✗";
        }

        .status-hidden {
            background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
            color: #374151;
            border: 1px solid #9ca3af;
        }

        .status-hidden::before {
            content: "👁";
        }

        .status-active {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 1px solid #10b981;
        }

        .status-active::before {
            content: "✓";
        }

        .status-banned {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 1px solid #ef4444;
        }

        .status-banned::before {
            content: "🚫";
        }

        .status-unverified {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border: 1px solid #f59e0b;
        }

        .status-unverified::before {
            content: "⚠";
        }

        .status-approved {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .status-pending {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .status-rejected {
            background-color: #FEE2E2;
            color: #991B1B;
        }

        .status-hidden {
            background-color: #E5E7EB;
            color: #374151;
        }

        .status-active {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .status-banned {
            background-color: #FEE2E2;
            color: #991B1B;
        }

        .status-unverified {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .action-buttons {
            display: flex;
            gap: 4px;
        }

        .action-btn {
            padding: 4px 8px;
            border: none;
            background: transparent;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .action-btn:hover {
            background-color: #f3f4f6;
        }

        body.dark .action-btn:hover {
            background-color: #374151;
        }

        .action-btn.approve {
            color: #10B981;
        }

        .action-btn.reject {
            color: #EF4444;
        }

        .action-btn.delete {
            color: #F59E0B;
        }

        .action-btn.view {
            color: #3B82F6;
        }

        /* Dropdown actions */
        .dropdown-toggle::after {
            display: none;
        }

        .dropdown-item {
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        body.dark .dropdown-item:hover {
            background-color: #374151;
        }

        .dropdown-item i {
            width: 18px;
            text-align: center;
        }

        .order-status-table {
            font-size: 0.65rem;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: visible;
            width: 100%;
            margin: 0;
        }

        .order-status-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: sticky;
            top: 0;
            z-index: 20;
        }

        .order-status-table thead th {
            font-size: 0.65rem;
            font-weight: 600;
            padding: 0.5rem 0.5rem;
            white-space: nowrap;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .order-status-table tbody {
            background-color: #ffffff;
        }

        .order-status-table tbody td {
            font-size: 0.65rem;
            padding: 0.5rem 0.5rem;
            vertical-align: middle;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.2s ease;
        }

        .order-status-table tbody tr:last-child td {
            border-bottom: none;
        }

        .order-status-table .order-status-row {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .order-status-table .order-status-row:hover {
            background-color: #f8f9fa !important;
            transform: translateX(2px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .order-status-table .order-status-row:hover td {
            color: #212529;
        }

        /* Color dot */
        .order-status-dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        /* Label column */
        .order-status-label {
            font-weight: 500;
            color: #212529;
        }

        /* Count column */
        .order-status-count {
            font-weight: 600;
            color: #495057;
            font-family: 'Courier New', monospace;
        }

        /* Ratio column */
        .order-status-ratio {
            font-weight: 600;
            color: #667eea;
        }

        /* Trend column */
        .order-status-trend {
            font-weight: 500;
        }

        /* Dark mode support */
        body.dark .order-status-table-wrapper {
            background-color: #1f1f1f;
            border-color: #444;
        }

        body.dark .order-status-table-wrapper::-webkit-scrollbar-track {
            background: #2b2b2b;
        }

        body.dark .order-status-table-wrapper::-webkit-scrollbar-thumb {
            background: #555;
        }

        body.dark .order-status-table-wrapper::-webkit-scrollbar-thumb:hover {
            background: #666;
        }

        body.dark .order-status-table thead {
            background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
        }

        body.dark .order-status-table thead th {
            color: #ffffff;
            border-bottom-color: rgba(255, 255, 255, 0.2);
            background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
        }

        body.dark .order-status-table tbody {
            background-color: #1f1f1f;
        }

        body.dark .order-status-table tbody td {
            border-bottom-color: #333;
            color: #e9ecef;
        }

        body.dark .order-status-table .order-status-row:hover {
            background-color: #2b2b2b !important;
        }

        body.dark .order-status-label {
            color: #e9ecef;
        }

        body.dark .order-status-count {
            color: #d1d5db;
        }

        /* ========== RESPONSIVE IMPROVEMENTS - COMPREHENSIVE UI/UX ========== */

        /* Tablet (768px - 991px) */
        @media (max-width: 991.98px) {
            /* KPI Cards - Tablet */
            .kpi-card-premium .card-body {
                padding: 1.25rem !important;
                min-height: 160px !important;
            }

            .kpi-main-value {
                font-size: 1.75rem !important;
            }

            .kpi-icon-glow,
            .kpi-icon-circle-glow,
            .kpi-icon-animated,
            .kpi-icon-neon,
            .kpi-icon-check-circle {
                width: 45px;
                height: 45px;
                font-size: 1.4rem !important;
            }

            /* Charts - Tablet */
            #revenueOrdersChart {
                min-height: 300px !important;
            }

            .chart-container {
                min-height: 250px !important;
            }

            /* Button Groups - Tablet */
            .btn-group-sm .btn {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }

            /* Card Headers - Tablet */
            .card-header h6 {
                font-size: 1rem;
            }

            .card-header small {
                font-size: 0.75rem;
            }

            /* Quick Actions - Tablet */
            .quick-action-card {
                padding: 1.25rem 0.75rem;
            }

            .quick-action-icon {
                width: 55px;
                height: 55px;
                font-size: 1.6rem;
            }

            /* Tables - Tablet */
            .comments-table-wrapper,
            .users-table-wrapper {
                max-height: 320px;
                height: 320px;
            }

            .inventory-table-wrapper {
                max-height: 280px;
            }
        }

        /* Mobile (576px - 767px) */
        @media (max-width: 767.98px) {
            /* Section Spacing - Mobile */
            section.mb-4 {
                margin-bottom: 1.5rem !important;
            }

            /* Header - Mobile */
            section.mb-4 h4 {
                font-size: 1.25rem;
            }

            section.mb-4 p {
                font-size: 0.875rem;
            }

            /* KPI Cards - Mobile */
            .row-cols-2 .col {
                margin-bottom: 0.75rem;
            }

            .kpi-card-premium .card-body {
                padding: 1rem !important;
                min-height: 140px !important;
            }

            .kpi-main-value {
                font-size: 1.5rem !important;
                line-height: 1.1 !important;
            }

            .kpi-card-premium .card-body p {
                font-size: 0.7rem !important;
            }

            .kpi-card-premium .card-body .d-flex.flex-column.gap-1 {
                font-size: 0.65rem !important;
            }

            .kpi-icon-glow,
            .kpi-icon-circle-glow,
            .kpi-icon-animated,
            .kpi-icon-neon,
            .kpi-icon-check-circle {
                width: 38px;
                height: 38px;
                font-size: 1.1rem !important;
            }

            .kpi-donut-mini {
                width: 45px !important;
                height: 45px !important;
            }

            .kpi-donut-mini svg {
                width: 45px;
                height: 45px;
            }

            /* Charts - Mobile */
            .col-lg-8,
            .col-lg-4 {
                margin-bottom: 1rem;
            }

            #revenueOrdersChart {
                min-height: 280px !important;
            }

            .chart-container {
                min-height: 220px !important;
            }

            /* Card Headers - Mobile */
            .card-header {
                padding: 1rem !important;
            }

            .card-header h6 {
                font-size: 0.95rem;
                margin-bottom: 0.5rem;
            }

            .card-header small {
                font-size: 0.7rem;
                display: block;
                margin-top: 0.25rem;
            }

            /* Button Groups - Mobile */
            .revenue-orders-btn-group,
            .order-status-btn-group {
                flex-wrap: wrap;
                width: 100%;
                display: flex;
                gap: 0.25rem;
            }

            .revenue-orders-btn-group .btn,
            .order-status-btn-group .btn {
                font-size: 0.7rem;
                padding: 0.2rem 0.4rem;
                flex: 1 1 auto;
                min-width: calc(50% - 0.125rem);
                border-radius: 0.375rem !important;
            }

            .revenue-orders-btn-group .btn:last-child,
            .order-status-btn-group .btn:last-child {
                min-width: 100%;
                margin-top: 0.25rem;
            }

            /* Revenue Orders Filters - Mobile */
            .revenue-orders-filters {
                flex-direction: column;
                align-items: stretch !important;
                width: 100%;
            }

            .revenue-orders-filters > * {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .revenue-orders-filters > *:last-child {
                margin-bottom: 0;
            }

            /* Date Range Picker - Mobile */
            .revenue-orders-custom-range {
                flex-direction: column;
                width: 100%;
                margin-top: 0.5rem;
            }

            .revenue-orders-custom-range input,
            .revenue-orders-custom-range button {
                width: 100% !important;
                margin-bottom: 0.5rem;
            }

            .revenue-orders-custom-range span {
                display: none;
            }

            /* Form Selects - Mobile */
            .form-select-sm {
                width: 100% !important;
                margin-top: 0.5rem;
            }

            /* Quick Actions - Mobile */
            .quick-action-card {
                padding: 1rem 0.5rem;
            }

            .quick-action-icon {
                width: 48px;
                height: 48px;
                font-size: 1.4rem;
                margin-bottom: 0.5rem;
            }

            .quick-action-title {
                font-size: 0.75rem;
                margin-bottom: 0.15rem;
            }

            .quick-action-subtitle {
                font-size: 0.6rem;
            }

            /* Tables - Mobile */
            .card-body.p-0 {
                padding: 0 !important;
            }

            .comments-table-wrapper,
            .users-table-wrapper {
                max-height: 300px;
                height: 300px;
            }

            .inventory-table-wrapper {
                max-height: 240px;
            }

            /* Table Headers - Mobile */
            .card-header .d-flex.flex-wrap {
                flex-direction: column;
                align-items: flex-start !important;
            }

            .card-header .d-flex.gap-2 {
                width: 100%;
                margin-top: 0.75rem;
            }

            .card-header .form-select-sm,
            .card-header .input-group {
                width: 100% !important;
                margin-bottom: 0.5rem;
            }

            /* Card Footer - Mobile */
            .card-footer .row {
                margin: 0;
            }

            .card-footer .col-md-6 {
                margin-bottom: 0.75rem;
            }

            .card-footer .col-md-6:last-child {
                margin-bottom: 0;
            }

            /* Inventory Table - Mobile */
            .inventory-table-wrapper {
                font-size: 0.875rem;
                max-width: 100% !important;
                overflow-x: auto !important;
                overflow-y: auto !important;
            }

            #inventoryTable {
                min-width: 600px !important;
            }

            .inventory-table-wrapper td {
                padding: 0.5rem 0.25rem !important;
            }

            /* Inventory filter - Mobile */
            .inventory-filter-wrapper {
                width: 100% !important;
                max-width: 100% !important;
                margin-top: 0.75rem !important;
                flex-wrap: nowrap !important;
                overflow: hidden;
            }

            .inventory-filter-wrapper .table-search-input {
                flex: 1 1 auto !important;
                min-width: 0 !important;
                max-width: calc(100% - 140px) !important;
            }

            .inventory-filter-wrapper .table-search-input .form-control {
                min-width: 0 !important;
                font-size: 0.875rem !important;
            }

            .inventory-filter-wrapper .table-filter-select {
                flex: 0 0 auto !important;
                min-width: 120px !important;
                max-width: 140px !important;
                font-size: 0.875rem !important;
            }

            /* Card header - Mobile */
            .card-header {
                padding: 12px 16px !important;
                overflow: hidden !important;
            }

            .card-header > div {
                width: 100% !important;
                max-width: 100% !important;
            }

            /* Top Products/Customers - Mobile */
            .product-thumbnail-top {
                width: 35px;
                height: 35px;
            }

            /* Order Status Table - Mobile */
            .order-status-table-wrapper {
                font-size: 0.8rem;
            }

            .order-status-table th,
            .order-status-table td {
                padding: 0.5rem 0.25rem !important;
            }
        }

        /* Small Mobile (max-width: 575px) */
        @media (max-width: 575.98px) {
            /* KPI Cards - Small Mobile */
            .kpi-card-premium .card-body {
                padding: 0.875rem !important;
                min-height: 130px !important;
            }

            .kpi-main-value {
                font-size: 1.25rem !important;
            }

            .kpi-icon-glow,
            .kpi-icon-circle-glow,
            .kpi-icon-animated,
            .kpi-icon-neon,
            .kpi-icon-check-circle {
                width: 35px;
                height: 35px;
                font-size: 1rem !important;
            }

            /* Charts - Small Mobile */
            #revenueOrdersChart {
                min-height: 250px !important;
            }

            .chart-container {
                min-height: 200px !important;
            }

            /* Button Groups - Small Mobile */
            .revenue-orders-btn-group .btn,
            .order-status-btn-group .btn {
                font-size: 0.65rem;
                padding: 0.15rem 0.3rem;
                min-width: calc(33.333% - 0.1rem);
            }

            .revenue-orders-btn-group .btn:last-child,
            .order-status-btn-group .btn:last-child {
                min-width: 100%;
            }

            /* Quick Actions - Small Mobile */
            .quick-action-card {
                padding: 0.875rem 0.375rem;
            }

            .quick-action-icon {
                width: 42px;
                height: 42px;
                font-size: 1.2rem;
            }

            .quick-action-title {
                font-size: 0.7rem;
            }

            .quick-action-subtitle {
                font-size: 0.55rem;
            }

            /* Tables - Small Mobile */
            .comments-table-wrapper,
            .users-table-wrapper {
                max-height: 280px;
                height: 280px;
            }

            .inventory-table-wrapper {
                max-height: 220px;
            }

            /* Card Headers - Small Mobile */
            .card-header {
                padding: 0.875rem !important;
            }

            .card-header h6 {
                font-size: 0.9rem;
            }

            /* Footer - Small Mobile */
            .card-footer {
                padding: 0.75rem !important;
            }

            .card-footer small {
                font-size: 0.7rem;
            }

            .card-footer strong {
                font-size: 0.9rem;
            }
        }

        /* Extra Small Mobile (max-width: 375px) */
        @media (max-width: 374.98px) {
            /* KPI Cards - Extra Small */
            .kpi-main-value {
                font-size: 1.1rem !important;
            }

            .kpi-card-premium .card-body {
                padding: 0.75rem !important;
                min-height: 120px !important;
            }

            /* Charts - Extra Small */
            #revenueOrdersChart {
                min-height: 220px !important;
            }

            /* Button Groups - Extra Small */
            .btn-group-sm .btn {
                font-size: 0.6rem;
                padding: 0.1rem 0.25rem;
            }
        }

        /* Landscape Mobile (max-width: 767px and orientation: landscape) */
        @media (max-width: 767.98px) and (orientation: landscape) {
            .kpi-card-premium .card-body {
                min-height: 120px !important;
            }

            #revenueOrdersChart {
                min-height: 200px !important;
            }

            .comments-table-wrapper,
            .users-table-wrapper {
                max-height: 200px;
                height: 200px;
            }
        }

        /* Print Styles */
        @media print {
            .kpi-card-premium,
            .quick-action-card,
            .card {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .btn-group,
            .dropdown {
                display: none;
            }
        }

        /* Accessibility Improvements */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* High Contrast Mode */
        @media (prefers-contrast: high) {
            .kpi-card-premium {
                border: 2px solid currentColor;
            }

            .btn-outline-primary,
            .btn-outline-secondary {
                border-width: 2px;
            }
        }

        /* Dark Mode Responsive Adjustments */
        @media (max-width: 767.98px) {
            body.dark .kpi-card-premium {
                background: linear-gradient(135deg, #2b2b2b 0%, #1f1f1f 100%);
            }

            body.dark .card {
                background-color: #1f1f1f;
                border-color: #444;
            }

            body.dark .card-header {
                background-color: #2b2b2b !important;
                border-bottom-color: #444;
            }
        }

        /* Additional UI/UX Improvements */

        /* Smooth Transitions */
        .card,
        .kpi-card-premium,
        .quick-action-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Focus States for Accessibility */
        .btn:focus-visible,
        .form-select:focus-visible,
        .form-control:focus-visible {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }

        /* Loading States */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.15em;
        }

        /* Better Touch Targets for Mobile */
        @media (max-width: 767.98px) {
            .btn,
            .dropdown-toggle,
            .form-select,
            .form-control {
                min-height: 44px; /* iOS recommended touch target */
            }

            .btn-sm {
                min-height: 36px;
            }
        }

        /* Improved Scrollbar for Better UX */
        .inventory-table-wrapper::-webkit-scrollbar,
        .comments-table-wrapper::-webkit-scrollbar,
        .users-table-wrapper::-webkit-scrollbar {
            height: 6px;
        }

        .inventory-table-wrapper::-webkit-scrollbar-thumb,
        .comments-table-wrapper::-webkit-scrollbar-thumb,
        .users-table-wrapper::-webkit-scrollbar-thumb {
            background: linear-gradient(90deg, #cbd5e1 0%, #94a3b8 100%);
            border-radius: 3px;
        }

        .inventory-table-wrapper::-webkit-scrollbar-thumb:hover,
        .comments-table-wrapper::-webkit-scrollbar-thumb:hover,
        .users-table-wrapper::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(90deg, #94a3b8 0%, #64748b 100%);
        }

        /* Card Shadows - Subtle but Effective */
        .card {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Better Text Contrast */
        .text-muted {
            color: #6b7280 !important;
        }

        body.dark .text-muted {
            color: #9ca3af !important;
        }

        /* Improved Badge Styling */
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }

        /* Section Spacing Consistency */
        section {
            scroll-margin-top: 80px; /* Account for sticky navbar */
        }

        /* Improved Form Controls */
        .form-select-sm,
        .form-control-sm {
            border-radius: 0.375rem;
            border: 1px solid #d1d5db;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-select-sm:focus,
        .form-control-sm:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
        }

        body.dark .form-select-sm,
        body.dark .form-control-sm {
            background-color: #1f1f1f;
            border-color: #444;
            color: #e9ecef;
        }

        body.dark .form-select-sm:focus,
        body.dark .form-control-sm:focus {
            border-color: #3b82f6;
            background-color: #1f1f1f;
        }

        /* ========== RESPONSIVE PROFESSIONAL IMPROVEMENTS ========== */

        /* Card Headers Responsive */
        .card-header-responsive {
            padding: 20px 24px;
        }

        /* Category Revenue & Top Customers Cards */
        .category-revenue-card-responsive,
        .top-customers-card-responsive {
            height: 420px;
        }

        /* Form Selects & Inputs Responsive */
        .revenue-orders-select {
            width: auto;
            min-width: 100px;
        }

        .table-filter-select {
            width: auto;
            min-width: 120px;
        }

        .table-search-input {
            width: 200px;
        }

        /* Inventory filter wrapper - luôn giữ trên cùng một dòng */
        .inventory-filter-wrapper {
            flex-wrap: nowrap !important;
            display: flex !important;
            max-width: 100%;
            overflow: hidden;
            box-sizing: border-box;
        }

        .inventory-filter-wrapper .table-search-input {
            flex: 1 1 auto;
            min-width: 0;
            max-width: 100%;
            overflow: hidden;
        }

        .inventory-filter-wrapper .table-search-input .form-control {
            min-width: 0;
            width: 100%;
        }

        .inventory-filter-wrapper .table-filter-select {
            flex: 0 0 auto;
            min-width: 120px;
            max-width: 180px;
            white-space: nowrap;
        }

        /* Card header - đảm bảo không tràn */
        .card-header {
            overflow: hidden;
            box-sizing: border-box;
        }

        .card-header > div {
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }

        /* Responsive Breakpoints - Mobile First Approach */

        /* Extra Small Devices (phones, < 576px) */
        @media (max-width: 575.98px) {
            /* Card Headers */
            .card-header-responsive {
                padding: 12px 16px !important;
            }

            .card-header-responsive h6 {
                font-size: 0.95rem;
                margin-bottom: 0.75rem;
            }

            /* Category & Customers Cards */
            .category-revenue-card-responsive,
            .top-customers-card-responsive {
                height: auto !important;
                min-height: 350px;
            }

            /* Form Controls */
            .revenue-orders-select,
            .table-filter-select {
                width: 100% !important;
                min-width: 100% !important;
                margin-top: 0.5rem;
            }

            .table-search-input {
                width: 100% !important;
                max-width: 100% !important;
                margin-top: 0.5rem;
            }

            /* Inventory filter wrapper - responsive - luôn giữ trên cùng một dòng */
            .inventory-filter-wrapper {
                width: 100% !important;
                max-width: 100% !important;
                margin-top: 0.75rem;
                flex-wrap: nowrap !important;
                overflow: hidden;
                box-sizing: border-box;
            }

            .inventory-filter-wrapper .table-search-input {
                flex: 1 1 auto !important;
                min-width: 0 !important;
                max-width: calc(100% - 160px) !important;
                margin-top: 0 !important;
                overflow: hidden;
            }

            .inventory-filter-wrapper .table-search-input .form-control {
                min-width: 0 !important;
                width: 100% !important;
            }

            .inventory-filter-wrapper .table-filter-select {
                flex: 0 0 auto !important;
                min-width: 120px !important;
                max-width: 160px !important;
                margin-top: 0 !important;
            }

            /* Card header responsive */
            .card-header {
                overflow: hidden !important;
                box-sizing: border-box;
            }

            .card-header > div {
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box;
            }

            /* Button Groups - Stack vertically */
            .revenue-orders-btn-group,
            .order-status-btn-group {
                flex-direction: column;
                width: 100%;
            }

            .revenue-orders-btn-group .btn,
            .order-status-btn-group .btn {
                width: 100%;
                margin-bottom: 0.25rem;
                border-radius: 0.375rem !important;
            }

            .revenue-orders-btn-group .btn:last-child,
            .order-status-btn-group .btn:last-child {
                margin-bottom: 0;
            }

            /* Charts */
            #revenueOrdersChart {
                min-height: 250px !important;
            }

            /* Card Footer */
            .card-footer {
                padding: 0.75rem !important;
                font-size: 0.8rem;
            }

            .card-footer .row {
                flex-direction: column;
            }

            .card-footer .col-md-6 {
                margin-bottom: 0.75rem;
            }

            .card-footer .col-md-6:last-child {
                margin-bottom: 0;
            }
        }

        /* ========== CHARTS RESPONSIVE ========== */
        .chart-responsive {
            min-height: var(--chart-min-height);
            width: 100%;
            position: relative;
        }

        /* Chart responsive adjustments for different zoom levels */
        @media (min-width: 1400px) {
            .chart-responsive {
                min-height: 420px;
            }
        }

        @media (max-width: 991.98px) {
            .chart-responsive {
                min-height: clamp(250px, 35vw, 320px);
            }
        }

        @media (max-width: 767.98px) {
            .chart-responsive {
                min-height: clamp(220px, 40vw, 280px);
            }
        }

        @media (max-width: 575.98px) {
            .chart-responsive {
                min-height: clamp(200px, 45vw, 250px);
            }
        }

        /* ========== CUSTOM DATE RANGE PICKERS RESPONSIVE ========== */

        .custom-date-range-picker {
            width: 100%;
        }

        .custom-date-range-wrapper {
            flex-wrap: wrap;
        }

        .custom-date-input {
            width: auto;
            min-width: 140px;
        }

        .custom-date-separator {
            white-space: nowrap;
        }

        /* Mobile */
        @media (max-width: 767.98px) {
            .custom-date-range-wrapper {
                flex-direction: column;
                align-items: stretch !important;
            }

            .custom-date-range-wrapper > * {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .custom-date-range-wrapper > *:last-child {
                margin-bottom: 0;
            }

            .custom-date-input {
                width: 100% !important;
                min-width: 100% !important;
            }

            .custom-date-separator {
                display: none;
            }
        }

        /* Small Mobile */
        @media (max-width: 575.98px) {
            .custom-date-range-picker {
                margin-top: 0.75rem !important;
            }

            .custom-date-range-wrapper {
                gap: 0.5rem;
            }
        }

        /* Small Devices (landscape phones, >= 576px) */
        @media (min-width: 576px) and (max-width: 767.98px) {
            .card-header-responsive {
                padding: 16px 20px;
            }

            .category-revenue-card-responsive,
            .top-customers-card-responsive {
                height: auto !important;
                min-height: 380px;
            }

            /* Inventory filter responsive - luôn giữ trên cùng một dòng */
            .inventory-filter-wrapper {
                width: 100% !important;
                max-width: 100% !important;
                margin-top: 0.75rem;
                flex-wrap: nowrap !important;
                overflow: hidden;
                box-sizing: border-box;
            }

            .inventory-filter-wrapper .table-search-input {
                flex: 1 1 auto !important;
                min-width: 0 !important;
                max-width: calc(100% - 170px) !important;
                overflow: hidden;
            }

            .inventory-filter-wrapper .table-search-input .form-control {
                min-width: 0 !important;
                width: 100% !important;
            }

            .inventory-filter-wrapper .table-filter-select {
                flex: 0 0 auto !important;
                min-width: 120px !important;
                max-width: 170px !important;
            }

            /* Inventory table responsive */
            #inventoryTable {
                min-width: 700px !important;
            }

            .inventory-table-wrapper {
                max-width: 100% !important;
                overflow-x: auto !important;
            }

            .revenue-orders-select {
                min-width: 90px;
            }

            .table-filter-select {
                min-width: 110px;
            }

            .table-search-input {
                width: 180px;
            }
        }

        /* Medium Devices (tablets, >= 768px) */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .card-header-responsive {
                padding: 18px 22px;
            }

            .category-revenue-card-responsive,
            .top-customers-card-responsive {
                height: 400px;
            }

            /* KPI Cards - 4 columns on tablet */
            .row-cols-md-3 > * {
                flex: 0 0 auto;
                width: 25%;
            }
        }

        /* Large Devices (desktops, >= 992px) */
        @media (min-width: 992px) {
            .category-revenue-card-responsive,
            .top-customers-card-responsive {
                height: 420px;
            }
        }

        /* Extra Large Devices (large desktops, >= 1200px) */
        @media (min-width: 1200px) {
            /* KPI Cards - 5 columns on xl */
            .row-cols-xl-5 > * {
                flex: 0 0 auto;
                width: 20%;
            }
        }

        /* Landscape Orientation */
        @media (max-width: 991.98px) and (orientation: landscape) {
            .category-revenue-card-responsive,
            .top-customers-card-responsive {
                height: auto !important;
                min-height: 300px;
            }

            #revenueOrdersChart {
                min-height: 250px !important;
            }
        }

        /* Print Styles */
        @media print {
            .card-header-responsive {
                padding: 12px;
            }

            .btn-group,
            .dropdown,
            .table-search-input,
            .table-filter-select {
                display: none;
            }

            .category-revenue-card-responsive,
            .top-customers-card-responsive {
                height: auto !important;
                page-break-inside: avoid;
            }
        }

        /* ========== TABLES RESPONSIVE IMPROVEMENTS ========== */

        /* Top Products Table */
        .top-products-table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
            width: 100%;
        }

        .top-products-table-wrapper::-webkit-scrollbar {
            height: 6px;
        }

        .top-products-table-wrapper::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        .top-products-table-wrapper::-webkit-scrollbar-thumb {
            background: linear-gradient(90deg, #cbd5e1 0%, #94a3b8 100%);
            border-radius: 3px;
        }

        .top-products-table-wrapper::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(90deg, #94a3b8 0%, #64748b 100%);
        }

        /* Đảm bảo bảng vừa với container - không scroll ngang nếu có thể */
        @media (min-width: 1200px) {
            .top-products-table-compact {
                table-layout: auto;
            }

            .top-products-th-name {
                max-width: none;
            }
        }

        /* Responsive adjustments */
        @media (max-width: 1199.98px) {
            .top-products-table-compact {
                font-size: 0.75rem;
            }

            .top-products-table-compact th,
            .top-products-table-compact td {
                padding: 6px 8px;
            }

            .product-thumbnail-top {
                width: 40px;
                height: 40px;
            }

            .top-products-th-image {
                min-width: 50px !important;
                width: 50px;
            }

            .top-products-th-name {
                min-width: 120px !important;
                max-width: 180px;
            }

            .top-products-th-quantity {
                min-width: 120px !important;
            }

            .top-products-th-revenue {
                min-width: 140px !important;
            }

            .top-products-th-contribution {
                min-width: 160px !important;
            }

            .top-products-th-conversion {
                min-width: 70px !important;
            }

            .top-products-th-stock {
                min-width: 60px !important;
            }

            .top-products-th-sub {
                min-width: 60px !important;
            }
        }

        /* Inventory Table */
        .inventory-table-wrapper {
            overflow-x: auto;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            max-height: 320px;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .inventory-table-wrapper::-webkit-scrollbar {
            height: 8px;
            width: 0;
        }

        .inventory-table-wrapper::-webkit-scrollbar:horizontal {
            height: 8px;
        }

        .inventory-table-wrapper::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .inventory-table-wrapper::-webkit-scrollbar-thumb {
            background: linear-gradient(90deg, #cbd5e1 0%, #94a3b8 100%);
            border-radius: 4px;
        }

        .inventory-table-wrapper::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(90deg, #94a3b8 0%, #64748b 100%);
        }

        /* Comments & Users Tables */
        .comments-table-wrapper,
        .users-table-wrapper {
            overflow-x: auto !important;
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .comments-table-wrapper::-webkit-scrollbar,
        .users-table-wrapper::-webkit-scrollbar {
            height: 8px;
            width: 0;
        }

        .comments-table-wrapper::-webkit-scrollbar:horizontal,
        .users-table-wrapper::-webkit-scrollbar:horizontal {
            height: 8px;
        }

        .comments-table-wrapper::-webkit-scrollbar-track,
        .users-table-wrapper::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .comments-table-wrapper::-webkit-scrollbar-thumb,
        .users-table-wrapper::-webkit-scrollbar-thumb {
            background: linear-gradient(90deg, #cbd5e1 0%, #94a3b8 100%);
            border-radius: 4px;
        }

        .comments-table-wrapper::-webkit-scrollbar-thumb:hover,
        .users-table-wrapper::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(90deg, #94a3b8 0%, #64748b 100%);
        }

        /* Table Responsive - Mobile */
        @media (max-width: 767.98px) {
            /* Hide some columns on mobile for better UX */
            #topProductsTable th:nth-child(5),
            #topProductsTable td:nth-child(5),
            #topProductsTable th:nth-child(6),
            #topProductsTable td:nth-child(6),
            #topProductsTable th:nth-child(7),
            #topProductsTable td:nth-child(7),
            #topProductsTable th:nth-child(8),
            #topProductsTable td:nth-child(8) {
                display: none;
            }

            /* Adjust table min-widths for mobile */
            .top-products-table-compact {
                min-width: 600px;
                font-size: 0.7rem;
            }

            .top-products-table-compact th,
            .top-products-table-compact td {
                padding: 5px 6px;
            }

            #inventoryTable {
                min-width: 500px;
            }

            #commentsTable {
                min-width: 700px;
            }

            #usersTable {
                min-width: 800px;
            }

            /* Table font sizes */
            .table {
                font-size: 0.875rem;
            }

            .table th,
            .table td {
                padding: 0.5rem 0.375rem;
            }

            /* Sticky headers */
            .table thead th {
                position: sticky;
                top: 0;
                background: white;
                z-index: 10;
                box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
            }

            body.dark .table thead th {
                background: #1f1f1f;
            }
        }

        /* Table Responsive - Small Mobile */
        @media (max-width: 575.98px) {
            .top-products-table-compact {
                min-width: 500px;
                font-size: 0.65rem;
            }

            .top-products-table-compact th,
            .top-products-table-compact td {
                padding: 4px 5px;
            }

            .product-thumbnail-top {
                width: 35px;
                height: 35px;
            }

            #inventoryTable {
                min-width: 400px;
            }

            #commentsTable {
                min-width: 600px;
            }

            #usersTable {
                min-width: 700px;
            }

            .table {
                font-size: 0.8rem;
            }

            .table th,
            .table td {
                padding: 0.4rem 0.25rem;
            }
        }

        /* Table Footer Responsive */
        @media (max-width: 767.98px) {
            .card-footer .d-flex {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 0.5rem;
            }

            .card-footer .d-flex > * {
                width: 100%;
            }
        }

        /* ========== APEXCHARTS MENU STYLES ========== */
        /* Fix menu background và text color để không bị trùng */
        .apexcharts-menu {
            background: #ffffff !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
            padding: 4px 0 !important;
            min-width: 160px !important;
            z-index: 10000 !important;
        }

        .apexcharts-menu-item {
            color: #374151 !important;
            background: transparent !important;
            padding: 8px 16px !important;
            font-size: 13px !important;
            font-weight: 400 !important;
            cursor: pointer !important;
            transition: background-color 0.2s ease !important;
            border: none !important;
            text-align: left !important;
            width: 100% !important;
            display: block !important;
        }

        .apexcharts-menu-item:hover {
            background: #f3f4f6 !important;
            color: #111827 !important;
        }

        .apexcharts-menu-item:active {
            background: #e5e7eb !important;
        }

        /* Dark mode support */
        body.dark .apexcharts-menu {
            background: #1f2937 !important;
            border-color: #374151 !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2) !important;
        }

        body.dark .apexcharts-menu-item {
            color: #e5e7eb !important;
        }

        body.dark .apexcharts-menu-item:hover {
            background: #374151 !important;
            color: #ffffff !important;
        }

        body.dark .apexcharts-menu-item:active {
            background: #4b5563 !important;
        }

        /* CUSTOM DASHBOARD FIXES */
        /* Dashboard specific section spacing */
        .dashboard-section {
            margin-bottom: clamp(1rem, 2vw, 1.5rem);
        }

        /* Fix KPI card responsiveness */
        .kpi-card-premium {
            min-height: 160px;
            display: flex;
            flex-direction: column;
        }

        .kpi-card-body-responsive {
            flex: 1;
            padding: clamp(1rem, 1.5vw, 1.5rem) !important;
        }

        .kpi-main-value {
            font-size: clamp(1.2rem, 2vw, 1.8rem) !important;
        }

        /* Fix Table responsiveness */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Toast notification fix */
        .toast-container {
            z-index: 9999;
        }
    </style>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Global Toast Function
        window.showToast = function(message, type = 'success') {
            // Check if toast container exists
            let toastContainer = document.querySelector('.toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                toastContainer.style.zIndex = '9999';
                document.body.appendChild(toastContainer);
            }

            const toastId = 'toast-' + Date.now();
            const bgColor = type === 'success' ? 'bg-success' : (type === 'error' ? 'bg-danger' : 'bg-info');

            const toastHtml = `
                <div id="${toastId}" class="toast align-items-center text-white ${bgColor} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;

            toastContainer.insertAdjacentHTML('beforeend', toastHtml);

            const toastElement = document.getElementById(toastId);
            // Use Bootstrap Toast if available, otherwise simple fallback
            if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
                const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
                toast.show();
                toastElement.addEventListener('hidden.bs.toast', () => {
                    toastElement.remove();
                });
            } else {
                // Simple fallback
                toastElement.classList.add('show');
                setTimeout(() => {
                    toastElement.classList.remove('show');
                    setTimeout(() => toastElement.remove(), 300);
                }, 3000);
            }
        };

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
            const gauge = document.querySelector('.half-gauge-value');
            const text = document.getElementById('halfPercent');

            if (gauge && text) {
                const totalLength = 314;
                const offset = totalLength - (percent / 100) * totalLength;

                gauge.style.strokeDashoffset = offset;
                text.innerText = percent + "%";
            }
        });

        let isDark = document.documentElement.classList.contains("dark");

        const textColor = () => isDark ? '#ffffff' : '#000000';
        const bgColor = () => isDark ? 'rgba(59,130,246,0.4)' : 'rgba(59,130,246,0.6)';
        const borderCol = 'rgb(59,130,246)';

        window.addEventListener("theme-changed", () => {
            isDark = document.documentElement.classList.contains("dark");

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

        if (orderGrowthCtx && typeof Chart !== 'undefined') {
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

        // Combined Chart: Doanh thu & Đơn hàng theo thời gian
        let revenueOrdersChart = null;
        const revenueOrdersChartEl = document.querySelector('#revenueOrdersChart');
        const revenueOrdersRangeButtons = document.querySelectorAll('[data-range]');
        const revenueOrdersGroupBySelect = document.getElementById('revenueOrdersGroupBy');
        const revenueOrdersCustomWrapper = document.querySelector('.revenue-orders-custom-range');
        const revenueOrdersFromInput = document.getElementById('revenueOrdersFrom');
        const revenueOrdersToInput = document.getElementById('revenueOrdersTo');
        const revenueOrdersApplyBtn = document.getElementById('revenueOrdersApply');

        let currentRange = '30';
        let currentGroupBy = 'day';

        function formatCurrencyVN(value) {
            return Math.round(value).toLocaleString('vi-VN') + ' ₫';
        }

        function formatNumber(value) {
            return Math.round(value).toLocaleString('vi-VN');
        }

        async function loadRevenueOrdersChart(range = '7', groupBy = 'day') {
            if (!revenueOrdersChartEl) return;

            // Destroy existing chart before showing loading state to prevent DOM detachment issues
            if (revenueOrdersChart) {
                try {
                    revenueOrdersChart.destroy();
                } catch (e) {
                    console.warn('Error destroying chart:', e);
                }
                revenueOrdersChart = null;
            }

            // Show loading skeleton
            revenueOrdersChartEl.innerHTML = '<div class="d-flex align-items-center justify-content-center" style="min-height: 380px;"><div class="spinner-border text-primary" role="status"></div></div>';

            try {
                const baseUrl = "{{ url('/admin/api/dashboard/revenue-orders-chart') }}";
                const params = new URLSearchParams({ range, group_by: groupBy });

                if (range === 'custom') {
                    const from = revenueOrdersFromInput.value;
                    const to = revenueOrdersToInput.value;
                    if (from) params.append('from', from);
                    if (to) params.append('to', to);

                    // Validate custom date range
                    if (from && to && new Date(from) > new Date(to)) {
                        alert('Ngày bắt đầu phải nhỏ hơn hoặc bằng ngày kết thúc!');
                        return;
                    }
                }

                const response = await fetch(`${baseUrl}?${params.toString()}`);

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('API Error:', response.status, errorText);
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                // Validate data
                if (!data) {
                    throw new Error('No data received from server');
                }

                if (!Array.isArray(data.labels) || !Array.isArray(data.revenue) || !Array.isArray(data.orders)) {
                    console.error('Invalid data format:', data);
                    throw new Error('Invalid data format from server. Expected: labels, revenue, orders arrays');
                }

                const { labels, revenue, orders, total_revenue, total_orders, change_revenue, change_orders } = data;

                // Handle empty data - allow empty arrays but show message
                if (labels.length === 0) {
                    revenueOrdersChartEl.innerHTML = '<div class="alert alert-info m-3 text-center">Không có dữ liệu trong thời gian này. Hãy chọn mốc khác.</div>';
                    return;
                }

                // Ensure arrays have same length
                if (labels.length !== revenue.length || labels.length !== orders.length) {
                    console.error('Array length mismatch:', {
                        labels: labels.length,
                        revenue: revenue.length,
                        orders: orders.length
                    });
                    throw new Error('Data arrays length mismatch');
                }

                // Update summary footer
                document.getElementById('summaryTotalRevenue').textContent = formatCurrencyVN(total_revenue);
                document.getElementById('summaryTotalOrders').textContent = formatNumber(total_orders) + ' đơn';

                // Update revenue change badge
                const revenueChangeEl = document.getElementById('summaryRevenueChange');
                revenueChangeEl.textContent = (change_revenue > 0 ? '↑ ' : change_revenue < 0 ? '↓ ' : '') + Math.abs(change_revenue) + '%';
                revenueChangeEl.className = 'badge ' + (change_revenue > 0 ? 'bg-success' : change_revenue < 0 ? 'bg-danger' : 'bg-secondary');

                // Update orders change badge
                const ordersChangeEl = document.getElementById('summaryOrdersChange');
                ordersChangeEl.textContent = (change_orders > 0 ? '↑ ' : change_orders < 0 ? '↓ ' : '') + Math.abs(change_orders) + '%';
                ordersChangeEl.className = 'badge ' + (change_orders > 0 ? 'bg-success' : change_orders < 0 ? 'bg-danger' : 'bg-secondary');

                const options = {
                    chart: {
                        type: 'line',
                        height: 380,

                        toolbar: {
                            show: true,
                            tools: {
                                download: true,
                                selection: false, // Tắt selection
                                zoom: true,
                                zoomin: true,
                                zoomout: true,
                                pan: false, // Tắt panning

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
                    series: [
                        {
                            name: 'Đơn hàng',
                            type: 'column',
                            data: orders,
                            yAxisIndex: 0, // Left Y-axis
                        },
                        {
                            name: 'Doanh thu',
                            type: 'line',
                            data: revenue,
                            yAxisIndex: 1, // Right Y-axis
                        },
                    ],
                    stroke: {
                        width: [0, 2],
                        curve: ['straight', 'smooth'],
                    },
                    colors: ['#93C5FD', '#1D4ED8'], // Bar: xanh nhạt, Line: xanh đậm
                    fill: {
                        type: ['solid', 'solid'],
                        opacity: [0.6, 1],
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            columnWidth: '60%',
                        },
                    },
                    xaxis: {
                        categories: labels,
                        labels: {
                            style: {
                                colors: '#6b7280',
                                fontSize: '11px',
                            },
                        },
                    },
                    yaxis: [
                        {
                            // Left Y-axis: Đơn hàng
                            title: {
                                text: 'Số lượng đơn',
                                style: {
                                    color: '#93C5FD',
                                    fontSize: '12px',
                                },
                            },
                        labels: {
                                style: {
                                    colors: '#93C5FD',
                                },
                            formatter: function (value) {
                                    return Math.round(value);
                                },
                            },
                            opposite: false,
                            min: 0, // Bắt đầu từ 0
                        },
                        {
                            // Right Y-axis: Doanh thu
                            title: {
                                text: 'Doanh thu (VNĐ)',
                                style: {
                                    color: '#1D4ED8',
                                    fontSize: '12px',
                                },
                            },
                            labels: {
                                style: {
                                    colors: '#1D4ED8',
                                },
                                formatter: function (value) {
                                    if (value >= 1000000000) {
                                        return (value / 1000000000).toFixed(1) + 'B';
                                    } else if (value >= 1000000) {
                                        return (value / 1000000).toFixed(1) + 'M';
                                    } else if (value >= 1000) {
                                        return (value / 1000).toFixed(1) + 'K';
                                    }
                                    return Math.round(value).toLocaleString('vi-VN');
                                },
                            },
                            opposite: true,
                            min: 0, // Bắt đầu từ 0
                        },
                    ],
                    tooltip: {
                        shared: true,
                        intersect: false,
                        backgroundColor: '#ffffff',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        textColor: '#1f2937',
                        style: {
                            fontSize: '13px',
                            fontFamily: 'inherit',
                        },
                        custom: function ({ series, seriesIndex, dataPointIndex, w }) {
                            const label = w.globals.categoryLabels[dataPointIndex];
                            const ordersValue = series[0][dataPointIndex] ?? 0;
                            const revenueValue = series[1][dataPointIndex] ?? 0;

                            return `
                                <div style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); min-width: 200px;">
                                    <div style="font-weight: 600; color: #1f2937; margin-bottom: 10px; font-size: 14px; border-bottom: 1px solid #e5e7eb; padding-bottom: 8px;">
                                        ${label}
                                    </div>
                                    <div style="margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                                        <span style="display: inline-block; width: 12px; height: 12px; background-color: #93C5FD; border-radius: 2px;"></span>
                                        <span style="color: #6b7280; font-size: 12px;">Đơn hàng:</span>
                                        <strong style="color: #1f2937; font-size: 13px;">${ordersValue} đơn</strong>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span style="display: inline-block; width: 12px; height: 12px; background-color: #1D4ED8; border-radius: 2px;"></span>
                                        <span style="color: #6b7280; font-size: 12px;">Doanh thu:</span>
                                        <strong style="color: #1f2937; font-size: 13px;">${formatCurrencyVN(revenueValue)}</strong>
                                    </div>
                                </div>
                            `;
                        },
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'left',
                        markers: {
                            width: 12,
                            height: 12,
                            radius: 2,
                        },

                    },
                    grid: {
                        borderColor: '#e5e7eb',
                        strokeDashArray: 4,
                        xaxis: {
                            lines: {
                                show: false,
                            },
                        },
                        yaxis: {
                            lines: {
                                show: true,
                            },
                        },
                    },
                };

                // Check if ApexCharts is loaded
                if (typeof ApexCharts === 'undefined') {
                    throw new Error('ApexCharts library is not loaded. Please check if the script is included.');
                }

                // Clear loading state
                revenueOrdersChartEl.innerHTML = '';

                if (revenueOrdersChart) {
                    revenueOrdersChart.updateOptions(options, true, true);
                } else {
                    revenueOrdersChart = new ApexCharts(revenueOrdersChartEl, options);
                    revenueOrdersChart.render().then(() => {
                    }).catch((err) => {
                        console.error('Chart render error:', err);
                        revenueOrdersChartEl.innerHTML = '<div class="alert alert-danger m-3">Lỗi khi render biểu đồ. Vui lòng thử lại.</div>';
                    });
                }
            } catch (e) {
                console.error('Failed to load revenue orders chart', e);
                revenueOrdersChartEl.innerHTML = '<div class="alert alert-danger m-3">' +
                    '<strong>Lỗi:</strong> ' + (e.message || 'Đã xảy ra lỗi khi tải biểu đồ') + '<br>' +
                    '<small>Vui lòng mở Console (F12) để xem chi tiết lỗi.</small><br>' +
                    '<button class="btn btn-sm btn-primary mt-2" onclick="loadRevenueOrdersChart(currentRange, currentGroupBy)">Thử lại</button>' +
                    '</div>';
            }
        }

        if (revenueOrdersChartEl) {
            // Đợi ApexCharts load xong trước khi khởi tạo
            function initRevenueOrdersChart() {
                if (typeof ApexCharts !== 'undefined') {
                    // Khởi tạo mặc định với 30 ngày
                    loadRevenueOrdersChart('30', 'day');
                } else {
                    // Retry sau 100ms nếu ApexCharts chưa load
                    setTimeout(initRevenueOrdersChart, 100);
                }
            }

            // Kiểm tra nếu ApexCharts đã load
            if (typeof ApexCharts !== 'undefined') {
                initRevenueOrdersChart();
            } else {
                // Đợi DOMContentLoaded hoặc window load
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initRevenueOrdersChart);
                } else {
                    window.addEventListener('load', initRevenueOrdersChart);
                    // Fallback: thử sau 1 giây
                    setTimeout(initRevenueOrdersChart, 1000);
                }
            }

            // Date range buttons
            revenueOrdersRangeButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    revenueOrdersRangeButtons.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    currentRange = btn.getAttribute('data-range');

                    if (currentRange === 'custom') {
                        // Hiện thanh chọn thời gian khi click "Tùy chọn"
                        revenueOrdersCustomWrapper.style.display = 'flex';
                        revenueOrdersCustomWrapper.style.visibility = 'visible';
                        // Focus vào input đầu tiên
                        if (revenueOrdersFromInput) {
                            setTimeout(() => revenueOrdersFromInput.focus(), 100);
                        }
                    } else {
                        // Ẩn thanh chọn thời gian khi click các nút khác
                        revenueOrdersCustomWrapper.style.display = 'none';
                        revenueOrdersCustomWrapper.style.visibility = 'hidden';
                        // Load biểu đồ với range mới
                        loadRevenueOrdersChart(currentRange, currentGroupBy);
                    }
                });
            });

            // Group by dropdown
            if (revenueOrdersGroupBySelect) {
                revenueOrdersGroupBySelect.addEventListener('change', (e) => {
                    currentGroupBy = e.target.value;
                    loadRevenueOrdersChart(currentRange, currentGroupBy);
                });
            }

            // Custom date range apply
            if (revenueOrdersApplyBtn) {
                revenueOrdersApplyBtn.addEventListener('click', () => {
                    loadRevenueOrdersChart('custom', currentGroupBy);
                });
            }
        }

        // Biểu đồ tỷ lệ trạng thái đơn hàng với bảng chi tiết
        const orderStatusCtx = document.getElementById('orderStatusChart');
        const orderStatusTableBody = document.getElementById('orderStatusTableBody');
        const orderStatusTotalEl = document.getElementById('orderStatusTotal');
        const orderStatusPeriodEl = document.getElementById('orderStatusPeriod');
        const orderStatusDateRangeEl = document.getElementById('orderStatusDateRange');
        const orderStatusRangeButtons = document.querySelectorAll('[data-status-range]');

        let orderStatusChart = null;
        let currentStatusRange = '30';
        let chartEventListenersAdded = false; // Flag để tránh duplicate event listeners

        async function loadOrderStatusRatio(range = '7') {
            if (!orderStatusCtx || !orderStatusTableBody) return;

            // Show loading
            orderStatusTableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                        <span class="ms-2">Đang tải...</span>
                    </td>
                </tr>
            `;

            try {
                const baseUrl = "{{ url('/admin/api/dashboard/order-status-ratio') }}";
                const response = await fetch(`${baseUrl}?range=${range}`);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                // Kiểm tra dữ liệu hợp lệ
                if (!result || typeof result !== 'object') {
                    throw new Error('Invalid response format');
                }

                const { total_orders, from, to, data } = result;

                // Kiểm tra data có tồn tại và là array
                if (!Array.isArray(data)) {
                    throw new Error('Data is not an array');
                }

                // Update footer
                if (orderStatusTotalEl) {
                    orderStatusTotalEl.textContent = (total_orders || 0).toLocaleString('vi-VN');
                }
                if (orderStatusPeriodEl && from && to) {
                const fromDate = new Date(from).toLocaleDateString('vi-VN');
                const toDate = new Date(to).toLocaleDateString('vi-VN');
                orderStatusPeriodEl.textContent = `${fromDate} - ${toDate}`;
                }

                // Update date range text
                if (orderStatusDateRangeEl) {
                const rangeTexts = {
                    'today': 'Hôm nay',
                    '7': '7 ngày gần nhất',
                    '30': '30 ngày gần nhất',
                    '90': '90 ngày gần nhất',
                    'month': 'Tháng này',
                };
                orderStatusDateRangeEl.textContent = `(${rangeTexts[range] || '7 ngày gần nhất'})`;
                }

                // Render bảng (có cột xu hướng)
                if (data.length === 0) {
                    orderStatusTableBody.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">Không có đơn hàng phát sinh</td>
                        </tr>
                    `;

                    // Render biểu đồ trắng toàn vẹn khi không có dữ liệu
                    const emptyChartOptions = {
                        type: 'doughnut',
                        data: {
                            labels: ['Không có dữ liệu'],
                            datasets: [{
                                data: [1],
                                backgroundColor: ['#F3F4F6'],
                                borderWidth: 2,
                                borderColor: '#ffffff',
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '65%',
                            plugins: {
                                legend: {
                                    display: false,
                                },
                                tooltip: {
                                    enabled: true,
                                    backgroundColor: 'rgba(0, 0, 0, 0.9)',
                                    titleColor: '#ffffff',
                                    bodyColor: '#ffffff',
                                    borderColor: 'rgba(255, 255, 255, 0.2)',
                                    borderWidth: 1,
                                    padding: {
                                        top: 12,
                                        right: 16,
                                        bottom: 12,
                                        left: 16
                                    },
                                    titleFont: {
                                        size: 14,
                                        weight: '600',
                                        family: "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
                                    },
                                    bodyFont: {
                                        size: 13,
                                        weight: '400',
                                        family: "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
                                    },
                                    cornerRadius: 8,
                                    displayColors: false,
                                    callbacks: {
                                        label: function(context) {
                                            return 'Không có đơn hàng';
                                        },
                                        title: function() {
                                            return '';
                                        }
                                    }
                                }
                            },
                            interaction: {
                                intersect: true,
                                mode: 'point'
                            }
                        }
                    };

                    if (orderStatusChart) {
                        // Đảm bảo đồng bộ dữ liệu
                        orderStatusChart.data.labels = ['Không có dữ liệu'];
                        if (orderStatusChart.data.datasets && orderStatusChart.data.datasets[0]) {
                            orderStatusChart.data.datasets[0].data = [1];
                            orderStatusChart.data.datasets[0].backgroundColor = ['#F3F4F6'];
                        }
                        // Xóa statusData khi không có dữ liệu
                        orderStatusChart.data._statusData = [];
                        // Cập nhật tooltip options để hiển thị "Không có đơn hàng"
                        if (orderStatusChart.options && orderStatusChart.options.plugins && orderStatusChart.options.plugins.tooltip) {
                            orderStatusChart.options.plugins.tooltip.enabled = true;
                            orderStatusChart.options.plugins.tooltip.backgroundColor = 'rgba(0, 0, 0, 0.9)';
                            orderStatusChart.options.plugins.tooltip.titleColor = '#ffffff';
                            orderStatusChart.options.plugins.tooltip.bodyColor = '#ffffff';
                            orderStatusChart.options.plugins.tooltip.borderColor = 'rgba(255, 255, 255, 0.2)';
                            orderStatusChart.options.plugins.tooltip.borderWidth = 1;
                            orderStatusChart.options.plugins.tooltip.padding = {
                                top: 12,
                                right: 16,
                                bottom: 12,
                                left: 16
                            };
                            orderStatusChart.options.plugins.tooltip.titleFont = {
                                size: 14,
                                weight: '600',
                                family: "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
                            };
                            orderStatusChart.options.plugins.tooltip.bodyFont = {
                                size: 13,
                                weight: '400',
                                family: "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
                            };
                            orderStatusChart.options.plugins.tooltip.cornerRadius = 8;
                            orderStatusChart.options.plugins.tooltip.displayColors = false;
                            orderStatusChart.options.plugins.tooltip.callbacks = {
                                label: function(context) {
                                    return 'Không có đơn hàng';
                                },
                                title: function() {
                                    return '';
                                }
                            };
                        }
                        orderStatusChart.update();
                    } else {
                        if (orderStatusCtx) {
                            if (typeof Chart !== 'undefined') {
                                orderStatusChart = new Chart(orderStatusCtx, emptyChartOptions);
                                orderStatusChart.data._statusData = [];

                                // Thêm event listener cho hover trên canvas (chỉ thêm 1 lần)
                                if (!chartEventListenersAdded) {
                                    orderStatusCtx.addEventListener('mousemove', handleChartHover);
                                    orderStatusCtx.addEventListener('mouseleave', handleChartLeave);
                                    chartEventListenersAdded = true;
                                }
                            }
                        }
                    }
                    return;
                }

                orderStatusTableBody.innerHTML = data.map(item => {
                    // Validate và set giá trị mặc định cho các trường
                    const status = item.status || '';
                    const label = item.label || 'N/A';
                    const color = item.color || '#6B7280';
                    const count = item.count || 0;
                    const ratio = item.ratio || 0;
                    const trend = item.trend || 0;

                    const trendIcon = trend > 0 ? '↑' : trend < 0 ? '↓' : '→';
                    const trendColor = trend > 0 ? '#10B981' : trend < 0 ? '#EF4444' : '#6B7280';
                    const trendText = trend !== 0 ? `${trendIcon} ${Math.abs(trend)}%` : '→ 0%';

                    return `
                        <tr class="order-status-row" data-status="${status}">
                            <td class="order-status-color">
                                <span class="order-status-dot" style="background-color: ${color};"></span>
                            </td>
                            <td class="order-status-label">${label}</td>
                            <td class="order-status-count text-end">${count.toLocaleString('vi-VN')}</td>
                            <td class="order-status-ratio text-end">${ratio}%</td>
                            <td class="order-status-trend text-end">
                                <span style="color: ${trendColor}; font-weight: 500;">${trendText}</span>
                            </td>
                        </tr>
                    `;
                }).join('');

                // Render donut chart
                const labels = data.map(item => item.label || 'N/A');
                const chartData = data.map(item => item.count || 0);
                const colors = data.map(item => item.color || '#6B7280');

                const chartOptions = {
                    type: 'doughnut',
                    data: {
                        labels,
                        datasets: [{
                            data: chartData,
                            backgroundColor: colors,
                            borderWidth: 2,

                            borderColor: '#ffffff',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: {
                                display: false,
                            },
                            tooltip: {
                                enabled: true,
                                backgroundColor: 'rgba(0, 0, 0, 0.9)',
                                titleColor: '#ffffff',
                                bodyColor: '#ffffff',
                                borderColor: 'rgba(255, 255, 255, 0.2)',
                                borderWidth: 1,
                                padding: {
                                    top: 12,
                                    right: 16,
                                    bottom: 12,
                                    left: 16
                                },
                                titleFont: {
                                    size: 14,
                                    weight: '600',
                                    family: "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
                                },
                                bodyFont: {
                                    size: 13,
                                    weight: '400',
                                    family: "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
                                },
                                cornerRadius: 8,
                                displayColors: true,
                                boxPadding: 8,
                                boxWidth: 12,
                                boxHeight: 12,
                                usePointStyle: true,
                                callbacks: {
                                    title: function(context) {
                                        return context[0].label || '';
                                    },
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((sum, v) => sum + v, 0);
                                        const value = context.raw ?? 0;
                                        const percent = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return `Số lượng: ${value.toLocaleString('vi-VN')} đơn`;
                                    },
                                    afterLabel: function(context) {
                                        const total = context.dataset.data.reduce((sum, v) => sum + v, 0);
                                        const value = context.raw ?? 0;
                                        const percent = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return `Tỷ lệ: ${percent}%`;
                                    }
                                }
                            }
                        },
                        interaction: {
                            intersect: true,
                            mode: 'point'
                        }
                    }
                };

                // Lưu data vào biến để dùng trong hover (đảm bảo đồng bộ)
                const statusDataForHover = [...data]; // Tạo copy để tránh reference issues

                // Hàm xử lý hover trên chart
                function handleChartHover(e) {
                    const chart = orderStatusChart;
                    if (!chart) return;

                    const activeElements = chart.getElementsAtEventForMode(e, 'point', { intersect: true }, false);
                    // Luôn sử dụng data từ chart để đảm bảo đồng bộ
                    const statusData = chart.data._statusData || [];

                    if (activeElements.length > 0) {
                        const index = activeElements[0].index;
                        if (statusData && statusData[index]) {
                            const status = statusData[index].status;

                            // Highlight row tương ứng
                            document.querySelectorAll('.order-status-row').forEach(row => {
                                if (row.dataset.status === status) {
                                    row.style.backgroundColor = '#f8f9fa';
                                    row.style.transition = 'background-color 0.2s';
                                    row.style.fontWeight = '600';
                                } else {
                                    row.style.backgroundColor = '';
                                    row.style.fontWeight = '';
                                }
                            });
                        }
            } else {
                        // Reset tất cả rows
                        document.querySelectorAll('.order-status-row').forEach(row => {
                            row.style.backgroundColor = '';
                            row.style.fontWeight = '';
                        });
                    }
                }

                function handleChartLeave() {
                    // Reset tất cả rows khi rời khỏi chart
                    document.querySelectorAll('.order-status-row').forEach(row => {
                        row.style.backgroundColor = '';
                        row.style.fontWeight = '';
                    });
                }

                if (orderStatusChart) {
                    // Đảm bảo đồng bộ dữ liệu giữa chart và table
                    orderStatusChart.data.labels = [...labels]; // Tạo copy
                    if (orderStatusChart.data.datasets && orderStatusChart.data.datasets[0]) {
                        orderStatusChart.data.datasets[0].data = [...chartData]; // Tạo copy
                        orderStatusChart.data.datasets[0].backgroundColor = [...colors]; // Tạo copy
                    }
                    // Cập nhật statusData để đồng bộ với chart data
                    orderStatusChart.data._statusData = [...statusDataForHover];

                    // Cập nhật tooltip options để hiển thị đúng khi có dữ liệu
                    if (orderStatusChart.options && orderStatusChart.options.plugins && orderStatusChart.options.plugins.tooltip) {
                        orderStatusChart.options.plugins.tooltip.enabled = true;
                        orderStatusChart.options.plugins.tooltip.backgroundColor = 'rgba(0, 0, 0, 0.9)';
                        orderStatusChart.options.plugins.tooltip.titleColor = '#ffffff';
                        orderStatusChart.options.plugins.tooltip.bodyColor = '#ffffff';
                        orderStatusChart.options.plugins.tooltip.borderColor = 'rgba(255, 255, 255, 0.2)';
                        orderStatusChart.options.plugins.tooltip.borderWidth = 1;
                        orderStatusChart.options.plugins.tooltip.padding = {
                            top: 12,
                            right: 16,
                            bottom: 12,
                            left: 16
                        };
                        orderStatusChart.options.plugins.tooltip.titleFont = {
                            size: 14,
                            weight: '600',
                            family: "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
                        };
                        orderStatusChart.options.plugins.tooltip.bodyFont = {
                            size: 13,
                            weight: '400',
                            family: "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
                        };
                        orderStatusChart.options.plugins.tooltip.cornerRadius = 8;
                        orderStatusChart.options.plugins.tooltip.displayColors = true;
                        orderStatusChart.options.plugins.tooltip.boxPadding = 8;
                        orderStatusChart.options.plugins.tooltip.boxWidth = 12;
                        orderStatusChart.options.plugins.tooltip.boxHeight = 12;
                        orderStatusChart.options.plugins.tooltip.usePointStyle = true;
                        orderStatusChart.options.plugins.tooltip.callbacks = {
                            title: function(context) {
                                return context[0].label || '';
                            },
                            label: function(context) {
                                const total = context.dataset.data.reduce((sum, v) => sum + v, 0);
                                const value = context.raw ?? 0;
                                const percent = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `Số lượng: ${value.toLocaleString('vi-VN')} đơn`;
                            },
                            afterLabel: function(context) {
                                const total = context.dataset.data.reduce((sum, v) => sum + v, 0);
                                const value = context.raw ?? 0;
                                const percent = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `Tỷ lệ: ${percent}%`;
                            }
                        };
                    }

                    orderStatusChart.update();
                } else {
                    if (orderStatusCtx && labels.length > 0 && chartData.length > 0 && typeof Chart !== 'undefined') {
                    orderStatusChart = new Chart(orderStatusCtx, chartOptions);
                        orderStatusChart.data._statusData = [...statusDataForHover]; // Tạo copy để đồng bộ

                    // Thêm event listener cho hover trên canvas (chỉ thêm 1 lần)
                        if (!chartEventListenersAdded) {
                    orderStatusCtx.addEventListener('mousemove', handleChartHover);
                    orderStatusCtx.addEventListener('mouseleave', handleChartLeave);
                            chartEventListenersAdded = true;
                        }
                    }
                }

                // Custom tooltip implementation
                let tooltipElement = null;

                function createTooltip(text, x, y) {
                    if (!tooltipElement) {
                        tooltipElement = document.createElement('div');
                        tooltipElement.className = 'order-status-tooltip';
                        tooltipElement.style.cssText = `
                            position: absolute;
                            background: rgba(0, 0, 0, 0.95);
                            color: white;
                            padding: 14px 18px;
                            border-radius: 10px;
                            font-size: 13px;
                            font-weight: 400;
                            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                            pointer-events: none;
                            z-index: 1000;
                            white-space: normal;
                            max-width: 300px;
                            min-width: 150px;
                            word-wrap: break-word;
                            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3), 0 2px 4px rgba(0, 0, 0, 0.2);
                            border: 1px solid rgba(255, 255, 255, 0.15);
                            line-height: 1.5;
                            backdrop-filter: blur(10px);
                            transition: opacity 0.2s ease, transform 0.2s ease;
                        `;
                        document.body.appendChild(tooltipElement);
                    }
                    tooltipElement.innerHTML = text;
                    tooltipElement.style.left = (x + 15) + 'px';
                    tooltipElement.style.top = (y - tooltipElement.offsetHeight / 2) + 'px';
                    tooltipElement.style.display = 'block';
                    tooltipElement.style.opacity = '0';
                    tooltipElement.style.transform = 'translateY(-5px)';

                    // Trigger animation
                    setTimeout(() => {
                        if (tooltipElement) {
                            tooltipElement.style.opacity = '1';
                            tooltipElement.style.transform = 'translateY(0)';
                        }
                    }, 10);
                }

                function hideTooltip() {
                    if (tooltipElement) {
                        tooltipElement.style.opacity = '0';
                        tooltipElement.style.transform = 'translateY(-5px)';
                        setTimeout(() => {
                    if (tooltipElement) {
                        tooltipElement.style.display = 'none';
                            }
                        }, 200);
                    }
                }

                // Click vào row để navigate và hover effect
                // Xóa event listeners cũ trước khi thêm mới để tránh duplicate
                const existingRows = document.querySelectorAll('.order-status-row');
                existingRows.forEach(row => {
                    // Clone node để xóa tất cả event listeners
                    const newRow = row.cloneNode(true);
                    row.parentNode.replaceChild(newRow, row);
                });

                document.querySelectorAll('.order-status-row').forEach((row, index) => {
                    // Click để navigate
                    row.addEventListener('click', function() {
                        const status = this.dataset.status;
                        // Navigate to orders list with status filter
                        window.location.href = `{{ route('admin.orders.list') }}?order_status=${status}&order_filter_type=date_range&order_start_date=${from}&order_end_date=${to}`;
                    });

                    // Hover vào row để highlight trên chart và hiển thị tooltip
                    row.addEventListener('mouseenter', function(e) {
                        const tooltipText = this.dataset.tooltipText;
                        if (tooltipText) {
                            const rect = this.getBoundingClientRect();
                            createTooltip(tooltipText, rect.right, rect.top + rect.height / 2);
                        }

                        // Highlight segment trên chart - sử dụng data từ chart để đảm bảo đồng bộ
                        if (orderStatusChart && orderStatusChart.data._statusData) {
                            const status = this.dataset.status;
                            const dataIndex = orderStatusChart.data._statusData.findIndex(item => item && item.status === status);
                            if (dataIndex !== -1) {
                                orderStatusChart.setActiveElements([{ datasetIndex: 0, index: dataIndex }]);
                                orderStatusChart.update('none');
                            }
                        }
                    });

                    row.addEventListener('mousemove', function(e) {
                        const tooltipText = this.dataset.tooltipText;
                        if (tooltipText && tooltipElement) {
                            const rect = this.getBoundingClientRect();
                            const tooltipWidth = tooltipElement.offsetWidth || 200;
                            const tooltipHeight = tooltipElement.offsetHeight || 50;

                            // Đảm bảo tooltip không bị tràn ra ngoài màn hình
                            let left = rect.right + 15;
                            let top = rect.top + rect.height / 2 - tooltipHeight / 2;

                            // Kiểm tra nếu tooltip tràn ra bên phải
                            if (left + tooltipWidth > window.innerWidth) {
                                left = rect.left - tooltipWidth - 15;
                            }

                            // Kiểm tra nếu tooltip tràn ra phía trên
                            if (top < 0) {
                                top = 10;
                            }

                            // Kiểm tra nếu tooltip tràn ra phía dưới
                            if (top + tooltipHeight > window.innerHeight) {
                                top = window.innerHeight - tooltipHeight - 10;
                            }

                            tooltipElement.style.left = left + 'px';
                            tooltipElement.style.top = top + 'px';
                        }
                    });

                    row.addEventListener('mouseleave', function() {
                        hideTooltip();

                        // Reset chart highlight
                        if (orderStatusChart) {
                            orderStatusChart.setActiveElements([]);
                            orderStatusChart.update('none');
                        }
                    });
                });

            } catch (e) {
                console.error('Failed to load order status ratio', e);
                const errorMessage = e.message || 'Đã xảy ra lỗi khi tải dữ liệu';
                orderStatusTableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-danger py-3">
                            ${errorMessage}
                            <br>
                            <button class="btn btn-sm btn-primary mt-2" onclick="loadOrderStatusRatio('${range}')">Thử lại</button>
                        </td>
                    </tr>
                `;
            }
        }

        // Initialize và xử lý date range buttons
        if (orderStatusCtx && orderStatusRangeButtons.length > 0) {
            // Load mặc định với 30 ngày
            currentStatusRange = '30';
            loadOrderStatusRatio('30');

            // Date range buttons
            orderStatusRangeButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    orderStatusRangeButtons.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    currentStatusRange = btn.getAttribute('data-status-range');
                    loadOrderStatusRatio(currentStatusRange);
                });
            });
        }

        // ========== CATEGORY REVENUE CHART ==========
        const categoryRevenueCtx = document.getElementById('categoryRevenueChart');
        const categoryDateRangePicker = document.getElementById('categoryDateRangePicker');
        const categoryDateFrom = document.getElementById('categoryDateFrom');
        const categoryDateTo = document.getElementById('categoryDateTo');
        const categoryDateRangeApply = document.getElementById('categoryDateRangeApply');
        const categoryRangeButtons = document.querySelectorAll('[data-category-range]');
        const categoryTotalRevenueEstimatedEl = document.getElementById('categoryTotalRevenueEstimated');
        const categoryTotalRevenueActualEl = document.getElementById('categoryTotalRevenueActual');
        const categoryTopCategoryEstimatedEl = document.getElementById('categoryTopCategoryEstimated');
        const categoryTopCategoryActualEl = document.getElementById('categoryTopCategoryActual');

        let categoryRevenueChart = null;
        let currentCategoryRange = '30';

        async function loadCategoryRevenue(range = '30') {
            if (!categoryRevenueCtx) return;

            try {
                let url = "{{ url('/admin/api/dashboard/category-revenue') }}";
                if (range === 'custom') {
                    const from = categoryDateFrom.value;
                    const to = categoryDateTo.value;
                    url += `?range=custom&from=${from}&to=${to}`;
                } else {
                    url += `?range=${range}`;
                }

                const response = await fetch(url);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success && result.data) {
                    const {
                        categories,
                        estimated_values,
                        actual_values,
                        estimated_percent,
                        actual_percent,
                        total_estimated,
                        total_actual
                    } = result.data;

                    // Update footer
                    categoryTotalRevenueEstimatedEl.textContent = new Intl.NumberFormat('vi-VN').format(total_estimated) + ' ₫';
                    categoryTotalRevenueActualEl.textContent = new Intl.NumberFormat('vi-VN').format(total_actual) + ' ₫';

                    // Tìm danh mục bán chạy nhất (theo dự tính)
                    const topEstimatedIndex = estimated_values.indexOf(Math.max(...estimated_values));
                    if (topEstimatedIndex >= 0 && categories[topEstimatedIndex]) {
                        categoryTopCategoryEstimatedEl.textContent = `Dự tính: ${categories[topEstimatedIndex]} (${estimated_percent[topEstimatedIndex]}%)`;
                    }

                    // Tìm danh mục bán chạy nhất (theo thực tế)
                    const topActualIndex = actual_values.indexOf(Math.max(...actual_values));
                    if (topActualIndex >= 0 && categories[topActualIndex]) {
                        categoryTopCategoryActualEl.textContent = `Thực tế: ${categories[topActualIndex]} (${actual_percent[topActualIndex]}%)`;
                    }

                    // Render bar chart ngang với 2 datasets: Dự tính và Thực tế
                    const chartOptions = {
                        type: 'bar',
                        data: {
                            labels: categories,
                            datasets: [
                                {
                                    label: 'Doanh thu dự tính',
                                    data: estimated_values,
                                    backgroundColor: '#3B82F6',
                                    borderRadius: 4,
                                },
                                {
                                    label: 'Doanh thu thực tế',
                                    data: actual_values,
                                    backgroundColor: '#10B981',
                                    borderRadius: 4,
                                }
                            ]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const value = context.parsed.x;
                                            const index = context.dataIndex;
                                            const datasetLabel = context.dataset.label;
                                            let percentValue = 0;

                                            if (datasetLabel === 'Doanh thu dự tính') {
                                                percentValue = estimated_percent[index] || 0;
                                            } else {
                                                percentValue = actual_percent[index] || 0;
                                            }

                                            return [
                                                `Danh mục: ${categories[index]}`,
                                                `${datasetLabel}: ${new Intl.NumberFormat('vi-VN').format(value)} đ`,
                                                `Tỉ lệ: ${percentValue}%`
                                            ];
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    grid: {
                                        display: true,
                                        color: 'rgba(0, 0, 0, 0.05)'
                                    },
                                    ticks: {
                                        callback: function(value) {
                                            if (value >= 1000000) {
                                                return (value / 1000000).toFixed(1) + 'M';
                                            } else if (value >= 1000) {
                                                return (value / 1000).toFixed(0) + 'K';
                                            }
                                            return value;
                                        }
                                    }
                                },
                                y: {
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    };

                    if (categoryRevenueChart) {
                        categoryRevenueChart.data.labels = categories;
                        categoryRevenueChart.data.datasets[0].data = estimated_values;
                        categoryRevenueChart.data.datasets[1].data = actual_values;
                        categoryRevenueChart.update();
                    } else if (typeof Chart !== 'undefined') {
                        categoryRevenueChart = new Chart(categoryRevenueCtx, chartOptions);
                    }
                }
            } catch (e) {
                console.error('Error loading category revenue:', e);
                if (typeof window.showToast === 'function') {
                    window.showToast('Lỗi tải dữ liệu doanh thu danh mục: ' + e.message, 'error');
                }
                if (categoryRevenueCtx) {
                    const parent = categoryRevenueCtx.parentElement;
                    if (parent) {
                        parent.innerHTML = `
                            <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                                <i class="bi bi-exclamation-triangle fs-1 mb-2 text-danger"></i>
                                <span>Không thể tải biểu đồ</span>
                                <small class="text-secondary">${e.message}</small>
                            </div>
                        `;
                    }
                }
            }
        }

        // Event listeners cho category range buttons
        if (categoryRangeButtons.length > 0) {
            categoryRangeButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    categoryRangeButtons.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    const range = this.dataset.categoryRange;
                    currentCategoryRange = range;

                    if (range === 'custom') {
                        categoryDateRangePicker.style.display = 'block';
                    } else {
                        categoryDateRangePicker.style.display = 'none';
                        loadCategoryRevenue(range);
                    }
                });
            });
        }

        // Custom date range apply cho category
        if (categoryDateRangeApply) {
            categoryDateRangeApply.addEventListener('click', () => {
                loadCategoryRevenue('custom');
            });
        }

        // ========== TOP CUSTOMERS TABLE ==========
        const topCustomersTableBody = document.getElementById('topCustomersTableBody');
        const customersDateRangePicker = document.getElementById('customersDateRangePicker');
        const customersDateFrom = document.getElementById('customersDateFrom');
        const customersDateTo = document.getElementById('customersDateTo');
        const customersDateRangeApply = document.getElementById('customersDateRangeApply');
        const customersRangeButtons = document.querySelectorAll('[data-customers-range]');
        const customersTotalCountEl = document.getElementById('customersTotalCount');
        const customersDateRangeTextEl = document.getElementById('customersDateRangeText');

        let currentCustomersRange = '30';

        async function loadTopCustomers(range = '30') {
            if (!topCustomersTableBody) return;

            // Show loading
            topCustomersTableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                        <span class="ms-2">Đang tải...</span>
                    </td>
                </tr>
            `;

            try {
                let url = "{{ url('/admin/api/dashboard/top-customers') }}";
                if (range === 'custom') {
                    const from = customersDateFrom.value;
                    const to = customersDateTo.value;
                    url += `?range=custom&from=${from}&to=${to}`;
                } else {
                    url += `?range=${range}`;
                }

                const response = await fetch(url);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success && result.data) {
                    const users = result.data.users || [];
                    const totalRevenue = result.data.totalRevenue || 0;

                    if (users.length === 0) {
                        topCustomersTableBody.innerHTML = `
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Không có dữ liệu</td>
                            </tr>
                        `;
                        customersTotalCountEl.textContent = '0';
                        return;
                    }

                    // Render table
                    topCustomersTableBody.innerHTML = users.map((user, index) => {
                        const avatarInitial = user.name ? user.name.charAt(0).toUpperCase() : '?';
                        const totalFormatted = new Intl.NumberFormat('vi-VN').format(user.total);

                        return `
                            <tr class="customer-row" data-user-id="${user.id || ''}">
                                <td>
                                    <div class="customer-info-container">
                                        <div class="customer-avatar" title="${user.name || 'N/A'}">${avatarInitial}</div>
                                        <div class="customer-info-text">
                                            <div class="customer-name fw-semibold" title="${user.name || 'N/A'}">${user.name || 'N/A'}</div>
                                            <small class="customer-email text-muted" title="${user.email || ''}">${user.email || ''}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">${user.orders || 0}</td>
                                <td class="text-end fw-semibold">${totalFormatted} ₫</td>
                                <td class="text-end">
                                    <span class="badge bg-primary bg-opacity-10 text-primary">${user.percent || 0}%</span>
                                </td>
                            </tr>
                        `;
                    }).join('');

                    customersTotalCountEl.textContent = users.length;

                    // Update date range text
                    const rangeTexts = {
                        '7': '7 ngày gần nhất',
                        '30': '30 ngày gần nhất',
                        '90': '90 ngày gần nhất',
                        'custom': 'Khoảng thời gian tùy chọn'
                    };
                    customersDateRangeTextEl.textContent = `Lọc theo: ${rangeTexts[range] || '30 ngày gần nhất'}`;

                    // Add click event để mở trang user detail (nếu có route)
                    document.querySelectorAll('.customer-row').forEach(row => {
                        row.addEventListener('click', function() {
                            const userId = this.dataset.userId;
                            if (userId) {
                                // Có thể redirect đến trang user detail
                                // window.location.href = `/admin/users/${userId}`;
                            }
                        });
                    });
                }
            } catch (e) {
                console.error('Error loading top customers:', e);
                if (typeof window.showToast === 'function') {
                    window.showToast('Lỗi tải dữ liệu khách hàng: ' + e.message, 'error');
                }
                topCustomersTableBody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center text-danger py-4">
                            <i class="bi bi-exclamation-triangle"></i> Lỗi tải dữ liệu: ${e.message}
                        </td>
                    </tr>
                `;
            }
        }

        // Event listeners cho customers range buttons
        if (customersRangeButtons.length > 0) {
            customersRangeButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    customersRangeButtons.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    const range = this.dataset.customersRange;
                    currentCustomersRange = range;

                    if (range === 'custom') {
                        customersDateRangePicker.style.display = 'block';
                    } else {
                        customersDateRangePicker.style.display = 'none';
                        loadTopCustomers(range);
                    }
                });
            });
        }

        // Custom date range apply cho customers
        if (customersDateRangeApply) {
            customersDateRangeApply.addEventListener('click', () => {
                loadTopCustomers('custom');
            });
        }

        // ========== TOP PRODUCTS TABLE ==========
        const topProductsTableBody = document.getElementById('topProductsTableBody');
        const topProductsTotalRevenueEl = document.getElementById('topProductsTotalRevenue');
        const topProductsDateRangeTextEl = document.getElementById('topProductsDateRangeText');
        const topProductsCustomRangeBtn = document.getElementById('topProductsCustomRangeBtn');
        const topProductsDateRangeGroup = document.getElementById('topProductsDateRangeGroup');
        const topProductsDateFrom = document.getElementById('topProductsDateFrom');
        const topProductsDateTo = document.getElementById('topProductsDateTo');
        const topProductsDateRangeApply = document.getElementById('topProductsDateRangeApply');

        let currentTopProductsRange = '30';
        let currentTopProductsFrom = null;
        let currentTopProductsTo = null;

        async function loadTopProducts(range = '30', from = null, to = null) {
            if (!topProductsTableBody) {
                console.error('topProductsTableBody not found!');
                return;
            }


            // Show loading
            topProductsTableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                        <span class="ms-2">Đang tải...</span>
                    </td>
                </tr>
            `;

            try {
                let url = "{{ url('/admin/api/dashboard/top-products') }}";
                const params = new URLSearchParams({
                    range: range,
                    limit: 5,
                    _t: Date.now(),
                });

                if (range === 'custom' && from && to) {
                    params.append('from', from);
                    params.append('to', to);
                }

                url += '?' + params.toString();

                const response = await fetch(url, {
                    cache: 'no-cache',
                    headers: {
                        'Cache-Control': 'no-cache',
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();


                if (!result.success) {
                    throw new Error(result.message || 'Lỗi từ server');
                }

                if (result.success && result.data) {
                    const products = result.data.items || [];
                    const totalRevenueEstimated = result.data.total_revenue_estimated || 0;
                    const totalRevenueCompleted = result.data.total_revenue_completed || 0;


                    if (products.length === 0) {
                        topProductsTableBody.innerHTML = `
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Không có dữ liệu</td>
                            </tr>
                        `;
                        topProductsTotalRevenueEl.innerHTML = '<div>Dự tính: <strong>0 đ</strong></div><div>Thực tế: <strong>0 đ</strong></div>';
                        topProductsDateRangeTextEl.textContent = 'Không có dữ liệu';
                        return;
                    }

                    // Helper function để escape HTML
                    function escapeHtml(text) {
                        if (!text) return '';
                        const div = document.createElement('div');
                        div.textContent = text;
                        return div.innerHTML;
                    }

                    // Render table với UI/UX improvements
                    topProductsTableBody.innerHTML = products.map((product, index) => {
                        const productNameEscaped = escapeHtml(product.name);
                        const categoryEscaped = escapeHtml(product.category);
                        const imageUrl = product.image || '/images/placeholder.png';
                        const rank = index + 1;

                        // Dự tính
                        const soldEstimated = product.sold_estimated || 0;
                        const revenueEstimated = product.revenue_estimated || 0;
                        const percentEstimated = product.percent_estimated || 0;

                        // Thực tế
                        const soldCompleted = product.sold_completed || 0;
                        const revenueCompleted = product.revenue_completed || 0;
                        const percentCompleted = product.percent_completed || 0;

                        // Tỷ lệ chuyển đổi
                        const conversionRate = product.conversion_rate || 0;

                        // Stock
                        const stock = product.stock || 0;

                        // Ranking badge
                        let rankBadgeClass = 'product-rank-badge rank-other';
                        if (rank === 1) rankBadgeClass = 'product-rank-badge rank-1';
                        else if (rank === 2) rankBadgeClass = 'product-rank-badge rank-2';
                        else if (rank === 3) rankBadgeClass = 'product-rank-badge rank-3';

                        // Stock badge class
                        let stockBadgeClass = 'stock-badge ';
                        let stockBadgeText = '';
                        if (stock > 20) {
                            stockBadgeClass += 'high';
                            stockBadgeText = '🟢 ' + stock;
                        } else if (stock >= 5) {
                            stockBadgeClass += 'medium';
                            stockBadgeText = '🟡 ' + stock;
                        } else {
                            stockBadgeClass += 'low';
                            stockBadgeText = '🔴 ' + stock;
                        }

                        // Format revenue (không có đ ở cuối, sẽ thêm trong HTML)
                        const revenueEstimatedFormatted = new Intl.NumberFormat('vi-VN').format(revenueEstimated);
                        const revenueCompletedFormatted = new Intl.NumberFormat('vi-VN').format(revenueCompleted);

                        // Conversion badge
                        let conversionBadgeClass = 'conversion-badge ';
                        if (conversionRate >= 5) {
                            conversionBadgeClass += 'high';
                        } else if (conversionRate >= 2) {
                            conversionBadgeClass += 'medium';
                        } else {
                            conversionBadgeClass += 'low';
                        }

                        // Progress bar width (max 100%)
                        const progressEstimatedWidth = Math.min(percentEstimated, 100);
                        const progressCompletedWidth = Math.min(percentCompleted, 100);

                        return `
                            <tr class="product-row" data-product-id="${product.id}" style="cursor: pointer;" onclick="window.location.href='{{ url('/admin/products/show') }}/${product.id}'">
                                <td class="text-center align-middle">
                                    <span class="${rankBadgeClass}" style="position: static; transform: none;">${rank}</span>
                                </td>
                                <td>
                                    <div class="product-info-container">
                                        <img src="${escapeHtml(imageUrl)}" alt="${productNameEscaped}" class="product-thumbnail-top" onerror="this.onerror=null; this.src='/images/placeholder.png';" title="Xem chi tiết sản phẩm">
                                    </div>
                                </td>
                                <td>
                                    <div class="product-info-text">
                                        <div class="product-name-top" title="Xem chi tiết sản phẩm">${productNameEscaped}</div>
                                        <div class="product-category-top">${categoryEscaped}</div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold">${revenueCompletedFormatted} đ</span>
                                </td>
                                <td class="text-center">
                                    <span class="${stockBadgeClass}" title="Tồn kho: ${stock} sản phẩm">${stockBadgeText}</span>
                                </td>
                            </tr>
                        `;
                    }).join('');

                    // Update footer (sử dụng biến đã khai báo ở trên)
                    topProductsTotalRevenueEl.innerHTML = `
                        <div>Dự tính: <strong>${new Intl.NumberFormat('vi-VN').format(totalRevenueEstimated)} đ</strong></div>
                        <div>Thực tế: <strong>${new Intl.NumberFormat('vi-VN').format(totalRevenueCompleted)} đ</strong></div>
                    `;

                    // Update date range text
                    let dateRangeText = '';
                    if (range === 'custom' && from && to) {
                        // Format date to Vietnamese format
                        const fromDate = new Date(from);
                        const toDate = new Date(to);
                        const fromFormatted = fromDate.toLocaleDateString('vi-VN');
                        const toFormatted = toDate.toLocaleDateString('vi-VN');
                        dateRangeText = `Lọc theo: ${fromFormatted} đến ${toFormatted}`;
                    } else {
                        const days = parseInt(range);
                        dateRangeText = `Lọc theo: ${days} ngày gần nhất`;
                    }
                    topProductsDateRangeTextEl.textContent = dateRangeText;
                }
            } catch (e) {
                console.error('Error loading top products:', e);
                topProductsTableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-danger py-4">
                            <i class="bi bi-exclamation-triangle"></i> Lỗi tải dữ liệu: ${e.message}
                        </td>
                    </tr>
                `;
            }
        }

        // Event listeners cho Top Products
        // Chỉ lấy các button trong top products section để tránh conflict với các section khác
        const topProductsSection = document.querySelector('#topProductsTableBody')?.closest('section');
        const topProductsRangeButtons = topProductsSection ? topProductsSection.querySelectorAll('[data-range]') : [];

        topProductsRangeButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const range = this.getAttribute('data-range');
                currentTopProductsRange = range;
                currentTopProductsFrom = null;
                currentTopProductsTo = null;

                // Update active state - chỉ trong top products section
                topProductsRangeButtons.forEach(b => {
                    b.classList.remove('active');
                });
                this.classList.add('active');

                // Hide custom date range
                if (topProductsDateRangeGroup) {
                    topProductsDateRangeGroup.classList.add('d-none');
                }

                loadTopProducts(range);
            });
        });

        if (topProductsCustomRangeBtn) {
            topProductsCustomRangeBtn.addEventListener('click', function() {
                if (topProductsDateRangeGroup) {
                    topProductsDateRangeGroup.classList.toggle('d-none');
                }
            });
        }

        if (topProductsDateRangeApply) {
            topProductsDateRangeApply.addEventListener('click', function() {
                const from = topProductsDateFrom?.value;
                const to = topProductsDateTo?.value;

                if (!from || !to) {
                    alert('Vui lòng chọn đầy đủ ngày bắt đầu và kết thúc');
                    return;
                }

                if (new Date(from) > new Date(to)) {
                    alert('Ngày bắt đầu không được lớn hơn ngày kết thúc');
                    return;
                }

                currentTopProductsRange = 'custom';
                currentTopProductsFrom = from;
                currentTopProductsTo = to;

                // Update active state - chỉ trong top products section
                const topProductsSection = topProductsTableBody?.closest('section');
                if (topProductsSection) {
                    topProductsSection.querySelectorAll('[data-range]').forEach(b => {
                        b.classList.remove('active');
                    });
                }
                topProductsCustomRangeBtn.classList.add('active');

                loadTopProducts('custom', from, to);
            });
        }

        // Load initial data
        if (topProductsTableBody) {
            // Set default active button - chỉ trong top products section
            const topProductsSection = topProductsTableBody.closest('section');
            const defaultBtn = topProductsSection ? topProductsSection.querySelector('[data-range="30"]') : null;
            if (defaultBtn) {
                defaultBtn.classList.add('active');
            }
            loadTopProducts('30');
        }

        // ========== COMMENTS TABLE ==========
        const commentsTableBody = document.getElementById('commentsTableBody');
        const commentsStatusFilter = document.getElementById('commentsStatusFilter');
        const commentsSearch = document.getElementById('commentsSearch');
        const commentsSearchBtn = document.getElementById('commentsSearchBtn');
        const commentsTotalCountEl = document.getElementById('commentsTotalCount');

        let currentCommentsStatus = '';
        let currentCommentsSearch = '';
        let allCommentsData = [];

        async function loadComments() {
            if (!commentsTableBody) return;

            // Show loading
            commentsTableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                        <span class="ms-2">Đang tải...</span>
                    </td>
                </tr>
            `;

            try {
                let url = "{{ url('/admin/api/dashboard/comments') }}";
                const params = new URLSearchParams({
                    per_page: 1000, // Load tất cả dữ liệu
                    _t: Date.now(), // Cache busting timestamp
                });

                if (currentCommentsStatus) {
                    params.append('status', currentCommentsStatus);
                }
                if (currentCommentsSearch) {
                    params.append('search', currentCommentsSearch);
                }

                url += '?' + params.toString();

                const response = await fetch(url, {
                    cache: 'no-cache', // Đảm bảo không dùng cache
                    headers: {
                        'Cache-Control': 'no-cache',
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success && result.data) {
                    allCommentsData = result.data.data || [];
                    const pagination = result.data;

                    if (allCommentsData.length === 0) {
                        commentsTableBody.innerHTML = `
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Không có dữ liệu</td>
                            </tr>
                        `;
                        commentsTotalCountEl.textContent = '0';
                        return;
                    }

                    // Update total count
                    commentsTotalCountEl.textContent = pagination.total || allCommentsData.length;

                    // Render tất cả dữ liệu
                    renderCommentsTable();

                }
            } catch (e) {
                console.error('Error loading comments:', e);
                if (typeof window.showToast === 'function') {
                    window.showToast('Lỗi tải bình luận: ' + e.message, 'error');
                }
                commentsTableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-danger py-4">
                            <i class="bi bi-exclamation-triangle"></i> Lỗi tải dữ liệu: ${e.message}
                        </td>
                    </tr>
                `;
            }
        }

        function renderCommentsTable() {
            if (!commentsTableBody) return;

            // Constants
            const CONTENT_PREVIEW_LENGTH = 100;
            const REVIEW_STATUS = {
                PENDING: 'pending',
                APPROVED: 'approved',
                REJECTED: 'rejected',
                HIDDEN: 'hidden'
            };

            // Helper function để escape HTML và prevent XSS
            function escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // Render table
            commentsTableBody.innerHTML = allCommentsData.map(comment => {
                        const avatarInitial = comment.user_name ? comment.user_name.charAt(0).toUpperCase() : '?';
                        const contentFull = comment.content || 'Không có nội dung';
                        const contentPreview = contentFull.length > CONTENT_PREVIEW_LENGTH ? contentFull.substring(0, CONTENT_PREVIEW_LENGTH) + '...' : contentFull;
                        const contentEscaped = escapeHtml(contentFull);
                        const contentPreviewEscaped = escapeHtml(contentPreview);
                        const timeAgo = comment.created_at_ago || comment.created_at;
                        const statusClass = `status-${comment.status}`;
                        const statusLabels = {
                            'approved': 'Đã duyệt',
                            'pending': 'Chờ duyệt',
                            'rejected': 'Đã từ chối',
                            'hidden': 'Đã ẩn'
                        };
                        const productThumbnail = comment.product_image || '/images/placeholder.png';
                        const productNameEscaped = escapeHtml(comment.product_name || 'N/A');
                        const userNameEscaped = escapeHtml(comment.user_name || 'N/A');
                        const userEmailEscaped = escapeHtml(comment.user_email || '');

                        return `
                            <tr class="comment-row" data-comment-id="${comment.id}">
                                <td>
                                    <div class="comment-user-info">
                                        <div class="comment-avatar" title="${userNameEscaped}">${escapeHtml(avatarInitial)}</div>
                                        <div class="comment-user-text">
                                            <div class="comment-user-name" title="${userNameEscaped}">${userNameEscaped}</div>
                                            <div class="comment-user-email" title="${userEmailEscaped}">${userEmailEscaped}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="comment-content-preview" title="${contentEscaped.replace(/"/g, '&quot;').replace(/'/g, '&#39;')}" data-bs-toggle="tooltip" data-bs-placement="top">
                                        ${contentPreviewEscaped}
                                    </div>
                                </td>
                                <td>
                                    <div class="product-info-comment">
                                        <img src="${escapeHtml(productThumbnail)}" alt="${productNameEscaped}" class="product-thumbnail" onerror="this.onerror=null; this.src='/images/placeholder.png';" onclick="event.stopPropagation(); window.location.href='{{ url('/admin/products/show') }}/${comment.product_id}'" title="Xem sản phẩm">
                                        <div class="product-name-comment" onclick="event.stopPropagation(); window.location.href='{{ url('/admin/products/show') }}/${comment.product_id}'" title="Xem sản phẩm">${productNameEscaped}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1" title="${timeAgo}">
                                        <i class="bi bi-clock text-muted" style="font-size: 0.7rem;"></i>
                                        <small class="text-muted">${timeAgo}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge ${statusClass}" title="Trạng thái: ${statusLabels[comment.status] || comment.status}">${statusLabels[comment.status] || comment.status}</span>
                                </td>
                                <td>
                                    <div class="dropdown" onclick="event.stopPropagation();">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle table-action-btn" type="button" id="actionDropdown${comment.id}" data-bs-toggle="dropdown" aria-expanded="false" title="Thao tác">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionDropdown${comment.id}">
                                            ${comment.status !== 'approved' ? `<li><a class="dropdown-item" href="#" onclick="approveComment(${comment.id}); return false;"><i class="bi bi-check-circle text-success me-2"></i>Duyệt</a></li>` : ''}
                                            ${comment.status !== 'rejected' ? `<li><a class="dropdown-item" href="#" onclick="rejectComment(${comment.id}); return false;"><i class="bi bi-x-circle text-danger me-2"></i>Từ chối</a></li>` : ''}
                                            <li><a class="dropdown-item" href="#" onclick="viewComment(${comment.id}); return false;"><i class="bi bi-eye text-primary me-2"></i>Xem chi tiết</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteComment(${comment.id}); return false;"><i class="bi bi-trash me-2"></i>Xóa</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }).join('');
        }

        // Event listeners cho Comments
        if (commentsStatusFilter) {
            commentsStatusFilter.addEventListener('change', function() {
                currentCommentsStatus = this.value;
                loadComments();
            });
        }

        // Debounce function
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Debounced search function
        const debouncedSearch = debounce(() => {
            currentCommentsSearch = commentsSearch.value;
            loadComments();
        }, 500);

        if (commentsSearchBtn) {
            commentsSearchBtn.addEventListener('click', function() {
                currentCommentsSearch = commentsSearch.value;
                loadComments();
            });
        }

        if (commentsSearch) {
            // Debounced search khi gõ
            commentsSearch.addEventListener('input', debouncedSearch);
            // Enter để search ngay lập tức
            commentsSearch.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    currentCommentsSearch = this.value;
                    loadComments();
                }
            });
        }

        // Helper function để update UI optimistically
        function updateCommentStatusOptimistically(commentId, newStatus, oldStatus) {
            const row = document.querySelector(`tr[data-comment-id="${commentId}"]`);
            if (!row) return;

            // Update status badge
            const statusBadge = row.querySelector('.status-badge');
            if (statusBadge) {
                const statusLabels = {
                    'approved': 'Đã duyệt',
                    'pending': 'Chờ duyệt',
                    'rejected': 'Đã từ chối',
                    'hidden': 'Đã ẩn'
                };
                const statusClass = `status-${newStatus}`;
                statusBadge.className = `status-badge ${statusClass}`;
                statusBadge.textContent = statusLabels[newStatus] || newStatus;
            }

            // Update dropdown menu
            const dropdown = row.querySelector('.dropdown-menu');
            if (dropdown) {
                const statusLabels = {
                    'approved': 'Đã duyệt',
                    'pending': 'Chờ duyệt',
                    'rejected': 'Đã từ chối',
                    'hidden': 'Đã ẩn'
                };

                let dropdownHTML = '';
                if (newStatus !== 'approved') {
                    dropdownHTML += `<li><a class="dropdown-item" href="#" onclick="approveComment(${commentId}); return false;"><i class="bi bi-check-circle text-success me-2"></i>Duyệt</a></li>`;
                }
                if (newStatus !== 'rejected') {
                    dropdownHTML += `<li><a class="dropdown-item" href="#" onclick="rejectComment(${commentId}); return false;"><i class="bi bi-x-circle text-danger me-2"></i>Từ chối</a></li>`;
                }
                dropdownHTML += `<li><a class="dropdown-item" href="#" onclick="viewComment(${commentId}); return false;"><i class="bi bi-eye text-primary me-2"></i>Xem chi tiết</a></li>`;
                dropdownHTML += `<li><hr class="dropdown-divider"></li>`;
                dropdownHTML += `<li><a class="dropdown-item text-danger" href="#" onclick="deleteComment(${commentId}); return false;"><i class="bi bi-trash me-2"></i>Xóa</a></li>`;
                dropdown.innerHTML = dropdownHTML;
            }
        }

        // Action functions cho Comments với optimistic update
        async function approveComment(id) {
            if (!confirm('Bạn có chắc muốn duyệt bình luận này?')) return;

            // Tìm row và lưu trạng thái cũ
            const row = document.querySelector(`tr[data-comment-id="${id}"]`);
            let oldStatus = 'pending';
            if (row) {
                const statusBadge = row.querySelector('.status-badge');
                if (statusBadge) {
                    if (statusBadge.classList.contains('status-approved')) oldStatus = 'approved';
                    else if (statusBadge.classList.contains('status-rejected')) oldStatus = 'rejected';
                    else if (statusBadge.classList.contains('status-hidden')) oldStatus = 'hidden';
                    else oldStatus = 'pending';
                }
            }

            // Optimistic update - update UI ngay lập tức
            updateCommentStatusOptimistically(id, 'approved', oldStatus);
            showToast('Đang xử lý...', 'info');

            // Disable button và show loading
            const dropdownItem = document.querySelector(`[onclick*="approveComment(${id})"]`);
            const originalHtml = dropdownItem ? dropdownItem.innerHTML : null;
            if (dropdownItem) {
                dropdownItem.style.pointerEvents = 'none';
                dropdownItem.style.opacity = '0.6';
                const icon = dropdownItem.querySelector('i');
                if (icon) {
                    icon.className = 'bi bi-hourglass-split me-2';
                }
            }

            try {
                const response = await fetch(`{{ url('/admin/api/dashboard/comments') }}/${id}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                const result = await response.json();
                if (response.ok && result.success) {
                    // Store action for undo
                    lastAction = { id: id, action: 'approve', old_status: result.old_status || oldStatus };
                    showToast(result.message || 'Bình luận đã được duyệt', 'success');
                    // Reload để đảm bảo data sync
                    loadComments();
                } else {
                    // Rollback optimistic update
                    updateCommentStatusOptimistically(id, oldStatus, 'approved');
                    showToast(result.message || 'Có lỗi xảy ra', 'error');
                    if (dropdownItem && originalHtml) {
                        dropdownItem.style.pointerEvents = '';
                        dropdownItem.style.opacity = '1';
                        dropdownItem.innerHTML = originalHtml;
                    }
                }
            } catch (e) {
                console.error('Error approving comment:', e);
                // Rollback optimistic update
                updateCommentStatusOptimistically(id, oldStatus, 'approved');
                showToast('Có lỗi xảy ra khi duyệt bình luận', 'error');
                if (dropdownItem && originalHtml) {
                    dropdownItem.style.pointerEvents = '';
                    dropdownItem.style.opacity = '1';
                    dropdownItem.innerHTML = originalHtml;
                }
            }
        }

        async function rejectComment(id) {
            if (!confirm('Bạn có chắc muốn từ chối bình luận này?')) return;

            // Tìm row và lưu trạng thái cũ
            const row = document.querySelector(`tr[data-comment-id="${id}"]`);
            let oldStatus = 'pending';
            if (row) {
                const statusBadge = row.querySelector('.status-badge');
                if (statusBadge) {
                    if (statusBadge.classList.contains('status-approved')) oldStatus = 'approved';
                    else if (statusBadge.classList.contains('status-rejected')) oldStatus = 'rejected';
                    else if (statusBadge.classList.contains('status-hidden')) oldStatus = 'hidden';
                    else oldStatus = 'pending';
                }
            }

            // Optimistic update - update UI ngay lập tức
            updateCommentStatusOptimistically(id, 'rejected', oldStatus);
            showToast('Đang xử lý...', 'info');

            const dropdownItem = document.querySelector(`[onclick*="rejectComment(${id})"]`);
            const originalHtml = dropdownItem ? dropdownItem.innerHTML : null;
            if (dropdownItem) {
                dropdownItem.style.pointerEvents = 'none';
                dropdownItem.style.opacity = '0.6';
                const icon = dropdownItem.querySelector('i');
                if (icon) {
                    icon.className = 'bi bi-hourglass-split me-2';
                }
            }

            try {
                const response = await fetch(`{{ url('/admin/api/dashboard/comments') }}/${id}/reject`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                const result = await response.json();
                if (response.ok && result.success) {
                    lastAction = { id: id, action: 'reject', old_status: result.old_status || oldStatus };
                    showToast(result.message || 'Bình luận đã bị từ chối', 'success');
                    // Reload để đảm bảo data sync
                    loadComments();
                } else {
                    // Rollback optimistic update
                    updateCommentStatusOptimistically(id, oldStatus, 'rejected');
                    showToast(result.message || 'Có lỗi xảy ra', 'error');
                    if (dropdownItem && originalHtml) {
                        dropdownItem.style.pointerEvents = '';
                        dropdownItem.style.opacity = '1';
                        dropdownItem.innerHTML = originalHtml;
                    }
                }
            } catch (e) {
                console.error('Error rejecting comment:', e);
                // Rollback optimistic update
                updateCommentStatusOptimistically(id, oldStatus, 'rejected');
                showToast('Có lỗi xảy ra khi từ chối bình luận', 'error');
                if (dropdownItem && originalHtml) {
                    dropdownItem.style.pointerEvents = '';
                    dropdownItem.style.opacity = '1';
                    dropdownItem.innerHTML = originalHtml;
                }
            }
        }

        async function deleteComment(id) {
            if (!confirm('Bạn có chắc muốn xóa bình luận này? Hành động này không thể hoàn tác.')) return;

            // Tìm row và lưu trạng thái cũ
            const row = document.querySelector(`tr[data-comment-id="${id}"]`);
            let oldStatus = 'pending';
            if (row) {
                const statusBadge = row.querySelector('.status-badge');
                if (statusBadge) {
                    if (statusBadge.classList.contains('status-approved')) oldStatus = 'approved';
                    else if (statusBadge.classList.contains('status-rejected')) oldStatus = 'rejected';
                    else if (statusBadge.classList.contains('status-hidden')) oldStatus = 'hidden';
                    else oldStatus = 'pending';
                }
            }

            // Optimistic update - ẩn row ngay lập tức
            if (row) {
                row.style.opacity = '0.5';
                row.style.transition = 'opacity 0.3s';
                setTimeout(() => {
                    row.style.display = 'none';
                }, 300);
            }
            showToast('Đang xóa...', 'info');

            const dropdownItem = document.querySelector(`[onclick*="deleteComment(${id})"]`);
            const originalHtml = dropdownItem ? dropdownItem.innerHTML : null;
            if (dropdownItem) {
                dropdownItem.style.pointerEvents = 'none';
                dropdownItem.style.opacity = '0.6';
                const icon = dropdownItem.querySelector('i');
                if (icon) {
                    icon.className = 'bi bi-hourglass-split me-2';
                }
            }

            try {
                const response = await fetch(`{{ url('/admin/api/dashboard/comments') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                const result = await response.json();
                if (response.ok && result.success) {
                    lastAction = { id: id, action: 'delete', old_status: result.old_status || oldStatus };
                    showToast(result.message || 'Bình luận đã được xóa', 'success');
                    // Reload để đảm bảo data sync và cập nhật pagination
                    loadComments();
                } else {
                    // Rollback optimistic update
                    if (row) {
                        row.style.display = '';
                        row.style.opacity = '1';
                    }
                    showToast(result.message || 'Có lỗi xảy ra', 'error');
                    if (dropdownItem && originalHtml) {
                        dropdownItem.style.pointerEvents = '';
                        dropdownItem.style.opacity = '1';
                        dropdownItem.innerHTML = originalHtml;
                    }
                }
            } catch (e) {
                console.error('Error deleting comment:', e);
                // Rollback optimistic update
                if (row) {
                    row.style.display = '';
                    row.style.opacity = '1';
                }
                showToast('Có lỗi xảy ra khi xóa bình luận', 'error');
                if (dropdownItem && originalHtml) {
                    dropdownItem.style.pointerEvents = '';
                    dropdownItem.style.opacity = '1';
                    dropdownItem.innerHTML = originalHtml;
                }
            }
        }

        // Undo last action
        async function undoLastAction() {
            if (!lastAction) {
                showToast('Không có thao tác nào để hoàn tác', 'error');
                return;
            }

            if (!confirm('Bạn có chắc muốn hoàn tác thao tác vừa thực hiện?')) return;

            // Tìm row và lưu trạng thái hiện tại
            const row = document.querySelector(`tr[data-comment-id="${lastAction.id}"]`);
            const currentStatus = row ? row.querySelector('.status-badge')?.classList.contains('status-approved') ? 'approved' :
                row.querySelector('.status-badge')?.classList.contains('status-rejected') ? 'rejected' :
                row.querySelector('.status-badge')?.classList.contains('status-hidden') ? 'hidden' : 'pending' : 'pending';

            // Optimistic update - rollback UI ngay lập tức
            if (lastAction.action === 'delete' && row) {
                row.style.display = '';
                row.style.opacity = '0.5';
                setTimeout(() => {
                    row.style.opacity = '1';
                }, 100);
            } else if (row) {
                updateCommentStatusOptimistically(lastAction.id, lastAction.old_status, currentStatus);
            }

            showToast('Đang hoàn tác...', 'info');

            try {
                const response = await fetch(`{{ url('/admin/api/dashboard/comments') }}/${lastAction.id}/undo`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        old_status: lastAction.old_status
                    })
                });
                const result = await response.json();
                if (response.ok && result.success) {
                    showToast('Đã hoàn tác thao tác', 'success');
                    lastAction = null;
                    // Reload để đảm bảo data sync
                    loadComments();
                } else {
                    // Rollback optimistic update
                    if (lastAction.action === 'delete' && row) {
                        row.style.display = 'none';
                    } else if (row) {
                        updateCommentStatusOptimistically(lastAction.id, currentStatus, lastAction.old_status);
                    }
                    showToast(result.message || 'Không thể hoàn tác', 'error');
                }
            } catch (e) {
                console.error('Error undoing action:', e);
                // Rollback optimistic update
                if (lastAction.action === 'delete' && row) {
                    row.style.display = 'none';
                } else if (row) {
                    updateCommentStatusOptimistically(lastAction.id, currentStatus, lastAction.old_status);
                }
                showToast('Có lỗi xảy ra khi hoàn tác', 'error');
            }
        }

        function viewComment(id) {
            // Redirect đến trang chi tiết bình luận
            window.location.href = `{{ url('/admin/comments') }}/${id}`;
        }

        // ========== USERS TABLE ==========
        const usersTableBody = document.getElementById('usersTableBody');
        const usersStatusFilter = document.getElementById('usersStatusFilter');
        const usersSearch = document.getElementById('usersSearch');
        const usersSearchBtn = document.getElementById('usersSearchBtn');
        const usersTotalCountEl = document.getElementById('usersTotalCount');

        let currentUsersStatus = '';
        let currentUsersSearch = '';
        let allUsersData = [];

        async function loadUsers() {
            if (!usersTableBody) return;

            // Show loading
            usersTableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                        <span class="ms-2">Đang tải...</span>
                    </td>
                </tr>
            `;

            try {
                let url = "{{ url('/admin/api/dashboard/users') }}";
                const params = new URLSearchParams({
                    per_page: 1000, // Load tất cả dữ liệu
                });

                if (currentUsersStatus) {
                    params.append('status', currentUsersStatus);
                }
                if (currentUsersSearch) {
                    params.append('search', currentUsersSearch);
                }

                url += '?' + params.toString();

                const response = await fetch(url);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success && result.data) {
                    allUsersData = result.data.data || [];
                    const pagination = result.data;

                    if (allUsersData.length === 0) {
                        usersTableBody.innerHTML = `
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Không có dữ liệu</td>
                            </tr>
                        `;
                        usersTotalCountEl.textContent = '0';
                        return;
                    }

                    // Update total count
                    usersTotalCountEl.textContent = pagination.total || allUsersData.length;

                    // Render tất cả dữ liệu
                    renderUsersTable();
                }
            } catch (e) {
                console.error('Error loading users:', e);
                usersTableBody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center text-danger py-4">
                            <i class="bi bi-exclamation-triangle"></i> Lỗi tải dữ liệu
                        </td>
                    </tr>
                `;
            }
        }

        function renderUsersTable() {
            if (!usersTableBody) return;

            // Render table
            usersTableBody.innerHTML = allUsersData.map(user => {
                        const avatarInitial = user.name ? user.name.charAt(0).toUpperCase() : '?';
                        const totalSpentFormatted = new Intl.NumberFormat('vi-VN').format(user.total_spent || 0);
                        const createdAt = user.created_at_ago || user.created_at;
                        const statusClass = `status-${user.status || 'active'}`;
                        const statusLabels = {
                            'active': 'Hoạt động',
                            'inactive': 'Không hoạt động',
                            'banned': 'Đã khóa',
                            'unverified': 'Unverified'
                        };

                        return `
                            <tr class="user-row" data-user-id="${user.id}">
                                <td>
                                    <div class="user-avatar" title="${user.name || 'N/A'}">${avatarInitial}</div>
                                </td>
                                <td>
                                    <div class="user-info-text">
                                        <div class="user-name" title="${user.name || 'N/A'}">${user.name || 'N/A'}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="user-email" title="${user.email || ''}">${user.email || ''}</div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1" title="Số đơn hàng">
                                        <i class="bi bi-cart text-primary" style="font-size: 0.75rem;"></i>
                                        <strong>${user.orders_count || 0}</strong>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1 revenue-value" title="Tổng chi tiêu">
                                        <i class="bi bi-cash-coin text-success" style="font-size: 0.75rem;"></i>
                                        <strong>${totalSpentFormatted} ₫</strong>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1" title="${createdAt}">
                                        <i class="bi bi-calendar text-muted" style="font-size: 0.7rem;"></i>
                                        <small class="text-muted">${createdAt}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge ${statusClass}" title="Trạng thái: ${statusLabels[user.status] || user.status || 'Active'}">${statusLabels[user.status] || user.status || 'Active'}</span>
                                </td>
                                <td>
                                    <div class="dropdown" onclick="event.stopPropagation();">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle table-action-btn" type="button" id="userActionDropdown${user.id}" data-bs-toggle="dropdown" aria-expanded="false" title="Thao tác">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userActionDropdown${user.id}">
                                            <li><a class="dropdown-item" href="#" onclick="viewUser(${user.id}); return false;"><i class="bi bi-eye text-primary me-2"></i>Xem chi tiết</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            ${user.status !== 'banned' ? `<li><a class="dropdown-item text-danger" href="#" onclick="banUser(${user.id}); return false;"><i class="bi bi-ban me-2"></i>Khóa tài khoản</a></li>` : `<li><a class="dropdown-item text-success" href="#" onclick="unbanUser(${user.id}); return false;"><i class="bi bi-check-circle me-2"></i>Mở khóa</a></li>`}
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }).join('');
        }

        // Event listeners cho Users
        if (usersStatusFilter) {
            usersStatusFilter.addEventListener('change', function() {
                currentUsersStatus = this.value;
                loadUsers();
            });
        }

        if (usersSearchBtn) {
            usersSearchBtn.addEventListener('click', function() {
                currentUsersSearch = usersSearch.value;
                loadUsers();
            });
        }

        if (usersSearch) {
            usersSearch.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    currentUsersSearch = this.value;
                    loadUsers();
                }
            });
        }

        // Action functions cho Users
        function viewUser(id) {
            window.location.href = `{{ url('/admin/account/users') }}/${id}`;
        }

        async function banUser(id) {
            if (!confirm('Bạn có chắc muốn khóa người dùng này?')) return;

            const dropdownItem = document.querySelector(`[onclick*="banUser(${id})"]`);
            const originalHtml = dropdownItem ? dropdownItem.innerHTML : null;
            if (dropdownItem) {
                dropdownItem.style.pointerEvents = 'none';
                dropdownItem.style.opacity = '0.6';
                const icon = dropdownItem.querySelector('i');
                if (icon) {
                    icon.className = 'bi bi-hourglass-split me-2';
                }
            }

            try {
                const response = await fetch(`{{ url('/admin/api/dashboard/users') }}/${id}/ban`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                const result = await response.json();
                if (response.ok && result.success) {
                    showToast(result.message || 'Người dùng đã bị khóa', 'success');
                    loadUsers();
                } else {
                    showToast(result.message || 'Có lỗi xảy ra', 'error');
                    if (dropdownItem && originalHtml) {
                        dropdownItem.style.pointerEvents = '';
                        dropdownItem.style.opacity = '1';
                        dropdownItem.innerHTML = originalHtml;
                    }
                }
            } catch (e) {
                console.error('Error banning user:', e);
                showToast('Có lỗi xảy ra khi khóa người dùng', 'error');
                if (dropdownItem && originalHtml) {
                    dropdownItem.style.pointerEvents = '';
                    dropdownItem.style.opacity = '1';
                    dropdownItem.innerHTML = originalHtml;
                }
            }
        }

        async function unbanUser(id) {
            if (!confirm('Bạn có chắc muốn mở khóa người dùng này?')) return;

            const dropdownItem = document.querySelector(`[onclick*="unbanUser(${id})"]`);
            const originalHtml = dropdownItem ? dropdownItem.innerHTML : null;
            if (dropdownItem) {
                dropdownItem.style.pointerEvents = 'none';
                dropdownItem.style.opacity = '0.6';
                const icon = dropdownItem.querySelector('i');
                if (icon) {
                    icon.className = 'bi bi-hourglass-split me-2';
                }
            }

            try {
                const response = await fetch(`{{ url('/admin/api/dashboard/users') }}/${id}/unban`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                const result = await response.json();
                if (response.ok && result.success) {
                    showToast(result.message || 'Người dùng đã được mở khóa', 'success');
                    loadUsers();
                } else {
                    showToast(result.message || 'Có lỗi xảy ra', 'error');
                    if (dropdownItem && originalHtml) {
                        dropdownItem.style.pointerEvents = '';
                        dropdownItem.style.opacity = '1';
                        dropdownItem.innerHTML = originalHtml;
                    }
                }
            } catch (e) {
                console.error('Error unbanning user:', e);
                showToast('Có lỗi xảy ra khi mở khóa người dùng', 'error');
                if (dropdownItem && originalHtml) {
                    dropdownItem.style.pointerEvents = '';
                    dropdownItem.style.opacity = '1';
                    dropdownItem.innerHTML = originalHtml;
                }
            }
        }

        // Initialize Bootstrap tooltips
        function initTooltips() {
            // Destroy existing tooltips
            const existingTooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            existingTooltips.forEach(el => {
                const tooltipInstance = bootstrap.Tooltip.getInstance(el);
                if (tooltipInstance) {
                    tooltipInstance.dispose();
                }
            });

            // Initialize new tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        // Wrap loadComments để re-initialize tooltips
        const originalLoadComments = loadComments;
        loadComments = async function(page) {
            await originalLoadComments(page);
            setTimeout(initTooltips, 200);
        };

        // Wrap loadUsers để re-initialize tooltips
        const originalLoadUsers = loadUsers;
        loadUsers = async function(page) {
            await originalLoadUsers(page);
            setTimeout(initTooltips, 200);
        };

        // Load initial data
        if (categoryRevenueCtx) {
            loadCategoryRevenue('30');
        }
        if (topCustomersTableBody) {
            loadTopCustomers('30');
        }
        if (topProductsTableBody) {
            // Set default active button - chỉ trong top products section
            const topProductsSection = topProductsTableBody.closest('section');
            const defaultBtn = topProductsSection ? topProductsSection.querySelector('[data-range="30"]') : null;
            if (defaultBtn) {
                defaultBtn.classList.add('active');
            }
            loadTopProducts('30');
        }
        if (commentsTableBody) {
            loadComments();
        }
        if (usersTableBody) {
            loadUsers();
        }

        // Initialize KPI tooltips on page load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(initTooltips, 300);
            });
        } else {
            setTimeout(initTooltips, 300);
        }

        // ========== INVENTORY SUMMARY TABLE ==========
        const inventoryTableBody = document.getElementById('inventoryTableBody');
        const inventorySearch = document.getElementById('inventorySearch');
        const inventoryStatusFilter = document.getElementById('inventoryStatusFilter');
        const inventoryTotalEl = document.getElementById('inventoryTotal');
        const inventoryFilterInfoEl = document.getElementById('inventoryFilterInfo');

        let allInventoryData = []; // Lưu tất cả dữ liệu để filter
        let filteredInventoryData = [];

        async function loadInventory() {
            if (!inventoryTableBody) {
                console.error('inventoryTableBody not found!');
                return;
            }

            // Show loading
            inventoryTableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                        <span class="ms-2">Đang tải...</span>
                    </td>
                </tr>
            `;

            try {
                const url = "{{ url('/admin/api/dashboard/inventory') }}";
                const response = await fetch(url, {
                    cache: 'no-cache',
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (!result.success) {
                    throw new Error(result.message || 'Lỗi khi tải dữ liệu');
                }

                if (result.success && result.data) {
                    allInventoryData = result.data.items || [];
                    filterInventoryData();
                }
            } catch (e) {
                console.error('Error loading inventory:', e);
                if (typeof window.showToast === 'function') {
                    window.showToast('Lỗi tải kho hàng: ' + e.message, 'error');
                }
                inventoryTableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-danger py-4">
                            <i class="bi bi-exclamation-triangle"></i> Lỗi tải dữ liệu: ${e.message}
                        </td>
                    </tr>
                `;
            }
        }

        // Filter inventory data
        function filterInventoryData() {
            const searchTerm = inventorySearch?.value.toLowerCase() || '';
            const statusFilter = inventoryStatusFilter?.value || '';

            filteredInventoryData = allInventoryData.filter(item => {
                const matchSearch = !searchTerm ||
                    item.name.toLowerCase().includes(searchTerm) ||
                    (item.sku && item.sku.toLowerCase().includes(searchTerm)) ||
                    (item.category && item.category.toLowerCase().includes(searchTerm));

                const matchStatus = !statusFilter || item.stock_status === statusFilter;

                return matchSearch && matchStatus;
            });

            renderInventoryTable();
            updateInventoryFooter();
        }

        // Render inventory table
        function renderInventoryTable() {
            if (!inventoryTableBody) return;

            if (filteredInventoryData.length === 0) {
                inventoryTableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Không có dữ liệu</td>
                    </tr>
                `;
                return;
            }

            // Hiển thị tất cả sản phẩm, có thể scroll lên xuống để xem
            const displayData = filteredInventoryData;

            // Helper function để escape HTML
            function escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            inventoryTableBody.innerHTML = displayData.map(item => {
                const productNameEscaped = escapeHtml(item.name);
                const skuEscaped = escapeHtml(item.sku || '');

                const stock = item.stock || 0;
                const sold = item.sold_quantity || 0;

                // Stock status badge
                const stockStatusBadge = `
                    <span class="badge" style="background-color: ${item.stock_status_color}; color: white;">
                        ${item.stock_status_label}
                    </span>
                `;

                return `
                    <tr class="inventory-row" data-product-id="${item.id}" style="cursor: pointer;" onclick="window.location.href='{{ url('/admin/products/show') }}/${item.id}'">
                        <td title="${productNameEscaped}">
                            <div class="fw-bold" title="${productNameEscaped}">${productNameEscaped}</div>
                            ${skuEscaped ? `<div class="small text-muted" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">SKU: ${skuEscaped}</div>` : ''}
                        </td>
                        <td class="text-center">
                            <strong>${stock}</strong>
                        </td>
                        <td class="text-center">
                            ${stockStatusBadge}
                        </td>
                        <td class="text-center">
                            <span class="text-muted">Đã bán: </span><strong>${sold}</strong>
                        </td>
                        <td class="text-center">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" onclick="event.stopPropagation();">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ url('/admin/products/show') }}/${item.id}"><i class="bi bi-eye"></i> Xem chi tiết</a></li>
                                    <li><a class="dropdown-item" href="{{ url('/admin/products') }}/${item.id}/edit"><i class="bi bi-pencil"></i> Chỉnh sửa</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');

            // Update footer
            updateInventoryFooter();
        }

        // Update inventory footer
        function updateInventoryFooter() {
            const inventoryTotalEl = document.getElementById('inventoryTotal');
            const inventoryFilterInfoEl = document.getElementById('inventoryFilterInfo');

            if (inventoryTotalEl) {
                inventoryTotalEl.textContent = filteredInventoryData.length;
            }

            if (inventoryFilterInfoEl) {
                const searchTerm = inventorySearch?.value || '';
                const statusFilter = inventoryStatusFilter?.value || '';

                if (searchTerm || statusFilter) {
                    inventoryFilterInfoEl.textContent = `Đã lọc: ${filteredInventoryData.length} sản phẩm`;
                } else {
                    inventoryFilterInfoEl.innerHTML = '<i class="bi bi-info-circle"></i> Vuốt chuột lên xuống để xem tất cả sản phẩm';
                }
            }
        }

        // Event listeners
        if (inventorySearch) {
            let searchTimeout;
            inventorySearch.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    filterInventoryData();
                }, 300); // Debounce 300ms
            });
        }

        if (inventoryStatusFilter) {
            inventoryStatusFilter.addEventListener('change', function() {
                filterInventoryData();
            });
        }

        // Load initial data
        if (inventoryTableBody) {
            loadInventory();
        }

        // Toggle filter options based on filter type (with null guards)
        function toggleFilterOptions() {
            const filterTypeEl = document.getElementById('order_filter_type');
            const monthGroup = document.getElementById('filter_month_group');
            const dateRangeGroup = document.getElementById('filter_date_range_group');

            if (!filterTypeEl || !monthGroup || !dateRangeGroup) return;

            const filterType = filterTypeEl.value || '';

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
            const hiddenInputs = form.querySelectorAll('input[type="hidden"]');~
            hiddenInputs.forEach(input => {
                if (input.name === 'start_date' || input.name === 'end_date') {
                    input.remove();
                }
            });
            form.submit();
        }
    </script>
    @endpush
@endsection
