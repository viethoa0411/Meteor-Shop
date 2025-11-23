@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1">Đơn hàng #{{ $order->order_code }}</h2>
            <div class="text-muted small">
                Đặt ngày: {{ $order->created_at->format('d/m/Y H:i') }}
            </div>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.orders.list') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
            @if(in_array($order->order_status, ['pending', 'confirmed']))
                <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil"></i> Chỉnh sửa
            </a>
            @endif
        </div>
    </div>

    {{-- Status Badge & Quick Actions --}}
    <div class="card shadow-sm mb-4">
                <div class="card-body">
            <div class="row align-items-center">
        <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3">
                        <div>
                            @php
                                $statusMeta = $order->status_meta;
                            @endphp
                            <span class="badge bg-{{ $statusMeta['badge'] }} fs-6 px-3 py-2">
                                <i class="bi {{ $statusMeta['icon'] ?? '' }}"></i>
                                {{ $statusMeta['label'] }}
                                </span>
                        </div>
                                            <div>
                            <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group">
                        {{-- 快速操作按钮 --}}
                        @if($order->canConfirm() && $order->order_status === 'pending')
                            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="d-inline">
                                @csrf @method('PUT')
                            <input type="hidden" name="order_status" value="processing">
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="bi bi-check-circle"></i> Xác nhận đơn
                            </button>
                        </form>
                        @endif
                        @if($order->canPack() && $order->order_status === 'processing')
                            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="d-inline">
                                @csrf @method('PUT')
                                <input type="hidden" name="order_status" value="packed">
                                <button type="submit" class="btn btn-info btn-sm">
                                    <i class="bi bi-box"></i> Đóng gói
                            </button>
                        </form>
                        @endif
                        @if($order->canShip())
                            <a href="{{ route('admin.orders.shipments.create', $order->id) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-truck"></i> Tạo đơn vận chuyển
                            </a>
                        @endif
                        @if(count($order->getAllowedNextStatuses()) > 0)
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#statusChangeModal">
                                <i class="bi bi-arrow-repeat"></i> Thay đổi trạng thái
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'summary' ? 'active' : '' }}" 
               href="{{ route('admin.orders.show', ['id' => $order->id, 'tab' => 'summary']) }}">
                <i class="bi bi-info-circle"></i> Tổng quan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'products' ? 'active' : '' }}" 
               href="{{ route('admin.orders.show', ['id' => $order->id, 'tab' => 'products']) }}">
                <i class="bi bi-box-seam"></i> Sản phẩm
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'payments' ? 'active' : '' }}" 
               href="{{ route('admin.orders.show', ['id' => $order->id, 'tab' => 'payments']) }}">
                <i class="bi bi-credit-card"></i> Thanh toán
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'shipment' ? 'active' : '' }}" 
               href="{{ route('admin.orders.show', ['id' => $order->id, 'tab' => 'shipment']) }}">
                <i class="bi bi-truck"></i> Vận chuyển
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'timeline' ? 'active' : '' }}" 
               href="{{ route('admin.orders.show', ['id' => $order->id, 'tab' => 'timeline']) }}">
                <i class="bi bi-clock-history"></i> Lịch sử
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'notes' ? 'active' : '' }}" 
               href="{{ route('admin.orders.show', ['id' => $order->id, 'tab' => 'notes']) }}">
                <i class="bi bi-sticky"></i> Ghi chú
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'customer' ? 'active' : '' }}" 
               href="{{ route('admin.orders.show', ['id' => $order->id, 'tab' => 'customer']) }}">
                <i class="bi bi-person"></i> Khách hàng
            </a>
        </li>
    </ul>

    {{-- Tab Content --}}
    <div class="tab-content">
        @if($tab === 'summary')
            @include('admin.orders.tabs.summary')
        @elseif($tab === 'products')
            @include('admin.orders.tabs.products')
        @elseif($tab === 'payments')
            @include('admin.orders.tabs.payments')
        @elseif($tab === 'shipment')
            @include('admin.orders.tabs.shipment')
        @elseif($tab === 'timeline')
            @include('admin.orders.tabs.timeline')
        @elseif($tab === 'notes')
            @include('admin.orders.tabs.notes')
        @elseif($tab === 'customer')
            @include('admin.orders.tabs.customer')
        @endif
    </div>
</div>

    {{-- Status Change Modal --}}
    @include('admin.orders.partials.status-modal')
@endsection
