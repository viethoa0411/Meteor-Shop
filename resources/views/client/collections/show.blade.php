@extends('client.layouts.app')

@section('title', $collection->name . ' - Bộ Sưu Tập - Meteor Shop')

@push('head')
<style>
    .collection-detail-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 60px 20px;
    }

    .collection-detail-header {
        text-align: center;
        margin-bottom: 60px;
    }

    .collection-detail-image {
        width: 100%;
        max-width: 800px;
        height: auto;
        max-height: 500px;
        object-fit: cover;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        margin: 0 auto 30px;
        display: block;
        background: #f5f5f5;
    }

    .collection-detail-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #111;
        margin-bottom: 20px;
        letter-spacing: -0.5px;
    }

    .collection-detail-description {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #666;
        max-width: 800px;
        margin: 0 auto;
    }

    .collection-products-section {
        margin-top: 80px;
    }

    .collection-products-title {
        font-size: 1.8rem;
        font-weight: 600;
        color: #111;
        margin-bottom: 40px;
        text-align: center;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 30px;
        margin-bottom: 60px;
    }

    .product-card {
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .product-card-image-wrapper {
        position: relative;
        overflow: hidden;
        background: #f5f5f5;
    }

    .product-card-image {
        width: 100%;
        aspect-ratio: 1/1;
        object-fit: cover;
        transition: transform 0.4s ease;
        display: block;
    }

    .product-card:hover .product-card-image {
        transform: scale(1.05);
    }

    .product-card-body {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .product-card-name {
        font-size: 1rem;
        font-weight: 600;
        color: #111;
        margin-bottom: 12px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        transition: color 0.3s;
    }

    .product-card-name:hover {
        color: #ffb703;
    }

    .product-card-price {
        color: #d41;
        font-weight: 600;
        font-size: 1.1rem;
        margin-top: auto;
    }

    .product-card-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .products-empty {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }

    .products-empty i {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.5;
    }

    .products-empty h3 {
        font-size: 1.3rem;
        margin-bottom: 10px;
        color: #666;
    }

    .related-collections-section {
        margin-top: 80px;
        padding-top: 60px;
        border-top: 1px solid #eee;
    }

    .related-collections-title {
        font-size: 1.8rem;
        font-weight: 600;
        color: #111;
        margin-bottom: 40px;
        text-align: center;
    }

    .related-collections-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 30px;
    }

    .related-collection-card {
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .related-collection-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .related-collection-card-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        background: #f5f5f5;
    }

    .related-collection-card-body {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .related-collection-card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #111;
        margin-bottom: 10px;
        line-height: 1.4;
        transition: color 0.3s;
    }

    .related-collection-card-title:hover {
        color: #ffb703;
    }

    .related-collection-card-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    @media (max-width: 768px) {
        .collection-detail-container {
            padding: 40px 15px;
        }

        .collection-detail-title {
            font-size: 2rem;
        }

        .collection-detail-description {
            font-size: 1rem;
        }

        .products-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .related-collections-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="collection-detail-container">
    <!-- Header -->
    <div class="collection-detail-header">
        @if($collection->image)
            <img src="{{ $collection->image_url }}" alt="{{ $collection->name }}" class="collection-detail-image" onerror="this.src='https://via.placeholder.com/800x500?text=No+Image'">
        @endif
        <h1 class="collection-detail-title">{{ $collection->name }}</h1>
        @if($collection->description)
            <div class="collection-detail-description">
                {{ $collection->description }}
            </div>
        @endif
    </div>

    <!-- Products Section -->
    @if($collection->products && $collection->products->count() > 0)
        <div class="collection-products-section">
            <h2 class="collection-products-title">Sản Phẩm Trong Bộ Sưu Tập</h2>
            <div class="products-grid">
                @foreach($collection->products as $product)
                    <article class="product-card">
                        <a href="{{ route('client.product.detail', $product->slug) }}" class="product-card-link">
                            <div class="product-card-image-wrapper">
                                @if($product->image)
                                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="product-card-image" onerror="this.src='https://via.placeholder.com/300x300?text=No+Image'">
                                @else
                                    <img src="https://via.placeholder.com/300x300?text=No+Image" alt="{{ $product->name }}" class="product-card-image">
                                @endif
                            </div>
                            <div class="product-card-body">
                                <h3 class="product-card-name">{{ $product->name }}</h3>
                                <div class="product-card-price">{{ number_format($product->price, 0, ',', '.') }}₫</div>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    @else
        <div class="products-empty">
            <i class="bi bi-box-seam"></i>
            <h3>Chưa có sản phẩm nào trong bộ sưu tập này</h3>
            <p>Bộ sưu tập đang được cập nhật. Vui lòng quay lại sau!</p>
        </div>
    @endif

    <!-- Related Collections -->
    @if($relatedCollections && $relatedCollections->count() > 0)
        <div class="related-collections-section">
            <h2 class="related-collections-title">Bộ Sưu Tập Liên Quan</h2>
            <div class="related-collections-grid">
                @foreach($relatedCollections as $related)
                    <article class="related-collection-card">
                        <a href="{{ route('client.collections.show', $related->slug) }}" class="related-collection-card-link">
                            <div class="related-collection-card-image-wrapper">
                                @if($related->image)
                                    <img src="{{ $related->image_url }}" alt="{{ $related->name }}" class="related-collection-card-image" onerror="this.src='https://via.placeholder.com/400x200?text=No+Image'">
                                @else
                                    <img src="https://via.placeholder.com/400x200?text=No+Image" alt="{{ $related->name }}" class="related-collection-card-image">
                                @endif
                            </div>
                            <div class="related-collection-card-body">
                                <h3 class="related-collection-card-title">{{ $related->name }}</h3>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

