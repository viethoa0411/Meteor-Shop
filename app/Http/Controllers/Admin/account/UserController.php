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
    public function create()
    {
        // Trả về view form tạo user
        return view('admin.account.users.create');
    }
    //  * XỬ LÝ LƯU USER MỚI VÀO DATABASE
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

        // Bước 2: Tạo bản ghi mới trong bảng users
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password), // Mã hóa password trước khi lưu
            'role' => $request->role,
            'address' => $request->address,
            'status' => $request->status,
        ]);

        // Bước 3: Redirect về danh sách user với thông báo thành công
        return redirect()->route('admin.account.users.list')->with('success', 'Đã thêm người dùng thành công.');
    }
    // Chức năng ẩn tài khoản
     public function destroy($id)
    {
        // Bước 1: Tìm user theo ID
        $user = User::findOrFail($id);

        // Bước 2: Thực hiện soft delete (ẩn tài khoản)
        $user->delete();

        // Bước 3: Redirect về danh sách với thông báo
        return redirect()->route('admin.account.users.list')->with('success', 'Tài khoản user đã được ẩn.');
    }
    // Danh sách tài khoản bị ẩn
     public function trash()
    {
        // Bước 1: Lấy danh sách user đã bị ẩn, phân trang 15 bản ghi/trang
        $users = User::onlyTrashed()->where('role', 'user')->paginate(15);

        // Bước 2: Trả về view trash
        return view('admin.account.users.trash', compact('users'));
    }
    // Khôi phục tài khoản bị ẩn
     public function restore($id)
    {
        // Bước 1: Tìm user đã bị ẩn theo ID
        $user = User::withTrashed()->findOrFail($id);

        // Bước 2: Khôi phục tài khoản
        $user->restore();

        // Bước 3: Redirect về trang trash với thông báo
        return redirect()->route('admin.account.users.trash')->with('success', 'Khôi phục tài khoản user thành công.');
    }
    // Sửa tài khoản
    public function edit($id)
    {
        // Bước 1: Tìm user theo ID (chỉ lấy user chưa bị ẩn)
        $user = User::findOrFail($id);

        // Bước 2: Trả về view form chỉnh sửa với dữ liệu user
        return view('admin.account.users.edit', compact('user'));
    }
    // Lưu vào database
    public function update(Request $request, $id)
    {
        // Bước 1: Tìm user theo ID
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',              // Tên bắt buộc
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),     // Email phải unique, trừ chính user này
            ],
            'phone' => 'nullable|string|max:20',              // Số điện thoại không bắt buộc
            'password' => 'nullable|string|min:8|confirmed',  // Password không bắt buộc (để trống = không đổi)
            'role' => 'required|in:admin,staff,user',         // Role bắt buộc
            'address' => 'nullable|string|max:500',           // Địa chỉ không bắt buộc
            'status' => 'required|in:active,inactive,banned', // Trạng thái bắt buộc
        ]);

        // Bước 3: Cập nhật thông tin user
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'address' => $request->address,
            'status' => $request->status,
            // Nếu có nhập password mới thì mã hóa và cập nhật, không thì giữ nguyên password cũ
            'password' => $request->filled('password') ? Hash::make($request->password) : $user->password,
        ]);

        // Bước 4: Redirect về danh sách user với thông báo thành công
        return redirect()->route('admin.account.users.list')->with('success', 'Cập nhật người dùng thành công.');
    }
    // Xem chi tiết tài khoản
    public function show($id)
    {
        // Bước 1: Tìm user theo ID, bao gồm cả user đã bị ẩn (soft deleted)
        // withTrashed() cho phép lấy cả bản ghi đã bị xóa mềm
        $user = User::withTrashed()->findOrFail($id);

        // Bước 2: Trả về view hiển thị chi tiết với dữ liệu user
        return view('admin.account.users.show', compact('user'));
    }
}
