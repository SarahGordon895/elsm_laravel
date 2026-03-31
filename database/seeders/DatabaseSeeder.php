<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ProfessionalDatabaseSeeder::class,
            CreateAdminUsersSeeder::class,
            NewRolesAndPermissionsSeeder::class,
            HeadOfDepartmentSeeder::class,
            UpdateOrganizationalStructureSeeder::class,
            FixSuperAdminPermissionsSeeder::class,
            SyncUserRoleAssignmentsSeeder::class,
            ResetEmployeePasswordsSeeder::class,
            ResetAdminPortalPasswordsSeeder::class,
            EnforceRequestedUserRolesSeeder::class,
        ]);
    }
}
