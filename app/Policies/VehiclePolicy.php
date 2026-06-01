<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Auth\Access\HandlesAuthorization;

class VehiclePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'super-admin', 'branch_manager', 'sales_agent']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Vehicle $vehicle): bool
    {
        return $user->hasAnyRole(['super_admin', 'super-admin', 'branch_manager', 'sales_agent']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'super-admin', 'branch_manager']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Vehicle $vehicle): bool
    {
        return $user->hasAnyRole(['super_admin', 'super-admin', 'branch_manager']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Vehicle $vehicle): bool
    {
        return $user->hasAnyRole(['super_admin', 'super-admin', 'branch_manager']);
    }

    /**
     * Determine whether the user can update the status of the model.
     */
    public function updateStatus(User $user, Vehicle $vehicle): bool
    {
        return $user->hasAnyRole(['super_admin', 'super-admin', 'branch_manager']);
    }
}
