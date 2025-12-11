@extends('client.layouts.app')

@section('title', 'Góc Chia Sẻ - Meteor Shop')

@push('head')
<style>
    .share-list-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 60px 20px;
    }

    .share-list-header {
        text-align: center;
        margin-bottom: 60px;
    }

    .share-list-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #111;
        margin-bottom: 15px;
        letter-spacing: -0.5px;
    }

    .share-list-header p {
        font-size: 1.1rem;
        color: #666;
        max-width: 700px;
        margin: 0 auto;
    }

    .share-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 40px 30px;
        margin-bottom: 60px;
    }

    .share-card {
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .share-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .share-card-image-wrapper {
        position: relative;
        overflow: hidden;
        background: #f5f5f5;
    }

    .share-card-image {
        width: 100%;
        height: 220px;
        object-fit: cover;
        transition: transform 0.4s ease;
        display: block;
    }

    .share-card:hover .share-card-image {
        transform: scale(1.05);
    }

    .share-card-body {
        padding: 24px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .share-card-title {
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

    .share-card-title:hover {
        color: #ffb703;
    }

    .share-card-excerpt {
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

    .share-card-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 16px;
        border-top: 1px solid #eee;
        font-size: 0.85rem;
        color: #999;
    }

    .share-card-author {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .share-card-date {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .share-card-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .share-empty {
        text-align: center;
        padding: 100px 20px;
        color: #999;
    }

    .share-empty i {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .share-empty h3 {
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
        .share-list-container {
            padding: 40px 15px;
        }

        .share-list-header h1 {
            font-size: 2rem;
        }

        .share-grid {
            grid-template-columns: 1fr;
            gap: 30px;
        }
    }
</style>
@endpush

@section('content')
<div class="share-list-container">
    <div class="share-list-header">
        <h1>Góc Chia Sẻ</h1>
        <p>Nơi cộng đồng chia sẻ kinh nghiệm, ý tưởng và cảm nhận về không gian sống. Hãy cùng nhau tạo nên những điều tuyệt vời!</p>
    </div>

    @if($shares->count() > 0)
        <div class="share-grid">
            @foreach($shares as $share)
                <article class="share-card">
                    <a href="{{ route('client.shares.show', $share->slug) }}" class="share-card-link">
                        <div class="share-card-image-wrapper">
                            @if($share->thumbnail)
                                <img src="{{ asset('blogs/images/' . $share->thumbnail) }}" alt="{{ $share->title }}" class="share-card-image">
                            @else
                                <img src="https://via.placeholder.com/400x250?text=No+Image" alt="{{ $share->title }}" class="share-card-image">
                            @endif
                        </div>
                        <div class="share-card-body">
                            <h2 class="share-card-title">{{ $share->title }}</h2>
                            @if($share->excerpt)
                                <p class="share-card-excerpt">{{ $share->excerpt }}</p>
                            @else
                                <p class="share-card-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($share->content), 150) }}</p>
                            @endif
                            <div class="share-card-meta">
                                <div class="share-card-author">
                                    <i class="bi bi-person-circle"></i>
                                    <span>{{ $share->user->name ?? 'Admin' }}</span>
                                </div>
                                <div class="share-card-date">
                                    <i class="bi bi-calendar3"></i>
                                    <span>{{ $share->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </article>
            @endforeach
        </div>

        @if($shares->hasPages())
            <div class="pagination-wrapper">
                {{ $shares->links('vendor.pagination.bootstrap-5') }}
            </div>
        @endif
    @else
        <div class="share-empty">
            <i class="bi bi-chat-heart"></i>
            <h3>Chưa có bài viết nào trong góc chia sẻ</h3>
            <p>Hiện tại chưa có bài viết nào được xuất bản. Vui lòng quay lại sau!</p>
        </div>
    @endif
</div>
@endsection

