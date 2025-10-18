<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Dùng paginate để view gọi $users->links() hoạt động
        $users = User::orderBy('id', 'asc')->paginate(15);
        return view('admin.users.list', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,staff,user',
            'address' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive,banned',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'address' => $request->address,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.users.list')->with('success', 'Đã thêm người dùng thành công.');
    }
    /**  Hiển thị form sửa người dùng */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**  Cập nhật người dùng */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validate dữ liệu khi sửa
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,staff,user',
            'address' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive,banned',
        ]);

        // Cập nhật dữ liệu
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'address' => $request->address,
            'status' => $request->status,
            'password' => $request->filled('password') ? Hash::make($request->password) : $user->password,
        ]);

        return redirect()->route('admin.users.list')->with('success', 'Cập nhật người dùng thành công.');
    }
    /**  Xóa mềm người dùng */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete(); // Xóa mềm
        return redirect()->route('admin.users.list')->with('success', 'Người dùng đã bị đưa vào thùng rác.');
    }
    /**  Hiển thị danh sách người dùng bị xóa mềm */
    public function trash()
    {
        $users = User::onlyTrashed()->paginate(15);
        return view('admin.users.trash', compact('users'));
    }
}