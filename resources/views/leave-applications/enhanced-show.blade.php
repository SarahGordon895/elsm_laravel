@extends('layouts.enhanced-app')

@section('title', 'Leave Application Details - ELMS')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <!-- Application Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <h1 class="text-xl font-semibold text-gray-900">Leave Application Details</h1>
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                        {{ $leaveApplication->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $leaveApplication->status == 'approved' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $leaveApplication->status == 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ ucfirst($leaveApplication->status) }}
                    </span>
                </div>
                <div class="flex space-x-2">
                    @if(!auth()->user()->isAdmin() && !auth()->user()->isHR() && $leaveApplication->status == 'pending')
                        <a href="{{ route('leave-applications.edit', $leaveApplication) }}" 
                           class="text-yellow-600 hover:text-yellow-900 px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-edit mr-1"></i>
                            Edit
                        </a>
                    @endif
                    <a href="{{ route('leave-applications.index') }}" 
                       class="bg-gray-300 text-gray-700 px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </a>
                </div>
            </div>
        </div>

        <!-- Application Content -->
        <div class="p-6 space-y-6">
            <!-- Employee Information -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Employee Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Name</p>
                            <p class="text-lg font-medium text-gray-900">{{ $leaveApplication->user->full_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Employee ID</p>
                            <p class="text-lg font-medium text-gray-900">{{ $leaveApplication->user->employee_id }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Email</p>
                            <p class="text-lg font-medium text-gray-900">{{ $leaveApplication->user->email }}</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Department</p>
                            <p class="text-lg font-medium text-gray-900">{{ $leaveApplication->user->department?->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Employment Type</p>
                        <p class="text-lg font-medium text-gray-900">{{ ucfirst($leaveApplication->user->employment_type) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Manager</p>
                            <p class="text-lg font-medium text-gray-900">
                                {{ $leaveApplication->user->manager?->full_name ?? 'Not assigned' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leave Details -->
            <div class="bg-blue-50 rounded-lg p-6">
                <h3 class="text-lg font-medium text-blue-900 mb-4">Leave Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Leave Type</p>
                            <p class="text-lg font-medium text-gray-900">{{ $leaveApplication->leaveType->name }}</p>
                            <p class="text-sm text-gray-600">{{ $leaveApplication->leaveType->description }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Duration</p>
                            <p class="text-lg font-medium text-gray-900">
                                {{ $leaveApplication->start_date->format('F d, Y') }} - 
                                {{ $leaveApplication->end_date->format('F d, Y') }}
                            </p>
                            <p class="text-sm text-gray-600">
                                {{ $leaveApplication->start_date->diffInDays($leaveApplication->end_date) + 1 }} days
                            </p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Application Date</p>
                            <p class="text-lg font-medium text-gray-900">
                                {{ $leaveApplication->created_at->format('F d, Y g:i A') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Last Updated</p>
                            <p class="text-lg font-medium text-gray-900">
                                {{ $leaveApplication->updated_at->format('F d, Y g:i A') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reason -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Reason for Leave</h3>
                <p class="text-gray-700 whitespace-pre-wrap">{{ $leaveApplication->reason }}</p>
            </div>

            <!-- Supporting Documents -->
            @if($leaveApplication->documents->count() > 0)
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Supporting Documents</h3>
                    <div class="space-y-3">
                        @foreach($leaveApplication->documents as $document)
                            <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-file text-gray-400"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $document->original_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $document->formattedSize }}</p>
                                    </div>
                                </div>
                                <a href="{{ $document->url }}" target="_blank" 
                                   class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                                    <i class="fas fa-download mr-1"></i>
                                    Download
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Approval Information -->
            @if($leaveApplication->approved_by)
                <div class="bg-green-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-green-900 mb-4">Approval Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Approved By</p>
                            <p class="text-lg font-medium text-gray-900">
                                {{ $leaveApplication->approver->full_name }}
                            </p>
                            <p class="text-sm text-gray-600">{{ $leaveApplication->approver->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Approved At</p>
                            <p class="text-lg font-medium text-gray-900">
                                {{ $leaveApplication->approved_at->format('F d, Y g:i A') }}
                            </p>
                        </div>
                    </div>
                    @if($leaveApplication->admin_remarks)
                        <div class="mt-4">
                            <p class="text-sm font-medium text-gray-500">Remarks</p>
                            <p class="text-gray-700">{{ $leaveApplication->admin_remarks }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Rejection Information -->
            @if($leaveApplication->status == 'rejected' && $leaveApplication->admin_remarks)
                <div class="bg-red-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-red-900 mb-4">Rejection Information</h3>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Reason</p>
                        <p class="text-gray-700">{{ $leaveApplication->admin_remarks }}</p>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                @can('approve-leave')
                @if($leaveApplication->status == 'pending')
                    <form action="{{ route('leave-applications.approve', $leaveApplication) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <i class="fas fa-check mr-2"></i>
                            Approve
                        </button>
                    </form>
                    <form action="{{ route('leave-applications.reject', $leaveApplication) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            <i class="fas fa-times mr-2"></i>
                            Reject
                        </button>
                    </form>
                @endif
                @endcan
                
                @if(!auth()->user()->isAdmin() && !auth()->user()->isHR() && $leaveApplication->status == 'pending')
                    <a href="{{ route('leave-applications.edit', $leaveApplication) }}" 
                       class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <i class="fas fa-edit mr-2"></i>
                        Edit
                    </a>
                @endif
                
                @if(!auth()->user()->isAdmin() && !auth()->user()->isHR() && $leaveApplication->status == 'pending')
                    <form action="{{ route('leave-applications.destroy', $leaveApplication) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                                onclick="return confirm('Are you sure you want to delete this leave application?')">
                            <i class="fas fa-trash mr-2"></i>
                            Delete
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
