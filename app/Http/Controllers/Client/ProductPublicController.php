<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductPublicController extends Controller
{
    /**
     * Hiển thị chi tiết sản phẩm theo slug
     */
    public function show($slug)
    {
        $product = Product::with(['category', 'brand', 'variants'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        // Lấy sản phẩm liên quan (cùng danh mục)
        $relatedProducts = Product::query()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->limit(4)
            ->get();

        // --- Sản phẩm vừa xem (session) ---
        $recent = session()->get('recently_viewed', []);
        // Loại trùng và bỏ hiện tại
        $recent = array_values(array_filter($recent, function ($id) use ($product) { return (int)$id !== (int)$product->id; }));
        // Lấy dữ liệu các sản phẩm vừa xem (tối đa 8)
        $recentIds = array_slice($recent, 0, 8);
        $recentlyViewedProducts = collect();
        if (!empty($recentIds)) {
            $idsStr = implode(',', $recentIds);
            $recentlyViewedProducts = Product::query()
                ->whereIn('id', $recentIds)
                ->where('status', 'active')
                ->orderByRaw("FIELD(id, $idsStr)")
                ->get(['id','name','slug','price','image','status']);
        }
        // Cập nhật session: đưa sản phẩm hiện tại lên đầu danh sách
        array_unshift($recent, (int)$product->id);
        $recent = array_values(array_unique($recent));
        $recent = array_slice($recent, 0, 20); // lưu tối đa 20 id
        session()->put('recently_viewed', $recent);

        return view('client.product.product_detail', compact('product', 'relatedProducts', 'recentlyViewedProducts'));
    }
}
