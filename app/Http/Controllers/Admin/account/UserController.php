<?php

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    //  * HIỂN THỊ DANH SÁCH USER
    public function index(Request $request)
    {
        // Bước 1: Tạo query chỉ lấy user có role = 'user'
        $query = User::where('role', 'user');

        // Bước 2: Lọc theo trạng thái (mặc định là 'active')
        $status = $request->get('status', 'active'); // Mặc định là 'active'

        if ($status !== 'all') {
            // Nếu không phải 'all' thì lọc theo status cụ thể
            $query->where('status', $status);
        }

        // Bước 3: Xử lý tìm kiếm (nếu có)
        if ($request->has('keyword') && $request->keyword != '') {
            $keyword = $request->keyword;
            // Tìm kiếm theo tên HOẶC email HOẶC số điện thoại
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%");
            });
        }

        // Bước 4: Sắp xếp theo ID tăng dần và phân trang 7 bản ghi/trang
        $users = $query->orderBy('id', 'asc')->paginate(7);

        // Bước 5: Giữ lại từ khóa tìm kiếm và status khi chuyển trang
        $users->appends($request->only(['keyword', 'status']));

        // Bước 6: Trả về view với dữ liệu danh sách user
        return view('admin.account.users.list', compact('users'));
    }
}
