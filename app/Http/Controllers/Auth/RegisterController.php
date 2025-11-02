<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        

        try {
            User::create([
                'name' => 'User', // Tên mặc định
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => '', // Địa chỉ trống
                'password' => Hash::make($request->password),
                'role' => 'user',
                'status' => 'active',
            ]);

            return redirect()->route('login')->with('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi đăng ký. Vui lòng thử lại.')->withInput();
        }
    }

}

