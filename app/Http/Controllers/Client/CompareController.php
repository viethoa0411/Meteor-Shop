<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    const MAX_ITEMS = 4;

    public function index(Request $request)
    {
        $ids = collect($request->session()->get('compare', []))->unique()->take(self::MAX_ITEMS)->values()->all();
        $products = empty($ids) ? collect() : Product::with(['brand','category'])->whereIn('id', $ids)->get();
        return view('client.compare.index', compact('products'));
    }

    public function add(Request $request)
    {
        $request->validate(['product_id' => 'required|integer|exists:products,id']);
        $ids = collect($request->session()->get('compare', []));
        if ($ids->contains((int)$request->product_id)) {
            return back();
        }
        if ($ids->count() >= self::MAX_ITEMS) {
            return back()->with('error', 'Danh sách so sánh tối đa ' . self::MAX_ITEMS . ' sản phẩm.');
        }
        $ids = $ids->push((int)$request->product_id)->unique()->values();
        $request->session()->put('compare', $ids->all());
        return back()->with('success', 'Đã thêm vào so sánh.');
    }

    public function remove(Request $request)
    {
        $request->validate(['product_id' => 'required|integer']);
        $ids = collect($request->session()->get('compare', []))->reject(fn($v)=> (int)$v === (int)$request->product_id)->values();
        $request->session()->put('compare', $ids->all());
        return back();
    }

    public function clear(Request $request)
    {
        $request->session()->forget('compare');
        return back();
    }
}


