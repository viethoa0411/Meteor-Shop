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
    // Lưu bài viết mới vào database
    public function store(Request $request)
    {
         // Tìm user theo tên hoặc email
        $author = trim($request->author);
        $user = User::where('name', $author)
            ->orWhere('email', $author)
            ->first();

        if (!$user) {
            return back()->withInput()->withErrors(['author' => 'Không tìm thấy tác giả với tên hoặc email: ' . $author]);
        }

        // Map status: active -> published, inactive -> draft
        $dbStatus = $request->status === 'active' ? 'published' : 'draft';

        $imageName = null;

        // Xử lý upload ảnh thumbnail
        if ($request->hasFile('thumbnail')) {
            try {
                $image = $request->file('thumbnail');
                $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $destination = public_path('blog/images');

                // Tạo thư mục nếu chưa tồn tại
                if (!File::exists($destination)) {
                    File::makeDirectory($destination, 0777, true);
                }

                $image->move($destination, $imageName);
            } catch (\Exception $e) {
                return back()->withInput()->with('error', 'Lỗi upload ảnh: ' . $e->getMessage());
            }
        }

        // Tạo slug unique
        $slug = Str::slug($request->title);
        if (empty($slug)) {
            $slug = 'blog-' . time();
        }

        $originalSlug = $slug;
        $count = 1;

        while (Blog::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        // Lưu bài viết vào database
        try {
            $blog = Blog::create([
                'user_id'   => $user->id,
                'title'     => $request->title,
                'slug'      => $slug,
                'excerpt'   => $request->excerpt ?? Str::limit(strip_tags($request->content), 150),
                'content'   => $request->content,
                'status'    => $dbStatus,
                'thumbnail' => $imageName,
            ]);

            return redirect()->route('admin.blogs.index')->with('success', 'Thêm bài viết thành công!');
        } catch (\Exception $e) {
            // Xóa ảnh nếu lưu thất bại
            if ($imageName && file_exists(public_path('blog/images/' . $imageName))) {
                unlink(public_path('blog/images/' . $imageName));
            }
            return back()->withInput()->with('error', 'Lỗi lưu database: ' . $e->getMessage());
        }
    }

    
}
