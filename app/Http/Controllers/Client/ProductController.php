<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    // Hiển thị sản phẩm theo danh mục
    public function productsByCategory($slug)
    {
        $cate = Category::all();
        $category = Category::where('slug', $slug)->firstOrFail();
        $childIds = Category::where('parent_id', $category->id)->pluck('id');
            if ($childIds->count() > 0) {
                $products = Product::whereIn('category_id', $childIds)
                    ->orderBy('created_at', 'desc')
                    ->paginate(12);
            } else {
                $products = Product::where('category_id', $category->id)
                    ->orderBy('created_at', 'desc')
                    ->paginate(12);
            }
        return view('client.products.category', compact('category', 'products', 'cate'));
    }
    //
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

    public function index() 
    {
        // Hiển thị 6 danh mục + 4 sản phẩm mới nhất mỗi danh mục
        $categories = Category::take(6)->get();
        foreach ($categories as $category) {
            $childIds = Category::where('parent_id', $category->id)->pluck('id'); 
            $category->latestProducts = Product::whereIn('category_id', $childIds)
                ->orderBy('created_at', 'desc')
                ->take(4)
                ->get();
        }
        return view('client.products.index', compact('categories'));
    }

}