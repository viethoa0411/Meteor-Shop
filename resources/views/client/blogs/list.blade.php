@extends('client.layouts.app')

@section('title', 'Bài Viết - Meteor Shop')

@push('head')
<style>
    .blog-list-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 60px 20px;
    }

    .blog-list-header {
        text-align: center;
        margin-bottom: 60px;
    }

    .blog-list-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #111;
        margin-bottom: 15px;
        letter-spacing: -0.5px;
    }

    .blog-list-header p {
        font-size: 1.1rem;
        color: #666;
        max-width: 700px;
        margin: 0 auto;
    }

    .blog-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 40px 30px;
        margin-bottom: 60px;
    }

    .blog-card {
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .blog-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .blog-card-image-wrapper {
        position: relative;
        overflow: hidden;
        background: #f5f5f5;
    }

    .blog-card-image {
        width: 100%;
        height: 220px;
        object-fit: cover;
        transition: transform 0.4s ease;
        display: block;
    }

    .blog-card:hover .blog-card-image {
        transform: scale(1.05);
    }

    .blog-card-body {
        padding: 24px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .blog-card-title {
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

    .blog-card-title:hover {
        color: #ffb703;
    }

    .blog-card-excerpt {
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

    .blog-card-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 16px;
        border-top: 1px solid #eee;
        font-size: 0.85rem;
        color: #999;
    }

    .blog-card-author {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .blog-card-author i {
        font-size: 0.9rem;
    }

    .blog-card-date {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .blog-card-date i {
        font-size: 0.85rem;
    }

    .blog-card-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .blog-empty {
        text-align: center;
        padding: 100px 20px;
        color: #999;
    }

    .blog-empty i {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .blog-empty h3 {
        font-size: 1.5rem;
        margin-bottom: 10px;
        color: #666;
    }

    .blog-empty p {
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
        .blog-list-container {
            padding: 40px 15px;
        }

        .blog-list-header h1 {
            font-size: 2rem;
        }

        .blog-list-header p {
            font-size: 1rem;
        }

        .blog-grid {
            grid-template-columns: 1fr;
            gap: 30px;
        }

        .blog-card-image {
            height: 200px;
        }

        .blog-card-body {
            padding: 20px;
        }

        .blog-card-title {
            font-size: 1.1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="blog-list-container">

        <!-- Tiêu đề trang -->
    <div class="blog-list-header">
        <h1>Tin Tức & Bài Viết Về Nội Thất</h1>
        <p>Cập nhật xu hướng thiết kế, mẹo trang trí không gian sống và những chia sẻ hữu ích từ Meteor Shop – nơi tôn vinh phong cách sống hiện đại.</p>
    </div>

    @if($blogs->count() > 0)
        <div class="blog-grid">
            @foreach($blogs as $blog)
                <article class="blog-card">
                    <a href="{{ route('client.blog.show', $blog->slug) }}" class="blog-card-link">
                        <div class="blog-card-image-wrapper">
                            @if($blog->thumbnail)
                                <img src="{{ asset('blogs/images/' . $blog->thumbnail) }}" alt="{{ $blog->title }}" class="blog-card-image">
                            @else
                                <img src="https://via.placeholder.com/400x250?text=No+Image" alt="{{ $blog->title }}" class="blog-card-image">
                            @endif
                        </div>
                        <div class="blog-card-body">
                            <h2 class="blog-card-title">{{ $blog->title }}</h2>
                            @if($blog->excerpt)
                                <p class="blog-card-excerpt">{{ $blog->excerpt }}</p>
                            @else
                                <p class="blog-card-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($blog->content), 150) }}</p>
                            @endif
                            <div class="blog-card-meta">
                                <div class="blog-card-author">
                                    <i class="bi bi-person-circle"></i>
                                    <span>{{ $blog->user->name ?? 'Admin' }}</span>
                                </div>
                                <div class="blog-card-date">
                                    <i class="bi bi-calendar3"></i>
                                    <span>{{ $blog->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </article>
            @endforeach
        </div>

        @if($blogs->hasPages())
            <div class="pagination-wrapper">
                <ul class="pagination">
                    {{-- Previous Page Link --}}
                    @if ($blogs->onFirstPage())
                        <li class="disabled"><span><i class="bi bi-chevron-left"></i></span></li>
                    @else
                        <li><a href="{{ $blogs->previousPageUrl() }}" rel="prev"><i class="bi bi-chevron-left"></i></a></li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($blogs->getUrlRange(1, $blogs->lastPage()) as $page => $url)
                        @if ($page == $blogs->currentPage())
                            <li class="active"><span>{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($blogs->hasMorePages())
                        <li><a href="{{ $blogs->nextPageUrl() }}" rel="next"><i class="bi bi-chevron-right"></i></a></li>
                    @else
                        <li class="disabled"><span><i class="bi bi-chevron-right"></i></span></li>
                    @endif
                </ul>
            </div>
        @endif
    @else
        <div class="blog-empty">
            <i class="bi bi-journal-x"></i>
            <h3>Chưa có bài viết nào</h3>
            <p>Hiện tại chưa có bài viết nào được xuất bản. Vui lòng quay lại sau!</p>
        </div>
    @endif
</div>
@endsection
