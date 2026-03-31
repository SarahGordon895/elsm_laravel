<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\LeaveApplication;
use App\Models\User;
use App\Models\UserNotificationPreference;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class DashboardController extends Controller
{
    private const NOTIFICATION_EVENT_LABELS = [
        'leave_application_applied' => 'My leave application submitted',
        'leave_application_approved' => 'Leave application approved',
        'leave_application_rejected' => 'Leave application rejected',
        'leave_application_approved_by_management' => 'Leave approved by management',
        'leave_application_rejected_by_management' => 'Leave rejected by management',
        'leave_plan_created' => 'Leave plan created',
        'leave_plan_approved' => 'Leave plan approved',
        'leave_plan_rejected' => 'Leave plan rejected',
        'hr_leave_application' => 'HR notified about leave application',
        'hr_sick_leave_proof_missing' => 'HR sick leave proof alerts',
        'account_created_by_management' => 'Account creation by management',
        'account_status_changed_by_management' => 'Account status changes',
        'profile_updated_by_management' => 'Profile updates by management',
        'department_updated_by_management' => 'Department updates',
    ];

    public function index()
    {
        $user = Auth::user();

        // Get dashboard data based on user role
        $stats = [
            'total_leaves_applied' => LeaveApplication::where('user_id', $user->id)->count(),
            'pending_leaves' => LeaveApplication::where('user_id', $user->id)->where('status', 'pending')->count(),
            'approved_leaves' => LeaveApplication::where('user_id', $user->id)->where('status', 'approved')->count(),
            'rejected_leaves' => LeaveApplication::where('user_id', $user->id)->where('status', 'rejected')->count(),
        ];

        // For Head of Department, show department stats too
        if ($user->role === 'head_of_department') {
            $stats['department_pending_leaves'] = LeaveApplication::whereHas('user', function($query) use ($user) {
                $query->where('department_id', $user->department_id);
            })->where('status', 'pending')->count();
            
            $stats['department_total_employees'] = User::where('department_id', $user->department_id)
                ->where('role', 'employee')
                ->count();
        }

        $recentLeaveApplications = LeaveApplication::where('user_id', $user->id)
            ->with('leaveType')
            ->latest()
            ->take(5)
            ->get();

        // Get leave balances
        $leaveBalances = \App\Models\LeaveBalance::with('leaveType')
            ->where('user_id', $user->id)
            ->where('year', date('Y'))
            ->whereHas('leaveType', function ($query) {
                $query->where('is_active', true)
                    ->whereIn('name', ['Annual Leave', 'Sick Leave', 'Maternity Leave', 'Paternity Leave']);
            })
            ->get();

        // Get notifications (unread) - handle if table doesn't exist
        $notifications = collect();
        $notificationsCount = 0;
        
        try {
            $notifications = \App\Models\SystemNotification::where('user_id', $user->id)
                ->where('is_read', false)
                ->latest()
                ->take(10)
                ->get();
            $notificationsCount = $notifications->count();
        } catch (\Exception $e) {
            // Notifications table doesn't exist or other error
            $notificationsCount = 0;
        }

        // Get pending count for managers and admins
        $pendingCount = 0;
        if (in_array($user->role, ['super_admin', 'admin', 'hr', 'head_of_department'])) {
            if ($user->role === 'head_of_department') {
                // HOD sees pending applications from their department
                $pendingCount = LeaveApplication::whereHas('user', function($query) use ($user) {
                    $query->where('department_id', $user->department_id);
                })->where('status', 'pending')->count();
            } else {
                // Admins and HR see all pending applications
                $pendingCount = LeaveApplication::where('status', 'pending')->count();
            }
        }

        return view('dashboard', compact('stats', 'recentLeaveApplications', 'leaveBalances', 'notifications', 'notificationsCount', 'pendingCount'));
    }

    public function profile()
    {
        $user = Auth::user()->load('department');
        $allowedLeaveTypes = ['Annual Leave', 'Sick Leave', 'Maternity Leave', 'Paternity Leave'];
        $leaveBalances = $user->leaveBalances()
            ->with('leaveType')
            ->whereHas('leaveType', function ($query) use ($allowedLeaveTypes) {
                $query->where('is_active', true)->whereIn('name', $allowedLeaveTypes);
            })
            ->orderByDesc('year')
            ->get();

        $notificationPreferences = $user->notificationPreferences()
            ->whereIn('event_type', array_keys(self::NOTIFICATION_EVENT_LABELS))
            ->get()
            ->keyBy('event_type');
        $profileUpdatedAt = AuditLog::where('user_id', $user->id)
            ->where('model_type', User::class)
            ->where('model_id', $user->id)
            ->where('action', 'profile_updated')
            ->latest()
            ->value('created_at');
        $passwordChangedAt = AuditLog::where('user_id', $user->id)
            ->where('model_type', User::class)
            ->where('model_id', $user->id)
            ->where('action', 'password_changed')
            ->latest()
            ->value('created_at');

        $notificationEventLabels = self::NOTIFICATION_EVENT_LABELS;

        return view('profile', compact('user', 'leaveBalances', 'notificationPreferences', 'notificationEventLabels', 'profileUpdatedAt', 'passwordChangedAt'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
        ]);

        $oldValues = $user->only(['first_name', 'last_name', 'email', 'phone_number', 'address', 'date_of_birth', 'gender']);

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
        ]);

        AuditLog::log('profile_updated', $user, $user->id, $oldValues, $user->only(['first_name', 'last_name', 'email', 'phone_number', 'address', 'date_of_birth', 'gender']));

        return redirect()->route('profile')
            ->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        AuditLog::log('password_changed', $user, $user->id);

        return redirect()->route('profile')
            ->with('success', 'Password updated successfully!');
    }

    public function updateNotificationPreferences(Request $request)
    {
        $user = Auth::user();

        $user->update([
            'notify_system' => $request->boolean('notify_system'),
            'notify_email' => $request->boolean('notify_email'),
            'notify_sms' => $request->boolean('notify_sms'),
        ]);

        return redirect()->route('profile')
            ->with('success', 'Notification preferences updated successfully!');
    }

    public function updateNotificationEventPreferences(Request $request)
    {
        $user = Auth::user();
        $eventTypes = array_keys(self::NOTIFICATION_EVENT_LABELS);

        foreach ($eventTypes as $eventType) {
            $prefixed = str_replace('.', '_', $eventType);
            UserNotificationPreference::updateOrCreate(
                ['user_id' => $user->id, 'event_type' => $eventType],
                [
                    'notify_system' => $request->boolean("event_notify_system_{$prefixed}"),
                    'notify_email' => $request->boolean("event_notify_email_{$prefixed}"),
                    'notify_sms' => $request->boolean("event_notify_sms_{$prefixed}"),
                ]
            );
        }

        return redirect()->route('profile')
            ->with('success', 'Event notification preferences updated successfully!');
    }
}
