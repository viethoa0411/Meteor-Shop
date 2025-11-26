<?php

namespace App\Http\Controllers\Admin\Wallet;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionLog;
use App\Models\Wallet;
use App\Models\WalletWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WalletWithdrawController extends Controller
{
    /**
     * ========================================
     * RÚT TIỀN - HIỂN THỊ FORM
     * ========================================
     * Hiển thị form rút tiền từ ví
     */
    public function showWithdrawForm($id)
    {
        $wallet = Wallet::with('user')->findOrFail($id);

        if (Auth::user()->role !== 'admin' && $wallet->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền truy cập ví này.');
        }

        return view('admin.wallet.withdraw', compact('wallet'));
    }

}

