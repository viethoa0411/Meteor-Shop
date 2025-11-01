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

}