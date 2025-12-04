@extends('client.layouts.app')

@section('title', $title ?? 'Phòng')

@push('head')
<style>
    .room-page {
        max-width: 1320px;
        margin: 0 auto;
        padding: 40px 16px 60px;
    }

    .room-page-header {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 24px;
    }

    .room-page-title {
        font-size: 26px;
        font-weight: 700;
        letter-spacing: -0.03em;
        color: #111827;
    }

    .room-page-subtitle {
        font-size: 14px;
        color: #6b7280;
    }

    .room-page-layout {
        display: grid;
        grid-template-columns: minmax(0, 280px) minmax(0, 1fr);
        gap: 24px;
        align-items: flex-start;
    }

    .room-filter-card {
        background: #f9fafb;
        border-radius: 16px;
        padding: 18px 18px 20px;
        border: 1px solid #e5e7eb;
        position: sticky;
        top: 96px;
        width: 100%;
        box-sizing: border-box;
        max-width: 100%;
        overflow: hidden;
    }

    .room-filter-title {
        font-size: 15px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 10px;
    }

    .room-filter-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 14px;
    }

    .room-filter-label {
        font-size: 13px;
        font-weight: 500;
        color: #4b5563;
    }

    .room-filter-select,
    .room-filter-input {
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

    .room-filter-range-row {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }

    .room-filter-range-row input[type="number"] {
        flex: 1 1 0;
        min-width: 0;
        border-radius: 999px;
        border: 1px solid #d1d5db;
        padding: 6px 10px;
        font-size: 13px;
        box-sizing: border-box;
    }

    .room-filter-apply {
        margin-top: 8px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
        justify-content: space-between;
    }

    .btn-room-apply {
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

    .btn-room-reset {
        border: none;
        background: none;
        font-size: 12px;
        color: #6b7280;
        cursor: pointer;
        text-decoration: underline;
    }

    .room-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .room-result-count {
        font-size: 13px;
        color: #6b7280;
    }

    .room-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
        gap: 20px;
    }

    .room-empty {
        padding: 60px 20px;
        text-align: center;
        color: #6b7280;
        font-size: 14px;
        background: #f9fafb;
        border-radius: 16px;
        border: 1px dashed #e5e7eb;
    }

    .room-pagination {
        margin-top: 24px;
        display: flex;
        justify-content: center;
    }

    .room-pagination .pagination {
        gap: 4px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .room-pagination .page-link {
        border-radius: 999px !important;
        min-width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        padding: 0 10px;
    }

    .room-pagination .page-item.active .page-link {
        background-color: #111827;
        border-color: #111827;
        color: #fff;
    }

    .room-pagination .page-link:hover {
        background-color: #e5e7eb;
    }

    @media (max-width: 992px) {
        .room-page-layout {
            grid-template-columns: minmax(0, 1fr);
        }

        .room-filter-card {
            position: static;
            max-width: 100%;
            margin-bottom: 16px;
        }

        .room-pagination {
            margin-top: 20px;
        }
    }

    @media (max-width: 768px) {
        .room-page {
            padding-top: 28px;
        }

        .room-page-title {
            font-size: 22px;
        }
    }
</style>
@endpush

@section('content')
<div class="room-page">
    <div class="room-page-header">
        <h1 class="room-page-title">
            Phòng
        </h1>
        <p class="room-page-subtitle">
            Chọn không gian bạn muốn thiết kế (phòng khách, phòng ngủ, phòng ăn, làm việc...) và lọc nhanh theo ngân sách.
        </p>
    </div>

    <div class="room-page-layout">
        {{-- Bộ lọc bên trái --}}
        <aside>
            <form method="GET" action="{{ route('client.rooms.index') }}" class="room-filter-card">
                <div class="room-filter-title">Bộ lọc phòng</div>

                <div class="room-filter-group">
                    <label class="room-filter-label">Chọn phòng</label>
                    <select name="room" class="room-filter-select" onchange="this.form.submit()">
                        <option value="">Tất cả phòng</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->slug }}"
                                {{ isset($selectedRoom) && $selectedRoom->id === $room->id ? 'selected' : '' }}>
                                {{ $room->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="room-filter-group">
                    <label class="room-filter-label">Khoảng giá (VND)</label>
                    <div class="room-filter-range-row">
                        <input type="number" name="minPrice" min="0" step="500000"
                               value="{{ $minPrice }}"
                               placeholder="Từ">
                        <input type="number" name="maxPrice" min="0" step="500000"
                               value="{{ $maxPrice }}"
                               placeholder="Đến">
                    </div>
                    <small style="font-size:11px; color:#9ca3af;">Ví dụ: 3.000.000 – 30.000.000</small>
                </div>

                <div class="room-filter-group">
                    <label class="room-filter-label">Sắp xếp</label>
                    <select name="sort" class="room-filter-select" onchange="this.form.submit()">
                        <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Giá thấp → cao</option>
                        <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Giá cao → thấp</option>
                    </select>
                </div>

                <div class="room-filter-apply">
                    <button type="submit" class="btn-room-apply">Áp dụng bộ lọc</button>
                    <button type="button" class="btn-room-reset"
                            onclick="window.location='{{ route('client.rooms.index') }}'">
                        Xóa lọc
                    </button>
                </div>
            </form>
        </aside>

        {{-- Kết quả bên phải --}}
        <section>
            <div class="room-toolbar">
                <div class="room-result-count">
                    @if($selectedRoom)
                        <span>Phòng: <strong>{{ $selectedRoom->name }}</strong></span>
                        &nbsp;•&nbsp;
                    @endif

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
                <div class="room-grid">
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
                    <div class="room-pagination">
                        {{ $products->appends(request()->except('page'))->links('vendor.pagination.bootstrap-5') }}
                    </div>
                @endif
            @else
                <div class="room-empty">
                    Hiện tại chưa có sản phẩm nào phù hợp với bộ lọc phòng này. Hãy thử chọn phòng khác
                    hoặc nới rộng khoảng giá.
                </div>
            @endif
        </section>
    </div>
</div>
@endsection


