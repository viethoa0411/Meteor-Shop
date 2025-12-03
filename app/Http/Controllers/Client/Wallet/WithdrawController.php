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

     

     
}

