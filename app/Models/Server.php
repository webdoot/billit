<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Server extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'provider',
        'hostname',
        'ip_address',
        'location',
        'monthly_cost',
        'renewal_date',
        'notes',
        'status',
    ];

    protected $casts = [
        'monthly_cost' => 'decimal:2',
        'renewal_date' => 'date',
    ];

    public function hostings(): HasMany
    {
        return $this->hasMany(Hosting::class);
    }
}
