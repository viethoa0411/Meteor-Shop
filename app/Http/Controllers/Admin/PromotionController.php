<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\Category; // Cần thiết cho chức năng áp dụng theo danh mục
use App\Models\Product; // Cần thiết cho chức năng áp dụng theo sản phẩm
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    /**
     * Hiển thị danh sách các mã khuyến mãi (Promotion).
     * (Không thay đổi)
     */
    public function list(Request $request)
    {
        $query = Promotion::query();

        if ($request->filled('keyword')) {
            $kw = $request->keyword;
            $query->where(function ($q) use ($kw) {
                $q->where('code', 'like', "%$kw%")
                  ->orWhere('name', 'like', "%$kw%");
            });
        }

        if ($request->filled('status') && in_array($request->status, ['active', 'inactive', 'expired'])) {
            $query->where('status', $request->status);
        }

        $promotions = $query->orderByDesc('id')->paginate(10);

        return view('admin.promotions.list', compact('promotions'));
    }

    // ----------------------------------------------------------------------
    // PHƯƠNG THỨC THÊM MỚI (CREATE)
    // ----------------------------------------------------------------------

    /**
     * Hiển thị form tạo mã khuyến mãi mới.
     */
    public function create()
    {
        // Lấy danh sách danh mục và sản phẩm để hiển thị trong form áp dụng
        $categories = Category::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('admin.promotions.create', compact('categories', 'products'));
    }

    /**
     * Lưu dữ liệu mã khuyến mãi mới vào database.
     */
    public function store(Request $request)
    {
        // 1. Validation (Kiểm tra dữ liệu)
        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:promotions,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percent,fixed', // Kiểu giảm giá
            'discount_value' => 'required|numeric|min:0.01',

            // Các trường đã bổ sung theo yêu cầu
            'max_discount' => 'nullable|numeric|min:0', // Giới hạn tiền giảm tối đa
            'min_amount' => 'nullable|numeric|min:0',   // Giá trị đơn hàng tối thiểu
            'min_orders' => 'nullable|integer|min:0',   // Số lần mua tối thiểu (2-N+)

            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',

            'limit_global' => 'nullable|integer|min:0',     // Tổng lượt dùng tối đa
            'limit_per_user' => 'nullable|integer|min:0', // Lượt dùng trên mỗi user

            'status' => 'required|in:active,inactive',
            'scope' => 'required|in:all,category,product', // Phạm vi áp dụng

            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:categories,id',

            'product_ids' => 'nullable|array',
            'product_ids.*' => 'integer|exists:products,id',
        ]);

        // Kiểm tra logic: Bắt buộc nhập Giới hạn tiền giảm nếu chọn giảm theo %
        if ($validated['discount_type'] === 'percent' && empty($validated['max_discount'])) {
            return back()->withErrors(['max_discount' => 'Vui lòng nhập giới hạn tiền giảm tối đa khi chọn giảm theo %'])->withInput();
        }

        // 2. Tạo đối tượng Promotion
        $promotion = Promotion::create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['discount_type'], // Lưu ý: Cột trong DB là 'type'
            'value' => $validated['discount_value'],

            'max_discount_amount' => $validated['max_discount'] ?? null, // Lưu ý: Cột trong DB là 'max_discount_amount'
            'min_order_amount' => $validated['min_amount'] ?? 0,
            'min_order_count' => $validated['min_orders'] ?? 0,

            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],

            'usage_limit' => $validated['limit_global'] ?? null, // Lưu ý: Cột trong DB là 'usage_limit'
            'usage_limit_per_customer' => $validated['limit_per_user'] ?? null,

            'used_count' => 0, // Khởi tạo số lần dùng là 0
            'status' => $validated['status'],
        ]);

        // 3. Xử lý mối quan hệ (scope: category/product)
        if ($validated['scope'] === 'category') {
            // Sử dụng sync để lưu các category_ids vào bảng promotion_category
            $promotion->categories()->sync($validated['category_ids'] ?? []);
            // Đảm bảo không còn liên kết sản phẩm cũ nếu có
            $promotion->products()->detach();
        } elseif ($validated['scope'] === 'product') {
            // Sử dụng sync để lưu các product_ids vào bảng promotion_product
            $promotion->products()->sync($validated['product_ids'] ?? []);
            // Đảm bảo không còn liên kết danh mục cũ nếu có
            $promotion->categories()->detach();
        } else {
            // Nếu là 'all', detach tất cả các liên kết
            $promotion->categories()->detach();
            $promotion->products()->detach();
        }

        // 4. Chuyển hướng
        return redirect()->route('admin.promotions.list')->with('success', 'Tạo mã khuyến mãi thành công');
    }

    // Các phương thức: edit(), update(), destroy() đã được loại bỏ.
}
