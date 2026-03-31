<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EnforceRequestedUserRolesSeeder extends Seeder
{
    public function run(): void
    {
        $roleMap = Role::pluck('id', 'name');

        $requestedAssignments = [
            'h.zuher' => 'super_admin',
            'a.gonda' => 'hr',
            'm.gadi' => 'head_of_department',
            'developer' => 'admin',
            'sarahgeorge' => 'admin',
        ];

        $managedUserIds = [];

        foreach ($requestedAssignments as $identifier => $roleName) {
            $user = $this->findUserByIdentifier($identifier);
            if (!$user) {
                $this->command->warn("User not found for identifier: {$identifier}");
                continue;
            }

            $user->update([
                'role' => $roleName,
                'status' => 'active',
            ]);

            if (isset($roleMap[$roleName])) {
                $user->roles()->sync([$roleMap[$roleName]]);
            }

            $managedUserIds[] = $user->id;
            $this->command->info("Assigned {$user->email} => {$roleName}");
        }

        // Remove or deactivate legacy seeded admin users listed by the user.
        $legacyEmailsToRemove = [
            'system@elsm.com',
            'admin1774871717@elsm.com',
            'hr@company.com',
            'hod@company.com',
            'admin@company.com',
            'superadmin@company.com',
            'sarah.johnson@company.com',
            'michael.robinson@company.com',
            'jennifer.williams@company.com',
        ];

        $legacyUsers = User::whereIn('email', $legacyEmailsToRemove)->get();
        foreach ($legacyUsers as $legacyUser) {
            try {
                $legacyEmail = $legacyUser->email;
                $legacyUser->roles()->detach();
                $legacyUser->delete();
                $this->command->info("Removed legacy user: {$legacyEmail}");
            } catch (\Throwable $e) {
                $legacyUser->update(['status' => 'inactive']);
                $this->command->warn("Could not delete {$legacyUser->email}, set inactive instead.");
            }
        }

        // Enforce exact three employee users requested by the user.
        $employeeRoleId = $roleMap['employee'] ?? null;
        $employeeEmails = [
            'john.employee@imartgroup.co.tz',
            'jane.smith@imartgroup.co.tz',
            'robert.johnson@imartgroup.co.tz',
        ];

        $employeeDefaults = [
            'john.employee@imartgroup.co.tz' => ['first_name' => 'John', 'last_name' => 'Employee', 'employee_id' => 'IMT_EMP_001'],
            'jane.smith@imartgroup.co.tz' => ['first_name' => 'Jane', 'last_name' => 'Smith', 'employee_id' => 'IMT_EMP_002'],
            'robert.johnson@imartgroup.co.tz' => ['first_name' => 'Robert', 'last_name' => 'Johnson', 'employee_id' => 'IMT_EMP_003'],
        ];

        foreach ($employeeDefaults as $email => $defaults) {
            User::updateOrCreate(
                ['email' => $email],
                array_merge($defaults, [
                    'role' => 'employee',
                    'status' => 'active',
                    'password' => Hash::make('password123'),
                ])
            );
        }

        $employeeUsers = User::whereIn('email', $employeeEmails)->get();
        foreach ($employeeUsers as $remainingUser) {
            $remainingUser->update([
                'role' => 'employee',
                'status' => 'active',
            ]);
            if ($employeeRoleId) {
                $remainingUser->roles()->sync([$employeeRoleId]);
            }
            $this->command->info("Assigned {$remainingUser->email} => employee");
        }

        // Force all other active users outside requested role users + explicit employee set to inactive.
        $managedEmails = User::whereIn('id', $managedUserIds)->pluck('email')->all();
        User::where('status', 'active')
            ->whereNotIn('email', array_merge($managedEmails, $employeeEmails))
            ->update(['status' => 'inactive']);
    }

    private function findUserByIdentifier(string $identifier): ?User
    {
        $identifier = strtolower(trim($identifier));
        $normalized = str_replace('.', '', $identifier);

        return User::whereRaw('LOWER(email) = ?', [$identifier])
            ->orWhereRaw('LOWER(email) LIKE ?', ["%{$identifier}%"])
            ->orWhereRaw('LOWER(CONCAT(first_name, last_name)) LIKE ?', ["%{$normalized}%"])
            ->orWhereRaw('LOWER(CONCAT(first_name, ".", last_name)) LIKE ?', ["%{$identifier}%"])
            ->first();
    }
}
