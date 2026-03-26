<?php

namespace Database\Seeders;

use App\Models\LeaveBalance;
use App\Models\User;
use App\Models\LeaveType;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class LeaveBalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $leaveTypes = LeaveType::where('is_active', true)->get();
        
        foreach ($users as $user) {
            foreach ($leaveTypes as $leaveType) {
                // Calculate allocated days based on role and leave type
                $allocatedDays = $this->calculateAllocatedDays($user, $leaveType);
                
                // Only create balance if allocated days > 0
                if ($allocatedDays > 0) {
                    $balance = LeaveBalance::firstOrCreate(
                        [
                            'user_id' => $user->id,
                            'leave_type_id' => $leaveType->id,
                            'year' => Carbon::now()->year,
                        ],
                        [
                            'balance_days' => $allocatedDays,
                            'used_days' => 0,
                            'carry_over_days' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                    
                    $this->command->info("Leave balance created: {$user->full_name} - {$leaveType->name} ({$allocatedDays} days)");
                }
            }
        }
        
        $this->command->info('Leave balance seeding completed successfully!');
    }
    
    /**
     * Calculate allocated days based on user role and leave type
     */
    private function calculateAllocatedDays($user, $leaveType): int
    {
        $baseDays = $leaveType->max_days_per_year;
        
        // Adjust based on user role
        switch ($user->role) {
            case 'super_admin':
            case 'admin':
            case 'hr':
                // Admin staff get full allocation
                return $baseDays;
                
            case 'head_of_department':
                // HOD gets slightly more than regular employees
                return (int) ($baseDays * 1.2);
                
            case 'employee':
                // Regular employees get standard allocation
                return $baseDays;
                
            default:
                return $baseDays;
        }
    }
}
