<?php

namespace App\Http\Controllers\Client\Account;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Refund;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use App\Models\OrderStatusHistory;
use App\Models\OrderLog;



class RefundController extends Controller
{
    /**
     * Hiển thị form yêu cầu hoàn tiền (trả hàng)
     */
    public function showReturnForm(Request $request, Order $order)
    {
        $this->authorizeOwnership($request->user()->id, $order);

        // Chỉ cho phép khi đơn hàng đã giao thành công
        if ($order->order_status !== 'completed') {
            return back()->with('error', 'Chỉ có thể yêu cầu trả hàng hoàn tiền khi đơn hàng đã giao thành công.');
        }

        // Kiểm tra đã nhận hàng chưa
        if (!$order->delivered_at) {
            return back()->with('error', 'Đơn hàng chưa được xác nhận đã nhận hàng.');
        }

        // Kiểm tra trong vòng 7 ngày
        if ($order->isReturnExpired()) {
            $daysSinceDelivery = now()->diffInDays($order->delivered_at);
            return back()->with('error', "Đơn hàng đã quá hạn để yêu cầu trả hàng hoàn tiền. Thời gian cho phép là 7 ngày kể từ khi nhận hàng (đã qua {$daysSinceDelivery} ngày).");
        }

        // Kiểm tra xem đã có yêu cầu hoàn tiền chưa
        $existingRefund = Refund::where('order_id', $order->id)
            ->where('refund_type', 'return')
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingRefund) {
            return back()->with('error', 'Bạn đã có yêu cầu trả hàng hoàn tiền đang được xử lý.');
        }

