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
     * Danh sách sản phẩm (có tìm kiếm)
     * View: resources/views/admin/products/list.blade.php 
     * Route name: amdin.products.list
     */
    public function index(Request $req)
    {
        $q = Product::query()
            ->select(['id','name','slug','price','stock','image','category_id','brand_id','status','created_at' ])        
            ->with([
                'category:id,name',
                'brand:id,name',
            ])
            ->orderByDesc('id');

        if ($search = trim((string) $req->get('search'))) {
            $q->where(function ($x) use ($search) {
                $x->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }
        
        if ($status = $req->get('status')) {
            $q->where('status', $status);
        }

        if ($cat = $req->get('category_id')) {
            $q->where('category_id', $cat);
        }
    
        $products   = $q->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get(['id', 'name']);

        return view('admin.products.list', compact('products', 'categories'));    
    }

    /**
     * Form tạo
     * View: rerources/views/admin/products/create.blade.php 
     * Route name: admin.products.create 
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get(['id', 'name']);
        $brands     = Brand::orderBy('name')->get(['id', 'name']);

        return view('admin.products.create', compact('categories', 'brands'));
    }

    /**
     * Lưu sản phẩm mới
     * admin.product.list
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        // Tự tạo slug nếu bỏ trống
        $data['slug'] = $data['slug'] ?: Str::slug($data['name'].'-'.Str::random(4));

        // Upload ảnh (Lưu vào storage/app/public/products)
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')
                ->store('products', 'public');
        }

        // để Intelephense không cảnh báo
        $product = null;

        DB::transaction(function () use ($request, &$product, $data){
            //Tạo product
            $product = Product::create($data);

            // Lấy dữ liệu biến thể từ form
            $colors = collect($request->input('colors', []))
                ->filter(fn($c)  => !empty($c['code']))->values();

            $sizes = collect($request->input('sizes', []))
                ->filter(fn($s)  =>Arr::has($s, ['length', 'width', 'height']))->values();

            // mặc định giá/stock cho biến thể
            $basePrice = $data['variant_price'] ?? ($data['price'] ?? null);
            $baseStock = $data['variant_stock'] ?? ($data['stock'] ?? 0);

            // sinh tổ hợp
            $variant = [];

            if ($colors->isEmpty() && $sizes->isEmpty()) {
                 // không tạo biến thể
            } elseif ($colors->isEmpty()) {
                foreach ($sizes as $sz) {
                    $variants[] =[
                        'length'    => $sz['length'],
                        'width'     => $sz['length'],
                        'height'    => $sz['length'],
                        'price'     => $basePrice,
                        'stock'     => $baseStock,
                    ];
                }
            } elseif ($sizes->isEmpty()) {
                foreach ($colors as $c)
            }
        });

        return redirect()->route('admin.products.list')
            ->with('success', 'Đã tạo sản phẩm ' .$product->name);
    }

    /**
     * Xem chi tiết
     * View: resource/views/admin/products/show.blade.php
     */
    public function show(Product $product)
    {
        $product->load(['category:id,name', 'brand:id,name']);
        return view('admin.products.show', compact('product'));
    }

    /**
     * Form sửa
     * View: resources/views/admin/products/edit.blade.php 
     * Route name: admin.products.edit 
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get(['id', 'name']);
        $brands     = Brand::orderBy('name')->get(['id', 'name']);

        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    /**
     * Cập nhật
     * admin.products.list
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: ($product->slug ?: Str::slug($data['name'].'-'.$product->id));
    
        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.list')
                    ->with('success', 'Đã cập nhật sản phẩm');
    }

    /**
     * Xoá vĩnh viễn
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('error', 'Sản phẩm đã được chuyển vào thùng rác.');
    }
}
