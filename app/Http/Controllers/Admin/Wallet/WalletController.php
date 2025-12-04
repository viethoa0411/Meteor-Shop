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

    /**
     * Chi tiết yêu cầu nạp tiền
     */
    public function depositDetail($id)
    {
        $deposit = DepositRequest::with(['user', 'wallet'])->findOrFail($id);
        $wallet = $deposit->wallet;
        
        // Lịch sử giao dịch của user này
        $transactions = WalletTransaction::where('wallet_id', $wallet->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('admin.wallet.deposit-detail', compact('deposit', 'wallet', 'transactions'));
    }

    /**
     * Xác nhận nạp tiền
     */
    public function confirmDeposit(Request $request, $id)
    {
        $request->validate([
            'confirmed_amount' => 'required|numeric|min:1000',
        ]);

        $deposit = DepositRequest::with('wallet')->findOrFail($id);
        
        if (!$deposit->isPending()) {
            return back()->with('error', 'Yêu cầu này đã được xử lý');
        }

        $wallet = $deposit->wallet;
        $amount = $request->confirmed_amount;
        $balanceBefore = $wallet->balance;
        
        // Cộng tiền vào ví
        $wallet->addBalance($amount);
        
        // Cập nhật deposit request
        $deposit->update([
            'confirmed_amount' => $amount,
            'status' => 'confirmed',
            'admin_note' => $request->admin_note,
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);
        
        // Tạo transaction log
        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'user_id' => $deposit->user_id,
            'type' => 'deposit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $wallet->balance,
            'description' => 'Nạp tiền - ' . $deposit->request_code,
            'deposit_request_id' => $deposit->id,
            'processed_by' => auth()->id(),
        ]);
        
        return redirect()->route('admin.wallet.index', ['tab' => 'deposits'])
            ->with('success', 'Đã xác nhận nạp ' . number_format($amount) . 'đ cho ' . $deposit->user->name);
    }

    /**
     * Từ chối nạp tiền
     */
    public function rejectDeposit(Request $request, $id)
    {
        $deposit = DepositRequest::findOrFail($id);
        
        if (!$deposit->isPending()) {
            return back()->with('error', 'Yêu cầu này đã được xử lý');
        }

        $deposit->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note,
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);
        
        return redirect()->route('admin.wallet.index', ['tab' => 'deposits'])
            ->with('success', 'Đã từ chối yêu cầu nạp tiền');
    }
}

