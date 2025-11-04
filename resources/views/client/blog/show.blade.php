@extends('client.layout')

@section('title', $post->meta_title ?: $post->title)

@push('head')
<meta name="description" content="{{ $post->meta_description ?? $post->excerpt }}">
<meta property="og:type" content="article">
<meta property="og:title" content="{{ $post->meta_title ?: $post->title }}">
<meta property="og:description" content="{{ $post->meta_description ?? $post->excerpt }}">
<meta property="og:image" content="{{ $post->og_image ? asset('storage/'.$post->og_image) : ($post->image ? asset('storage/'.$post->image) : '') }}">
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "{{ $post->title }}",
  "image": ["{{ $post->image ? asset('storage/'.$post->image) : '' }}"],
  "datePublished": "{{ optional($post->published_at)->toIso8601String() }}",
  "author": {
    "@type": "Person",
    "name": "Meteor Shop"
  }
}
</script>
@endpush

@section('content')
<section class="max-w-3xl mx-auto px-4 py-8">
    <nav class="text-sm text-gray-500 mb-4"><a class="hover:underline" href="{{ route('client.blog.index') }}">Góc cảm hứng</a> @if($post->category) / <a class="hover:underline" href="{{ route('client.blog.category', $post->category) }}">{{ $post->category->name }}</a>@endif</nav>
    <h1 class="text-3xl font-bold">{{ $post->title }}</h1>
    <div class="text-sm text-gray-500 mt-2">{{ $post->published_at?->format('d/m/Y') }}</div>
    <img class="w-full rounded-lg mt-4" src="{{ $post->image ? asset('storage/'.$post->image) : 'https://via.placeholder.com/800x500?text=No+Image' }}" alt="{{ $post->title }}">
    <article class="prose max-w-none mt-6">{!! $post->content !!}</article>

    @if($post->tags && $post->tags->count())
        <div class="mt-6 text-sm flex flex-wrap gap-2">
            @foreach($post->tags as $t)
                <a class="px-2 py-1 border rounded-full hover:bg-gray-50" href="{{ route('client.blog.tag', $t) }}">#{{ $t->name }}</a>
            @endforeach
        </div>
    @endif

    @if($related->count())
        <div class="mt-10">
            <div class="text-lg font-semibold mb-3">Bài viết liên quan</div>
            <div class="grid md:grid-cols-3 gap-4">
                @foreach($related as $p)
                    <a class="block border rounded-xl overflow-hidden" href="{{ route('client.blog.show', $p->slug) }}">
                        <img class="w-full aspect-[16/10] object-cover" src="{{ $p->image ? asset('storage/'.$p->image) : 'https://via.placeholder.com/640x400?text=No+Image' }}" alt="{{ $p->title }}">
                        <div class="p-3 text-sm font-semibold line-clamp-2">{{ $p->title }}</div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</section>
@endsection


