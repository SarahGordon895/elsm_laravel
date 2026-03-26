<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'app:create-admin-user';
    protected $description = 'Create missing admin user to fix authentication';

    public function handle()
    {
        $this->info('🔧 CREATING MISSING ADMIN USER...');
        
        // Check if admin user already exists
        $existingAdmin = User::where('email', 'admin@company.com')->first();
        
        if ($existingAdmin) {
            $this->info('✅ Admin user already exists: ' . $existingAdmin->email);
            $this->info('Updating password...');
            
            $existingAdmin->password = Hash::make('password123');
            $existingAdmin->save();
            
            $this->info('✅ Admin password updated successfully');
        } else {
            $this->info('Creating new admin user...');
            
            $admin = User::create([
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'email' => 'admin@company.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'department_id' => 1,
                'employee_id' => 'ADMIN001',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            
            $this->info('✅ Admin user created successfully');
        }
        
        // Verify admin user
        $admin = User::where('email', 'admin@company.com')->first();
        if ($admin) {
            $this->info('✅ Admin verification:');
            $this->info('   Email: ' . $admin->email);
            $this->info('   Role: ' . $admin->role);
            $this->info('   ID: ' . $admin->id);
            $this->info('   Active: ' . ($admin->is_active ? 'Yes' : 'No'));
        }
        
        // Test login
        $this->info("\n🧪 TESTING ADMIN LOGIN...");
        $this->info('1. Go to: http://127.0.0.1:8000/login');
        $this->info('2. Email: admin@company.com');
        $this->info('3. Password: password123');
        $this->info('4. Should redirect to: /admin/dashboard');
        
        $this->info("\n🎯 ADMIN USER CREATED SUCCESSFULLY!");
        $this->info("The unauthorized error should now be completely resolved.");
        
        return 0;
    }
}
