@extends('client.layouts.app')


@section('title', $title ?? 'Kết quả tìm kiếm')

<style>
/* --- Giữ nguyên CSS cũ --- */
.filter-bar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
    background: #fafafa;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 1px 6px rgba(0,0,0,0.1);
    margin-bottom: 24px;
}
.filter-group { display: flex; flex-direction: column; font-size: 14px; }
.filter-group label { font-weight: 500; margin-bottom: 6px; color: #444; height: 38px; display: flex; align-items: center; }
.filter-group select { padding: 8px 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; height: 38px; min-width: 180px; }
.btn-filter {
    background: #007bff; color: #fff; border: none; padding: 8px 16px; border-radius: 6px;
    cursor: pointer; transition: 0.2s; font-weight: 500; height: 38px; align-self: flex-end;
}
.btn-filter:hover { background: #0056b3; }
.slider-wrapper { display: flex; flex-direction: column; gap: 6px; min-width: 300px; flex-grow: 1; }
.sort-actions-wrapper { display: flex; gap: 20px; align-items: flex-end; }
input[type="range"] { width: 100%; accent-color: #007bff; cursor: pointer; }
input[type="range"]::-webkit-slider-thumb { width: 16px; height: 16px; border-radius: 50%; background: #007bff; cursor: pointer; appearance: none; }

/* Thêm CSS cho card bài viết để tái sử dụng */
.article-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}
</style>

@section('content')
{{-- Vùng chứa chính, dùng padding tương tự trang chủ --}}
<div style="padding: 40px 20px 20px 20px; max-width: 1200px; margin: 0 auto;">

    {{-- Tiêu đề động: có thể là tìm kiếm hoặc danh mục --}}
    <div style="padding-left: 0; padding-right: 0; padding-bottom: 20px;">
        @if(isset($selectedCategory))
            <h2 style="font-size: 22px; font-weight:600; margin-bottom:12px;">
                Sản phẩm thuộc danh mục:
                <span style="color:#007bff;">{{ $selectedCategory->name }}</span>
            </h2>
        @elseif(isset($searchQuery))
            <h2 style="font-size: 22px; font-weight:600; margin-bottom:12px;">
                Kết quả tìm kiếm cho:
                <span style="color:#007bff;">“{{ $searchQuery }}”</span>
            </h2>
        @else
            <h2 style="font-size: 22px; font-weight:600; margin-bottom:12px;">Danh sách sản phẩm</h2>
        @endif
    </div>

    @if($products->isEmpty())
        <p>Không tìm thấy sản phẩm nào phù hợp.</p>
    @else
        <p style="color:#666; font-size:14px; margin-bottom:24px;">
            Tìm thấy <strong>{{ $products->total() }}</strong> sản phẩm phù hợp.
        </p>

        {{-- Chỉ hiển thị thanh lọc nếu là trang tìm kiếm --}}
        @if(isset($searchQuery))
        <form method="GET" action="{{ route('client.product.search') }}" class="filter-bar" id="filterForm">
            <input type="hidden" name="query" value="{{ $searchQuery }}">

            <div class="filter-group slider-wrapper" style="flex:1;">
                <label>Khoảng giá (1 triệu - 20 triệu):</label>
                <div style="display:flex; flex-direction:column; gap:4px;">
                    <input type="range" id="minPrice" name="minPrice" min="1000000" max="20000000" step="500000"
                        value="{{ request('minPrice', 1000000) }}">
                    <input type="range" id="maxPrice" name="maxPrice" min="1000000" max="20000000" step="500000"
                        value="{{ request('maxPrice', 20000000) }}">
                    <p style="margin:0; color:#555;">Từ: <strong id="priceDisplay"></strong></p>
                </div>
            </div>

            <div class="sort-actions-wrapper">
                <div class="filter-group">
                    <label for="sort">Sắp xếp theo:</label>
                    <select name="sort" id="sort" onchange="document.getElementById('filterForm').submit()">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá thấp → cao</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá cao → thấp</option>
                    </select>
                </div>

                <button type="submit" class="btn-filter">Lọc</button>
            </div>
        </form>
        @endif

        {{-- Danh sách sản phẩm (Bố cục sản phẩm tương tự trang chủ) --}}
        <div class="product" style="padding-bottom: 50px; padding-left:0; padding-right:0">
            <div class="grid-products">
                @foreach ($products as $p)
                    <a href="{{ route('client.product.detail', $p->slug) }}" class="product-card">
                        <div class="product-img">
                            <img src="{{ $p->image ? asset('storage/'.$p->image) : 'https://via.placeholder.com/400x400?text=No+Image' }}" alt="{{ $p->name }}">
                        </div>
                        <div class="product-name">{{ $p->name }}</div>
                        <div class="product-price">{{ number_format($p->price, 0, ',', '.') }} đ</div>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Phân trang --}}
        <div style="margin-top: 10px; margin-bottom: 50px;">
>>>>>>> eca3fb6387947a26f91d698ae62b346887ad3fab
            {{ $products->links() }}
        </div>
    @endif
</div>

{{-- --- GÓC CẢM HỨNG --- --}}
{{-- Khối này được đặt ngoài điều kiện @if($products->isEmpty()) để luôn hiển thị --}}
<div style="text-align: center; padding: 40px 20px; background: #f9f9f9; overflow:hidden;">
    <h2 style="font-size: 28px; font-weight:700; margin-bottom:30px">Góc Cảm Hứng</h2>

    <div style="display:flex;justify-content:center;gap:2vw;flex-wrap:nowrap; max-width: 1200px; margin: 0 auto;">
        <div class="article-card"
            style="width:48vw;max-width:580px;background:#fff;border-radius:10px;overflow:hidden;text-align:left;box-shadow:0 4px 12px  rgba(0,0,0,0.1);flex-shrink:0;transition: all 0.4s ease;">
            <img src="https://picsum.photos/800/600?random=501" alt="Bài viết 1"
                style="width: 100%; height: auto; max-height: 350px; object-fit: cover; display: block">
            <h3 style="font-size: 20px; font-weight: 600; margin: 16px; ">Thiết kế hiện đại cho không gian nhỏ</h3>
            <p style="font-size: 14px; color:#555; line-height: 1.6; margin: 0 16px 20px;">
                Những ý tưởng thông minh giúp tận dụng tối đa diện tích, mang lại sự tiện nghi và phong cách hiện đại.
                Từ màu sắc, ánh sáng đến cách bố trí nội thất, tất cả đều tạo nên cảm giác thoải mái và ấm cúng cho căn
                hộ của bạn.
            </p>
        </div>
        <div class="article-card"
            style="width: 48vw; max-width:580px; background: #fff; border-radius:10px; overflow:hidden; text-align: left; box-shadow: 0 4px 12px  rgba(0,0,0,0.1); flex-shrink:0;  transition: all 0.4s ease;">
            <img src="https://picsum.photos/800/600?random=502" alt="Bài viết 1"
                style="width: 100%; height: auto; max-height: 350px; object-fit: cover; display: block">
            <h3 style="font-size: 20px; font-weight: 600; margin: 16px; ">Xu hướng nội thất bền vững và thân thiện với môi trường</h3>
            <p style="font-size: 14px; color:#555; line-height: 1.6; margin: 0 16px 20px;">
                Cây xanh và ánh sáng tự nhiên đang trở thành xu hướng trong thiết kế nội thất hiện đại.
                Cùng khám phá cách đưa thiên nhiên vào ngôi nhà của bạn để tạo nên không gian thư giãn và tươi mới mỗi
                ngày.
            </p>
        </div>
    </div>
</div>
{{-- end Góc Cảm Hứng --}}

{{-- Script cho phần thanh lọc giá (chỉ khi có form tìm kiếm) --}}
@if(isset($searchQuery))
<script>
const minPrice = document.getElementById('minPrice');
const maxPrice = document.getElementById('maxPrice');
const priceDisplay = document.getElementById('priceDisplay');

function formatMoney(num) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND', minimumFractionDigits: 0 }).format(num);
}

function updatePriceDisplay() {
    const min = Math.min(parseInt(minPrice.value), parseInt(maxPrice.value));
    const max = Math.max(parseInt(minPrice.value), parseInt(maxPrice.value));
    // Cập nhật cả min và max range nếu chúng vượt qua nhau
    if (parseInt(minPrice.value) > parseInt(maxPrice.value)) {
        minPrice.value = max;
        maxPrice.value = min;
    }
    priceDisplay.textContent = `${formatMoney(min).replace('₫', '')} - ${formatMoney(max)}`;
}

minPrice.addEventListener('input', updatePriceDisplay);
maxPrice.addEventListener('input', updatePriceDisplay);
updatePriceDisplay();
</script>
@endif
@endsection
>>>>>>> eca3fb6387947a26f91d698ae62b346887ad3fab
