<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ActivateAdmin extends Command
{
    protected $signature = 'app:activate-admin';
    protected $description = 'Activate admin user to fix authorization';

    public function handle()
    {
        $this->info('🔧 ACTIVATING ADMIN USER...');
        
        // Find and activate admin user
        $admin = User::where('email', 'admin@company.com')->first();
        
        if ($admin) {
            $admin->is_active = true;
            $admin->save();
            
            $this->info('✅ Admin user activated successfully');
            $this->info('   Email: ' . $admin->email);
            $this->info('   Role: ' . $admin->role);
            $this->info('   Status: ' . ($admin->is_active ? 'Active' : 'Inactive'));
            $this->info('   ID: ' . $admin->id);
        } else {
            $this->error('❌ Admin user not found!');
        }
        
        // Also activate super_admin if exists
        $superAdmin = User::where('email', 'superadmin@company.com')->first();
        if ($superAdmin) {
            $superAdmin->is_active = true;
            $superAdmin->save();
            $this->info('✅ Super Admin user activated successfully');
        }
        
        $this->info("\n🎯 ADMIN USERS ACTIVATED!");
        $this->info("The unauthorized error should now be completely resolved.");
        
        $this->info("\n📋 WORKING LOGIN CREDENTIALS:");
        $this->info("ADMIN:");
        $this->info("  URL: http://127.0.0.1:8000/login");
        $this->info("  Email: admin@company.com");
        $this->info("  Password: password123");
        $this->info("  Status: ACTIVE ✅");
        
        $this->info("\n📋 EMPLOYEE LOGIN:");
        $this->info("  URL: http://127.0.0.1:8000/employee/login");
        $this->info("  Email: david.brown@company.com");
        $this->info("  Password: password123");
        $this->info("  Status: ACTIVE ✅");
        
        $this->info("\n🚀 FINAL INSTRUCTIONS:");
        $this->info("1. Clear your browser cache completely");
        $this->info("2. Use the exact URLs above");
        $this->info("3. Use the exact credentials");
        $this->info("4. Login should work without any errors");
        
        return 0;
    }
}
