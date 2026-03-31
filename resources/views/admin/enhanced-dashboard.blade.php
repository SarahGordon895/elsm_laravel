@extends('layouts.enhanced-app')

@section('title', 'Admin Dashboard - ELMS')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Admin Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">System overview and analytics</p>
        </div>
        <div class="flex space-x-3">
            <button class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transform hover:scale-105 transition-all duration-200">
                <i class="fas fa-download mr-2"></i>
                Export Report
            </button>
            <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transform hover:scale-105 transition-all duration-200">
                <i class="fas fa-cog mr-2"></i>
                Settings
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <div class="bg-white overflow-hidden shadow rounded-lg hover-lift">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Total Employees
                            </dt>
                            <dd class="text-2xl font-bold text-gray-900">
                                {{ $stats['total_employees'] ?? 0 }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg hover-lift">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-building text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Departments
                            </dt>
                            <dd class="text-2xl font-bold text-gray-900">
                                {{ $stats['total_departments'] ?? 0 }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg hover-lift">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Pending Applications
                            </dt>
                            <dd class="text-2xl font-bold text-gray-900">
                                {{ $stats['pending_applications'] ?? 0 }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg hover-lift">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Approved Today
                            </dt>
                            <dd class="text-2xl font-bold text-gray-900">
                                {{ $stats['approved_today'] ?? 0 }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg hover-lift">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar-check text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                On Leave Today
                            </dt>
                            <dd class="text-2xl font-bold text-gray-900">
                                {{ $stats['on_leave_today'] ?? 0 }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Leave Applications -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Leave Applications</h3>
            </div>
            <div class="p-6">
                @if($recentApplications->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentApplications as $application)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                                            <span class="text-primary-600 text-xs font-medium">
                                                {{ strtoupper(substr($application->user->first_name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $application->user->full_name }}</p>
                                            <p class="text-xs text-gray-500">{{ $application->leaveType->name }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $application->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $application->status == 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $application->status == 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $application->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 text-center">
                        <a href="{{ route('leave-applications.index') }}" class="text-sm text-primary-600 hover:text-primary-800 font-medium">
                            View All Applications →
                        </a>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-calendar-times text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">No recent applications</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Department Statistics -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Department Statistics</h3>
            </div>
            <div class="p-6">
                @if($departmentStats->count() > 0)
                    <div class="space-y-4">
                        @foreach($departmentStats as $department)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-building text-gray-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $department->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $department->short_name }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-gray-900">{{ $department->users_count }}</p>
                                    <p class="text-xs text-gray-500">employees</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 text-center">
                        <a href="{{ route('admin.departments') }}" class="text-sm text-primary-600 hover:text-primary-800 font-medium">
                            Manage Departments →
                        </a>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-building text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">No departments found</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Leave Balance Summary and Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Leave Balance Summary -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Leave Balance Summary ({{ date('Y') }})</h3>
            </div>
            <div class="p-6">
                @if($leaveBalanceSummary->count() > 0)
                    <div class="space-y-4">
                        @foreach($leaveBalanceSummary as $balance)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $balance->name }}</p>
                                    <p class="text-xs text-gray-500">Available / Used</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-green-600">{{ $balance->total_available }}</p>
                                    <p class="text-sm text-red-500">{{ $balance->total_used }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-chart-pie text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">No leave balance data available</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Audit Logs -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
            </div>
            <div class="p-6">
                @if($recentAuditLogs->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentAuditLogs as $log)
                            <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                        @switch($log->action)
                                            @case('created')
                                                <i class="fas fa-plus text-green-600 text-xs"></i>
                                            @case('updated')
                                                <i class="fas fa-edit text-blue-600 text-xs"></i>
                                            @case('deleted')
                                                <i class="fas fa-trash text-red-600 text-xs"></i>
                                            @case('approved')
                                                <i class="fas fa-check text-green-600 text-xs"></i>
                                            @case('rejected')
                                                <i class="fas fa-times text-red-600 text-xs"></i>
                                            @default
                                                <i class="fas fa-circle text-gray-600 text-xs"></i>
                                        @endswitch
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-900">
                                        <span class="font-medium">{{ ucfirst($log->action) }}</span>
                                        {{ $log->model_type }} {{ $log->model_id }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $log->user?->full_name ?? 'System' }} • {{ $log->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 text-center">
                        <a href="{{ route('admin.audit-logs') }}" class="text-sm text-primary-600 hover:text-primary-800 font-medium">
                            View All Activity →
                        </a>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-history text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">No recent activity</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
