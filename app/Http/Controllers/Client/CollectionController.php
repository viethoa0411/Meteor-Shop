<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    /**
     * Hiển thị danh sách bộ sưu tập
     */
    public function index(Request $request)
    {
        // Lấy danh sách categories để truyền vào layout
        $cate = Category::query()
            ->select(['name', 'slug', 'description', 'parent_id', 'status'])
            ->where('status', 1)
            ->get();

        // Lấy các bộ sưu tập active
        $collections = Collection::active()
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('client.collections.index', compact('collections', 'cate'));
    }

    /**
     * Hiển thị chi tiết bộ sưu tập theo slug
     */
    public function show($slug)
    {
        // Lấy danh sách categories để truyền vào layout
        $cate = Category::query()
            ->select(['name', 'slug', 'description', 'parent_id', 'status'])
            ->where('status', 1)
            ->get();

        // Lấy bộ sưu tập theo slug
        $collection = Collection::active()
            ->where('slug', $slug)
            ->with(['products' => function ($query) {
                $query->where('status', 1)
                    ->orderBy('created_at', 'desc');
            }])
            ->firstOrFail();

        // Lấy các bộ sưu tập liên quan
        $relatedCollections = Collection::active()
            ->where('id', '!=', $collection->id)
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        return view('client.collections.show', compact('collection', 'relatedCollections', 'cate'));
    }
}
