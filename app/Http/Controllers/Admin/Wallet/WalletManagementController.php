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
  
}