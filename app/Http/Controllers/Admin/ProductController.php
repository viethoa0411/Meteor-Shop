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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class ProductController extends Controller
{
    /**
     * Danh sách sản phẩm (có tìm kiếm và lọc)
     */
    public function index(Request $request)
    {
        $q = Product::query()
            ->select(['id', 'name', 'slug', 'price', 'stock', 'image', 'category_id', 'brand_id', 'status', 'created_at'])
            ->with(['category:id,name', 'brand:id,name'])
            ->orderByDesc('id');

        // Tìm kiếm
        if ($search = trim((string) $request->get('search'))) {
            $q->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Lọc theo trạng thái
        if ($status = $request->get('status')) {
            $q->where('status', $status);
        }

        // Lọc theo danh mục
        if ($categoryId = $request->get('category_id')) {
            $q->where('category_id', $categoryId);
        }

        // Lọc theo thương hiệu
        if ($brandId = $request->get('brand_id')) {
            $q->where('brand_id', $brandId);
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

    /**
     * Lưu sản phẩm mới
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        // Tự tạo slug nếu bỏ trống
        $data['slug'] = $data['slug'] ?: Str::slug($data['name'] . '-' . Str::random(4));

        // Upload ảnh
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = null;

        DB::transaction(function () use (&$product, $data, $request) {
            $product = Product::create($data);

            // Xử lý biến thể (nếu có)
            $colors = collect($request->input('colors', []))
                ->filter(fn($c) => !empty($c['code']))->values();

            $sizes = collect($request->input('sizes', []))
                ->filter(fn($s) => Arr::has($s, ['length', 'width', 'height']))->values();

            $basePrice = $data['variant_price'] ?? ($data['price'] ?? null);
            $baseStock = $data['variant_stock'] ?? ($data['stock'] ?? 0);

            $variants = [];

            if ($colors->isEmpty() && $sizes->isEmpty()) {
                // Không tạo biến thể nếu không có màu và kích thước
            } elseif ($colors->isEmpty()) {
                foreach ($sizes as $sz) {
                    $variants[] = [
                        'length' => $sz['length'],
                        'width'  => $sz['width'],
                        'height' => $sz['height'],
                        'price'  => $basePrice,
                        'stock'  => $baseStock,
                        'sku'    => strtoupper(Str::random(8)),
                    ];
                }
            } elseif ($sizes->isEmpty()) {
                foreach ($colors as $c) {
                    $variants[] = [
                        'color_name' => $c['name'] ?? null,
                        'color_code' => $c['code'],
                        'price'      => $basePrice,
                        'stock'      => $baseStock,
                        'sku'        => strtoupper(Str::random(8)),
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
                            'sku'        => strtoupper(Str::random(8)),
                        ];
                    }
                }
            }

            if (!empty($variants)) {
                $product->variants()->createMany($variants);
            }
        });

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Đã tạo sản phẩm và các biến thể thành công!');
    }

    /**
     * Xem chi tiết sản phẩm
     */
    public function show(Product $product)
    {
        $product->load(['category:id,name', 'brand:id,name']);
        return view('admin.products.show', compact('product'));
    }

    /**
     * Form sửa sản phẩm
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get(['id', 'name']);
        $brands     = Brand::orderBy('name')->get(['id', 'name']);

        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    /**
     * Cập nhật sản phẩm
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: ($product->slug ?: Str::slug($data['name'] . '-' . $product->id));

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.list')
            ->with('success', 'Cập nhật sản phẩm thành công!');
    }

    /**
     * Xoá sản phẩm
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('error', 'Sản phẩm đã được chuyển vào thùng rác.');
    }
}
