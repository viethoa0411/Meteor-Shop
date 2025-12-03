<?php

namespace App\Http\Controllers\Client\Wallet;

use App\Http\Controllers\Controller;
use App\Models\ClientWallet;
use App\Models\WithdrawRequest;
use App\Models\WalletSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

 
class WithdrawController extends Controller
{
    /**
     * Trang rút tiền
     * - Kiểm tra ví có tiền không
     * - Form nhập thông tin ngân hàng + số tiền
     */
    public function index()
    {
        $user = Auth::user();
        $wallet = ClientWallet::getOrCreateForUser($user->id);
        
        // Kiểm tra ví có tiền không
        if ($wallet->balance <= 0) {
            return redirect()->route('client.account.wallet.index')
                ->with('error', 'Ví không có số dư, không thể rút tiền');
        }
        
        // Lấy các yêu cầu rút tiền đang chờ
        $pendingWithdraws = WithdrawRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'processing'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('client.wallet.withdraw', compact('wallet', 'pendingWithdraws'));
    }

    /**
     * Xử lý yêu cầu rút tiền
     * - Validate thông tin ngân hàng
     * - Validate số tiền không vượt quá số dư
     * - Tạo yêu cầu rút tiền
     * - Gửi mail thông báo cho admin
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $wallet = ClientWallet::getOrCreateForUser($user->id);
        
        $request->validate([
            'account_holder' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'amount' => 'required|numeric|min:10000|max:' . $wallet->balance,
            'phone' => 'required|string|max:20',
        ], [
            'account_holder.required' => 'Vui lòng nhập tên chủ tài khoản',
            'bank_name.required' => 'Vui lòng chọn ngân hàng',
            'account_number.required' => 'Vui lòng nhập số tài khoản',
            'amount.required' => 'Vui lòng nhập số tiền rút',
            'amount.min' => 'Số tiền rút tối thiểu là 10,000đ',
            'amount.max' => 'Số tiền rút không được vượt quá số dư trong ví',
            'phone.required' => 'Vui lòng nhập số điện thoại liên hệ',
        ]);
        
        // Tạo yêu cầu rút tiền
        $withdraw = WithdrawRequest::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'amount' => $request->amount,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_holder' => $request->account_holder,
            'phone' => $request->phone,
            'note' => $request->note,
        ]);
        
        // Gửi mail thông báo cho admin
        $this->sendWithdrawNotificationEmail($withdraw, $user);

        return redirect()->route('client.account.wallet.withdraw.success', $withdraw->id);
    }

    /**
     * Trang thông báo thành công
     */
    public function success($id)
    {
        $withdraw = WithdrawRequest::where('user_id', Auth::id())->findOrFail($id);
        $settings = WalletSetting::getSettings();
        
        return view('client.wallet.withdraw-success', compact('withdraw', 'settings'));
    }

    /**
     * Hủy yêu cầu rút tiền (chỉ khi đang pending)
     */
    public function cancel($id)
    {
        $withdraw = WithdrawRequest::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->findOrFail($id);
        
        $withdraw->update(['status' => 'cancelled']);
        
        return redirect()->route('client.account.wallet.index')
            ->with('success', 'Đã hủy yêu cầu rút tiền');
    }

    /**
     * Gửi email thông báo yêu cầu rút tiền cho admin
     */
    private function sendWithdrawNotificationEmail($withdraw, $user)
    {
        try {
            $adminEmail = env('MAIL_FROM_ADDRESS', 'admin@meteorshop.com');
            $formattedAmount = number_format($withdraw->amount, 0, ',', '.') . 'đ';

            $emailContent = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 20px; text-align: center;'>
                        <h1 style='margin: 0;'>💸 Yêu cầu rút tiền mới</h1>
                    </div>
                    <div style='padding: 30px; background: #f9f9f9;'>
                        <div style='background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
                            <h2 style='color: #333; border-bottom: 2px solid #f5576c; padding-bottom: 10px;'>
                                Thông tin yêu cầu
                            </h2>
                            <table style='width: 100%; border-collapse: collapse;'>
                                <tr>
                                    <td style='padding: 10px 0; color: #666;'>Mã yêu cầu:</td>
                                    <td style='padding: 10px 0; font-weight: bold; color: #333;'>{$withdraw->request_code}</td>
                                </tr>
                                <tr>
                                    <td style='padding: 10px 0; color: #666;'>Khách hàng:</td>
                                    <td style='padding: 10px 0; font-weight: bold; color: #333;'>{$user->name}</td>
                                </tr>
                                <tr>
                                    <td style='padding: 10px 0; color: #666;'>Email:</td>
                                    <td style='padding: 10px 0; color: #333;'>{$user->email}</td>
                                </tr>
                                <tr>
                                    <td style='padding: 10px 0; color: #666;'>Số điện thoại:</td>
                                    <td style='padding: 10px 0; color: #333;'>{$withdraw->phone}</td>
                                </tr>
                                <tr>
                                    <td style='padding: 10px 0; color: #666;'>Số tiền rút:</td>
                                    <td style='padding: 10px 0; font-weight: bold; font-size: 18px; color: #dc3545;'>{$formattedAmount}</td>
                                </tr>
                            </table>

                            <h3 style='color: #333; border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-top: 20px;'>
                                Thông tin ngân hàng
                            </h3>
                            <table style='width: 100%; border-collapse: collapse;'>
                                <tr>
                                    <td style='padding: 10px 0; color: #666;'>Ngân hàng:</td>
                                    <td style='padding: 10px 0; font-weight: bold; color: #333;'>{$withdraw->bank_name}</td>
                                </tr>
                                <tr>
                                    <td style='padding: 10px 0; color: #666;'>Số tài khoản:</td>
                                    <td style='padding: 10px 0; font-weight: bold; color: #333;'>{$withdraw->account_number}</td>
                                </tr>
                                <tr>
                                    <td style='padding: 10px 0; color: #666;'>Chủ tài khoản:</td>
                                    <td style='padding: 10px 0; font-weight: bold; color: #333;'>{$withdraw->account_holder}</td>
                                </tr>
                                <tr>
                                    <td style='padding: 10px 0; color: #666;'>Ghi chú:</td>
                                    <td style='padding: 10px 0; color: #333;'>" . ($withdraw->note ?? 'Không có') . "</td>
                                </tr>
                            </table>
                        </div>
                        <div style='text-align: center; margin-top: 20px;'>
                            <a href='" . route('admin.wallet.withdraw.detail', $withdraw->id) . "'
                               style='display: inline-block; background: #f5576c; color: white; padding: 12px 30px;
                                      text-decoration: none; border-radius: 5px; font-weight: bold;'>
                                Xem chi tiết & Xử lý
                            </a>
                        </div>
                    </div>
                    <div style='text-align: center; padding: 15px; color: #666; font-size: 12px;'>
                        <p>Email này được gửi tự động từ hệ thống Meteor Shop</p>
                    </div>
                </div>
            ";

            Mail::html($emailContent, function ($message) use ($adminEmail, $withdraw) {
                $message->to($adminEmail)
                    ->subject("💸 Yêu cầu rút tiền mới #{$withdraw->request_code} - Meteor Shop");
            });
        } catch (\Exception $e) {
            Log::error('Lỗi gửi email thông báo rút tiền: ' . $e->getMessage());
        }
    }
}

