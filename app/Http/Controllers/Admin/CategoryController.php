<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // Hiển thị danh sách danh mục
    public function list(Request $request)
    {
        $categories = Category::orderBy('sort_order', 'asc')->paginate(15);
        $query = Category::with('parent');

        $status = $request->get('status', 'active');

        // Filter theo trạng thái
       if ($status !== 'all') {
            // Nếu không phải 'all' thì lọc theo status cụ thể
            $query->where('status', $status);
        }

        // Tìm kiếm theo từ khóa
        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        // Phân trang
        $categories = $query->orderBy('id', 'desc')->paginate(10);

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
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        // Tạo slug tự động nếu chưa nhập
        $slug = $request->slug ?: Str::slug($request->name);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        Category::create([
            'name' => $request->name,
            'slug' => $slug,
            'image' => $imagePath,
            'parent_id' => $request->parent_id,
            'status' => 'active',
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
            'parent_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $imagePath = $category->image;
        if ($request->hasFile('image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        $category->update([
            'name' => $request->name,
            'slug' => $request->slug ?: \Illuminate\Support\Str::slug($request->name),
            'parent_id' => $request->parent_id,
            'status' => $request->status,
            'image' => $imagePath,
        ]);

        // Nếu danh mục bị ẩn, ẩn luôn tất cả con và sản phẩm
        if ($request->status == 'inactive') {
            \Illuminate\Support\Facades\Log::info("Hiding category {$category->id} and its descendants.");

            // Lấy danh sách ID của tất cả danh mục con (đệ quy)
            $descendantIds = $this->getAllDescendantIds($category->id);
            \Illuminate\Support\Facades\Log::info("Descendant IDs: " . implode(',', $descendantIds));

            \Illuminate\Support\Facades\DB::transaction(function () use ($category, $descendantIds) {
                // Cập nhật trạng thái các danh mục con
                if (!empty($descendantIds)) {
                    \App\Models\Category::whereIn('id', $descendantIds)->update(['status' => 'inactive']);
                }

                // Cập nhật trạng thái sản phẩm (của danh mục hiện tại VÀ các danh mục con)
                $allCategoryIds = array_merge([$category->id], $descendantIds);
                \App\Models\Product::whereIn('category_id', $allCategoryIds)->update(['status' => 'inactive']);
            });
        } elseif ($request->status == 'active') {
             // Nếu danh mục được bật lại, bật lại luôn tất cả con và sản phẩm (để khôi phục trạng thái cũ)
             \Illuminate\Support\Facades\Log::info("Activating category {$category->id} and its descendants.");

             $descendantIds = $this->getAllDescendantIds($category->id);

             \Illuminate\Support\Facades\DB::transaction(function () use ($category, $descendantIds) {
                 if (!empty($descendantIds)) {
                     \App\Models\Category::whereIn('id', $descendantIds)->update(['status' => 'active']);
                 }
                 $allCategoryIds = array_merge([$category->id], $descendantIds);
                 \App\Models\Product::whereIn('category_id', $allCategoryIds)->update(['status' => 'active']);
             });
        }

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

        // Nếu danh mục có sản phẩm
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.list')
                ->with('error', 'Không thể xoá danh mục vì vẫn còn sản phẩm!');
        }

        $category->delete();

        return redirect()->route('admin.categories.list')
            ->with('success', 'Xóa danh mục thành công!');
    }

    // Hàm đệ quy lấy tất cả ID của danh mục con
    private function getAllDescendantIds($parentId)
    {
        $ids = [];
        $childrenIds = Category::where('parent_id', $parentId)->pluck('id')->toArray();

        foreach ($childrenIds as $childId) {
            $ids[] = $childId;
            $ids = array_merge($ids, $this->getAllDescendantIds($childId));
        }

        return $ids;
    }
}
