<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingSetting;
use Illuminate\Http\Request;

class ShippingSettingController extends Controller
{
    public function index()
    {
        $settings = ShippingSetting::getSettings();
        return view('admin.shipping.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'origin_address' => 'nullable|string|max:500',
            'origin_city' => 'required|string|max:255',
            'origin_district' => 'required|string|max:255',
            'origin_ward' => 'required|string|max:255',
            'base_fee' => 'required|numeric|min:0',
            'fee_per_km' => 'required|numeric|min:0',
            'free_shipping_threshold' => 'required|numeric|min:0',
            'inner_city_fee' => 'nullable|numeric|min:0',
            'outer_city_fee' => 'nullable|numeric|min:0',
            'other_province_fee' => 'nullable|numeric|min:0',
            'first_length_price' => 'required|numeric|min:0',
            'next_length_price' => 'required|numeric|min:0',
            'first_width_price' => 'required|numeric|min:0',
            'next_width_price' => 'required|numeric|min:0',
            'first_height_price' => 'required|numeric|min:0',
            'next_height_price' => 'required|numeric|min:0',
            'first_weight_price' => 'required|numeric|min:0',
            'next_weight_price' => 'required|numeric|min:0',
            'express_surcharge_type' => 'required|in:percent,fixed',
            'fast_surcharge_type' => 'required|in:percent,fixed',
            'express_surcharge_value' => 'required|numeric|min:0',
            'fast_surcharge_value' => 'required|numeric|min:0',
            'express_label' => 'required|string|max:255',
            'fast_label' => 'required|string|max:255',
        ], [
            'origin_city.required' => 'Vui lòng chọn tỉnh/thành phố',
            'origin_district.required' => 'Vui lòng chọn quận/huyện',
            'origin_ward.required' => 'Vui lòng chọn phường/xã',
            'base_fee.required' => 'Vui lòng nhập phí cơ bản',
            'fee_per_km.required' => 'Vui lòng nhập phí mỗi km',
            'free_shipping_threshold.required' => 'Vui lòng nhập ngưỡng miễn phí vận chuyển',
            'first_length_price.required' => 'Vui lòng nhập phí chiều dài mét đầu',
            'next_length_price.required' => 'Vui lòng nhập phí chiều dài mét tiếp theo',
            'first_width_price.required' => 'Vui lòng nhập phí chiều rộng mét đầu',
            'next_width_price.required' => 'Vui lòng nhập phí chiều rộng mét tiếp theo',
            'first_height_price.required' => 'Vui lòng nhập phí chiều cao mét đầu',
            'next_height_price.required' => 'Vui lòng nhập phí chiều cao mét tiếp theo',
            'first_weight_price.required' => 'Vui lòng nhập phí cân nặng đầu',
            'next_weight_price.required' => 'Vui lòng nhập phí cân nặng tiếp theo',
            'express_label.required' => 'Vui lòng nhập tên hiển thị cho giao nhanh',
            'fast_label.required' => 'Vui lòng nhập tên hiển thị cho giao hỏa tốc',
        ]);

        $settings = ShippingSetting::getSettings();
        
        $settings->update([
            'origin_address' => $request->origin_address,
            'origin_city' => $request->origin_city,
            'origin_district' => $request->origin_district,
            'origin_ward' => $request->origin_ward,
            'base_fee' => $request->base_fee,
            'fee_per_km' => $request->fee_per_km,
            'free_shipping_threshold' => $request->free_shipping_threshold,
            'inner_city_fee' => $request->inner_city_fee ?? 0,
            'outer_city_fee' => $request->outer_city_fee ?? 0,
            'other_province_fee' => $request->other_province_fee ?? 0,
            'first_length_price' => $request->first_length_price,
            'next_length_price' => $request->next_length_price,
            'first_width_price' => $request->first_width_price,
            'next_width_price' => $request->next_width_price,
            'first_height_price' => $request->first_height_price,
            'next_height_price' => $request->next_height_price,
            'first_weight_price' => $request->first_weight_price,
            'next_weight_price' => $request->next_weight_price,
            'express_surcharge_type' => $request->express_surcharge_type,
            'express_surcharge_value' => $request->express_surcharge_value,
            'fast_surcharge_type' => $request->fast_surcharge_type,
            'fast_surcharge_value' => $request->fast_surcharge_value,
            'express_label' => $request->express_label,
            'fast_label' => $request->fast_label,
        ]);

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
        $feeData = $settings->calculateShippingFee([], $request->method ?? 'standard', $request->subtotal);
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
}

