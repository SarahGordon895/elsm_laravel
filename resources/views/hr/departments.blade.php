@extends('layouts.enhanced-app')

@section('title', 'Departments - HR Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Departments</h1>
                <p class="text-gray-600 mt-1">View department information and employee counts</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('hr.departments.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                    <i class="fas fa-plus mr-2"></i>
                    Add Department
                </a>
                <a href="{{ route('hr.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Department Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($departments as $department)
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="bg-blue-100 rounded-full p-3 mr-4">
                            <i class="fas fa-building text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ $department->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $department->description ?? 'No description available' }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total Employees</span>
                        <span class="text-sm font-medium text-gray-900">{{ $department->users_count ?? 0 }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Department Head</span>
                        <span class="text-sm font-medium text-gray-900">
                            @if($department->head)
                                {{ $department->head->full_name }}
                            @else
                                Not Assigned
                            @endif
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Status</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $department->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($department->status ?? 'active') }}
                        </span>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="text-sm text-gray-500">
                        Created: {{ $department->created_at->format('M d, Y') }}
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <i class="fas fa-building text-gray-400 text-5xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Departments Found</h3>
                    <p class="text-gray-500">There are no departments to display.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Statistics Summary -->
    <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Department Statistics</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600">{{ $departments->count() }}</div>
                <div class="text-sm text-gray-600">Total Departments</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600">{{ $departments->sum('users_count') }}</div>
                <div class="text-sm text-gray-600">Total Employees</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-purple-600">{{ $departments->where('status', 'active')->count() }}</div>
                <div class="text-sm text-gray-600">Active Departments</div>
            </div>
        </div>
    </div>
</div>
@endsection
