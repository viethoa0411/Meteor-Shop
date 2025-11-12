<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Danh sách sản phẩm
     */
    public function list(Request $req)
    {
        $q = Product::query()
            ->select(['id', 'name', 'slug', 'price', 'stock', 'image', 'category_id', 'status', 'created_at'])
            ->with(['category:id,name'])
            ->orderByDesc('id');

        // Tìm kiếm
        if ($search = trim((string) $req->get('search'))) {
            $q->where(function ($x) use ($search) {
                $x->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Lọc theo trạng thái
        if ($status = $req->get('status')) {
            $q->where('status', $status);
        }

        // Lọc theo danh mục
        if ($cat = $req->get('category_id')) {
            $q->where('category_id', $cat);
        }

        $products   = $q->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get(['id', 'name']);

        return view('admin.products.list', compact('products', 'categories',));
    }

    /**
     * Form tạo sản phẩm mới
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get(['id', 'name']);

        return view('admin.products.create', compact('categories',));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'images' => 'nullable',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
            'status' => 'required|in:active,inactive',
        ]);

        // Upload ảnh chính
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Lưu sản phẩm
        $product = Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $imagePath,
            'category_id' => $request->category_id,
            'status' => $request->status,
        ]);

        // Lưu nhiều ảnh chi tiết
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('products', 'public');
                $product->images()->create([
                    'image' => $path
                ]);
            }
        }

        // Lưu biến thể (nếu có)
        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                $product->variants()->create([
                    'color_name' => $variant['color_name'] ?? null,
                    'color_code' => $variant['color_code'] ?? null,
                    'length' => $variant['length'] ?? null,
                    'width' => $variant['width'] ?? null,
                    'height' => $variant['height'] ?? null,
                ]);
            }
        }

        return redirect()->route('admin.products.list')
            ->with('success', 'Thêm sản phẩm thành công 🎉');
    }


    /**
     * Xem chi tiết
     */
    public function show($id)
    {
        $product = Product::with([
            'category:id,name',
            'variants',   // load biến thể sản phẩm
            'images'      // load tất cả ảnh phụ
        ])->findOrFail($id);

        return view('admin.products.show', compact('product'));
    }


    /**
     * Form sửa sản phẩm
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'image' => $imagePath,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.products.list')->with('success', 'Cập nhật sản phẩm thành công!');
    }

    // Xóa sản phẩm
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->route('admin.products.list')->with('success', 'Đã xoá sản phẩm!');
    }
}
