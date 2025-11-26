<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Danh sách sản phẩm
     */
    public function list(Request $request)
    {
        $query = Product::query()
            ->select(['id', 'name', 'slug', 'price', 'stock', 'image', 'category_id', 'status', 'created_at'])
            ->with(['category:id,name'])
            ->orderByDesc('id');

        // Tìm kiếm
        if ($search = trim((string) $request->get('search'))) {
            $query->where(function ($x) use ($search) {
                $x->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Lọc theo trạng thái
        $status = $request->get('status', 'active');

        // Filter theo trạng thái
        if ($status !== 'all') {
            // Nếu không phải 'all' thì lọc theo status cụ thể
            $query->where('status', $status);
        }

        // Lọc theo danh mục
        if ($cat = $request->get('category_id')) {
            $query->where('category_id', $cat);
        }

        $products   = $query->paginate(15)->withQueryString();
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
            'slug' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'images' => 'nullable',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
            'status' => 'required|in:active,inactive',

            // Validate biến thể
            'variants' => 'nullable|array',
            'variants.*.color_name' => 'nullable|string|max:50',
            'variants.*.color_code' => 'nullable|string|max:20',
            'variants.*.length' => 'nullable|numeric|min:0',
            'variants.*.width' => 'nullable|numeric|min:0',
            'variants.*.height' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.price' => 'nullable|numeric|min:0',

        ]);

        // 🖼 Upload ảnh đại diện
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // 🛍️ Tạo sản phẩm chính
        $product = Product::create([
            'name' => $request->name,
            'slug' => $request->slug ? Str::slug($request->slug) : Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath,
            'category_id' => $request->category_id,
            'status' => $request->status,
            'stock' => $request->stock,
        ]);

        // 🖼 Lưu ảnh chi tiết (nếu có)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('products', 'public');
                $product->images()->create([
                    'image' => $path,
                ]);
            }
        }

        // 🧩 Lưu biến thể kèm tồn kho riêng
        // 🧩 Lưu biến thể kèm tồn kho riêng
        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                $product->variants()->create([
                    'color_name' => $variant['color_name'] ?? null,
                    'color_code' => $variant['color_code'] ?? null,
                    'length'     => $variant['length'] ?? null,
                    'width'      => $variant['width'] ?? null,
                    'height'     => $variant['height'] ?? null,
                    'stock'      => $variant['stock'] ?? 0,
                    'price'      => $variant['price'] ?? $request->price,
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
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096', // ảnh phụ
        ]);

        // Xử lý ảnh đại diện
        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Cập nhật thông tin sản phẩm
        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'status' => $request->status,
            'image' => $imagePath,
        ]);

        // Xử lý upload ảnh phụ (nếu có)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');
                $product->images()->create(['image' => $path]);
            }
        }

        // BIến thể
        foreach ($request->variants ?? [] as $v) {

            // Sửa biến thể cũ 
            if (!empty($v['id'])) {
                        $variant = $product->variants->firstWhere('id', $v['id']);
                        if ($variant) {
                            $variant->update([
                                'color_name' => $v['color_name'] ?? null,
                                'color_code' => $v['color_code'] ?? null,
                                'length'     => $v['length'] ?? null,
                                'width'      => $v['width'] ?? null,
                                'height'     => $v['height'] ?? null,
                                'stock'      => $v['stock'] ?? 0,
                                'price'      => $v['price'] ?? $product->price,
                            ]);
                        }
                        continue;
                    }

                // Tạo biến thể mới 
                    $product->variants()->create([
                        'product_version' => $product->version,
                        'color_name' => $v['color_name'] ?? null,
                        'color_code' => $v['color_code'] ?? null,
                        'length'     => $v['length'] ?? null,
                        'width'      => $v['width'] ?? null,
                        'height'     => $v['height'] ?? null,
                        'stock'      => $v['stock'] ?? 0,
                        'price'      => $v['price'] ?? $product->price,
                    ]);
                }

         // tăng version 

        return redirect()->route('admin.products.list')->with('success', 'Cập nhật sản phẩm thành công!');
    }


    // Xóa sản phẩm
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->route('admin.products.list')->with('success', 'Đã xoá sản phẩm!');
    }

    public function destroyImage($productId, $imageId)
    {
        $img = ProductImage::where('product_id', $productId)->findOrFail($imageId);
        if (Storage::disk('public')->exists($img->image)) {
            Storage::disk('public')->delete($img->image);
        }
        $img->delete();
        return response()->json(['success' => true]);
    }
}
