@extends('layouts.enhanced-app')

@section('title', 'Leave Plans Report - ELMS')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Leave Plans Report</h1>
                <p class="text-gray-600 mt-1">Comprehensive leave planning analysis and reporting</p>
            </div>
            <div class="flex space-x-3">
                <button onclick="exportReport('excel')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                    <i class="fas fa-file-excel mr-2"></i>
                    Export Excel
                </button>
                <button onclick="exportReport('pdf')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Export PDF
                </button>
                <a href="{{ route('leave-plans.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Report Summary -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Report Summary</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600">{{ $leavePlans->count() }}</div>
                <div class="text-sm text-gray-600">Total Plans</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600">{{ $byStatus->get('approved', collect())->count() }}</div>
                <div class="text-sm text-gray-600">Approved</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-yellow-600">{{ $byStatus->get('pending', collect())->count() }}</div>
                <div class="text-sm text-gray-600">Pending</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-red-600">{{ $byStatus->get('rejected', collect())->count() }}</div>
                <div class="text-sm text-gray-600">Rejected</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Status Distribution -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-chart-pie mr-2 text-blue-500"></i>
                    Status Distribution
                </h2>
            </div>
            <div class="p-6">
                @foreach($byStatus as $status => $plans)
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full 
                                {{ $status == 'approved' ? 'bg-green-500' : 
                                   ($status == 'pending' ? 'bg-yellow-500' : 'bg-red-500') }} mr-3"></div>
                            <span class="text-sm font-medium text-gray-900">{{ ucfirst($status) }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-sm text-gray-600 mr-2">{{ $plans->count() }} plans</span>
                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                <div class=" 
                                    {{ $status == 'approved' ? 'bg-green-500' : 
                                       ($status == 'pending' ? 'bg-yellow-500' : 'bg-red-500') }} h-2 rounded-full" 
                                     style="width: {{ ($plans->count() / $leavePlans->count()) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Department Distribution -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-building mr-2 text-green-500"></i>
                    Department Distribution
                </h2>
            </div>
            <div class="p-6">
                @foreach($byDepartment as $department => $plans)
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-purple-500 mr-3"></div>
                            <span class="text-sm font-medium text-gray-900">{{ $department }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-sm text-gray-600 mr-2">{{ $plans->count() }} plans</span>
                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                <div class="bg-purple-500 h-2 rounded-full" style="width: {{ ($plans->count() / $leavePlans->count()) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Leave Type Distribution -->
    <div class="bg-white rounded-lg shadow-md mt-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">
                <i class="fas fa-calendar-alt mr-2 text-purple-500"></i>
                Leave Type Distribution
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($byLeaveType as $leaveType => $plans)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-900 mb-2">{{ $leaveType }}</h3>
                        <div class="text-2xl font-bold text-blue-600 mb-2">{{ $plans->count() }}</div>
                        <div class="text-xs text-gray-500">
                            {{ round(($plans->count() / $leavePlans->count()) * 100, 1) }}% of total plans
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Detailed Leave Plans Table -->
    <div class="bg-white rounded-lg shadow-md mt-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">
                <i class="fas fa-table mr-2 text-gray-500"></i>
                Detailed Leave Plans
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($leavePlans as $plan)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img src="{{ $plan->user->profile_photo ?? asset('images/default-avatar.png') }}" 
                                         alt="{{ $plan->user->full_name }}" 
                                         class="w-8 h-8 rounded-full mr-3">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $plan->user->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $plan->user->department->name ?? 'No Department' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $plan->leaveType->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $plan->start_date->format('M d, Y') }} - {{ $plan->end_date->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $plan->total_days ?? 'N/A' }} days</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $plan->status == 'approved' ? 'bg-green-100 text-green-800' : 
                                       ($plan->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($plan->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $plan->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('leave-plans.show', $plan) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(Auth::user()->role === 'hr' && $plan->status === 'pending')
                                        <a href="{{ route('leave-plans.edit', $plan) }}" class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No leave plans found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function exportReport(format) {
    fetch(`/leave-plans/export?format=${format}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Report exported successfully!');
        } else {
            alert('Export failed. Please try again.');
        }
    });
}
</script>
@endsection
