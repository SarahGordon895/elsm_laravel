<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CURRENT USER CHECK ===\n";

try {
    // Get current authenticated user
    if (Auth::check()) {
        $user = Auth::user();
        echo "✅ User is authenticated\n";
        echo "User ID: {$user->id}\n";
        echo "First Name: {$user->first_name}\n";
        echo "Email: {$user->email}\n";
        echo "Role: '{$user->role}'\n";
        echo "Role Length: " . strlen($user->role) . "\n";
        echo "Role Lowercase: '" . strtolower($user->role) . "'\n";
        echo "---\n";
    } else {
        echo "❌ No user is authenticated\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
