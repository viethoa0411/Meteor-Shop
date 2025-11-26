<?php

namespace App\Http\Controllers\Admin\Wallet;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletManagementController extends Controller
{
    /**
     * ========================================
     * HIỂN THỊ TẤT CẢ CÁC VÍ
     * ========================================
     * Danh sách ví của admin với phân trang
     * - Admin: xem tất cả ví
     * - User khác: chỉ xem ví của mình
     */
    public function index(Request $request)
    {
        $query = Wallet::with('user');

        if (Auth::user()->role !== 'admin') {
            $query->where('user_id', Auth::id());
        }

        $wallets = $query->paginate(15);

        return view('admin.wallet.index', compact('wallets'));
    }
    // Hiển thị form thêm ví
    public function create()
    {
        $admins = User::where('role', 'admin')->get();
        return view('admin.wallet.create', compact('admins'));
    }
  public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'bank_name' => 'required|string|max:255',
            'bank_account' => 'required|string|max:255',
            'account_holder' => 'required|string|max:255',
            'balance' => 'nullable|numeric|min:0',
        ]);

        Wallet::create([
            'user_id' => $request->user_id,
            'bank_name' => $request->bank_name,
            'bank_account' => $request->bank_account,
            'account_holder' => $request->account_holder,
            'balance' => $request->balance ?? 0,
            'status' => 'active',
        ]);

        return redirect()->route('admin.wallet.index')
            ->with('success', 'Tạo ví thành công!');
    }
    /**
     * ========================================
     * SỬA VÍ - HIỂN THỊ FORM
     * ========================================
     * Hiển thị form chỉnh sửa ví
     */
    public function edit($id)
    {
        $wallet = Wallet::findOrFail($id);
        $admins = User::where('role', 'admin')->get();

        return view('admin.wallet.edit', compact('wallet', 'admins'));
    }
}