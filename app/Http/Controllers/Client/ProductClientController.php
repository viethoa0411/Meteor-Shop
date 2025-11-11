<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductClientController extends Controller
{
    public function productsByCategory($slug)
    {
        $cate = Category::all();
        $category = Category::where('slug', $slug)->firstOrFail();
        $products = Product::where('category_id', $category->id)->paginate(12);
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

     public function search(Request $request)
    {
        $searchQuery = trim($request->input('query'));
        $sort = $request->input('sort', 'popular');
        $categoryId = $request->input('category');

        // Lấy tất cả danh mục đang hoạt động
        $cate = Category::query()
            ->select(['id', 'name', 'slug', 'description', 'parent_id', 'status'])
            ->where('status', 1)
            ->get();

        // Nếu không có từ khóa tìm kiếm, trả về danh sách rỗng
        if (!$searchQuery) {
            $products = collect();
            return view('client.search', compact('products', 'searchQuery', 'cate'));
        }

        // Truy vấn sản phẩm theo từ khóa
        $query = Product::query()
            ->select(['id', 'name', 'slug', 'price', 'image', 'status', 'description', 'created_at', 'category_id'])
            ->where('status', 1)
            ->where(function ($q) use ($searchQuery) {
                $q->where('name', 'LIKE', "%{$searchQuery}%")
                  ->orWhere('description', 'LIKE', "%{$searchQuery}%")
                  ->orWhere('slug', 'LIKE', "%{$searchQuery}%");
            });

        // Lọc theo danh mục nếu có
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Sắp xếp theo lựa chọn
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(8)->withQueryString();

        return view('client.search', compact('products', 'searchQuery', 'cate'));
    }
}
