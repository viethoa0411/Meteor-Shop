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

    /**
     * Xử lý yêu cầu nạp tiền
     * - Validate số tiền
     * - Tạo yêu cầu nạp tiền
     * - Gửi mail thông báo cho admin
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000|max:999999999999', // Tối thiểu 10,000đ, tối đa 999 tỷ
        ], [
            'amount.required' => 'Vui lòng nhập số tiền nạp',
            'amount.numeric' => 'Số tiền phải là số',
            'amount.min' => 'Số tiền nạp tối thiểu là 10,000đ',
            'amount.max' => 'Số tiền nạp tối đa là 999,999,999,999đ',
        ]);

        $user = Auth::user();
        $wallet = ClientWallet::getOrCreateForUser($user->id);
        
        // Tạo yêu cầu nạp tiền
        $deposit = DepositRequest::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'amount' => $request->amount,
            'note' => $request->note,
        ]);

        // Gửi mail thông báo cho admin
        $this->sendDepositNotificationEmail($deposit, $user);

        return redirect()->route('client.account.wallet.deposit.success', $deposit->id);
    }

 
}

