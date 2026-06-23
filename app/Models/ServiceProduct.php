<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_category_id',
        'name',
        'billing_cycle',
        'price',
        'description',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function customerServices(): HasMany
    {
        return $this->hasMany(CustomerService::class);
    }
}
