<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class SyncUserRoleAssignmentsSeeder extends Seeder
{
    /**
     * Ensure users.role and role_user stay aligned.
     */
    public function run(): void
    {
        $roleIdsByName = Role::pluck('id', 'name');
        $preferredRoleByEmail = [
            'h.zuheri@imartgroup.co.tz' => 'super_admin',
            'm.gadi@imartgroup.co.tz' => 'head_of_department',
            'a.gonda@imartgroup.co.tz' => 'hr',
            'developers@imartgroup.co.tz' => 'admin',
        ];

        if ($roleIdsByName->isEmpty()) {
            $this->command->warn('No roles found. Skipping user role sync.');
            return;
        }

        $normalized = 0;
        $synced = 0;

        User::query()->with('roles')->chunkById(200, function ($users) use ($roleIdsByName, $preferredRoleByEmail, &$normalized, &$synced) {
            foreach ($users as $user) {
                $roleName = $user->role;

                if (isset($preferredRoleByEmail[$user->email])) {
                    $roleName = $preferredRoleByEmail[$user->email];
                }

                // Recover role from pivot assignment when role column drifted.
                if (
                    (!is_string($roleName) || $roleName === '' || $roleName === 'employee')
                    && $user->roles()->exists()
                ) {
                    $pivotRole = $user->roles()->orderByDesc('level')->value('name');
                    if (is_string($pivotRole) && $pivotRole !== '') {
                        $roleName = $pivotRole;
                    }
                }

                // Normalize legacy alias to canonical role key.
                if ($roleName === 'hod') {
                    $roleName = 'head_of_department';
                }

                // Fallback to employee if role key is unknown.
                if (!isset($roleIdsByName[$roleName])) {
                    $roleName = 'employee';
                }

                if ($user->role !== $roleName) {
                    $user->update(['role' => $roleName]);
                    $normalized++;
                }

                $user->roles()->sync([$roleIdsByName[$roleName]]);
                $synced++;
            }
        });

        $this->command->info("User role sync complete: {$synced} users processed, {$normalized} normalized.");
    }
}
