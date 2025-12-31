@extends('client.layouts.app')

@section('content')
    <div class="container pb-5">

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="text-center mb-4">
            <h2>Giỏ hàng</h2>
        </div>
        @if (count($cart) > 0)
            <form action="{{ route('client.checkout.index') }}" method="GET" id="cart-form">
                <input type="hidden" name="type" value="cart">
                <table class="table table-bordered align-middle">
                    <tr>
                        <th style="width:40px;">
                            <input type="checkbox" id="select-all" checked>
                        </th>
                        <th>Ảnh</th>
                        <th>Tên</th>
                        <th class="text-end">Giá</th>
                        <th>Số lượng</th>
                        <th class="text-end">Thành tiền</th>
                        <th>Hành động</th>
                    </tr>

                    @foreach ($cart as $id => $item)
                        @php
                            $maxStock = $item['max_stock'] ?? 0;
                            $isOutOfStock = $maxStock < 1;
                            $isNotEnough = $maxStock < $item['quantity'];
                        @endphp
                        <tr id="row-{{ $id }}" class="{{ $isOutOfStock ? 'table-secondary' : '' }}">
                            <td class="text-center">
                                <input type="checkbox" name="selected[]" value="{{ $id }}"
                                    class="cart-item-checkbox"
                                    data-id="{{ $id }}"
                                    data-subtotal="{{ $item['price'] * $item['quantity'] }}"
                                    {{ $isOutOfStock ? 'disabled' : 'checked' }}>
                            </td>

                            <td>
                                <img src="{{ $item['image'] ? asset('storage/' . $item['image']) : 'https://via.placeholder.com/70x70?text=No+Image' }}"
                                    width="70" alt="{{ $item['name'] }}" style="{{ $isOutOfStock ? 'opacity: 0.5' : '' }}">
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
                                @if ($isOutOfStock)
                                    <div class="text-danger fw-bold small mt-1">Hết hàng</div>
                                @elseif ($isNotEnough)
                                    <div class="text-danger fw-bold small mt-1">Kho chỉ còn {{ $maxStock }}</div>
                                @endif
                            </td>

                            <td class="text-end">{{ number_format($item['price']) }}đ</td>

                            <td class="d-flex align-items-center gap-2 flex-column flex-sm-row">
                                <button type="button" class="btn btn-outline-secondary btn-sm updateQty" data-id="{{ $id }}"
                                    data-type="minus">-</button>
                                <span id="qty-{{ $id }}" data-max="{{ $item['max_stock'] ?? '' }}">{{ $item['quantity'] }}</span>
                                <button type="button" class="btn btn-outline-secondary btn-sm updateQty" data-id="{{ $id }}"
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
                                <button type="button" class="btn btn-danger btn-sm removeItem" data-id="{{ $id }}">Xóa</button>
                            </td>
                        </tr>
                    @endforeach
                </table>
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mt-3">
                    <div class="fw-semibold fs-5">
                        Tổng tiền đã chọn:
                        <span id="selected-total">{{ number_format($total) ?? 0 }}đ</span>
                    </div>
                    <button type="submit" form="cart-form" id="checkout-selected" class="btn btn-dark mt-2 mt-md-0">
                        <i class="bi bi-cart-check me-1"></i> Đặt hàng
                    </button>
                </div>
            </form>
        @else
            <div class="text-center mt-4">
                <p>Hiện tại giỏ hàng của bạn trống.</p>
                <a href="{{ route('client.home') }}" class="btn btn-primary mt-3">Quay lại trang chủ</a>
            </div>
        @endif

        @if (! empty($suggestedProducts) && $suggestedProducts->count())
            <div class="mt-5 mb-4">
                <h4 class="mb-3 fw-semibold">Sản phẩm bạn có thể thích</h4>
                <div class="row g-3">
                    @foreach ($suggestedProducts as $product)
                        <div class="col-6 col-md-3">
                            <div class="card h-100">
                                <a href="{{ route('client.product.detail', $product->slug) }}" class="text-decoration-none text-dark">
                                    <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/300x300?text=No+Image' }}"
                                        class="card-img-top" alt="{{ $product->name }}" style="object-fit:cover; aspect-ratio:1/1;">
                                    <div class="card-body">
                                        <h6 class="card-title" style="min-height: 40px;">{{ $product->name }}</h6>
                                        <p class="mb-0 text-danger fw-semibold">
                                            {{ number_format($product->price, 0, ',', '.') }} đ
                                        </p>
                                        @if ($product->category)
                                            <small class="text-muted">{{ $product->category->name }}</small>
                                        @endif
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        const formatCurrency = (value) => Number(value).toLocaleString('vi-VN') + "đ";

        const updateSelectedTotal = () => {
            let total = 0;
            $('.cart-item-checkbox:checked').each(function() {
                total += Number($(this).attr('data-subtotal')) || 0;
            });
            $('#selected-total').text(formatCurrency(total));
        };

        const syncSelectAll = () => {
            const totalCheckboxes = $('.cart-item-checkbox').length;
            const checked = $('.cart-item-checkbox:checked').length;
            $('#select-all').prop('checked', totalCheckboxes > 0 && checked === totalCheckboxes);
        };

        updateSelectedTotal();
        syncSelectAll();

        $('#select-all').on('change', function() {
            const checked = $(this).is(':checked');
            $('.cart-item-checkbox').prop('checked', checked);
            updateSelectedTotal();
        });

        $(document).on('change', '.cart-item-checkbox', function() {
            syncSelectAll();
            updateSelectedTotal();
        });

        // Validate form submit
        $('#cart-form').on('submit', function(e) {
            const selectedCount = $('.cart-item-checkbox:checked').length;

            if (selectedCount === 0) {
                e.preventDefault();
                alert('Vui lòng chọn ít nhất một sản phẩm để đặt hàng.');
            }
        });


        // Update số lượng
        $(document).on('click', '.updateQty', function() {
            let id = $(this).data('id');
            let type = $(this).data('type');
            let qtySpan = $("#qty-" + id);
            let currentQty = parseInt(qtySpan.text());
            let maxStock = parseInt(qtySpan.attr('data-max'));

            // Chặn bớt request nếu giảm về 1
            if (type === 'minus' && currentQty <= 1) return;

            // Check limit 10 (REMOVED)
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
                    $("#subtotal-" + id).text(Number(data.subtotal).toLocaleString('vi-VN') + "đ");
                    $(".cart-item-checkbox[data-id='" + id + "']").attr('data-subtotal', data.subtotal);
                    updateSelectedTotal();
                    syncSelectAll();

                    if (typeof data.max_stock !== 'undefined') {
                        if (data.max_stock) {
                            $("#stock-note-" + id).text('Tối đa: ' + data.max_stock);
                        } else {
                            $("#stock-note-" + id).text('');
                        }
                    }
                } else if (data.status === 'error') {
                    // Nếu lỗi (hết hàng hoặc giới hạn), hiện SweetAlert
                    Swal.fire({
                        icon: 'error',
                        title: 'Thông báo',
                        text: data.message
                    });
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
                    updateSelectedTotal();
                    syncSelectAll();

                    if (data.total == 0) {
                        window.location.reload();
                    }
                }
            });
        });

    });
</script>
@endpush
