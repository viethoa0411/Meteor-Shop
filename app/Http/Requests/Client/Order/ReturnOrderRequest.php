<?php

namespace App\Http\Requests\Client\Order;

use Illuminate\Foundation\Http\FormRequest;

class ReturnOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'attachments' => ['nullable', 'array', 'max:3'],
            'attachments.*' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required' => 'Vui lòng nhập lý do đổi trả.',
            'description.required' => 'Vui lòng mô tả vấn đề gặp phải.',
            'attachments.max' => 'Chỉ được tải lên tối đa 3 ảnh.',
            'attachments.*.image' => 'Ảnh tải lên không hợp lệ.',
            'attachments.*.max' => 'Kích thước ảnh tối đa 4MB.',
        ];
    }
}

