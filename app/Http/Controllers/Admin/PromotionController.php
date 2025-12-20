<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
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

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        return view('admin.promotions.create', compact('categories', 'products'));
    }

    public function store(Request $request)
    {
        $messages = [
            'code.required' => 'Mã khuyến mãi không được để trống.',
            'code.string' => 'Mã khuyến mãi phải là chuỗi ký tự.',
            'code.max' => 'Mã khuyến mãi không được vượt quá 100 ký tự.',
            'code.unique' => 'Mã khuyến mãi này đã tồn tại.',
            'name.required' => 'Tên khuyến mãi không được để trống.',
            'name.string' => 'Tên khuyến mãi phải là chuỗi ký tự.',
            'name.max' => 'Tên khuyến mãi không được vượt quá 255 ký tự.',
            'discount_type.required' => 'Kiểu giảm giá là bắt buộc.',
            'discount_type.in' => 'Kiểu giảm giá không hợp lệ.',
            'discount_value.required' => 'Giá trị giảm là bắt buộc.',
            'discount_value.numeric' => 'Giá trị giảm phải là số.',
            'discount_value.min' => 'Giá trị giảm phải lớn hơn 0.',
            'max_discount.numeric' => 'Giới hạn giảm tối đa phải là số.',
            'max_discount.min' => 'Giới hạn giảm tối đa không được nhỏ hơn 0.',
            'min_amount.numeric' => 'Giá trị đơn tối thiểu phải là số.',
            'min_amount.min' => 'Giá trị đơn tối thiểu không được nhỏ hơn 0.',
            'min_orders.integer' => 'Số lần mua tối thiểu phải là số nguyên.',
            'min_orders.min' => 'Số lần mua tối thiểu không được nhỏ hơn 0.',
            'start_date.required' => 'Thời gian bắt đầu là bắt buộc.',
            'start_date.date' => 'Thời gian bắt đầu không đúng định dạng.',
            'end_date.required' => 'Thời gian kết thúc là bắt buộc.',
            'end_date.date' => 'Thời gian kết thúc không đúng định dạng.',
            'end_date.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
            'limit_per_user.integer' => 'Giới hạn dùng mỗi khách phải là số nguyên.',
            'limit_per_user.min' => 'Giới hạn dùng mỗi khách không được nhỏ hơn 0.',
            'limit_global.integer' => 'Giới hạn tổng hệ thống phải là số nguyên.',
            'limit_global.min' => 'Giới hạn tổng hệ thống không được nhỏ hơn 0.',
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'scope.required' => 'Phạm vi áp dụng là bắt buộc.',
            'scope.in' => 'Phạm vi áp dụng không hợp lệ.',
            'category_ids.array' => 'Danh sách danh mục phải là mảng.',
            'category_ids.*.exists' => 'Danh mục đã chọn không tồn tại.',
            'product_ids.array' => 'Danh sách sản phẩm phải là mảng.',
            'product_ids.*.exists' => 'Sản phẩm đã chọn không tồn tại.',
        ];

        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:promotions,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0.01',
            'max_discount' => 'nullable|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'min_orders' => 'nullable|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'limit_per_user' => 'nullable|integer|min:0',
            'limit_global' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
            'scope' => 'required|in:all,category,product',
            'category_ids' => 'array',
            'category_ids.*' => 'integer|exists:categories,id',
            'product_ids' => 'array',
            'product_ids.*' => 'integer|exists:products,id',
        ], $messages);

        if ($validated['discount_type'] === 'percent' && empty($validated['max_discount'])) {
            return back()->withErrors(['max_discount' => 'Vui lòng nhập giới hạn tiền giảm tối đa khi chọn giảm theo %'])->withInput();
        }

        $promotion = Promotion::create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'max_discount' => $validated['max_discount'] ?? null,
            'min_amount' => $validated['min_amount'] ?? 0,
            'min_orders' => $validated['min_orders'] ?? 0,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'limit_per_user' => $validated['limit_per_user'] ?? null,
            'limit_global' => $validated['limit_global'] ?? null,
            'used_count' => 0,
            'status' => $validated['status'],
            'scope' => $validated['scope'],
        ]);

        if ($validated['scope'] === 'category') {
            $promotion->categories()->sync($validated['category_ids'] ?? []);
        } elseif ($validated['scope'] === 'product') {
            $promotion->products()->sync($validated['product_ids'] ?? []);
        } else {
            $promotion->categories()->detach();
            $promotion->products()->detach();
        }

        return redirect()->route('admin.promotions.list')->with('success', 'Tạo mã khuyến mãi thành công');
    }

    public function edit($id)
    {
        $promotion = Promotion::findOrFail($id);
        $categories = Category::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $selectedCategoryIds = $promotion->categories()->pluck('categories.id')->toArray();
        $selectedProductIds = $promotion->products()->pluck('products.id')->toArray();

        return view('admin.promotions.edit', compact('promotion', 'categories', 'products', 'selectedCategoryIds', 'selectedProductIds'));
    }

    public function update(Request $request, $id)
    {
        $promotion = Promotion::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:promotions,code,' . $promotion->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0.01',
            'max_discount' => 'nullable|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'min_orders' => 'nullable|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'limit_per_user' => 'nullable|integer|min:0',
            'limit_global' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
            'scope' => 'required|in:all,category,product',
            'category_ids' => 'array',
            'category_ids.*' => 'integer|exists:categories,id',
            'product_ids' => 'array',
            'product_ids.*' => 'integer|exists:products,id',
        ]);

        if ($validated['discount_type'] === 'percent' && empty($validated['max_discount'])) {
            return back()->withErrors(['max_discount' => 'Vui lòng nhập giới hạn tiền giảm tối đa khi chọn giảm theo %'])->withInput();
        }

        $promotion->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'max_discount' => $validated['max_discount'] ?? null,
            'min_amount' => $validated['min_amount'] ?? 0,
            'min_orders' => $validated['min_orders'] ?? 0,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'limit_per_user' => $validated['limit_per_user'] ?? null,
            'limit_global' => $validated['limit_global'] ?? null,
            'status' => $validated['status'],
            'scope' => $validated['scope'],
        ]);

        if ($validated['scope'] === 'category') {
            $promotion->categories()->sync($validated['category_ids'] ?? []);
            $promotion->products()->detach();
        } elseif ($validated['scope'] === 'product') {
            $promotion->products()->sync($validated['product_ids'] ?? []);
            $promotion->categories()->detach();
        } else {
            $promotion->categories()->detach();
            $promotion->products()->detach();
        }

        return redirect()->route('admin.promotions.list')->with('success', 'Cập nhật mã khuyến mãi thành công');
    }

    public function destroy($id)
    {
        $promotion = Promotion::find($id);
        if (!$promotion) {
            return redirect()->route('admin.promotions.list')->with('error', 'Mã khuyến mãi không tồn tại');
        }

        $promotion->categories()->detach();
        $promotion->products()->detach();
        $promotion->delete();

        return redirect()->route('admin.promotions.list')->with('success', 'Đã xoá mã khuyến mãi');
    }
}
