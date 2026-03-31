<?php

namespace App\Http\Controllers;

use App\Events\LeaveApplicationStatusChanged;
use App\Events\LeaveApplicationSubmitted;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EnhancedLeaveApplicationController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view-leave-applications');
        
        $user = Auth::user();
        $query = LeaveApplication::with(['user', 'leaveType', 'approver']);
        
        // Filter based on user role
        if ($user->isAdmin() || $user->isHR()) {
            // Can see all applications
            if ($request->status) {
                $query->where('status', $request->status);
            }
            if ($request->department_id) {
                $query->whereHas('user.department', function ($q) use ($request) {
                    $q->where('id', $request->department_id);
                });
            }
            if ($request->date_from && $request->date_to) {
                $query->whereBetween('start_date', [$request->date_from, $request->date_to]);
            }
        } elseif ($user->isManager()) {
            // Can see subordinate applications
            $subordinateIds = $user->subordinates()->pluck('id');
            $query->whereIn('user_id', $subordinateIds);
            
            if ($request->status) {
                $query->where('status', $request->status);
            }
        } else {
            // Can only see own applications
            $query->where('user_id', $user->id);
        }
        
        $leaveApplications = $query->latest()->get();
        
        // Get departments for filter
        $departments = \App\Models\Department::all();
        
        return view('leave-applications.index', compact('leaveApplications', 'departments'));
    }

    public function pendingApplications(Request $request)
    {
        // Keep this role-based to avoid false 403 when pivot permission data is incomplete.
        $this->authorize('approve-leave');
        
        $user = Auth::user();
        $query = LeaveApplication::with(['user', 'leaveType'])
            ->where('status', 'pending');
        
        // Filter based on user role
        if ($user->isManager()) {
            $subordinateIds = $user->subordinates()->pluck('id');
            $query->whereIn('user_id', $subordinateIds);
        }
        
        // Additional filters
        if ($request->department_id) {
            $query->whereHas('user.department', function ($q) use ($request) {
                $q->where('id', $request->department_id);
            });
        }
        
        if ($request->leave_type_id) {
            $query->where('leave_type_id', $request->leave_type_id);
        }
        
        if ($request->date_from && $request->date_to) {
            $query->whereBetween('start_date', [$request->date_from, $request->date_to]);
        }
        
        $pendingApplications = $query->orderBy('created_at', 'desc')->paginate(15);
        $departments = \App\Models\Department::all();
        $leaveTypes = \App\Models\LeaveType::all();
        
        return view('leave-applications.pending', compact('pendingApplications', 'departments', 'leaveTypes'));
    }

    public function create()
    {
        $this->authorize('create-leave-applications');
        
        $user = Auth::user();
        $leaveTypes = LeaveType::where('is_active', true)
            ->whereIn('name', ['Annual Leave', 'Sick Leave', 'Maternity Leave', 'Paternity Leave'])
            ->get();
        
        // Get user's leave balances
        $leaveBalances = LeaveBalance::where('user_id', $user->id)
            ->whereHas('leaveType', function ($query) {
                $query->where('is_active', true)
                    ->whereIn('name', ['Annual Leave', 'Sick Leave', 'Maternity Leave', 'Paternity Leave']);
            })
            ->with('leaveType')
            ->get();
        
        return view('leave-applications.create', compact('leaveTypes', 'leaveBalances'));
    }

    public function store(Request $request)
    {
        $this->authorize('create-leave-applications');
        
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'documents' => 'nullable|array|max:5',
            'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::user();
        $leaveType = LeaveType::findOrFail($request->leave_type_id);
        $allowedLeaveTypes = ['Annual Leave', 'Sick Leave', 'Maternity Leave', 'Paternity Leave'];
        if (!in_array($leaveType->name, $allowedLeaveTypes, true)) {
            return back()->withInput()->with('error', 'This leave type is not available in current policy.');
        }
        
        // Check leave balance
        $currentYear = date('Y');
        $leaveBalance = LeaveBalance::getBalance($user->id, $leaveType->id, $currentYear);
        
        if (!$leaveBalance) {
            return back()->withInput()->with('error', 'No leave balance found for this leave type.');
        }
        
        $requestedDays = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)) + 1;
        $availableDays = $leaveBalance->available_days;
        
        if ($requestedDays > $availableDays) {
            return back()->withInput()->with('error', "Insufficient leave balance. Available: {$availableDays} days, Requested: {$requestedDays} days.");
        }

        // Annual leave gate: no annual leave means no annual application.
        if ($leaveType->name === 'Annual Leave' && $leaveBalance->available_days <= 0) {
            return back()->withInput()->with('error', 'Your annual leave balance is exhausted. You cannot apply annual leave.');
        }
        
        // Check for overlapping leave applications
        $overlapping = LeaveApplication::where('user_id', $user->id)
            ->where('status', '!=', 'rejected')
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                      });
            })->exists();
            
        if ($overlapping) {
            return back()->withInput()->with('error', 'You already have a leave application for this period.');
        }
        
        DB::beginTransaction();
        try {
            // Create leave application
            $leaveApplication = LeaveApplication::create([
                'user_id' => $user->id,
                'leave_type_id' => $request->leave_type_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'reason' => $request->reason,
                'status' => 'pending',
            ]);
            
            // Handle document uploads
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    $path = $document->store('leave-documents', 'public');
                    $leaveApplication->documents()->create([
                        'file_path' => $path,
                        'original_name' => $document->getClientOriginalName(),
                        'file_size' => $document->getSize(),
                    ]);
                }
            }
            
            // Update leave balance
            $leaveBalance->useDays($requestedDays);

            $sickExtraAnnualDeduction = 0;

            // Sick leave policy:
            // If sick leave is more than one day and no supporting document is uploaded,
            // deduct extra days (after day 1) from annual leave balance.
            if ($leaveType->name === 'Sick Leave' && $requestedDays > 1 && !$request->hasFile('documents')) {
                $extraDaysWithoutProof = $requestedDays - 1;
                $annualLeaveType = LeaveType::where('name', 'Annual Leave')->first();
                if ($annualLeaveType) {
                    $annualBalance = LeaveBalance::getBalance($user->id, $annualLeaveType->id, $currentYear);
                    if (!$annualBalance || $annualBalance->available_days < $extraDaysWithoutProof) {
                        throw new \RuntimeException('Insufficient annual leave balance for sick leave proof policy.');
                    }

                    $annualBalance->useDays($extraDaysWithoutProof);
                    $sickExtraAnnualDeduction = $extraDaysWithoutProof;
                }
            }
            
            // Log audit trail
            AuditLog::log('created', $leaveApplication, $user->id);

            event(new LeaveApplicationSubmitted(
                $leaveApplication,
                $user,
                $leaveType,
                $requestedDays,
                $sickExtraAnnualDeduction
            ));
            
            DB::commit();
            
            return redirect()->route('leave-applications.index')
                ->with('success', 'Leave application submitted successfully.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', $e->getMessage() ?: 'Failed to submit leave application. Please try again.');
        }
    }

    public function show(LeaveApplication $leaveApplication)
    {
        $this->authorize('view-leave-applications');
        
        // Check if user can view this application
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isHR() && 
            !$user->canApproveLeave($leaveApplication) && 
            $leaveApplication->user_id !== $user->id) {
            abort(403);
        }
        
        $leaveApplication->load(['user', 'leaveType', 'approver', 'documents']);
        
        return view('leave-applications.enhanced-show', compact('leaveApplication'));
    }

    public function edit(LeaveApplication $leaveApplication)
    {
        $this->authorize('edit-leave-applications');
        
        // Only allow editing pending applications owned by the user
        if ($leaveApplication->user_id !== Auth::id() || $leaveApplication->status !== 'pending') {
            abort(403);
        }
        
        $leaveTypes = LeaveType::where('is_active', true)->get();
        
        return view('leave-applications.enhanced-edit', compact('leaveApplication', 'leaveTypes'));
    }

    public function update(Request $request, LeaveApplication $leaveApplication)
    {
        $this->authorize('edit-leave-applications');
        
        if ($leaveApplication->user_id !== Auth::id() || $leaveApplication->status !== 'pending') {
            abort(403);
        }
        
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        $oldValues = $leaveApplication->getAttributes();
        
        $leaveApplication->update($request->all());
        
        // Log audit trail
        AuditLog::log('updated', $leaveApplication, Auth::id(), $oldValues);
        
        return redirect()->route('leave-applications.index')
            ->with('success', 'Leave application updated successfully.');
    }

    public function destroy(LeaveApplication $leaveApplication)
    {
        $this->authorize('delete-leave-applications');
        
        if ($leaveApplication->user_id !== Auth::id() || $leaveApplication->status !== 'pending') {
            abort(403);
        }

        DB::beginTransaction();
        try {
            // Return days to balance
            $requestedDays = Carbon::parse($leaveApplication->start_date)
                ->diffInDays(Carbon::parse($leaveApplication->end_date)) + 1;
            $currentYear = date('Y');
            $leaveBalance = LeaveBalance::getBalance($leaveApplication->user_id, $leaveApplication->leave_type_id, $currentYear);
            
            if ($leaveBalance) {
                $leaveBalance->returnDays($requestedDays);
            }
            
            // Log audit trail
            AuditLog::log('deleted', $leaveApplication, Auth::id());
            
            $leaveApplication->delete();
            
            DB::commit();
            
            return redirect()->route('leave-applications.index')
                ->with('success', 'Leave application deleted successfully.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to delete leave application. Please try again.');
        }
    }

    public function approve(LeaveApplication $leaveApplication, Request $request)
    {
        $this->authorize('approve-leave');
        
        $user = Auth::user();
        if (!$user->canApproveLeave($leaveApplication)) {
            abort(403);
        }
        
        $request->validate([
            'remarks' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $leaveApplication->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
                'admin_remarks' => $request->remarks,
            ]);
            
            event(new LeaveApplicationStatusChanged(
                $leaveApplication,
                $user,
                'approved',
                $request->remarks
            ));
            
            // Log audit trail
            AuditLog::log('approved', $leaveApplication, $user->id);
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Leave application approved successfully.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to approve leave application. Please try again.');
        }
    }

    public function reject(LeaveApplication $leaveApplication, Request $request)
    {
        $this->authorize('reject-leave');
        
        $user = Auth::user();
        if (!$user->canApproveLeave($leaveApplication)) {
            abort(403);
        }
        
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Return days to balance
            $requestedDays = Carbon::parse($leaveApplication->start_date)
                ->diffInDays(Carbon::parse($leaveApplication->end_date)) + 1;
            $currentYear = date('Y');
            $leaveBalance = LeaveBalance::getBalance($leaveApplication->user_id, $leaveApplication->leave_type_id, $currentYear);
            
            if ($leaveBalance) {
                $leaveBalance->returnDays($requestedDays);
            }
            
            $leaveApplication->update([
                'status' => 'rejected',
                'approved_by' => $user->id,
                'approved_at' => now(),
                'admin_remarks' => $request->rejection_reason,
            ]);
            
            event(new LeaveApplicationStatusChanged(
                $leaveApplication,
                $user,
                'rejected',
                $request->rejection_reason
            ));
            
            // Log audit trail
            AuditLog::log('rejected', $leaveApplication, $user->id);
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Leave application rejected successfully.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to reject leave application. Please try again.');
        }
    }

}
