<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Lead;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeadPolicy
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
    public function view(User $user, Lead $lead): bool
    {
        if ($user->hasAnyRole(['super_admin', 'super-admin', 'branch_manager'])) {
            return true;
        }

        return $lead->assigned_to === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'super-admin', 'branch_manager', 'sales_agent']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Lead $lead): bool
    {
        if ($user->hasAnyRole(['super_admin', 'super-admin', 'branch_manager'])) {
            return true;
        }

        return $lead->assigned_to === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Lead $lead): bool
    {
        if ($user->hasAnyRole(['super_admin', 'super-admin', 'branch_manager'])) {
            return true;
        }

        return $lead->assigned_to === $user->id;
    }
}
