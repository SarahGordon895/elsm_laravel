<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard ?: 'web')->check()) {
                $user = Auth::user();
                
                // Redirect based on user role according to system flow
                if ($user) {
                    switch ($user->role) {
                        case 'super_admin':
                        case 'admin':
                            return redirect()->route('admin.dashboard');
                        case 'hr':
                            return redirect()->route('hr.dashboard');
                        case 'hod':
                        case 'head_of_department':
                            return redirect()->route('hod.dashboard');
                        case 'employee':
                            return redirect()->route('dashboard');
                        default:
                            return redirect()->route('dashboard');
                    }
                }
                
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
