<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Trace unauthenticated redirects to diagnose login/session persistence issues.
        \Log::warning('LOGIN_TRACE unauthenticated redirect', [
            'path' => $request->path(),
            'full_url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'session_id' => $request->session()->getId(),
            'has_session_cookie' => $request->hasCookie(config('session.cookie')),
            'session_cookie_name' => config('session.cookie'),
            'session_domain' => config('session.domain'),
        ]);

        // Employee flow routes should return to employee login form.
        if ($request->is('dashboard') || $request->is('profile') || $request->is('profile/*')) {
            return route('employee.login');
        }

        return route('login');
    }
}
