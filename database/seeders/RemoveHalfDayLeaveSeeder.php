<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RemoveHalfDayLeaveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find and remove Half Day Leave type
        $halfDayLeave = LeaveType::where('name', 'Half Day Leave')->first();
        
        if ($halfDayLeave) {
            // Check if there are any leave applications using this type
            $applicationsCount = $halfDayLeave->leaveApplications()->count();
            
            if ($applicationsCount > 0) {
                // If there are applications, mark as inactive instead of deleting
                $halfDayLeave->update(['is_active' => false]);
                $this->command->info('Half Day Leave marked as inactive (had ' . $applicationsCount . ' applications)');
            } else {
                // If no applications, delete the leave type
                $halfDayLeave->delete();
                $this->command->info('Half Day Leave deleted successfully');
            }
            
            // Remove any leave balances for this leave type
            DB::table('leave_balances')
                ->where('leave_type_id', $halfDayLeave->id)
                ->delete();
                
            $this->command->info('Leave balances for Half Day Leave removed');
        } else {
            $this->command->info('Half Day Leave not found in database');
        }
    }
}
