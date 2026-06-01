<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealInstallment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_at' => 'date',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the associated sales deal.
     */
    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    /**
     * Get the payment transaction details.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(DealPayment::class, 'deal_payment_id');
    }
}
