<?php

namespace App\Http\Controllers\Client\Wallet;

use App\Http\Controllers\Controller;
use App\Models\ClientWallet;
use App\Models\WithdrawRequest;
use App\Models\WalletSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

 
class WithdrawController extends Controller
{
    /**
     * Trang rút tiền
     * - Kiểm tra ví có tiền không
     * - Form nhập thông tin ngân hàng + số tiền
     */
    public function index()
    {
        $user = Auth::user();
        $wallet = ClientWallet::getOrCreateForUser($user->id);
        
        // Kiểm tra ví có tiền không
        if ($wallet->balance <= 0) {
            return redirect()->route('client.account.wallet.index')
                ->with('error', 'Ví không có số dư, không thể rút tiền');
        }
        
        // Lấy các yêu cầu rút tiền đang chờ
        $pendingWithdraws = WithdrawRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'processing'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('client.wallet.withdraw', compact('wallet', 'pendingWithdraws'));
    }

    /**
     * Xử lý yêu cầu rút tiền
     * - Validate thông tin ngân hàng
     * - Validate số tiền không vượt quá số dư
     * - Tạo yêu cầu rút tiền
     * - Gửi mail thông báo cho admin
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $wallet = ClientWallet::getOrCreateForUser($user->id);
        
        
        
        // Tạo yêu cầu rút tiền
        $withdraw = WithdrawRequest::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'amount' => $request->amount,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_holder' => $request->account_holder,
            'phone' => $request->phone,
            'note' => $request->note,
        ]);
        
        // Gửi mail thông báo cho admin
        $this->sendWithdrawNotificationEmail($withdraw, $user);

        return redirect()->route('client.account.wallet.withdraw.success', $withdraw->id);
    }
 
}

