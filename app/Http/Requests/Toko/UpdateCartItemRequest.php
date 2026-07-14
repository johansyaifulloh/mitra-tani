<?php

namespace App\Http\Requests\Toko;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => ['required_without:is_selected', 'integer', 'min:0'],
            'is_selected' => ['required_without:quantity', 'boolean'],
        ];
    }
}
