<?php

namespace App\Http\Requests\Admin;

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
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'emoji' => ['nullable', 'string', 'max:10'],
            'badge' => ['nullable', 'string', 'max:20'],
            'status' => ['nullable', 'string', 'in:active,draft'],
            'image' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
