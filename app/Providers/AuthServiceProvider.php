<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Policies\LeaveApplicationPolicy;
use App\Policies\UserPolicy;
use App\Models\LeavePlan;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        LeaveApplication::class => LeaveApplicationPolicy::class,
        User::class => UserPolicy::class,
        LeavePlan::class => \App\Policies\LeavePlanPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Define permission gates for backward compatibility
        Gate::define('view-leave-applications', function ($user) {
            return in_array($user->role, ['super_admin', 'admin', 'hr', 'head_of_department', 'employee']);
        });

        Gate::define('create-leave-applications', function ($user) {
            return in_array($user->role, ['super_admin', 'admin', 'hr', 'head_of_department', 'employee']);
        });

        Gate::define('edit-leave-applications', function ($user) {
            return in_array($user->role, ['super_admin', 'admin', 'hr', 'head_of_department', 'employee']);
        });

        Gate::define('delete-leave-applications', function ($user) {
            return in_array($user->role, ['super_admin', 'admin', 'hr', 'head_of_department', 'employee']);
        });

        Gate::define('view-dashboard', function ($user) {
            return in_array($user->role, ['super_admin', 'admin', 'hr', 'head_of_department', 'employee']);
        });

        Gate::define('view-users', function ($user) {
            return in_array($user->role, ['super_admin', 'admin', 'hr']);
        });

        Gate::define('create-users', function ($user) {
            return in_array($user->role, ['super_admin', 'admin', 'hr']);
        });

        Gate::define('edit-users', function ($user) {
            return in_array($user->role, ['super_admin', 'admin', 'hr']);
        });

        Gate::define('view-departments', function ($user) {
            return in_array($user->role, ['super_admin', 'admin', 'hr', 'head_of_department']);
        });

        Gate::define('create-departments', function ($user) {
            return in_array($user->role, ['super_admin', 'admin', 'hr']);
        });

        Gate::define('edit-departments', function ($user) {
            return in_array($user->role, ['super_admin', 'admin', 'hr']);
        });

        Gate::define('delete-departments', function ($user) {
            return in_array($user->role, ['super_admin', 'admin']);
        });

        Gate::define('view-reports', function ($user) {
            return in_array($user->role, ['super_admin', 'admin', 'hr', 'head_of_department']);
        });

        Gate::define('view-audit-logs', function ($user) {
            return in_array($user->role, ['super_admin', 'admin']);
        });

        Gate::define('manage-system-settings', function ($user) {
            return in_array($user->role, ['super_admin', 'admin']);
        });

        Gate::define('manage-leave-plans', function ($user) {
            return in_array($user->role, ['super_admin', 'admin', 'hr', 'head_of_department', 'employee']);
        });

        Gate::define('approve-leave-plans', function ($user) {
            return in_array($user->role, ['super_admin', 'admin', 'hr', 'head_of_department']);
        });

        Gate::define('reject-leave-plans', function ($user) {
            return in_array($user->role, ['super_admin', 'admin', 'hr', 'head_of_department']);
        });

        Gate::define('view-leave', function ($user, $leaveApplication) {
            return in_array($user->role, ['super_admin', 'admin', 'hr', 'head_of_department']) || 
                   $user->id === $leaveApplication->user_id;
        });

        Gate::define('approve-leave', function ($user) {
            return in_array($user->role, ['super_admin', 'admin', 'hr', 'head_of_department']);
        });

        Gate::define('reject-leave', function ($user) {
            return in_array($user->role, ['super_admin', 'admin', 'hr', 'head_of_department']);
        });

        // Super admin gate - can do everything
        Gate::before(function ($user, $ability) {
            if ($user->role === 'super_admin') {
                return true;
            }
        });
    }
}
