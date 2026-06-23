<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDomainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('services.edit');
    }

    public function rules(): array
    {
        return [
            'customer_service_id' => ['required', 'exists:customer_services,id'],
            'domain_name' => ['required', 'string', 'max:255'],
            'registrar' => ['required', 'string', 'max:255'],
            'registrar_account' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['nullable', 'date'],
            'expiry_date' => ['required', 'date'],
            'auto_renew' => ['nullable', 'boolean'],
            'dns_provider' => ['nullable', 'string', 'max:100'],
            'nameserver_1' => ['nullable', 'string', 'max:100'],
            'nameserver_2' => ['nullable', 'string', 'max:100'],
            'nameserver_3' => ['nullable', 'string', 'max:100'],
            'nameserver_4' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'string', 'in:Active,Expired,Transferred'],
        ];
    }
}
