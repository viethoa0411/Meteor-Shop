<?php

namespace App\Http\Controllers\Client\Wallet;

use App\Http\Controllers\Controller;
use App\Models\ClientWallet;
use App\Models\DepositRequest;
use App\Models\WalletSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

 
class DepositController extends Controller
{
    /**
     * Trang nạp tiền
     * - Hiển thị QR code
     * - Hiển thị thông tin ngân hàng
     * - Form nhập số tiền nạp
     */
    public function index()
    {
        $user = Auth::user();
        $wallet = ClientWallet::getOrCreateForUser($user->id);
        $settings = WalletSetting::getSettings();
        
        // Lấy các yêu cầu nạp tiền đang chờ
        $pendingDeposits = DepositRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('client.wallet.deposit', compact('wallet', 'settings', 'pendingDeposits'));
    }

    /**
     * Xử lý yêu cầu nạp tiền
     * - Validate số tiền
     * - Tạo yêu cầu nạp tiền
     * - Gửi mail thông báo cho admin
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000|max:999999999999', // Tối thiểu 10,000đ, tối đa 999 tỷ
        ], [
            'amount.required' => 'Vui lòng nhập số tiền nạp',
            'amount.numeric' => 'Số tiền phải là số',
            'amount.min' => 'Số tiền nạp tối thiểu là 10,000đ',
            'amount.max' => 'Số tiền nạp tối đa là 999,999,999,999đ',
        ]);

        $user = Auth::user();
        $wallet = ClientWallet::getOrCreateForUser($user->id);
        
        // Tạo yêu cầu nạp tiền
        $deposit = DepositRequest::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'amount' => $request->amount,
            'note' => $request->note,
        ]);

        // Gửi mail thông báo cho admin
        $this->sendDepositNotificationEmail($deposit, $user);

        return redirect()->route('client.account.wallet.deposit.success', $deposit->id);
    }

    /**
     * Trang thông báo thành công
     * - Hiển thị thông báo chờ xác nhận
     * - Hiển thị số điện thoại liên hệ
     */
    public function success($id)
    {
        $deposit = DepositRequest::where('user_id', Auth::id())->findOrFail($id);
        $settings = WalletSetting::getSettings();
        
        return view('client.wallet.deposit-success', compact('deposit', 'settings'));
    }

    /**
     * Hủy yêu cầu nạp tiền (chỉ khi đang pending)
     */
    public function cancel($id)
    {
        $deposit = DepositRequest::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->findOrFail($id);
        
        $deposit->update(['status' => 'cancelled']);
        
        return redirect()->route('client.account.wallet.index')
            ->with('success', 'Đã hủy yêu cầu nạp tiền');
    }

    /**
     * Gửi email thông báo yêu cầu nạp tiền cho admin
     */
    private function sendDepositNotificationEmail($deposit, $user)
    {
        try {
            $adminEmail = env('MAIL_FROM_ADDRESS', 'admin@meteorshop.com');
            $formattedAmount = number_format($deposit->amount, 0, ',', '.') . 'đ';

            $emailContent = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center;'>
                        <h1 style='margin: 0;'>💰 Yêu cầu nạp tiền mới</h1>
                    </div>
                    <div style='padding: 30px; background: #f9f9f9;'>
                        <div style='background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
                            <h2 style='color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px;'>
                                Thông tin yêu cầu
                            </h2>
                            <table style='width: 100%; border-collapse: collapse;'>
                                <tr>
                                    <td style='padding: 10px 0; color: #666;'>Mã yêu cầu:</td>
                                    <td style='padding: 10px 0; font-weight: bold; color: #333;'>{$deposit->request_code}</td>
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
                                    <td style='padding: 10px 0; color: #666;'>Số tiền:</td>
                                    <td style='padding: 10px 0; font-weight: bold; font-size: 18px; color: #28a745;'>{$formattedAmount}</td>
                                </tr>
                                <tr>
                                    <td style='padding: 10px 0; color: #666;'>Ghi chú:</td>
                                    <td style='padding: 10px 0; color: #333;'>" . ($deposit->note ?? 'Không có') . "</td>
                                </tr>
                                <tr>
                                    <td style='padding: 10px 0; color: #666;'>Thời gian:</td>
                                    <td style='padding: 10px 0; color: #333;'>{$deposit->created_at->format('d/m/Y H:i:s')}</td>
                                </tr>
                            </table>
                        </div>
                        <div style='text-align: center; margin-top: 20px;'>
                            <a href='" . route('admin.wallet.deposit.detail', $deposit->id) . "'
                               style='display: inline-block; background: #667eea; color: white; padding: 12px 30px;
                                      text-decoration: none; border-radius: 5px; font-weight: bold;'>
                                Xem chi tiết & Xác nhận
                            </a>
                        </div>
                    </div>
                    <div style='text-align: center; padding: 15px; color: #666; font-size: 12px;'>
                        <p>Email này được gửi tự động từ hệ thống Meteor Shop</p>
                    </div>
                </div>
            ";

            Mail::html($emailContent, function ($message) use ($adminEmail, $deposit) {
                $message->to($adminEmail)
                    ->subject("💰 Yêu cầu nạp tiền mới #{$deposit->request_code} - Meteor Shop");
            });
        } catch (\Exception $e) {
            Log::error('Lỗi gửi email thông báo nạp tiền: ' . $e->getMessage());
        }
    }
}

