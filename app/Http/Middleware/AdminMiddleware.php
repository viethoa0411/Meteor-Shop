<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Kiểm tra xem user có phải admin hoặc staff không
     */
    public function handle(Request $request, Closure $next)
    {
        // Nếu chưa đăng nhập, chuyển hướng đến trang login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập trước');
        }

        // Kiểm tra role - chỉ admin mới có quyền
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Bạn không có quyền truy cập trang quản trị');
        }

        return $next($request);
    }
}

