<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class NewRolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add new permissions for leave plans and notifications
        $newPermissions = [
            // Leave Management
            ['name' => 'manage-leave-plans', 'display_name' => 'Manage Leave Plans', 'group' => 'Leave Management'],
            ['name' => 'approve-leave-plans', 'display_name' => 'Approve Leave Plans', 'group' => 'Leave Management'],
            ['name' => 'reject-leave-plans', 'display_name' => 'Reject Leave Plans', 'group' => 'Leave Management'],
            ['name' => 'view-department-leave', 'display_name' => 'View Department Leave', 'group' => 'Leave Management'],
            
            // Notification Management
            ['name' => 'send-notifications', 'display_name' => 'Send Notifications', 'group' => 'Notification Management'],
            ['name' => 'manage-notifications', 'display_name' => 'Manage Notifications', 'group' => 'Notification Management'],
            ['name' => 'view-notifications', 'display_name' => 'View Notifications', 'group' => 'Notification Management'],
            ['name' => 'send-leave-notifications', 'display_name' => 'Send Leave Notifications', 'group' => 'Notification Management'],
            ['name' => 'manage-leave-plan-notifications', 'display_name' => 'Manage Leave Plan Notifications', 'group' => 'Notification Management'],
        ];

        foreach ($newPermissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                [
                    'display_name' => $permission['display_name'],
                    'group' => $permission['group'],
                ]
            );
        }

        // Create Head of Department role
        $hodRole = Role::firstOrCreate(
            ['name' => 'head_of_department'],
            [
                'display_name' => 'Head of Department',
                'description' => 'Department leadership with leave approval authority',
                'level' => 70,
                'is_system_role' => true,
            ]
        );

        // Update HR role description
        $hrRole = Role::where('name', 'hr')->first();
        if ($hrRole) {
            $hrRole->update([
                'description' => 'Human Resources management with leave plan oversight',
            ]);
        }

        // Assign permissions to Head of Department
        $hodPermissions = Permission::whereIn('group', [
            'Leave Management', 'Reports', 'Notification Management'
        ])->get();
        
        $hodPermissions = $hodPermissions->filter(function ($permission) {
            return !in_array($permission->name, ['manage-leave-balances', 'manage-user-roles', 'manage-departments']);
        });
        
        $hodRole->permissions()->syncWithoutDetaching($hodPermissions->pluck('id'));

        // Add new permissions to HR role
        if ($hrRole) {
            $hrNewPermissions = Permission::whereIn('name', [
                'manage-leave-plans', 'approve-leave-plans', 'reject-leave-plans',
                'send-notifications', 'manage-notifications', 'view-notifications',
                'send-leave-notifications', 'manage-leave-plan-notifications'
            ])->get();
            
            $hrRole->permissions()->syncWithoutDetaching($hrNewPermissions->pluck('id'));
        }

        // Add view-notifications to employee role
        $employeeRole = Role::where('name', 'employee')->first();
        if ($employeeRole) {
            $viewNotificationPermission = Permission::where('name', 'view-notifications')->first();
            if ($viewNotificationPermission) {
                $employeeRole->permissions()->syncWithoutDetaching([$viewNotificationPermission->id]);
            }
        }

        $this->command->info('New roles and permissions seeded successfully!');
    }
}
