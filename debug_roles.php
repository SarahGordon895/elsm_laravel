<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DEBUG USER ROLES ===\n";

try {
    $users = App\Models\User::all(['id', 'first_name', 'last_name', 'email', 'role', 'status']);
    
    foreach ($users as $user) {
        echo "User ID: {$user->id}\n";
        echo "First Name: '{$user->first_name}'\n";
        echo "Last Name: '{$user->last_name}'\n";
        echo "Email: '{$user->email}'\n";
        echo "Role: '{$user->role}'\n";
        echo "Role Length: " . strlen($user->role) . "\n";
        echo "Role Trimmed: '" . trim($user->role) . "'\n";
        echo "Role Lowercase: '" . strtolower($user->role) . "'\n";
        echo "---\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
