<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('invoices.create');
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:invoice_date'],
            'discount' => ['required', 'numeric', 'min:0'],
            'tax_rate' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'in:Draft,Sent,Paid,Partial,Overdue,Cancelled'],
            
            // Item rows validation
            'items' => ['required', 'array', 'min:1'],
            'items.*.customer_service_id' => ['nullable', 'exists:customer_services,id'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.rate' => ['required', 'numeric', 'min:0'],
        ];
    }
}
