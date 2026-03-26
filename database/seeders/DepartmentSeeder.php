<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Executive Management',
                'short_name' => 'EXEC',
                'code' => 'EXEC001',
                'description' => 'Executive management and administration',
            ],
            [
                'name' => 'Information Technology',
                'short_name' => 'IT',
                'code' => 'IT001',
                'description' => 'IT infrastructure and software development',
            ],
            [
                'name' => 'Finance',
                'short_name' => 'FIN',
                'code' => 'FIN001',
                'description' => 'Financial planning and accounting',
            ],
            [
                'name' => 'Human Resources',
                'short_name' => 'HR',
                'code' => 'HR001',
                'description' => 'Employee management and HR services',
            ],
            [
                'name' => 'Marketing',
                'short_name' => 'MKT',
                'code' => 'MKT001',
                'description' => 'Marketing and business development',
            ],
            [
                'name' => 'Operations',
                'short_name' => 'OPS',
                'code' => 'OPS001',
                'description' => 'Operations and process management',
            ],
        ];

        foreach ($departments as $deptData) {
            $department = Department::firstOrCreate(
                ['code' => $deptData['code']],
                $deptData
            );
            
            $this->command->info("Department created/updated: {$department->name}");
        }

        // Assign managers to departments (HOD users)
        $hodUsers = User::where('role', 'head_of_department')->get();
        
        foreach ($hodUsers as $hod) {
            if ($hod->department_id) {
                $department = Department::find($hod->department_id);
                if ($department && !$department->manager_id) {
                    $department->update(['manager_id' => $hod->id]);
                    $this->command->info("Assigned {$hod->full_name} as manager of {$department->name}");
                }
            }
        }

        // Update existing users to ensure they have proper department_id
        $this->command->info('Updating user department assignments...');
        
        // Assign departments to users who don't have one
        $usersWithoutDept = User::whereNull('department_id')->where('role', 'employee')->get();
        $departments = Department::all();
        
        foreach ($usersWithoutDept as $user) {
            // Assign to IT department by default
            $itDept = Department::where('code', 'IT001')->first();
            if ($itDept) {
                $user->update(['department_id' => $itDept->id]);
                $this->command->info("Assigned {$user->full_name} to IT department");
            }
        }

        $this->command->info('Department seeding completed successfully!');
    }
}
