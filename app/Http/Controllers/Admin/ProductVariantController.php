<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ProductVariantController extends Controller
{
    public function index(Product $product)
    {
        $product->load('variants');
        return view('admin.products.variants.index', compact('product'));
    }

    public function store(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'variants' => 'required|array|min:1',
            'variants.*.color' => 'nullable|string|max:255',
            'variants.*.material' => 'nullable|string|max:255',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'variants.*' => [
                function ($attribute, $value, $fail) use ($product, $request) {
                    $index = explode('.', $attribute)[1];
                    $variantId = $value['id'] ?? null;
                    $color = $value['color'] ?? null;
                    $material = $value['material'] ?? null; 

                    $query = ProductVariant::where('product_id', $product->id)
                        ->where('color', $color)
                        ->where('material', $material); 

                    if ($variantId) {
                        $query->where('id', '!=', $variantId);
                    }

                    if ($query->exists()) {
                        $fail("Sự kết hợp Màu sắc '{$color}' và Chất liệu '{$material}' đã tồn tại.");
                    }
                },
                function ($attribute, $value, $fail) {
                    if (empty($value['color']) && empty($value['material'])) { 
                        $fail('Biến thể tại dòng ' . (explode('.', $attribute)[1] + 1) . ' phải có ít nhất Màu sắc hoặc Chất liệu.');
                    }
                },
            ],
            // -----------------------------

        ], [
            'variants.required' => 'Bạn phải có ít nhất một biến thể.',
            'variants.*.price.required' => 'Giá không được để trống.',
            'variants.*.stock.required' => 'Tồn kho không được để trống.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->has('deleted_variants')) {
        }
        foreach ($request->variants as $variantData) {
            $variantId = $variantData['id'] ?? null;

            $slugPrefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $product->slug), 0, 3));
            $colorCode = strtoupper(str_replace(' ', '', \Illuminate\Support\Str::ascii($variantData['color'] ?? '')));
            $materialCode = strtoupper(str_replace(' ', '', \Illuminate\Support\Str::ascii($variantData['material'] ?? '')));
            $generatedSku = $slugPrefix . ($colorCode ? "-{$colorCode}" : '') . ($materialCode ? "-{$materialCode}" : ''); 
            // ------------------------

            $dataToSave = [
                'color' => $variantData['color'] ?? null,
                'material' => $variantData['material'] ?? null,
                'sku' => $generatedSku,
                'price' => $variantData['price'],
                'stock' => $variantData['stock'],
            ];

            $variant = $variantId ? ProductVariant::find($variantId) : null;

            if (isset($variantData['image'])) {
                if ($variant && $variant->image) {
                    Storage::disk('public')->delete($variant->image);
                }
                $dataToSave['image'] = $variantData['image']->store('products/variants', 'public');
            }

            $product->variants()->updateOrCreate(['id' => $variantId], $dataToSave);
        }

        return redirect()->route('admin.products.variants.index', $product->id)
            ->with('success', 'Đã cập nhật biến thể thành công!');
    }
}
