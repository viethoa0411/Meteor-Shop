<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // Hiển thị danh sách danh mục
    public function list(Request $request)
    {
        $query = Category::with('parent');

        // Nếu có từ khóa tìm kiếm
        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        // Lấy toàn bộ danh mục
        $categories = $query->orderBy('id', 'desc')->get();

        return view('admin.categories.list', compact('categories'));
    }



    // Hiển thị form thêm danh mục
    public function create()
    {
        // Lấy tất cả danh mục cha để người dùng chọn
        $parents = Category::whereNull('parent_id')->get();
        return view('admin.categories.create', compact('parents'));
    }

    // Xử lý lưu danh mục
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:active,inactive',
        ]);

        // Tạo slug tự động nếu chưa nhập
        $slug = $request->slug ?: Str::slug($request->name);

        Category::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'parent_id' => $request->parent_id,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.categories.list')
            ->with('success', 'Thêm danh mục thành công!');
    }

    public function edit($id)
    {
        // Lấy danh mục cần sửa
        $category = Category::findOrFail($id);

        // Lấy tất cả danh mục cha để chọn (trừ chính nó để tránh lỗi lặp)
        $parents = Category::whereNull('parent_id')
            ->where('id', '!=', $id)
            ->get();

        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:active,inactive',
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => $request->slug ?: \Illuminate\Support\Str::slug($request->name),
            'description' => $request->description,
            'parent_id' => $request->parent_id,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.categories.list')
            ->with('success', 'Cập nhật danh mục thành công!');
    }
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return redirect()->route('admin.categories.list')
                ->with('error', 'Danh mục không tồn tại!');
        }

        // Nếu danh mục có danh mục con
        if ($category->children()->count() > 0) {
            return redirect()->route('admin.categories.list')
                ->with('error', 'Không thể xoá danh mục vì vẫn còn danh mục con!');
        }

        $category->delete();

        return redirect()->route('admin.categories.list')
            ->with('success', 'Xoá danh mục thành công!');
    }
}
