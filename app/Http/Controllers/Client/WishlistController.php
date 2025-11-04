<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\WishlistItem;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $ids = collect($request->session()->get('wishlist', []))->unique()->values()->all();
        if (auth()->check()) {
            $dbIds = WishlistItem::where('user_id', auth()->id())->pluck('product_id')->all();
            $ids = array_values(array_unique(array_merge($ids, $dbIds)));
        }
        $products = empty($ids) ? collect() : Product::whereIn('id', $ids)->get();
        return view('client.wishlist.index', compact('products'));
    }

    public function toggle(Request $request)
    {
        $request->validate(['product_id' => 'required|integer|exists:products,id']);
        $pid = (int) $request->product_id;
        $ids = collect($request->session()->get('wishlist', []));
        if ($ids->contains($pid)) {
            $ids = $ids->reject(fn($v) => (int)$v === $pid)->values();
        } else {
            $ids = $ids->push($pid)->unique()->values();
        }
        $request->session()->put('wishlist', $ids->all());

        if (auth()->check()) {
            $exists = WishlistItem::where('user_id', auth()->id())->where('product_id', $pid)->exists();
            if ($exists) {
                WishlistItem::where('user_id', auth()->id())->where('product_id', $pid)->delete();
            } else {
                WishlistItem::create(['user_id' => auth()->id(), 'product_id' => $pid]);
            }
        }

        return back()->with('success', 'Đã cập nhật wishlist.');
    }

    public function sync(Request $request)
    {
        if (!auth()->check()) return back();
        $ids = collect($request->session()->get('wishlist', []))->unique()->values()->all();
        foreach ($ids as $pid) {
            WishlistItem::firstOrCreate(['user_id' => auth()->id(), 'product_id' => (int)$pid]);
        }
        return back()->with('success', 'Đã đồng bộ wishlist.');
    }
}


