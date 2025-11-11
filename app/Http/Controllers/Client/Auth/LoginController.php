<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showClientLoginForm()
    {
        return view('client.auth.login-client');
    }

    public function loginClient(Request $request)
    {
        

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

}


