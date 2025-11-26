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
    // Hiển thị form rút tiền
    public function showWithdrawForm($id)
    {
        $wallet = Wallet::with('user')->findOrFail($id);

        if (Auth::user()->role !== 'admin' && $wallet->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền truy cập ví này.');
        }

        return view('admin.wallet.withdraw', compact('wallet'));
    }
    // Xử lý rút tiền
    public function processWithdraw(Request $request, $id)
    {
        $wallet = Wallet::with('user')->findOrFail($id);

        if (Auth::user()->role !== 'admin' && $wallet->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền truy cập ví này.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'bank_name' => 'required|string|max:255',
            'bank_account' => 'required|string|max:255',
            'account_holder' => 'required|string|max:255',
            'note' => 'nullable|string|max:1000',
        ]);

        if ($request->amount > $wallet->balance) {
            return back()->withInput()->with('error', 'Số dư ví không đủ để rút tiền.');
        }

        DB::beginTransaction();
        try {
            if (!$wallet->subtractBalance($request->amount)) {
                throw new \Exception('Số dư ví không đủ để rút tiền.');
            }

            $withdraw = WalletWithdrawal::create([
                'wallet_id' => $wallet->id,
                'requested_by' => Auth::id(),
                'processed_by' => Auth::id(),
                'amount' => $request->amount,
                'bank_name' => $request->bank_name,
                'bank_account' => $request->bank_account,
                'account_holder' => $request->account_holder,
                'note' => $request->note,
                'status' => 'completed',
                'processed_at' => now(),
            ]);

            $expenseTransaction = Transaction::create([
                'wallet_id' => $wallet->id,
                'amount' => $request->amount,
                'type' => 'expense',
                'status' => 'completed',
                'payment_method' => 'withdraw',
                'transaction_code' => 'WITHDRAW_' . strtoupper(Str::random(8)),
                'description' => 'Rút tiền khỏi ví',
                'completed_at' => now(),
                'processed_by' => Auth::id(),
            ]);

            TransactionLog::create([
                'transaction_id' => $expenseTransaction->id,
                'user_id' => Auth::id(),
                'action' => 'withdraw',
                'description' => 'Rút tiền khỏi ví',
                'old_data' => null,
                'new_data' => ['amount' => $request->amount],
            ]);

            DB::commit();

            return redirect()
                ->route('admin.wallet.show', $wallet->id)
                ->with('success', 'Rút tiền thành công! Số dư ví đã được cập nhật.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    /**
     * ========================================
     * LỊCH SỬ RÚT TIỀN
     * ========================================
     * Hiển thị lịch sử rút tiền của ví với phân trang (7 bản ghi/trang)
     */
    public function withdrawHistory($id)
    {
        $wallet = Wallet::with('user')->findOrFail($id);

        if (Auth::user()->role !== 'admin' && $wallet->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền truy cập ví này.');
        }

        $withdrawals = WalletWithdrawal::with(['requester', 'processor'])
            ->where('wallet_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(7);

        return view('admin.wallet.withdraw-history', compact('wallet', 'withdrawals'));
    }

    /**
     * ========================================
     * ĐÁNH DẤU ĐÃ NHẬN TIỀN (CHƯA CHỐT)
     * ========================================
     * Đánh dấu giao dịch đã nhận tiền nhưng chưa chốt vào ví:
     * - Chỉ áp dụng cho giao dịch pending
     * - Chỉ áp dụng cho thanh toán online (bank, momo)
     * - Lưu thông tin người đánh dấu và thời gian
     * - Chuyển sang trang xác nhận để chốt sau
     */
    public function receivedTransaction($transactionId)
    {
        $transaction = Transaction::with(['wallet', 'order'])->findOrFail($transactionId);

        if ($transaction->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Giao dịch này đã được xử lý.');
        }

        if (!$transaction->order || !in_array($transaction->payment_method, ['bank', 'momo'])) {
            return redirect()->back()
                ->with('error', 'Chỉ áp dụng cho đơn hàng thanh toán online.');
        }

        if ($transaction->marked_as_received_at) {
            return redirect()->back()
                ->with('error', 'Giao dịch này đã được đánh dấu chờ chốt.');
        }

        DB::beginTransaction();
        try {
            $transaction->update([
                'marked_as_received_by' => Auth::id(),
                'marked_as_received_at' => now(),
            ]);

            TransactionLog::create([
                'transaction_id' => $transaction->id,
                'user_id' => Auth::id(),
                'action' => 'mark_received',
                'description' => 'Đánh dấu giao dịch đã nhận - chờ chốt',
                'old_data' => null,
                'new_data' => ['marked_as_received_by' => Auth::id()],
            ]);

            DB::commit();

            return redirect()->route('admin.wallet.receive.confirmations', $transaction->wallet_id)
                ->with('success', 'Đã đánh dấu giao dịch chờ chốt. Vui lòng chốt ở trang xác nhận.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

}

