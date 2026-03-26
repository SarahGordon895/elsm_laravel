<?php

namespace App\Http\Controllers;

use App\Models\LeaveApplication;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveApplicationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $leaveApplications = LeaveApplication::with(['user', 'leaveType', 'approver'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $leaveApplications = LeaveApplication::with(['leaveType', 'approver'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('leave-applications.index', compact('leaveApplications'));
    }

    public function create()
    {
        $leaveTypes = LeaveType::all();
        return view('leave-applications.create', compact('leaveTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
        ]);

        $leaveApplication = LeaveApplication::create([
            'user_id' => Auth::id(),
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('leave-applications.index')
            ->with('success', 'Leave application submitted successfully.');
    }

    public function show(LeaveApplication $leaveApplication)
    {
        $this->authorize('view', $leaveApplication);
        
        return view('leave-applications.show', compact('leaveApplication'));
    }

    public function edit(LeaveApplication $leaveApplication)
    {
        $this->authorize('update', $leaveApplication);
        
        $leaveTypes = LeaveType::all();
        return view('leave-applications.edit', compact('leaveApplication', 'leaveTypes'));
    }

    public function update(Request $request, LeaveApplication $leaveApplication)
    {
        $this->authorize('update', $leaveApplication);
        
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
        ]);

        $leaveApplication->update($request->all());

        return redirect()->route('leave-applications.index')
            ->with('success', 'Leave application updated successfully.');
    }

    public function destroy(LeaveApplication $leaveApplication)
    {
        $this->authorize('delete', $leaveApplication);
        
        $leaveApplication->delete();

        return redirect()->route('leave-applications.index')
            ->with('success', 'Leave application deleted successfully.');
    }

    public function approve(LeaveApplication $leaveApplication)
    {
        $this->authorize('approve', $leaveApplication);
        
        $leaveApplication->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Leave application approved successfully.');
    }

    public function reject(LeaveApplication $leaveApplication)
    {
        $this->authorize('approve', $leaveApplication);
        
        $leaveApplication->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Leave application rejected successfully.');
    }

    public function markAsRead(LeaveApplication $leaveApplication)
    {
        $this->authorize('view', $leaveApplication);
        
        if ($leaveApplication->user_id !== Auth::id() && $leaveApplication->status !== 'pending') {
            $leaveApplication->update(['is_read' => true]);
        }

        return redirect()->back();
    }
}
