<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class BlogController extends Controller
{
    // Hiển thị danh sách bài viết với tìm kiếm và lọc
    public function index(Request $request)
    {
        // Bước 1: Tạo query với quan hệ user
        $query = Blog::with('user');

        // Bước 2: Lọc theo trạng thái (mặc định là 'all')
        $status = $request->get('status', 'all');

        

        

        // Bước 4: Sắp xếp theo ID giảm dần 
        $blogs = $query->orderBy('id', 'desc')->paginate();

        // Bước 5: Giữ lại từ khóa tìm kiếm và status khi chuyển trang
        $blogs->appends($request->only(['keyword', 'status']));

        // Bước 6: Trả về view với dữ liệu danh sách blog
        return view('admin.blog.list', compact('blogs'));
    }
    // Hiển thị form tạo bài viết mới
    public function create()
    {
        return view('admin.blog.create');
    }

    
}
