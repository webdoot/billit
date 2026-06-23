<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHostingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('services.edit');
    }

    public function rules(): array
    {
        return [
            'customer_service_id' => ['required', 'exists:customer_services,id'],
            'server_id' => ['nullable', 'exists:servers,id'],
            'hosting_type' => ['required', 'string', 'in:Shared,VPS,Dedicated,Cloud'],
            'control_panel' => ['nullable', 'string', 'max:50'],
            'username' => ['nullable', 'string', 'max:100'],
            'disk_limit' => ['nullable', 'string', 'max:50'],
            'bandwidth_limit' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'string', 'in:Active,Suspended,Inactive'],
        ];
    }
}
