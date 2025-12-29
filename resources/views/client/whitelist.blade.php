@extends('client.layouts.app')

@section('title', 'Sản phẩm yêu thích')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 text-center">Sản phẩm yêu thích</h2>

    @if ($items->isEmpty())
        <div class="text-center py-5">
            <p class="mb-4 fs-5">Bạn chưa thêm sản phẩm nào vào danh sách yêu thích.</p>
            <a href="{{ route('client.products.index') }}" class="btn btn-primary btn-lg">Xem sản phẩm ngay</a>
        </div>
    @else
        <div class="row g-4" id="wishlist-container">
            @foreach ($items as $item)
                @php $product = $item->product; @endphp
                @if ($product)
                    <div class="col-6 col-md-4 col-lg-3 wishlist-item" data-wishlist-id="{{ $item->id }}">
                        <div class="card h-100 position-relative border-0 shadow-sm">
                            <!-- Nút XÓA -->
                            <button type="button"
                                    class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 rounded-circle z-3 wishlist-remove-btn"
                                    data-product-id="{{ $product->id }}"
                                    title="Xóa khỏi danh sách yêu thích">
                                <i class="bi bi-trash"></i>
                            </button>

                            <a href="{{ route('client.product.detail', $product->slug) }}" class="text-decoration-none text-dark">
                                <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/300x300?text=No+Image' }}"
                                     class="card-img-top"
                                     alt="{{ $product->name }}"
                                     style="object-fit: cover; height: 250px;">
                                <div class="card-body text-center pt-3">
                                    <h6 class="card-title mb-2">{{ $product->name }}</h6>
                                    <p class="text-danger fw-bold fs-5 mb-0">
                                        {{ number_format($product->price, 0, ',', '.') }} đ
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.querySelectorAll('.wishlist-remove-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            if (!confirm('Bạn chắc chắn muốn xóa sản phẩm này khỏi danh sách yêu thích?')) {
                return;
            }

            const productId = this.dataset.productId;
            const card = this.closest('.wishlist-item');

            // Hiệu ứng loading
            this.innerHTML = '<i class="bi bi-hourglass-split"></i>';
            this.disabled = true;

            fetch('{{ route("client.wishlist.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => {
                if (!response.ok) throw new Error('Lỗi mạng');
                return response.json();
            })
            .then(data => {
                if (data.status === 'success' && data.liked === false) {
                    // Xóa card mượt
                    card.style.transition = 'all 0.4s ease';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.9)';
                    setTimeout(() => card.remove(), 400);

                    // Nếu hết sản phẩm
                    if (document.querySelectorAll('.wishlist-item').length === 0) {
                        document.getElementById('wishlist-container').innerHTML = `
                            <div class="text-center py-5 col-12">
                                <p class="mb-4 fs-5">Bạn chưa thêm sản phẩm nào vào danh sách yêu thích.</p>
                                <a href="{{ route('client.products.index') }}" class="btn btn-primary btn-lg">Xem sản phẩm ngay</a>
                            </div>
                        `;
                    }

                    // Cập nhật badge wishlist ở header (nếu bạn có)
                    const badge = document.querySelector('.client-wishlist__badge');
                    if (badge) {
                        let count = parseInt(badge.textContent) || 1;
                        count = Math.max(0, count - 1);
                        if (count === 0) {
                            badge.style.display = 'none';
                        } else {
                            badge.textContent = count > 99 ? '99+' : count;
                        }
                    }

                    alert(data.message);
                } else {
                    alert(data.message || 'Không thể xóa');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Lỗi kết nối. Vui lòng thử lại.');
            })
            .finally(() => {
                this.innerHTML = '<i class="bi bi-trash"></i>';
                this.disabled = false;
            });
        });
    });
});
</script>
@endsection