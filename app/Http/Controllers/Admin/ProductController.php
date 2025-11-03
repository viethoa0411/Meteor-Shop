<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
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
            ->select(['id', 'name', 'slug', 'price', 'stock', 'image', 'category_id', 'brand_id', 'status', 'created_at'])
            ->with(['category:id,name', 'brand:id,name'])
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

        // Lọc theo thương hiệu (nếu có)
        if ($brand = $req->get('brand_id')) {
            $q->where('brand_id', $brand);
        }

        $products   = $q->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get(['id', 'name']);
        $brands     = Brand::orderBy('name')->get(['id', 'name']);

        return view('admin.products.list', compact('products', 'categories', 'brands'));
    }

    /**
     * Form tạo sản phẩm mới
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get(['id', 'name']);
        $brands     = Brand::orderBy('name')->get(['id', 'name']);

        return view('admin.products.create', compact('categories', 'brands'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        // Ảnh
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Lưu sản phẩm chính
        $product = Product::create([
            'name' => $request->name,
            'slug' => $request->slug ? Str::slug($request->slug) : Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $imagePath,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'status' => $request->status,
        ]);

        if ($request->has('colors') && $request->has('sizes')) {
            $color = $request->colors[0];
            $size  = $request->sizes[0];

            $product->variants()->create([
                'color_name' => $color['name'] ?? null,
                'color_code' => $color['code'] ?? null,
                'length'     => $size['length'] ?? null,
                'width'      => $size['width'] ?? null,
                'height'     => $size['height'] ?? null,
                'price'      => $request->price,
                'stock'      => $request->stock,
            ]);
        }


        return redirect()->route('admin.products.list')
            ->with('success', 'Thêm sản phẩm thành công!');
    }



    /**
     * Xem chi tiết
     */
    public function show($id)
    {
        $product = Product::with(['category:id,name', 'brand:id,name'])->findOrFail($id);
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
