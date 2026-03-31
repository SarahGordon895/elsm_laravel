@extends('layouts.enhanced-app')

@section('title', 'System Settings - ELMS')

@section('content')
<div class="max-w-4xl mx-auto" x-data="{ activeTab: 'general' }">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">System Settings</h1>
            <p class="mt-1 text-sm text-gray-500">Configure system-wide settings and preferences</p>
        </div>
    </div>

    <!-- Success Messages -->
    @if(session('success'))
        <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4 animate-fade-in">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400 text-lg"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Settings Tabs -->
    <div class="bg-white shadow rounded-lg mt-6">
        <div class="border-b border-gray-200">
            <nav class="flex flex-wrap -mb-px">
                <button @click="activeTab = 'general'" 
                        :class="activeTab === 'general' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-6 border-b-2 font-medium text-sm focus:outline-none transition-colors duration-200">
                    <i class="fas fa-cog mr-2"></i>
                    General Settings
                </button>
                <button @click="activeTab = 'leave-policies'"
                        :class="activeTab === 'leave-policies' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-6 border-b-2 font-medium text-sm focus:outline-none transition-colors duration-200">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    Leave Policies
                </button>
                <button @click="activeTab = 'notifications'"
                        :class="activeTab === 'notifications' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-6 border-b-2 font-medium text-sm focus:outline-none transition-colors duration-200">
                    <i class="fas fa-bell mr-2"></i>
                    Notifications
                </button>
                <button @click="activeTab = 'system'"
                        :class="activeTab === 'system' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-6 border-b-2 font-medium text-sm focus:outline-none transition-colors duration-200">
                    <i class="fas fa-server mr-2"></i>
                    System Info
                </button>
            </nav>
        </div>

        <!-- General Settings Tab -->
        <div x-show="activeTab === 'general'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-6">
            <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Company Information -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Company Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name</label>
                            <input type="text" id="company_name" name="company_name" 
                                   value="{{ setting('company_name', 'Professional Leave Management System') }}"
                                   class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                        </div>
                        <div>
                            <label for="company_email" class="block text-sm font-medium text-gray-700">Company Email</label>
                            <input type="email" id="company_email" name="company_email" 
                                   value="{{ setting('company_email', 'hr@company.com') }}"
                                   class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                        </div>
                        <div class="md:col-span-2">
                            <label for="company_address" class="block text-sm font-medium text-gray-700">Company Address</label>
                            <textarea id="company_address" name="company_address" rows="3"
                                      class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">{{ setting('company_address', '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Application Settings -->
                <div class="bg-blue-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-blue-900 mb-4">Application Settings</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="enable_registration" class="text-sm font-medium text-gray-700">Enable User Registration</label>
                                <p class="text-xs text-gray-500">Allow new users to register themselves</p>
                            </div>
                            <input type="checkbox" id="enable_registration" name="enable_registration" 
                                   {{ setting('enable_registration', false) ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="require_approval" class="text-sm font-medium text-gray-700">Require Manager Approval</label>
                                <p class="text-xs text-gray-500">All leave applications require manager approval</p>
                            </div>
                            <input type="checkbox" id="require_approval" name="require_approval" 
                                   {{ setting('require_approval', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        </div>
                        <div>
                            <label for="max_leave_days" class="block text-sm font-medium text-gray-700">Maximum Leave Days per Request</label>
                            <input type="number" id="max_leave_days" name="max_leave_days" min="1" max="365"
                                   value="{{ setting('max_leave_days', 30) }}"
                                   class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            class="bg-primary-600 text-white px-6 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <i class="fas fa-save mr-2"></i>
                        Save Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- Leave Policies Tab -->
        <div x-show="activeTab === 'leave-policies'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-6">
            <div class="space-y-6">
                <!-- Leave Type Management -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Leave Types</h3>
                        <button onclick="openLeaveTypeModal()" 
                                class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm">
                            <i class="fas fa-plus mr-2"></i>
                            Add Leave Type
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max Days</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requires Approval</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                    $leaveTypes = \App\Models\LeaveType::all();
                                @endphp
                                @foreach($leaveTypes as $leaveType)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $leaveType->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $leaveType->max_days_per_year }} days</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $leaveType->requires_approval ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $leaveType->requires_approval ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $leaveType->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $leaveType->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button onclick="editLeaveType({{ $leaveType->id }})" class="text-primary-600 hover:text-primary-900">Edit</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Leave Balance Settings -->
                <div class="bg-blue-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-blue-900 mb-4">Leave Balance Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="accrual_frequency" class="block text-sm font-medium text-gray-700">Leave Accrual Frequency</label>
                            <select id="accrual_frequency" name="accrual_frequency" 
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                                <option value="monthly" {{ setting('accrual_frequency', 'monthly') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="quarterly" {{ setting('accrual_frequency', 'monthly') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                <option value="yearly" {{ setting('accrual_frequency', 'monthly') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                            </select>
                        </div>
                        <div>
                            <label for="carry_over_days" class="block text-sm font-medium text-gray-700">Maximum Carry Over Days</label>
                            <input type="number" id="carry_over_days" name="carry_over_days" min="0" max="365"
                                   value="{{ setting('carry_over_days', 10) }}"
                                   class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                        </div>
                        <div class="md:col-span-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <label for="enable_carry_over" class="text-sm font-medium text-gray-700">Enable Leave Carry Over</label>
                                    <p class="text-xs text-gray-500">Allow unused leave to be carried over to next year</p>
                                </div>
                                <input type="checkbox" id="enable_carry_over" name="enable_carry_over" 
                                       {{ setting('enable_carry_over', true) ? 'checked' : '' }}
                                       class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications Tab -->
        <div x-show="activeTab === 'notifications'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-6">
            <div class="space-y-6">
                <!-- Email Settings -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Email Notifications</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="email_leave_submitted" class="text-sm font-medium text-gray-700">Leave Application Submitted</label>
                                <p class="text-xs text-gray-500">Notify managers when leave is submitted</p>
                            </div>
                            <input type="checkbox" id="email_leave_submitted" name="email_leave_submitted" 
                                   {{ setting('email_leave_submitted', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="email_leave_approved" class="text-sm font-medium text-gray-700">Leave Application Approved</label>
                                <p class="text-xs text-gray-500">Notify employees when leave is approved</p>
                            </div>
                            <input type="checkbox" id="email_leave_approved" name="email_leave_approved" 
                                   {{ setting('email_leave_approved', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="email_leave_rejected" class="text-sm font-medium text-gray-700">Leave Application Rejected</label>
                                <p class="text-xs text-gray-500">Notify employees when leave is rejected</p>
                            </div>
                            <input type="checkbox" id="email_leave_rejected" name="email_leave_rejected" 
                                   {{ setting('email_leave_rejected', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        </div>
                    </div>
                </div>

                <!-- System Notifications -->
                <div class="bg-blue-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-blue-900 mb-4">System Notifications</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="notify_low_balance" class="text-sm font-medium text-gray-700">Low Leave Balance Warning</label>
                                <p class="text-xs text-gray-500">Warn users when leave balance is low</p>
                            </div>
                            <input type="checkbox" id="notify_low_balance" name="notify_low_balance" 
                                   {{ setting('notify_low_balance', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        </div>
                        <div>
                            <label for="low_balance_threshold" class="block text-sm font-medium text-gray-700">Low Balance Threshold (days)</label>
                            <input type="number" id="low_balance_threshold" name="low_balance_threshold" min="1" max="30"
                                   value="{{ setting('low_balance_threshold', 5) }}"
                                   class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Info Tab -->
        <div x-show="activeTab === 'system'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-6">
            <div class="space-y-6">
                <!-- System Information -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">System Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Application Version</p>
                            <p class="text-lg font-medium text-gray-900">ELMS v1.0.0</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Laravel Version</p>
                            <p class="text-lg font-medium text-gray-900">{{ app()->version() }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">PHP Version</p>
                            <p class="text-lg font-medium text-gray-900">{{ PHP_VERSION }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Environment</p>
                            <p class="text-lg font-medium text-gray-900">{{ app()->environment() }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Database</p>
                            <p class="text-lg font-medium text-gray-900">{{ config('database.default') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Timezone</p>
                            <p class="text-lg font-medium text-gray-900">{{ config('app.timezone') }}</p>
                        </div>
                    </div>
                </div>

                <!-- System Statistics -->
                <div class="bg-blue-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-blue-900 mb-4">System Statistics</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-white rounded-lg p-4 border border-blue-200">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-medium text-gray-900">Total Users</h4>
                                <i class="fas fa-users text-blue-600"></i>
                            </div>
                            <p class="text-2xl font-bold text-blue-600">{{ \App\Models\User::count() }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-blue-200">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-medium text-gray-900">Departments</h4>
                                <i class="fas fa-building text-blue-600"></i>
                            </div>
                            <p class="text-2xl font-bold text-blue-600">{{ \App\Models\Department::count() }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-blue-200">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-medium text-gray-900">Leave Types</h4>
                                <i class="fas fa-calendar-alt text-blue-600"></i>
                            </div>
                            <p class="text-2xl font-bold text-blue-600">{{ \App\Models\LeaveType::count() }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-blue-200">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-medium text-gray-900">Leave Applications</h4>
                                <i class="fas fa-file-alt text-blue-600"></i>
                            </div>
                            <p class="text-2xl font-bold text-blue-600">{{ \App\Models\LeaveApplication::count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Maintenance -->
                <div class="bg-yellow-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-yellow-900 mb-4">System Maintenance</h3>
                    <div class="space-y-3">
                        <button @click="clearCache" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <i class="fas fa-broom mr-2"></i>
                            Clear System Cache
                        </button>
                        <button @click="optimizeDatabase" class="bg-orange-600 text-white px-4 py-2 rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm ml-3">
                            <i class="fas fa-database mr-2"></i>
                            Optimize Database
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function clearCache() {
    if (confirm('Are you sure you want to clear the system cache? This will temporarily slow down the system.')) {
        fetch('/admin/clear-cache', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Cache cleared successfully!');
            } else {
                alert('Failed to clear cache.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while clearing cache.');
        });
    }
}

function optimizeDatabase() {
    if (confirm('Are you sure you want to optimize the database? This may take a few moments.')) {
        fetch('/admin/optimize-database', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Database optimized successfully!');
            } else {
                alert('Failed to optimize database.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while optimizing database.');
        });
    }
}

</script>
@endsection
