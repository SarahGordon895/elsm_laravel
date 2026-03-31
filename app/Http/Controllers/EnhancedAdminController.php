<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\LeaveApplication;
use App\Models\LeaveBalance;
use App\Models\LeavePlan;
use App\Models\Role;
use App\Models\Permission;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class EnhancedAdminController extends Controller
{
    /**
     * HOD Dashboard - Head of Department specific dashboard
     */
    public function hodDashboard()
    {
        $this->authorize('view-dashboard');
        
        $user = Auth::user();
        $departmentId = $user->department_id;
        
        // Get department employees
        $teamMembers = User::where('department_id', $departmentId)
            ->where('role', 'employee')
            ->get();
        
        // Get pending leave requests from department
        $pendingApplications = LeaveApplication::with(['user', 'leaveType'])
            ->whereHas('user', function($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get approved today
        $approvedTodayCount = LeaveApplication::with(['user'])
            ->whereHas('user', function($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->where('status', 'approved')
            ->whereDate('approved_at', today())
            ->count();
        
        // Get current leave count
        $onLeaveCount = LeaveApplication::with(['user'])
            ->whereHas('user', function($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->where('status', 'approved')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->count();
        
        // Get recent activities
        $recentActivities = collect([
            [
                'type' => 'approved',
                'description' => 'Leave request approved for John Doe',
                'created_at' => now()->subHours(2)
            ],
            [
                'type' => 'rejected',
                'description' => 'Leave request rejected for Jane Smith',
                'created_at' => now()->subHours(4)
            ],
            [
                'type' => 'pending',
                'description' => 'New leave request from Mike Johnson',
                'created_at' => now()->subMinutes(30)
            ]
        ]);
        
        return view('hod.dashboard', compact(
            'pendingApplications',
            'pendingCount',
            'approvedTodayCount',
            'onLeaveCount',
            'teamSize',
            'recentActivities'
        ));
    }
    
    /**
     * HR Leave Applications
     */
    public function hrLeaveApplications()
    {
        $applications = LeaveApplication::with(['user', 'leaveType', 'approver'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('hr.leave-applications', compact('applications'));
    }
    
    /**
     * HR Departments (View Only)
     */
    public function hrDepartments()
    {
        $departments = Department::withCount(['users' => function ($q) {
            $q->where('role', 'employee');
        }])->get();
        
        return view('hr.departments', compact('departments'));
    }
    
    /**
     * Show Leave Application
     */
    public function showLeaveApplication(LeaveApplication $application)
    {
        return view('hr.leave-application-detail', compact('application'));
    }
    
    /**
     * Approve Leave Application
     */
    public function approveLeaveApplication(LeaveApplication $application)
    {
        $application->status = 'approved';
        $application->approved_by = Auth::id();
        $application->approved_at = now();
        $application->save();
        
        // Send notification to employee
        // Add notification logic here
        
        return response()->json(['success' => true, 'message' => 'Leave application approved successfully']);
    }
    
    /**
     * Reject Leave Application
     */
    public function rejectLeaveApplication(Request $request, LeaveApplication $application)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);
        
        $application->status = 'rejected';
        $application->rejected_by = Auth::id();
        $application->rejected_at = now();
        $application->rejection_reason = $request->reason;
        $application->save();
        
        // Send notification to employee
        // Add notification logic here
        
        return response()->json(['success' => true, 'message' => 'Leave application rejected successfully']);
    }
    public function hrNotifications()
    {
        $notifications = \App\Models\Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('hr.notifications', compact('notifications'));
    }
    public function hrDashboard()
    {
        $this->authorize('view-dashboard');
        
        $user = Auth::user();
        
        // Get statistics
        $totalEmployees = User::where('role', 'employee')->count();
        $pendingCount = LeaveApplication::where('status', 'pending')->count();
        $approvedThisMonth = LeaveApplication::where('status', 'approved')
            ->whereMonth('approved_at', now()->month)
            ->whereYear('approved_at', now()->year)
            ->count();
        
        // Get low balance employees
        $lowBalanceCount = LeaveBalance::with(['user', 'leaveType'])
            ->whereHas('user', function($query) {
                $query->where('role', 'employee');
            })
            ->whereRaw('(balance_days - used_days) <= 2')
            ->count();
        
        // Get new applicants (placeholder)
        $newApplicantsCount = 0;
        
        // Get recent applications
        $recentApplications = LeaveApplication::with(['user', 'leaveType'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get recent employees
        $recentEmployees = User::with('department')
            ->where('role', 'employee')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get leave statistics
        $leaveStats = [
            'Annual Leave' => [
                'count' => LeaveApplication::whereHas('leaveType', function($query) {
                    $query->where('name', 'Annual Leave');
                })->where('status', 'approved')->count(),
                'percentage' => 75
            ],
            'Sick Leave' => [
                'count' => LeaveApplication::whereHas('leaveType', function($query) {
                    $query->where('name', 'Sick Leave');
                })->where('status', 'approved')->count(),
                'percentage' => 15
            ],
            'Personal Leave' => [
                'count' => LeaveApplication::whereHas('leaveType', function($query) {
                    $query->where('name', 'Personal Leave');
                })->where('status', 'approved')->count(),
                'percentage' => 10
            ]
        ];
        
        // Get HR notifications
        $hrNotifications = collect([
            [
                'type' => 'alert',
                'message' => '3 employees have low leave balance',
                'created_at' => now()->subHours(1)
            ],
            [
                'type' => 'warning',
                'message' => 'Pending leave requests require attention',
                'created_at' => now()->subMinutes(30)
            ],
            [
                'type' => 'info',
                'message' => 'Monthly leave report is ready',
                'created_at' => now()->subHours(3)
            ]
        ]);
        
        return view('hr.dashboard', compact(
            'totalEmployees',
            'pendingCount',
            'approvedThisMonth',
            'lowBalanceCount',
            'newApplicantsCount',
            'recentApplications',
            'recentEmployees',
            'leaveStats',
            'hrNotifications'
        ));
    }

    public function dashboard()
    {
        // Remove authorization check - let the route middleware handle it
        // $this->authorize('view-dashboard');
        
        $user = Auth::user();
        
        // Get statistics
        $stats = [
            'total_employees' => User::where('role', 'employee')->count(),
            'total_departments' => Department::count(),
            'pending_leaves' => LeaveApplication::where('status', 'pending')->count(),
            'approved_leaves' => LeaveApplication::where('status', 'approved')->count(),
            'rejected_leaves' => LeaveApplication::where('status', 'rejected')->count(),
        ];
        
        // Recent applications
        $recentApplications = LeaveApplication::with(['user', 'leaveType', 'approver'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Department statistics
        $departmentStats = Department::withCount(['users' => function ($q) {
            $q->where('role', 'employee');
        }])->get();
        
        // Leave balance summary
        $leaveBalanceSummary = LeaveBalance::selectRaw('
                leave_types.name, 
                SUM(balance_days + carry_over_days) as total_available, 
                SUM(used_days) as total_used
            ')
            ->join('leave_types', 'leave_balances.leave_type_id', '=', 'leave_types.id')
            ->where('year', date('Y'))
            ->groupBy('leave_types.id', 'leave_types.name')
            ->get();
        
        // Recent audit logs
        $recentAuditLogs = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        return view('admin.enhanced-dashboard', compact(
            'stats',
            'recentApplications',
            'departmentStats',
            'leaveBalanceSummary',
            'recentAuditLogs'
        ));
    }

    /**
     * Admin Dashboard - Alias for dashboard method
     */
    public function adminDashboard()
    {
        return $this->dashboard();
    }

    public function users(Request $request)
    {
        $this->authorize('view-users');
        
        $query = User::with(['department', 'manager', 'roles']);
        
        // Filters
        if ($request->department_id) {
            $query->where('department_id', $request->department_id);
        }
        
        if ($request->role) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('employee_id', 'like', '%' . $request->search . '%');
            });
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        $departments = Department::all();
        $roles = Role::all();
        
        return view('admin.users', compact('users', 'departments', 'roles'));
    }

    public function createUser()
    {
        $this->authorize('create-users');
        
        $departments = Department::all();
        $roles = Role::where('is_system_role', false)->get();
        $managers = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['manager', 'admin', 'hr']);
        })->get();
        
        return view('admin.create-user', compact('departments', 'roles', 'managers'));
    }

    public function storeUser(Request $request)
    {
        $this->authorize('create-users');
        
        $request->validate([
            'employee_id' => 'required|string|max:20|unique:users,employee_id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'gender' => 'required|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date|before:today',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'phone_number' => 'nullable|string|max:20',
            'department_id' => 'required|exists:departments,id',
            'manager_id' => 'nullable|exists:users,id',
            'employment_type' => 'required|in:full_time,part_time,contract,intern',
            'join_date' => 'required|date|before_or_equal:today',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'employee_id' => $request->employee_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'address' => $request->address,
                'city' => $request->city,
                'country' => $request->country,
                'phone_number' => $request->phone_number,
                'department_id' => $request->department_id,
                'manager_id' => $request->manager_id,
                'employment_type' => $request->employment_type,
                'join_date' => $request->join_date,
                'status' => 'active',
            ]);
            
            // Assign roles
            $user->roles()->attach($request->roles);
            
            // Initialize leave balances
            $currentYear = date('Y');
            $leaveTypes = \App\Models\LeaveType::all();
            
            foreach ($leaveTypes as $leaveType) {
                $balanceDays = $this->calculateInitialBalance($leaveType, $user);
                LeaveBalance::initializeBalance($user->id, $leaveType->id, $balanceDays, $currentYear);
            }
            
            // Log audit trail
            AuditLog::log('created', $user, Auth::id());
            
            DB::commit();
            
            return redirect()->route('admin.users')
                ->with('success', 'User created successfully.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Failed to create user. Please try again.');
        }
    }

    public function editUser(User $user)
    {
        $this->authorize('edit-users');
        
        $departments = Department::all();
        $roles = Role::all();
        $managers = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['manager', 'admin', 'hr']);
        })->where('id', '!=', $user->id)->get();
        
        $user->load('roles');
        
        return view('admin.edit-user', compact('user', 'departments', 'roles', 'managers'));
    }

    public function updateUser(Request $request, User $user)
    {
        $this->authorize('edit-users');
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'gender' => 'required|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date|before:today',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'phone_number' => 'nullable|string|max:20',
            'department_id' => 'required|exists:departments,id',
            'manager_id' => 'nullable|exists:users,id',
            'employment_type' => 'required|in:full_time,part_time,contract,intern',
            'status' => 'required|in:active,inactive,on_leave,terminated',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ]);

        $oldValues = $user->getAttributes();
        
        $user->update($request->except('roles'));
        
        // Update roles
        $user->roles()->sync($request->roles);
        
        // Log audit trail
        AuditLog::log('updated', $user, Auth::id(), $oldValues);
        
        return redirect()->route('admin.users')
            ->with('success', 'User updated successfully.');
    }

    public function toggleUserStatus(User $user)
    {
        $this->authorize('edit-users');
        
        $oldStatus = $user->status;
        $user->update([
            'status' => $user->status === 'active' ? 'inactive' : 'active'
        ]);
        
        // Log audit trail
        AuditLog::log('status_changed', $user, Auth::id(), ['status' => $oldStatus]);
        
        return redirect()->back()
            ->with('success', 'User status updated successfully.');
    }

    public function departments()
    {
        $this->authorize('view-departments');
        
        $departments = Department::with(['manager', 'users' => function($query) {
            $query->select('id', 'first_name', 'last_name', 'email', 'department_id');
        }])->get();
        
        // Add leave statistics to each department
        foreach ($departments as $department) {
            $department->pending_leaves = LeaveApplication::whereHas('user', function($query) use ($department) {
                $query->where('department_id', $department->id);
            })->where('status', 'pending')->count();
            
            $department->active_leave_plans = LeavePlan::whereHas('user', function($query) use ($department) {
                $query->where('department_id', $department->id);
            })->where('status', 'approved')->count();
        }
        
        return view('admin.departments', compact('departments'));
    }

    public function reports()
    {
        $this->authorize('view-reports');
        
        // Overall leave statistics
        $totalApplications = LeaveApplication::whereYear('created_at', date('Y'))->count();
        $approvedApplications = LeaveApplication::whereYear('created_at', date('Y'))->where('status', 'approved')->count();
        $rejectedApplications = LeaveApplication::whereYear('created_at', date('Y'))->where('status', 'rejected')->count();
        $pendingApplications = LeaveApplication::whereYear('created_at', date('Y'))->where('status', 'pending')->count();
        
        $leaveStats = [
            'total_applications' => $totalApplications,
            'approved_applications' => $approvedApplications,
            'rejected_applications' => $rejectedApplications,
            'pending_applications' => $pendingApplications,
            'approved_percentage' => $totalApplications > 0 ? round(($approvedApplications / $totalApplications) * 100, 1) : 0,
            'rejected_percentage' => $totalApplications > 0 ? round(($rejectedApplications / $totalApplications) * 100, 1) : 0,
            'by_type' => []
        ];
        
        // Leave statistics by type
        $leaveTypeStats = LeaveApplication::selectRaw('
                leave_types.name,
                COUNT(*) as total_applications,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending
            ')
            ->join('leave_types', 'leave_applications.leave_type_id', '=', 'leave_types.id')
            ->whereYear('leave_applications.created_at', date('Y'))
            ->groupBy('leave_types.id', 'leave_types.name')
            ->get();
            
        foreach($leaveTypeStats as $stat) {
            $leaveStats['by_type'][$stat->name] = $stat->total_applications;
        }
        
        // Department leave statistics
        $departmentStats = Department::selectRaw('
                departments.name,
                COUNT(DISTINCT users.id) as total_employees,
                COUNT(leave_applications.id) as total_applications,
                SUM(CASE WHEN leave_applications.status = "approved" THEN 1 ELSE 0 END) as approved_applications,
                SUM(CASE WHEN leave_applications.status = "pending" THEN 1 ELSE 0 END) as pending_applications,
                SUM(CASE WHEN leave_applications.status = "rejected" THEN 1 ELSE 0 END) as rejected_applications
            ')
            ->leftJoin('users', 'departments.id', '=', 'users.department_id')
            ->leftJoin('leave_applications', 'users.id', '=', 'leave_applications.user_id')
            ->whereYear('leave_applications.created_at', date('Y'))
            ->groupBy('departments.id', 'departments.name')
            ->get();
        
        // Monthly trends
        $monthlyTrends = LeaveApplication::selectRaw('
                MONTH(created_at) as month,
                COUNT(*) as applications,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved
            ')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.reports', compact('leaveStats', 'departmentStats', 'monthlyTrends'));
    }

    public function auditLogs(Request $request)
    {
        $this->authorize('view-audit-logs');
        
        $query = AuditLog::with('user');
        
        if ($request->action) {
            $query->where('action', $request->action);
        }
        
        if ($request->model_type) {
            $query->where('model_type', 'App\\Models\\' . $request->model_type);
        }
        
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $auditLogs = $query->orderBy('created_at', 'desc')->paginate(20);
        $users = User::all();
        
        return view('admin.audit-logs', compact('auditLogs', 'users'));
    }

    public function exportReports(Request $request)
    {
        $this->authorize('view-reports');
        
        $period = $request->get('period', 'current');
        $year = $period === 'last' ? date('Y') - 1 : ($period === 'all' ? null : date('Y'));
        
        // Get report data
        $query = LeaveApplication::with(['user', 'leaveType', 'user.department']);
        
        if ($year) {
            $query->whereYear('created_at', $year);
        }
        
        $applications = $query->orderBy('created_at', 'desc')->get();
        
        // Create CSV content
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="leave_reports_' . ($year ?? 'all') . '.csv"',
        ];
        
        $callback = function() use ($applications) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'ID', 'Employee Name', 'Employee ID', 'Email', 'Department',
                'Leave Type', 'Start Date', 'End Date', 'Total Days', 'Reason',
                'Status', 'Applied Date', 'Approved Date', 'Approved By'
            ]);
            
            // CSV Data
            foreach ($applications as $app) {
                fputcsv($file, [
                    $app->id,
                    $app->user->full_name,
                    $app->user->employee_id,
                    $app->user->email,
                    $app->user->department?->name,
                    $app->leaveType->name,
                    $app->start_date->format('Y-m-d'),
                    $app->end_date->format('Y-m-d'),
                    $app->start_date->diffInDays($app->end_date) + 1,
                    strip_tags($app->reason),
                    ucfirst($app->status),
                    $app->created_at->format('Y-m-d H:i:s'),
                    $app->approved_at?->format('Y-m-d H:i:s'),
                    $app->approver?->full_name
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function exportAuditLogs(Request $request)
    {
        $this->authorize('view-audit-logs');
        
        // Get audit log data with filters
        $query = AuditLog::with('user');
        
        if ($request->action) {
            $query->where('action', $request->action);
        }
        
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $logs = $query->orderBy('created_at', 'desc')->get();
        
        // Create CSV content
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="audit_logs_' . date('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'ID', 'Timestamp', 'User Name', 'User Email', 'Action',
                'Subject Type', 'Subject ID', 'IP Address', 'Details', 'Created At'
            ]);
            
            // CSV Data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user->full_name,
                    $log->user->email,
                    ucfirst(str_replace('_', ' ', $log->action)),
                    $log->model_type ? class_basename($log->model_type) : '',
                    $log->model_id ?? '',
                    $log->ip_address ?? '',
                    $log->new_values ? json_encode($log->new_values) : '',
                    $log->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function settings()
    {
        $this->authorize('manage-system-settings');
        
        return view('admin.settings');
    }

    public function updateSettings(Request $request)
    {
        $this->authorize('manage-system-settings');
        
        // Validate settings
        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_address' => 'nullable|string|max:500',
            'enable_registration' => 'nullable|boolean',
            'require_approval' => 'nullable|boolean',
            'max_leave_days' => 'nullable|integer|min:1|max:365',
            'accrual_frequency' => 'nullable|in:monthly,quarterly,yearly',
            'carry_over_days' => 'nullable|integer|min:0|max:365',
            'enable_carry_over' => 'nullable|boolean',
            'email_leave_submitted' => 'nullable|boolean',
            'email_leave_approved' => 'nullable|boolean',
            'email_leave_rejected' => 'nullable|boolean',
            'notify_low_balance' => 'nullable|boolean',
            'low_balance_threshold' => 'nullable|integer|min:1|max:30',
        ]);

        // Save settings to database or cache
        foreach ($validated as $key => $value) {
            // For now, we'll store in cache. In production, you might want a settings table
            cache()->forever("settings.{$key}", $value);
        }

        return redirect()->route('admin.settings')
            ->with('success', 'Settings updated successfully!');
    }

    public function clearCache()
    {
        $this->authorize('manage-system-settings');
        
        // Clear application cache
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('route:clear');
        \Artisan::call('view:clear');
        
        return response()->json(['success' => true, 'message' => 'Cache cleared successfully!']);
    }

    public function optimizeDatabase()
    {
        $this->authorize('manage-system-settings');
        
        try {
            // Optimize database tables
            \DB::statement('OPTIMIZE TABLE users');
            \DB::statement('OPTIMIZE TABLE leave_applications');
            \DB::statement('OPTIMIZE TABLE leave_balances');
            \DB::statement('OPTIMIZE TABLE departments');
            \DB::statement('OPTIMIZE TABLE leave_types');
            
            return response()->json(['success' => true, 'message' => 'Database optimized successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to optimize database.']);
        }
    }

    public function createDepartment()
    {
        $this->authorize('create-departments');
        
        return view('admin.create-department');
    }

    public function storeDepartment(Request $request)
    {
        $this->authorize('create-departments');
        
        $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:10',
            'code' => 'required|string|max:20|unique:departments,code',
            'description' => 'nullable|string|max:1000',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        $department = Department::create($request->all());

        return response()->json(['success' => true, 'message' => 'Department created successfully!']);
    }

    public function editDepartment(Department $department)
    {
        $this->authorize('edit-departments');
        
        return response()->json($department);
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $this->authorize('edit-departments');
        
        $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:10',
            'code' => 'required|string|max:20|unique:departments,code,' . $department->id,
            'description' => 'nullable|string|max:1000',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        $department->update($request->all());

        return response()->json(['success' => true, 'message' => 'Department updated successfully!']);
    }

    public function deleteDepartment(Department $department)
    {
        $this->authorize('delete-departments');
        
        // Check if department has users
        if ($department->users()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot delete department with assigned users.']);
        }

        $department->delete();

        return response()->json(['success' => true, 'message' => 'Department deleted successfully!']);
    }

    public function getManagers()
    {
        $managers = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['super_admin', 'admin', 'hr', 'head_of_department']);
        })->get(['id', 'first_name', 'last_name']);

        return response()->json($managers);
    }

    private function calculateInitialBalance($leaveType, $user)
    {
        $monthsWorked = $user->join_date ? min(12, $user->join_date->diffInMonths(now())) : 12;
        
        switch ($leaveType->name) {
            case 'Annual Leave':
                return round(($leaveType->max_days_per_year / 12) * $monthsWorked, 2);
            case 'Sick Leave':
                return round(($leaveType->max_days_per_year / 12) * $monthsWorked, 2);
            default:
                return $leaveType->max_days_per_year;
        }
    }
}
