<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'super-admin', 'branch_manager', 'sales_agent']);
    }

    public function view(User $user, Customer $customer): bool
    {
        return $user->hasAnyRole(['super_admin', 'super-admin', 'branch_manager', 'sales_agent']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'super-admin', 'branch_manager', 'sales_agent']);
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->hasAnyRole(['super_admin', 'super-admin', 'branch_manager', 'sales_agent']);
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->hasAnyRole(['super_admin', 'super-admin', 'branch_manager']);
    }
}
