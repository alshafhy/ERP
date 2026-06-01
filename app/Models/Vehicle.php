<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Models\Traits\BelongsToBranch;

class Vehicle extends Model implements HasMedia
{
    use InteractsWithMedia, BelongsToBranch;

    protected $guarded = [];

    protected $casts = [
        'year' => 'integer',
        'mileage' => 'integer',
        'cost_price' => 'decimal:2',
        'listing_price' => 'decimal:2',
    ];

    /**
     * Stock Aging Indicator: Days since added to inventory.
     */
    public function getDaysInStockAttribute(): int
    {
        return Carbon::parse($this->created_at)->diffInDays(Carbon::now());
    }

    /**
     * Get the purchase order associated with the vehicle.
     */
    public function purchaseOrder(): HasOne
    {
        return $this->hasOne(PurchaseOrder::class);
    }

    /**
     * Get the active or completed deal for this vehicle.
     */
    public function deal(): HasOne
    {
        return $this->hasOne(Deal::class);
    }

    /**
     * Get the appointments scheduled for this vehicle.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(ShowroomAppointment::class);
    }

    /**
     * Get the status logs for this vehicle.
     */
    public function statusLogs(): HasMany
    {
        return $this->hasMany(VehicleStatusLog::class)->orderBy('changed_at', 'desc');
    }
}
