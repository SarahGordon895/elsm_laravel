<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ResetEmployeePasswordsSeeder extends Seeder
{
    /**
     * Reset all employee passwords to a known default.
     */
    public function run(): void
    {
        $updated = User::where('role', 'employee')->update([
            'password' => Hash::make('password123'),
        ]);

        $this->command->info("Employee passwords reset for {$updated} users.");
        $this->command->info('Employee login password: password123');
    }
}
