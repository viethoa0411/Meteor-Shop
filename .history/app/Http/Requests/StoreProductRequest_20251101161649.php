<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          =>['required', 'string', 'max:255'],
            'slug'          =>['nullable', 'string', 'max:255', 'unique:product,slug'],
            'description'   =>['nullable', 'string'],
            'price'         =>['required', 'numeric'],
            'stock'         =>['required', 'integer', 'min:0'],
            'image'         =>['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
           
            'category_id'   =>['required', 'exists:categories,id'],
            'brand_id'      =>['nullable', 'exists:brand,id'],
            'status'        =>['required', 'in:active,inactive'],

            'length'        =>'nullable|numeric|min:0', 
            'width'         =>'nullable|numeric|min:0', 
            'height'        =>'nullable|numeric|min:0', 
            'color_code'    =>'nullable|string|max:20',

            //Màu (mảng)
            'colors'        =>'nullable|array',
            'colors.*.name' =>'nullable|string|max:50',
            'colors.*.code' =>'required_with:colors|string|max:20',

            // Kích thước 
            'sizes'         =>'nullable|array',
            'sizes.*.length'=>'required_with:sizes|numberic|min:0',
            'sizes.*.width' =>'required_with:sizes|numberic|min:0',
            'sizes.*.height'=>'required_with:sizes|numberic|min:0',


        ];
    }
}
