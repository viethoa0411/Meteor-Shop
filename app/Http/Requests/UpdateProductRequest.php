<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 
     * */
    public function rules(): array
    {
        $id = $this->route('product');
        return [
            'name'        => ['required','string','max:255'],
            'slug'        => ['nullable','string','max:255', Rule::unique('products','slug')->ignore($id)],
            'description' => ['nullable','string'],
            'price'       => ['required','numeric','min:0'],
            'stock'       => ['required','integer','min:0'],
            'image'       => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
            'category_id' => ['required','exists:categories,id'],
            'brand_id'    => ['nullable','exists:brands,id'],
            'status'      => ['required','in:active,inactive'],
        'length' => 'nullable|numeric|min:0', // (Nếu vẫn giữ cột kích thước tổng quát ở products)
            'width'  => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'color_code' => 'nullable|string|max:20',

            // Màu (mảng)
            'colors'                 => 'nullable|array',
            'colors.*.name'          => 'nullable|string|max:50',
            'colors.*.code'          => 'required_with:colors|string|max:20', // "#HEX"
            
            // Kích thước (mảng)
            'sizes'                  => 'nullable|array',
            'sizes.*.length'         => 'required_with:sizes|numeric|min:0',
            'sizes.*.width'          => 'required_with:sizes|numeric|min:0',
            'sizes.*.height'         => 'required_with:sizes|numeric|min:0',

            // (tuỳ chọn) giá/stock mặc định cho biến thể mới
            'variant_price'          => 'nullable|numeric|min:0',
            'variant_stock'          => 'nullable|integer|min:0',
        ];
    }
}
