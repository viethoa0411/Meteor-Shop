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
                            <!-- Nút XÓA (Style giống trang chủ) -->
                            <button type="button"
                                    class="wishlist-remove-btn"
                                    data-product-id="{{ $product->id }}"
                                    title="Xóa khỏi danh sách yêu thích"
                                    style="position:absolute; top:8px; right:8px; z-index:2; border-radius:999px; border:none; background:rgba(255,255,255,0.9); padding:4px 8px; cursor:pointer; display:flex; align-items:center; gap:4px;">
                                <i class="bi bi-heart-fill text-danger"></i>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.querySelectorAll('.wishlist-remove-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const productId = this.dataset.productId;
            const card = this.closest('.wishlist-item');
            const originalIcon = this.innerHTML;

            // Hiệu ứng loading
            this.innerHTML = '<div class="spinner-border spinner-border-sm text-danger" role="status"></div>';
            this.disabled = true;

            fetch('{{ route("client.wishlist.remove") }}', {
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
                // Trường hợp 1: Xóa thành công (hoặc đã xóa rồi)
                if (data.status === 'success') {
                    // Xóa card mượt
                    card.style.transition = 'all 0.4s ease';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.8)';
                    
                    setTimeout(() => {
                        card.remove();
                        // Nếu hết sản phẩm thì hiện thông báo trống
                        if (document.querySelectorAll('.wishlist-item').length === 0) {
                            document.getElementById('wishlist-container').innerHTML = `
                                <div class="text-center py-5 col-12">
                                    <p class="mb-4 fs-5">Bạn chưa thêm sản phẩm nào vào danh sách yêu thích.</p>
                                    <a href="{{ route('client.products.index') }}" class="btn btn-primary btn-lg">Xem sản phẩm ngay</a>
                                </div>
                            `;
                        }
                        
                        // Cập nhật badge wishlist trên header (nếu có)
                        const badge = document.querySelector('[data-wishlist-badge]');
                        if (badge) {
                            let count = parseInt(badge.innerText) || 0;
                            if (count > 0) count--;
                            badge.innerText = count;
                            if (count === 0) badge.classList.add('d-none');
                        }
                    }, 400);

                    // Show toast notification
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    
                    Toast.fire({
                        icon: 'success',
                        title: 'Đã xóa sản phẩm khỏi danh sách yêu thích'
                    });

                } else {
                    // Lỗi từ server
                    alert(data.message || 'Có lỗi xảy ra!');
                    this.innerHTML = originalIcon;
                    this.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra, vui lòng thử lại!');
                this.innerHTML = originalIcon;
                this.disabled = false;
            });
        });
    });
});
</script>
@endpush
