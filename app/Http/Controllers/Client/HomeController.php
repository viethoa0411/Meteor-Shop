<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Blog;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // lấy 4 sp mới nhât(theo ngày tạo)
        $newProducts = Product::query()
            ->select(['id', 'name',  'slug', 'price', 'image', 'status', 'created_at'])
            ->where('status', 1)
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        $outstandingProducts = Product::query()
            ->select(['id', 'name', 'slug', 'stock', 'price', 'image', 'status', 'created_at'])
            ->where('status', 1)
            ->orderByDesc('stock')
            ->limit(4)
            ->get();

        // Dùng cho menu/header
        $cate = Category::query()
            ->select(['id', 'name', 'slug', 'description', 'parent_id', 'status'])
            ->where('status', 1)
            ->get();

        // Dùng riêng cho block "danh mục theo đồ" trên trang home
        $homeCategories = $cate
            ->where('parent_id', null)   // nếu muốn chỉ lấy danh mục cha
            ->take(6);                   // lấy 6 danh mục

        $banners = Banner::active()
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        $latestBlogs = Blog::where('status', 'published')
            ->orderByDesc('created_at')
            ->take(2)
            ->get();

        return view('client.home', compact(
            'newProducts',
            'outstandingProducts',
            'cate',
            'banners',
            'latestBlogs',
            'homeCategories' // ⭐ THÊM DÒNG NÀY
        ));
    }



    public function getVersion($id)
    {
        $product = Product::select('id', 'version')->find($id);
        return response()->json([
            'version' => $product->version,
        ]);
    }
}
