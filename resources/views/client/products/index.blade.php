@extends('client.layouts.app')

@section('title', $title ?? 'Sản phẩm')

@push('head')
<style>
    .product-page {
        max-width: 1320px;
        margin: 0 auto;
        padding: 40px 16px 60px;
    }

    .product-page-header {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 24px;
    }

    .product-page-title {
        font-size: 26px;
        font-weight: 700;
        letter-spacing: -0.03em;
        color: #111827;
    }

    .product-page-subtitle {
        font-size: 14px;
        color: #6b7280;
    }

    .product-page-layout {
        display: grid;
        grid-template-columns: minmax(0, 280px) minmax(0, 1fr);
        gap: 24px;
        align-items: flex-start;
    }

    .product-filter-card {
        background: #f9fafb;
        border-radius: 16px;
        padding: 18px 18px 20px;
        border: 1px solid #e5e7eb;
        position: sticky;
        top: 96px;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        overflow: hidden;
    }

    .product-filter-title {
        font-size: 15px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 12px;
    }

    .product-filter-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 14px;
    }

    .product-filter-label {
        font-size: 13px;
        font-weight: 500;
        color: #4b5563;
    }

    .product-filter-select,
    .product-filter-input {
        width: 100%;
        border-radius: 999px;
        border: 1px solid #d1d5db;
        padding: 8px 12px;
        font-size: 13px;
        color: #111827;
        background: #fff;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg width='14' height='14' viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M5.25 7.5L10 12.25L14.75 7.5' stroke='%239CA3AF' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 14px 14px;
        padding-right: 30px;
    }

    .product-filter-range-wrapper {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .product-filter-range-row {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }

    .product-filter-range-row input[type="number"] {
        flex: 1 1 0;
        min-width: 0;
        border-radius: 999px;
        border: 1px solid #d1d5db;
        padding: 6px 10px;
        font-size: 13px;
        box-sizing: border-box;
    }

    .product-filter-apply {
        margin-top: 8px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
        justify-content: space-between;
    }

    .btn-filter-apply {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 14px;
        border-radius: 999px;
        border: none;
        background: #111827;
        color: #fff;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
    }

    .btn-filter-reset {
        border: none;
        background: none;
        font-size: 12px;
        color: #6b7280;
        cursor: pointer;
        text-decoration: underline;
    }

    .product-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .product-result-count {
        font-size: 13px;
        color: #6b7280;
    }

    .product-sort-select {
        min-width: 180px;
        border-radius: 999px;
        border: 1px solid #d1d5db;
        padding: 7px 12px;
        font-size: 13px;
        color: #111827;
        background: #fff;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
        gap: 20px;
    }

    .product-empty {
        padding: 60px 20px;
        text-align: center;
        color: #6b7280;
        font-size: 14px;
        background: #f9fafb;
        border-radius: 16px;
        border: 1px dashed #e5e7eb;
    }

    .product-pagination {
        margin-top: 24px;
        display: flex;
        justify-content: center;
    }

    .product-pagination .pagination {
        gap: 4px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .product-pagination .page-link {
        border-radius: 999px !important;
        min-width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        padding: 0 10px;
    }

    .product-pagination .page-item.active .page-link {
        background-color: #111827;
        border-color: #111827;
        color: #fff;
    }

    .product-pagination .page-link:hover {
        background-color: #e5e7eb;
    }

    @media (max-width: 992px) {
        .product-page-layout {
            grid-template-columns: minmax(0, 1fr);
        }

        .product-filter-card {
            position: static;
            max-width: 100%;
            margin-bottom: 16px;
        }

        .product-pagination {
            margin-top: 20px;
        }
    }

    @media (max-width: 768px) {
        .product-page {
            padding-top: 28px;
        }

        .product-page-title {
            font-size: 22px;
        }
    }
</style>
@endpush

@section('content')
<div class="product-page">
    <div class="product-page-header">
        <h1 class="product-page-title">
            {{ $title ?? 'Sản phẩm' }}
        </h1>
        <p class="product-page-subtitle">
            Khám phá bộ sưu tập nội thất hiện đại, được chọn lọc theo phòng, phong cách và ngân sách của bạn.
        </p>
    </div>

    @if(isset($groupedCategories))
        {{-- Chế độ hiển thị: mỗi danh mục 1 dòng, 4 sản phẩm mới nhất + giữ bộ lọc bên trái --}}
        <div class="product-page-layout">
            {{-- Bộ lọc bên trái --}}
            <aside>
                <form method="GET" action="{{ route('client.product.search') }}" class="product-filter-card">
                    <div class="product-filter-group">
                        <label class="product-filter-label">Danh mục</label>
                        <select name="category" class="product-filter-select" onchange="this.form.submit()">
                            <option value="">Tất cả danh mục</option>
                            @foreach($cate as $category)
                                <option value="{{ $category->slug }}">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="product-filter-group">
                        <label class="product-filter-label">Khoảng giá (VND)</label>
                        <div class="product-filter-range-wrapper">
                            <div class="product-filter-range-row">
                                <input type="number" name="minPrice" min="0" step="500000"
                                       placeholder="Từ">
                                <input type="number" name="maxPrice" min="0" step="500000"
                                       placeholder="Đến">
                            </div>
                            <small style="font-size:11px; color:#9ca3af;">Gợi ý: 1.000.000 – 20.000.000</small>
                        </div>
                    </div>

                    <div class="product-filter-group">
                        <label class="product-filter-label">Sắp xếp</label>
                        <select name="sort" class="product-filter-select" onchange="this.form.submit()">
                            <option value="newest">Mới nhất</option>
                            <option value="price_asc">Giá thấp → cao</option>
                            <option value="price_desc">Giá cao → thấp</option>
                        </select>
                    </div>

                    <div class="product-filter-apply">
                        <button type="submit" class="btn-filter-apply">Áp dụng bộ lọc</button>
                        <button type="button" class="btn-filter-reset"
                                onclick="window.location='{{ route('client.products.index') }}'">
                            Xóa lọc
                        </button>
                    </div>
                </form>
            </aside>

            {{-- Danh sách danh mục + 4 sản phẩm mới nhất mỗi dòng --}}
            <section>
                <div class="product-groups">
                    @foreach($groupedCategories as $category)
                        <section class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h2 class="h5 mb-0">{{ $category->name }}</h2>
                                <a href="{{ route('client.product.category', $category->slug) }}" class="text-primary small">
                                    Xem tất cả
                                </a>
                            </div>
                            <div class="product-grid" style="grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));">
                                @foreach($category->products as $p)
                                    <a href="{{ route('client.product.detail', $p->slug) }}" class="product-card">
                                        <div class="product-img">
                                            <img src="{{ $p->image ? asset('storage/'.$p->image) : 'https://via.placeholder.com/400x400?text=No+Image' }}"
                                                 alt="{{ $p->name }}">
                                        </div>
                                        <div class="product-name">{{ $p->name }}</div>
                                        <div class="product-price">{{ number_format($p->price, 0, ',', '.') }} đ</div>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </div>
            </section>
        </div>
    @else
        {{-- Chế độ lọc & kết quả tìm kiếm / theo danh mục --}}
        <div class="product-page-layout">
            {{-- Bộ lọc bên trái --}}
            <aside>
                <form method="GET" action="{{ route('client.product.search') }}" class="product-filter-card">
                    @if(!empty($searchQuery))
                        <div class="product-filter-group">
                            <label class="product-filter-label">Từ khóa</label>
                            <input type="text" name="query" value="{{ $searchQuery }}" class="product-filter-input"
                                   placeholder="Nhập tên sản phẩm...">
                        </div>
                    @endif

                    <div class="product-filter-group">
                        <label class="product-filter-label">Danh mục</label>
                        <select name="category" class="product-filter-select" onchange="this.form.submit()">
                            <option value="">Tất cả danh mục</option>
                            @foreach($cate as $category)
                                <option value="{{ $category->slug }}"
                                    {{ isset($selectedCategory) && $selectedCategory->id === $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="product-filter-group">
                        <label class="product-filter-label">Khoảng giá (VND)</label>
                        <div class="product-filter-range-wrapper">
                            <div class="product-filter-range-row">
                                <input type="number" name="minPrice" min="0" step="500000"
                                       value="{{ request('minPrice') }}"
                                       placeholder="Từ">
                                <input type="number" name="maxPrice" min="0" step="500000"
                                       value="{{ request('maxPrice') }}"
                                       placeholder="Đến">
                            </div>
                            <small style="font-size:11px; color:#9ca3af;">Gợi ý: 1.000.000 – 20.000.000</small>
                        </div>
                    </div>

                    <div class="product-filter-group">
                        <label class="product-filter-label">Sắp xếp</label>
                        <select name="sort" class="product-filter-select" onchange="this.form.submit()">
                            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Giá thấp → cao</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Giá cao → thấp</option>
                        </select>
                    </div>

                    <div class="product-filter-apply">
                        <button type="submit" class="btn-filter-apply">Áp dụng bộ lọc</button>
                        <button type="button" class="btn-filter-reset"
                                onclick="window.location='{{ route('client.products.index') }}'">
                            Xóa lọc
                        </button>
                    </div>
                </form>
            </aside>

            {{-- Kết quả bên phải --}}
            <section>
                <div class="product-toolbar">
                    <div class="product-result-count">
                        @if($products->total() > 0)
                            Hiển thị
                            <strong>{{ $products->firstItem() }}</strong>–<strong>{{ $products->lastItem() }}</strong>
                            trên tổng <strong>{{ $products->total() }}</strong> sản phẩm
                        @else
                            Không tìm thấy sản phẩm phù hợp.
                        @endif
                    </div>
                </div>

                @if($products->count() > 0)
                    <div class="product-grid">
                        @foreach ($products as $p)
                            <a href="{{ route('client.product.detail', $p->slug) }}" class="product-card">
                                <div class="product-img">
                                    <img src="{{ $p->image ? asset('storage/'.$p->image) : 'https://via.placeholder.com/400x400?text=No+Image' }}"
                                         alt="{{ $p->name }}">
                                </div>
                                <div class="product-name">{{ $p->name }}</div>
                                <div class="product-price">{{ number_format($p->price, 0, ',', '.') }} đ</div>
                            </a>
                        @endforeach
                    </div>

                    @if($products->hasPages())
                        <div class="product-pagination">
                            {{ $products->appends(request()->except('page'))->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    @endif
                @else
                    <div class="product-empty">
                        Hiện tại chưa có sản phẩm nào phù hợp với bộ lọc. Hãy thử thay đổi tiêu chí lọc hoặc xem tất cả
                        sản phẩm.
                    </div>
                @endif
            </section>
        </div>
    @endif
</div>
@endsection


