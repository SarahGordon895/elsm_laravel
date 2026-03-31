<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestEmployeeLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-employee-login';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test employee login credentials';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Employee Login...');
        
        // Check all users
        $users = User::all();
        $this->info('Total Users: ' . $users->count());
        
        // Check employee users
        $employees = User::where('role', 'employee')->get();
        $this->info('Employee Users: ' . $employees->count());
        
        foreach ($employees as $employee) {
            $this->info('Email: ' . $employee->email);
            $this->info('Name: ' . $employee->full_name);
            $this->info('Password Hash: ' . substr($employee->password, 0, 20) . '...');
            $this->info('---');
        }
        
        // Test specific employee login
        $testEmail = 'john.employee@imartgroup.co.tz';
        $testPassword = 'password123';
        
        $user = User::where('email', $testEmail)->first();
        
        if ($user) {
            $this->info('Testing login for: ' . $testEmail);
            $this->info('User Role: ' . $user->role);
            
            if (Hash::check($testPassword, $user->password)) {
                $this->info('✅ Password matches!');
            } else {
                $this->error('❌ Password does not match!');
                
                // Try to update password
                $user->password = Hash::make($testPassword);
                $user->save();
                $this->info('🔧 Password has been reset to: ' . $testPassword);
            }
        } else {
            $this->error('❌ User not found: ' . $testEmail);
        }
        
        // Now fix all existing employee passwords
        $this->info('Fixing existing employee passwords...');
        $employees = User::where('role', 'employee')->get();
        
        foreach ($employees as $employee) {
            // Ensure all employee passwords are set to the known default for testing.
            if (!Hash::check($testPassword, $employee->password)) {
                $this->info('Fixing password for: ' . $employee->email);
                $employee->password = Hash::make($testPassword);
                $employee->save();
            }
        }
        
        $this->info('✅ Employee login credentials fixed!');
        $this->info('Use any of these employee accounts:');
        foreach ($employees as $employee) {
            $this->info('📧 ' . $employee->email . ' | 🔑 ' . $testPassword);
        }
        
        return 0;
    }
}
