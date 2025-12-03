<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    /**
     * Hiển thị danh sách các mã khuyến mãi (Promotion).
     * Bao gồm chức năng tìm kiếm và lọc.
     */
    public function list(Request $request)
    {
        $query = Promotion::query();

        // Xử lý tìm kiếm theo từ khóa (code hoặc name)
        if ($request->filled('keyword')) {
            $kw = $request->keyword;
            $query->where(function ($q) use ($kw) {
                $q->where('code', 'like', "%$kw%")
                  ->orWhere('name', 'like', "%$kw%");
            });
        }

        // Xử lý lọc theo trạng thái
        if ($request->filled('status') && in_array($request->status, ['active', 'inactive', 'expired'])) {
            $query->where('status', $request->status);
        }

        // Phân trang và sắp xếp
        $promotions = $query->orderByDesc('id')->paginate(10);

        return view('admin.promotions.list', compact('promotions'));
    }

}
