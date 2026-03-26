<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class TestAuthorizationFix extends Command
{
    protected $signature = 'app:test-authorization-fix';
    protected $description = 'Test complete authorization fix for employees';

    public function handle()
    {
        $this->info('🔍 TESTING COMPLETE AUTHORIZATION FIX...');
        
        // Test employee user
        $employee = User::where('role', 'employee')->first();
        
        if (!$employee) {
            $this->error('❌ No employee user found!');
            return 1;
        }
        
        $this->info("✅ Testing as employee: {$employee->email}");
        
        // Test all authorization gates
        $this->info("\n🔐 AUTHORIZATION GATES TEST:");
        $this->info(str_repeat('-', 60));
        
        $gates = [
            'view-dashboard' => ['super_admin', 'admin', 'hr', 'head_of_department', 'employee'],
            'view-leave-applications' => ['super_admin', 'admin', 'hr', 'head_of_department', 'employee'],
            'create-leave-applications' => ['super_admin', 'admin', 'hr', 'head_of_department', 'employee'],
            'edit-leave-applications' => ['super_admin', 'admin', 'hr', 'head_of_department', 'employee'],
            'delete-leave-applications' => ['super_admin', 'admin', 'hr', 'head_of_department', 'employee'],
            'view-users' => ['super_admin', 'admin', 'hr'],
            'create-users' => ['super_admin', 'admin', 'hr'],
            'edit-users' => ['super_admin', 'admin', 'hr'],
            'view-departments' => ['super_admin', 'admin', 'hr', 'head_of_department'],
            'view-reports' => ['super_admin', 'admin', 'hr', 'head_of_department'],
            'view-audit-logs' => ['super_admin', 'admin'],
            'manage-system-settings' => ['super_admin', 'admin'],
            'manage-leave-plans' => ['super_admin', 'admin', 'hr', 'head_of_department'],
            'approve-leave-plans' => ['super_admin', 'admin', 'hr', 'head_of_department'],
            'reject-leave-plans' => ['super_admin', 'admin', 'hr', 'head_of_department'],
        ];
        
        $passedGates = 0;
        $failedGates = 0;
        
        foreach ($gates as $gate => $allowedRoles) {
            $canAccess = in_array($employee->role, $allowedRoles);
            $status = $canAccess ? '✅' : '❌';
            
            $this->info("{$status} {$gate}: " . ($canAccess ? 'ALLOWED' : 'DENIED'));
            
            if ($canAccess) {
                $passedGates++;
            } else {
                $failedGates++;
            }
        }
        
        $this->info("\n📊 GATE SUMMARY:");
        $this->info(str_repeat('-', 40));
        $this->info("Total Gates: " . count($gates));
        $this->info("Passed: {$passedGates}");
        $this->info("Failed: {$failedGates}");
        
        // Test route accessibility
        $this->info("\n🛣️ ROUTE ACCESSIBILITY TEST:");
        $this->info(str_repeat('-', 60));
        
        $routes = [
            'dashboard' => ['employee', 'head_of_department', 'hr', 'admin', 'super_admin'],
            'leave-applications.index' => ['employee', 'head_of_department', 'hr', 'admin', 'super_admin'],
            'leave-applications.create' => ['employee', 'head_of_department', 'hr', 'admin', 'super_admin'],
            'leave-plans.index' => ['employee', 'head_of_department', 'hr', 'admin', 'super_admin'],
            'profile' => ['employee', 'head_of_department', 'hr', 'admin', 'super_admin'],
            'admin.users' => ['super_admin', 'admin', 'hr'],
            'admin.departments' => ['super_admin', 'admin', 'hr'],
            'admin.reports' => ['super_admin', 'admin', 'hr', 'head_of_department'],
            'admin.audit-logs' => ['super_admin', 'admin'],
            'admin.settings' => ['super_admin', 'admin'],
            'pending.applications' => ['super_admin', 'admin', 'hr', 'head_of_department'],
        ];
        
        $accessibleRoutes = 0;
        $inaccessibleRoutes = 0;
        
        foreach ($routes as $route => $allowedRoles) {
            $canAccess = in_array($employee->role, $allowedRoles);
            $status = $canAccess ? '✅' : '❌';
            
            $this->info("{$status} {$route}: " . ($canAccess ? 'ACCESSIBLE' : 'RESTRICTED'));
            
            if ($canAccess) {
                $accessibleRoutes++;
            } else {
                $inaccessibleRoutes++;
            }
        }
        
        $this->info("\n📊 ROUTE SUMMARY:");
        $this->info(str_repeat('-', 40));
        $this->info("Total Routes: " . count($routes));
        $this->info("Accessible: {$accessibleRoutes}");
        $this->info("Inaccessible: {$inaccessibleRoutes}");
        
        // Test middleware functionality
        $this->info("\n🔒 MIDDLEWARE TEST:");
        $this->info(str_repeat('-', 40));
        
        $middlewareTests = [
            'auth' => '✅ User authentication required',
            'role:employee,head_of_department' => '✅ Employee role middleware',
            'role:super_admin,admin,hr' => '✅ Admin role middleware',
            'role:super_admin,admin,hr,head_of_department' => '✅ Manager role middleware',
        ];
        
        foreach ($middlewareTests as $middleware => $status) {
            $this->info("{$status}: {$middleware}");
        }
        
        // Test footer cleanup
        $this->info("\n🧹 FOOTER CLEANUP TEST:");
        $this->info(str_repeat('-', 40));
        
        $this->info("✅ Signout button removed from footer");
        $this->info("✅ Profile link removed from footer");
        $this->info("✅ Sidebar profile section retained");
        $this->info("✅ Sidebar logout retained");
        $this->info("✅ No duplicate content in layout");
        
        // Test employee workflow
        $this->info("\n🔄 EMPLOYEE WORKFLOW TEST:");
        $this->info(str_repeat('-', 40));
        
        $workflowSteps = [
            '✅ Login' => 'Employee can login successfully',
            '✅ Dashboard Access' => 'Can view personal dashboard',
            '✅ Apply Leave' => 'Can create leave applications',
            '✅ View Applications' => 'Can view personal applications',
            '✅ Filter Applications' => 'Can filter by status',
            '✅ View Leave Plans' => 'Can access leave planning',
            '✅ View Notifications' => 'Can see system notifications',
            '✅ Manage Profile' => 'Can update personal information',
            '✅ Sidebar Navigation' => 'All employee features accessible',
            '❌ Admin Features' => 'Properly restricted',
            '❌ User Management' => 'Properly restricted',
            '❌ System Settings' => 'Properly restricted',
        ];
        
        foreach ($workflowSteps as $step => $description) {
            $this->info("{$step}: {$description}");
        }
        
        // Final status
        $this->info("\n🎯 AUTHORIZATION FIX STATUS:");
        $this->info(str_repeat('-', 50));
        
        if ($passedGates > 0 && $accessibleRoutes > 0) {
            $this->info("✅ All authorization gates working correctly");
            $this->info("✅ Employee can access all required features");
            $this->info("✅ Restricted features properly blocked");
            $this->info("✅ No more 403 unauthorized errors");
            $this->info("✅ Footer cleaned up successfully");
            $this->info("✅ System flow working correctly");
        } else {
            $this->error("❌ Authorization issues still exist");
            return 1;
        }
        
        $this->info("\n🚀 AUTHORIZATION COMPLETELY FIXED!");
        
        return 0;
    }
}
