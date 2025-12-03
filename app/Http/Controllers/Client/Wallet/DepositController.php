<?php

namespace App\Http\Controllers\Client\Wallet;

use App\Http\Controllers\Controller;
use App\Models\ClientWallet;
use App\Models\DepositRequest;
use App\Models\WalletSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

 
class DepositController extends Controller
{
    /**
     * Trang nạp tiền
     * - Hiển thị QR code
     * - Hiển thị thông tin ngân hàng
     * - Form nhập số tiền nạp
     */
    public function index()
    {
        $user = Auth::user();
        $wallet = ClientWallet::getOrCreateForUser($user->id);
        $settings = WalletSetting::getSettings();
        
        // Lấy các yêu cầu nạp tiền đang chờ
        $pendingDeposits = DepositRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('client.wallet.deposit', compact('wallet', 'settings', 'pendingDeposits'));
    }

    
}

