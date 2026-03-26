@extends('layouts.enhanced-app')

@section('title', 'Reports & Analytics - ELMS')

@section('content')
<div class="space-y-6" x-data="{ 
    reportPeriod: 'current',
    viewMode: 'overview', // overview, departments, types, trends
    loading: false,
    exportData: null
}">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Reports & Analytics</h1>
            <p class="mt-1 text-sm text-gray-500">Comprehensive leave management reports and insights</p>
        </div>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
            <!-- Period Selector -->
            <select x-model="reportPeriod" @change="updateReports()" 
                    class="block w-full sm:w-auto pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                <option value="current">Current Year</option>
                <option value="last">Last Year</option>
                <option value="all">All Time</option>
            </select>
            
            <!-- Export Button -->
            <button @click="exportReports()" 
                    :disabled="loading"
                    class="w-full sm:w-auto bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-download mr-2"></i>
                <span x-show="!loading">Export</span>
                <span x-show="loading">Exporting...</span>
            </button>
        </div>
    </div>

    <!-- View Mode Tabs -->
    <div class="bg-white shadow rounded-lg">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button @click="viewMode = 'overview'" 
                        :class="viewMode === 'overview' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-6 border-b-2 font-medium text-sm focus:outline-none transition-colors duration-200">
                    <i class="fas fa-chart-line mr-2"></i>
                    Overview
                </button>
                <button @click="viewMode = 'departments'" 
                        :class="viewMode === 'departments' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-6 border-b-2 font-medium text-sm focus:outline-none transition-colors duration-200">
                    <i class="fas fa-building mr-2"></i>
                    Departments
                </button>
                <button @click="viewMode = 'types'" 
                        :class="viewMode === 'types' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-6 border-b-2 font-medium text-sm focus:outline-none transition-colors duration-200">
                    <i class="fas fa-list mr-2"></i>
                    Leave Types
                </button>
                <button @click="viewMode = 'trends'" 
                        :class="viewMode === 'trends' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-6 border-b-2 font-medium text-sm focus:outline-none transition-colors duration-200">
                    <i class="fas fa-chart-area mr-2"></i>
                    Trends
                </button>
            </nav>
        </div>

        <!-- Overview Tab -->
        <div x-show="viewMode === 'overview'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="p-6">
                <!-- Key Metrics -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <!-- Total Applications -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mr-4">
                                    <i class="fas fa-calendar-check text-white text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-blue-600">Total Applications</p>
                                    <p class="text-2xl font-bold text-blue-900">{{ $leaveStats['total_applications'] }}</p>
                                    <p class="text-xs text-blue-600 mt-1">
                                        {{ $leaveStats['pending_applications'] }} pending
                                    </p>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="bg-blue-100 rounded-full p-2">
                                    <i class="fas fa-arrow-up text-blue-600 text-sm"></i>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="w-full bg-blue-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $leaveStats['approved_percentage'] }}%"></div>
                            </div>
                            <p class="text-xs text-blue-600 mt-1">{{ $leaveStats['approved_percentage'] }}% approved</p>
                        </div>
                    </div>

                    <!-- Approved Applications -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 border border-green-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mr-4">
                                    <i class="fas fa-check-circle text-white text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-green-600">Approved</p>
                                    <p class="text-2xl font-bold text-green-900">{{ $leaveStats['approved_applications'] }}</p>
                                    <p class="text-xs text-green-600 mt-1">
                                        {{ $leaveStats['approved_percentage'] }}% rate
                                    </p>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="bg-green-100 rounded-full p-2">
                                    <i class="fas fa-check text-green-600 text-sm"></i>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="w-full bg-green-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $leaveStats['approved_percentage'] }}%"></div>
                            </div>
                            <p class="text-xs text-green-600 mt-1">Success rate</p>
                        </div>
                    </div>

                    <!-- Rejected Applications -->
                    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg p-6 border border-red-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center mr-4">
                                    <i class="fas fa-times-circle text-white text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-red-600">Rejected</p>
                                    <p class="text-2xl font-bold text-red-900">{{ $leaveStats['rejected_applications'] }}</p>
                                    <p class="text-xs text-red-600 mt-1">
                                        {{ $leaveStats['rejected_percentage'] }}% rate
                                    </p>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="bg-red-100 rounded-full p-2">
                                    <i class="fas fa-times text-red-600 text-sm"></i>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="w-full bg-red-200 rounded-full h-2">
                                <div class="bg-red-600 h-2 rounded-full" style="width: {{ $leaveStats['rejected_percentage'] }}%"></div>
                            </div>
                            <p class="text-xs text-red-600 mt-1">Rejection rate</p>
                        </div>
                    </div>

                    <!-- Pending Applications -->
                    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg p-6 border border-yellow-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center mr-4">
                                    <i class="fas fa-clock text-white text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-yellow-600">Pending</p>
                                    <p class="text-2xl font-bold text-yellow-900">{{ $leaveStats['pending_applications'] }}</p>
                                    <p class="text-xs text-yellow-600 mt-1">
                                        Need attention
                                    </p>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="bg-yellow-100 rounded-full p-2">
                                    <i class="fas fa-exclamation text-yellow-600 text-sm"></i>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="w-full bg-yellow-200 rounded-full h-2">
                                <div class="bg-yellow-600 h-2 rounded-full" style="width: {{ ($leaveStats['total_applications'] > 0 ? ($leaveStats['pending_applications'] / $leaveStats['total_applications']) * 100 : 0) }}%"></div>
                            </div>
                            <p class="text-xs text-yellow-600 mt-1">Pending rate</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Leave Type Distribution -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Leave Type Distribution</h3>
                        <div class="space-y-3">
                            @foreach($leaveStats['by_type'] as $type => $count)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-primary-500 rounded-full mr-3"></div>
                                        <span class="text-sm font-medium text-gray-700">{{ $type }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-sm text-gray-900 font-medium">{{ $count }}</span>
                                        <div class="w-24 bg-gray-200 rounded-full h-2 ml-3">
                                            <div class="bg-primary-500 h-2 rounded-full" style="width: {{ ($leaveStats['total_applications'] > 0 ? ($count / $leaveStats['total_applications']) * 100 : 0) }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Monthly Trends Preview -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Trends</h3>
                        <div class="space-y-2">
                            @foreach($monthlyTrends as $trend)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">{{ date('F', mktime(0, 0, 0, $trend->month, 1)) }}</span>
                                    <div class="flex items-center">
                                        <span class="text-sm text-gray-900 font-medium mr-2">{{ $trend->applications }}</span>
                                        <div class="w-20 bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ ($trend->applications / max($monthlyTrends->pluck('applications')->max(), 1)) * 100 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Departments Tab -->
        <div x-show="viewMode === 'departments'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employees</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Applications</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pending</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rejected</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approval Rate</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($departmentStats as $dept)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                                                <i class="fas fa-building text-primary-600 text-sm"></i>
                                            </div>
                                            <div class="text-sm font-medium text-gray-900">{{ $dept->name }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $dept->total_employees }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $dept->total_applications }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $dept->approved_applications }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $dept->pending_applications }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $dept->rejected_applications }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                        $approvalRate = $dept->total_applications > 0 ? round(($dept->approved_applications / $dept->total_applications) * 100, 1) : 0;
                                        $badgeClass = $approvalRate >= 70 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeClass }}">
                                            {{ $approvalRate }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Leave Types Tab -->
        <div x-show="viewMode === 'types'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($leaveStats['by_type'] as $type => $count)
                        <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $type }}</h3>
                                </h3>
                                <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-calendar-alt text-white text-lg"></i>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Total Applications</span>
                                    <span class="text-lg font-bold text-gray-900">{{ $count }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-primary-500 to-primary-600 h-3 rounded-full" style="width: {{ ($leaveStats['total_applications'] > 0 ? ($count / $leaveStats['total_applications']) * 100 : 0) }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Trends Tab -->
        <div x-show="viewMode === 'trends'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="p-6">
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Monthly Application Trends</h3>
                    <div class="space-y-4">
                        @foreach($monthlyTrends as $trend)
                            <div class="flex items-center">
                                <div class="w-24 text-sm text-gray-600 font-medium">{{ date('M', mktime(0, 0, 0, $trend->month, 1)) }}</div>
                                <div class="flex-1 mx-4">
                                    <div class="bg-gray-200 rounded-full h-4 relative">
                                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-4 rounded-full" style="width: {{ ($trend->applications / max($monthlyTrends->pluck('applications')->max(), 1)) * 100 }}%"></div>
                                    </div>
                                </div>
                                <div class="w-20 text-right">
                                    <div class="text-sm font-bold text-gray-900">{{ $trend->applications }}</div>
                                    <div class="text-xs text-gray-600">{{ $trend->approved }} approved</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateReports() {
    // Update reports based on selected period
    console.log('Updating reports for period:', window.reportPeriod);
    // In a real implementation, this would make an AJAX call to update the data
}

function exportReports() {
    window.loading = true;
    
    // Simulate export process
    setTimeout(() => {
        // Create CSV data
        const csvData = 'Department,Employees,Total Applications,Approved,Pending,Rejected\n' +
            @foreach($departmentStats as $dept)
                '{{ $dept->name }},{{ $dept->total_employees }},{{ $dept->total_applications }},{{ $dept->approved_applications }},{{ $dept->pending_applications }},{{ $dept->rejected_applications }}\n' +
            @endforeach
            '';
        
        // Create download link
        const blob = new Blob([csvData], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'department-reports-' + new Date().toISOString().split('T')[0] + '.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        
        window.loading = false;
    }, 1000);
}
</script>
@endsection
                                                    
