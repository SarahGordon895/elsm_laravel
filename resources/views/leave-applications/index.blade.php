@extends('layouts.enhanced-app')

@section('title', 'Leave Applications - ELMS')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Leave Applications</h1>
            <p class="mt-1 text-sm text-gray-500">Manage and track employee leave applications</p>
        </div>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
            <!-- Create Application Button -->
            <a href="{{ route('leave-applications.create') }}" 
               class="w-full sm:w-auto bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                <i class="fas fa-plus mr-2"></i>
                New Application
            </a>
        </div>
    </div>

    <!-- Applications List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        @if($leaveApplications->count() > 0)
            <div class="px-4 py-5 sm:px-6">
                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                @if(Auth::user()->isAdmin())
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Employee
                                    </th>
                                @endif
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Leave Type
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Duration
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($leaveApplications as $application)
                                <tr>
                                    @if(Auth::user()->isAdmin())
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $application->user->full_name }}
                                        </td>
                                    @endif
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $application->leave_type ? $application->leave_type->name : 'Leave Type' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $application->start_date->format('M d, Y') }} - 
                                        {{ $application->end_date->format('M d, Y') }}
                                        <br>
                                        <span class="text-xs text-gray-400">
                                            {{ $application->start_date->diffInDays($application->end_date) + 1 }} days
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $application->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $application->status == 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $application->status == 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst($application->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('leave-applications.show', $application) }}" 
                                           class="text-blue-600 hover:text-blue-900">View</a>
                                        
                                        @if(!Auth::user()->isAdmin() && $application->status == 'pending')
                                            <a href="{{ route('leave-applications.edit', $application) }}" 
                                               class="ml-3 text-yellow-600 hover:text-yellow-900">Edit</a>
                                            
                                            <form action="{{ route('leave-applications.destroy', $application) }}" 
                                                  method="POST" class="inline ml-3">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900"
                                                        onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        @endif

                                        @if(Auth::user()->isAdmin() && $application->status == 'pending')
                                            <form action="{{ route('leave-applications.approve', $application) }}" 
                                                  method="POST" class="inline ml-3">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900">Approve</button>
                                            </form>

                                            <form action="{{ route('leave-applications.reject', $application) }}" 
                                                  method="POST" class="inline ml-3">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:text-red-900">Reject</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-calendar-times text-gray-400 text-5xl mb-4"></i>
                <p class="text-gray-500">No leave applications found.</p>
                @if(!Auth::user()->isAdmin())
                    <a href="{{ route('leave-applications.create') }}" 
                       class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Create Your First Application
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
