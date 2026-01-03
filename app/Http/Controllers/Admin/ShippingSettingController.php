<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingSetting;
use App\Models\ShippingDistance;
use Illuminate\Http\Request;
use App\Helpers\ShippingHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ShippingDistanceImport;
use App\Exports\ShippingDistanceTemplateExport;

class ShippingSettingController extends Controller
{
    public function index()
    {
        $settings = ShippingSetting::getSettings();
        $provinces = ShippingDistance::select('province_name')
            ->distinct()
            ->orderBy('province_name')
            ->pluck('province_name');
        return view('admin.shipping.index', compact('settings', 'provinces'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'origin_address' => 'nullable|string|max:500',
            'origin_city' => 'nullable|string|max:255',
            'origin_district' => 'nullable|string|max:255',
            'origin_ward' => 'nullable|string|max:255',
            'base_fee' => 'nullable|numeric|min:0',
            'fee_per_km' => 'nullable|numeric|min:0',
            'default_distance_km' => 'nullable|numeric|min:0',
            'inner_city_fee' => 'nullable|numeric|min:0',
            'outer_city_fee' => 'nullable|numeric|min:0',
            'other_province_fee' => 'nullable|numeric|min:0',
            'first_length_price' => 'nullable|numeric|min:0',
            'next_length_price' => 'nullable|numeric|min:0',
            'first_width_price' => 'nullable|numeric|min:0',
            'next_width_price' => 'nullable|numeric|min:0',
            'first_height_price' => 'nullable|numeric|min:0',
            'next_height_price' => 'nullable|numeric|min:0',
            'first_weight_price' => 'nullable|numeric|min:0',
            'next_weight_price' => 'nullable|numeric|min:0',
            'length_block_cm' => 'nullable|integer|min:1|max:1000',
            'width_block_cm' => 'nullable|integer|min:1|max:1000',
            'height_block_cm' => 'nullable|integer|min:1|max:1000',
            'weight_block_kg' => 'nullable|integer|min:1|max:100',
            'express_surcharge_type' => 'nullable|in:percent,fixed',
            'fast_surcharge_type' => 'nullable|in:percent,fixed',
            'express_surcharge_value' => 'nullable|numeric|min:0',
            'fast_surcharge_value' => 'nullable|numeric|min:0',
            'express_label' => 'nullable|string|max:255',
            'fast_label' => 'nullable|string|max:255',
            'installation_fee' => 'nullable|numeric|min:0',
            'same_order_discount_percent' => 'nullable|numeric|min:0|max:100',
            'volume_price_per_m3' => 'nullable|numeric|min:0',
            'min_shipping_fee' => 'nullable|numeric|min:0',
            'conversion_factor' => 'nullable|integer|min:1000|max:10000',
            'price_per_km_per_ton' => 'nullable|numeric|min:0',
            'free_km_first' => 'nullable|numeric|min:0',
            'labor_fee_type' => 'nullable|in:percent,fixed',
            'labor_fee_value' => 'nullable|numeric|min:0',
        ]);

        $settings = ShippingSetting::getSettings();

        $updateData = [];

        // Chỉ update địa chỉ kho hàng nếu có trong request
        if ($request->has('origin_city') && $request->has('origin_district') && $request->has('origin_ward')) {
            // Kiểm tra tỉnh/thành phố phải là miền Bắc
            if (!ShippingHelper::isNorthernProvince($request->origin_city)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Hệ thống chỉ hỗ trợ địa chỉ kho hàng tại khu vực miền Bắc. Vui lòng chọn tỉnh/thành phố miền Bắc.');
            }

            $updateData['origin_address'] = $request->origin_address;
            $updateData['origin_city'] = $request->origin_city;
            $updateData['origin_district'] = $request->origin_district;
            $updateData['origin_ward'] = $request->origin_ward;
        }

        // Chỉ update các trường nếu có trong request
        if ($request->has('base_fee')) $updateData['base_fee'] = $request->base_fee ?? 0;
        if ($request->has('fee_per_km')) $updateData['fee_per_km'] = $request->fee_per_km ?? 0;
        if ($request->has('default_distance_km')) $updateData['default_distance_km'] = $request->default_distance_km ?? 10;
        if ($request->has('inner_city_fee')) $updateData['inner_city_fee'] = $request->inner_city_fee ?? 0;
        if ($request->has('outer_city_fee')) $updateData['outer_city_fee'] = $request->outer_city_fee ?? 0;
        if ($request->has('other_province_fee')) $updateData['other_province_fee'] = $request->other_province_fee ?? 0;
        if ($request->has('first_length_price')) $updateData['first_length_price'] = $request->first_length_price ?? 0;
        if ($request->has('next_length_price')) $updateData['next_length_price'] = $request->next_length_price ?? 0;
        if ($request->has('first_width_price')) $updateData['first_width_price'] = $request->first_width_price ?? 0;
        if ($request->has('next_width_price')) $updateData['next_width_price'] = $request->next_width_price ?? 0;
        if ($request->has('first_height_price')) $updateData['first_height_price'] = $request->first_height_price ?? 0;
        if ($request->has('next_height_price')) $updateData['next_height_price'] = $request->next_height_price ?? 0;
        if ($request->has('first_weight_price')) $updateData['first_weight_price'] = $request->first_weight_price ?? 0;
        if ($request->has('next_weight_price')) $updateData['next_weight_price'] = $request->next_weight_price ?? 0;
        if ($request->has('length_block_cm')) $updateData['length_block_cm'] = $request->length_block_cm ?? 200;
        if ($request->has('width_block_cm')) $updateData['width_block_cm'] = $request->width_block_cm ?? 200;
        if ($request->has('height_block_cm')) $updateData['height_block_cm'] = $request->height_block_cm ?? 200;
        if ($request->has('weight_block_kg')) $updateData['weight_block_kg'] = $request->weight_block_kg ?? 10;
        if ($request->has('express_surcharge_type')) $updateData['express_surcharge_type'] = $request->express_surcharge_type;
        if ($request->has('express_surcharge_value')) $updateData['express_surcharge_value'] = $request->express_surcharge_value ?? 0;
        if ($request->has('fast_surcharge_type')) $updateData['fast_surcharge_type'] = $request->fast_surcharge_type;
        if ($request->has('fast_surcharge_value')) $updateData['fast_surcharge_value'] = $request->fast_surcharge_value ?? 0;
        if ($request->has('express_label')) $updateData['express_label'] = $request->express_label;
        if ($request->has('fast_label')) $updateData['fast_label'] = $request->fast_label;
        if ($request->has('installation_fee')) $updateData['installation_fee'] = $request->installation_fee ?? 0;
        if ($request->has('same_order_discount_percent')) $updateData['same_order_discount_percent'] = $request->same_order_discount_percent ?? 0;
        if ($request->has('volume_price_per_m3')) $updateData['volume_price_per_m3'] = $request->volume_price_per_m3 ?? 5000;
        if ($request->has('min_shipping_fee')) $updateData['min_shipping_fee'] = $request->min_shipping_fee ?? 30000;
        if ($request->has('conversion_factor')) $updateData['conversion_factor'] = $request->conversion_factor ?? 5000;
        if ($request->has('price_per_km_per_ton')) $updateData['price_per_km_per_ton'] = $request->price_per_km_per_ton ?? 17000;
        if ($request->has('free_km_first')) $updateData['free_km_first'] = $request->free_km_first ?? 10.0;
        if ($request->has('labor_fee_type')) $updateData['labor_fee_type'] = $request->labor_fee_type;
        if ($request->has('labor_fee_value')) $updateData['labor_fee_value'] = $request->labor_fee_value ?? 10.0;

        $settings->update($updateData);

        return redirect()->route('admin.shipping.index')
            ->with('success', 'Cập nhật cài đặt vận chuyển thành công!');
    }

