@extends('client.layout')

@section('title', 'So sánh sản phẩm')

@section('content')
<section class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">So sánh sản phẩm</h1>
        <form method="post" action="{{ route('client.compare.clear') }}">@csrf<button class="text-sm underline">Xoá tất cả</button></form>
    </div>
    @if($products->isEmpty())
        <div class="bg-white border rounded-xl p-6 text-sm text-gray-600">Chưa chọn sản phẩm để so sánh.</div>
    @else
        <div class="overflow-auto">
            <table class="min-w-full text-sm border">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="p-3 text-left">Thuộc tính</th>
                        @foreach($products as $p)
                            <th class="p-3 text-left align-top">
                                <div class="font-semibold">{{ $p->name }}</div>
                                <div class="text-red-600 font-bold">{{ number_format($p->price,0,',','.') }} đ</div>
                                <form method="post" action="{{ route('client.compare.remove') }}" class="mt-1 inline">@csrf<input type="hidden" name="product_id" value="{{ $p->id }}"><button class="text-xs underline">Bỏ</button></form>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t">
                        <td class="p-3 font-medium bg-gray-50">Hình ảnh</td>
                        @foreach($products as $p)
                            <td class="p-3"><img class="w-24 h-24 object-cover rounded" src="{{ $p->image ? asset('storage/'.$p->image) : 'https://via.placeholder.com/96x96?text=No+Image' }}" alt="{{ $p->name }}"></td>
                        @endforeach
                    </tr>
                    <tr class="border-t">
                        <td class="p-3 font-medium bg-gray-50">Danh mục</td>
                        @foreach($products as $p)
                            <td class="p-3">{{ $p->category->name ?? '—' }}</td>
                        @endforeach
                    </tr>
                    <tr class="border-t">
                        <td class="p-3 font-medium bg-gray-50">Thương hiệu</td>
                        @foreach($products as $p)
                            <td class="p-3">{{ $p->brand->name ?? '—' }}</td>
                        @endforeach
                    </tr>
                    <tr class="border-t">
                        <td class="p-3 font-medium bg-gray-50">Tình trạng</td>
                        @foreach($products as $p)
                            <td class="p-3">{{ $p->status ?? '—' }}</td>
                        @endforeach
                    </tr>
                    <tr class="border-t">
                        <td class="p-3 font-medium bg-gray-50">Mô tả ngắn</td>
                        @foreach($products as $p)
                            <td class="p-3">{{ \Illuminate\Support\Str::limit($p->description ?? '', 100) }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    @endif
</section>
@endsection


