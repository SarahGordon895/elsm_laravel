<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\LeaveType;
use App\Models\Role;
use App\Models\Permission;
use App\Models\LeaveBalance;
use Illuminate\Support\Facades\Hash;

class ProfessionalDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            // User Management
            ['name' => 'view-users', 'display_name' => 'View Users', 'group' => 'User Management'],
            ['name' => 'create-users', 'display_name' => 'Create Users', 'group' => 'User Management'],
            ['name' => 'edit-users', 'display_name' => 'Edit Users', 'group' => 'User Management'],
            ['name' => 'delete-users', 'display_name' => 'Delete Users', 'group' => 'User Management'],
            ['name' => 'manage-user-roles', 'display_name' => 'Manage User Roles', 'group' => 'User Management'],
            
            // Leave Management
            ['name' => 'view-leave-applications', 'display_name' => 'View Leave Applications', 'group' => 'Leave Management'],
            ['name' => 'create-leave-applications', 'display_name' => 'Create Leave Applications', 'group' => 'Leave Management'],
            ['name' => 'edit-leave-applications', 'display_name' => 'Edit Leave Applications', 'group' => 'Leave Management'],
            ['name' => 'delete-leave-applications', 'display_name' => 'Delete Leave Applications', 'group' => 'Leave Management'],
            ['name' => 'approve-leave', 'display_name' => 'Approve Leave', 'group' => 'Leave Management'],
            ['name' => 'reject-leave', 'display_name' => 'Reject Leave', 'group' => 'Leave Management'],
            ['name' => 'manage-leave-balances', 'display_name' => 'Manage Leave Balances', 'group' => 'Leave Management'],
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
            
            // Department Management
            ['name' => 'view-departments', 'display_name' => 'View Departments', 'group' => 'Department Management'],
            ['name' => 'create-departments', 'display_name' => 'Create Departments', 'group' => 'Department Management'],
            ['name' => 'edit-departments', 'display_name' => 'Edit Departments', 'group' => 'Department Management'],
            ['name' => 'delete-departments', 'display_name' => 'Delete Departments', 'group' => 'Department Management'],
            
            // Reports
            ['name' => 'view-reports', 'display_name' => 'View Reports', 'group' => 'Reports'],
            ['name' => 'export-reports', 'display_name' => 'Export Reports', 'group' => 'Reports'],
            
            // System Administration
            ['name' => 'view-audit-logs', 'display_name' => 'View Audit Logs', 'group' => 'System Administration'],
            ['name' => 'manage-system-settings', 'display_name' => 'Manage System Settings', 'group' => 'System Administration'],
            ['name' => 'view-dashboard', 'display_name' => 'View Dashboard', 'group' => 'System Administration'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Create Roles
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'System Administrator',
                'description' => 'Full system access',
                'level' => 100,
                'is_system_role' => true,
            ],
            [
                'name' => 'hr',
                'display_name' => 'HR Manager',
                'description' => 'Human Resources management with leave plan oversight',
                'level' => 80,
                'is_system_role' => true,
            ],
            [
                'name' => 'head_of_department',
                'display_name' => 'Head of Department',
                'description' => 'Department leadership with leave approval authority',
                'level' => 70,
                'is_system_role' => true,
            ],
            [
                'name' => 'manager',
                'display_name' => 'Department Manager',
                'description' => 'Department and team management',
                'level' => 60,
                'is_system_role' => true,
            ],
            [
                'name' => 'employee',
                'display_name' => 'Employee',
                'description' => 'Regular employee',
                'level' => 20,
                'is_system_role' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        // Assign Permissions to Roles
        $adminRole = Role::where('name', 'admin')->first();
        $hrRole = Role::where('name', 'hr')->first();
        $headOfDepartmentRole = Role::where('name', 'head_of_department')->first();
        $managerRole = Role::where('name', 'manager')->first();
        $employeeRole = Role::where('name', 'employee')->first();

        // Admin gets all permissions
        $adminRole->permissions()->attach(Permission::all());

        // HR permissions - Enhanced with leave plan and notification management
        $hrPermissions = Permission::whereIn('group', [
            'User Management', 'Leave Management', 'Department Management', 'Reports', 'Notification Management'
        ])->get();
        $hrRole->permissions()->attach($hrPermissions);

        // Head of Department permissions - Department leadership with leave authority
        $hodPermissions = Permission::whereIn('group', [
            'Leave Management', 'Reports', 'Notification Management'
        ])->get();
        $hodPermissions = $hodPermissions->filter(function ($permission) {
            return !in_array($permission->name, ['manage-leave-balances', 'manage-user-roles', 'manage-departments']);
        });
        $headOfDepartmentRole->permissions()->attach($hodPermissions);

        // Manager permissions - Limited leave management
        $managerPermissions = Permission::whereIn('group', [
            'Leave Management', 'Reports'
        ])->get();
        $managerPermissions = $managerPermissions->filter(function ($permission) {
            return !in_array($permission->name, [
                'manage-leave-balances', 'manage-user-roles', 'manage-leave-plans', 
                'approve-leave-plans', 'reject-leave-plans', 'send-notifications'
            ]);
        });
        $managerRole->permissions()->attach($managerPermissions);

        // Employee permissions
        $employeePermissions = Permission::whereIn('name', [
            'view-leave-applications', 'create-leave-applications', 'edit-leave-applications', 
            'delete-leave-applications', 'view-dashboard', 'view-notifications'
        ])->get();
        $employeeRole->permissions()->attach($employeePermissions);

        // Create Departments
        $departments = [
            [
                'name' => 'Executive Management',
                'short_name' => 'EXEC',
                'code' => 'EXEC001',
                'description' => 'Executive leadership team',
                'manager_id' => null,
            ],
            [
                'name' => 'Human Resources',
                'short_name' => 'HR',
                'code' => 'HR001',
                'description' => 'Human Resources department',
                'manager_id' => null,
            ],
            [
                'name' => 'Information Technology',
                'short_name' => 'IT',
                'code' => 'IT001',
                'description' => 'IT and software development',
                'manager_id' => null,
            ],
            [
                'name' => 'Finance & Accounting',
                'short_name' => 'FIN',
                'code' => 'FIN001',
                'description' => 'Finance and accounting department',
                'manager_id' => null,
            ],
            [
                'name' => 'Marketing & Sales',
                'short_name' => 'MKT',
                'code' => 'MKT001',
                'description' => 'Marketing and sales team',
                'manager_id' => null,
            ],
            [
                'name' => 'Operations',
                'short_name' => 'OPS',
                'code' => 'OPS001',
                'description' => 'Operations and logistics',
                'manager_id' => null,
            ],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }

        // Create Professional Leave Types
        $leaveTypes = [
            [
                'name' => 'Annual Leave',
                'description' => 'Paid annual leave for vacation and personal time',
                'max_days_per_year' => 28,
                'requires_approval' => true,
                'requires_documentation' => false,
                'paid' => true,
                'carry_over_allowed' => true,
                'max_carry_over_days' => 10,
                'accrual_frequency' => 'monthly',
                'probation_restriction' => false,
            ],
            [
                'name' => 'Sick Leave',
                'description' => 'Paid leave for illness and medical appointments',
                'max_days_per_year' => 7,
                'requires_approval' => false,
                'requires_documentation' => true,
                'paid' => true,
                'carry_over_allowed' => false,
                'max_carry_over_days' => 0,
                'accrual_frequency' => 'monthly',
                'probation_restriction' => false,
            ],
            [
                'name' => 'Maternity Leave',
                'description' => 'Paid leave for female employees during pregnancy and post-delivery',
                'max_days_per_year' => 90,
                'requires_approval' => true,
                'requires_documentation' => true,
                'paid' => true,
                'carry_over_allowed' => false,
                'max_carry_over_days' => 0,
                'accrual_frequency' => 'one_time',
                'probation_restriction' => true,
            ],
            [
                'name' => 'Paternity Leave',
                'description' => 'Paid leave for male employees during childbirth',
                'max_days_per_year' => 7,
                'requires_approval' => true,
                'requires_documentation' => true,
                'paid' => true,
                'carry_over_allowed' => false,
                'max_carry_over_days' => 0,
                'accrual_frequency' => 'one_time',
                'probation_restriction' => true,
            ],
            [
                'name' => 'Study Leave',
                'description' => 'Leave for professional development and education',
                'max_days_per_year' => 5,
                'requires_approval' => true,
                'requires_documentation' => true,
                'paid' => false,
                'carry_over_allowed' => false,
                'max_carry_over_days' => 0,
                'accrual_frequency' => 'as_needed',
                'probation_restriction' => true,
            ],
            [
                'name' => 'Unpaid Leave',
                'description' => 'Unpaid leave for personal reasons',
                'max_days_per_year' => 30,
                'requires_approval' => true,
                'requires_documentation' => false,
                'paid' => false,
                'carry_over_allowed' => false,
                'max_carry_over_days' => 0,
                'accrual_frequency' => 'as_needed',
                'probation_restriction' => false,
            ],
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::create($leaveType);
        }

        // Create Professional Users
        $currentYear = date('Y');
        
        // System Administrator
        $admin = User::create([
            'employee_id' => 'ADM001',
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'email' => 'admin@company.com',
            'password' => Hash::make('admin123'),
            'gender' => 'Other',
            'department_id' => Department::where('code', 'EXEC001')->first()->id,
            'role' => 'admin',
            'status' => 'active',
            'employment_type' => 'full_time',
            'join_date' => now()->subYears(3),
        ]);
        $admin->roles()->attach($adminRole);

        // HR Manager
        $hrManager = User::create([
            'employee_id' => 'HRM001',
            'first_name' => 'Sarah',
            'last_name' => 'Johnson',
            'email' => 'sarah.johnson@company.com',
            'password' => Hash::make('hr123'),
            'gender' => 'Female',
            'department_id' => Department::where('code', 'HR001')->first()->id,
            'role' => 'hr',
            'status' => 'active',
            'employment_type' => 'full_time',
            'join_date' => now()->subYears(2),
        ]);
        $hrManager->roles()->attach($hrRole);

        // IT Manager
        $itManager = User::create([
            'employee_id' => 'ITM001',
            'first_name' => 'Michael',
            'last_name' => 'Chen',
            'email' => 'michael.chen@company.com',
            'password' => Hash::make('manager123'),
            'gender' => 'Male',
            'department_id' => Department::where('code', 'IT001')->first()->id,
            'role' => 'manager',
            'status' => 'active',
            'employment_type' => 'full_time',
            'join_date' => now()->subYears(2),
        ]);
        $itManager->roles()->attach($managerRole);

        // Finance Manager
        $financeManager = User::create([
            'employee_id' => 'FINM001',
            'first_name' => 'Emma',
            'last_name' => 'Williams',
            'email' => 'emma.williams@company.com',
            'password' => Hash::make('manager123'),
            'gender' => 'Female',
            'department_id' => Department::where('code', 'FIN001')->first()->id,
            'role' => 'manager',
            'status' => 'active',
            'employment_type' => 'full_time',
            'join_date' => now()->subYears(2),
        ]);
        $financeManager->roles()->attach($managerRole);

        // IT Employees
        $itEmployees = [
            [
                'employee_id' => 'ITE001',
                'first_name' => 'David',
                'last_name' => 'Brown',
                'email' => 'david.brown@company.com',
                'password' => 'emp123',
                'gender' => 'Male',
                'manager_id' => $itManager->id,
            ],
            [
                'employee_id' => 'ITE002',
                'first_name' => 'Lisa',
                'last_name' => 'Anderson',
                'email' => 'lisa.anderson@company.com',
                'password' => 'emp123',
                'gender' => 'Female',
                'manager_id' => $itManager->id,
            ],
            [
                'employee_id' => 'ITE003',
                'first_name' => 'James',
                'last_name' => 'Wilson',
                'email' => 'james.wilson@company.com',
                'password' => 'emp123',
                'gender' => 'Male',
                'manager_id' => $itManager->id,
            ],
        ];

        foreach ($itEmployees as $index => $employee) {
            $user = User::create(array_merge($employee, [
                'department_id' => Department::where('code', 'IT001')->first()->id,
                'role' => 'employee',
                'status' => 'active',
                'employment_type' => 'full_time',
                'join_date' => now()->subMonths($index * 6 + 3),
            ]));
            $user->roles()->attach($employeeRole);
        }

        // Finance Employees
        $financeEmployees = [
            [
                'employee_id' => 'FNE001',
                'first_name' => 'Robert',
                'last_name' => 'Taylor',
                'email' => 'robert.taylor@company.com',
                'password' => 'emp123',
                'gender' => 'Male',
                'manager_id' => $financeManager->id,
            ],
            [
                'employee_id' => 'FNE002',
                'first_name' => 'Jennifer',
                'last_name' => 'Martinez',
                'email' => 'jennifer.martinez@company.com',
                'password' => 'emp123',
                'gender' => 'Female',
                'manager_id' => $financeManager->id,
            ],
        ];

        foreach ($financeEmployees as $index => $employee) {
            $user = User::create(array_merge($employee, [
                'department_id' => Department::where('code', 'FIN001')->first()->id,
                'role' => 'employee',
                'status' => 'active',
                'employment_type' => 'full_time',
                'join_date' => now()->subMonths($index * 8 + 4),
            ]));
            $user->roles()->attach($employeeRole);
        }

        // Initialize Leave Balances for all users
        $allUsers = User::all();
        $leaveTypes = LeaveType::all();

        foreach ($allUsers as $user) {
            foreach ($leaveTypes as $leaveType) {
                // Initialize balance based on leave type and user tenure
                $balanceDays = $this->calculateInitialBalance($leaveType, $user);
                
                LeaveBalance::initializeBalance($user->id, $leaveType->id, $balanceDays, $currentYear);
            }
        }

        // Update department managers
        Department::where('code', 'EXEC001')->update(['manager_id' => $admin->id]);
        Department::where('code', 'HR001')->update(['manager_id' => $hrManager->id]);
        Department::where('code', 'IT001')->update(['manager_id' => $itManager->id]);
        Department::where('code', 'FIN001')->update(['manager_id' => $financeManager->id]);
        Department::where('code', 'MKT001')->update(['manager_id' => $financeManager->id]); // Assign to finance for now
        Department::where('code', 'OPS001')->update(['manager_id' => $itManager->id]); // Assign to IT for now
    }

    private function calculateInitialBalance($leaveType, $user)
    {
        $monthsWorked = $user->join_date ? min(12, $user->join_date->diffInMonths(now())) : 12;
        
        switch ($leaveType->name) {
            case 'Annual Leave':
                return round(($leaveType->max_days_per_year / 12) * $monthsWorked, 2);
            case 'Sick Leave':
                return round(($leaveType->max_days_per_year / 12) * $monthsWorked, 2);
            default:
                return $leaveType->max_days_per_year;
        }
    }
}
