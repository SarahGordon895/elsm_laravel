@extends('layouts.enhanced-app')

@section('title', 'HR Dashboard - ELMS')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Welcome Header -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">HR Dashboard</h1>
                    <p class="text-purple-100 mt-1">Welcome back, {{ Auth::user()->full_name }}!</p>
                    <p class="text-purple-200 text-sm mt-2">Human Resources Management System</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold">{{ now()->format('M d, Y') }}</div>
                    <div class="text-purple-200 text-sm">{{ now()->format('l') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- HR Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <!-- Total Employees -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Employees</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalEmployees ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

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

        <!-- Approved This Month -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Approved This Month</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $approvedThisMonth ?? 0 }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Leave Balance Alerts -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500 hover:shadow-lg transition-shadow duration-200 cursor-pointer" onclick="window.location.href='{{ route('hr.leave-plans.index') }}'">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Low Balance Alerts</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $lowBalanceCount ?? 0 }}</p>
                </div>
                <div class="bg-orange-100 rounded-full p-3">
                    <i class="fas fa-exclamation-triangle text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- New Applicants -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">New Applicants</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $newApplicantsCount ?? 0 }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-user-plus text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Leave Management Section -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Recent Leave Requests -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                            Recent Leave Requests
                        </h2>
                        <div class="flex space-x-2">
                            <a href="{{ route('hr.leave-applications') }}" 
                               class="text-sm text-yellow-600 hover:text-yellow-500 font-medium">
                                Pending
                            </a>
                            <a href="{{ route('hr.leave-applications') }}" 
                               class="text-sm text-primary-600 hover:text-primary-500 font-medium">
                                All
                            </a>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @if(isset($recentApplications) && $recentApplications->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentApplications as $application)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                                    <div class="flex-1">
                                        <div class="flex items-center">
                                            <img src="{{ $application->user->profile_photo ?? asset('images/default-avatar.png') }}" 
                                                 alt="{{ $application->user->full_name }}" 
                                                 class="w-10 h-10 rounded-full mr-3">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $application->user->full_name }}</p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $application->user->department->name ?? 'No Department' }} • 
                                                    {{ $application->leaveType->name }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-500">{{ $application->start_date->format('M d, Y') }}</p>
                                            <p class="text-sm text-gray-500">{{ $application->duration }} days</p>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ $application->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                   ($application->status == 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                                {{ ucfirst($application->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-3 flex space-x-2">
                                        @if($application->status == 'pending')
                                            <button onclick="approveLeave({{ $application->id }})" 
                                                    class="flex-1 bg-green-600 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-green-700 transition-colors duration-200">
                                                <i class="fas fa-check mr-1"></i> Approve
                                            </button>
                                            <button onclick="rejectLeave({{ $application->id }})" 
                                                    class="flex-1 bg-red-600 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-red-700 transition-colors duration-200">
                                                <i class="fas fa-times mr-1"></i> Reject
                                            </button>
                                        @endif
                                        <a href="{{ route('leave-applications.show', $application->id) }}" 
                                           class="flex-1 bg-gray-100 text-gray-700 px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-200 transition-colors duration-200 text-center">
                                            <i class="fas fa-eye mr-1"></i> View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-calendar-check text-green-500 text-4xl mb-4"></i>
                            <p class="text-gray-500">No recent leave requests</p>
                            <p class="text-sm text-gray-400 mt-2">All leave requests are up to date</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Employee Management -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-users-cog mr-2 text-purple-500"></i>
                            Recent Employees
                        </h2>
                        <a href="{{ route('hr.departments') }}" 
                           class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                            View All Departments
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    @if(isset($recentEmployees) && $recentEmployees->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentEmployees as $employee)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                                    <div class="flex items-center">
                                        <img src="{{ $employee->profile_photo ?? asset('images/default-avatar.png') }}" 
                                             alt="{{ $employee->full_name }}" 
                                             class="w-8 h-8 rounded-full mr-3">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $employee->full_name }}</p>
                                            <p class="text-sm text-gray-500">
                                                {{ $employee->department->name ?? 'No Department' }} • 
                                                {{ $employee->role }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $employee->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($employee->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-users text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-500">No recent employee updates</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- HR Sidebar -->
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
                    <a href="{{ route('hr.leave-applications') }}" 
                       class="block w-full text-center bg-blue-600 text-white px-4 py-3 rounded-md font-medium hover:bg-blue-700 transition-colors duration-200 transform hover:scale-105">
                        <i class="fas fa-calendar-check mr-2"></i>
                        Pending Applications
                    </a>
                    <a href="{{ route('hr.departments') }}" 
                       class="block w-full text-center bg-purple-600 text-white px-4 py-3 rounded-md font-medium hover:bg-purple-700 transition-colors duration-200 transform hover:scale-105">
                        <i class="fas fa-building mr-2"></i>
                        View Departments
                    </a>
                    <a href="{{ route('hr.leave-plans.index') }}" 
                       class="block w-full text-center bg-green-600 text-white px-4 py-3 rounded-md font-medium hover:bg-green-700 transition-colors duration-200 transform hover:scale-105">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Manage Leave Plans
                    </a>
                    <a href="{{ route('analytics.leave') }}" 
                       class="block w-full text-center bg-orange-600 text-white px-4 py-3 rounded-md font-medium hover:bg-orange-700 transition-colors duration-200 transform hover:scale-105">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Leave Analytics
                    </a>
                    <a href="{{ route('hr.notifications') }}" 
                       class="block w-full text-center bg-indigo-600 text-white px-4 py-3 rounded-md font-medium hover:bg-indigo-700 transition-colors duration-200 transform hover:scale-105">
                        <i class="fas fa-bell mr-2"></i>
                        Notifications
                    </a>
                </div>
            </div>

            <!-- Leave Statistics -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-chart-pie mr-2 text-green-500"></i>
                        Leave Statistics
                    </h2>
                </div>
                <div class="p-6">
                    @if(isset($leaveStats))
                        <div class="space-y-4">
                            @foreach($leaveStats as $type => $stats)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">{{ $type }}</span>
                                    <div class="flex items-center">
                                        <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $stats['percentage'] }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-900">{{ $stats['count'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chart-pie text-gray-400 text-3xl mb-2"></i>
                            <p class="text-gray-500">No statistics available</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- HR Notifications -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors duration-200" onclick="window.location.href='{{ route('hr.notifications') }}'">
                    <h2 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-bell mr-2 text-red-500"></i>
                        HR Notifications
                        @if(isset($hrNotifications) && $hrNotifications->count() > 0)
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $hrNotifications->count() }}
                            </span>
                        @endif
                    </h2>
                </div>
                <div class="p-6">
                    @if(isset($hrNotifications) && $hrNotifications->count() > 0)
                        <div class="space-y-3">
                            @foreach($hrNotifications as $notification)
                                @php
                                    $notificationType = is_object($notification) ? $notification->type : ($notification['type'] ?? 'info');
                                    $notificationMessage = is_object($notification) ? $notification->message : ($notification['message'] ?? 'No message');
                                    $notificationCreatedAt = is_object($notification) ? $notification->created_at : ($notification['created_at'] ?? now());
                                @endphp
                                <div class="flex items-start space-x-3 p-3 bg-{{ $notificationType == 'alert' ? 'red' : ($notificationType == 'warning' ? 'yellow' : 'blue') }}-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-{{ $notificationType == 'alert' ? 'exclamation-triangle' : ($notificationType == 'warning' ? 'exclamation' : 'info-circle') }} text-{{ $notificationType == 'alert' ? 'red' : ($notificationType == 'warning' ? 'yellow' : 'blue') }}-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-900">{{ $notificationMessage }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($notificationCreatedAt)->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-bell-slash text-gray-400 text-3xl mb-2"></i>
                            <p class="text-gray-500">No new notifications</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Approve leave function
function approveLeave(id) {
    if (confirm('Are you sure you want to approve this leave request?')) {
        fetch(`/hr/leave-applications/${id}/approve`, {
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
        fetch(`/hr/leave-applications/${id}/reject`, {
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
