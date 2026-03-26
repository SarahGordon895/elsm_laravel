<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create HR Manager
        $hrManager = User::firstOrCreate(
            ['email' => 'hr@company.com'],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'hr@company.com',
                'password' => Hash::make('password123'),
                'role' => 'hr',
                'employee_id' => 'HR001',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // Create Head of Department
        $hodManager = User::firstOrCreate(
            ['email' => 'hod@company.com'],
            [
                'first_name' => 'Michael',
                'last_name' => 'Chen',
                'email' => 'hod@company.com',
                'password' => Hash::make('password123'),
                'role' => 'head_of_department',
                'employee_id' => 'HOD001',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // Create Administrator
        $admin = User::firstOrCreate(
            ['email' => 'admin@company.com'],
            [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'admin@company.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'employee_id' => 'ADM001',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // Create Super Administrator
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@company.com'],
            [
                'first_name' => 'Robert',
                'last_name' => 'Williams',
                'email' => 'superadmin@company.com',
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
                'employee_id' => 'SUP001',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        $this->command->info('Admin users created/updated successfully!');
        $this->command->info('HR Manager: hr@company.com');
        $this->command->info('Head of Department: hod@company.com');
        $this->command->info('Administrator: admin@company.com');
        $this->command->info('Super Administrator: superadmin@company.com');
        $this->command->info('Password: password123 (for all accounts)');
    }
}
