<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Renewal extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_service_id',
        'renewal_date',
        'old_expiry',
        'new_expiry',
        'amount',
        'invoice_id',
        'status',
        'created_by',
    ];

    protected $casts = [
        'renewal_date' => 'date',
        'old_expiry' => 'date',
        'new_expiry' => 'date',
        'amount' => 'decimal:2',
    ];

    public function customerService(): BelongsTo
    {
        return $this->belongsTo(CustomerService::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
