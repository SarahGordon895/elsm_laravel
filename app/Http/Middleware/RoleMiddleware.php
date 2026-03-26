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
        
        $userRole = Auth::user()->role;
        
        // Check if user's role is in the allowed roles
        if (!in_array($userRole, $allowedRoles)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
