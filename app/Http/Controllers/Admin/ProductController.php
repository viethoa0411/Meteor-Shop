<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\NotificationService;


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
            'variant_color.*' => 'required_with:variant_size.*',
            'variant_size.*'  => 'required_with:variant_color.*',

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

         // Validate biến thể
        $request->validate([
            'variants.*.color_name' => 'required',
            'variants.*.color_code' => 'required',
            'variants.*.stock' => 'required|numeric|min:0',
            'variants.*.length' => 'required|numeric|min:0',
            'variants.*.width' => 'required|numeric|min:0',
            'variants.*.height' => 'required|numeric|min:0',
        ], [
            'variants.*.color_name.required' => 'Vui lòng nhập màu cho biến thể.',
            'variants.*.color_code.required' => 'Vui lòng chọn mã màu.',
            'variants.*.stock.required' => 'Vui lòng nhập số lượng tồn kho.',
            'variants.*.length.required' => 'Vui lòng nhập chiều dài.',
            'variants.*.width.required' => 'Vui lòng nhập chiều rộng.',
            'variants.*.height.required' => 'Vui lòng nhập chiều cao.',
        ]);

        // Xử lý ảnh đại diện
        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Lưu stock cũ để so sánh
        $oldStock = $product->stock;
        
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
        
        // Kiểm tra và tạo thông báo về tồn kho
        $newStock = $product->fresh()->stock;
        if ($newStock == 0) {
            // Sản phẩm hết hàng
            try {
                NotificationService::createForAdmins([
                    'type' => 'product',
                    'level' => 'danger',
                    'title' => 'Sản phẩm hết hàng',
                    'message' => $product->name . ' đã hết hàng',
                    'url' => route('admin.products.show', $product->id),
                    'metadata' => ['product_id' => $product->id, 'stock' => 0]
                ]);
            } catch (\Exception $e) {
                Log::error('Error creating out of stock notification: ' . $e->getMessage());
            }
        } elseif ($newStock > 0 && $newStock <= 10 && ($oldStock > 10 || $oldStock == 0)) {
            // Sản phẩm sắp hết hàng (chỉ thông báo khi chuyển từ >10 xuống <=10 hoặc từ 0 lên >0)
            try {
                NotificationService::notifyLowStock($product);
            } catch (\Exception $e) {
                Log::error('Error creating low stock notification: ' . $e->getMessage());
            }
        }

        // Xử lý upload ảnh phụ (nếu có)
        if ($request->hasFile('images')) {

            // 1. XÓA toàn bộ ảnh cũ (trong database + trong storage)
            foreach ($product->images as $img) {
                if ($img->image && Storage::disk('public')->exists($img->image)) {
                    Storage::disk('public')->delete($img->image);
                }
                $img->delete();
            }

            // 2. THÊM ảnh mới
            foreach ($request->file('images') as $file) {
                $product->images()->create([
                    'image' => $file->store('products', 'public')
                ]);
            }
        }

        // ========================== 
        // 🔥 TĂNG VERSION SẢN PHẨM     
        // ==========================
        $product->increment('product_version');
        $product->refresh();
        $version = $product->product_version; // lấy version mới

        // BIến thể
        foreach ($request->variants ?? [] as $v) {

            // Sửa biến thể cũ 
          if (!empty($v['id'])) {

            $variant = $product->variants->firstWhere('id', $v['id']);

                if ($variant) {
                    $oldVariantStock = $variant->stock;
                    $variant->update([
                        'product_version' => $version,
                        'color_name' => $v['color_name'],
                        'color_code' => $v['color_code'],
                        'length'     => $v['length'] ?? null,
                        'width'      => $v['width'] ?? null,
                        'height'     => $v['height'] ?? null,
                        'stock'      => $v['stock'] ?? 0,
                        'price'      => $v['price'] ?? $product->price,
                    ]);
                    
                    // Kiểm tra stock của variant sau khi update
                    $newVariantStock = $variant->fresh()->stock;
                    if ($newVariantStock == 0 && $oldVariantStock > 0) {
                        // Variant hết hàng
                        try {
                            NotificationService::createForAdmins([
                                'type' => 'product',
                                'level' => 'danger',
                                'title' => 'Biến thể hết hàng',
                                'message' => $product->name . ' - ' . ($variant->color_name ?? 'Biến thể') . ' đã hết hàng',
                                'url' => route('admin.products.show', $product->id),
                                'metadata' => ['product_id' => $product->id, 'variant_id' => $variant->id, 'stock' => 0]
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Error creating variant out of stock notification: ' . $e->getMessage());
                        }
                    } elseif ($newVariantStock > 0 && $newVariantStock <= 10 && ($oldVariantStock > 10 || $oldVariantStock == 0)) {
                        // Variant sắp hết hàng
                        try {
                            NotificationService::createForAdmins([
                                'type' => 'product',
                                'level' => 'warning',
                                'title' => 'Biến thể sắp hết hàng',
                                'message' => $product->name . ' - ' . ($variant->color_name ?? 'Biến thể') . ' chỉ còn ' . $newVariantStock . ' sản phẩm',
                                'url' => route('admin.products.show', $product->id),
                                'metadata' => ['product_id' => $product->id, 'variant_id' => $variant->id, 'stock' => $newVariantStock]
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Error creating variant low stock notification: ' . $e->getMessage());
                        }
                    }
                }

                continue;
                    }

                // Tạo biến thể mới 
                    $variant = $product->variants()->create([
                        'product_id'      => $product->id,
                        'product_version' => $version,   // 🔥 KHÔNG BAO GIỜ NULL
                        'color_name'      => $v['color_name'],
                        'color_code'      => $v['color_code'],
                        'length'          => $v['length'] ?? null,
                        'width'           => $v['width'] ?? null,
                        'height'          => $v['height'] ?? null,
                        'stock'           => $v['stock'] ?? 0,
                        'price'           => $v['price'] ?? $product->price,
                    ]);
                    
                    // Kiểm tra stock của variant
                    if ($variant->stock == 0) {
                        try {
                            NotificationService::createForAdmins([
                                'type' => 'product',
                                'level' => 'danger',
                                'title' => 'Biến thể hết hàng',
                                'message' => $product->name . ' - ' . ($variant->color_name ?? 'Biến thể') . ' đã hết hàng',
                                'url' => route('admin.products.show', $product->id),
                                'metadata' => ['product_id' => $product->id, 'variant_id' => $variant->id, 'stock' => 0]
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Error creating variant out of stock notification: ' . $e->getMessage());
                        }
                    } elseif ($variant->stock > 0 && $variant->stock <= 10) {
                        try {
                            NotificationService::createForAdmins([
                                'type' => 'product',
                                'level' => 'warning',
                                'title' => 'Biến thể sắp hết hàng',
                                'message' => $product->name . ' - ' . ($variant->color_name ?? 'Biến thể') . ' chỉ còn ' . $variant->stock . ' sản phẩm',
                                'url' => route('admin.products.show', $product->id),
                                'metadata' => ['product_id' => $product->id, 'variant_id' => $variant->id, 'stock' => $variant->stock]
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Error creating variant low stock notification: ' . $e->getMessage());
                        }
                    }
                }

      
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
