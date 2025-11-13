<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Danh sách banner
     */
    public function list(Request $request)
    {
        $query = Banner::query();

        // Tìm kiếm theo từ khóa
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // Lọc theo trạng thái
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Lọc theo thời gian
        if ($request->filled('date_from')) {
            $query->where(function ($q) use ($request) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', $request->date_from);
            });
        }

        if ($request->filled('date_to')) {
            $query->where(function ($q) use ($request) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $request->date_to);
            });
        }

        // Sắp xếp theo sort_order và id
        $banners = $query->orderBy('sort_order', 'asc')
                         ->orderBy('id', 'desc')
                         ->paginate(15)
                         ->withQueryString();

        return view('admin.banners.list', compact('banners'));
    }

    /**
     * Hiển thị form tạo banner mới
     */
    public function create()
    {
        return view('admin.banners.create');
    }

    /**
     * Lưu banner mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120', // 5MB
            'link' => 'nullable|url|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề banner',
            'image.required' => 'Vui lòng chọn hình ảnh banner',
            'image.image' => 'File phải là hình ảnh',
            'image.mimes' => 'Hình ảnh phải có định dạng: jpg, jpeg, png, webp',
            'image.max' => 'Kích thước hình ảnh không được vượt quá 5MB',
            'link.url' => 'Link không hợp lệ',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu',
        ]);

        // Upload ảnh
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('banners', 'public');
        }

        // Lấy sort_order cao nhất + 1 nếu không nhập
        $sortOrder = $request->sort_order ?? (Banner::max('sort_order') ?? 0) + 1;

        Banner::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath,
            'link' => $request->link,
            'sort_order' => $sortOrder,
            'status' => $request->status,
            'start_date' => $request->start_date ? date('Y-m-d H:i:s', strtotime($request->start_date)) : null,
            'end_date' => $request->end_date ? date('Y-m-d H:i:s', strtotime($request->end_date)) : null,
        ]);

        return redirect()->route('admin.banners.list')
            ->with('success', 'Thêm banner thành công! 🎉');
    }

    /**
     * Hiển thị chi tiết banner
     */
    public function show($id)
    {
        $banner = Banner::findOrFail($id);
        return view('admin.banners.show', compact('banner'));
    }

    /**
     * Hiển thị form chỉnh sửa banner
     */
    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * Cập nhật banner
     */
    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'link' => 'nullable|url|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề banner',
            'image.image' => 'File phải là hình ảnh',
            'image.mimes' => 'Hình ảnh phải có định dạng: jpg, jpeg, png, webp',
            'image.max' => 'Kích thước hình ảnh không được vượt quá 5MB',
            'link.url' => 'Link không hợp lệ',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu',
        ]);

        // Upload ảnh mới nếu có
        $imagePath = $banner->image;
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ
            if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }
            $imagePath = $request->file('image')->store('banners', 'public');
        }

        $banner->update([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath,
            'link' => $request->link,
            'sort_order' => $request->sort_order ?? $banner->sort_order,
            'status' => $request->status,
            'start_date' => $request->start_date ? date('Y-m-d H:i:s', strtotime($request->start_date)) : null,
            'end_date' => $request->end_date ? date('Y-m-d H:i:s', strtotime($request->end_date)) : null,
        ]);

        return redirect()->route('admin.banners.list')
            ->with('success', 'Cập nhật banner thành công!');
    }

    /**
     * Xóa banner (soft delete)
     */
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        
        // Xóa ảnh
        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return redirect()->route('admin.banners.list')
            ->with('success', 'Xóa banner thành công!');
    }

    /**
     * Xóa hàng loạt
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:banners,id',
        ]);

        $banners = Banner::whereIn('id', $request->ids)->get();
        
        foreach ($banners as $banner) {
            if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }
            $banner->delete();
        }

        return redirect()->route('admin.banners.list')
            ->with('success', 'Đã xóa ' . count($request->ids) . ' banner thành công!');
    }

    /**
     * Cập nhật trạng thái nhanh
     */
    public function updateStatus(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);
        $banner->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công!',
        ]);
    }

    /**
     * Cập nhật thứ tự sắp xếp (drag & drop)
     */
    public function updateSortOrder(Request $request)
    {
        $request->validate([
            'banners' => 'required|array',
            'banners.*.id' => 'required|exists:banners,id',
            'banners.*.sort_order' => 'required|integer',
        ]);

        foreach ($request->banners as $item) {
            Banner::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thứ tự thành công!',
        ]);
    }

    /**
     * Trang thùng rác (soft deleted banners)
     */
    public function trash(Request $request)
    {
        $query = Banner::onlyTrashed();

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        $banners = $query->orderBy('deleted_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.banners.trash', compact('banners'));
    }

    /**
     * Khôi phục banner
     */
    public function restore($id)
    {
        $banner = Banner::onlyTrashed()->findOrFail($id);
        $banner->restore();

        return redirect()->route('admin.banners.trash')
            ->with('success', 'Khôi phục banner thành công!');
    }

    /**
     * Xóa vĩnh viễn
     */
    public function forceDelete($id)
    {
        $banner = Banner::onlyTrashed()->findOrFail($id);
        
        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->forceDelete();

        return redirect()->route('admin.banners.trash')
            ->with('success', 'Xóa vĩnh viễn banner thành công!');
    }
}
