<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ResetAdminPortalPasswordsSeeder extends Seeder
{
    /**
     * Reset administrator-portal role passwords to a known default.
     */
    public function run(): void
    {
        $adminPortalRoles = ['super_admin', 'superadmin', 'admin', 'hr', 'head_of_department', 'hod'];
        $passwordHash = Hash::make('password123');

        // Role stored directly on users.role column.
        $updatedByColumn = User::whereIn('role', $adminPortalRoles)->update([
            'password' => $passwordHash,
        ]);

        // Role stored in role_user pivot (handles drifted role column).
        $roleIds = Role::whereIn('name', ['super_admin', 'admin', 'hr', 'head_of_department'])->pluck('id');
        $userIds = \DB::table('role_user')
            ->whereIn('role_id', $roleIds)
            ->pluck('user_id')
            ->unique()
            ->values();

        $updatedByPivot = 0;
        if ($userIds->isNotEmpty()) {
            $updatedByPivot = User::whereIn('id', $userIds)->update([
                'password' => $passwordHash,
            ]);
        }

        $this->command->info("Admin-portal passwords reset by role column: {$updatedByColumn}");
        $this->command->info("Admin-portal passwords reset by role pivot: {$updatedByPivot}");
        $this->command->info('Administrator portal login password: password123');
    }
}

