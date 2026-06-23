<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Domain extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_service_id',
        'domain_name',
        'registrar',
        'registrar_account',
        'purchase_date',
        'expiry_date',
        'auto_renew',
        'dns_provider',
        'nameserver_1',
        'nameserver_2',
        'nameserver_3',
        'nameserver_4',
        'status',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'expiry_date' => 'date',
        'auto_renew' => 'boolean',
    ];

    public function customerService(): BelongsTo
    {
        return $this->belongsTo(CustomerService::class);
    }
}
