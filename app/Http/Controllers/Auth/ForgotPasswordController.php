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
     public function showVerifyOtpForm()
    {
        // Kiểm tra xem có email trong session không
        if (!session('reset_email')) {
            return redirect()->route('password.request')->with('error', 'Vui lòng nhập email trước.');
        }
        
        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|numeric|digits:6',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.exists' => 'Email không tồn tại trong hệ thống.',
            'otp.required' => 'Vui lòng nhập mã OTP.',
            'otp.numeric' => 'Mã OTP phải là số.',
            'otp.digits' => 'Mã OTP phải có đúng 6 chữ số.',
        ]);

        // Kiểm tra OTP
        $cachedOtp = Cache::get('otp_' . $request->email);
        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return back()->with('error', 'Mã OTP không chính xác hoặc đã hết hạn.')->withInput();
        }

        // Kiểm tra user có tồn tại và đang active
        $user = \App\Models\User::where('email', $request->email)->first();
        if (!$user || $user->status !== 'active') {
            return back()->with('error', 'Tài khoản không tồn tại hoặc đã bị khóa.')->withInput();
        }

        // Lưu email vào session để sử dụng ở bước reset password
        session(['reset_email' => $request->email, 'otp_verified' => true]);
        
        return redirect()->route('password.reset')->with('success', 'Xác nhận OTP thành công! Vui lòng nhập mật khẩu mới.');
    }

    public function showResetForm()
    {
        // Kiểm tra xem có email và OTP đã được xác nhận không
        if (!session('reset_email') || !session('otp_verified')) {
            return redirect()->route('password.request')->with('error', 'Vui lòng xác nhận OTP trước.');
        }
        
        return view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        // Kiểm tra xem OTP đã được xác nhận chưa
        if (!session('otp_verified') || !session('reset_email')) {
            return redirect()->route('password.request')->with('error', 'Vui lòng xác nhận OTP trước.');
        }
        
        $request->validate([
            'password' => 'required|confirmed|min:6',
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);
        

        $email = session('reset_email');

        // Kiểm tra user có tồn tại và đang active
        $user = \App\Models\User::where('email', $email)->first();
        if (!$user || $user->status !== 'active') {
            return back()->with('error', 'Tài khoản không tồn tại hoặc đã bị khóa.')->withInput();
        }

        try {
            // Cập nhật mật khẩu
            $user->password = bcrypt($request->password);
            $user->save();

            // Xóa OTP và thông tin liên quan
            Cache::forget('otp_' . $email);
            Cache::forget('otp_sent_' . $email);
            
            // Xóa session
            session()->forget(['reset_email', 'otp_verified']);

            return redirect()->route('login')->with('success', 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập với mật khẩu mới.');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi đặt lại mật khẩu. Vui lòng thử lại.')->withInput();
        }
    }
    
  };