<?php

namespace App\Http\Controllers\Admin\Wallet;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class WalletTransactionFilterController extends Controller
{
    /**
     * ========================================
     * LỌC GIAO DỊCH - AJAX
     * ========================================
     * Trả về bảng giao dịch (AJAX) nếu cần
     */
    public function index(Request $request, $walletId)
    {
        $data = $this->getFilteredTransactions($request, $walletId);

        return view('admin.wallet.partials.transactions-table', $data);
    }

    /**
     * ========================================
     * LỌC GIAO DỊCH + PHÂN TRANG + TÌM KIẾM
     * ========================================
     * Xây dựng dữ liệu giao dịch theo bộ lọc:
     * - Lọc theo trạng thái (status): all, pending, completed, cancelled
     * - Lọc theo loại (type): all, income, expense
     * - Tìm kiếm theo từ khóa (keyword): mã giao dịch, mô tả
     * - Phân trang: 7 giao dịch/trang
     */
    public function getFilteredTransactions(Request $request, $walletId): array
    {
        $status = $request->get('status', 'all');
        $type = $request->get('type', 'all');
        $keyword = $request->get('keyword');

        $query = Transaction::with(['order.refunds', 'refund', 'processor', 'logs.user', 'marker'])
            ->where('wallet_id', $walletId);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('transaction_code', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(7)->withQueryString();

        return compact('transactions', 'status', 'type', 'keyword');
    }
}

