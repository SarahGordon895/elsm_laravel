(<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\EnhancedLeaveApplicationController;
use App\Http\Controllers\EnhancedAdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeavePlanController;

// Guest Routes
Route::get('/', function () {
    return view('welcome-enhanced');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::get('/employee/login', [AuthenticatedSessionController::class, 'createEmployeeLogin'])->name('employee.login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
    Route::post('/employee/login', [AuthenticatedSessionController::class, 'storeEmployee'])->name('employee.login.store');
});

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Admin Routes (Super Admin, Admin)
    Route::middleware(['role:super_admin,admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [EnhancedAdminController::class, 'dashboard'])->name('dashboard');
        
        // User Management
        Route::get('/users', [EnhancedAdminController::class, 'users'])->name('users');
        Route::get('/users/create', [EnhancedAdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [EnhancedAdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}/edit', [EnhancedAdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [EnhancedAdminController::class, 'updateUser'])->name('users.update');
        Route::post('/users/{user}/toggle-status', [EnhancedAdminController::class, 'toggleUserStatus'])
            ->name('users.toggle-status');
        
        // Department Management (Only Super Admin and Admin)
        Route::get('/departments', [EnhancedAdminController::class, 'departments'])->name('departments');
        Route::get('/departments/create', [EnhancedAdminController::class, 'createDepartment'])->name('departments.create');
        Route::post('/departments', [EnhancedAdminController::class, 'storeDepartment'])->name('departments.store');
        Route::get('/departments/{department}/edit', [EnhancedAdminController::class, 'editDepartment'])->name('departments.edit');
        Route::put('/departments/{department}', [EnhancedAdminController::class, 'updateDepartment'])->name('departments.update');
        Route::delete('/departments/{department}', [EnhancedAdminController::class, 'deleteDepartment'])->name('departments.delete');
        
        // Reports
        Route::get('/reports', [EnhancedAdminController::class, 'reports'])->name('reports');
        Route::post('/export/reports', [EnhancedAdminController::class, 'exportReports'])->name('export.reports');
        Route::post('/export/audit-logs', [EnhancedAdminController::class, 'exportAuditLogs'])->name('export.audit-logs');
        
        // Settings
        Route::get('/settings', [EnhancedAdminController::class, 'settings'])->name('settings');
        Route::put('/settings', [EnhancedAdminController::class, 'updateSettings'])->name('settings.update');
        Route::post('/clear-cache', [EnhancedAdminController::class, 'clearCache'])->name('clear-cache');
        Route::post('/optimize-database', [EnhancedAdminController::class, 'optimizeDatabase'])->name('optimize-database');
    });

    // HR Routes (HR specific)
    Route::middleware(['role:hr'])->prefix('hr')->name('hr.')->group(function () {
        Route::get('/dashboard', [EnhancedAdminController::class, 'hrDashboard'])->name('dashboard');
        
        // Leave Applications (HR can view and approve)
        Route::get('/leave-applications', [EnhancedAdminController::class, 'hrLeaveApplications'])->name('leave-applications');
        Route::get('/leave-applications/{application}', [EnhancedAdminController::class, 'showLeaveApplication'])->name('leave-applications.show');
        Route::post('/leave-applications/{application}/approve', [EnhancedAdminController::class, 'approveLeaveApplication'])->name('leave-applications.approve');
        Route::post('/leave-applications/{application}/reject', [EnhancedAdminController::class, 'rejectLeaveApplication'])->name('leave-applications.reject');
        
        // Leave Plans (HR has CRUD functionality)
        Route::get('/leave-plans', [LeavePlanController::class, 'index'])->name('leave-plans.index');
        Route::get('/leave-plans/dashboard', [LeavePlanController::class, 'dashboard'])->name('leave-plans.dashboard');
        Route::get('/leave-plans/report', [LeavePlanController::class, 'report'])->name('leave-plans.report');
        Route::get('/leave-plans/create', [LeavePlanController::class, 'create'])->name('leave-plans.create');
        Route::post('/leave-plans', [LeavePlanController::class, 'store'])->name('leave-plans.store');
        Route::get('/leave-plans/{leavePlan}/edit', [LeavePlanController::class, 'edit'])->name('leave-plans.edit');
        Route::put('/leave-plans/{leavePlan}', [LeavePlanController::class, 'update'])->name('leave-plans.update');
        Route::delete('/leave-plans/{leavePlan}', [LeavePlanController::class, 'destroy'])->name('leave-plans.destroy');
        
        // Departments (HR can only view)
        Route::get('/departments', [EnhancedAdminController::class, 'hrDepartments'])->name('departments');
        
        // Notifications
        Route::get('/notifications', [EnhancedAdminController::class, 'hrNotifications'])->name('notifications');
    });

    // Employee and Head of Department Routes
    Route::middleware(['role:employee,head_of_department,hod'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile/password', [DashboardController::class, 'updatePassword'])->name('profile.password.update');
    });

    // Analytics Routes
    Route::middleware(['auth'])->prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/leave', [LeaveAnalyticsController::class, 'index'])->name('leave');
        Route::post('/export', [LeaveAnalyticsController::class, 'export'])->name('export');
    });
    Route::resource('leave-applications', EnhancedLeaveApplicationController::class)->names([
        'index' => 'leave-applications.index',
        'create' => 'leave-applications.create',
        'store' => 'leave-applications.store',
        'show' => 'leave-applications.show',
        'edit' => 'leave-applications.edit',
        'update' => 'leave-applications.update',
        'destroy' => 'leave-applications.destroy',
    ]);

    // Leave application actions (protected by role middleware)
    Route::middleware(['role:super_admin,admin,hr,head_of_department,hod'])->group(function () {
        Route::get('/pending-applications', [EnhancedLeaveApplicationController::class, 'pendingApplications'])
            ->name('pending.applications');
        Route::get('leave-applications/pending', [EnhancedLeaveApplicationController::class, 'pendingApplications'])
            ->name('leave-applications.pending');
        Route::post('leave-applications/{leaveApplication}/approve', [EnhancedLeaveApplicationController::class, 'approve'])
            ->name('leave-applications.approve');
        Route::post('leave-applications/{leaveApplication}/reject', [EnhancedLeaveApplicationController::class, 'reject'])
            ->name('leave-applications.reject');
        Route::post('leave-applications/{leaveApplication}/mark-read', [EnhancedLeaveApplicationController::class, 'markAsRead'])
            ->name('leave-applications.mark-read');
    });

    // Leave Plan Routes (accessible by all authenticated users)
    Route::resource('leave-plans', LeavePlanController::class)->names([
        'index' => 'leave-plans.index',
        'create' => 'leave-plans.create',
        'store' => 'leave-plans.store',
        'show' => 'leave-plans.show',
        'edit' => 'leave-plans.edit',
        'update' => 'leave-plans.update',
        'destroy' => 'leave-plans.destroy',
    ]);
    
    Route::post('leave-plans/{leavePlan}/approve', [LeavePlanController::class, 'approve'])
        ->name('leave-plans.approve');
    Route::post('leave-plans/{leavePlan}/reject', [LeavePlanController::class, 'reject'])
        ->name('leave-plans.reject');
});

