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
            [
                'name' => 'Annual Leave',
                'description' => 'Main leave entitlement (also used for emergency deductions).',
                'max_days_per_year' => 28,
                'requires_approval' => true,
                'requires_documentation' => false,
                'paid' => true,
                'carry_over_allowed' => false,
                'max_carry_over_days' => 0,
                'accrual_frequency' => 'annually',
                'probation_restriction' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Sick Leave',
                'description' => 'Sick leave (5 days/year). Days beyond first day without doctor proof are deducted from annual leave.',
                'max_days_per_year' => 5,
                'requires_approval' => true,
                'requires_documentation' => true,
                'paid' => true,
                'carry_over_allowed' => false,
                'max_carry_over_days' => 0,
                'accrual_frequency' => 'annually',
                'probation_restriction' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Maternity Leave',
                'description' => 'Maternity leave entitlement.',
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
            [
                'name' => 'Paternity Leave',
                'description' => 'Paternity leave entitlement.',
                'max_days_per_year' => 7,
                'requires_approval' => true,
                'requires_documentation' => true,
                'paid' => true,
                'carry_over_allowed' => false,
                'max_carry_over_days' => 0,
                'accrual_frequency' => 'as_needed',
                'probation_restriction' => false,
                'is_active' => true,
            ],
        ];

        foreach ($leaveTypes as $leaveTypeData) {
            $leaveType = LeaveType::updateOrCreate(
                ['name' => $leaveTypeData['name']],
                $leaveTypeData
            );
            
            $this->command->info("Leave type created/updated: {$leaveType->name}");
        }
        
        LeaveType::whereNotIn('name', collect($leaveTypes)->pluck('name')->all())
            ->update(['is_active' => false]);

        $this->command->info('Leave type seeding completed successfully!');
    }
}
