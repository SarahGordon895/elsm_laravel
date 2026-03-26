@extends('layouts.enhanced-app')

@section('title', 'HOD Dashboard - ELMS')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Welcome Header -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Head of Department Dashboard</h1>
                    <p class="text-blue-100 mt-1">Welcome back, {{ Auth::user()->full_name }}!</p>
                    <p class="text-blue-200 text-sm mt-2">{{ Auth::user()->department->name ?? 'Your Department' }}</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold">{{ now()->format('M d, Y') }}</div>
                    <div class="text-blue-200 text-sm">{{ now()->format('l') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Pending Leave Requests -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Requests</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $pendingCount ?? 0 }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Approved Leave Requests -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Approved Today</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $approvedTodayCount ?? 0 }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Team Members on Leave -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Team on Leave</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $onLeaveCount ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Department Size -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Team Size</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $teamSize ?? 0 }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-users-cog text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Leave Requests Section -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Pending Leave Requests -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-clock mr-2 text-yellow-500"></i>
                            Pending Leave Requests
                        </h2>
                        <a href="{{ route('leave-applications.index') }}" 
                           class="text-sm text-primary-600 hover:text-primary-500 font-medium">
                            View All
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    @if(isset($pendingApplications) && $pendingApplications->count() > 0)
                        <div class="space-y-4">
                            @foreach($pendingApplications as $application)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                                    <div class="flex-1">
                                        <div class="flex items-center">
                                            <img src="{{ $application->user->profile_photo ?? asset('images/default-avatar.png') }}" 
                                                 alt="{{ $application->user->full_name }}" 
                                                 class="w-10 h-10 rounded-full mr-3">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $application->user->full_name }}</p>
                                                <p class="text-sm text-gray-500">{{ $application->leaveType->name }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-500">{{ $application->start_date->format('M d, Y') }}</p>
                                            <p class="text-sm text-gray-500">to {{ $application->end_date->format('M d, Y') }}</p>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Pending
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-3 flex space-x-2">
                                        <button onclick="approveLeave({{ $application->id }})" 
                                                class="flex-1 bg-green-600 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-green-700 transition-colors duration-200">
                                            <i class="fas fa-check mr-1"></i> Approve
                                        </button>
                                        <button onclick="rejectLeave({{ $application->id }})" 
                                                class="flex-1 bg-red-600 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-red-700 transition-colors duration-200">
                                            <i class="fas fa-times mr-1"></i> Reject
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-check-circle text-green-500 text-4xl mb-4"></i>
                            <p class="text-gray-500">No pending leave requests</p>
                            <p class="text-sm text-gray-400 mt-2">All team member requests have been processed</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-history mr-2 text-blue-500"></i>
                        Recent Activities
                    </h2>
                </div>
                <div class="p-6">
                    @if(isset($recentActivities) && $recentActivities->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentActivities as $activity)
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        @if($activity->type == 'approved')
                                            <div class="bg-green-100 rounded-full p-2">
                                                <i class="fas fa-check text-green-600 text-sm"></i>
                                            </div>
                                        @elseif($activity->type == 'rejected')
                                            <div class="bg-red-100 rounded-full p-2">
                                                <i class="fas fa-times text-red-600 text-sm"></i>
                                            </div>
                                        @else
                                            <div class="bg-blue-100 rounded-full p-2">
                                                <i class="fas fa-clock text-blue-600 text-sm"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-900">
                                            {{ $activity->description }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-history text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-500">No recent activities</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-bolt mr-2 text-yellow-500"></i>
                        Quick Actions
                    </h2>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('leave-applications.create') }}" 
                       class="block w-full text-center bg-primary-600 text-white px-4 py-3 rounded-md font-medium hover:bg-primary-700 transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Apply for Leave
                    </a>
                    <a href="{{ route('leave-applications.index') }}" 
                       class="block w-full text-center bg-gray-100 text-gray-700 px-4 py-3 rounded-md font-medium hover:bg-gray-200 transition-colors duration-200">
                        <i class="fas fa-list mr-2"></i>
                        View All Requests
                    </a>
                    <a href="{{ route('reports.team') }}" 
                       class="block w-full text-center bg-blue-600 text-white px-4 py-3 rounded-md font-medium hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Team Reports
                    </a>
                </div>
            </div>

            <!-- Team Leave Calendar -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-calendar mr-2 text-purple-500"></i>
                        Team Calendar
                    </h2>
                </div>
                <div class="p-6">
                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-4">{{ now()->format('F Y') }}</p>
                        <div class="grid grid-cols-7 gap-1 text-xs">
                            @for($day = 1; $day <= now()->daysInMonth; $day++)
                                <div class="p-2 text-center {{ now()->day == $day ? 'bg-primary-600 text-white rounded' : 'text-gray-700' }}">
                                    {{ $day }}
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            <!-- Department Info -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-building mr-2 text-indigo-500"></i>
                        Department Info
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Department</p>
                            <p class="text-gray-900">{{ Auth::user()->department->name ?? 'Not Assigned' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Manager</p>
                            <p class="text-gray-900">{{ Auth::user()->full_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active Members</p>
                            <p class="text-gray-900">{{ $teamSize ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Approve leave function
function approveLeave(id) {
    if (confirm('Are you sure you want to approve this leave request?')) {
        fetch(`/leave-applications/${id}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to approve leave request.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while approving leave request.');
        });
    }
}

// Reject leave function
function rejectLeave(id) {
    const reason = prompt('Please provide a reason for rejection:');
    if (reason) {
        fetch(`/leave-applications/${id}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to reject leave request.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while rejecting leave request.');
        });
    }
}
</script>
@endsection
