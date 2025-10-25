<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;



class ProductController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm (có tìm kiếm)
     */
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('keyword') && $request->keyword != '') {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('slug', 'like', "%{$keyword}%");
            });
        }

        $products = $query->orderBy('id', 'asc')->paginate(15);
        $products->appends($request->only('keyword'));

        return view('admin.products.list', compact('products'));
    }

    /**
     * Hiển thị form thêm mới
     */
    public function create()
    {

        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|integer|exists:categories,id',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', 
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048' 
        ]);

        $data = $request->except('image', 'images');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        }
        $galleryPaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('products/gallery', 'public');
                $galleryPaths[] = $path;
            }
        }
        $data['gallery'] = $galleryPaths;

        Product::create($data);

        return redirect()->route('admin.products.list')->with('success', 'Đã thêm sản phẩm thành công.');
    }
    /**
     * Hiển thị form chỉnh sửa
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Hiển thị chi tiết sản phẩm
     */
    public function show($id) 
    {
        $product = Product::findOrFail($id); 
        $product->load('variants'); 
        return view('admin.products.show', compact('product')); 
    }
    /**
     * Cập nhật sản phẩm
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products')->ignore($product->id),
            ],
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|integer|exists:categories,id',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $data = $request->except('image', 'images');

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        }
        $galleryPaths = $product->gallery ?? [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('products/gallery', 'public');
                $galleryPaths[] = $path;
            }
        }
        $data['gallery'] = $galleryPaths;

        $product->update($data);

        return redirect()->route('admin.products.list')->with('success', 'Cập nhật sản phẩm thành công.');
    }


    /**
     * Xóa sản phẩm
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        if ($product->gallery) {
            foreach ($product->gallery as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $product->delete();

        return redirect()->route('admin.products.list')->with('success', 'Đã xóa vĩnh viễn sản phẩm.');
    }
}
