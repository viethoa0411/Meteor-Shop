@extends('client.layouts.app')

@section('content')
    <div class="container">
        <div class="text-center mb-4">
            <h2>Giỏ hàng</h2>
        </div>
        @if (count($cart) > 0)
            <table class="table table-bordered">
                <tr>
                    <th>Ảnh</th>
                    <th>Tên</th>
                    <th class="text-end">Giá</th>
                    <th>Số lượng</th>
                    <th class="text-end">Thành tiền</th>
                    <th>Hành động</th>
                </tr>

                @foreach ($cart as $id => $item)
                    <tr id="row-{{ $id }}">
                        <td>
                            <img src="{{ $item['image'] ? asset('storage/' . $item['image']) : 'https://via.placeholder.com/70x70?text=No+Image' }}"
                                width="70" alt="{{ $item['name'] }}">
                        </td>

                        <td>
                            {{ $item['name'] }}
                            <div class="mt-1 text-muted" style="font-size: 0.9em;">
                                @if ($item['color'])
                                    <span>Màu: <strong>{{ $item['color'] }}</strong></span>
                                @endif
                                @if ($item['size'])
                                    <span class="ms-2">Size: <strong>{{ $item['size'] }}</strong></span>
                                @endif
                            </div>
                        </td>

                        <td class="text-end">{{ number_format($item['price']) }}đ</td>

                        <td class="d-flex align-items-center gap-2 flex-column flex-sm-row">
                            <button class="btn btn-outline-secondary btn-sm updateQty" data-id="{{ $id }}"
                                data-type="minus">-</button>
                            <span id="qty-{{ $id }}" data-max="{{ $item['max_stock'] ?? '' }}">{{ $item['quantity'] }}</span>
                            <button class="btn btn-outline-secondary btn-sm updateQty" data-id="{{ $id }}"
                                data-type="plus">+</button>
                            @if (!empty($item['max_stock']))
                                <small class="text-muted" id="stock-note-{{ $id }}">Tối đa: {{ $item['max_stock'] }}</small>
                            @else
                                <small class="text-muted" id="stock-note-{{ $id }}"></small>
                            @endif
                        </td>

                        <td class="text-end">
                            <span
                                id="subtotal-{{ $id }}">{{ number_format($item['price'] * $item['quantity']) }}đ</span>
                        </td>

                        <td>
                            <button class="btn btn-danger btn-sm removeItem" data-id="{{ $id }}">Xóa</button>
                        </td>
                    </tr>
                @endforeach
            </table>
            <div class="text-end mt-3">
                <h4>Tổng tiền: <span id="total">{{ number_format($total) ?? 0 }}đ</span></h4>
                <a href="{{ route('client.checkout.index', ['type' => 'cart']) }}" class="btn btn-dark mt-2">Đặt hàng</a>
            </div>
        @else
            <div class="text-center mt-4">
                <p>Hiện tại giỏ hàng của bạn trống.</p>
                <a href="{{ route('client.home') }}" class="btn btn-primary mt-3">Quay lại trang chủ</a>
            </div>
        @endif
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {

        // Update số lượng
        $(document).on('click', '.updateQty', function() {
            let id = $(this).data('id');
            let type = $(this).data('type');
            let qtySpan = $("#qty-" + id);
            let currentQty = parseInt(qtySpan.text());
            let maxStock = parseInt(qtySpan.attr('data-max'));

            // Chặn bớt request nếu giảm về 1
            if (type === 'minus' && currentQty <= 1) return;
            if (type === 'plus' && maxStock && currentQty >= maxStock) {
                alert('Bạn chỉ có thể mua tối đa ' + maxStock + ' sản phẩm cho lựa chọn này.');
                return;
            }

            $.post("{{ route('cart.updateQty') }}", {
                id: id,
                type: type,
                _token: "{{ csrf_token() }}"
            }, function(data) {
                if (data.status === 'success') {
                    // Cập nhật giao diện nếu thành công
                    $("#qty-" + id).text(data.quantity)
                        .attr('data-max', data.max_stock ?? '');
                    $("#subtotal-" + id).text(Number(data.subtotal).toLocaleString() + "đ");
                    $("#total").text(Number(data.total).toLocaleString() + "đ");
                    if (typeof data.max_stock !== 'undefined') {
                        if (data.max_stock) {
                            $("#stock-note-" + id).text('Tối đa: ' + data.max_stock);
                        } else {
                            $("#stock-note-" + id).text('');
                        }
                    }
                } else if (data.status === 'error') {
                    // Nếu lỗi (hết hàng), alert ra và KHÔNG tăng số lượng
                    alert(data.message);
                }
            }).fail(function() {
                alert('Có lỗi xảy ra, vui lòng thử lại');
            });
        });

        // Xóa sản phẩm
        $(document).on('click', '.removeItem', function() {
            let id = $(this).data('id');

            $.post("{{ route('cart.remove') }}", {
                id: id,
                _token: "{{ csrf_token() }}"
            }, function(data) {
                if (data.status === 'success') {
                    $("#row-" + id).remove();
                    $("#total").text(Number(data.total).toLocaleString() + "đ");

                    if (data.total == 0) {
                        $("table").remove();
                        $(".container").append("<p>Giỏ hàng của bạn trống.</p>");
                    }
                }
            });
        });

    });
</script>
