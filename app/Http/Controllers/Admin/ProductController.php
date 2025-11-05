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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:active,inactive',
            'variants' => 'nullable|array',
            'variants.*.color_name' => 'nullable|string|max:50',
            'variants.*.color_code' => 'nullable|string|max:10',
            'variants.*.length' => 'nullable|numeric',
            'variants.*.width' => 'nullable|numeric',
            'variants.*.height' => 'nullable|numeric',
        ]);

        $data = $validated;

        // nếu slug trống hoặc không có, tự động tạo slug mới
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
            // Upload ảnh (nếu có)
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            // Tạo sản phẩm và biến thể trong transaction
            $product = DB::transaction(function () use ($request, $data) {

                // Tạo sản phẩm
                $product = Product::create($data);

                // Lấy danh sách màu & kích thước từ form (được JS thêm input ẩn)
                $colors = collect($request->input('colors', []))
                    ->filter(fn($c) => !empty($c['code']))
                    ->values();

                $sizes = collect($request->input('sizes', []))
                    ->filter(fn($s) => Arr::has($s, ['length', 'width', 'height']))
                    ->values();

                // Giá & tồn kho mặc định (nếu có)
                $basePrice = $data['variant_price'] ?? ($data['price'] ?? null);
                $baseStock = $data['variant_stock'] ?? ($data['stock'] ?? 0);

                // Sinh tổ hợp biến thể
                $variants = [];

                if ($colors->isEmpty() && $sizes->isEmpty()) {
                    // Không tạo biến thể
                } elseif ($colors->isEmpty()) {
                    foreach ($sizes as $sz) {
                        $variants[] = [
                            'length' => $sz['length'],
                            'width'  => $sz['width'],
                            'height' => $sz['height'],
                            'price'  => $basePrice,
                            'stock'  => $baseStock,
                        ];
                    }
                } elseif ($sizes->isEmpty()) {
                    foreach ($colors as $c) {
                        $variants[] = [
                            'color_name' => $c['name'] ?? null,
                            'color_code' => $c['code'],
                            'price'      => $basePrice,
                            'stock'      => $baseStock,
                        ];
                    }
                } else {
                    foreach ($colors as $c) {
                        foreach ($sizes as $sz) {
                            $variants[] = [
                                'color_name' => $c['name'] ?? null,
                                'color_code' => $c['code'],
                                'length'     => $sz['length'],
                                'width'      => $sz['width'],
                                'height'     => $sz['height'],
                                'price'      => $basePrice,
                                'stock'      => $baseStock,
                            ];
                        }
                    }
                }

                // Lưu các biến thể
                if (!empty($variants)) {
                    $product->variants()->createMany($variants);
                }

                return $product;
            });

            return redirect()
                ->route('admin.products.list', $product)
                ->with('success', 'Đã tạo sản phẩm và các biến thể thành công!');
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
