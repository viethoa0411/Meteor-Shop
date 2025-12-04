@extends('client.layouts.app')

@section('title', $share->title . ' - Góc Chia Sẻ - Meteor Shop')

@push('head')
<style>
    .share-detail-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 60px 20px;
    }

    .share-detail-wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        align-items: start;
        margin-bottom: 60px;
    }

    .share-detail-image {
        width: 100%;
        height: auto;
        object-fit: cover;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        background: #f5f5f5;
    }

    .share-detail-content-wrapper {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .share-detail-title-block {
        background: #fff;
        color: #111;
        padding: 30px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .share-detail-title {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1.3;
        margin: 0;
        color: #111;
    }

    .share-detail-excerpt-block {
        background: #fff;
        color: #111;
        padding: 25px;
        border-radius: 8px;
        margin-bottom: 20px;
        flex: 0 0 auto;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .share-detail-excerpt {
        font-size: 1.05rem;
        line-height: 1.6;
        margin: 0;
        color: #666;
        font-style: italic;
    }

    .share-detail-content-block {
        background: #fff;
        color: #111;
        padding: 30px;
        border-radius: 8px;
        flex: 1;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .share-detail-content {
        font-size: 1.05rem;
        line-height: 1.8;
        color: #333;
    }

    .related-shares-section {
        margin-top: 80px;
        padding-top: 60px;
        border-top: 1px solid #eee;
    }

    .related-shares-title {
        font-size: 1.8rem;
        font-weight: 600;
        color: #111;
        margin-bottom: 40px;
        text-align: center;
    }

    .related-shares-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 30px;
    }

    .related-share-card {
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .related-share-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .related-share-card-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        background: #f5f5f5;
    }

    .related-share-card-body {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .related-share-card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #111;
        margin-bottom: 10px;
        line-height: 1.4;
        transition: color 0.3s;
    }

    .related-share-card-title:hover {
        color: #ffb703;
    }

    .related-share-card-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    @media (max-width: 968px) {
        .share-detail-wrapper {
            grid-template-columns: 1fr;
            gap: 30px;
        }
    }
</style>
@endpush

@section('content')
<div class="share-detail-container">
    <div class="share-detail-wrapper">
        @if($share->thumbnail)
            <img src="{{ asset('blogs/images/' . $share->thumbnail) }}" alt="{{ $share->title }}" class="share-detail-image">
        @endif
        <div class="share-detail-content-wrapper">
            <div class="share-detail-title-block">
                <h1 class="share-detail-title">{{ $share->title }}</h1>
            </div>
            @if($share->excerpt)
                <div class="share-detail-excerpt-block">
                    <p class="share-detail-excerpt">{{ $share->excerpt }}</p>
                </div>
            @endif
            <div class="share-detail-content-block">
                <div class="share-detail-content">
                    {!! \App\Helpers\TextHelper::sanitizeHtml($share->content) !!}
                </div>
            </div>
        </div>
    </div>

    @if($relatedShares && $relatedShares->count() > 0)
        <div class="related-shares-section">
            <h2 class="related-shares-title">Bài Viết Liên Quan</h2>
            <div class="related-shares-grid">
                @foreach($relatedShares as $related)
                    <article class="related-share-card">
                        <a href="{{ route('client.shares.show', $related->slug) }}" class="related-share-card-link">
                            <div class="related-share-card-image-wrapper">
                                @if($related->thumbnail)
                                    <img src="{{ asset('blogs/images/' . $related->thumbnail) }}" alt="{{ $related->title }}" class="related-share-card-image">
                                @else
                                    <img src="https://via.placeholder.com/400x200?text=No+Image" alt="{{ $related->title }}" class="related-share-card-image">
                                @endif
                            </div>
                            <div class="related-share-card-body">
                                <h3 class="related-share-card-title">{{ $related->title }}</h3>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

