<?php

namespace App\Http\Controllers\Client\Wallet;

use App\Http\Controllers\Controller;
use App\Models\ClientWallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

 
class WalletController extends Controller
{
    /**
     * Trang chính của ví
     * - Hiển thị số dư
     * - Nút: Nạp tiền, Rút tiền, Lịch sử
     */
    public function index()
    {
        $user = Auth::user();
        $wallet = ClientWallet::getOrCreateForUser($user->id);
        
        // Lấy 5 giao dịch gần nhất
        $recentTransactions = WalletTransaction::where('wallet_id', $wallet->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('client.wallet.index', compact('wallet', 'recentTransactions'));
    }
 
}

