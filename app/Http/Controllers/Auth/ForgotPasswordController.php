<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request)
    {

        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.exists' => 'Email không tồn tại trong hệ thống.',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->with('error', 'Tài khoản đã bị ẩn vui lòng liên hệ admin để được khắc phục.');
        }

        if ($user->status === 'inactive') {
            return back()->with('error', 'Tài khoản bị dừng hoạt động, không thể gửi mã.');
        }

        if ($user->status === 'hidden') {
            return back()->with('error', 'Tài khoản bị ẩn, không thể gửi mã.');
        }

         // Kiểm tra spam - chỉ cho phép gửi OTP mỗi 1 phút
        $lastSent = Cache::get('otp_sent_' . $request->email);
        if ($lastSent && now()->diffInSeconds($lastSent) < 60) {
            $remaining = 60 - now()->diffInSeconds($lastSent);
            return back()->with('error', "Vui lòng chờ {$remaining} giây trước khi gửi lại mã OTP.");
        }

        // Sinh mã OTP ngẫu nhiên 6 chữ số
        $otp = rand(100000, 999999);

        // Lưu tạm OTP vào cache với thời gian hết hạn 5 phút
        Cache::put('otp_' . $request->email, $otp, now()->addMinutes(5));
        
        // Lưu thời gian gửi OTP để tránh spam
        Cache::put('otp_sent_' . $request->email, now(), now()->addMinutes(1));

        // Gửi mail OTP
        try {
            $emailContent = "
                <h2>Mã xác nhận khôi phục mật khẩu</h2>
                <p>Xin chào <strong>{$user->name}</strong>,</p>
                <p>Bạn đã yêu cầu khôi phục mật khẩu cho tài khoản Meteor Shop.</p>
                <p><strong>Mã OTP của bạn là: <span style='color: #007bff; font-size: 24px; font-weight: bold;'>{$otp}</span></strong></p>
                <p>Mã này có hiệu lực trong <strong>5 phút</strong>.</p>
                <p>Nếu bạn không yêu cầu khôi phục mật khẩu, vui lòng bỏ qua email này.</p>
                <hr>
                <p><small>Trân trọng,<br>Đội ngũ Meteor Shop</small></p>
            ";

            Mail::html($emailContent, function ($message) use ($request, $user) {
                $message->to($request->email, $user->name)
                    ->subject('Mã xác nhận khôi phục mật khẩu - Meteor Shop');
            });

            // Lưu email vào session để sử dụng ở bước tiếp theo
            session(['reset_email' => $request->email]);
            
            return redirect()->route('password.verify-otp')->with('success', 'Mã xác nhận đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư.');
        } catch (\Exception $e) {
            // Xóa OTP nếu gửi mail thất bại
            Cache::forget('otp_' . $request->email);
            Cache::forget('otp_sent_' . $request->email);
            
            return back()->with('error', 'Không thể gửi email. Vui lòng thử lại sau hoặc liên hệ hỗ trợ.');
        }
    }
  };