@extends('admin.dashboard')

@section('content')
<div class="container mt-3">
    <h3>Chi tiết đơn hàng: <span class="text-primary">{{ $order->order_code }}</span></h3>

    <div class="mt-3">
        <h5>Thông tin khách hàng</h5>
        <p><strong>Tên:</strong> {{ $order->customer_name }}</p>
        <p><strong>SĐT:</strong> {{ $order->customer_phone }}</p>
        <p><strong>Địa chỉ giao hàng:</strong> {{ $order->shipping_address }}</p>
    </div>

    <div class="mt-3">
        <h5>Thông tin đơn hàng</h5>
        <p><strong>Ngày đặt:</strong> {{ date('d/m/Y H:i', strtotime($order->created_at)) }}</p>

        {{-- FORM UPDATE TRẠNG THÁI --}}
        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="mb-2">
            @csrf
            @method('PUT')
            <label><strong>Trạng thái đơn hàng:</strong></label>
            <div class="d-flex" style="max-width: 300px;">
                <select name="order_status" class="form-select me-2">
                    <option value="pending" {{ $order->order_status=='pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                    <option value="processing" {{ $order->order_status=='processing' ? 'selected' : '' }}>Đang xử lý</option>
                    <option value="completed" {{ $order->order_status=='completed' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="cancelled" {{ $order->order_status=='cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
                <button class="btn btn-primary">Cập nhật</button>
            </div>
        </form>

        {{-- THÔNG BÁO --}}
        @if(session('success'))
            <div class="alert alert-success py-2">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger py-2">{{ session('error') }}</div>
        @endif

        <p><strong>Tổng thanh toán:</strong>
            <strong class="text-danger">{{ number_format($order->final_total, 0, ',', '.') }}₫</strong>
        </p>
    </div>

    <hr>

    <h5>Danh sách sản phẩm đã đặt</h5>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Sản phẩm</th>
                <th>Giá</th>
                <th>SL</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orderDetails as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ number_format($item->price, 0, ',', '.') }}₫</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->subtotal, 0, ',', '.') }}₫</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary mt-3">Quay lại danh sách</a>
</div>
@endsection
