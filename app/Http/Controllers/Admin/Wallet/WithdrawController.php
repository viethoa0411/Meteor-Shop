<?php

namespace App\Http\Controllers\Admin\Wallet;

use App\Http\Controllers\Controller;
use App\Models\WithdrawRequest;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

 
class WithdrawController extends Controller
{
    /**
     * Chi tiết yêu cầu rút tiền
     */
    public function detail($id)
    {
        $withdraw = WithdrawRequest::with(['user', 'wallet'])->findOrFail($id);
        $wallet = $withdraw->wallet;
        
        // Lịch sử giao dịch của user này
        $transactions = WalletTransaction::where('wallet_id', $wallet->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('admin.wallet.withdraw-detail', compact('withdraw', 'wallet', 'transactions'));
    }

    /**
     * Xác nhận rút tiền
     */
    public function confirm(Request $request, $id)
    {
        $request->validate([
            'confirmed_amount' => 'required|numeric|min:1000',
        ]);

        $withdraw = WithdrawRequest::with('wallet')->findOrFail($id);
        
        if (!$withdraw->isPending() && $withdraw->status !== 'processing') {
            return back()->with('error', 'Yêu cầu này đã được xử lý');
        }

        $wallet = $withdraw->wallet;
        $amount = $request->confirmed_amount;
        
        // Kiểm tra số dư
        if (!$wallet->hasEnoughBalance($amount)) {
            return back()->with('error', 'Số dư ví không đủ để rút ' . number_format($amount) . 'đ');
        }

        $balanceBefore = $wallet->balance;
        
        // Trừ tiền từ ví
        $wallet->subtractBalance($amount);
        
        // Cập nhật withdraw request
        $withdraw->update([
            'confirmed_amount' => $amount,
            'status' => 'completed',
            'admin_note' => $request->admin_note,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);
        
        // Tạo transaction log
        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'user_id' => $withdraw->user_id,
            'type' => 'withdraw',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $wallet->balance,
            'description' => 'Rút tiền - ' . $withdraw->request_code . ' - ' . $withdraw->bank_name,
            'withdraw_request_id' => $withdraw->id,
            'processed_by' => auth()->id(),
        ]);
        
        return redirect()->route('admin.wallet.index', ['tab' => 'withdrawals'])
            ->with('success', 'Đã xác nhận rút ' . number_format($amount) . 'đ cho ' . $withdraw->user->name);
    }

    /**
     * Từ chối rút tiền
     */
    public function reject(Request $request, $id)
    {
        $withdraw = WithdrawRequest::findOrFail($id);
        
        if (!$withdraw->isPending() && $withdraw->status !== 'processing') {
            return back()->with('error', 'Yêu cầu này đã được xử lý');
        }

        $withdraw->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);
        
        return redirect()->route('admin.wallet.index', ['tab' => 'withdrawals'])
            ->with('success', 'Đã từ chối yêu cầu rút tiền');
    }

    /**
     * Đánh dấu đang xử lý
     */
    public function markProcessing($id)
    {
        $withdraw = WithdrawRequest::findOrFail($id);
        
        if (!$withdraw->isPending()) {
            return back()->with('error', 'Yêu cầu này không ở trạng thái chờ xử lý');
        }

        $withdraw->update([
            'status' => 'processing',
            'processed_by' => auth()->id(),
        ]);
        
        return back()->with('success', 'Đã đánh dấu đang xử lý');
    }
}

