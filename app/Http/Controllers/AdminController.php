<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\LeaveApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalEmployees = User::where('role', 'employee')->count();
        $totalDepartments = Department::count();
        $pendingApplications = LeaveApplication::where('status', 'pending')->count();
        $recentApplications = LeaveApplication::with(['user', 'leaveType', 'approver'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalEmployees',
            'totalDepartments',
            'pendingApplications',
            'recentApplications'
        ));
    }

    public function reports()
    {
        $stats = [
            'total_employees' => User::count(),
            'total_departments' => Department::count(),
            'pending_applications' => LeaveApplication::where('status', 'pending')->count(),
            'approved_today' => LeaveApplication::where('status', 'approved')->whereDate('updated_at', today())->count(),
            'on_leave_today' => LeaveApplication::where('status', 'approved')->where('start_date', '<=', today())->where('end_date', '>=', today())->count(),
        ];

        $recentApplications = LeaveApplication::with(['user', 'leaveType'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.reports', compact('stats', 'recentApplications'));
    }

    public function settings()
    {
        $systemSettings = [
            'company_name' => config('app.name', 'Employee Leave Management System'),
            'timezone' => config('app.timezone', 'UTC'),
            'locale' => config('app.locale', 'en'),
            'maintenance_mode' => app()->isDownForMaintenance(),
        ];

        return view('admin.settings', compact('systemSettings'));
    }

    public function auditLogs()
    {
        // For now, return a simple view. In a real implementation, you would
        // fetch actual audit logs from a dedicated audit log table
        $auditLogs = collect([
            [
                'id' => 1,
                'user' => 'System',
                'action' => 'System initialized',
                'ip_address' => request()->ip(),
                'created_at' => now(),
            ],
        ]);

        return view('admin.audit-logs', compact('auditLogs'));
    }

    public function createEmployee()
    {
        $departments = Department::all();
        return view('admin.employees-create', compact('departments'));
    }

    public function storeEmployee(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'gender' => 'required|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date|before:today',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'phone_number' => 'nullable|string|max:20',
            'department_id' => 'required|exists:departments,id',
        ]);

        $employee = User::create([
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
            'role' => 'employee',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.employees')
            ->with('success', 'Employee created successfully.');
    }

    public function editEmployee(User $employee)
    {
        if ($employee->role !== 'employee') {
            abort(404);
        }

        $departments = Department::all();
        return view('admin.employees-edit', compact('employee', 'departments'));
    }

    public function updateEmployee(Request $request, User $employee)
    {
        if ($employee->role !== 'employee') {
            abort(404);
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $employee->id,
            'gender' => 'required|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date|before:today',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'phone_number' => 'nullable|string|max:20',
            'department_id' => 'required|exists:departments,id',
        ]);

        $employee->update($request->all());

        return redirect()->route('admin.employees')
            ->with('success', 'Employee updated successfully.');
    }

    public function toggleEmployeeStatus(User $employee)
    {
        if ($employee->role !== 'employee') {
            abort(404);
        }

        $employee->update([
            'status' => $employee->status === 'active' ? 'inactive' : 'active'
        ]);

        return redirect()->back()
            ->with('success', 'Employee status updated successfully.');
    }
}
