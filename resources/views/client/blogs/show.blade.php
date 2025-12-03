@extends('client.layouts.app')

@section('title', $blog->title . ' - Meteor Shop')

@push('head')
<style>
    .blog-detail-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 60px 20px;
    }

    .blog-detail-wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        align-items: start;
        margin-bottom: 60px;
    }

    .blog-detail-image {
        width: 100%;
        height: auto;
        object-fit: cover;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        background: #f5f5f5;
    }

    .blog-detail-content-wrapper {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .blog-detail-title-block {
        background: #fff;
        color: #111;
        padding: 30px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .blog-detail-title {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1.3;
        margin: 0;
        color: #111;
    }

    .blog-detail-excerpt-block {
        background: #fff;
        color: #111;
        padding: 25px;
        border-radius: 8px;
        margin-bottom: 20px;
        flex: 0 0 auto;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .blog-detail-excerpt {
        font-size: 1.05rem;
        line-height: 1.6;
        margin: 0;
        color: #666;
        font-style: italic;
    }

    .blog-detail-content-block {
        background: #fff;
        color: #111;
        padding: 30px;
        border-radius: 8px;
        flex: 1;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .blog-detail-content {
        font-size: 1.05rem;
        line-height: 1.8;
        color: #333;
    }

    .blog-detail-content h1,
    .blog-detail-content h2,
    .blog-detail-content h3,
    .blog-detail-content h4 {
        margin-top: 25px;
        margin-bottom: 15px;
        color: #111;
        font-weight: 600;
    }

    .blog-detail-content h1 {
        font-size: 1.8rem;
    }

    .blog-detail-content h2 {
        font-size: 1.5rem;
    }

    .blog-detail-content h3 {
        font-size: 1.3rem;
    }

    .blog-detail-content p {
        margin-bottom: 18px;
        color: #333;
    }

    .blog-detail-content img {
        max-width: 100%;
        height: auto;
        border-radius: 6px;
        margin: 20px 0;
    }

    .blog-detail-content ul,
    .blog-detail-content ol {
        margin-bottom: 18px;
        padding-left: 25px;
        color: #333;
    }

    .blog-detail-content li {
        margin-bottom: 8px;
        color: #333;
    }

    .blog-detail-content blockquote {
        border-left: 4px solid #ffb703;
        padding-left: 20px;
        margin: 20px 0;
        font-style: italic;
        color: #666;
        background: #f9f9f9;
        padding: 15px 20px;
        border-radius: 4px;
    }

    .blog-detail-content a {
        color: #09f;
        text-decoration: underline;
        font-weight: 500;
    }

    .blog-detail-content a:hover {
        color: #ffb703;
    }

    .blog-detail-content strong {
        color: #111;
        font-weight: 600;
    }

    .blog-actions {
        display: flex;
        justify-content: center;
        margin-bottom: 60px;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 28px;
        background: #111;
        color: #fff;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s;
        font-size: 1rem;
    }

    .btn-back:hover {
        background: #ffb703;
        color: #111;
        transform: translateY(-2px);
    }

    .related-blogs {
        margin-top: 80px;
    }

    .related-blogs-title {
        font-size: 2rem;
        font-weight: 600;
        margin-bottom: 40px;
        text-align: center;
        color: #111;
    }

    .related-blogs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 30px;
    }

    .related-blog-card {
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .related-blog-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .related-blog-card-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        background: #f5f5f5;
    }

    .related-blog-card-body {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .related-blog-card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #111;
        margin-bottom: 10px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        transition: color 0.3s;
    }

    .related-blog-card-title:hover {
        color: #ffb703;
    }

    .related-blog-card-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.85rem;
        color: #999;
        margin-top: auto;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }

    .related-blog-card-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    @media (max-width: 968px) {
        .blog-detail-wrapper {
            grid-template-columns: 1fr;
            gap: 30px;
        }

        .blog-detail-image {
            max-height: 500px;
            object-fit: cover;
        }

        .blog-detail-title {
            font-size: 1.6rem;
        }

        .blog-detail-content {
            font-size: 1rem;
        }

        .related-blogs-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .blog-detail-container {
            padding: 40px 15px;
        }

        .blog-detail-title {
            font-size: 1.4rem;
        }

        .blog-detail-title-block,
        .blog-detail-excerpt-block,
        .blog-detail-content-block {
            padding: 20px;
        }

        .related-blogs-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="blog-detail-container">
    <div class="blog-detail-wrapper">
        {{-- Ảnh bên trái --}}
        <div class="blog-detail-image-wrapper">
            @if($blog->thumbnail)
                <img src="{{ asset('blog/images/' . $blog->thumbnail) }}" alt="{{ $blog->title }}" class="blog-detail-image">
            @else
                <img src="https://via.placeholder.com/600x600?text=No+Image" alt="{{ $blog->title }}" class="blog-detail-image">
            @endif
        </div>

        {{-- Nội dung bên phải --}}
        <div class="blog-detail-content-wrapper">
            {{-- Tiêu đề --}}
            <div class="blog-detail-title-block">
                <h1 class="blog-detail-title">{{ $blog->title }}</h1>
            </div>

            {{-- Mô tả --}}
            @if($blog->excerpt)
                <div class="blog-detail-excerpt-block">
                    <p class="blog-detail-excerpt">{{ $blog->excerpt }}</p>
                </div>
            @endif

            {{-- Nội dung --}}
            <div class="blog-detail-content-block">
                <div class="blog-detail-content">
                    {!! \App\Helpers\TextHelper::sanitizeHtml($blog->content) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="blog-actions">
        <a href="{{ route('client.blogs.list') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i>
            <span>Quay lại danh sách bài viết</span>
        </a>
    </div>

    @if($relatedBlogs->count() > 0)
        <div class="related-blogs">
            <h2 class="related-blogs-title">Bài viết liên quan</h2>
            <div class="related-blogs-grid">
                @foreach($relatedBlogs as $relatedBlog)
                    <article class="related-blog-card">
                        <a href="{{ route('client.blog.show', $relatedBlog->slug) }}" class="related-blog-card-link">
                            @if($relatedBlog->thumbnail)
                                <img src="{{ asset('blogs/images/' . $relatedBlog->thumbnail) }}" alt="{{ $relatedBlog->title }}" class="related-blog-card-image">
                            @else
                                <img src="https://via.placeholder.com/400x250?text=No+Image" alt="{{ $relatedBlog->title }}" class="related-blog-card-image">
                            @endif
                            <div class="related-blog-card-body">
                                <h3 class="related-blog-card-title">{{ $relatedBlog->title }}</h3>
                                <div class="related-blog-card-meta">
                                    <div>
                                        <i class="bi bi-person-circle"></i>
                                        <span>{{ $relatedBlog->user->name ?? 'Admin' }}</span>
                                    </div>
                                    <div>
                                        <i class="bi bi-calendar3"></i>
                                        <span>{{ $relatedBlog->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
