<?php

namespace App\Http\Controllers\Admin\Wallet;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Models\Transaction;
use App\Models\TransactionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletTransactionActionController extends Controller
{
    // Xác nhận giao dịch đã nhận tiền 
    public function confirmTransaction($transactionId)
    {
        $transaction = Transaction::with('wallet')->findOrFail($transactionId);

        if ($transaction->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Giao dịch này đã được xử lý.');
        }

        DB::beginTransaction();
        try {
            if ($transaction->type === 'income') {
                $transaction->wallet->addBalance($transaction->amount);
            } else {
                $transaction->wallet->subtractBalance($transaction->amount);
            }

            $transaction->update([
                'status' => 'completed',
                'completed_at' => now(),
                'processed_by' => Auth::id(),
            ]);

            TransactionLog::create([
                'transaction_id' => $transaction->id,
                'user_id' => Auth::id(),
                'action' => 'confirm',
                'description' => 'Xác nhận giao dịch',
                'old_data' => ['status' => 'pending'],
                'new_data' => ['status' => 'completed', 'processed_by' => Auth::id()],
            ]);

            if ($transaction->order) {
                $transaction->order->update(['payment_status' => 'paid']);
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Xác nhận giao dịch thành công! Số dư ví đã được cập nhật.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

}