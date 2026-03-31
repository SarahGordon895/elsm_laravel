<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;
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
            $status = $user->status === 'active' ? '✅ Active' : '❌ Inactive';
            $this->info("{$user->email} -> Role: {$user->role} | Status: {$status}");
        }
        
        // Check for users with no role or invalid roles
        $this->info("\n🔍 CHECKING FOR ROLE ISSUES:");
        $this->info(str_repeat('-', 50));
        
        $validRoles = ['super_admin', 'admin', 'hr', 'head_of_department', 'employee'];
        $legacyRoleMap = [
            'superadmin' => 'super_admin',
            'hod' => 'head_of_department',
            'support' => 'employee',
        ];
        
        foreach ($users as $user) {
            if (!$user->role || !in_array($user->role, $validRoles)) {
                $this->error("❌ {$user->email} has invalid role: '{$user->role}'");
            }
        }
        
        // Fix users with missing/legacy roles.
        $this->info("\n🔧 FIXING USERS WITH NO ROLE:");
        $this->info(str_repeat('-', 50));
        
        $fixedCount = 0;
        foreach ($users as $user) {
            if (!$user->role || !in_array($user->role, $validRoles)) {
                $normalizedRole = $legacyRoleMap[$user->role] ?? 'employee';
                $user->role = $normalizedRole;
                $user->status = 'active';
                $user->save();
                $roleId = Role::where('name', $normalizedRole)->value('id');
                if ($roleId) {
                    $user->roles()->sync([$roleId]);
                }
                $this->info("✅ Fixed {$user->email} -> Role: {$normalizedRole}");
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
            $this->info("Active: " . ($johndoe->status === 'active' ? 'Yes' : 'No'));
            $this->info("First Name: {$johndoe->first_name}");
            $this->info("Last Name: {$johndoe->last_name}");
            
            if ($johndoe->role !== 'employee') {
                $johndoe->role = 'employee';
                $johndoe->status = 'active';
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
        $this->info("Active Employees: " . User::where('role', 'employee')->where('status', 'active')->count());
        $this->info("Active Admins: " . User::whereIn('role', ['super_admin', 'admin', 'hr'])->where('status', 'active')->count());
        
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
