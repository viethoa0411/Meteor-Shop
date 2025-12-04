@extends('client.layouts.app')

@section('title', 'Thiết Kế Nội Thất - Meteor Shop')

@push('head')
<style>
    .design-list-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 60px 20px;
    }

    .design-list-header {
        text-align: center;
        margin-bottom: 60px;
    }

    .design-list-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #111;
        margin-bottom: 15px;
        letter-spacing: -0.5px;
    }

    .design-list-header p {
        font-size: 1.1rem;
        color: #666;
        max-width: 700px;
        margin: 0 auto;
    }

    .design-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 40px 30px;
        margin-bottom: 60px;
    }

    .design-card {
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .design-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .design-card-image-wrapper {
        position: relative;
        overflow: hidden;
        background: #f5f5f5;
    }

    .design-card-image {
        width: 100%;
        height: 220px;
        object-fit: cover;
        transition: transform 0.4s ease;
        display: block;
    }

    .design-card:hover .design-card-image {
        transform: scale(1.05);
    }

    .design-card-body {
        padding: 24px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .design-card-title {
        font-size: 1.25rem;
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

    .design-card-title:hover {
        color: #ffb703;
    }

    .design-card-excerpt {
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

    .design-card-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 16px;
        border-top: 1px solid #eee;
        font-size: 0.85rem;
        color: #999;
    }

    .design-card-author {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .design-card-date {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .design-card-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .design-empty {
        text-align: center;
        padding: 100px 20px;
        color: #999;
    }

    .design-empty i {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .design-empty h3 {
        font-size: 1.5rem;
        margin-bottom: 10px;
        color: #666;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin: 60px 0 40px;
    }

    @media (max-width: 768px) {
        .design-list-container {
            padding: 40px 15px;
        }

        .design-list-header h1 {
            font-size: 2rem;
        }

        .design-grid {
            grid-template-columns: 1fr;
            gap: 30px;
        }
    }
</style>
@endpush

@section('content')
<div class="design-list-container">
    <div class="design-list-header">
        <h1>Thiết Kế Nội Thất</h1>
        <p>Khám phá những ý tưởng thiết kế nội thất độc đáo, xu hướng mới nhất và cảm hứng trang trí không gian sống từ Meteor Shop.</p>
    </div>

    @if($designs->count() > 0)
        <div class="design-grid">
            @foreach($designs as $design)
                <article class="design-card">
                    <a href="{{ route('client.designs.show', $design->slug) }}" class="design-card-link">
                        <div class="design-card-image-wrapper">
                            @if($design->thumbnail)
                                <img src="{{ asset('blogs/images/' . $design->thumbnail) }}" alt="{{ $design->title }}" class="design-card-image">
                            @else
                                <img src="https://via.placeholder.com/400x250?text=No+Image" alt="{{ $design->title }}" class="design-card-image">
                            @endif
                        </div>
                        <div class="design-card-body">
                            <h2 class="design-card-title">{{ $design->title }}</h2>
                            @if($design->excerpt)
                                <p class="design-card-excerpt">{{ $design->excerpt }}</p>
                            @else
                                <p class="design-card-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($design->content), 150) }}</p>
                            @endif
                            <div class="design-card-meta">
                                <div class="design-card-author">
                                    <i class="bi bi-person-circle"></i>
                                    <span>{{ $design->user->name ?? 'Admin' }}</span>
                                </div>
                                <div class="design-card-date">
                                    <i class="bi bi-calendar3"></i>
                                    <span>{{ $design->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </article>
            @endforeach
        </div>

        @if($designs->hasPages())
            <div class="pagination-wrapper">
                {{ $designs->links('vendor.pagination.bootstrap-5') }}
            </div>
        @endif
    @else
        <div class="design-empty">
            <i class="bi bi-palette"></i>
            <h3>Chưa có bài viết nào về thiết kế nội thất</h3>
            <p>Hiện tại chưa có bài viết nào được xuất bản. Vui lòng quay lại sau!</p>
        </div>
    @endif
</div>
@endsection

