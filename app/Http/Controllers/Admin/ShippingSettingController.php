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
            'inner_city_fee' => 'required|numeric|min:0',
            'outer_city_fee' => 'required|numeric|min:0',
            'other_province_fee' => 'required|numeric|min:0',
        ], [
            'origin_city.required' => 'Vui lòng chọn tỉnh/thành phố',
            'origin_district.required' => 'Vui lòng chọn quận/huyện',
            'origin_ward.required' => 'Vui lòng chọn phường/xã',
            'base_fee.required' => 'Vui lòng nhập phí cơ bản',
            'fee_per_km.required' => 'Vui lòng nhập phí mỗi km',
            'free_shipping_threshold.required' => 'Vui lòng nhập ngưỡng miễn phí vận chuyển',
            'inner_city_fee.required' => 'Vui lòng nhập phí nội thành',
            'outer_city_fee.required' => 'Vui lòng nhập phí ngoại thành',
            'other_province_fee.required' => 'Vui lòng nhập phí tỉnh khác',
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
            'inner_city_fee' => $request->inner_city_fee,
            'outer_city_fee' => $request->outer_city_fee,
            'other_province_fee' => $request->other_province_fee,
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
            'city' => 'required|string',
            'district' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $settings = ShippingSetting::getSettings();
        $fee = $settings->calculateShippingFee(
            $request->city,
            $request->district,
            $request->subtotal
        );

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

