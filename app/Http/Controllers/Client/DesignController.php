<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use Illuminate\Http\Request;

class DesignController extends Controller
{
    /**
     * Hiển thị danh sách bài viết về thiết kế nội thất
     */
    public function index(Request $request)
    {
        // Lấy danh sách categories để truyền vào layout
        $cate = Category::query()
            ->select(['name', 'slug', 'description', 'parent_id', 'status'])
            ->where('status', 1)
            ->get();

        // Lấy các bài viết về thiết kế nội thất
        // Filter theo title hoặc excerpt chứa từ khóa "thiết kế nội thất"
        $designs = Blog::with('user')
            ->where('status', 'published')
            ->where(function ($query) {
                $query->where('title', 'like', '%thiết kế nội thất%')
                    ->orWhere('title', 'like', '%interior design%')
                    ->orWhere('excerpt', 'like', '%thiết kế nội thất%')
                    ->orWhere('excerpt', 'like', '%interior design%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('client.designs.index', compact('designs', 'cate'));
    }

    /**
     * Hiển thị chi tiết bài viết thiết kế nội thất
     */
    public function show($slug)
    {
        // Lấy danh sách categories để truyền vào layout
        $cate = Category::query()
            ->select(['name', 'slug', 'description', 'parent_id', 'status'])
            ->where('status', 1)
            ->get();

        // Lấy bài viết theo slug
        $design = Blog::with('user')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // Lấy các bài viết liên quan
        $relatedDesigns = Blog::with('user')
            ->where('status', 'published')
            ->where('id', '!=', $design->id)
            ->where(function ($query) {
                $query->where('title', 'like', '%thiết kế nội thất%')
                    ->orWhere('title', 'like', '%interior design%')
                    ->orWhere('excerpt', 'like', '%thiết kế nội thất%')
                    ->orWhere('excerpt', 'like', '%interior design%');
            })
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        return view('client.designs.show', compact('design', 'relatedDesigns', 'cate'));
    }
}
