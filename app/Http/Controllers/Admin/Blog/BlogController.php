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
    public function list(Request $request)
    {
        // Bước 1: Tạo query với quan hệ user
        $query = Blog::with('user');

        // Bước 2: Lọc theo trạng thái (mặc định là 'all')
        $status = $request->get('status', 'all');
        
         if ($status !== 'all') {
            // Nếu không phải 'all' thì lọc theo status cụ thể
            // 'active' = published, 'inactive' = draft hoặc archived
            if ($status === 'active') {
                $query->where('status', 'published');
            } elseif ($status === 'inactive') {
                $query->whereIn('status', ['draft', 'archived']);
            } else {
                $query->where('status', $status);
            }
        }

        // Bước 3: Xử lý tìm kiếm (nếu có)
        if ($request->has('keyword') && $request->keyword != '') {
            $keyword = $request->keyword;
            // Tìm kiếm theo tiêu đề HOẶC excerpt HOẶC slug
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('excerpt', 'like', "%{$keyword}%")
                    ->orWhere('slug', 'like', "%{$keyword}%");
            });
        }
        
        // Bước 4: Sắp xếp theo ID giảm dần và phân trang
        $blogs = $query->orderBy('id', 'desc')->paginate(7);

        // Bước 5: Giữ lại từ khóa tìm kiếm và status khi chuyển trang
        $blogs->appends($request->only(['keyword', 'status']));

        // Bước 6: Trả về view với dữ liệu danh sách blog
        return view('admin.blogs.list', compact('blogs'));
    }
    // Hiển thị form tạo bài viết mới
    public function create()
    {
        return view('admin.blogs.create');
    }
    // Lưu bài viết mới vào database
    public function store(Request $request)
    {

      // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'title'     => 'required|string|min:3|max:255',
            'content'   => 'required|string|min:10',
            'excerpt'   => 'nullable|string|max:500',
            'status'    => 'required|in:active,inactive',
            'author'    => 'required|string|min:2',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048|dimensions:min_width=100,min_height=100',
        ], [
            'title.required' => 'Tiêu đề là bắt buộc.',
            'title.min' => 'Tiêu đề phải có ít nhất 3 ký tự.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'title.string' => 'Tiêu đề phải là chuỗi ký tự.',
            'content.required' => 'Nội dung là bắt buộc.',
            'content.min' => 'Nội dung phải có ít nhất 10 ký tự.',
            'content.string' => 'Nội dung phải là chuỗi ký tự.',
            'excerpt.string' => 'Mô tả ngắn phải là chuỗi ký tự.',
            'excerpt.max' => 'Mô tả ngắn không được vượt quá 500 ký tự.',
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.in' => 'Trạng thái không hợp lệ. Chỉ chấp nhận: hoạt động hoặc dừng hoạt động.',
            'author.required' => 'Tác giả là bắt buộc.',
            'author.min' => 'Tác giả phải có ít nhất 2 ký tự.',
            'author.string' => 'Tác giả phải là chuỗi ký tự.',
            'thumbnail.image' => 'File phải là hình ảnh.',
            'thumbnail.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
            'thumbnail.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
            'thumbnail.dimensions' => 'Hình ảnh phải có kích thước tối thiểu 100x100 pixel.',
        ]);

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
                $destination = public_path('blogs/images');

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
                'excerpt'   => $request->excerpt ?? Str::limit(strip_tags($request->getContent), 150),
                'content'   => $request->getContent,
                'status'    => $dbStatus,
                'thumbnail' => $imageName,
            ]);

            return redirect()->route('admin.blogs.list')->with('success', 'Thêm bài viết thành công!');
        } catch (\Exception $e) {
            // Xóa ảnh nếu lưu thất bại
            if ($imageName && file_exists(public_path('blogs/images/' . $imageName))) {
                unlink(public_path('blogs/images/' . $imageName));
            }
            return back()->withInput()->with('error', 'Lỗi lưu database: ' . $e->getMessage());
        }
    }

    // Hiển thị form chỉnh sửa bài viết
    public function edit($id)
    {
        $blog = Blog::with('user')->findOrFail($id);
        // Map status từ database sang form: published -> active, draft/archived -> inactive
        $blog->form_status = $blog->status === 'published' ? 'active' : 'inactive';
        return view('admin.blogs.edit', compact('blog'));
    }
    // Cập nhật bài viết trong database
    // Cập nhật bài viết trong database
    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'title'     => 'required|string|min:3|max:255',
            'content'   => 'required|string|min:10',
            'excerpt'   => 'nullable|string|max:500',
            'status'    => 'required|in:active,inactive',
            'author'    => 'required|string|min:2',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048|dimensions:min_width=100,min_height=100',
        ], [
            'title.required' => 'Vui lòng nhập dữ liệu.',
            'title.min' => 'Tiêu đề phải có ít nhất 3 ký tự.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'title.string' => 'Tiêu đề phải là chuỗi ký tự.',
            'content.required' => 'Vui lòng nhập dữ liệu.',
            'content.min' => 'Nội dung phải có ít nhất 10 ký tự.',
            'content.string' => 'Nội dung phải là chuỗi ký tự.',
            'excerpt.string' => 'Mô tả ngắn phải là chuỗi ký tự.',
            'excerpt.max' => 'Mô tả ngắn không được vượt quá 500 ký tự.',
            'status.required' => 'Vui lòng nhập dữ liệu.',
            'status.in' => 'Trạng thái không hợp lệ. Chỉ chấp nhận: hoạt động hoặc dừng hoạt động.',
            'author.required' => 'Vui lòng nhập dữ liệu.',
            'author.min' => 'Tác giả phải có ít nhất 2 ký tự.',
            'author.string' => 'Tác giả phải là chuỗi ký tự.',
            'thumbnail.image' => 'File phải là hình ảnh.',
            'thumbnail.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
            'thumbnail.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
            'thumbnail.dimensions' => 'Hình ảnh phải có kích thước tối thiểu 100x100 pixel.',
        ]);

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

        $imageName = $blog->thumbnail;

        // Xử lý upload ảnh mới
        if ($request->hasFile('thumbnail')) {
            try {
                // Xóa ảnh cũ nếu có
                $oldPath = public_path('blogs/images/' . $blog->thumbnail);
                if ($blog->thumbnail && file_exists($oldPath)) {
                    unlink($oldPath);
                }

                // Upload ảnh mới
                $image = $request->file('thumbnail');
                $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $destination = public_path('blogs/images');

                // Tạo thư mục nếu chưa tồn tại
                if (!File::exists($destination)) {
                    File::makeDirectory($destination, 0777, true);
                }

                $image->move($destination, $imageName);
            } catch (\Exception $e) {
                return back()->withInput()->with('error', 'Lỗi upload ảnh: ' . $e->getMessage());
            }
        }

        // Tạo slug unique nếu title thay đổi
        $slug = $blog->slug;
        if ($request->title !== $blog->title) {
            $slug = Str::slug($request->title);

            if (empty($slug)) {
                $slug = 'blog-' . time();
            }

            $originalSlug = $slug;
            $count = 1;

            while (Blog::where('slug', $slug)->where('id', '!=', $blog->id)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }
        }

        // Map status: active -> published, inactive -> draft
        $dbStatus = $request->status === 'active' ? 'published' : 'draft';

        // Cập nhật bài viết
        try {
            $blog->update([
                'user_id'   => $user->id,
                'title'     => $request->title,
                'slug'      => $slug,
                'excerpt'   => $request->excerpt ?? Str::limit(strip_tags($request->getContent), 150),
                'content'   => $request->getContent,
                'status'    => $dbStatus,
                'thumbnail' => $imageName,
            ]);

            return redirect()->route('admin.blogs.list')->with('success', 'Cập nhật bài viết thành công!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Lỗi cập nhật: ' . $e->getMessage());
        }
    }
    // Hiển thị chi tiết bài viết
    public function show($id)
    {
        $blog = Blog::with('user')->findOrFail($id);
        return view('admin.blogs.show', compact('blog'));
    }
    // Xóa bài viết
    public function destroy($id)
    {
        try {
            $blog = Blog::findOrFail($id);

            // Xóa ảnh thumbnail nếu có
            if ($blog->thumbnail) {
                $path = public_path('blogs/images/' . $blog->thumbnail);
                if (file_exists($path)) {
                    unlink($path);
                }
            }

            $blog->delete();

            return redirect()->route('admin.blogs.list')->with('success', 'Đã xóa bài viết thành công!');
        } catch (\Exception $e) {
            return redirect()->route('admin.blogs.list')->with('error', 'Lỗi khi xóa bài viết: ' . $e->getMessage());
        }
    }
    

    
}
