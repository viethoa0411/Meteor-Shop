@extends('client.layouts.app')

@section('title', $blog->title . ' - Meteor Shop')

@push('head')
    {{-- Tailwind CDN cho trang bài viết (chỉ ảnh hưởng lớp utility) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Giữ nội dung rich-text hiển thị đẹp */
        .blog-content p { margin-bottom: 0.9rem; }
        .blog-content h1, .blog-content h2, .blog-content h3, .blog-content h4 {
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            font-weight: 600;
        }
        .blog-content ul, .blog-content ol {
            padding-left: 1.5rem;
            margin-bottom: 0.9rem;
        }
        .blog-content img {
            max-width: 100%;
            height: auto;
            border-radius: 0.5rem;
            margin: 1.25rem 0;
        }
        .blog-content blockquote {
            border-left: 4px solid #fbbf24;
            padding-left: 1rem;
            margin: 1.25rem 0;
            background: #f9fafb;
            border-radius: 0.375rem;
        }
    </style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10 lg:py-16">
    <div class="grid lg:grid-cols-2 gap-10 items-start mb-12">
        {{-- Ảnh bên trái --}}
        <div class="overflow-hidden rounded-2xl shadow-lg bg-gray-100">
            @if($blog->thumbnail)
                <img src="{{ asset('blogs/images/' . $blog->thumbnail) }}" alt="{{ $blog->title }}"
                     class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
            @else
                <img src="https://via.placeholder.com/800x600?text=Meteor+Shop" alt="{{ $blog->title }}"
                     class="w-full h-full object-cover">
            @endif
        </div>

        {{-- Nội dung bên phải --}}
        <div class="flex flex-col h-full space-y-5">
            {{-- Tiêu đề + meta --}}
            <div class="bg-white rounded-2xl shadow-md px-6 py-5">
                <h1 class="text-2xl lg:text-3xl font-semibold tracking-tight text-slate-900 mb-3">
                    {{ $blog->title }}
                </h1>
                <div class="flex flex-wrap items-center gap-3 text-sm text-slate-500">
                    <span class="inline-flex items-center gap-1.5">
                        <i class="bi bi-person-circle"></i>
                        <span>{{ $blog->user->name ?? 'Admin' }}</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <i class="bi bi-calendar3"></i>
                        <span>
                            {{ optional($blog->published_at ?: $blog->created_at)->format('d/m/Y') }}
                        </span>
                    </span>
                </div>
            </div>

            {{-- Mô tả ngắn --}}
            @if($blog->excerpt)
                <div class="bg-white rounded-2xl shadow px-6 py-5">
                    <p class="italic text-slate-600 leading-relaxed">
                        {{ $blog->excerpt }}
                    </p>
                </div>
            @endif

            {{-- Nội dung chi tiết --}}
            <div class="bg-white rounded-2xl shadow px-6 py-6 lg:py-8">
                <div class="blog-content prose max-w-none text-slate-800">
                    {!! $blog->content !!}
                </div>
            </div>
        </div>
    </div>

    {{-- Nút quay lại --}}
    <div class="flex justify-center mb-12">
        <a href="{{ route('client.blogs.list') }}"
           class="inline-flex items-center gap-2 rounded-full bg-slate-900 text-white px-6 py-3 text-sm font-medium shadow hover:bg-amber-400 hover:text-slate-900 hover:shadow-lg transition-all duration-200">
            <i class="bi bi-arrow-left"></i>
            <span>Quay lại danh sách bài viết</span>
        </a>
    </div>

    @php
        // Đảm bảo biến luôn tồn tại kể cả khi gọi từ admin preview
        $relatedBlogs = $relatedBlogs ?? collect();
    @endphp

    @if($relatedBlogs->count() > 0)
        <div class="mt-10 lg:mt-16">
            <h2 class="text-2xl font-semibold text-center text-slate-900 mb-8">
                Bài viết liên quan
            </h2>
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($relatedBlogs as $relatedBlog)
                    <article class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-shadow duration-200 overflow-hidden flex flex-col h-full opacity-0 translate-y-3 related-card">
                        <a href="{{ route('client.blog.show', $relatedBlog->slug) }}" class="block h-full">
                            @if($relatedBlog->thumbnail)
                                <img src="{{ asset('blogs/images/' . $relatedBlog->thumbnail) }}"
                                     alt="{{ $relatedBlog->title }}"
                                     class="w-full h-48 object-cover">
                            @else
                                <img src="https://via.placeholder.com/400x250?text=Meteor+Shop"
                                     alt="{{ $relatedBlog->title }}"
                                     class="w-full h-48 object-cover">
                            @endif
                            <div class="p-4 flex flex-col h-full">
                                <h3 class="text-base font-semibold text-slate-900 mb-2 line-clamp-2">
                                    {{ $relatedBlog->title }}
                                </h3>
                                <div class="mt-auto pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                                    <div class="flex items-center gap-1.5">
                                        <i class="bi bi-person-circle"></i>
                                        <span>{{ $relatedBlog->user->name ?? 'Admin' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
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

@push('scripts')
<script>
    // Hiệu ứng fade-in cho card liên quan
    document.addEventListener('DOMContentLoaded', () => {
        const cards = document.querySelectorAll('.related-card');
        if (!('IntersectionObserver' in window) || cards.length === 0) {
            cards.forEach(c => {
                c.classList.remove('opacity-0', 'translate-y-3');
            });
            return;
        }
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.remove('opacity-0', 'translate-y-3');
                    entry.target.classList.add('transition-all', 'duration-500', 'ease-out', 'opacity-100', 'translate-y-0');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.2 });
        cards.forEach(card => observer.observe(card));
    });
</script>
@endpush
