<?php

namespace App\Policies;

use App\Models\LeaveApplication;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LeaveApplicationPolicy
{
    public function view(User $user, LeaveApplication $leaveApplication): bool
    {
        // Admin and HR can view all applications
        if ($user->isAdmin() || $user->isHR()) {
            return true;
        }
        
        // Managers can view their subordinates' applications
        if ($user->isManager()) {
            return $user->subordinates()->where('id', $leaveApplication->user_id)->exists();
        }
        
        // Users can view their own applications
        return $user->id === $leaveApplication->user_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create-leave-applications');
    }

    public function update(User $user, LeaveApplication $leaveApplication): bool
    {
        // Users can only edit their own pending applications
        return $user->id === $leaveApplication->user_id && 
               $leaveApplication->status === 'pending' &&
               $user->hasPermission('edit-leave-applications');
    }

    public function delete(User $user, LeaveApplication $leaveApplication): bool
    {
        // Users can only delete their own pending applications
        return $user->id === $leaveApplication->user_id && 
               $leaveApplication->status === 'pending' &&
               $user->hasPermission('delete-leave-applications');
    }

    public function approve(User $user, LeaveApplication $leaveApplication): bool
    {
        return $user->hasPermission('approve-leave') && 
               $user->canApproveLeave($leaveApplication);
    }

    public function reject(User $user, LeaveApplication $leaveApplication): bool
    {
        return $user->hasPermission('reject-leave') && 
               $user->canApproveLeave($leaveApplication);
    }
}
