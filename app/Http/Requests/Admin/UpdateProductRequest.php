<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'category_id' => ['sometimes', 'integer'],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'emoji' => ['nullable', 'string', 'max:10'],
            'badge' => ['nullable', 'string', 'max:20'],
            'status' => ['nullable', 'string', 'in:active,draft'],
            'image' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
