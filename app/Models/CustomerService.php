<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerService extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'service_product_id',
        'service_name',
        'start_date',
        'expiry_date',
        'billing_cycle',
        'amount',
        'auto_renew',
        'status',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expiry_date' => 'date',
        'amount' => 'decimal:2',
        'auto_renew' => 'boolean',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(ServiceProduct::class, 'service_product_id');
    }

    public function domain(): HasOne
    {
        return $this->hasOne(Domain::class);
    }

    public function hosting(): HasOne
    {
        return $this->hasOne(Hosting::class);
    }

    public function renewals(): HasMany
    {
        return $this->hasMany(Renewal::class);
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function expiryAlerts(): HasMany
    {
        return $this->hasMany(ExpiryAlert::class);
    }
}
