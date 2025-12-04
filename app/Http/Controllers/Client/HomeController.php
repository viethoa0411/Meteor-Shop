<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Blog;
use App\Models\Product;
use App\Models\Category;
use App\Models\HomeCategory;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // lấy 4 sp mới nhât(theo ngày tạo)
        $newProducts = Product::query()
            ->select(['id', 'name',  'slug', 'price', 'image', 'status', 'created_at'])
            ->where('status', 1) // chỉ lấy sản phẩm đang active
            ->orderByDesc('created_at') //lấy 4 cái ngày tạo mới nhất giảm dần
            ->limit(4) // lấy 4cp
            ->get();

        $outstandingProducts = Product::query()
            ->select(['id', 'name', 'slug', 'stock', 'price', 'image', 'status', 'created_at'])
            ->where('status', 1)
            ->orderByDesc('stock')
            ->limit(4)
            ->get();

        $cate = Category::query()
            ->select(['name', 'slug', 'description', 'parent_id', 'status'])
            ->where('status', 1)
            ->get();

        $banners = Banner::active()
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        $latestBlogs = Blog::where('status', 'published')
            ->orderByDesc('created_at')
            ->take(2)
            ->get();

        // Lấy danh mục hiển thị trên trang chủ (3 ảnh: Sofa, Giường, Bàn làm việc)
        $homeCategories = HomeCategory::active()
            ->ordered()
            ->get();

        return view('client.home', compact('newProducts', 'outstandingProducts', 'cate', 'banners', 'latestBlogs', 'homeCategories'));
    }


    public function getVersion($id)
    {
        $product = Product::select('id','version')->find($id);
        return response()->json([
            'version' => $product->version,
        ]);
    }
}
