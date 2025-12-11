<?php

namespace App\Http\Controllers\Client\Account;

use App\Http\Controllers\Controller;
use App\Models\ReviewReport;
use Illuminate\Http\Request;

class ReviewReportController extends Controller
{
    /**
     * Danh sách các báo cáo bình luận của khách hàng
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $query = ReviewReport::with(['review.product'])
            ->where('user_id', $userId)
            ->latest();

        if ($reason = $request->get('reason')) {
            $query->where('reason', $reason);
        }

        $reports = $query->paginate(10)->withQueryString();

        return view('client.account.reports.index', compact('reports', 'reason'));
    }
}


