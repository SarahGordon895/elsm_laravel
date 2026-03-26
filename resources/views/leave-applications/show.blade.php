@extends('layouts.app')

@section('title', 'Leave Application Details - ELMS')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="max-w-4xl mx-auto">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">Leave Application Details</h1>
                    <div>
                        <a href="{{ route('leave-applications.index') }}" 
                           class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 mr-3">
                            Back to List
                        </a>
                        @if(!Auth::user()->isAdmin() && $leaveApplication->status == 'pending')
                            <a href="{{ route('leave-applications.edit', $leaveApplication) }}" 
                               class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700">
                                Edit
                            </a>
                        @endif
                    </div>
                </div>

                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-medium text-gray-900">Application Information</h2>
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                {{ $leaveApplication->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $leaveApplication->status == 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $leaveApplication->status == 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($leaveApplication->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="px-6 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if(Auth::user()->isAdmin())
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Employee</h3>
                                    <p class="mt-1 text-sm text-gray-900">{{ $leaveApplication->user->full_name }}</p>
                                    <p class="text-sm text-gray-500">{{ $leaveApplication->user->email }}</p>
                                </div>
                            @endif

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Leave Type</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ $leaveApplication->leaveType->name }}</p>
                                <p class="text-sm text-gray-500">{{ $leaveApplication->leaveType->description }}</p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Duration</h3>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $leaveApplication->start_date->format('F d, Y') }} - 
                                    {{ $leaveApplication->end_date->format('F d, Y') }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ $leaveApplication->start_date->diffInDays($leaveApplication->end_date) + 1 }} days
                                </p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Application Date</h3>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $leaveApplication->created_at->format('F d, Y g:i A') }}
                                </p>
                            </div>

                            <div class="md:col-span-2">
                                <h3 class="text-sm font-medium text-gray-500">Reason</h3>
                                <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">
                                    {{ $leaveApplication->reason }}
                                </p>
                            </div>

                            @if($leaveApplication->approved_by)
                                <div class="md:col-span-2">
                                    <h3 class="text-sm font-medium text-gray-500">Approval Information</h3>
                                    <p class="mt-1 text-sm text-gray-900">
                                        Approved by: {{ $leaveApplication->approver->full_name }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $leaveApplication->approved_at->format('F d, Y g:i A') }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if(Auth::user()->isAdmin() && $leaveApplication->status == 'pending')
                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                            <h3 class="text-sm font-medium text-gray-900 mb-3">Take Action</h3>
                            <div class="flex space-x-3">
                                <form action="{{ route('leave-applications.approve', $leaveApplication) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                        <i class="fas fa-check mr-2"></i>Approve
                                    </button>
                                </form>
                                <form action="{{ route('leave-applications.reject', $leaveApplication) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                                        <i class="fas fa-times mr-2"></i>Reject
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
