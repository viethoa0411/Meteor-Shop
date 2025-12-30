<?php

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{

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

        // Bước 4: Sắp xếp theo ID tăng dần và phân trang 7 bản ghi/trang
        $users = $query->orderBy('id', 'asc')->paginate(7);

        // Bước 5: Giữ lại từ khóa tìm kiếm và status khi chuyển trang
        $users->appends($request->only(['keyword', 'status']));

        // Bước 6: Trả về view với dữ liệu danh sách admin
        return view('admin.account.admin.list', compact('users'));
    }


    public function create()
    {
        // Trả về view form tạo admin
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

        // Bước 3: Redirect về danh sách admin với thông báo thành công
        return redirect()->route('admin.account.admin.list')->with('success', 'Đã thêm người dùng thành công.');
    }


    public function show($id)
    {
        // Bước 1: Tìm admin theo ID, bao gồm cả admin đã bị ẩn (soft deleted)
        // withTrashed() cho phép lấy cả bản ghi đã bị xóa mềm
        $user = User::withTrashed()->findOrFail($id);

        // Bước 2: Trả về view hiển thị chi tiết với dữ liệu admin
        return view('admin.account.admin.show', compact('user'));
    }


    public function edit($id)
    {
        // Bước 1: Tìm admin theo ID (chỉ lấy admin chưa bị ẩn)
        $user = User::findOrFail($id);

        // Bước 2: Trả về view form chỉnh sửa với dữ liệu admin
        return view('admin.account.admin.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        // Bước 1: Tìm admin theo ID
        $user = User::findOrFail($id);

        // Bước 2: Validate dữ liệu từ form
        $request->validate([
            'name' => 'required|string|max:255',              // Tên bắt buộc
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),     // Email phải unique, trừ chính admin này
            ],
            'phone' => 'nullable|string|max:20',              // Số điện thoại không bắt buộc
            'password' => 'nullable|string|min:8|confirmed',  // Password không bắt buộc (để trống = không đổi)
            'role' => 'required|in:admin,staff,user',         // Role bắt buộc
            'address' => 'nullable|string|max:500',           // Địa chỉ không bắt buộc
            'status' => 'required|in:active,inactive,banned', // Trạng thái bắt buộc
        ]);

        // Bước 3: Cập nhật thông tin admin
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

        // Bước 4: Redirect về danh sách admin với thông báo thành công
        return redirect()->route('admin.account.admin.list')->with('success', 'Cập nhật người dùng thành công.');
    }


    public function destroy($id)
    {
        // Bước 1: Tìm admin theo ID
        $user = User::findOrFail($id);

        // Bước 2: Thực hiện soft delete (ẩn tài khoản)
        $user->delete();

        // Bước 3: Redirect về danh sách với thông báo
        return redirect()->route('admin.account.admin.list')->with('success', 'Tài khoản admin đã được ẩn.');
    }


    public function trash()
    {
        // Bước 1: Lấy danh sách admin đã bị ẩn, phân trang 15 bản ghi/trang
        $users = User::onlyTrashed()->where('role', 'admin')->paginate(15);

        // Bước 2: Trả về view trash
        return view('admin.account.admin.trash', compact('users'));
    }


    public function restore($id)
    {
        // Bước 1: Tìm admin đã bị ẩn theo ID
        $user = User::withTrashed()->findOrFail($id);

        // Bước 2: Khôi phục tài khoản
        $user->restore();

        // Bước 3: Redirect về trang trash với thông báo
        return redirect()->route('admin.account.admin.trash')->with('success', 'Khôi phục tài khoản admin thành công.');
    }
    public function changeInfoForm($id)
    {
        $user = User::findOrFail($id);
        // Chỉ cho phép admin thay đổi chính mình hoặc admin khác (middleware admin đã bảo vệ)
        return view('admin.account.admin.change-info', compact('user'));
    }

    /**
     * Gửi OTP về email admin
     */
    public function sendOtpForChangeInfo(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,staff,super_admin', // nếu có role phân cấp
            'status' => 'required|in:active,inactive,banned',
        ]);

        // Tạo OTP 6 số
        $otp = rand(100000, 999999);

        // Lưu tạm vào session (hết hạn 10 phút)
        session([
            'admin_change_admin_info' => [
                'user_id' => $id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => $request->role,
                'status' => $request->status,
                'otp' => $otp,
                'expires_at' => now()->addMinutes(10),
            ]
        ]);

        // Gửi email OTP
        \Mail::to($user->email)->send(new \App\Mail\AdminOtpChangeInfoMail($otp, $user->name));

        return back()->with('success', 'Mã OTP đã gửi đến email: ' . $user->email . '. Vui lòng nhập để xác nhận.');
    }

    /**
     * Xác nhận OTP và lưu thay đổi
     */
    public function verifyOtpAndUpdateInfo(Request $request, $id)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $data = session('admin_change_admin_info');

        if (!$data || $data['user_id'] != $id || now()->gt($data['expires_at'])) {
            return back()->withErrors(['otp' => 'Mã OTP không hợp lệ hoặc đã hết hạn']);
        }

        if ($request->otp != $data['otp']) {
            return back()->withErrors(['otp' => 'Mã OTP sai']);
        }

        $user = User::findOrFail($id);
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'role' => $data['role'],
            'status' => $data['status'],
        ]);

        session()->forget('admin_change_admin_info');

        return redirect()->route('admin.account.admin.list')->with('success', 'Thay đổi thông tin admin thành công!');
    }
}
