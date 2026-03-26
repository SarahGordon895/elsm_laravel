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
        $this->authorize('manage-leave-plans');
        
        $user = Auth::user();
        
        if ($user->hasRole('hr')) {
            // HR can see all leave plans
            $leavePlans = LeavePlan::with(['user', 'leaveType', 'approver'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } elseif ($user->hasRole('head_of_department')) {
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
     * Show the form for creating a new leave plan.
     */
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
