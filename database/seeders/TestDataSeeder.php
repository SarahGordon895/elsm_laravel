<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        // Create departments
        DB::table('departments')->insert([
            ['name' => 'IT Department', 'description' => 'Information Technology', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'HR Department', 'description' => 'Human Resources', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Finance Department', 'description' => 'Finance and Accounting', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Create leave types
        DB::table('leave_types')->insert([
            ['name' => 'Annual Leave', 'description' => 'Standard annual leave entitlement', 'max_days_per_year' => 21, 'requires_approval' => true, 'requires_documentation' => false, 'paid' => true, 'carry_over_allowed' => true, 'max_carry_over_days' => 5, 'accrual_frequency' => 'monthly', 'probation_restriction' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sick Leave', 'description' => 'Leave for medical reasons', 'max_days_per_year' => 10, 'requires_approval' => true, 'requires_documentation' => true, 'paid' => true, 'carry_over_allowed' => false, 'max_carry_over_days' => 0, 'accrual_frequency' => 'monthly', 'probation_restriction' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Personal Leave', 'description' => 'Personal emergency leave', 'max_days_per_year' => 5, 'requires_approval' => true, 'requires_documentation' => false, 'paid' => false, 'carry_over_allowed' => false, 'max_carry_over_days' => 0, 'accrual_frequency' => 'annually', 'probation_restriction' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Create roles
        DB::table('roles')->insert([
            ['name' => 'super_admin', 'display_name' => 'Super Administrator', 'description' => 'System administrator', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'admin', 'display_name' => 'Administrator', 'description' => 'System administrator', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'hr', 'display_name' => 'HR Manager', 'description' => 'Human Resources Manager', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'head_of_department', 'display_name' => 'Head of Department', 'description' => 'Department Head', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'employee', 'display_name' => 'Employee', 'description' => 'Regular Employee', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Create test users
        $users = [
            [
                'employee_id' => 'SYS001',
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'email' => 'admin@elsm.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active',
                'department_id' => 1,
                'join_date' => Carbon::now()->subYears(2),
                'gender' => 'Male',
                'phone_number' => '+1234567890',
                'address' => '123 Admin Street',
                'city' => 'Admin City',
                'country' => 'Admin Country',
                'employment_type' => 'full_time',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employee_id' => 'HR001',
                'first_name' => 'HR',
                'last_name' => 'Manager',
                'email' => 'hr@elsm.com',
                'password' => Hash::make('hr123'),
                'role' => 'hr',
                'status' => 'active',
                'department_id' => 2,
                'join_date' => Carbon::now()->subYears(1),
                'gender' => 'Female',
                'phone_number' => '+1234567891',
                'address' => '456 HR Street',
                'city' => 'HR City',
                'country' => 'HR Country',
                'employment_type' => 'full_time',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employee_id' => 'EMP001',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@elsm.com',
                'password' => Hash::make('emp123'),
                'role' => 'employee',
                'status' => 'active',
                'department_id' => 1,
                'join_date' => Carbon::now()->subMonths(6),
                'gender' => 'Male',
                'phone_number' => '+1234567892',
                'address' => '789 Employee Street',
                'city' => 'Employee City',
                'country' => 'Employee Country',
                'employment_type' => 'full_time',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert($user);
        }

        $this->command->info('Test data seeded successfully!');
    }
}
