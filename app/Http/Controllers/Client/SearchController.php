<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function suggest(Request $request)
    {
        $q = trim((string) $request->get('q'));
        if ($q === '') {
            return response()->json(['products' => [], 'categories' => [], 'brands' => []]);
        }

        $products = Product::query()
            ->select(['id','name','slug','image','price'])
            ->where('name', 'like', "%{$q}%")
            ->orderBy('sold_count', 'desc')
            ->limit(6)
            ->get();

        $categories = Category::query()
            ->select(['id','name','slug'])
            ->where('name', 'like', "%{$q}%")
            ->limit(5)->get();

        $brands = Brand::query()
            ->select(['id','name','slug'])
            ->where('name', 'like', "%{$q}%")
            ->limit(5)->get();

        return response()->json(compact('products','categories','brands'));
    }
}


