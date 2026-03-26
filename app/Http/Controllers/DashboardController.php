<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveApplication;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class DashboardController extends Controller
{
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
            ->get();

        // Get notifications (unread) - handle if table doesn't exist
        $notifications = collect();
        $notificationsCount = 0;
        
        try {
            $notifications = \App\Models\Notification::where('user_id', $user->id)
                ->where('read', false)
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
        $user = Auth::user();
        return view('profile', compact('user'));
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

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
        ]);

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

        return redirect()->route('profile')
            ->with('success', 'Password updated successfully!');
    }
}
