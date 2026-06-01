<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrder extends Model
{
    protected $guarded = [];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'purchased_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Get the supplier who sold the vehicle.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the vehicle purchased.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
