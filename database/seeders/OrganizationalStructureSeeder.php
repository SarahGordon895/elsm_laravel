<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeaveType;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class OrganizationalStructureSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data
        DB::table('leave_types')->delete();
        DB::table('departments')->delete();

        // Create Standard Leave Types
        $leaveTypes = [
            [
                'name' => 'Annual Leave',
                'description' => 'Standard annual leave entitlement for all employees',
                'max_days_per_year' => 21,
                'requires_approval' => true,
                'requires_documentation' => false,
                'paid' => true,
                'carry_over_allowed' => true,
                'max_carry_over_days' => 5,
                'accrual_frequency' => 'monthly',
                'probation_restriction' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sick Leave',
                'description' => 'Leave for illness or medical appointments',
                'max_days_per_year' => 10,
                'requires_approval' => true,
                'requires_documentation' => true,
                'paid' => true,
                'carry_over_allowed' => false,
                'max_carry_over_days' => 0,
                'accrual_frequency' => 'monthly',
                'probation_restriction' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Maternity Leave',
                'description' => 'Leave for female employees for childbirth',
                'max_days_per_year' => 90,
                'requires_approval' => true,
                'requires_documentation' => true,
                'paid' => true,
                'carry_over_allowed' => false,
                'max_carry_over_days' => 0,
                'accrual_frequency' => 'once',
                'probation_restriction' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Paternity Leave',
                'description' => 'Leave for male employees for childbirth support',
                'max_days_per_year' => 14,
                'requires_approval' => true,
                'requires_documentation' => true,
                'paid' => true,
                'carry_over_allowed' => false,
                'max_carry_over_days' => 0,
                'accrual_frequency' => 'once',
                'probation_restriction' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Compassionate Leave',
                'description' => 'Leave for bereavement or family emergencies',
                'max_days_per_year' => 3,
                'requires_approval' => true,
                'requires_documentation' => false,
                'paid' => true,
                'carry_over_allowed' => false,
                'max_carry_over_days' => 0,
                'accrual_frequency' => 'as_needed',
                'probation_restriction' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Study Leave',
                'description' => 'Leave for educational purposes and professional development',
                'max_days_per_year' => 5,
                'requires_approval' => true,
                'requires_documentation' => true,
                'paid' => true,
                'carry_over_allowed' => false,
                'max_carry_over_days' => 0,
                'accrual_frequency' => 'as_needed',
                'probation_restriction' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Unpaid Leave',
                'description' => 'Leave without pay for personal reasons',
                'max_days_per_year' => 30,
                'requires_approval' => true,
                'requires_documentation' => false,
                'paid' => false,
                'carry_over_allowed' => false,
                'max_carry_over_days' => 0,
                'accrual_frequency' => 'as_needed',
                'probation_restriction' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Emergency Leave',
                'description' => 'Emergency leave for unexpected situations',
                'max_days_per_year' => 2,
                'requires_approval' => false,
                'requires_documentation' => false,
                'paid' => true,
                'carry_over_allowed' => false,
                'max_carry_over_days' => 0,
                'accrual_frequency' => 'as_needed',
                'probation_restriction' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        LeaveType::insert($leaveTypes);

        // Create Organizational Department Structure
        $departments = [
            [
                'name' => 'Executive Management',
                'description' => 'Senior leadership and strategic decision-making',
                'manager_email' => 'ceo@company.com',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Human Resources',
                'description' => 'Employee management, recruitment, and HR operations',
                'manager_email' => 'hr@company.com',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Finance Department',
                'description' => 'Financial planning, accounting, and budget management',
                'manager_email' => 'finance@company.com',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'IT Department',
                'description' => 'Technology infrastructure and digital solutions',
                'manager_email' => 'it@company.com',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Operations Department',
                'description' => 'Core business operations and service delivery',
                'manager_email' => 'operations@company.com',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Marketing Department',
                'description' => 'Brand management, marketing campaigns, and communications',
                'manager_email' => 'marketing@company.com',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sales Department',
                'description' => 'Sales operations, client relations, and revenue generation',
                'manager_email' => 'sales@company.com',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Legal Department',
                'description' => 'Legal compliance, contracts, and risk management',
                'manager_email' => 'legal@company.com',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Quality Assurance',
                'description' => 'Quality control, testing, and process improvement',
                'manager_email' => 'qa@company.com',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Department::insert($departments);

        $this->command->info('Organizational structure seeded successfully!');
        $this->command->info('Leave Types: ' . count($leaveTypes));
        $this->command->info('Departments: ' . count($departments));
    }
}
