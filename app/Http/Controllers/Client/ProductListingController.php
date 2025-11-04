<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;

class ProductListingController extends Controller
{
    public function index(Request $request, ?string $categorySlug = null, ?string $brandSlug = null)
    {
        $query = Product::query()->with(['brand:id,name,slug', 'category:id,name,slug']);

        // Keyword
        $keyword = trim((string) $request->get('q'));
        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('slug', 'like', "%{$keyword}%");
            });
        }

        // Category (from query or SEO segment)
        $slug = $categorySlug ?: $request->get('category');
        if ($slug) {
            $category = Category::where('slug', $slug)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Brand (from query or SEO segment)
        $bslug = $brandSlug ?: $request->get('brand');
        if ($bslug) {
            $brand = Brand::where('slug', $bslug)->first();
            if ($brand) {
                $query->where('brand_id', $brand->id);
            }
        }

        // Price range
        $min = $request->integer('price_min');
        $max = $request->integer('price_max');
        if ($min) { $query->where('price', '>=', $min); }
        if ($max) { $query->where('price', '<=', $max); }

        // In stock
        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        // Sort
        switch ($request->get('sort')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'best':
                $query->orderBy('sold_count', 'desc')->orderBy('id', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        // View mode
        $viewMode = $request->get('view', 'grid') === 'list' ? 'list' : 'grid';

        $products = $query->paginate(12)->withQueryString();

        $categories = Category::whereNull('parent_id')->with('children:id,name,slug,parent_id')->get(['id','name','slug','parent_id']);
        $brands = Brand::orderBy('name')->get(['id','name','slug']);

        return view('client.product.listing', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'applied' => [
                'q' => $keyword,
                'category' => $slug,
                'brand' => $bslug,
                'price_min' => $request->get('price_min'),
                'price_max' => $request->get('price_max'),
                'sort' => $request->get('sort') ?? 'new',
                'view' => $viewMode,
                'in_stock' => $request->boolean('in_stock'),
            ],
        ]);
    }
}


