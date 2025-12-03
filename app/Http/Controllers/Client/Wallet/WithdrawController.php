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

 
}

