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
    /**
     * ========================================
     * XÁC NHẬN GIAO DỊCH (ĐÃ NHẬN TIỀN)
     * ========================================
     * Xác nhận giao dịch đã nhận tiền:
     * - Cập nhật số dư ví (cộng tiền nếu income, trừ tiền nếu expense)
     * - Cập nhật trạng thái giao dịch thành 'completed'
     * - Lưu lịch sử hành động vào TransactionLog
     * - Cập nhật trạng thái thanh toán đơn hàng thành 'paid'
     */
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

    /**
     * ========================================
     * HỦY GIAO DỊCH (CHƯA NHẬN TIỀN)
     * ========================================
     * Hủy giao dịch chưa hoàn thành:
     * - Cập nhật trạng thái giao dịch thành 'cancelled'
     * - Lưu lịch sử hành động vào TransactionLog
     * - Không được hủy giao dịch đã hoàn thành
     */
    public function cancelTransaction($transactionId)
    {
        $transaction = Transaction::findOrFail($transactionId);

        if ($transaction->status === 'completed') {
            return redirect()->back()
                ->with('error', 'Không thể hủy giao dịch đã hoàn thành.');
        }

        DB::beginTransaction();
        try {
            $oldStatus = $transaction->status;
            $transaction->update([
                'status' => 'cancelled',
                'processed_by' => Auth::id(),
            ]);

            TransactionLog::create([
                'transaction_id' => $transaction->id,
                'user_id' => Auth::id(),
                'action' => 'cancel',
                'description' => 'Hủy giao dịch',
                'old_data' => ['status' => $oldStatus],
                'new_data' => ['status' => 'cancelled', 'processed_by' => Auth::id()],
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Đã hủy giao dịch.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    // Hiển thị trang yêu cầu hoàn tiền
    public function showRefund($transactionId)
    {
        $transaction = Transaction::with(['order', 'refund.user', 'wallet', 'logs.user'])
            ->findOrFail($transactionId);

        if (!$transaction->refund) {
            return redirect()->back()
                ->with('error', 'Giao dịch này không có yêu cầu hoàn tiền.');
        }

        return view('admin.wallet.refund-detail', compact('transaction'));
    }
  }