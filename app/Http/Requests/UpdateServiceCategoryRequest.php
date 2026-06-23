<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('services.edit');
    }

    public function rules(): array
    {
        $id = $this->route('service_category')->id ?? $this->route('service_category');
        return [
            'name' => ['required', 'string', 'max:255', 'unique:service_categories,name,' . $id],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:Active,Inactive'],
        ];
    }
}
