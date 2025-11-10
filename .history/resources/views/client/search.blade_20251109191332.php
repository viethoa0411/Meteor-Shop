@extends('client.layouts.app')

@section('title', 'Kết quả tìm kiếm')

@section('content')
<div style="padding: 40px 20px; max-width: 1200px; margin: 0 auto;">

    <!-- Tiêu đề -->
    <h2 style="font-size: 22px; font-weight:600; margin-bottom:12px; display:flex; align-items:center; gap:8px;">
        Kết quả tìm kiếm cho:
        <span style="color:#007bff;">“{{ $searchQuery }}”</span>
    </h2>


    @if($products->isEmpty())
        <p>Không tìm thấy sản phẩm nào phù hợp.</p>
    @else
        <p style="color:#666; font-size:14px; margin-bottom:24px;">
            Tìm thấy <strong>{{ $products->total() }}</strong> sản phẩm phù hợp.
        </p>

        <!-- Bộ lọc sắp xếp -->
<form method="GET" action="{{ route('client.product.search') }}" style="margin-bottom: 20px;">
    <input type="hidden" name="query" value="{{ $searchQuery }}">
    <label for="sort" style="font-weight: 500; margin-right: 8px;">Sắp xếp theo:</label>
    <select name="sort" id="sort" onchange="this.form.submit()" style="padding: 6px 12px; border-radius: 4px;">
        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Theo giá: Thấp đến cao</option>
        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Theo giá: Cao đến thấp</option>
    </select>
</form>

        <!-- Lưới sản phẩm -->
        <div class="grid-products">
            @foreach ($products as $p)
                <a href="{{ route('client.product.detail', $p->slug) }}" class="product-card">
                    <div class="product-img">
                        <img
                            src="{{ $p->image ? asset('storage/'.$p->image) : 'https://via.placeholder.com/400x400?text=No+Image' }}"
                            alt="{{ $p->name }}">
                    </div>
                    <div class="product-name">{{ $p->name }}</div>
                    <div class="product-price">{{ number_format($p->price, 0, ',', '.') }} đ</div>
                </a>
            @endforeach
        </div>

        <!-- Phân trang -->
        <div style="margin-top: 30px;">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection