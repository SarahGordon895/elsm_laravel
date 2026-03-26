<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HeadOfDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the Head of Department role
        $hodRole = Role::where('name', 'head_of_department')->first();
        
        if (!$hodRole) {
            $this->command->error('Head of Department role not found!');
            return;
        }

        // Create Head of Department users
        $hodUsers = [
            [
                'employee_id' => 'HODIT001',
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.johnson@company.com',
                'password' => Hash::make('hod123'),
                'gender' => 'Female',
                'department_id' => 3, // IT Department
                'role' => 'head_of_department',
                'status' => 'active',
                'employment_type' => 'full_time',
                'join_date' => now()->subYears(3),
            ],
            [
                'employee_id' => 'HODFIN001',
                'first_name' => 'Michael',
                'last_name' => 'Robinson',
                'email' => 'michael.robinson@company.com',
                'password' => Hash::make('hod123'),
                'gender' => 'Male',
                'department_id' => 4, // Finance Department
                'role' => 'head_of_department',
                'status' => 'active',
                'employment_type' => 'full_time',
                'join_date' => now()->subYears(4),
            ],
            [
                'employee_id' => 'HODHR001',
                'first_name' => 'Jennifer',
                'last_name' => 'Williams',
                'email' => 'jennifer.williams@company.com',
                'password' => Hash::make('hod123'),
                'gender' => 'Female',
                'department_id' => 2, // HR Department
                'role' => 'head_of_department',
                'status' => 'active',
                'employment_type' => 'full_time',
                'join_date' => now()->subYears(5),
            ],
        ];

        foreach ($hodUsers as $hodData) {
            $hodUser = User::firstOrCreate(
                ['email' => $hodData['email']],
                $hodData
            );
            
            // Assign Head of Department role
            $hodUser->roles()->syncWithoutDetaching([$hodRole->id]);
            
            $this->command->info("Head of Department created: {$hodUser->full_name} ({$hodUser->email})");
        }

        $this->command->info('Head of Department users seeded successfully!');
        $this->command->info('Login credentials: email / hod123');
    }
}
