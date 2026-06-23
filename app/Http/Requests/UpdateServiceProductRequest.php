<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('services.edit');
    }

    public function rules(): array
    {
        return [
            'service_category_id' => ['required', 'exists:service_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'billing_cycle' => ['required', 'string', 'in:Monthly,Quarterly,Half Yearly,Yearly,One Time'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:Active,Inactive'],
        ];
    }
}
