<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== SIMPLE MIDDLEWARE TEST ===\n";

try {
    echo "Testing role 'admin':\n";
    
    // Create a simple user object with role 'admin'
    $user = new stdClass();
    $user->role = 'admin';
    
    // Test the role check logic directly
    $allowedRoles = ['super_admin', 'admin', 'hr'];
    $isAllowed = in_array($user->role, $allowedRoles);
    
    echo "Role 'admin' in allowed roles: " . ($isAllowed ? 'YES' : 'NO') . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
