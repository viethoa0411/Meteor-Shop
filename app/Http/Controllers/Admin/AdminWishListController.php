<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminWishListController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm với số lượt yêu thích
     */
    public function index()
    {
        // Lấy tất cả sản phẩm với số lượt yêu thích, sắp xếp theo số lượt yêu thích giảm dần
        $products = Product::withCount('wishlists as favorite_count')
            ->orderByDesc('favorite_count')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.wishlist.index', compact('products'));
    }

    /**
     * Xem chi tiết sản phẩm yêu thích - danh sách khách hàng đã yêu thích
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        
        // Lấy danh sách khách hàng đã yêu thích sản phẩm này
        $wishlists = Wishlist::with('user')
            ->where('product_id', $id)
            ->orderByDesc('created_at')
            ->paginate(20);

        $favoriteCount = $wishlists->total();

        return view('admin.wishlist.show', compact('product', 'wishlists', 'favoriteCount'));
    }
}