<?php

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    //  * HIỂN THỊ DANH SÁCH ADMIN
    
    public function index(Request $request)
    {
        // Bước 1: Tạo query chỉ lấy user có role = 'admin'
        $query = User::where('role', 'admin');

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

     
        $users = $query->orderBy('id', 'asc')->paginate(7);

        
        $users->appends($request->only(['keyword', 'status']));

      
        return view('admin.account.admin.list', compact('users'));
    }
    public function create()
    {
        return view('admin.account.admin.create');
    }
    
    public function store(Request $request)
    {
        // Bước 1: Validate dữ liệu từ form
        $request->validate([
            'name' => 'required|string|max:255',              // Tên bắt buộc, tối đa 255 ký tự
            'email' => 'required|string|email|max:255|unique:users', // Email bắt buộc, phải unique
            'phone' => 'nullable|string|max:20',              // Số điện thoại không bắt buộc
            'password' => 'required|string|min:8|confirmed',  // Mật khẩu tối thiểu 8 ký tự, phải nhập lại khớp
            'role' => 'required|in:admin,staff,user',         // Role phải là 1 trong 3 giá trị
            'address' => 'nullable|string|max:500',           // Địa chỉ không bắt buộc
            'status' => 'required|in:active,inactive,banned', // Trạng thái bắt buộc
        ]);
        
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password), // Mã hóa password trước khi lưu
            'role' => $request->role,
            'address' => $request->address,
            'status' => $request->status,
        ]);

       
        return redirect()->route('admin.account.admin.list')->with('success', 'Đã thêm người dùng thành công.');
    }
    // Chức năng sửa tài khoản admin
    public function edit($id)
    {
        // Bước 1: Tìm admin theo ID (chỉ lấy admin chưa bị ẩn)
        $user = User::findOrFail($id);

        // Bước 2: Trả về view form chỉnh sửa với dữ liệu admin
        return view('admin.account.admin.edit', compact('user'));
    }
    
    //  * CẬP NHẬT THÔNG TIN ADMIN
    public function update(Request $request, $id)
    {
        // Bước 1: Tìm admin theo ID
        $user = User::findOrFail($id);

        // Bước 2: Validate dữ liệu từ form
          $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($user->id), // Không được trùng username của user khác
            ],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,staff,user',
            'address' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive,banned',
        ]);
        // Bước 4: Redirect về danh sách admin với thông báo thành công
        return redirect()->route('admin.account.admin.list')->with('success', 'Cập nhật người dùng thành công.');
    }

}