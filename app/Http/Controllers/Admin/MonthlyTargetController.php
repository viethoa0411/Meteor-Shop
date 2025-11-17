<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MonthlyTarget;

class MonthlyTargetController extends Controller
{
    public function create()
    {
        return view('admin.monthly_target.create'); // form nhập mục tiêu
    }

    public function store(Request $request)
    {
        $request->validate([
            'target_amount' => 'required|numeric|min:0',
        ]);

        MonthlyTarget::updateOrCreate(
            [
                'year' => now()->year,
                'month' => now()->month
            ],
            [
                'target_amount' => $request->target_amount
            ]
        );

        return redirect()->route('admin.dashboard')
                         ->with('success', 'Đặt mục tiêu tháng thành công!');
    }
}
