@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')
@if ($showTargetAlert)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Chú ý!</strong> Bạn chưa đặt mục tiêu cho tháng {{ now()->month }} năm {{ now()->year }}.
        <a href="{{ route('admin.monthly_target.create') }}" class="btn btn-sm btn-primary ms-2">Đặt ngay</a>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@section('content')
@php
    $filteredRevenue = $filteredRevenue ?? null;
    $filteredOrdersCount = $filteredOrdersCount ?? null;
    $startDate = $startDate ?? null;
    $endDate = $endDate ?? null;
@endphp
    <div class="row g-4 mb-4 d-flex align-items-stretch">
        {{-- Cột trái 60% --}}
        <div class="col-md-7 d-flex flex-column">
            <div class="row g-4 flex-grow-1">
                {{-- Người dùng --}}
                <div class="col-6">
                    <div class="card p-3 shadow-sm border-start h-100">
                        <div class="d-flex justify-content-between align-items-center h-100">
                            <div>
                                <i class="bi bi-people text-warning display-5"></i>

                                <h6 class="text-muted mb-1">Người dùng</h6>
                            </div>
                            <h2 class="fw-bold">{{ number_format($totalUsers) }}</h2>
                        </div>
                    </div>
                </div>

                {{-- Đơn hàng tháng này --}}
                <div class="col-6">
                    <div class="card p-3 shadow-sm border-start h-100">
                        <div class="d-flex justify-content-between align-items-center h-100">
                            <div>
                                <i class="bi bi-cart-check text-success display-5"></i>
                                        @if (!is_null($filteredOrdersCount))
                                            <h6 class="text-muted mb-1">Tổng đơn
                                                
                                            </h6>
                                        @else
                                            <h6 class="text-muted mb-1">Đơn hàng tháng này</h6>
                                        @endif
                            </div>
                                    @if (!is_null($filteredOrdersCount))
                                        <h2 class="fw-bold">{{ number_format($filteredOrdersCount) }}</h2>
                                    @else
                                        <h2 class="fw-bold">{{ number_format($totalOrders) }}</h2>
                                    @endif
                        </div>
                    </div>
                </div>

                {{-- Biểu đồ doanh thu --}}
                <div class="col-12 mt-3">
                    <div class="card shadow-sm p-4 h-100">
                        <h5 class="mb-3 fw-semibold">Doanh thu theo tháng ({{ now()->year }})</h5>
                        <canvas id="revenueChart" style="height: 330px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        {{-- Cột phải 40% --}}
        <div class="col-md-5 d-flex flex-column">
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

                @if (!is_null($filteredOrdersCount))
                    <div class="mt-2 p-2 border rounded bg-light">
                        <strong>Tổng đơn: </strong> {{ $filteredOrdersCount }}
                    </div>
                @endif
            </div>

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

    <style>
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
    </style>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const percent = {{ round(($currentMonthRevenue / max($monthlyTarget, 1)) * 100, 2) }};
            const gauge = document.querySelector('.half-gauge-value');
            const text = document.getElementById('halfPercent');

            const totalLength = 314;
            const offset = totalLength - (percent / 100) * totalLength;

            gauge.style.strokeDashoffset = offset; // chạy từ trái → phải
            text.innerText = percent + "%";
        });



        const revenueData = @json($revenueData);
        const revenueLabels = @json($revenueLabels ?? []);
        const ctx = document.getElementById('revenueChart').getContext('2d');

        let isDark = document.documentElement.classList.contains("dark");

        const textColor = () => isDark ? '#ffffff' : '#000000';
        const bgColor = () => isDark ? 'rgba(59,130,246,0.4)' : 'rgba(59,130,246,0.6)';
        const borderCol = 'rgb(59,130,246)';

        let chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: revenueLabels.length ? revenueLabels : ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'],
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
        });
    </script>
@endsection
