<?php

namespace App\Http\Controllers\Admin\Wallet;

use App\Http\Controllers\Controller;
use App\Models\ClientWallet;
use App\Models\DepositRequest;
use App\Models\WithdrawRequest;
use App\Models\WalletTransaction;
use App\Models\WalletSetting;
use Illuminate\Http\Request;


class WalletController extends Controller
{
    /**
     * Trang chính - Hiển thị tabs
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'deposits');
        
        // Đếm số lượng pending cho badges
        $pendingDeposits = DepositRequest::where('status', 'pending')->count();
        $pendingWithdraws = WithdrawRequest::whereIn('status', ['pending', 'processing'])->count();
        
        $data = [
            'tab' => $tab,
            'pendingDeposits' => $pendingDeposits,
            'pendingWithdraws' => $pendingWithdraws,
        ];
        
        // Load data theo tab
        if ($tab === 'deposits') {
            $data['deposits'] = DepositRequest::with(['user', 'wallet'])
                ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } elseif ($tab === 'withdrawals') {
            $data['withdrawals'] = WithdrawRequest::with(['user', 'wallet'])
                ->orderByRaw("CASE WHEN status IN ('pending', 'processing') THEN 0 ELSE 1 END")
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            $data['transactions'] = WalletTransaction::with(['user', 'processedBy'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }
        
        return view('admin.wallet.index', $data);
    }

 
}