        return view('client.account.orders.refund-return', compact('order'));
    }

    /**
     * Hiển thị form hủy đơn và hoàn tiền (cho thanh toán online)
     */
    public function showCancelRefundForm(Request $request, Order $order)
    {
        $this->authorizeOwnership($request->user()->id, $order);

        // Chỉ cho phép khi đơn hàng đang ở trạng thái có thể hủy và đã thanh toán online
        if (!in_array($order->order_status, ['pending', 'processing'])) {
            return back()->with('error', 'Chỉ có thể hủy đơn hàng khi đơn hàng đang chờ xác nhận hoặc đang chuẩn bị.');
        }

        // Chỉ cho phép với thanh toán online
        if (!in_array($order->payment_method, ['bank', 'momo'])) {
            return back()->with('error', 'Chức năng này chỉ áp dụng cho đơn hàng thanh toán online.');
        }

        // Kiểm tra xem đã có yêu cầu hoàn tiền chưa
        $existingRefund = Refund::where('order_id', $order->id)
            ->where('refund_type', 'cancel')
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingRefund) {
            return back()->with('error', 'Bạn đã có yêu cầu hủy đơn và hoàn tiền đang được xử lý.');
        }

        return view('client.account.orders.refund-cancel', compact('order'));
    }

    /**
     * Xử lý yêu cầu trả hàng hoàn tiền
     */
    public function submitReturnRefund(Request $request, Order $order)
    {
        $this->authorizeOwnership($request->user()->id, $order);

        if ($order->order_status !== 'completed') {
            return back()->with('error', 'Chỉ có thể yêu cầu trả hàng hoàn tiền khi đơn hàng đã giao thành công.');
        }

        $request->validate([
            'cancel_reason' => 'required|string|max:255',
            'reason_description' => 'nullable|string|max:1000',
            'bank_name' => 'required|string|max:255',
            'bank_account' => 'required|string|max:50',
            'account_holder' => 'required|string|max:255',
        ], [
            'cancel_reason.required' => 'Vui lòng chọn lý do trả hàng',
            'bank_name.required' => 'Vui lòng nhập tên ngân hàng',
            'bank_account.required' => 'Vui lòng nhập số tài khoản',
            'account_holder.required' => 'Vui lòng nhập tên chủ tài khoản',
        ]);

        DB::beginTransaction();
        try {
            $refund = Refund::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'refund_type' => 'return',
                'cancel_reason' => $request->cancel_reason,
                'reason_description' => $request->reason_description,
                'refund_amount' => $order->final_total,
                'bank_name' => $request->bank_name,
                'bank_account' => $request->bank_account,
                'account_holder' => $request->account_holder,
                'status' => 'pending',
            ]);

            // Cập nhật trạng thái đơn hàng
            $order->update([
                'order_status' => 'return_requested',
                'return_status' => 'requested',
                'return_reason' => $request->cancel_reason,
                'return_note' => $request->reason_description,
            ]);
            if (Schema::hasTable('order_status_history')) {
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'admin_id' => null,
                    'old_status' => 'completed',
                    'new_status' => 'return_requested',
                    'note' => 'Khách hàng yêu cầu trả hàng hoàn tiền',
                ]);
            }

            if (Schema::hasTable('order_logs')) {
                OrderLog::create([
                    'order_id' => $order->id,
                    'status' => 'return_requested',
                    'updated_by' => Auth::id(),
                    'role' => 'client',
                    'created_at' => now(),
                ]);
            }

            // Liên kết refund với transaction nếu có (cho đơn hàng đã thanh toán online)
            if (in_array($order->payment_method, ['bank', 'momo'])) {
                $transaction = Transaction::where('order_id', $order->id)
                    ->where('type', 'income')
                    ->where('status', 'completed')
                    ->first();
                if ($transaction) {
                    $transaction->update(['refund_id' => $refund->id]);
                }
            }

            // Gửi email thông báo
            $this->sendRefundNotificationEmail($refund, $order);

            DB::commit();

            return redirect()->route('client.account.orders.show', $order)
                ->with('success', 'Yêu cầu trả hàng hoàn tiền đã được gửi thành công. Chúng tôi sẽ xử lý trong thời gian sớm nhất.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Xử lý yêu cầu hủy đơn và hoàn tiền
     */
    public function submitCancelRefund(Request $request, Order $order)
    {
        $this->authorizeOwnership($request->user()->id, $order);

        if (!in_array($order->order_status, ['pending', 'processing'])) {
            return back()->with('error', 'Chỉ có thể hủy đơn hàng khi đơn hàng đang chờ xác nhận hoặc đang chuẩn bị.');
        }

        if (!in_array($order->payment_method, ['bank', 'momo'])) {
            return back()->with('error', 'Chức năng này chỉ áp dụng cho đơn hàng thanh toán online.');
        }

        $request->validate([
            'cancel_reason' => 'required|string|max:255',
            'reason_description' => 'nullable|string|max:1000',
            'bank_name' => 'required|string|max:255',
            'bank_account' => 'required|string|max:50',
            'account_holder' => 'required|string|max:255',
        ], [
            'cancel_reason.required' => 'Vui lòng chọn lý do hủy đơn',
            'bank_name.required' => 'Vui lòng nhập tên ngân hàng',
            'bank_account.required' => 'Vui lòng nhập số tài khoản',
            'account_holder.required' => 'Vui lòng nhập tên chủ tài khoản',
        ]);

        DB::beginTransaction();
        try {
            $refund = Refund::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'refund_type' => 'cancel',
                'cancel_reason' => $request->cancel_reason,
                'reason_description' => $request->reason_description,
                'refund_amount' => $order->final_total,
                'bank_name' => $request->bank_name,
                'bank_account' => $request->bank_account,
                'account_holder' => $request->account_holder,
                'status' => 'pending',
            ]);

            // Cập nhật trạng thái đơn hàng
            $order->update([
                'order_status' => 'cancelled',
                'cancel_reason' => $request->cancel_reason,
                'notes' => $request->reason_description,
                'cancelled_at' => now(),
            ]);

            // Liên kết refund với transaction nếu có (cho đơn hàng đã thanh toán online)
            $transaction = Transaction::where('order_id', $order->id)
                ->where('type', 'income')
                ->whereIn('status', ['pending', 'completed'])
                ->first();
            if ($transaction) {
                $transaction->update(['refund_id' => $refund->id]);
            }

            // Gửi email thông báo
            $this->sendRefundNotificationEmail($refund, $order);

            DB::commit();

            return redirect()->route('client.account.orders.show', $order)
                ->with('success', 'Yêu cầu hủy đơn và hoàn tiền đã được gửi thành công. Chúng tôi sẽ xử lý trong thời gian sớm nhất.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Khách hàng hủy yêu cầu hoàn tiền (Đặt lại)
     */
    public function resetCancelRefund(Request $request, Order $order)
    {
        $this->authorizeOwnership($request->user()->id, $order);

        $refund = Refund::where('order_id', $order->id)
            ->where('refund_type', 'cancel')
            ->where('status', 'pending')
            ->first();

        if (!$refund) {
            return back()->with('error', 'Không tìm thấy yêu cầu hoàn tiền đang chờ xử lý.');
        }

        DB::beginTransaction();
        try {
            $refund->update([
                'status' => 'rejected',
                'admin_note' => 'Khách hàng chọn Đặt lại, dừng hoàn tiền.',
            ]);

            $order->update([
                'order_status' => 'pending',
                'cancel_reason' => null,
                'notes' => null,
                'cancelled_at' => null,
            ]);

            Transaction::where('order_id', $order->id)
                ->where('refund_id', $refund->id)
                ->update(['refund_id' => null]);

            DB::commit();

            return back()->with('success', 'Đã hủy yêu cầu hoàn tiền. Đơn hàng sẽ được xử lý lại.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Gửi email thông báo yêu cầu hoàn tiền
     */
    private function sendRefundNotificationEmail(Refund $refund, Order $order)
    {
        try {
            $user = $order->user;
            $refundTypeLabel = $refund->refund_type === 'return' ? 'Trả hàng hoàn tiền' : 'Hủy đơn và hoàn tiền';

            Mail::send('emails.refund-request', [
                'refund' => $refund,
                'order' => $order,
                'user' => $user,
                'refundTypeLabel' => $refundTypeLabel,
            ], function ($message) use ($user, $order, $refundTypeLabel) {
                $message->to($user->email, $user->name)
                    ->subject("Yêu cầu {$refundTypeLabel} - Đơn hàng #{$order->order_code}");
            });
        } catch (\Exception $e) {
            // Log lỗi nhưng không làm gián đoạn quy trình
            Log::error('Lỗi gửi email hoàn tiền: ' . $e->getMessage());
        }
    }

    protected function authorizeOwnership(int $userId, Order $order): void
    {
        abort_if($order->user_id !== $userId, 403, 'Bạn không có quyền truy cập đơn hàng này.');
    }
}

