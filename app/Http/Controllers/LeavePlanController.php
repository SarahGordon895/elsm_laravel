<?php

namespace App\Http\Controllers;

use App\Models\LeavePlan;
use App\Models\LeaveType;
use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeavePlanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of leave plans.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'hr') {
            // HR can see all leave plans
            $leavePlans = LeavePlan::with(['user', 'leaveType', 'approver'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } elseif ($user->role === 'head_of_department') {
            // HOD can see leave plans for their department
            $leavePlans = LeavePlan::with(['user', 'leaveType', 'approver'])
                ->whereHas('user', function($query) use ($user) {
                    $query->where('department_id', $user->department_id);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            // Employees can only see their own leave plans
            $leavePlans = LeavePlan::with(['user', 'leaveType', 'approver'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        return view('leave-plans.index', compact('leavePlans'));
    }
    
    /**
     * Display official leave plan dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get leave plan statistics
        $stats = [
            'total_plans' => LeavePlan::count(),
            'pending_plans' => LeavePlan::where('status', 'pending')->count(),
            'approved_plans' => LeavePlan::where('status', 'approved')->count(),
            'this_month_plans' => LeavePlan::whereMonth('created_at', now()->month)->count(),
        ];
        
        // Get recent leave plans
        $recentPlans = LeavePlan::with(['user', 'leaveType'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get leave type distribution
        $leaveTypeDistribution = LeavePlan::join('leave_types', 'leave_plans.leave_type_id', '=', 'leave_types.id')
            ->selectRaw('leave_types.name, COUNT(*) as count')
            ->groupBy('leave_types.id', 'leave_types.name')
            ->orderBy('count', 'desc')
            ->get();
        
        return view('leave-plans.dashboard', compact('stats', 'recentPlans', 'leaveTypeDistribution'));
    }
    
    /**
     * Display official leave plan report
     */
    public function report()
    {
        $user = Auth::user();
        
        // Get all leave plans with filtering
        $leavePlans = LeavePlan::with(['user', 'leaveType', 'approver'])
            ->when($user->role === 'head_of_department', function($query) use ($user) {
                return $query->whereHas('user', function($q) use ($user) {
                    $q->where('department_id', $user->department_id);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Group by status
        $byStatus = $leavePlans->groupBy('status');
        
        // Group by department
        $byDepartment = $leavePlans->groupBy(function($plan) {
            return $plan->user->department->name ?? 'Unknown';
        });
        
        // Group by leave type
        $byLeaveType = $leavePlans->groupBy(function($plan) {
            return $plan->leaveType->name;
        });
        
        return view('leave-plans.report', compact('leavePlans', 'byStatus', 'byDepartment', 'byLeaveType'));
    }

    /**
     * Show the form for editing the specified leave plan.
     */
    public function edit(LeavePlan $leavePlan)
    {
        $user = Auth::user();
        
        // Check if user can edit this leave plan
        if ($user->role !== 'hr' && $leavePlan->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $leaveTypes = LeaveType::where('paid', true)->get();
        
        return view('leave-plans.edit', compact('leavePlan', 'leaveTypes'));
    }

    /**
     * Update the specified leave plan in storage.
     */
    public function update(Request $request, LeavePlan $leavePlan)
    {
        $user = Auth::user();
        
        // Check if user can update this leave plan
        if ($user->role !== 'hr' && $leavePlan->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);
        
        $leavePlan->update([
            'title' => $request->title,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'pending', // Reset to pending when edited
        ]);
        
        return redirect()->route('leave-plans.index')
            ->with('status', 'Leave plan updated successfully!');
    }
    public function create()
    {
        $this->authorize('manage-leave-plans');
        
        $leaveTypes = LeaveType::where('paid', true)->get();
        
        return view('leave-plans.create', compact('leaveTypes'));
    }

    /**
     * Store a newly created leave plan.
     */
    public function store(Request $request)
    {
        $this->authorize('manage-leave-plans');
        
        $validated = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'allocated_days' => 'required|integer|min:1|max:365',
            'effective_date' => 'required|date|after_or_equal:today',
            'expiry_date' => 'required|date|after:effective_date',
            'notes' => 'nullable|string|max:500',
        ]);

        $leaveType = LeaveType::findOrFail($validated['leave_type_id']);
        
        $leavePlan = LeavePlan::create([
            'user_id' => Auth::id(),
            'leave_type_id' => $validated['leave_type_id'],
            'allocated_days' => $validated['allocated_days'],
            'used_days' => 0,
            'remaining_days' => $validated['allocated_days'],
            'status' => 'pending',
            'notes' => $validated['notes'],
            'effective_date' => $validated['effective_date'],
            'expiry_date' => $validated['expiry_date'],
        ]);

        // Send notification to HR
        $hrUsers = User::whereHas('roles', function($query) {
            $query->where('name', 'hr');
        })->get();

        foreach ($hrUsers as $hr) {
            SystemNotification::createNotification(
                $hr->id,
                'leave_plan_created',
                'New Leave Plan Created',
                Auth::user()->full_name . ' has created a leave plan for ' . $leaveType->name . ' and is awaiting your approval.',
                'system',
                [
                    'leave_plan_id' => $leavePlan->id,
                    'employee_name' => Auth::user()->full_name,
                    'leave_type' => $leaveType->name,
                ],
                true, // send email
                true  // send SMS
            );
        }

        // Send notification to employee
        SystemNotification::sendLeavePlanNotification(
            Auth::id(),
            'created',
            ['leave_plan_id' => $leavePlan->id],
            true, // send email
            true  // send SMS
        );

        return redirect()->route('leave-plans.index')
            ->with('success', 'Leave plan created successfully and sent for HR approval.');
    }

    /**
     * Display the specified leave plan.
     */
    public function show(LeavePlan $leavePlan)
    {
        $this->authorize('view', $leavePlan);
        
        $leavePlan->load(['user', 'leaveType', 'approver']);
        
        return view('leave-plans.show', compact('leavePlan'));
    }

    /**
     * Approve a leave plan.
     */
    public function approve(LeavePlan $leavePlan)
    {
        $this->authorize('approve-leave-plans');
        
        if ($leavePlan->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending leave plans can be approved.');
        }

        $leavePlan->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
        ]);

        // Update or create leave balance
        $existingBalance = $leavePlan->user->leaveBalances()
            ->where('leave_type_id', $leavePlan->leave_type_id)
            ->first();

        if ($existingBalance) {
            $existingBalance->update([
                'available_days' => $leavePlan->allocated_days,
                'balance_days' => $leavePlan->allocated_days - $existingBalance->used_days,
            ]);
        } else {
            $leavePlan->user->leaveBalances()->create([
                'leave_type_id' => $leavePlan->leave_type_id,
                'available_days' => $leavePlan->allocated_days,
                'used_days' => 0,
                'balance_days' => $leavePlan->allocated_days,
                'year' => now()->year,
            ]);
        }

        // Send notification to employee
        SystemNotification::sendLeavePlanNotification(
            $leavePlan->user_id,
            'approved',
            ['leave_plan_id' => $leavePlan->id],
            true, // send email
            true  // send SMS
        );

        return redirect()->route('leave-plans.index')
            ->with('success', 'Leave plan approved successfully.');
    }

    /**
     * Reject a leave plan.
     */
    public function reject(Request $request, LeavePlan $leavePlan)
    {
        $this->authorize('reject-leave-plans');
        
        if ($leavePlan->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending leave plans can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $leavePlan->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        // Send notification to employee
        SystemNotification::sendLeavePlanNotification(
            $leavePlan->user_id,
            'rejected',
            [
                'leave_plan_id' => $leavePlan->id,
                'rejection_reason' => $validated['rejection_reason'],
            ],
            true, // send email
            true  // send SMS
        );

        return redirect()->route('leave-plans.index')
            ->with('success', 'Leave plan rejected successfully.');
    }

    /**
     * Remove the specified leave plan.
     */
    public function destroy(LeavePlan $leavePlan)
    {
        $this->authorize('delete', $leavePlan);
        
        if ($leavePlan->status === 'approved') {
            return redirect()->back()
                ->with('error', 'Approved leave plans cannot be deleted.');
        }

        $leavePlan->delete();

        return redirect()->route('leave-plans.index')
            ->with('success', 'Leave plan deleted successfully.');
    }
}
