<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class FixSuperAdminPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Super Admin role
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if (!$superAdminRole) {
            $this->command->error('Super Admin role not found!');
            return;
        }

        // Get all permissions
        $allPermissions = Permission::all();
        
        // Assign all permissions to Super Admin role
        $superAdminRole->permissions()->sync($allPermissions->pluck('id'));
        
        $this->command->info('Super Admin role updated with all permissions');

        // Get Super Admin user
        $superAdminUser = User::where('email', 'superadmin@company.com')->first();
        if (!$superAdminUser) {
            $this->command->error('Super Admin user not found!');
            return;
        }

        // Ensure user has Super Admin role
        $superAdminUser->roles()->sync([$superAdminRole->id]);
        $superAdminUser->update(['role' => 'super_admin']);
        
        $this->command->info('Super Admin user updated with Super Admin role');

        // Check user permissions
        $userPermissions = $superAdminUser->permissions()->count();
        $this->command->info("Super Admin user now has {$userPermissions} permissions");

        // Test specific permissions needed for leave plans
        $requiredPermissions = ['manage-leave-plans', 'approve-leave-plans', 'reject-leave-plans'];
        foreach ($requiredPermissions as $perm) {
            $hasPermission = $superAdminUser->hasPermission($perm);
            $status = $hasPermission ? '✓' : '✗';
            $this->command->info("{$status} {$perm}: " . ($hasPermission ? 'Granted' : 'Denied'));
        }

        $this->command->info('Super Admin permissions fixed successfully!');
        $this->command->info('Try accessing leave plans again.');
    }
}
