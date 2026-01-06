<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\User;
use App\Models\PostCategory;
use App\Models\PostTag;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{
    /**
     * Hiển thị danh sách bài viết với tìm kiếm và lọc nâng cao
     */
    public function list(Request $request)
    {
        $query = Blog::with(['user']);

        // Lọc theo trạng thái
        $status = $request->get('status', 'all');
        if ($status !== 'all') {
            if ($status === 'published') {
                $query->where('status', 'published');
            } elseif ($status === 'draft') {
                $query->where('status', 'draft');
            } elseif ($status === 'scheduled') {
                $query->where('status', 'published')
                    ->where('published_at', '>', now());
            } elseif ($status === 'archived') {
                $query->where('status', 'archived');
            }
        }

        // Tìm kiếm
        if ($request->has('keyword') && $request->keyword != '') {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('excerpt', 'like', "%{$keyword}%")
                    ->orWhere('slug', 'like', "%{$keyword}%");
            });
        }

        // Lọc theo author
        if ($request->has('author') && $request->author != '') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('users.id', $request->author);
            });
        }

        // Lọc theo date range
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['id', 'title', 'created_at', 'published_at', 'view_count', 'updated_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('id', 'desc');
        }

        $blogs = $query->paginate(15);
        $blogs->appends($request->only(['keyword', 'status', 'category', 'author', 'date_from', 'date_to', 'sort_by', 'sort_order']));

        // Lấy dữ liệu cho filters
        $authors = User::whereHas('blogs')->orderBy('name')->get();

        return view('admin.blogs.list', compact('blogs', 'authors'));
    }

    /**
     * Hiển thị form tạo bài viết mới
     */
    public function create()
    {
        $users = User::orderBy('name')->get();
        
        return view('admin.blogs.create', compact('users'));
    }

    /**
     * Lưu bài viết mới vào database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'           => 'required|string|min:3|max:255',
            'slug'            => 'nullable|string|max:255|unique:blogs,slug',
            'content'         => 'required|string|min:10',
            'excerpt'         => 'nullable|string|max:500',
            'status'          => 'required|in:draft,published,scheduled',
            'author_id'       => 'required|exists:users,id',
            'thumbnail'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'published_at'    => 'nullable|date',
            'seo_title'       => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:160',
            'noindex'         => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Xử lý slug
            $slug = $request->slug ?: Str::slug($request->title);
            if (empty($slug)) {
                $slug = 'blog-' . time();
            }

            $originalSlug = $slug;
            $count = 1;
            while (Blog::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }

            // Xử lý published_at
            $publishedAt = null;
            if ($request->status === 'published') {
                $publishedAt = $request->published_at ?: now();
            } elseif ($request->status === 'scheduled') {
                $publishedAt = $request->published_at;
            }

            // Upload thumbnail
            $imageName = null;
            if ($request->hasFile('thumbnail')) {
                $image = $request->file('thumbnail');
                $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $destination = public_path('blogs/images');

                if (!File::exists($destination)) {
                    File::makeDirectory($destination, 0777, true);
                }

                $image->move($destination, $imageName);
            }

            // Tạo bài viết
            $blog = Blog::create([
                'user_id'         => $request->author_id,
                'title'           => $request->title,
                'slug'            => $slug,
                'excerpt'         => $request->excerpt ?? Str::limit(strip_tags($request->content), 150),
                'content'         => $request->content,
                'status'          => $request->status === 'scheduled' ? 'published' : $request->status,
                'published_at'    => $publishedAt,
                'thumbnail'       => $imageName,
                'seo_title'       => $request->seo_title,
                'seo_description' => $request->seo_description,
                'canonical_url'   => $request->canonical_url,
                'noindex'         => $request->has('noindex') ? true : false,
            ]);

            DB::commit();

            // Clear cache
            Cache::forget('blog_list');
            
            return redirect()->route('admin.blogs.list')->with('success', 'Thêm bài viết thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($imageName) && file_exists(public_path('blogs/images/' . $imageName))) {
                unlink(public_path('blogs/images/' . $imageName));
            }
            return back()->withInput()->with('error', 'Lỗi lưu database: ' . $e->getMessage());
        }
    }

    /**
     * Autosave draft
     */
    public function autosave(Request $request)
    {
        $validated = $request->validate([
            'title'   => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'excerpt' => 'nullable|string|max:500',
        ]);

        try {
            $blog = null;
            if ($request->has('blog_id')) {
                $blog = Blog::find($request->blog_id);
            }

            if ($blog) {
                // Update existing draft
                $blog->update([
                    'title'   => $request->title ?: $blog->title,
                    'content' => $request->content ?: $blog->content,
                    'excerpt' => $request->excerpt ?: $blog->excerpt,
                    'status'  => 'draft',
                ]);
            } else {
                // Create new draft
                $blog = Blog::create([
                    'user_id' => auth()->id(),
                    'title'   => $request->title ?: 'Untitled',
                    'slug'    => 'draft-' . time(),
                    'content' => $request->content ?: '',
                    'excerpt' => $request->excerpt,
                    'status'  => 'draft',
                ]);
            }

            return response()->json([
                'success' => true,
                'blog_id' => $blog->id,
                'message' => 'Đã lưu tự động',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi autosave: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hiển thị form chỉnh sửa bài viết
     */
    public function edit($id)
    {
        $blog = Blog::with(['user'])->findOrFail($id);
        $users = User::orderBy('name')->get();
        
        return view('admin.blogs.edit', compact('blog', 'users'));
    }

    /**
     * Cập nhật bài viết trong database
     */
    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $validated = $request->validate([
            'title'           => 'required|string|min:3|max:255',
            'slug'            => 'nullable|string|max:255|unique:blogs,slug,' . $id,
            'content'         => 'required|string|min:10',
            'excerpt'         => 'nullable|string|max:500',
            'status'          => 'required|in:draft,published,scheduled',
            'author_id'       => 'required|exists:users,id',
            'thumbnail'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'published_at'    => 'nullable|date',
            'seo_title'       => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:160',
            'canonical_url'   => 'nullable|url|max:255',
            'noindex'         => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $oldSlug = $blog->slug;

            // Xử lý slug
            $slug = $request->slug ?: Str::slug($request->title);
            if (empty($slug)) {
                $slug = 'blog-' . time();
            }

            if ($slug !== $blog->slug) {
                $originalSlug = $slug;
                $count = 1;
                while (Blog::where('slug', $slug)->where('id', '!=', $blog->id)->exists()) {
                    $slug = $originalSlug . '-' . $count;
                    $count++;
                }
            }

            // Xử lý published_at
            $publishedAt = $blog->published_at;
            if ($request->status === 'published') {
                $publishedAt = $request->published_at ?: ($blog->published_at ?: now());
            } elseif ($request->status === 'scheduled') {
                $publishedAt = $request->published_at;
            } elseif ($request->status === 'draft') {
                $publishedAt = null;
            }

            // Upload thumbnail mới
            $imageName = $blog->thumbnail;
            if ($request->hasFile('thumbnail')) {
                // Xóa ảnh cũ
                if ($blog->thumbnail) {
                    $oldPath = public_path('blogs/images/' . $blog->thumbnail);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                // Upload ảnh mới
                $image = $request->file('thumbnail');
                $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $destination = public_path('blogs/images');

                if (!File::exists($destination)) {
                    File::makeDirectory($destination, 0777, true);
                }

                $image->move($destination, $imageName);
            }

            // Cập nhật bài viết
            $blog->update([
                'user_id'         => $request->author_id,
                'title'           => $request->title,
                'slug'            => $slug,
                'excerpt'         => $request->excerpt ?? Str::limit(strip_tags($request->content), 150),
                'content'         => $request->content,
                'status'          => $request->status === 'scheduled' ? 'published' : $request->status,
                'published_at'    => $publishedAt,
                'thumbnail'       => $imageName,
                'seo_title'       => $request->seo_title,
                'seo_description' => $request->seo_description,
                'canonical_url'   => $request->canonical_url,
                'noindex'         => $request->has('noindex') ? true : false,
            ]);

            DB::commit();

            // Clear cache
            Cache::forget('blog_list');
            if ($oldSlug !== $slug) {
                // TODO: Tạo redirect 301 nếu slug thay đổi
                Cache::forget('blog_' . $oldSlug);
            }

            return redirect()->route('admin.blogs.list')->with('success', 'Cập nhật bài viết thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Lỗi cập nhật: ' . $e->getMessage());
        }
    }

    /**
     * Preview bài viết
     */
    public function preview($id)
    {
        $blog = Blog::with(['user'])->findOrFail($id);
        
        // Render frontend view (giả sử có route frontend)
        return view('client.blogs.show', compact('blog'))->with('isPreview', true);
    }

    /**
     * Hiển thị chi tiết bài viết
     */
    public function show($id)
    {
        $blog = Blog::with(['user'])->findOrFail($id);
        return view('admin.blogs.show', compact('blog'));
    }

    /**
     * Xóa bài viết (soft delete)
     */
    public function destroy($id)
    {
        try {
            $blog = Blog::findOrFail($id);

            // Soft delete
            $blog->delete();

            // Clear cache
            Cache::forget('blog_list');
            Cache::forget('blog_' . $blog->slug);

            return redirect()->route('admin.blogs.list')->with('success', 'Đã xóa bài viết thành công!');
        } catch (\Exception $e) {
            return redirect()->route('admin.blogs.list')->with('error', 'Lỗi khi xóa bài viết: ' . $e->getMessage());
        }
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:publish,unpublish,delete',
            'ids'    => 'required|array',
            'ids.*'  => 'exists:blogs,id',
        ]);

        try {
            $blogs = Blog::whereIn('id', $request->ids);

            switch ($request->action) {
                case 'publish':
                    $blogs->update([
                        'status'       => 'published',
                        'published_at' => now(),
                    ]);
                    $message = 'Đã publish ' . count($request->ids) . ' bài viết!';
                    break;

                case 'unpublish':
                    $blogs->update([
                        'status' => 'draft',
                    ]);
                    $message = 'Đã unpublish ' . count($request->ids) . ' bài viết!';
                    break;

                case 'delete':
                    $blogs->delete();
                    $message = 'Đã xóa ' . count($request->ids) . ' bài viết!';
                    break;
            }

            // Clear cache
            Cache::forget('blog_list');

            return redirect()->route('admin.blogs.list')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.blogs.list')->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status
     */
    public function toggleStatus($id)
    {
        try {
            $blog = Blog::findOrFail($id);
            $newStatus = $blog->status === 'published' ? 'draft' : 'published';
            
            $blog->update([
                'status'       => $newStatus,
                'published_at' => $newStatus === 'published' ? ($blog->published_at ?: now()) : null,
            ]);

            Cache::forget('blog_list');

            return response()->json([
                'success' => true,
                'status'  => $newStatus,
                'message' => 'Đã cập nhật trạng thái!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
            ], 500);
        }
    }
}
