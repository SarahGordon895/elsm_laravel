<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class TestAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-auth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test authorization gates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Authorization Gates...');
        
        // Test each user role
        $roles = ['super_admin', 'admin', 'hr', 'head_of_department', 'employee'];
        
        foreach ($roles as $role) {
            $user = User::where('role', $role)->first();
            
            if ($user) {
                $this->info("\n--- Testing Role: {$role} ({$user->email}) ---");
                
                // Test dashboard access
                $canViewDashboard = Gate::forUser($user)->allows('view-dashboard');
                $this->info('View Dashboard: ' . ($canViewDashboard ? '✅' : '❌'));
                
                // Test user management
                $canViewUsers = Gate::forUser($user)->allows('view-users');
                $this->info('View Users: ' . ($canViewUsers ? '✅' : '❌'));
                
                // Test department management
                $canViewDepartments = Gate::forUser($user)->allows('view-departments');
                $this->info('View Departments: ' . ($canViewDepartments ? '✅' : '❌'));
                
                // Test reports
                $canViewReports = Gate::forUser($user)->allows('view-reports');
                $this->info('View Reports: ' . ($canViewReports ? '✅' : '❌'));
                
                // Test leave approval
                $canApproveLeave = Gate::forUser($user)->allows('approve-leave');
                $this->info('Approve Leave: ' . ($canApproveLeave ? '✅' : '❌'));
            } else {
                $this->error("No user found with role: {$role}");
            }
        }
        
        $this->info("\n✅ Authorization test completed!");
        return 0;
    }
}
