<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LeaveAnalyticsExport;

class LeaveAnalyticsController extends Controller
{
    /**
     * Display the leave analytics dashboard.
     */
    public function index(Request $request)
    {
        $this->authorize('view-reports');
        $allowedLeaveTypeNames = ['Annual Leave', 'Sick Leave', 'Maternity Leave', 'Paternity Leave'];

        // Get filter parameters
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', null);
        $department = $request->get('department', null);
        $leaveType = $request->get('leave_type', null);
        $status = $request->get('status', null);

        // Base query
        $query = LeaveApplication::with(['user', 'leaveType', 'department'])
            ->whereYear('start_date', $year)
            ->whereHas('leaveType', function ($q) use ($allowedLeaveTypeNames) {
                $q->where('is_active', true)->whereIn('name', $allowedLeaveTypeNames);
            });

        // Apply filters
        if ($month) {
            $query->whereMonth('start_date', $month);
        }

        if ($department) {
            $query->whereHas('user', function ($q) use ($department) {
                $q->where('department_id', $department);
            });
        }

        if ($leaveType) {
            $query->where('leave_type_id', $leaveType);
        }

        if ($status) {
            $query->where('status', $status);
        }

        // Get analytics data
        $applications = $query->get();

        // Calculate statistics
        $stats = [
            'total_applications' => $applications->count(),
            'approved_applications' => $applications->where('status', 'approved')->count(),
            'rejected_applications' => $applications->where('status', 'rejected')->count(),
            'pending_applications' => $applications->where('status', 'pending')->count(),
            'total_days_taken' => $applications->where('status', 'approved')->sum('duration'),
        ];

        // Applications by month
        $monthlyData = $applications->groupBy(function ($item) {
            return Carbon::parse($item->start_date)->format('F');
        })->map(function ($month) {
            return [
                'total' => $month->count(),
                'approved' => $month->where('status', 'approved')->count(),
                'rejected' => $month->where('status', 'rejected')->count(),
                'pending' => $month->where('status', 'pending')->count(),
            ];
        });

        // Applications by department
        $departmentData = $applications->groupBy('user.department_id')->map(function ($dept) {
            return [
                'department_name' => $dept->first()->user->department->name ?? 'Unknown',
                'total' => $dept->count(),
                'approved' => $dept->where('status', 'approved')->count(),
                'rejected' => $dept->where('status', 'rejected')->count(),
                'pending' => $dept->where('status', 'pending')->count(),
            ];
        });

        // Applications by leave type
        $leaveTypeData = $applications->groupBy('leave_type_id')->map(function ($type) {
            return [
                'leave_type_name' => $type->first()->leaveType->name ?? 'Unknown',
                'total' => $type->count(),
                'approved' => $type->where('status', 'approved')->count(),
                'rejected' => $type->where('status', 'rejected')->count(),
                'pending' => $type->where('status', 'pending')->count(),
            ];
        });

        // Get filter options
        $departments = \App\Models\Department::orderBy('name')->get();
        $leaveTypes = LeaveType::where('is_active', true)
            ->whereIn('name', $allowedLeaveTypeNames)
            ->orderBy('name')
            ->get();
        $years = range(Carbon::now()->year - 5, Carbon::now()->year);
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        return view('analytics.leave-analytics', compact(
            'stats',
            'monthlyData',
            'departmentData',
            'leaveTypeData',
            'departments',
            'leaveTypes',
            'years',
            'months',
            'year',
            'month',
            'department',
            'leaveType',
            'status'
        ));
    }

    /**
     * Export analytics data to Excel.
     */
    public function export(Request $request)
    {
        $this->authorize('view-reports');
        $allowedLeaveTypeNames = ['Annual Leave', 'Sick Leave', 'Maternity Leave', 'Paternity Leave'];

        // Get filter parameters
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', null);
        $department = $request->get('department', null);
        $leaveType = $request->get('leave_type', null);
        $status = $request->get('status', null);

        // Base query
        $query = LeaveApplication::with(['user', 'leaveType', 'department'])
            ->whereYear('start_date', $year)
            ->whereHas('leaveType', function ($q) use ($allowedLeaveTypeNames) {
                $q->where('is_active', true)->whereIn('name', $allowedLeaveTypeNames);
            });

        // Apply filters
        if ($month) {
            $query->whereMonth('start_date', $month);
        }

        if ($department) {
            $query->whereHas('user', function ($q) use ($department) {
                $q->where('department_id', $department);
            });
        }

        if ($leaveType) {
            $query->where('leave_type_id', $leaveType);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $applications = $query->get();

        // Prepare data for export
        $exportData = $applications->map(function ($app) {
            return [
                'ID' => $app->id,
                'Employee' => $app->user->full_name,
                'Department' => $app->user->department->name ?? 'N/A',
                'Leave Type' => $app->leaveType->name,
                'Start Date' => $app->start_date->format('Y-m-d'),
                'End Date' => $app->end_date->format('Y-m-d'),
                'Duration' => $app->duration,
                'Status' => ucfirst($app->status),
                'Reason' => $app->reason,
                'Applied At' => $app->created_at->format('Y-m-d H:i:s'),
            ];
        });

        $filename = 'leave-analytics-' . Carbon::now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new LeaveAnalyticsExport($exportData), $filename);
    }
}
