<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use Illuminate\Http\Request;

class ShareController extends Controller
{
    /**
     * Hiển thị danh sách bài viết góc chia sẻ
     */
    public function index(Request $request)
    {
        // Lấy danh sách categories để truyền vào layout
        $cate = Category::query()
            ->select(['name', 'slug', 'description', 'parent_id', 'status'])
            ->where('status', 1)
            ->get();

        // Lấy các bài viết góc chia sẻ
        // Filter theo title hoặc excerpt chứa từ khóa "góc chia sẻ" hoặc "chia sẻ"
        $shares = Blog::with('user')
            ->where('status', 'published')
            ->where(function ($query) {
                $query->where('title', 'like', '%góc chia sẻ%')
                    ->orWhere('title', 'like', '%chia sẻ%')
                    ->orWhere('title', 'like', '%sharing%')
                    ->orWhere('excerpt', 'like', '%góc chia sẻ%')
                    ->orWhere('excerpt', 'like', '%chia sẻ%')
                    ->orWhere('excerpt', 'like', '%sharing%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('client.shares.index', compact('shares', 'cate'));
    }

    /**
     * Hiển thị chi tiết bài viết góc chia sẻ
     */
    public function show($slug)
    {
        // Lấy danh sách categories để truyền vào layout
        $cate = Category::query()
            ->select(['name', 'slug', 'description', 'parent_id', 'status'])
            ->where('status', 1)
            ->get();

        // Lấy bài viết theo slug
        $share = Blog::with('user')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // Lấy các bài viết liên quan
        $relatedShares = Blog::with('user')
            ->where('status', 'published')
            ->where('id', '!=', $share->id)
            ->where(function ($query) {
                $query->where('title', 'like', '%góc chia sẻ%')
                    ->orWhere('title', 'like', '%chia sẻ%')
                    ->orWhere('title', 'like', '%sharing%')
                    ->orWhere('excerpt', 'like', '%góc chia sẻ%')
                    ->orWhere('excerpt', 'like', '%chia sẻ%')
                    ->orWhere('excerpt', 'like', '%sharing%');
            })
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        return view('client.shares.show', compact('share', 'relatedShares', 'cate'));
    }
}
