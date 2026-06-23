<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('services.create');
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'service_product_id' => ['required', 'exists:service_products,id'],
            'service_name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'expiry_date' => ['required', 'date', 'after_or_equal:start_date'],
            'billing_cycle' => ['required', 'string', 'in:Monthly,Quarterly,Half Yearly,Yearly,One Time'],
            'amount' => ['required', 'numeric', 'min:0'],
            'auto_renew' => ['nullable', 'boolean'],
            'remarks' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'in:Active,Expired,Suspended,Cancelled,Pending'],
        ];
    }
}
