<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Traits\BelongsToBranch;

class User extends Authenticatable
{
    use HasRoles, BelongsToBranch;

    protected $fillable = [
        'name',
        'email',
        'password',
        'position',
        'phone',
        'avatar',
        'branch_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the leads assigned to this sales agent.
     */
    public function leads()
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    /**
     * Get the deals managed by this sales agent.
     */
    public function deals()
    {
        return $this->hasMany(Deal::class, 'salesperson_id');
    }
}
