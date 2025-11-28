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

    /**
     * ========================================
     * HIỂN THỊ CHI TIẾT YÊU CẦU HOÀN TIỀN
     * ========================================
     * Hiển thị chi tiết yêu cầu hoàn tiền của giao dịch
     */
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

    /**
     * ========================================
     * XÁC NHẬN HOÀN TIỀN
     * ========================================
     * Xác nhận hoàn tiền cho khách hàng:
     * - Tạo giao dịch chi (expense) mới với số tiền hoàn
     * - Trừ số dư ví
     * - Cập nhật trạng thái giao dịch gốc thành 'cancelled'
     * - Cập nhật trạng thái yêu cầu hoàn tiền thành 'completed'
     * - Cập nhật thời gian hoàn tiền cho đơn hàng
     * - Lưu lịch sử hành động
     */
    public function confirmRefund($transactionId)
    {
        $transaction = Transaction::with(['wallet', 'refund', 'order'])->findOrFail($transactionId);

        if (!$transaction->refund) {
            return redirect()->back()
                ->with('error', 'Giao dịch này không có yêu cầu hoàn tiền.');
        }

        if ($transaction->refund->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Yêu cầu hoàn tiền này đã được xử lý.');
        }

        DB::beginTransaction();
        try {
            $refundTransaction = Transaction::create([
                'order_id' => $transaction->order_id,
                'wallet_id' => $transaction->wallet_id,
                'refund_id' => $transaction->refund_id,
                'amount' => $transaction->refund->refund_amount,
                'type' => 'expense',
                'status' => 'completed',
                'payment_method' => $transaction->payment_method,
                'transaction_code' => 'REFUND_' . $transaction->transaction_code,
                'description' => 'Hoàn tiền cho đơn hàng #' . ($transaction->order->order_code ?? 'N/A'),
                'completed_at' => now(),
                'processed_by' => Auth::id(),
            ]);

            $transaction->wallet->subtractBalance($transaction->refund->refund_amount);

            if ($transaction->status === 'pending') {
                $transaction->update([
                    'status' => 'cancelled',
                    'processed_by' => Auth::id(),
                ]);
            }

            $transaction->refund->update([
                'status' => 'completed',
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);

            if ($transaction->order) {
                $transaction->order->update([
                    'refunded_at' => now(),
                ]);
            }

            TransactionLog::create([
                'transaction_id' => $transaction->id,
                'user_id' => Auth::id(),
                'action' => 'refund',
                'description' => 'Hoàn tiền - Tạo giao dịch chi mới',
                'old_data' => ['status' => $transaction->status, 'type' => $transaction->type],
                'new_data' => ['status' => 'cancelled', 'refund_transaction_id' => $refundTransaction->id],
            ]);

            TransactionLog::create([
                'transaction_id' => $refundTransaction->id,
                'user_id' => Auth::id(),
                'action' => 'refund',
                'description' => 'Giao dịch hoàn tiền được tạo',
                'old_data' => null,
                'new_data' => ['type' => 'expense', 'status' => 'completed', 'amount' => $refundTransaction->amount],
            ]);

            DB::commit();

            return redirect()->route('admin.wallet.show', $transaction->wallet_id)
                ->with('success', 'Hoàn tiền thành công! Số dư ví đã được cập nhật.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * ========================================
     * HIỂN THỊ TRANG CHƯA NHẬN TIỀN
     * ========================================
     * Hiển thị chi tiết giao dịch chưa nhận tiền (thanh toán online)
     * Chỉ áp dụng cho:
     * - Giao dịch có trạng thái 'pending'
     * - Đơn hàng thanh toán qua bank hoặc momo
     */
    public function showNotReceived($transactionId)
    {
        $transaction = Transaction::with(['order', 'order.items', 'wallet'])->findOrFail($transactionId);

        if ($transaction->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Giao dịch này đã được xử lý.');
        }

        if (!$transaction->order || !in_array($transaction->payment_method, ['bank', 'momo'])) {
            return redirect()->back()
                ->with('error', 'Chỉ áp dụng cho đơn hàng thanh toán online.');
        }

        return view('admin.wallet.not-received', compact('transaction'));
    }

    /**
     * ========================================
     * HỦY ĐƠN HÀNG TỪ TRANG CHƯA NHẬN TIỀN
     * ========================================
     * Hủy đơn hàng và giao dịch khi chưa nhận được tiền:
     * - Cập nhật trạng thái giao dịch thành 'cancelled'
     * - Cập nhật trạng thái đơn hàng thành 'cancelled'
     * - Lưu lịch sử hành động
     */
    public function cancelOrderFromTransaction($transactionId)
    {
        $transaction = Transaction::with(['order', 'wallet'])->findOrFail($transactionId);

        if ($transaction->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Giao dịch này đã được xử lý.');
        }

        if (!$transaction->order || !in_array($transaction->payment_method, ['bank', 'momo'])) {
            return redirect()->back()
                ->with('error', 'Chỉ áp dụng cho đơn hàng thanh toán online.');
        }

        DB::beginTransaction();
        try {
            $transaction->update([
                'status' => 'cancelled',
                'processed_by' => Auth::id(),
            ]);

            $transaction->order->update([
                'order_status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            TransactionLog::create([
                'transaction_id' => $transaction->id,
                'user_id' => Auth::id(),
                'action' => 'cancel',
                'description' => 'Hủy đơn hàng từ trang Chưa Nhận',
                'old_data' => ['status' => 'pending', 'order_status' => $transaction->order->order_status],
                'new_data' => ['status' => 'cancelled', 'order_status' => 'cancelled', 'processed_by' => Auth::id()],
            ]);

            DB::commit();

            return redirect()->route('admin.wallet.show', $transaction->wallet_id)
                ->with('success', 'Đã hủy đơn hàng và giao dịch thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * ========================================
     * CHI TIẾT MÃ GIAO DỊCH + LỊCH SỬ HÀNH ĐỘNG
     * ========================================
     * Hiển thị chi tiết giao dịch bao gồm:
     * - Thông tin giao dịch
     * - Thông tin đơn hàng
     * - Thông tin ví
     * - Lịch sử hành động (logs)
     * - Người xử lý (processor)
     */
    public function showTransactionDetails($transactionId)
    {
        $transaction = Transaction::with(['order', 'wallet', 'logs.user', 'processor'])->findOrFail($transactionId);

        return view('admin.wallet.transaction-details', compact('transaction'));
    }

    /**
     * ========================================
     * HIỂN THỊ FORM HOÀN TIỀN
     * ========================================
     * Hiển thị form hoàn tiền cho giao dịch
     * Kiểm tra xem đơn hàng có yêu cầu hoàn tiền chưa
     */
    public function showRefundForm($transactionId)
    {
        $transaction = Transaction::with(['order', 'wallet', 'order.refunds'])->findOrFail($transactionId);

        if (!$transaction->order) {
            return redirect()->back()
                ->with('error', 'Giao dịch này không liên quan đến đơn hàng.');
        }

        $refundRequest = $transaction->order->refunds()
            ->where('refund_type', 'cancel')
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        return view('admin.wallet.refund-form', compact('transaction', 'refundRequest'));
    }

    /**
     * ========================================
     * XỬ LÝ HOÀN TIỀN + VALIDATE
     * ========================================
     * Xử lý hoàn tiền cho đơn hàng đã hủy:
     *
     * Validate:
     * - refund_amount: bắt buộc, phải là số, tối thiểu 0, tối đa bằng số tiền giao dịch
     * - bank_name: bắt buộc, tối đa 255 ký tự
     * - bank_account: bắt buộc, tối đa 255 ký tự
     * - account_holder: bắt buộc, tối đa 255 ký tự
     *
     * Quy trình:
     * - Kiểm tra đơn hàng đã bị hủy chưa
     * - Kiểm tra đơn hàng đã được hoàn tiền chưa
     * - Tạo hoặc cập nhật yêu cầu hoàn tiền
     * - Nếu giao dịch đã completed: tạo giao dịch chi mới và trừ số dư ví
     * - Cập nhật trạng thái giao dịch gốc thành 'cancelled'
     * - Cập nhật trạng thái thanh toán đơn hàng thành 'refunded'
     * - Lưu lịch sử hành động
     */
    public function processRefund(Request $request, $transactionId)
    {
        $transaction = Transaction::with(['order', 'wallet'])->findOrFail($transactionId);

        if (!$transaction->order) {
            return redirect()->back()
                ->with('error', 'Giao dịch này không liên quan đến đơn hàng.');
        }

        if ($transaction->order->order_status !== 'cancelled') {
            return redirect()->back()
                ->with('error', 'Chỉ có thể hoàn tiền cho đơn hàng đã bị hủy.');
        }

        $completedRefund = Refund::where('order_id', $transaction->order->id)
            ->where('refund_type', 'cancel')
            ->where('status', 'completed')
            ->first();

        if ($completedRefund) {
            return redirect()->back()
                ->with('error', 'Đơn hàng này đã được hoàn tiền rồi.');
        }

        $request->validate([
            'refund_amount' => 'required|numeric|min:0|max:' . $transaction->amount,
            'bank_name' => 'required|string|max:255',
            'bank_account' => 'required|string|max:255',
            'account_holder' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $existingRefund = Refund::where('order_id', $transaction->order->id)
                ->where('refund_type', 'cancel')
                ->whereIn('status', ['pending', 'approved'])
                ->first();

            if ($existingRefund) {
                $refund = $existingRefund;
                $refund->update([
                    'refund_amount' => $request->refund_amount,
                    'bank_name' => $request->bank_name,
                    'bank_account' => $request->bank_account,
                    'account_holder' => $request->account_holder,
                    'status' => 'completed',
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                ]);
            } else {
                $refund = Refund::create([
                    'order_id' => $transaction->order->id,
                    'user_id' => $transaction->order->user_id,
                    'refund_type' => 'cancel',
                    'refund_amount' => $request->refund_amount,
                    'bank_name' => $request->bank_name,
                    'bank_account' => $request->bank_account,
                    'account_holder' => $request->account_holder,
                    'status' => 'completed',
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                ]);
            }

            if ($transaction->status === 'completed') {
                $refundTransaction = Transaction::create([
                    'order_id' => $transaction->order_id,
                    'wallet_id' => $transaction->wallet_id,
                    'refund_id' => $refund->id,
                    'amount' => $request->refund_amount,
                    'type' => 'expense',
                    'status' => 'completed',
                    'payment_method' => $transaction->payment_method,
                    'transaction_code' => 'REFUND_' . $transaction->transaction_code . '_' . time(),
                    'description' => 'Hoàn tiền cho đơn hàng #' . ($transaction->order->order_code ?? 'N/A'),
                    'completed_at' => now(),
                    'processed_by' => Auth::id(),
                ]);

                if (!$transaction->wallet->subtractBalance($request->refund_amount)) {
                    throw new \Exception('Số dư ví không đủ để hoàn tiền.');
                }

                TransactionLog::create([
                    'transaction_id' => $refundTransaction->id,
                    'user_id' => Auth::id(),
                    'action' => 'refund',
                    'description' => 'Giao dịch hoàn tiền được tạo - Số tiền: ' . number_format($request->refund_amount, 0, ',', '.') . ' VNĐ',
                    'old_data' => null,
                    'new_data' => ['type' => 'expense', 'status' => 'completed', 'amount' => $request->refund_amount],
                ]);
            }

            $transaction->update([
                'refund_id' => $refund->id,
                'status' => 'cancelled',
                'processed_by' => Auth::id(),
            ]);

            $transaction->order->update([
                'payment_status' => 'refunded',
                'refunded_at' => now(),
            ]);

            TransactionLog::create([
                'transaction_id' => $transaction->id,
                'user_id' => Auth::id(),
                'action' => 'refund',
                'description' => 'Hoàn tiền thành công - Số tiền: ' . number_format($request->refund_amount, 0, ',', '.') . ' VNĐ',
                'old_data' => ['status' => $transaction->status, 'order_status' => $transaction->order->order_status],
                'new_data' => ['status' => 'cancelled', 'order_status' => 'cancelled', 'refund_id' => $refund->id],
            ]);

            DB::commit();

            return redirect()->route('admin.wallet.show', $transaction->wallet_id)
                ->with('success', 'Hoàn tiền thành công! Đơn hàng đã được hủy và thông báo đã gửi đến khách hàng.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}

