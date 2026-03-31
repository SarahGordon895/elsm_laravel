<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\LeaveApplication;
use App\Models\User;
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

        $hasAnyRole = static function ($user, array $roles): bool {
            $effectiveRole = method_exists($user, 'getEffectiveRole')
                ? $user->getEffectiveRole()
                : \App\Models\User::normalizeRoleName($user->role ?? '');

            return in_array($effectiveRole, $roles, true);
        };

        // Define permission gates for backward compatibility
        Gate::define('view-leave-applications', function ($user) use ($hasAnyRole) {
            return $hasAnyRole($user, ['super_admin', 'admin', 'hr', 'head_of_department', 'employee']);
        });

        Gate::define('create-leave-applications', function ($user) use ($hasAnyRole) {
            return $hasAnyRole($user, ['super_admin', 'admin', 'hr', 'head_of_department', 'employee']);
        });

        Gate::define('edit-leave-applications', function ($user) use ($hasAnyRole) {
            return $hasAnyRole($user, ['super_admin', 'admin', 'hr', 'head_of_department', 'employee']);
        });

        Gate::define('delete-leave-applications', function ($user) use ($hasAnyRole) {
            return $hasAnyRole($user, ['super_admin', 'admin', 'hr', 'head_of_department', 'employee']);
        });

        Gate::define('view-dashboard', function ($user) use ($hasAnyRole) {
            return $hasAnyRole($user, ['super_admin', 'admin', 'hr', 'head_of_department', 'employee']);
        });

        Gate::define('view-users', function ($user) use ($hasAnyRole) {
            return $hasAnyRole($user, ['super_admin', 'admin', 'hr']);
        });

        Gate::define('create-users', function ($user) use ($hasAnyRole) {
            return $hasAnyRole($user, ['super_admin', 'admin', 'hr']);
        });

        Gate::define('edit-users', function ($user) use ($hasAnyRole) {
            return $hasAnyRole($user, ['super_admin', 'admin', 'hr']);
        });

        Gate::define('view-departments', function ($user) use ($hasAnyRole) {
            return $hasAnyRole($user, ['super_admin', 'admin', 'hr', 'head_of_department']);
        });

        Gate::define('create-departments', function ($user) use ($hasAnyRole) {
            return $hasAnyRole($user, ['super_admin', 'admin', 'hr', 'head_of_department']);
        });

        Gate::define('edit-departments', function ($user) use ($hasAnyRole) {
            return $hasAnyRole($user, ['super_admin', 'admin', 'hr', 'head_of_department']);
        });

        Gate::define('delete-departments', function ($user) use ($hasAnyRole) {
            return $hasAnyRole($user, ['super_admin', 'admin', 'hr', 'head_of_department']);
        });

        Gate::define('view-reports', function ($user) use ($hasAnyRole) {
            return $hasAnyRole($user, ['super_admin', 'admin', 'hr', 'head_of_department']);
        });

        Gate::define('view-audit-logs', function ($user) use ($hasAnyRole) {
            return $hasAnyRole($user, ['super_admin', 'admin']);
        });

        Gate::define('manage-system-settings', function ($user) use ($hasAnyRole) {
            return $hasAnyRole($user, ['super_admin', 'admin']);
        });

        Gate::define('manage-leave-plans', function ($user) use ($hasAnyRole) {
            return $hasAnyRole($user, ['super_admin', 'admin', 'hr', 'head_of_department', 'employee']);
        });

        Gate::define('approve-leave-plans', function ($user) use ($hasAnyRole) {
            return $hasAnyRole($user, ['super_admin', 'admin', 'hr', 'head_of_department']);
        });

        Gate::define('reject-leave-plans', function ($user) use ($hasAnyRole) {
            return $hasAnyRole($user, ['super_admin', 'admin', 'hr', 'head_of_department']);
        });

        Gate::define('view-leave', function ($user, $leaveApplication) use ($hasAnyRole) {
            return $hasAnyRole($user, ['super_admin', 'admin', 'hr', 'head_of_department']) ||
                   $user->id === $leaveApplication->user_id;
        });

        Gate::define('approve-leave', function ($user) use ($hasAnyRole) {
            return $hasAnyRole($user, ['super_admin', 'admin', 'hr']);
        });

        Gate::define('reject-leave', function ($user) use ($hasAnyRole) {
            return $hasAnyRole($user, ['super_admin', 'admin', 'hr']);
        });

        // Super admin gate - can do everything
        Gate::before(function ($user, $ability) {
            if ((method_exists($user, 'getEffectiveRole') ? $user->getEffectiveRole() : ($user->role ?? null)) === 'super_admin') {
                return true;
            }
        });
    }
}
