<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class CustomersExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return Customer::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Customer Code',
            'Company Name',
            'Contact Person',
            'Email',
            'Mobile',
            'Alternate Mobile',
            'GSTIN',
            'PAN',
            'Address',
            'City',
            'State',
            'Country',
            'Pin Code',
            'Website',
            'Status',
            'Created At',
        ];
    }

    /**
     * @param Customer $customer
     * @return array
     */
    public function map($customer): array
    {
        return [
            $customer->id,
            $customer->customer_code,
            $customer->company_name,
            $customer->contact_person,
            $customer->email,
            $customer->mobile,
            $customer->alternate_mobile,
            $customer->gstin,
            $customer->pan,
            $customer->address,
            $customer->city,
            $customer->state,
            $customer->country,
            $customer->pin_code,
            $customer->website,
            $customer->status,
            $customer->created_at ? $customer->created_at->format('Y-m-d H:i:s') : '',
        ];
    }
}
