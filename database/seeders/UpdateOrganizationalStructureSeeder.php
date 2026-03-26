<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UpdateOrganizationalStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin role
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin'],
            [
                'display_name' => 'Super Administrator',
                'description' => 'System super administrator with full access',
                'level' => 100,
                'is_system_role' => true,
            ]
        );

        // Update Admin role description
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->update([
                'description' => 'System administrator with full access',
                'level' => 90,
            ]);
        }

        // Remove Manager role
        $managerRole = Role::where('name', 'manager')->first();
        if ($managerRole) {
            // Remove manager role from all users
            User::whereHas('roles', function($query) {
                $query->where('name', 'manager');
            })->get()->each(function($user) use ($managerRole) {
                $user->roles()->detach($managerRole->id);
            });
            
            // Delete the manager role
            $managerRole->delete();
            $this->command->info('Manager role removed from system');
        } else {
            $this->command->info('Manager role not found - skipping removal');
        }

        // Update existing admin users to be super admins
        $adminUsers = User::where('role', 'admin')->get();
        foreach ($adminUsers as $adminUser) {
            $adminUser->roles()->syncWithoutDetaching([$superAdminRole->id]);
            $adminUser->update(['role' => 'super_admin']);
            $this->command->info("Updated {$adminUser->email} to Super Admin");
        }

        // Create a dedicated Super Admin user
        $superAdminUser = User::firstOrCreate(
            ['email' => 'superadmin@company.com'],
            [
                'employee_id' => 'SUP001',
                'first_name' => 'Super',
                'last_name' => 'Administrator',
                'password' => Hash::make('superadmin123'),
                'gender' => 'Other',
                'department_id' => 1, // Executive Management
                'role' => 'super_admin',
                'status' => 'active',
                'employment_type' => 'full_time',
                'join_date' => now()->subYears(10),
            ]
        );
        
        $superAdminUser->roles()->syncWithoutDetaching([$superAdminRole->id]);
        $this->command->info("Super Admin user created: superadmin@company.com / superadmin123");

        // Update Head of Department users to be department managers
        $hodUsers = User::where('role', 'head_of_department')->get();
        foreach ($hodUsers as $hodUser) {
            // Update department manager_id to point to HOD
            User::where('department_id', $hodUser->department_id)
                ->where('role', 'employee')
                ->update(['manager_id' => $hodUser->id]);
            
            $this->command->info("Updated department {$hodUser->department_id} manager to {$hodUser->full_name}");
        }

        $this->command->info('Organizational structure updated successfully!');
        $this->command->info('New structure: Super Admin > Admin > HR > Head of Department > Employee');
        $this->command->info('Super Admin login: superadmin@company.com / superadmin123');
    }
}
