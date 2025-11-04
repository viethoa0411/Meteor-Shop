<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Post;

class HomeController extends Controller
{
    /**
     * Hiển thị trang chủ với sản phẩm mới và nổi bật
     */
    public function index()
    {
        try {
            // Banners từ DB, theo lịch hiển thị
            $banners = Banner::query()
                ->active()
                ->available()
                ->orderBy('position')
                ->orderByDesc('id')
                ->get(['id','title','image','link','position','status','start_at','end_at']);

            // Thương hiệu
            $brands = Brand::query()->where('status', 'active')->orderBy('name')->limit(12)->get(['id','name','slug']);
            $featuredBrands = Brand::query()->where('status','active')->where('is_featured', true)->orderBy('name')->limit(8)->get(['id','name','slug']);

            // Danh mục cha (hiển thị trong collections)
            $topCategories = Category::query()
                ->whereNull('parent_id')
                ->where('status','active')
                ->orderBy('name')
                ->limit(6)
                ->get(['id','name','slug','description','parent_id','status']);

            // Nhóm sản phẩm
            $newProducts = Product::where('status','active')->latest()->take(8)->get(['id','name','slug','price','image']);
            $bestProducts = Product::where('status','active')->orderByDesc('sold_count')->take(8)->get(['id','name','slug','price','image']);
            $saleProducts = Product::where('status','active')->take(8)->get(['id','name','slug','price','image']);

            $posts = Post::where('status','published')->orderByRaw('COALESCE(published_at, created_at) DESC')->take(3)->get(['id','title','slug','image','excerpt','published_at']);

            return view('client.home', compact('banners','brands','featuredBrands','topCategories','newProducts','bestProducts','saleProducts','posts'));
        } catch (\Exception $e) {
            return view('client.home', [
                'banners' => collect([]),
                'brands' => collect([]),
                'featuredBrands' => collect([]),
                'topCategories' => collect([]),
                'newProducts' => collect([]),
                'bestProducts' => collect([]),
                'saleProducts' => collect([]),
                'posts' => collect([]),
            ]);
        }
    }

    public function search()
    {
        return view('client.search');
    }
}
