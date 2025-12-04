<?php

namespace App\Http\Controllers\Admin\Wallet;

use App\Http\Controllers\Controller;
use App\Models\WalletSetting;
use Illuminate\Http\Request;

 
class SettingsController extends Controller
{
    /**
     * Trang cài đặt
     */
    public function index()
    {
        $settings = WalletSetting::getSettings();
        $bankCodes = WalletSetting::BANK_CODES;
        
        return view('admin.wallet.settings', compact('settings', 'bankCodes'));
    }

    /**
     * Cập nhật cài đặt
     */
    public function update(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'bank_account' => 'required|string|max:50',
            'account_holder' => 'required|string|max:255',
            'support_phone' => 'required|string|max:20',
        ]);

        $settings = WalletSetting::getSettings();
        
        // Lấy bank code từ bank name
        $bankCode = WalletSetting::BANK_CODES[$request->bank_name] ?? 'VCB';
        
        $settings->update([
            'bank_name' => $request->bank_name,
            'bank_account' => $request->bank_account,
            'account_holder' => strtoupper($request->account_holder),
            'bank_code' => $bankCode,
            'support_phone' => $request->support_phone,
            'support_email' => $request->support_email,
            'is_active' => $request->boolean('is_active'),
        ]);
        
        return back()->with('success', 'Đã cập nhật cài đặt ví');
    }
}

