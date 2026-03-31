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
    
    echo "\n=== ADMIN LOGIN ROUTE ACCESS ===\n";
    
    // Test if admin login route is accessible
    $adminUsers = $users->whereIn('role', ['super_admin', 'admin', 'hr'])->get();
    
    foreach ($adminUsers as $user) {
        echo "✅ {$user->first_name} {$user->last_name} ({$user->role}) should access admin login\n";
    }
    
    echo "\n=== EMPLOYEE LOGIN ROUTE ACCESS ===\n";
    
    // Test if employee login route is accessible
    $employeeUsers = $users->where('role', 'employee')->get();
    
    foreach ($employeeUsers as $user) {
        echo "✅ {$user->first_name} {$user->last_name} ({$user->role}) should access employee login\n";
    }
    
    echo "\n=== RECOMMENDATIONS ===\n";
    echo "1. All admin users should access /login\n";
    echo "2. All employee users should access /employee/login\n";
    echo "3. Check if RoleMiddleware is blocking legitimate access\n";
    echo "4. Verify role names match exactly (case-sensitive)\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
