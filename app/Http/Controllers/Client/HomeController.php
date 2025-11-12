<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
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

        return view('client.home', compact('newProducts', 'outstandingProducts', 'cate'));
    }
}
