<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Hiển thị trang đăng nhập
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Xử lý đăng nhập
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email không hợp lệ',
            'password.required' => 'Mật khẩu là bắt buộc',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
        ]);

        $credentials = $request->only('email', 'password');

        // Kiểm tra xem user có tồn tại không
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email không tồn tại trong hệ thống'])->withInput();
        }

        // Kiểm tra trạng thái user
        if ($user->status === 'banned') {
            return back()->withErrors(['email' => 'Tài khoản của bạn đã bị cấm'])->withInput();
        }

        if ($user->status === 'inactive') {
            return back()->withErrors(['email' => 'Tài khoản của bạn chưa được kích hoạt'])->withInput();
        }

        // Kiểm tra role - chỉ admin mới được đăng nhập
        if ($user->role !== 'admin') {
            return back()->withErrors(['email' => 'Bạn không có quyền truy cập vào trang quản trị'])->withInput();
        }

        // Thử đăng nhập
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard')->with('success', 'Đăng nhập thành công!');
        }

        return back()->withErrors(['password' => 'Mật khẩu không chính xác'])->withInput();
    }

    /**
     * Đăng xuất
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Đã đăng xuất thành công!');
    }
}

