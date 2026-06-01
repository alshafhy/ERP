<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\BelongsToBranch;

class Deal extends Model
{
    use BelongsToBranch;

    protected $guarded = [];

    protected $casts = [
        'agreed_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'trade_in_value' => 'decimal:2',
        'final_price' => 'decimal:2',
    ];

    /**
     * Get the vehicle involved in this deal.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the customer who purchased the vehicle.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the salesperson who closed the deal.
     */
    public function salesperson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'salesperson_id');
    }

    /**
     * Get all payments made for this deal.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(DealPayment::class);
    }

    /**
     * Get all installments scheduled for this deal.
     */
    public function installments(): HasMany
    {
        return $this->hasMany(DealInstallment::class)->orderBy('installment_number', 'asc');
    }

    /**
     * Helper to check if a deal requires manager approval.
     * Rule: agreed price > 200,000 SAR
     */
    public function requiresApproval(): bool
    {
        return $this->agreed_price > 200000.00;
    }
}
