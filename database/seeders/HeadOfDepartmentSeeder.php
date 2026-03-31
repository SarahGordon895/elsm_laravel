<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

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

        $departmentQueryByCode = function (string $code): ?int {
            if (!Schema::hasColumn('departments', 'code')) {
                return null;
            }
            return Department::where('code', $code)->value('id');
        };

        $itDepartmentId = $departmentQueryByCode('IT001')
            ?? Department::where('name', 'Information Technology')->value('id')
            ?? Department::where('name', 'IT Department')->value('id');
        $financeDepartmentId = $departmentQueryByCode('FIN001')
            ?? Department::where('name', 'Finance & Accounting')->value('id')
            ?? Department::where('name', 'Finance Department')->value('id');
        $hrDepartmentId = $departmentQueryByCode('HR001')
            ?? Department::where('name', 'Human Resources')->value('id')
            ?? Department::where('name', 'HR Department')->value('id');

        // Create Head of Department users
        $hodUsers = [
            [
                'employee_id' => 'HODIT001',
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.johnson@company.com',
                'password' => Hash::make('hod123'),
                'gender' => 'Female',
                'department_id' => $itDepartmentId,
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
                'department_id' => $financeDepartmentId,
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
                'department_id' => $hrDepartmentId,
                'role' => 'head_of_department',
                'status' => 'active',
                'employment_type' => 'full_time',
                'join_date' => now()->subYears(5),
            ],
            [
                'employee_id' => 'HODSYS001',
                'first_name' => 'M',
                'last_name' => 'Gadi',
                'email' => 'm.gadi@imartgroup.co.tz',
                'password' => Hash::make('password123'),
                'gender' => 'Male',
                'department_id' => $itDepartmentId,
                'role' => 'head_of_department',
                'status' => 'active',
                'employment_type' => 'full_time',
                'join_date' => now()->subYears(3),
            ],
        ];

        foreach ($hodUsers as $hodData) {
            $hodUser = User::firstOrCreate(
                ['email' => $hodData['email']],
                $hodData
            );
            if ($hodUser->role !== 'head_of_department') {
                $hodUser->update(['role' => 'head_of_department']);
            }
            
            // Assign Head of Department role
            $hodUser->roles()->sync([$hodRole->id]);
            
            $this->command->info("Head of Department created: {$hodUser->full_name} ({$hodUser->email})");
        }

        $this->command->info('Head of Department users seeded successfully!');
        $this->command->info('Login credentials: email / hod123');
    }
}
