<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;


class ClientProductController extends Controller
{
    // hiển thị sản phẩm theo 1 danh mục cụ thể
    public function productsByCategory($slug)
    {
        $cate = Category::all();
        $category = Category::where('slug', $slug)->firstOrFail();

        $products = Product::where('category_id', $category->id)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('client.products.category', compact('category', 'products', 'cate'));
    }
}