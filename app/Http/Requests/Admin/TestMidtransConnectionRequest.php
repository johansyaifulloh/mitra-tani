<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TestMidtransConnectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'server_key' => ['required', 'string'],
            'mode' => ['required', 'in:sandbox,production'],
        ];
    }

    public function messages(): array
    {
        return [
            'server_key.required' => 'Server Key wajib diisi untuk test koneksi.',
            'mode.in' => 'Mode harus sandbox atau production.',
        ];
    }
}
