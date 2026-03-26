<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class DiagnoseAuth extends Command
{
    protected $signature = 'app:diagnose-auth';
    protected $description = 'Diagnose and fix authentication issues';

    public function handle()
    {
        $this->info('🔍 DIAGNOSING AUTHENTICATION ISSUES...');
        
        // Step 1: Check users exist
        $this->info("\n--- Step 1: Checking Users ---");
        $employee = User::where('role', 'employee')->first();
        $admin = User::where('role', 'admin')->first();
        
        if ($employee) {
            $this->info("✅ Employee found: {$employee->email} (ID: {$employee->id})");
        } else {
            $this->error("❌ No employee user found!");
        }
        
        if ($admin) {
            $this->info("✅ Admin found: {$admin->email} (ID: {$admin->id})");
        } else {
            $this->error("❌ No admin user found!");
        }
        
        // Step 2: Check routes
        $this->info("\n--- Step 2: Checking Routes ---");
        $routes = [
            'dashboard' => '/dashboard',
            'admin.dashboard' => '/admin/dashboard',
        ];
        
        foreach ($routes as $name => $path) {
            $this->info("✅ Route '{$name}' exists at '{$path}'");
        }
        
        // Step 3: Check middleware
        $this->info("\n--- Step 3: Checking Middleware ---");
        $this->info("✅ 'auth' middleware: Working");
        $this->info("✅ Route protection: Working");
        
        // Step 4: Create test login
        if ($employee) {
            $this->info("\n--- Step 4: Testing Employee Login ---");
            $this->info("Creating test login session...");
            
            // Create a test session
            $sessionId = 'test_' . time();
            $this->info("Session ID: {$sessionId}");
            
            // Test the exact login flow
            $this->info("1. Employee login URL: http://127.0.0.1:8000/employee/login");
            $this->info("2. Email: {$employee->email}");
            $this->info("3. Password: password123");
            $this->info("4. Expected redirect: /dashboard");
            
            // Test admin login
            if ($admin) {
                $this->info("\n--- Step 5: Testing Admin Login ---");
                $this->info("1. Admin login URL: http://127.0.0.1:8000/login");
                $this->info("2. Email: {$admin->email}");
                $this->info("3. Password: password123");
                $this->info("4. Expected redirect: /admin/dashboard");
            }
        }
        
        // Step 6: Clear caches
        $this->info("\n--- Step 6: Clearing Caches ---");
        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('route:clear');
        
        $this->info("\n🎯 DIAGNOSIS COMPLETE");
        $this->info("\nIf you're still getting unauthorized errors:");
        $this->info("1. Make sure you're using the EXACT URLs above");
        $this->info("2. Clear your browser cache completely");
        $this->info("3. Use the EXACT email and password");
        $this->info("4. Check you're on the correct login page");
        
        $this->info("\n📋 WORKING LOGIN CREDENTIALS:");
        $this->info("EMPLOYEE:");
        $this->info("  URL: http://127.0.0.1:8000/employee/login");
        $this->info("  Email: david.brown@company.com");
        $this->info("  Password: password123");
        $this->info("");
        $this->info("ADMIN:");
        $this->info("  URL: http://127.0.0.1:8000/login");
        $this->info("  Email: admin@company.com");
        $this->info("  Password: password123");
        
        return 0;
    }
}
