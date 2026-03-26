<?php

namespace App\Policies;

use App\Models\LeavePlan;
use App\Models\User;

class LeavePlanPolicy
{
    /**
     * Determine if the user can view any leave plan.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'super_admin' || 
               $user->role === 'admin' || 
               $user->role === 'hr' ||
               $user->role === 'head_of_department';
    }

    /**
     * Determine if the user can view the leave plan.
     */
    public function view(User $user, LeavePlan $leavePlan): bool
    {
        // Users can view their own leave plans
        if ($user->id === $leavePlan->user_id) {
            return true;
        }

        // Super admin, admin, HR can view all
        if ($user->role === 'super_admin' || $user->role === 'admin' || $user->role === 'hr') {
            return true;
        }

        // Head of department can view department leave plans
        if ($user->role === 'head_of_department' && $user->department_id === $leavePlan->user->department_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can create leave plans.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('manage-leave-plans');
    }

    /**
     * Determine if the user can update the leave plan.
     */
    public function update(User $user, LeavePlan $leavePlan): bool
    {
        // Users can update their own pending leave plans
        if ($user->id === $leavePlan->user_id && $leavePlan->status === 'pending') {
            return true;
        }

        // Super admin, admin, HR can update any leave plan
        if ($user->role === 'super_admin' || $user->role === 'admin' || $user->role === 'hr') {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can delete the leave plan.
     */
    public function delete(User $user, LeavePlan $leavePlan): bool
    {
        // Users can delete their own pending leave plans
        if ($user->id === $leavePlan->user_id && $leavePlan->status === 'pending') {
            return true;
        }

        // Super admin, admin can delete any leave plan
        if ($user->role === 'super_admin' || $user->role === 'admin') {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can approve the leave plan.
     */
    public function approve(User $user, LeavePlan $leavePlan): bool
    {
        return $user->hasPermission('approve-leave-plans') && $leavePlan->status === 'pending';
    }

    /**
     * Determine if the user can reject the leave plan.
     */
    public function reject(User $user, LeavePlan $leavePlan): bool
    {
        return $user->hasPermission('reject-leave-plans') && $leavePlan->status === 'pending';
    }
}