    /**
     * API tính phí vận chuyển (cho AJAX)
     */
    public function calculateFee(Request $request)
    {
        $request->validate([
            'city' => 'nullable|string',
            'district' => 'nullable|string',
            'subtotal' => 'required|numeric|min:0',
            'method' => 'nullable|string|in:standard,express,fast',
        ]);

        $settings = ShippingSetting::getSettings();
        // Tạo items mẫu để tính phí
        $items = [[
            'length_cm' => 100,
            'width_cm' => 50,
            'height_cm' => 30,
            'weight_kg' => 1,
            'quantity' => 1,
        ]];
        $feeData = $settings->calculateShippingFee($items, $request->method ?? 'standard', $request->subtotal);
        $fee = $feeData['total'];

        $isFreeShipping = $request->subtotal >= $settings->free_shipping_threshold;

        return response()->json([
            'success' => true,
            'fee' => $fee,
            'fee_formatted' => $fee > 0 ? number_format($fee) . ' đ' : 'Miễn phí',
            'is_free_shipping' => $isFreeShipping,
            'free_shipping_threshold' => $settings->free_shipping_threshold,
            'message' => $isFreeShipping 
                ? 'Đơn hàng được miễn phí vận chuyển!' 
                : 'Phí vận chuyển: ' . number_format($fee) . ' đ'
        ]);
    }

