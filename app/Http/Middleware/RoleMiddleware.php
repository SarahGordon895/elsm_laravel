<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Split multiple roles by comma
        $allowedRoles = explode(',', $roles);
        $allowedRoles = array_map('trim', $allowedRoles);
        $allowedRoles = array_map([\App\Models\User::class, 'normalizeRoleName'], $allowedRoles);
        
        $user = Auth::user();
        $userRole = $user->getEffectiveRole();
        
        // Check if user's role is in the allowed roles
        if (!in_array($userRole, $allowedRoles)) {
            // Log the unauthorized access attempt
            \Log::warning('Unauthorized access attempt', [
                'user_id' => $user->id,
                'user_role' => $userRole,
                'allowed_roles' => $allowedRoles,
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
            ]);
            
            abort(403, 'Unauthorized action. Your role (' . $userRole . ') is not allowed to access this resource.');
        }

        return $next($request);
    }
}
