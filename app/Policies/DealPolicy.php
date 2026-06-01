<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Deal;
use Illuminate\Auth\Access\HandlesAuthorization;

class DealPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'super-admin', 'branch_manager', 'finance_officer', 'sales_agent']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Deal $deal): bool
    {
        if ($user->hasAnyRole(['super_admin', 'super-admin', 'branch_manager', 'finance_officer'])) {
            return true;
        }

        return $deal->salesperson_id === $user->id;
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
    public function update(User $user, Deal $deal): bool
    {
        if ($user->hasAnyRole(['super_admin', 'super-admin', 'branch_manager'])) {
            return true;
        }

        // Sales agents can update their own deals only if they are still in 'draft' or 'pending_approval' status
        if ($user->hasRole('sales_agent') && $deal->salesperson_id === $user->id) {
            return in_array($deal->status, ['draft', 'pending_approval']);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Deal $deal): bool
    {
        return $user->hasAnyRole(['super_admin', 'super-admin', 'branch_manager']);
    }

    /**
     * Determine whether the user can approve the deal.
     */
    public function approve(User $user, Deal $deal): bool
    {
        // Managers can approve any deal. Finance officers can approve deals that don't require manager approval (e.g. <= 200,000 SAR)
        if ($user->hasAnyRole(['super_admin', 'super-admin', 'branch_manager'])) {
            return true;
        }

        if ($user->hasRole('finance_officer')) {
            return !$deal->requiresApproval(); // Finance officer can approve if agreed price <= 200,000 SAR
        }

        return false;
    }
}
