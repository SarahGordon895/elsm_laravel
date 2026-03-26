<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leaveTypes = [
            // Annual Leave - 21 days
            [
                'name' => 'Annual Leave',
                'description' => 'Regular annual leave entitlement for employees',
                'max_days_per_year' => 21,
                'requires_approval' => true,
                'requires_documentation' => false,
                'paid' => true,
                'carry_over_allowed' => true,
                'max_carry_over_days' => 5,
                'accrual_frequency' => 'monthly',
                'probation_restriction' => false,
                'is_active' => true,
            ],
            
            // Unpaid Leave - 30 days
            [
                'name' => 'Unpaid Leave',
                'description' => 'Leave without pay for extended periods',
                'max_days_per_year' => 30,
                'requires_approval' => true,
                'requires_documentation' => true,
                'paid' => false,
                'carry_over_allowed' => false,
                'max_carry_over_days' => 0,
                'accrual_frequency' => 'as_needed',
                'probation_restriction' => false,
                'is_active' => true,
            ],
            
            // Personal Leave - 7 days
            [
                'name' => 'Personal Leave',
                'description' => 'Leave for personal matters and appointments',
                'max_days_per_year' => 7,
                'requires_approval' => true,
                'requires_documentation' => false,
                'paid' => true,
                'carry_over_allowed' => false,
                'max_carry_over_days' => 0,
                'accrual_frequency' => 'monthly',
                'probation_restriction' => false,
                'is_active' => true,
            ],
            
            // Compassionate Leave - 5 days
            [
                'name' => 'Compassionate Leave',
                'description' => 'Leave for bereavement and family emergencies',
                'max_days_per_year' => 5,
                'requires_approval' => true,
                'requires_documentation' => true,
                'paid' => true,
                'carry_over_allowed' => false,
                'max_carry_over_days' => 0,
                'accrual_frequency' => 'as_needed',
                'probation_restriction' => false,
                'is_active' => true,
            ],
            
            // Maternity Leave - 90 days
            [
                'name' => 'Maternity Leave',
                'description' => 'Leave for pregnancy and childbirth',
                'max_days_per_year' => 90,
                'requires_approval' => true,
                'requires_documentation' => true,
                'paid' => true,
                'carry_over_allowed' => false,
                'max_carry_over_days' => 0,
                'accrual_frequency' => 'as_needed',
                'probation_restriction' => false,
                'is_active' => true,
            ],
            
            // Paternity Leave - 14 days
            [
                'name' => 'Paternity Leave',
                'description' => 'Leave for new fathers',
                'max_days_per_year' => 14,
                'requires_approval' => true,
                'requires_documentation' => true,
                'paid' => true,
                'carry_over_allowed' => false,
                'max_carry_over_days' => 0,
                'accrual_frequency' => 'as_needed',
                'probation_restriction' => false,
                'is_active' => true,
            ],
            
            // Sick Leave - 14 days
            [
                'name' => 'Sick Leave',
                'description' => 'Leave for medical reasons or illness',
                'max_days_per_year' => 14,
                'requires_approval' => true,
                'requires_documentation' => true,
                'paid' => true,
                'carry_over_allowed' => false,
                'max_carry_over_days' => 0,
                'accrual_frequency' => 'monthly',
                'probation_restriction' => false,
                'is_active' => true,
            ],
            
            // Study Leave - 28 days
            [
                'name' => 'Study Leave',
                'description' => 'Leave for educational and training purposes',
                'max_days_per_year' => 28,
                'requires_approval' => true,
                'requires_documentation' => true,
                'paid' => false,
                'carry_over_allowed' => false,
                'max_carry_over_days' => 0,
                'accrual_frequency' => 'monthly',
                'probation_restriction' => false,
                'is_active' => true,
            ],
            
            // Emergency Leave - 3 days
            [
                'name' => 'Emergency Leave',
                'description' => 'Leave for urgent personal matters',
                'max_days_per_year' => 3,
                'requires_approval' => false,
                'requires_documentation' => false,
                'paid' => true,
                'carry_over_allowed' => false,
                'max_carry_over_days' => 0,
                'accrual_frequency' => 'as_needed',
                'probation_restriction' => false,
                'is_active' => true,
            ],
        ];

        foreach ($leaveTypes as $leaveTypeData) {
            $leaveType = LeaveType::firstOrCreate(
                ['name' => $leaveTypeData['name']],
                $leaveTypeData
            );
            
            $this->command->info("Leave type created/updated: {$leaveType->name}");
        }
        
        $this->command->info('Leave type seeding completed successfully!');
    }
}
