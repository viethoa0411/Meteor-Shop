<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\PriceWatch;
use Illuminate\Http\Request;

class PriceWatchController extends Controller
{
    public function subscribe(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'email' => 'nullable|email',
            'target_price' => 'nullable|numeric|min:0',
        ]);
        $attrs = [
            'product_id' => (int) $data['product_id'],
            'user_id' => auth()->id(),
            'email' => auth()->id() ? null : ($data['email'] ?? null),
        ];
        PriceWatch::updateOrCreate($attrs, [
            'target_price' => $data['target_price'] ?? null,
        ]);
        return back()->with('success', 'Đã đăng ký theo dõi giá.');
    }
}


