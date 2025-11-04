@extends('client.layout')

@section('title', 'Góc cảm hứng')

@push('head')
@if(isset($q) && $q)
    <meta name="robots" content="noindex,follow">
@endif
@endpush

@section('content')
<section class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Góc cảm hứng</h1>
        <form class="relative" method="get" action="{{ route('client.blog.index') }}">
            <input name="q" value="{{ $q ?? '' }}" class="border rounded-lg pl-10 pr-3 py-2 text-sm" placeholder="Tìm kiếm bài viết...">
            <span class="absolute left-3 top-2.5 text-gray-400"><i class="fa-solid fa-magnifying-glass"></i></span>
        </form>
    </div>

    <div class="grid md:grid-cols-12 gap-6">
        <aside class="md:col-span-3 space-y-6">
            <div>
                <div class="font-semibold mb-2">Danh mục</div>
                <ul class="space-y-1 text-sm">
                    @foreach(($categories??[]) as $c)
                        <li><a class="hover:underline" href="{{ route('client.blog.category', $c) }}">{{ $c->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <div class="font-semibold mb-2">Nổi bật</div>
                <div class="space-y-3">
                    @foreach(($featured??[]) as $p)
                        <a class="flex items-center gap-3" href="{{ route('client.blog.show', $p->slug) }}">
                            <img class="w-16 h-12 object-cover rounded" src="{{ $p->image ? asset('storage/'.$p->image) : 'https://via.placeholder.com/160x120?text=No+Image' }}" alt="{{ $p->title }}">
                            <div class="text-sm line-clamp-2">{{ $p->title }}</div>
                        </a>
                    @endforeach
                </div>
            </div>
            <div>
                <div class="font-semibold mb-2">Tags</div>
                <div class="flex flex-wrap gap-2 text-xs">
                    @foreach(($tags??[]) as $t)
                        <a class="px-2 py-1 border rounded-full hover:bg-gray-50" href="{{ route('client.blog.tag', $t) }}">#{{ $t->name }}</a>
                    @endforeach
                </div>
            </div>
        </aside>
        <main class="md:col-span-9">
            @if($posts->isEmpty())
                <div class="bg-white border rounded-xl p-6 text-sm text-gray-600">Không có bài viết phù hợp.</div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($posts as $p)
                        <article class="border rounded-xl overflow-hidden bg-white">
                            <a href="{{ route('client.blog.show', $p->slug) }}">
                                <img class="w-full aspect-[16/10] object-cover" src="{{ $p->image ? asset('storage/'.$p->image) : 'https://via.placeholder.com/640x400?text=No+Image' }}" alt="{{ $p->title }}">
                            </a>
                            <div class="p-4">
                                <a class="font-semibold hover:underline" href="{{ route('client.blog.show', $p->slug) }}">{{ $p->title }}</a>
                                <div class="text-xs text-gray-500 mt-1">{{ $p->published_at?->format('d/m/Y') }}</div>
                                <div class="text-sm text-gray-700 line-clamp-3 mt-2">{{ $p->excerpt }}</div>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="mt-6">{{ $posts->links() }}</div>
            @endif
        </main>
    </div>
</section>
@endsection


