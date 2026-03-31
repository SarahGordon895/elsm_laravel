<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== USER ROLES IN DATABASE ===\n";

try {
    $users = App\Models\User::all(['id', 'first_name', 'last_name', 'email', 'role', 'status']);
    
    foreach ($users as $user) {
        echo "ID: {$user->id} - {$user->first_name} {$user->last_name} ({$user->email}) - Role: {$user->role} - Status: {$user->status}\n";
    }
    
    echo "\n=== ROLE DISTRIBUTION ===\n";
    
    $roles = ['super_admin', 'admin', 'hr', 'head_of_department', 'employee'];
    
    foreach ($roles as $role) {
        $count = $users->where('role', $role)->count();
        echo "{$role}: {$count} users\n";
    }
    
    echo "\n=== ROUTE ACCESS EXPECTATIONS ===\n";
    echo "✅ Admin users should access /login\n";
    echo "✅ Employee users should access /employee/login\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
