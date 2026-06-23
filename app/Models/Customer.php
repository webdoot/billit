<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_code',
        'company_name',
        'contact_person',
        'email',
        'mobile',
        'alternate_mobile',
        'gstin',
        'pan',
        'address',
        'city',
        'state',
        'country',
        'pin_code',
        'website',
        'notes',
        'status',
    ];

    public function customerServices(): HasMany
    {
        return $this->hasMany(CustomerService::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
