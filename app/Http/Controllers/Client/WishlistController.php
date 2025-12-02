<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        if (! Auth::check()) {
            return redirect()->route('client.login')
                ->with('error', 'Vui lòng đăng nhập để xem danh sách yêu thích.');
        }

        $items = Wishlist::with('product')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('client.whitelist', compact('items'));
    }

    public function toggle(Request $request)
    {
        if (! Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vui lòng đăng nhập để thêm sản phẩm vào danh sách yêu thích.',
                'requires_auth' => true,
                'redirect' => route('client.login'),
            ], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $userId = Auth::id();
        $productId = (int) $request->input('product_id');

        $existing = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();

            return response()->json([
                'status' => 'success',
                'liked' => false,
                'message' => 'Đã xóa khỏi danh sách yêu thích.',
            ]);
        }

        Wishlist::create([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        return response()->json([
            'status' => 'success',
            'liked' => true,
            'message' => 'Đã thêm vào danh sách yêu thích.',
        ]);
    }
}


