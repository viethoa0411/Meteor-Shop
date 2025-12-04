<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Hiển thị trang đăng nhập
     */
    public function showLoginFormAdmin()
    {
        return view('auth.login');
    }

    /**
     * Xử lý đăng nhập
     */
    public function login(Request $request)
    {
        // Xác thực dữ liệu đầu vào
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

        // Kiểm tra trạng thái tài khoản
        if ($user->status === 'banned') {
            return back()->withErrors(['email' => 'Tài khoản của bạn đã bị cấm'])->withInput();
        }

        if ($user->status === 'inactive') {
            return back()->withErrors(['email' => 'Tài khoản của bạn chưa được kích hoạt'])->withInput();
        }

        // Kiểm tra role - chỉ admin mới được đăng nhập vào trang quản trị
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


    public function showLoginFormClient()
    {
        return view('auth.loginclient');
    }

    public function loginClient(Request $request)
    {
        //  Validate dữ liệu đầu vào
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'exists:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'max:64',
            ],
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'email.exists' => 'Email này chưa được đăng ký trong hệ thống.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất :min ký tự.',
            'password.max' => 'Mật khẩu không được vượt quá :max ký tự.',
        ]);

        $remember = $request->boolean('remember', false);

        //  Lấy thông tin người dùng theo email
        $user = DB::table('users')->where('email', $validated['email'])->first();

        //  Nếu người dùng tồn tại nhưng mật khẩu sai
        if ($user && !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Mật khẩu không chính xác. Vui lòng thử lại.',
            ]);
        }

        //  Nếu email và mật khẩu đúng
        if (Auth::attempt($validated, $remember)) {
            $request->session()->regenerate();

            // Xóa phiên đăng nhập admin (nếu có)
            if (config('auth.guards.admin')) {
                if (Auth::guard('admin')->check()) {
                    Auth::guard('admin')->logout();
                }
                $request->session()->forget('admin_auth');
            }

            return redirect()
                ->intended(route('client.home'))
                ->with('success', 'Đăng nhập thành công.');
        }

        //  Nếu tất cả đều sai (phòng trường hợp không khớp gì cả)
        throw ValidationException::withMessages([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ]);
    }
    public function logoutClient(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('client.home')->with('success', 'Bạn đã đăng xuất.');
    }
}
