<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $guarded = [];

    /**
     * Get all CRM leads generated for the customer.
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * Get all deals purchased by the customer.
     */
    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    /**
     * Get all showroom appointments scheduled by the customer.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(ShowroomAppointment::class);
    }
}
