<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_service_id',
        'server_id',
        'hosting_type',
        'control_panel',
        'username',
        'disk_limit',
        'bandwidth_limit',
        'status',
    ];

    public function customerService(): BelongsTo
    {
        return $this->belongsTo(CustomerService::class);
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
}
