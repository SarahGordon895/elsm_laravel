<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create(): \Illuminate\View\View
    {
        return view('auth.login');
    }

    public function createEmployeeLogin(): \Illuminate\View\View
    {
        return view('auth.employee-login');
    }

    public function storeEmployee(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        \Log::info('LOGIN_TRACE employee submit start', [
            'email' => $request->input('email'),
            'path' => $request->path(),
            'full_url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'session_id_before' => $request->session()->getId(),
            'intended_before' => $request->session()->get('url.intended'),
        ]);

        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            \Log::warning('LOGIN_TRACE employee auth failed', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
                'session_id' => $request->session()->getId(),
            ]);
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        // Log successful login
        $user = Auth::user();
        $normalizedRole = $user?->getEffectiveRole() ?? '';
        \Log::info('Employee logged in', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $normalizedRole,
            'login_type' => 'employee',
            'ip' => $request->ip(),
            'timestamp' => now()->toISOString()
        ]);

        // Employee portal is only for employees.
        if ($normalizedRole !== 'employee') {
            \Log::warning('LOGIN_TRACE employee portal role mismatch', [
                'user_id' => $user?->id,
                'email' => $user?->email,
                'effective_role' => $normalizedRole,
                'session_id' => $request->session()->getId(),
            ]);
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('employee.login')
                ->with('error', 'This portal is for employees only. HR/HOD/Admin/Super Admin should use Administrator Login.');
        }

        // Clear stale intended URL so employee login cannot bounce to admin login page.
        $request->session()->forget('url.intended');

        \Log::info('LOGIN_TRACE employee redirect dashboard', [
            'user_id' => $user?->id,
            'email' => $user?->email,
            'effective_role' => $normalizedRole,
            'session_id_after' => $request->session()->getId(),
            'intended_after' => $request->session()->get('url.intended'),
        ]);

        // Redirect employee to employee dashboard directly.
        return redirect()->route('dashboard')
            ->with('status', 'Welcome, ' . $user->full_name . '! You are logged in as Employee.');
    }

    public function store(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        // Log successful login
        $user = Auth::user();
        $normalizedRole = $user?->getEffectiveRole() ?? '';
        \Log::info('User logged in', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $normalizedRole,
            'login_type' => $request->input('login_type', 'unknown'),
            'ip' => $request->ip(),
            'timestamp' => now()->toISOString()
        ]);

        // Administrator portal roles only.
        if (!in_array($normalizedRole, ['super_admin', 'admin', 'hr', 'head_of_department'], true)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')
                ->with('error', 'This portal is for Administrator roles (Super Admin, Admin, HR, HOD). Employees should use Employee Login.');
        }

        // Super Admin - highest priority
        if ($normalizedRole === 'super_admin') {
            $request->session()->forget('url.intended');
            return redirect()->route('admin.dashboard')
                ->with('status', 'Welcome, ' . $user->full_name . '! You are logged in as Super Administrator.');
        }
        
        // Admin - system administrator
        if ($normalizedRole === 'admin') {
            $request->session()->forget('url.intended');
            return redirect()->route('admin.dashboard')
                ->with('status', 'Welcome, ' . $user->full_name . '! You are logged in as System Administrator.');
        }
        
        // HR - human resources
        if ($normalizedRole === 'hr') {
            $request->session()->forget('url.intended');
            return redirect()->route('hr.dashboard')
                ->with('status', 'Welcome, ' . $user->full_name . '! You are logged in as HR Administrator.');
        }
        
        // Head of Department
        if ($normalizedRole === 'head_of_department') {
            $request->session()->forget('url.intended');
            return redirect()->route('hod.dashboard')
                ->with('status', 'Welcome, ' . $user->full_name . '! You are logged in as Head of Department.');
        }

        // Default fallback
        $request->session()->forget('url.intended');
        return redirect()->route('login')
            ->with('status', 'Welcome, ' . $user->full_name . '! You are logged in.');
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
