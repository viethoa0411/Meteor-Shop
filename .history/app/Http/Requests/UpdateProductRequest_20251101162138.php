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
       
        ];
    }
}
