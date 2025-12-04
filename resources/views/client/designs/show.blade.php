@extends('client.layouts.app')

@section('title', $design->title . ' - Thiết Kế Nội Thất - Meteor Shop')

@push('head')
<style>
    .design-detail-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 60px 20px;
    }

    .design-detail-wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        align-items: start;
        margin-bottom: 60px;
    }

    .design-detail-image {
        width: 100%;
        height: auto;
        object-fit: cover;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        background: #f5f5f5;
    }

    .design-detail-content-wrapper {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .design-detail-title-block {
        background: #fff;
        color: #111;
        padding: 30px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .design-detail-title {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1.3;
        margin: 0;
        color: #111;
    }

    .design-detail-excerpt-block {
        background: #fff;
        color: #111;
        padding: 25px;
        border-radius: 8px;
        margin-bottom: 20px;
        flex: 0 0 auto;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .design-detail-excerpt {
        font-size: 1.05rem;
        line-height: 1.6;
        margin: 0;
        color: #666;
        font-style: italic;
    }

    .design-detail-content-block {
        background: #fff;
        color: #111;
        padding: 30px;
        border-radius: 8px;
        flex: 1;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .design-detail-content {
        font-size: 1.05rem;
        line-height: 1.8;
        color: #333;
    }

    .related-designs-section {
        margin-top: 80px;
        padding-top: 60px;
        border-top: 1px solid #eee;
    }

    .related-designs-title {
        font-size: 1.8rem;
        font-weight: 600;
        color: #111;
        margin-bottom: 40px;
        text-align: center;
    }

    .related-designs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 30px;
    }

    .related-design-card {
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .related-design-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .related-design-card-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        background: #f5f5f5;
    }

    .related-design-card-body {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .related-design-card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #111;
        margin-bottom: 10px;
        line-height: 1.4;
        transition: color 0.3s;
    }

    .related-design-card-title:hover {
        color: #ffb703;
    }

    .related-design-card-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    @media (max-width: 968px) {
        .design-detail-wrapper {
            grid-template-columns: 1fr;
            gap: 30px;
        }
    }
</style>
@endpush

@section('content')
<div class="design-detail-container">
    <div class="design-detail-wrapper">
        @if($design->thumbnail)
            <img src="{{ asset('blogs/images/' . $design->thumbnail) }}" alt="{{ $design->title }}" class="design-detail-image">
        @endif
        <div class="design-detail-content-wrapper">
            <div class="design-detail-title-block">
                <h1 class="design-detail-title">{{ $design->title }}</h1>
            </div>
            @if($design->excerpt)
                <div class="design-detail-excerpt-block">
                    <p class="design-detail-excerpt">{{ $design->excerpt }}</p>
                </div>
            @endif
            <div class="design-detail-content-block">
                <div class="design-detail-content">
                    {!! \App\Helpers\TextHelper::sanitizeHtml($design->content) !!}
                </div>
            </div>
        </div>
    </div>

    @if($relatedDesigns && $relatedDesigns->count() > 0)
        <div class="related-designs-section">
            <h2 class="related-designs-title">Bài Viết Liên Quan</h2>
            <div class="related-designs-grid">
                @foreach($relatedDesigns as $related)
                    <article class="related-design-card">
                        <a href="{{ route('client.designs.show', $related->slug) }}" class="related-design-card-link">
                            <div class="related-design-card-image-wrapper">
                                @if($related->thumbnail)
                                    <img src="{{ asset('blogs/images/' . $related->thumbnail) }}" alt="{{ $related->title }}" class="related-design-card-image">
                                @else
                                    <img src="https://via.placeholder.com/400x200?text=No+Image" alt="{{ $related->title }}" class="related-design-card-image">
                                @endif
                            </div>
                            <div class="related-design-card-body">
                                <h3 class="related-design-card-title">{{ $related->title }}</h3>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

