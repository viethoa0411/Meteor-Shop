<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function productsByCategory($slug)
    {
        $cate = Category::all();
        $category = Categor        $products = Product::where('category_id', $category->id)->paginate(12);
y::where('slug', $slug)->firstOrFail();
        return view('client.products.category', compact('category', 'products', 'cate'));
    }
    public function showDetail($slug)
    {
        $cate = Category::all();
        $product = Product::where('slug', $slug)->firstOrFail();
        $relatedProducts = Product::where('category_id', $product->category_id)
                            ->where('id', '!=', $product->id)
                            ->take(4)
                            ->get();

        return view('client.products.detail', compact('product', 'relatedProducts', 'cate'));
    }
}