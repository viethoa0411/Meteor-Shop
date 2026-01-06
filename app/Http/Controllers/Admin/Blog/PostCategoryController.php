<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Http\Controllers\Controller;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostCategoryController extends Controller
{
    /**
     * Hiển thị danh sách categories
     */
    public function index()
    {
        $categories = PostCategory::with('parent')
            ->orderBy('sort_order')
            ->get();
        
        return view('admin.blogs.categories.index', compact('categories'));
    }

    /**
     * Lưu category mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'slug'            => 'nullable|string|max:255|unique:post_categories,slug',
            'description'     => 'nullable|string',
            'parent_id'       => 'nullable|exists:post_categories,id',
            'seo_title'       => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:160',
            'status'          => 'required|in:active,inactive',
        ]);

        try {
            $category = PostCategory::create([
                'name'            => $request->name,
                'slug'            => $request->slug ?: Str::slug($request->name),
                'description'     => $request->description,
                'parent_id'       => $request->parent_id,
                'seo_title'       => $request->seo_title,
                'seo_description' => $request->seo_description,
                'status'          => $request->status,
            ]);

            return redirect()->route('admin.post-categories.index')
                ->with('success', 'Thêm danh mục thành công!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật category
     */
    public function update(Request $request, $id)
    {
        $category = PostCategory::findOrFail($id);

        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'slug'            => 'nullable|string|max:255|unique:post_categories,slug,' . $id,
            'description'     => 'nullable|string',
            'parent_id'       => 'nullable|exists:post_categories,id|not_in:' . $id,
            'seo_title'       => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:160',
            'status'          => 'required|in:active,inactive',
        ]);

        try {
            $category->update([
                'name'            => $request->name,
                'slug'            => $request->slug ?: Str::slug($request->name),
                'description'     => $request->description,
                'parent_id'       => $request->parent_id,
                'seo_title'       => $request->seo_title,
                'seo_description' => $request->seo_description,
                'status'          => $request->status,
            ]);

            return redirect()->route('admin.post-categories.index')
                ->with('success', 'Cập nhật danh mục thành công!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Xóa category
     */
    public function destroy($id)
    {
        try {
            $category = PostCategory::findOrFail($id);
            
            // Kiểm tra xem có bài viết nào đang dùng category này không
            if ($category->blogs()->count() > 0) {
                return back()->with('error', 'Không thể xóa danh mục này vì đang có bài viết sử dụng!');
            }

            $category->delete();

            return redirect()->route('admin.post-categories.index')
                ->with('success', 'Đã xóa danh mục thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật sort order (drag & drop)
     */
    public function updateSortOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
        ]);

        try {
            foreach ($request->items as $index => $item) {
                PostCategory::where('id', $item['id'])->update([
                    'sort_order' => $index + 1,
                    'parent_id'  => $item['parent_id'] ?? null,
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
