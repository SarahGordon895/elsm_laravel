@extends('layouts.enhanced-app')

@section('title', 'Dashboard - ELMS')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{ 
    showLeaveBalanceModal: false,
    showNotificationsModal: false,
    leaveBalances: @json($leaveBalances),
    notifications: @json($notifications)
}">
    <!-- Page Header -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">
                    <i class="fas fa-dashboard mr-2"></i>
                    {{ auth()->user()->role === 'head_of_department' ? 'Department Dashboard' : 'Employee Dashboard' }}
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Welcome back, {{ auth()->user()->full_name }}!
                    @if(auth()->user()->role === 'head_of_department')
                        <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                            Head of Department
                        </span>
                    @endif
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('leave-applications.create') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transform hover:scale-105 transition-all duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Apply Leave
                </a>
                <button @click="showNotificationsModal = true" 
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transform hover:scale-105 transition-all duration-200">
                    <i class="fas fa-bell mr-2"></i>
                    Notifications
                    @if($notificationsCount > 0)
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ $notificationsCount }}</span>
                    @endif
                </button>
            </div>
        </div>
    </div>
        
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-calendar text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Total Leaves Applied
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ $stats['total_leaves_applied'] ?? 0 }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-clock text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Pending Leaves
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ $stats['pending_leaves'] ?? 0 }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-check text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Approved Leaves
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ $stats['approved_leaves'] ?? 0 }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-times text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Rejected Leaves
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ $stats['rejected_leaves'] ?? 0 }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Balance Overview -->
    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-medium text-gray-900">
                <i class="fas fa-wallet mr-2"></i>
                Leave Balance Overview
            </h2>
            <button onclick="showLeaveBalance()" 
                    class="text-sm text-primary-600 hover:text-primary-500 font-medium cursor-pointer transition-colors duration-200">
                View Details
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @if($leaveBalances && $leaveBalances->count() > 0)
                @foreach($leaveBalances as $balance)
                    @php
                        $remaining = max(0, ($balance->balance_days + $balance->carry_over_days) - $balance->used_days);
                        $allocation = max(1, $balance->balance_days + $balance->carry_over_days);
                        $percent = min(100, round(($remaining / $allocation) * 100));
                        $badgeClass = $remaining > 5 ? 'bg-green-100 text-green-800' : ($remaining > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                    @endphp
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-medium text-gray-900">
                                {{ $balance->leaveType ? $balance->leaveType->name : 'Leave Type' }}
                            </h4>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $badgeClass }}">
                                {{ $remaining }} days
                            </span>
                        </div>
                        <div class="text-xs text-gray-500 mb-2">
                            Allocation: {{ $allocation }} | Used: {{ $balance->used_days }} | Remaining: {{ $remaining }}
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-primary-600 h-2 rounded-full transition-all duration-300" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-span-full text-center py-8">
                    <i class="fas fa-calendar-times text-gray-400 text-5xl mb-4"></i>
                    <p class="text-gray-500 text-lg font-medium mb-2">No Leave Balance Available</p>
                    <p class="text-gray-400 text-sm">You haven't applied for any leave yet. Your leave balance will appear here once you have leave entitlements.</p>
                    <a href="{{ route('leave-applications.create') }}" 
                       class="mt-4 inline-block bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Apply for Your First Leave
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Department Stats (for Head of Department) -->
    @if(auth()->user()->role === 'head_of_department')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                <h3 class="text-lg font-medium text-purple-900 mb-4">
                    <i class="fas fa-users mr-2"></i>
                    Department Overview
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Employees</span>
                        <span class="text-lg font-semibold text-purple-900">{{ $stats['department_total_employees'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Pending Leaves</span>
                        <span class="text-lg font-semibold text-yellow-600">{{ $stats['department_pending_leaves'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                <h3 class="text-lg font-medium text-purple-900 mb-4">
                    <i class="fas fa-chart-line mr-2"></i>
                    Department Actions
                </h3>
                <div class="space-y-3">
                    <a href="{{ route('pending.applications') }}" 
                       class="block w-full text-center px-4 py-2 border border-purple-300 text-sm font-medium rounded-md text-purple-700 bg-purple-100 hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-200">
                        <i class="fas fa-clock mr-2"></i>
                        Review Pending Applications
                    </a>
                    <a href="{{ route('leave-plans.index') }}" 
                       class="block w-full text-center px-4 py-2 border border-purple-300 text-sm font-medium rounded-md text-purple-700 bg-purple-100 hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-200">
                        <i class="fas fa-clipboard-list mr-2"></i>
                        Leave Plans
                    </a>
                    <a href="{{ route('leave-applications.index') }}" 
                       class="block w-full text-center px-4 py-2 border border-purple-300 text-sm font-medium rounded-md text-purple-700 bg-purple-100 hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-200">
                        <i class="fas fa-tasks mr-2"></i>
                        Team Leave Management
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Recent Leave Applications -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-medium text-gray-900">
                <i class="fas fa-history mr-2"></i>
                Recent Leave Applications
            </h2>
            <a href="{{ route('leave-applications.index') }}" 
               class="text-sm text-primary-600 hover:text-primary-500 font-medium">
                View All
            </a>
        </div>
        
        @if($recentLeaveApplications->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Leave Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Duration
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Applied On
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentLeaveApplications as $application)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $application->leave_type ? $application->leave_type->name : 'Leave Type' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($application->start_date)->format('M d') }} - 
                                        {{ \Carbon\Carbon::parse($application->end_date)->format('M d') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                          @if($application->status == 'approved') bg-green-100 text-green-800
                                          @elseif($application->status == 'rejected') bg-red-100 text-red-800
                                          @elseif($application->status == 'pending') bg-yellow-100 text-yellow-800
                                          @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $application->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-calendar-times text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-500">No leave applications found.</p>
                <a href="{{ route('leave-applications.create') }}" 
                   class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                    <i class="fas fa-plus mr-2"></i>
                    Apply for Leave
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Leave Balance Modal -->
<div x-show="showLeaveBalanceModal" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" @click="showLeaveBalanceModal = false">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            <i class="fas fa-wallet mr-2"></i>
                            Leave Balance Details
                        </h3>
                        <div class="space-y-3">
                            @foreach($leaveBalances as $balance)
                                @php
                                    $remaining = max(0, ($balance->balance_days + $balance->carry_over_days) - $balance->used_days);
                                    $allocation = max(1, $balance->balance_days + $balance->carry_over_days);
                                    $percent = min(100, round(($remaining / $allocation) * 100));
                                @endphp
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <h4 class="text-sm font-medium text-gray-900">
                                            {{ $balance->leaveType ? $balance->leaveType->name : 'Leave Type' }}
                                        </h4>
                                        <span class="text-lg font-semibold text-primary-600">
                                            {{ $remaining }} / {{ $allocation }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500 mb-2">
                                        Used: {{ $balance->used_days }} days | Remaining: {{ $remaining }} days
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-3">
                                        <div class="bg-primary-600 h-3 rounded-full transition-all duration-300" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" 
                        @click="showLeaveBalanceModal = false"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Notifications Modal -->
<div x-show="showNotificationsModal" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" @click="showNotificationsModal = false">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            <i class="fas fa-bell mr-2"></i>
                            Notifications
                        </h3>
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @if($notifications->count() > 0)
                                @foreach($notifications as $notification)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                                        <div class="flex justify-between items-start mb-2">
                                            <h4 class="text-sm font-medium text-gray-900">{{ $notification->title }}</h4>
                                            <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-sm text-gray-600">{{ $notification->message }}</p>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-8">
                                    <i class="fas fa-bell-slash text-gray-400 text-4xl mb-4"></i>
                                    <p class="text-gray-500">No notifications found.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" 
                        @click="showNotificationsModal = false"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
