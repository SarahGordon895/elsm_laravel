<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\LeaveApplication;
use App\Models\LeaveBalance;
use App\Models\Notification;

class TestDashboard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-dashboard';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test dashboard data and create sample notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Dashboard Data...');
        
        // Test employee dashboard data
        $employee = User::where('role', 'employee')->first();
        if ($employee) {
            $this->info("\n--- Testing Employee Dashboard for: {$employee->email} ---");
            
            // Get leave stats
            $stats = [
                'total_leaves_applied' => LeaveApplication::where('user_id', $employee->id)->count(),
                'pending_leaves' => LeaveApplication::where('user_id', $employee->id)->where('status', 'pending')->count(),
                'approved_leaves' => LeaveApplication::where('user_id', $employee->id)->where('status', 'approved')->count(),
                'rejected_leaves' => LeaveApplication::where('user_id', $employee->id)->where('status', 'rejected')->count(),
            ];
            
            $this->info('Total Leaves Applied: ' . $stats['total_leaves_applied']);
            $this->info('Pending Leaves: ' . $stats['pending_leaves']);
            $this->info('Approved Leaves: ' . $stats['approved_leaves']);
            $this->info('Rejected Leaves: ' . $stats['rejected_leaves']);
            
            // Get leave balances
            $leaveBalances = LeaveBalance::with('leaveType')
                ->where('user_id', $employee->id)
                ->where('year', date('Y'))
                ->get();
            
            $this->info('Leave Balances: ' . $leaveBalances->count());
            foreach ($leaveBalances as $balance) {
                $leaveTypeName = $balance->leave_type ? $balance->leave_type->name : 'Unknown';
                $remainingDays = $balance->balance_days - $balance->used_days;
                $this->info('  - ' . $leaveTypeName . ': ' . $remainingDays . ' days remaining');
            }
            
            // Create sample notifications if none exist
            try {
                $notifications = Notification::where('user_id', $employee->id)->get();
                if ($notifications->count() === 0) {
                    $this->info('Creating sample notifications...');
                    
                    Notification::create([
                        'user_id' => $employee->id,
                        'title' => 'Welcome to ELMS',
                        'message' => 'Your employee dashboard is ready! You can now apply for leave and track your applications.',
                        'type' => 'info',
                        'read' => false,
                    ]);
                    
                    if ($stats['pending_leaves'] > 0) {
                        Notification::create([
                            'user_id' => $employee->id,
                            'title' => 'Leave Application Pending',
                            'message' => 'You have ' . $stats['pending_leaves'] . ' leave application(s) awaiting approval.',
                            'type' => 'info',
                            'read' => false,
                        ]);
                    }
                    
                    $this->info('✅ Sample notifications created');
                } else {
                    $this->info('Existing Notifications: ' . $notifications->count());
                }
            } catch (\Exception $e) {
                $this->info('Notifications table not properly set up - skipping notifications');
            }
        }
        
        $this->info("\n✅ Dashboard test completed!");
        return 0;
    }
}
