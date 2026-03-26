<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AddPhoneNumbersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add phone numbers to existing users for SMS testing
        $usersWithPhoneNumbers = [
            'superadmin@company.com' => '+15551234567', // Super Admin
            'sarah.johnson@company.com' => '+15551234568', // HOD IT
            'michael.robinson@company.com' => '+15551234569', // HOD Finance
            'jennifer.williams@company.com' => '+15551234570', // HOD HR
            'david.brown@company.com' => '+15551234571', // Employee
            'lisa.anderson@company.com' => '+15551234572', // Employee
            'james.wilson@company.com' => '+15551234573', // Employee
            'robert.taylor@company.com' => '+15551234574', // Employee
            'jennifer.martinez@company.com' => '+15551234575', // Employee
        ];

        foreach ($usersWithPhoneNumbers as $email => $phoneNumber) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->update(['phone_number' => $phoneNumber]);
                $this->command->info("Added phone number {$phoneNumber} to {$user->full_name}");
            }
        }

        $this->command->info('Phone numbers added successfully!');
        $this->command->info('SMS notifications are now ready for testing.');
    }
}
