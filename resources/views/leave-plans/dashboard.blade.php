@extends('layouts.enhanced-app')

@section('title', 'Leave Plans Dashboard - ELMS')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Leave Plans Dashboard</h1>
                <p class="text-gray-600 mt-1">Official leave planning and management system</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('leave-plans.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-list mr-2"></i>
                    All Plans
                </a>
                <a href="{{ route('leave-plans.report') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Reports
                </a>
                @if(Auth::user()->role === 'hr')
                    <a href="{{ route('leave-plans.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        <i class="fas fa-plus mr-2"></i>
                        New Plan
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Plans</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_plans'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Plans</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_plans'] }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Approved Plans</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['approved_plans'] }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">This Month</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['this_month_plans'] }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-calendar text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Leave Plans -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-history mr-2 text-blue-500"></i>
                    Recent Leave Plans
                </h2>
            </div>
            <div class="p-6">
                @forelse($recentPlans as $plan)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg mb-3">
                        <div class="flex-1">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $plan->user->full_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $plan->leaveType->name }}</p>
                                </div>
                            </div>
                            <div class="mt-2">
                                <p class="text-xs text-gray-600">
                                    {{ $plan->start_date->format('M d, Y') }} - {{ $plan->end_date->format('M d, Y') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $plan->status == 'approved' ? 'bg-green-100 text-green-800' : 
                                   ($plan->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($plan->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-calendar-alt text-gray-300 text-4xl mb-3"></i>
                        <p>No recent leave plans found.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Leave Type Distribution -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-chart-pie mr-2 text-green-500"></i>
                    Leave Type Distribution
                </h2>
            </div>
            <div class="p-6">
                @forelse($leaveTypeDistribution as $type)
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-blue-500 mr-3"></div>
                            <span class="text-sm font-medium text-gray-900">{{ $type->name }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-sm text-gray-600 mr-2">{{ $type->count }} plans</span>
                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($type->count / $stats['total_plans']) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-chart-pie text-gray-300 text-4xl mb-3"></i>
                        <p>No leave plan data available.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">
            <i class="fas fa-bolt mr-2 text-yellow-500"></i>
            Quick Actions
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('leave-plans.create') }}" class="block text-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50">
                <i class="fas fa-plus-circle text-green-600 text-2xl mb-2"></i>
                <p class="text-sm font-medium text-gray-900">Create New Plan</p>
                <p class="text-xs text-gray-500">Start a new leave plan</p>
            </a>
            <a href="{{ route('leave-plans.index') }}" class="block text-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50">
                <i class="fas fa-list text-blue-600 text-2xl mb-2"></i>
                <p class="text-sm font-medium text-gray-900">View All Plans</p>
                <p class="text-xs text-gray-500">Browse all leave plans</p>
            </a>
            <a href="{{ route('leave-plans.report') }}" class="block text-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50">
                <i class="fas fa-chart-bar text-purple-600 text-2xl mb-2"></i>
                <p class="text-sm font-medium text-gray-900">Generate Reports</p>
                <p class="text-xs text-gray-500">View detailed analytics</p>
            </a>
        </div>
    </div>
</div>
@endsection