Route::get('/test-pending', [EnhancedLeaveApplicationController::class, 'pendingApplications'])
    ->name('test-pending');

// Role-based dashboard routes
Route::middleware(['auth', 'role:head_of_department,hod'])->group(function () {
    Route::get('/hod/dashboard', [EnhancedAdminController::class, 'hodDashboard'])
        ->name('hod.dashboard');
});

Route::middleware(['auth', 'role:hr'])->group(function () {
    Route::get('/hr/dashboard', [EnhancedAdminController::class, 'hrDashboard'])
        ->name('hr.dashboard');
});

// Admin Routes (protected by role middleware)
Route::middleware(['role:super_admin,admin,hr'])->group(function () {
    Route::get('/admin/reports', [EnhancedAdminController::class, 'reports'])
        ->name('admin.reports');
    Route::get('/admin/settings', [EnhancedAdminController::class, 'settings'])
        ->name('admin.settings');
    Route::put('/admin/settings', [EnhancedAdminController::class, 'updateSettings'])
        ->name('admin.settings.update');
});

// Super Admin Routes (protected by role middleware)
Route::middleware(['role:super_admin,admin'])->group(function () {
    Route::get('/admin/audit-logs', [EnhancedAdminController::class, 'auditLogs'])
        ->name('admin.audit-logs');
});
