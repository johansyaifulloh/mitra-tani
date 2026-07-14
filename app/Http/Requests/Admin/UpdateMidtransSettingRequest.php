<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMidtransSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'merchant_id' => ['nullable', 'string', 'max:255'],
            'server_key' => ['required', 'string', 'max:255'],
            'client_key' => ['required', 'string', 'max:255'],
            'mode' => ['required', 'in:sandbox,production'],
            'notification_url' => ['required', 'url'],
            'finish_url' => ['required', 'url'],
            'unfinish_url' => ['required', 'url'],
            'error_url' => ['required', 'url'],
            'expiry_duration' => ['nullable', 'integer', 'min:5', 'max:10080'],
            'is_active' => ['nullable', 'boolean'],
            'channels' => ['nullable', 'array'],
            'channels.*' => ['string'],
        ];
    }

    public function messages(): array
    {
        return [
            'server_key.required' => 'Server Key wajib diisi.',
            'client_key.required' => 'Client Key wajib diisi.',
            'mode.in' => 'Mode harus sandbox atau production.',
            'notification_url.url' => 'Notification URL tidak valid.',
            'finish_url.url' => 'Finish URL tidak valid.',
            'unfinish_url.url' => 'Unfinish URL tidak valid.',
            'error_url.url' => 'Error URL tidak valid.',
        ];
    }
}
