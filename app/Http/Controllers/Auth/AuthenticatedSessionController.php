<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;

class AuthenticatedSessionController extends Controller
{
    public function create(): \Illuminate\View\View
    {
        return view('auth.admin-login-simple');
    }

    public function createEmployeeLogin(): \Illuminate\View\View
    {
        return view('auth.employee-login-simple');
    }

    public function storeEmployee(Request $request)
    {
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
        \Log::info('Employee logged in', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'login_type' => 'employee',
            'ip' => $request->ip(),
            'timestamp' => now()->toISOString()
        ]);

        // Check if user is employee
        if ($user->role !== 'employee') {
            Auth::logout();
            return redirect()->route('employee.login')
                ->with('error', 'This login is for employees only. Please use the administrator login.');
        }

        // Redirect employee to dashboard
        return redirect()->intended(route('dashboard'))
            ->with('status', 'Welcome, ' . $user->full_name . '! You are logged in as Employee.');
    }

    public function store(Request $request)
    {
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
        \Log::info('User logged in', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'login_type' => $request->input('login_type', 'unknown'),
            'ip' => $request->ip(),
            'timestamp' => now()->toISOString()
        ]);

        // Redirect based on user role and login type
        $loginType = $request->input('login_type', 'unknown');
        
        // Super Admin - highest priority
        if ($user->role === 'super_admin') {
            return redirect()->intended(route('admin.dashboard'))
                ->with('status', 'Welcome, ' . $user->full_name . '! You are logged in as Super Administrator.');
        }
        
        // Admin - system administrator
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'))
                ->with('status', 'Welcome, ' . $user->full_name . '! You are logged in as System Administrator.');
        }
        
        // HR - human resources
        if ($user->role === 'hr') {
            return redirect()->intended(route('admin.dashboard'))
                ->with('status', 'Welcome, ' . $user->full_name . '! You are logged in as HR Administrator.');
        }
        
        // Head of Department
        if ($user->role === 'head_of_department') {
            return redirect()->intended(route('dashboard'))
                ->with('status', 'Welcome, ' . $user->full_name . '! You are logged in as Head of Department.');
        }
        
        // Employee
        if ($user->role === 'employee') {
            return redirect()->intended(route('dashboard'))
                ->with('status', 'Welcome, ' . $user->full_name . '! You are logged in as Employee.');
        }

        // Default fallback
        return redirect()->intended(route('dashboard'))
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
