<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:15|unique:users,phone',
            'password' => 'required|min:6|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập tên người dùng.',
            'name.string' => 'Tên người dùng không hợp lệ.',
            'name.max' => 'Tên người dùng không được vượt quá 100 ký tự.',

            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng.',

            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.max' => 'Số điện thoại không được vượt quá 15 ký tự.',

            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => '',
                'password' => Hash::make($request->password),
                'role' => 'user',
                'status' => 'active',
            ]);

            // Tạo thông báo cho admin về khách hàng mới
            try {
                NotificationService::createForAdmins([
                    'type' => 'user',
                    'level' => 'info',
                    'title' => 'Khách hàng mới',
                    'message' => $user->name . ' vừa đăng ký tài khoản',
                    'url' => route('admin.account.users.show', $user->id) ?? route('admin.account.users.index'),
                    'metadata' => ['user_id' => $user->id, 'email' => $user->email]
                ]);
            } catch (\Exception $e) {
                Log::error('Error creating new user notification: ' . $e->getMessage());
            }

            return redirect()->route('client.login') 
                ->with('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Có lỗi xảy ra khi đăng ký. Vui lòng thử lại.')
                ->withInput();
        }
    }
}
