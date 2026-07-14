<?php

namespace App\Http\Requests\Toko;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'address_id' => ['required', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'address_id.required' => 'Pilih alamat pengambilan terlebih dahulu.',
        ];
    }
}
