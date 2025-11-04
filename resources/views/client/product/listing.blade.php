@extends('client.layout')

@section('title', 'Sản phẩm')

@push('head')
@vite('resources/css/plp.css')
@endpush

@section('content')
<section class="max-w-7xl mx-auto px-4 py-6 md:py-10">
    <div class="flex items-center justify-between gap-3 mb-4">
        <h1 class="text-xl md:text-2xl font-bold text-gray-900">Tất cả sản phẩm</h1>
        <form method="get" class="flex items-center gap-2">
            <select name="sort" class="border rounded-lg px-3 py-2 text-sm" onchange="this.form.submit()">
                <option value="new" {{ ($applied['sort'] ?? 'new')==='new'?'selected':'' }}>Mới nhất</option>
                <option value="price_asc" {{ ($applied['sort'] ?? '')==='price_asc'?'selected':'' }}>Giá tăng dần</option>
                <option value="price_desc" {{ ($applied['sort'] ?? '')==='price_desc'?'selected':'' }}>Giá giảm dần</option>
                <option value="best" {{ ($applied['sort'] ?? '')==='best'?'selected':'' }}>Bán chạy</option>
            </select>
            <input type="hidden" name="q" value="{{ $applied['q'] ?? '' }}" />
            <input type="hidden" name="category" value="{{ $applied['category'] ?? '' }}" />
            <input type="hidden" name="brand" value="{{ $applied['brand'] ?? '' }}" />
            <input type="hidden" name="price_min" value="{{ $applied['price_min'] ?? '' }}" />
            <input type="hidden" name="price_max" value="{{ $applied['price_max'] ?? '' }}" />
            <input type="hidden" name="in_stock" value="{{ $applied['in_stock'] ? 1 : 0 }}" />
            <div class="hidden sm:flex items-center gap-2 ml-3">
                <a href="{{ request()->fullUrlWithQuery(['view'=>'grid']) }}" class="px-2 py-1 border rounded {{ ($applied['view'] ?? 'grid')==='grid' ? 'bg-gray-100' : '' }}" title="Lưới">▦</a>
                <a href="{{ request()->fullUrlWithQuery(['view'=>'list']) }}" class="px-2 py-1 border rounded {{ ($applied['view'] ?? 'grid')==='list' ? 'bg-gray-100' : '' }}" title="Danh sách">☰</a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        {{-- Sidebar filter --}}
        <aside class="md:col-span-3">
            <form id="filterForm" method="get" class="space-y-6">
                <input type="hidden" name="sort" value="{{ $applied['sort'] ?? 'new' }}" />
                <div>
                    <div class="text-sm font-semibold text-gray-800 mb-2">Từ khoá</div>
                    <input name="q" value="{{ $applied['q'] ?? '' }}" placeholder="Tìm theo tên" class="w-full border rounded-lg px-3 py-2 text-sm" />
                </div>

                <div>
                    <div class="text-sm font-semibold text-gray-800 mb-2">Danh mục</div>
                    <div class="space-y-2 max-h-64 overflow-auto pr-1">
                        @foreach($categories as $c)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="radio" name="category" value="{{ $c->slug }}" {{ ($applied['category']??'')===$c->slug?'checked':'' }}>
                                <span>{{ $c->name }}</span>
                            </label>
                            @foreach($c->children as $ch)
                                <label class="flex items-center gap-2 text-sm ml-6">
                                    <input type="radio" name="category" value="{{ $ch->slug }}" {{ ($applied['category']??'')===$ch->slug?'checked':'' }}>
                                    <span>{{ $ch->name }}</span>
                                </label>
                            @endforeach
                        @endforeach
                        <label class="flex items-center gap-2 text-sm"><input type="radio" name="category" value="" {{ empty($applied['category'])?'checked':'' }}> <span>Tất cả</span></label>
                    </div>
                </div>

                <div>
                    <div class="text-sm font-semibold text-gray-800 mb-2">Thương hiệu</div>
                    <div class="grid grid-cols-2 gap-2 max-h-44 overflow-auto pr-1">
                        @foreach($brands as $b)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="radio" name="brand" value="{{ $b->slug }}" {{ ($applied['brand']??'')===$b->slug?'checked':'' }}>
                                <span>{{ $b->name }}</span>
                            </label>
                        @endforeach
                        <label class="flex items-center gap-2 text-sm col-span-2"><input type="radio" name="brand" value="" {{ empty($applied['brand'])?'checked':'' }}> <span>Tất cả</span></label>
                    </div>
                </div>

                <div>
                    <div class="text-sm font-semibold text-gray-800 mb-2">Khoảng giá (VNĐ)</div>
                    <div class="flex items-center gap-2">
                        <input type="number" name="price_min" value="{{ $applied['price_min'] ?? '' }}" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Từ">
                        <span class="text-gray-400">—</span>
                        <input type="number" name="price_max" value="{{ $applied['price_max'] ?? '' }}" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Đến">
                    </div>
                </div>

                <div>
                    <div class="text-sm font-semibold text-gray-800 mb-2">Tình trạng</div>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="in_stock" value="1" {{ ($applied['in_stock']??false) ? 'checked' : '' }}>
                        <span>Còn hàng</span>
                    </label>
                </div>

                <div class="flex items-center gap-2">
                    <button class="btn-primary flex-1" type="submit">Áp dụng</button>
                    <a href="{{ url()->current() }}" class="px-4 py-2 border rounded-lg text-sm text-gray-700">Đặt lại</a>
                </div>
            </form>
        </aside>

        {{-- Product grid --}}
        <div class="md:col-span-9">
            @if($products->count() === 0)
                <div class="text-gray-500">Không tìm thấy sản phẩm phù hợp.</div>
            @else
                @if(($applied['view'] ?? 'grid') === 'list')
                    <div class="space-y-4">
                        @foreach($products as $p)
                            <div class="relative flex gap-4 p-3 border rounded-lg hover:bg-gray-50">
                                <a href="{{ route('client.product.detail', $p->slug) }}" class="flex gap-4 flex-1">
                                    <img src="{{ $p->image ? asset('storage/'.$p->image) : 'https://via.placeholder.com/160x160?text=No+Image' }}" class="w-24 h-24 md:w-36 md:h-36 rounded object-cover" alt="{{ $p->name }}">
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-900 mb-1">{{ $p->name }}</div>
                                        <div class="text-sm text-gray-500 line-clamp-2">{{ $p->category->name ?? '' }} @if($p->brand) • {{ $p->brand->name }} @endif</div>
                                    </div>
                                    <div class="text-red-600 font-bold">{{ number_format($p->price, 0, ',', '.') }} đ</div>
                                </a>
                                <form method="post" action="{{ route('client.wishlist.toggle') }}" class="absolute top-2 right-2">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $p->id }}">
                                    <button type="submit" title="Yêu thích" class="p-2 rounded-full bg-white border hover:text-red-500"><i class="fa-regular fa-heart"></i></button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                        @foreach($products as $p)
                            <div class="product-card group relative">
                                <a href="{{ route('client.product.detail', $p->slug) }}">
                                    <div class="product-img-container overflow-hidden rounded-lg mb-3">
                                        <img src="{{ $p->image ? asset('storage/'.$p->image) : 'https://via.placeholder.com/400x400?text=No+Image' }}" alt="{{ $p->name }}" class="w-full aspect-square object-cover transition-transform duration-500 group-hover:scale-110">
                                    </div>
                                    <h3 class="product-name text-gray-900 text-sm font-semibold mb-1 line-clamp-2 group-hover:text-blue-600 transition-colors">{{ $p->name }}</h3>
                                    <div class="product-price text-red-600 font-bold">{{ number_format($p->price, 0, ',', '.') }} đ</div>
                                </a>
                                <form method="post" action="{{ route('client.wishlist.toggle') }}" class="absolute top-2 right-2">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $p->id }}">
                                    <button type="submit" title="Yêu thích" class="p-2 rounded-full bg-white border shadow hover:text-red-500"><i class="fa-regular fa-heart"></i></button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
                <div class="mt-6">{{ $products->links() }}</div>
            @endif
        </div>
    </div>

    {{-- Mobile sticky filter trigger --}}
    <div class="md:hidden fixed bottom-4 right-4 z-40">
        <button id="openFilter" class="px-4 py-2 rounded-full shadow-lg bg-white border">Bộ lọc</button>
    </div>
    <div id="filterDrawer" class="fixed inset-0 bg-black/40 hidden">
        <div class="absolute right-0 top-0 bottom-0 w-80 bg-white p-4 overflow-y-auto">
            <div class="flex items-center justify-between mb-3"><div class="font-semibold">Bộ lọc</div><button id="closeFilter">Đóng</button></div>
            <form id="filterFormMobile" method="get" class="space-y-6">
                <input type="hidden" name="sort" value="{{ $applied['sort'] ?? 'new' }}" />
                <input type="hidden" name="view" value="{{ $applied['view'] ?? 'grid' }}" />
                <div>
                    <div class="text-sm font-semibold text-gray-800 mb-2">Từ khoá</div>
                    <input name="q" value="{{ $applied['q'] ?? '' }}" placeholder="Tìm theo tên" class="w-full border rounded-lg px-3 py-2 text-sm" />
                </div>
                <div>
                    <div class="text-sm font-semibold text-gray-800 mb-2">Danh mục</div>
                    <div class="space-y-2 max-h-64 overflow-auto pr-1">
                        @foreach($categories as $c)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="radio" name="category" value="{{ $c->slug }}" {{ ($applied['category']??'')===$c->slug?'checked':'' }}>
                                <span>{{ $c->name }}</span>
                            </label>
                            @foreach($c->children as $ch)
                                <label class="flex items-center gap-2 text-sm ml-6">
                                    <input type="radio" name="category" value="{{ $ch->slug }}" {{ ($applied['category']??'')===$ch->slug?'checked':'' }}>
                                    <span>{{ $ch->name }}</span>
                                </label>
                            @endforeach
                        @endforeach
                        <label class="flex items-center gap-2 text-sm"><input type="radio" name="category" value="" {{ empty($applied['category'])?'checked':'' }}> <span>Tất cả</span></label>
                    </div>
                </div>
                <div>
                    <div class="text-sm font-semibold text-gray-800 mb-2">Thương hiệu</div>
                    <div class="grid grid-cols-2 gap-2 max-h-44 overflow-auto pr-1">
                        @foreach($brands as $b)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="radio" name="brand" value="{{ $b->slug }}" {{ ($applied['brand']??'')===$b->slug?'checked':'' }}>
                                <span>{{ $b->name }}</span>
                            </label>
                        @endforeach
                        <label class="flex items-center gap-2 text-sm col-span-2"><input type="radio" name="brand" value="" {{ empty($applied['brand'])?'checked':'' }}> <span>Tất cả</span></label>
                    </div>
                </div>
                <div>
                    <div class="text-sm font-semibold text-gray-800 mb-2">Khoảng giá (VNĐ)</div>
                    <div class="flex items-center gap-2">
                        <input type="number" name="price_min" value="{{ $applied['price_min'] ?? '' }}" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Từ">
                        <span class="text-gray-400">—</span>
                        <input type="number" name="price_max" value="{{ $applied['price_max'] ?? '' }}" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Đến">
                    </div>
                </div>
                <div>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="in_stock" value="1" {{ ($applied['in_stock']??false) ? 'checked' : '' }}>
                        <span>Còn hàng</span>
                    </label>
                </div>
                <div class="flex items-center gap-2">
                    <button class="btn-primary flex-1" type="submit">Áp dụng</button>
                    <a href="{{ url()->current() }}" class="px-4 py-2 border rounded-lg text-sm text-gray-700">Đặt lại</a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@push('scripts')
@vite('resources/js/plp.js')
@endpush


