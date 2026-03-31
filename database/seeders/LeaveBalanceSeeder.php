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
                $allocatedDays = $leaveType->max_days_per_year;
                
                // Only create balance if allocated days > 0
                if ($allocatedDays > 0) {
                    $balance = LeaveBalance::updateOrCreate(
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
    
}
