@extends('layouts.client')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">{{ $category->name }}</h2>
    <div class="grid grid-cols-4 gap-4">
        @foreach($products as $product)
            <div class="border p-2 rounded text-center">
                <img src="{{ $product->image_url }}" class="w-100 mb-2" alt="{{ $product->name }}">
                <p>{{ $product->name }}</p>
            </div>
        @endforeach
    </div>
</div>
@endsection
