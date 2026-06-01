<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToBranch;

class ShowroomAppointment extends Model
{
    use BelongsToBranch;

    protected $guarded = [];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    /**
     * Get the customer who scheduled the appointment.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the vehicle of interest.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
