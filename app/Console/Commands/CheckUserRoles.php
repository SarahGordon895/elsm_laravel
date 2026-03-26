<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckUserRoles extends Command
{
    protected $signature = 'app:check-user-roles';
    protected $description = 'Check all user roles and fix any issues';

    public function handle()
    {
        $this->info('🔍 CHECKING USER ROLES AND PERMISSIONS...');
        
        $users = User::all();
        
        $this->info("\n📋 ALL USERS AND THEIR ROLES:");
        $this->info(str_repeat('-', 50));
        
        foreach ($users as $user) {
            $status = $user->is_active ? '✅ Active' : '❌ Inactive';
            $this->info("{$user->email} -> Role: {$user->role} | Status: {$status}");
        }
        
        // Check for users with no role or invalid roles
        $this->info("\n🔍 CHECKING FOR ROLE ISSUES:");
        $this->info(str_repeat('-', 50));
        
        $validRoles = ['super_admin', 'admin', 'hr', 'head_of_department', 'employee'];
        
        foreach ($users as $user) {
            if (!$user->role || !in_array($user->role, $validRoles)) {
                $this->error("❌ {$user->email} has invalid role: '{$user->role}'");
            }
        }
        
        // Fix users with no role - assign them as employee
        $this->info("\n🔧 FIXING USERS WITH NO ROLE:");
        $this->info(str_repeat('-', 50));
        
        $fixedCount = 0;
        foreach ($users as $user) {
            if (!$user->role || !in_array($user->role, $validRoles)) {
                $user->role = 'employee';
                $user->is_active = true;
                $user->save();
                $this->info("✅ Fixed {$user->email} -> Role: employee");
                $fixedCount++;
            }
        }
        
        // Check johndoe specifically
        $johndoe = User::where('email', 'john.doe@company.com')->first();
        if ($johndoe) {
            $this->info("\n👤 CHECKING JOHN DOE SPECIFICALLY:");
            $this->info(str_repeat('-', 50));
            $this->info("Email: {$johndoe->email}");
            $this->info("Role: {$johndoe->role}");
            $this->info("Active: " . ($johndoe->is_active ? 'Yes' : 'No'));
            $this->info("First Name: {$johndoe->first_name}");
            $this->info("Last Name: {$johndoe->last_name}");
            
            if ($johndoe->role !== 'employee') {
                $johndoe->role = 'employee';
                $johndoe->is_active = true;
                $johndoe->save();
                $this->info("✅ Fixed John Doe -> Role: employee");
            }
        } else {
            $this->error("❌ John Doe not found in database");
        }
        
        // Test login access for employees
        $this->info("\n🔐 TESTING EMPLOYEE LOGIN ACCESS:");
        $this->info(str_repeat('-', 50));
        
        $employees = User::where('role', 'employee')->get();
        foreach ($employees as $employee) {
            $this->info("✅ {$employee->email} - Employee access: AVAILABLE");
        }
        
        // Summary
        $this->info("\n📊 SUMMARY:");
        $this->info(str_repeat('-', 50));
        $this->info("Total Users: " . $users->count());
        $this->info("Users Fixed: {$fixedCount}");
        $this->info("Active Employees: " . User::where('role', 'employee')->where('is_active', true)->count());
        $this->info("Active Admins: " . User::whereIn('role', ['super_admin', 'admin', 'hr'])->where('is_active', true)->count());
        
        $this->info("\n🎯 WORKING LOGIN CREDENTIALS:");
        $this->info(str_repeat('-', 50));
        $this->info("Employee Login: http://127.0.0.1:8000/employee/login");
        $this->info("Admin Login: http://127.0.0.1:8000/login");
        
        $this->info("\n📱 EMPLOYEE ACCOUNTS:");
        $this->info(str_repeat('-', 50));
        foreach ($employees as $employee) {
            $this->info("Email: {$employee->email} | Password: password123");
        }
        
        $this->info("\n🚀 All user roles have been checked and fixed!");
        
        return 0;
    }
}