    /**
     * ============================================
     * SHIPPING DISTANCES CRUD
     * ============================================
     */

    /**
     * Hiển thị trang quản lý khoảng cách vận chuyển
     */
    public function distancesIndex()
    {
        $provinces = ShippingDistance::select('province_name')
            ->distinct()
            ->orderBy('province_name')
            ->pluck('province_name');
        
        return view('admin.shipping.distances.index', compact('provinces'));
    }

    /**
     * API lấy dữ liệu cho DataTable (AJAX)
     */
    public function distancesData(Request $request)
    {
        $query = ShippingDistance::query();

        // Lọc theo tỉnh
        if ($request->has('province') && $request->province) {
            $query->where('province_name', $request->province);
        }

        // Tìm kiếm
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('province_name', 'like', "%{$search}%")
                  ->orWhere('district_name', 'like', "%{$search}%");
            });
        }

        // Sắp xếp
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc');
        $columns = ['id', 'province_name', 'district_name', 'distance_km'];
        $orderBy = $columns[$orderColumn] ?? 'id';
        $query->orderBy($orderBy, $orderDir);

        // Phân trang
        $perPage = $request->input('length', 10);
        $page = ($request->input('start', 0) / $perPage) + 1;
        $distances = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'draw' => intval($request->input('draw', 1)),
            'recordsTotal' => ShippingDistance::count(),
            'recordsFiltered' => $distances->total(),
            'data' => $distances->items() ? collect($distances->items())->map(function($distance) {
                return [
                    'id' => $distance->id,
                    'province_name' => $distance->province_name,
                    'district_name' => $distance->district_name,
                    'distance_km' => number_format($distance->distance_km, 2),
                ];
            })->toArray() : [],
        ]);
    }

    /**
     * Lưu khoảng cách mới
     */
    public function distancesStore(Request $request)
    {
        // Kiểm tra trùng lặp trước khi validate
        $exists = ShippingDistance::where('province_name', $request->province_name)
            ->where('district_name', $request->district_name)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Địa chỉ này đã tồn tại trong hệ thống. Vui lòng chọn địa chỉ khác hoặc cập nhật bản ghi hiện có.',
                'errors' => [
                    'district_name' => ['Địa chỉ ' . $request->province_name . ' - ' . $request->district_name . ' đã tồn tại.']
                ]
            ], 422);
        }

        $request->validate([
            'province_name' => 'required|string|max:255',
            'district_name' => 'required|string|max:255',
            'distance_km' => 'required|numeric|min:0',
        ], [
            'province_name.required' => 'Vui lòng chọn tỉnh/thành phố',
            'district_name.required' => 'Vui lòng chọn quận/huyện',
            'distance_km.required' => 'Vui lòng nhập khoảng cách',
            'distance_km.numeric' => 'Khoảng cách phải là số',
            'distance_km.min' => 'Khoảng cách phải lớn hơn hoặc bằng 0',
        ]);

        try {
            $distance = ShippingDistance::create([
                'province_name' => $request->province_name,
                'district_name' => $request->district_name,
                'distance_km' => $request->distance_km,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thêm khoảng cách thành công!',
                'data' => $distance,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lấy thông tin một khoảng cách
     */
    public function distancesShow($id)
    {
        $distance = ShippingDistance::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $distance,
        ]);
    }

    /**
     * Cập nhật khoảng cách
     */
    public function distancesUpdate(Request $request, $id)
    {
        try {
            $distance = ShippingDistance::findOrFail($id);

            // Kiểm tra trùng lặp nếu có thay đổi tỉnh/huyện
            if ($distance->province_name !== $request->province_name || $distance->district_name !== $request->district_name) {
                $exists = ShippingDistance::where('province_name', $request->province_name)
                    ->where('district_name', $request->district_name)
                    ->where('id', '!=', $id)
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Địa chỉ này đã tồn tại trong hệ thống. Vui lòng chọn địa chỉ khác.',
                        'errors' => [
                            'district_name' => ['Địa chỉ ' . $request->province_name . ' - ' . $request->district_name . ' đã tồn tại.']
                        ]
                    ], 422);
                }
            }

            $request->validate([
                'province_name' => 'required|string|max:255',
                'district_name' => 'required|string|max:255',
                'distance_km' => 'required|numeric|min:0',
            ], [
                'province_name.required' => 'Vui lòng chọn tỉnh/thành phố',
                'district_name.required' => 'Vui lòng chọn quận/huyện',
                'distance_km.required' => 'Vui lòng nhập khoảng cách',
                'distance_km.numeric' => 'Khoảng cách phải là số',
                'distance_km.min' => 'Khoảng cách phải lớn hơn hoặc bằng 0',
            ]);

            $distance->update([
                'province_name' => $request->province_name,
                'district_name' => $request->district_name,
                'distance_km' => $request->distance_km,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật khoảng cách thành công!',
                'data' => $distance,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Xóa khoảng cách
     */
    public function distancesDestroy($id)
    {
        try {
            $distance = ShippingDistance::findOrFail($id);
            $distance->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa khoảng cách thành công!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download file Excel mẫu
     */
    public function downloadTemplate()
    {
        return Excel::download(new ShippingDistanceTemplateExport, 'mau_khoang_cach_van_chuyen.xlsx');
    }

    /**
     * Import Excel khoảng cách vận chuyển
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:2048',
        ], [
            'file.required' => 'Vui lòng chọn file Excel',
            'file.mimes' => 'File phải có định dạng .xlsx hoặc .xls',
            'file.max' => 'File không được vượt quá 2MB',
        ]);

        try {
            $import = new ShippingDistanceImport();
            Excel::import($import, $request->file('file'));

            $successCount = $import->getSuccessCount();
            $updateCount = $import->getUpdateCount();
            $failureCount = $import->getSkipCount();

            // Tạo message chi tiết
            $messages = [];
            if ($successCount > 0) {
                $messages[] = "Thêm mới: {$successCount} bản ghi";
            }
            if ($updateCount > 0) {
                $messages[] = "Cập nhật: {$updateCount} bản ghi";
            }
            if ($failureCount > 0) {
                $messages[] = "Lỗi: {$failureCount} bản ghi";
            }

            $message = implode(', ', $messages);

            // Lấy danh sách lỗi nếu có
            $failures = $import->failures();
            $errors = [];

            foreach ($failures as $failure) {
                $errors[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                    'values' => $failure->values(),
                ];
            }

            return response()->json([
                'success' => $failureCount === 0,
                'message' => $message,
                'data' => [
                    'success_count' => $successCount,
                    'update_count' => $updateCount,
                    'failure_count' => $failureCount,
                    'errors' => $errors,
                ],
            ]);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];

            foreach ($failures as $failure) {
                $errors[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                ];
            }

            return response()->json([
                'success' => false,
                'message' => 'File Excel có lỗi validation',
                'data' => [
                    'errors' => $errors,
                ],
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi import: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hiển thị trang chi tiết khoảng cách (xem/sửa/xóa)
     */
    public function showDetail($id)
    {
        $distance = ShippingDistance::findOrFail($id);
        return view('admin.shipping.distances.detail', compact('distance'));
    }

    /**
     * Cập nhật khoảng cách từ trang chi tiết
     */
    public function updateDetail(Request $request, $id)
    {
        try {
            $distance = ShippingDistance::findOrFail($id);

            // Kiểm tra trùng lặp nếu có thay đổi tỉnh/huyện
            if ($distance->province_name !== $request->province_name || $distance->district_name !== $request->district_name) {
                $exists = ShippingDistance::where('province_name', $request->province_name)
                    ->where('district_name', $request->district_name)
                    ->where('id', '!=', $id)
                    ->exists();

                if ($exists) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Địa chỉ ' . $request->province_name . ' - ' . $request->district_name . ' đã tồn tại trong hệ thống.');
                }
            }

            $request->validate([
                'province_name' => 'required|string|max:255',
                'district_name' => 'required|string|max:255',
                'distance_km' => 'required|numeric|min:0',
            ], [
                'province_name.required' => 'Vui lòng nhập tỉnh/thành phố',
                'district_name.required' => 'Vui lòng nhập quận/huyện',
                'distance_km.required' => 'Vui lòng nhập khoảng cách',
                'distance_km.numeric' => 'Khoảng cách phải là số',
                'distance_km.min' => 'Khoảng cách phải lớn hơn hoặc bằng 0',
            ]);

            $distance->update([
                'province_name' => $request->province_name,
                'district_name' => $request->district_name,
                'distance_km' => $request->distance_km,
            ]);

            return redirect()->route('admin.shipping.distances.detail', $id)
                ->with('success', 'Cập nhật khoảng cách thành công!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}

