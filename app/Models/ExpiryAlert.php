<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpiryAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_service_id',
        'days_before',
        'alert_date',
        'is_read',
    ];

    protected $casts = [
        'alert_date' => 'date',
        'is_read' => 'boolean',
    ];

    public function customerService(): BelongsTo
    {
        return $this->belongsTo(CustomerService::class);
    }
}
