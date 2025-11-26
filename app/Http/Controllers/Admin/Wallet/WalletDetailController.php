<?php

namespace App\Http\Controllers\Admin\Wallet;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletDetailController extends Controller
{
    // Hiển thị chi tiết ví
    public function show(Request $request, $id)
    {
        $wallet = Wallet::with('user')->findOrFail($id);

        if (Auth::user()->role !== 'admin' && $wallet->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền truy cập ví này.');
        }

        /** @var WalletTransactionFilterController $filterController */
        $filterController = app(WalletTransactionFilterController::class);
        $filterData = $filterController->getFilteredTransactions($request, $id);

        $totalIncome = Transaction::where('wallet_id', $id)
            ->where('type', 'income')
            ->where('status', 'completed')
            ->sum('amount');

        $totalExpense = Transaction::where('wallet_id', $id)
            ->where('type', 'expense')
            ->where('status', 'completed')
            ->sum('amount');

        $pendingTransactions = Transaction::where('wallet_id', $id)
            ->where('status', 'pending')
            ->count();

        $pendingMarkedCount = Transaction::where('wallet_id', $id)
            ->where('status', 'pending')
            ->whereNotNull('marked_as_received_at')
            ->count();

        return view('admin.wallet.show', array_merge([
            'wallet' => $wallet,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'pendingTransactions' => $pendingTransactions,
            'pendingMarkedCount' => $pendingMarkedCount,
        ], $filterData));
    }
}

