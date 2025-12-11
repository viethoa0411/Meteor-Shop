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

        $settings = ShippingSetting::getSettings(); 

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

