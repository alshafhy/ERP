<?php

namespace App\Models\Traits;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToBranch
{
    /**
     * Boot the trait and apply the branch filter scope globally.
     */
    protected static function bootBelongsToBranch(): void
    {
        // 1. Automatically scope queries by the authenticated user's branch
        static::addGlobalScope('branch_scope', function (Builder $builder) {
            static $resolvingUser = false;

            if ($resolvingUser) {
                return;
            }

            $isUserModel = $builder->getModel() instanceof \App\Models\User;

            if (Auth::hasUser() || (!$isUserModel && Auth::check())) {
                $resolvingUser = true;
                try {
                    $user = Auth::user();
                    if ($user && !$user->hasRole('super_admin') && !$user->hasRole('super-admin') && $user->branch_id) {
                        $builder->where($builder->getModel()->getTable() . '.branch_id', $user->branch_id);
                    }
                } finally {
                    $resolvingUser = false;
                }
            }
        });

        // 2. Automatically assign the active branch ID during database insertion
        static::creating(function ($model) {
            if (Auth::check()) {
                $user = Auth::user();
                if (!$model->branch_id && $user->branch_id) {
                    $model->branch_id = $user->branch_id;
                }
            }
        });
    }

    /**
     * Get the branch that owns this model.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
