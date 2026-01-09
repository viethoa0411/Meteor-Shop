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
            ->where('status', 'active') // chỉ lấy sản phẩm đang active
            ->where(function ($query) {
                $query->where('stock', '>', 0)
                    ->orWhereHas('variants', function ($q) {
                        $q->where('stock', '>', 0);
                    });
            })
            ->orderByDesc('created_at') //lấy 4 cái ngày tạo mới nhất giảm dần
            ->limit(4) // lấy 4cp
            ->get();

        $outstandingProducts = Product::query()
            ->select(['id', 'name', 'slug', 'stock', 'price', 'image', 'status', 'created_at'])
            ->where('status', 'active')
            ->where(function ($query) {
                $query->where('stock', '>', 0)
                    ->orWhereHas('variants', function ($q) {
                        $q->where('stock', '>', 0);
                    });
            })
            ->orderByDesc('stock')
            ->limit(4)
            ->get();

        // Dùng cho menu/header
        $cate = Category::query()
            ->select(['id', 'name', 'slug', 'description', 'parent_id', 'status', 'image'])
            ->where('status', 'active')
            ->get();

        // Dùng riêng cho block "danh mục theo đồ" trên trang home
        $homeCategories = $cate
            ->where('parent_id', '!=', null)
            ->take(3);

        $homeParentCategories = $cate
            ->where('parent_id', null)
            ->take(4);

        $banners = Banner::active()
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        $latestBlogs = Blog::where('status', 'published')
            ->orderByDesc('created_at')
            ->take(2)
            ->get();


        $wishlistIds = [];
        if (auth()->check()) {
            $wishlistIds = Wishlist::where('user_id', auth()->id())
                ->pluck('product_id')
                ->all();
        }

        return view('client.home', compact(
            'newProducts',
            'outstandingProducts',
            'cate',
            'banners',
            'latestBlogs',
            'homeCategories',
            'homeParentCategories',
            'wishlistIds'
        ));
    }



    public function getVersion($id)
    {
        $product = Product::select('id', 'version')->find($id);
        return view('client.home', compact('newProducts', 'outstandingProducts', 'cate', 'banners','latestBlogs'));
    }

}
