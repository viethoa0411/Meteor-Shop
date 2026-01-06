<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Http\Controllers\Controller;
use App\Models\PostTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostTagController extends Controller
{
    /**
     * Hiển thị danh sách tags
     */
    public function index(Request $request)
    {
        $query = PostTag::query();

        if ($request->has('keyword') && $request->keyword != '') {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('slug', 'like', "%{$keyword}%");
            });
        }

        $tags = $query->orderBy('usage_count', 'desc')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.blogs.tags.index', compact('tags'));
    }

    /**
     * Lưu tag mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:post_tags,name',
            'slug'        => 'nullable|string|max:255|unique:post_tags,slug',
            'description' => 'nullable|string',
        ]);

        try {
            $tag = PostTag::create([
                'name'        => $request->name,
                'slug'        => $request->slug ?: Str::slug($request->name),
                'description' => $request->description,
            ]);

            return redirect()->route('admin.post-tags.index')
                ->with('success', 'Thêm tag thành công!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật tag
     */
    public function update(Request $request, $id)
    {
        $tag = PostTag::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:post_tags,name,' . $id,
            'slug'        => 'nullable|string|max:255|unique:post_tags,slug,' . $id,
            'description' => 'nullable|string',
        ]);

        try {
            $tag->update([
                'name'        => $request->name,
                'slug'        => $request->slug ?: Str::slug($request->name),
                'description' => $request->description,
            ]);

            return redirect()->route('admin.post-tags.index')
                ->with('success', 'Cập nhật tag thành công!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Xóa tag
     */
    public function destroy($id)
    {
        try {
            $tag = PostTag::findOrFail($id);
            $tag->delete();

            return redirect()->route('admin.post-tags.index')
                ->with('success', 'Đã xóa tag thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * API: Tìm kiếm tags (autocomplete)
     */
    public function search(Request $request)
    {
        $keyword = $request->get('q', '');
        
        $tags = PostTag::where('name', 'like', "%{$keyword}%")
            ->orWhere('slug', 'like', "%{$keyword}%")
            ->orderBy('usage_count', 'desc')
            ->limit(10)
            ->get(['id', 'name', 'slug']);

        return response()->json($tags);
    }
}
