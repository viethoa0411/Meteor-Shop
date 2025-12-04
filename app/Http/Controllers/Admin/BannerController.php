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

        // Lọc theo thời gian - tìm banner đang hoạt động trong khoảng thời gian
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
            'start_date' => 'nullable|date_format:Y-m-d\TH:i',
            'end_date' => 'nullable|date_format:Y-m-d\TH:i|after_or_equal:start_date',
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
        try {
            // Đảm bảo thư mục tồn tại
            if (!Storage::disk('public')->exists('banners')) {
                Storage::disk('public')->makeDirectory('banners');
            }
            
            $imagePath = $request->file('image')->store('banners', 'public');
            
            // Kiểm tra file đã được upload thành công
            if (!$imagePath || !Storage::disk('public')->exists($imagePath)) {
                throw new \Exception('File không được upload thành công');
            }
        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['image' => 'Lỗi khi upload ảnh: ' . $e->getMessage()]);
        }
        // Chuẩn hóa path ảnh (đảm bảo luôn dạng banners/filename.ext)
        if (!empty($imagePath)) {
            $normalized = ltrim($imagePath, '/');
            $normalized = str_replace(['storage/', 'public/'], '', $normalized);
            $imagePath = $normalized;
        }


        // Lấy sort_order cao nhất + 1 nếu không nhập
        $sortOrder = $request->sort_order ?? (Banner::max('sort_order') ?? 0) + 1;

        Banner::create([
            'title' => $request->title,
            'description' => $request->description ?? null,
            'image' => $imagePath,
            'link' => $request->link ?? null,
            'sort_order' => $sortOrder,
            'status' => $request->status,
            'start_date' => $request->start_date ? date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $request->start_date))) : null,
            'end_date' => $request->end_date ? date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $request->end_date))) : null,
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
            'start_date' => 'nullable|date_format:Y-m-d\TH:i',
            'end_date' => 'nullable|date_format:Y-m-d\TH:i|after_or_equal:start_date',
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
            // Xóa ảnh cũ nếu có
            if (!empty($banner->image)) {
                try {
                    $oldImagePath = $banner->image;
                    if (Storage::disk('public')->exists($oldImagePath)) {
                        Storage::disk('public')->delete($oldImagePath);
                    }
                } catch (\Exception $e) {
                    // Bỏ qua lỗi nếu không xóa được ảnh cũ
                }
            }
            // Upload ảnh mới
            try {
                // Đảm bảo thư mục tồn tại
                if (!Storage::disk('public')->exists('banners')) {
                    Storage::disk('public')->makeDirectory('banners');
                }
                
                $imagePath = $request->file('image')->store('banners', 'public');
                
                // Kiểm tra file đã được upload thành công
                if (!$imagePath || !Storage::disk('public')->exists($imagePath)) {
                    throw new \Exception('File không được upload thành công');
                }
            } catch (\Exception $e) {
                return back()->withInput()
                    ->withErrors(['image' => 'Lỗi khi upload ảnh: ' . $e->getMessage()]);
            }
        }

        // Chuẩn hóa path ảnh (đảm bảo luôn dạng banners/filename.ext)
        if (!empty($imagePath)) {
            $normalized = ltrim($imagePath, '/');
            $normalized = str_replace(['storage/', 'public/'], '', $normalized);
            $imagePath = $normalized;
        }


        $banner->update([
            'title' => $request->title,
            'description' => $request->description ?? null,
            'image' => $imagePath,
            'link' => $request->link ?? null,
            'sort_order' => $request->sort_order ?? $banner->sort_order,
            'status' => $request->status,
            'start_date' => $request->start_date ? date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $request->start_date))) : null,
            'end_date' => $request->end_date ? date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $request->end_date))) : null,
        ]);

        return redirect()->route('admin.banners.list')
            ->with('success', 'Cập nhật banner thành công!');
    }

    /**
     * Xóa banner (soft delete)
     * Lưu ý: Không xóa ảnh khi soft delete để có thể khôi phục
     */
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        
        // Soft delete - không xóa ảnh để có thể khôi phục
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
            // Soft delete - không xóa ảnh để có thể khôi phục
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
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

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
            'banners.*.sort_order' => 'required|integer|min:0',
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
        
        // Xóa ảnh nếu có
        if (!empty($banner->image)) {
            try {
                if (Storage::disk('public')->exists($banner->image)) {
                    Storage::disk('public')->delete($banner->image);
                }
            } catch (\Exception $e) {
                // Bỏ qua lỗi nếu không xóa được ảnh
            }
        }

        $banner->forceDelete();

        return redirect()->route('admin.banners.trash')
            ->with('success', 'Xóa vĩnh viễn banner thành công!');
    }

    /**
     * Khôi phục hàng loạt
     */
    public function bulkRestore(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:banners,id',
        ]);

        $banners = Banner::onlyTrashed()->whereIn('id', $request->ids)->get();
        
        foreach ($banners as $banner) {
            $banner->restore();
        }

        return redirect()->route('admin.banners.trash')
            ->with('success', 'Đã khôi phục ' . count($request->ids) . ' banner thành công!');
    }

    /**
     * Xóa vĩnh viễn hàng loạt
     */
    public function bulkForceDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:banners,id',
        ]);

        $banners = Banner::onlyTrashed()->whereIn('id', $request->ids)->get();
        
        foreach ($banners as $banner) {
            // Xóa ảnh nếu có
            if (!empty($banner->image)) {
                try {
                    if (Storage::disk('public')->exists($banner->image)) {
                        Storage::disk('public')->delete($banner->image);
                    }
                } catch (\Exception $e) {
                    // Bỏ qua lỗi nếu không xóa được ảnh
                }
            }
            $banner->forceDelete();
        }

        return redirect()->route('admin.banners.trash')
            ->with('success', 'Đã xóa vĩnh viễn ' . count($request->ids) . ' banner thành công!');
    }

    /**
     * Cập nhật trạng thái hàng loạt
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:banners,id',
            'status' => 'required|in:active,inactive',
        ]);

        Banner::whereIn('id', $request->ids)->update(['status' => $request->status]);

        return redirect()->route('admin.banners.list')
            ->with('success', 'Đã cập nhật trạng thái ' . count($request->ids) . ' banner thành công!');
    }

    /**
     * Duplicate banner
     */
    public function duplicate($id)
    {
        $banner = Banner::findOrFail($id);
        
        // Tạo banner mới từ banner hiện tại
        $newBanner = $banner->replicate();
        $newBanner->title = $banner->title . ' (Copy)';
        $newBanner->sort_order = (Banner::max('sort_order') ?? 0) + 1;
        $newBanner->status = 'inactive'; // Mặc định inactive khi duplicate
        $newBanner->save();

        // Copy ảnh nếu có
        if (!empty($banner->image)) {
            try {
                $oldPath = $banner->image;
                $extension = pathinfo($oldPath, PATHINFO_EXTENSION);
                $newPath = 'banners/' . uniqid() . '_' . time() . '.' . $extension;
                
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->copy($oldPath, $newPath);
                    $newBanner->update(['image' => $newPath]);
                }
            } catch (\Exception $e) {
                // Bỏ qua lỗi nếu không copy được ảnh
            }
        }

        return redirect()->route('admin.banners.edit', $newBanner->id)
            ->with('success', 'Đã tạo bản sao banner thành công!');
    }
}
