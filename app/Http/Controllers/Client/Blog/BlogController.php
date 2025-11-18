<?php

namespace App\Http\Controllers\Client\Blog;


use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use Illuminate\Http\Request;


class BlogController extends Controller
{
    /**
     * Hiển thị danh sách bài viết có trạng thái hoạt động (published)
     */
    public function list(Request $request)
    {
        // Lấy danh sách categories để truyền vào layout
        $cate = Category::query()
            ->select(['name', 'slug', 'description', 'parent_id', 'status'])
            ->where('status', 1)
            ->get();

        // Lấy các bài viết có trạng thái published (hoạt động)
        $blogs = Blog::with('user')
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->paginate(9); // Hiển thị 9 bài viết mỗi trang

        return view('client.blog.list', compact('blogs', 'cate'));
    }
    /**
     * Hiển thị chi tiết bài viết theo slug
     */
    public function show($slug)
    {
        // Lấy danh sách categories để truyền vào layout
        $cate = Category::query()
            ->select(['name', 'slug', 'description', 'parent_id', 'status'])
            ->where('status', 1)
            ->get();

        // Lấy bài viết theo slug và chỉ lấy bài viết có trạng thái published
        $blog = Blog::with('user')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // Lấy các bài viết liên quan (cùng trạng thái published, loại trừ bài viết hiện tại)
        $relatedBlogs = Blog::with('user')
            ->where('status', 'published')
            ->where('id', '!=', $blog->id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        return view('client.blog.show', compact('blog', 'relatedBlogs', 'cate'));
    }
}