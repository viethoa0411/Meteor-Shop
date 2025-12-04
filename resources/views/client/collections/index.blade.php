@extends('client.layouts.app')

@section('title', 'Bộ Sưu Tập - Meteor Shop')

@push('head')
<style>
    .collection-list-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 60px 20px;
    }

    .collection-list-header {
        text-align: center;
        margin-bottom: 60px;
    }

    .collection-list-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #111;
        margin-bottom: 15px;
        letter-spacing: -0.5px;
    }

    .collection-list-header p {
        font-size: 1.1rem;
        color: #666;
        max-width: 700px;
        margin: 0 auto;
    }

    .collection-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 40px 30px;
        margin-bottom: 60px;
    }

    .collection-card {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        cursor: pointer;
    }

    .collection-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    }

    .collection-card-image-wrapper {
        position: relative;
        overflow: hidden;
        background: #f5f5f5;
        height: 280px;
    }

    .collection-card-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
        display: block;
    }

    .collection-card:hover .collection-card-image {
        transform: scale(1.1);
    }

    .collection-card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom, transparent 0%, rgba(0, 0, 0, 0.5) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .collection-card:hover .collection-card-overlay {
        opacity: 1;
    }

    .collection-card-body {
        padding: 24px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .collection-card-title {
        font-size: 1.35rem;
        font-weight: 600;
        color: #111;
        margin-bottom: 12px;
        line-height: 1.4;
        transition: color 0.3s;
    }

    .collection-card:hover .collection-card-title {
        color: #ffb703;
    }

    .collection-card-description {
        color: #666;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 16px;
        flex: 1;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .collection-card-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .collection-empty {
        text-align: center;
        padding: 100px 20px;
        color: #999;
    }

    .collection-empty i {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .collection-empty h3 {
        font-size: 1.5rem;
        margin-bottom: 10px;
        color: #666;
    }

    .collection-empty p {
        font-size: 1rem;
        color: #999;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin: 60px 0 40px;
    }

    .pagination {
        display: flex;
        gap: 8px;
        list-style: none;
        padding: 0;
        margin: 0;
        align-items: center;
    }

    .pagination li {
        display: inline-block;
    }

    .pagination a,
    .pagination span {
        display: block;
        padding: 10px 16px;
        border-radius: 6px;
        text-decoration: none;
        color: #333;
        background: #fff;
        border: 1px solid #e0e0e0;
        transition: all 0.3s;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .pagination a:hover {
        background: #111;
        color: #fff;
        border-color: #111;
    }

    .pagination .active span {
        background: #111;
        color: #fff;
        border-color: #111;
    }

    .pagination .disabled span {
        opacity: 0.4;
        cursor: not-allowed;
        background: #f5f5f5;
    }

    @media (max-width: 768px) {
        .collection-list-container {
            padding: 40px 15px;
        }

        .collection-list-header h1 {
            font-size: 2rem;
        }

        .collection-list-header p {
            font-size: 1rem;
        }

        .collection-grid {
            grid-template-columns: 1fr;
            gap: 30px;
        }

        .collection-card-image-wrapper {
            height: 220px;
        }

        .collection-card-body {
            padding: 20px;
        }

        .collection-card-title {
            font-size: 1.2rem;
        }
    }
</style>
@endpush

@section('content')
<div class="collection-list-container">
    <!-- Tiêu đề trang -->
    <div class="collection-list-header">
        <h1>Bộ Sưu Tập Nội Thất</h1>
        <p>Khám phá những bộ sưu tập nội thất độc đáo, được tuyển chọn kỹ lưỡng từ Meteor Shop – nơi hội tụ những sản phẩm tinh tế cho không gian sống của bạn.</p>
    </div>

    @if($collections->count() > 0)
        <div class="collection-grid">
            @foreach($collections as $collection)
                <article class="collection-card">
                    <a href="{{ route('client.collections.show', $collection->slug) }}" class="collection-card-link">
                        <div class="collection-card-image-wrapper">
                            @if($collection->image)
                                <img src="{{ $collection->image_url }}" alt="{{ $collection->name }}" class="collection-card-image" onerror="this.src='https://via.placeholder.com/400x280?text=No+Image'">
                            @else
                                <img src="https://via.placeholder.com/400x280?text=No+Image" alt="{{ $collection->name }}" class="collection-card-image">
                            @endif
                            <div class="collection-card-overlay"></div>
                        </div>
                        <div class="collection-card-body">
                            <h2 class="collection-card-title">{{ $collection->name }}</h2>
                            @if($collection->description)
                                <p class="collection-card-description">{{ $collection->description }}</p>
                            @endif
                        </div>
                    </a>
                </article>
            @endforeach
        </div>

        @if($collections->hasPages())
            <div class="pagination-wrapper">
                {{ $collections->links('vendor.pagination.bootstrap-5') }}
            </div>
        @endif
    @else
        <div class="collection-empty">
            <i class="bi bi-collection"></i>
            <h3>Chưa có bộ sưu tập nào</h3>
            <p>Hiện tại chưa có bộ sưu tập nào được xuất bản. Vui lòng quay lại sau!</p>
        </div>
    @endif
</div>
@endsection

