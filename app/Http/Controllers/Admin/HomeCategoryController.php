<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeCategoryController extends Controller
{
    public function index()
    {
        $categories = HomeCategory::orderBy('sort_order', 'asc')->get();
        return view('admin.home-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.home-categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
            'link' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'status' => 'required|in:active,inactive',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('home-categories', 'public');
        }

        HomeCategory::create([
            'name' => $request->name,
            'image' => $imagePath,
            'link' => $request->link,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.home-categories.index')
            ->with('success', 'Thêm danh mục trang chủ thành công!');
    }

    public function edit($id)
    {
        $category = HomeCategory::findOrFail($id);
        return view('admin.home-categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = HomeCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'link' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'status' => 'required|in:active,inactive',
        ]);

        $imagePath = $category->image;
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            $imagePath = $request->file('image')->store('home-categories', 'public');
        }

        $category->update([
            'name' => $request->name,
            'image' => $imagePath,
            'link' => $request->link,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.home-categories.index')
            ->with('success', 'Cập nhật danh mục trang chủ thành công!');
    }

    public function destroy($id)
    {
        $category = HomeCategory::findOrFail($id);
        
        // Xóa ảnh
        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }
        
        $category->delete();

        return redirect()->route('admin.home-categories.index')
            ->with('success', 'Xóa danh mục trang chủ thành công!');
    }
}

