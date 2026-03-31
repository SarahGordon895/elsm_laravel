@extends('layouts.enhanced-app')

@section('title', 'Department Management - ELMS')

@section('content')
<div class="space-y-6" x-data="{ 
    showModal: false, 
    editingDepartment: null, 
    departments: @json($departments ?? []),
    form: {
        name: '',
        short_name: '',
        code: '',
        description: '',
        manager_id: ''
    },
    viewMode: 'grid', // grid or list
    selectedDepartment: null
}">
    <!-- Debug Section -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <h3 class="text-sm font-medium text-yellow-800 mb-2">Debug Information</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="font-medium text-yellow-800">Departments Count:</span>
                <span class="font-mono text-yellow-900">{{ $departments->count() }}</span>
            </div>
            <div>
                <span class="font-medium text-yellow-800">Show Modal:</span>
                <span class="font-mono text-yellow-900" x-text="showModal ? 'Yes' : 'No'"></span>
            </div>
            <div>
                <span class="font-medium text-yellow-800">View Mode:</span>
                <span class="font-mono text-yellow-900" x-text="viewMode"></span>
            </div>
            <div>
                <span class="font-medium text-yellow-800">Form Name:</span>
                <span class="font-mono text-yellow-900" x-text="form.name"></span>
            </div>
        </div>
        <!-- Department Names Display -->
        <div class="mt-4">
            <h4 class="text-sm font-medium text-yellow-800 mb-2">Department Names:</h4>
            <div class="space-y-1">
                @foreach($departments as $department)
                    <div class="text-xs font-mono text-yellow-900">
                        • {{ $department->name }} ({{ $department->users->count() }} users)
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Department Management</h1>
            <p class="mt-1 text-sm text-gray-500">Manage organizational departments and their leave structure</p>
        </div>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
            <!-- View Toggle -->
            <div class="flex bg-gray-100 rounded-lg p-1">
                <button @click="viewMode = 'grid'" 
                        :class="viewMode === 'grid' ? 'bg-white shadow-sm' : ''"
                        class="px-3 py-1 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
                    <i class="fas fa-th-large"></i>
                </button>
                <button @click="viewMode = 'list'" 
                        :class="viewMode === 'list' ? 'bg-white shadow-sm' : ''"
                        class="px-3 py-1 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
                    <i class="fas fa-list"></i>
                </button>
            </div>
            
            <!-- Add Department Button -->
            <button @click="showModal = true; editingDepartment = null; resetForm(); console.log('Button clicked', showModal)" 
                    class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                <i class="fas fa-plus mr-2"></i>
                Add Department
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Departments Card -->
        <div class="bg-white shadow rounded-lg p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                        <i class="fas fa-building text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Departments</p>
                        <p class="text-2xl font-bold text-gray-900" x-text="departments.length"></p>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <div class="bg-blue-100 rounded-full p-2">
                        <i class="fas fa-arrow-up text-blue-600 text-sm"></i>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" :style="`width: ${(departments.length / 10) * 100}%`"></div>
                </div>
            </div>
        </div>
        
        <!-- Total Employees Card -->
        <div class="bg-white shadow rounded-lg p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                        <i class="fas fa-users text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Employees</p>
                        <p class="text-2xl font-bold text-gray-900" x-text="departments.reduce((sum, dept) => sum + (dept.users?.length || 0), 0)"></p>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <div class="bg-green-100 rounded-full p-2">
                        <i class="fas fa-users text-green-600 text-sm"></i>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full" :style="`width: ${Math.min((departments.reduce((sum, dept) => sum + (dept.users?.length || 0), 0) / 50) * 100}%`"></div>
                </div>
            </div>
        </div>
        
        <!-- Managed Departments Card -->
        <div class="bg-white shadow rounded-lg p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                        <i class="fas fa-user-tie text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Managed Departments</p>
                        <p class="text-2xl font-bold text-gray-900" x-text="departments.filter(dept => dept.manager_id).length"></p>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <div class="bg-purple-100 rounded-full p-2">
                        <i class="fas fa-user-tie text-purple-600 text-sm"></i>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-purple-600 h-2 rounded-full" :style="`width: ${(departments.filter(dept => dept.manager_id).length / departments.length) * 100}%`"></div>
                </div>
            </div>
        </div>
        
        <!-- Active Leave Plans Card -->
        <div class="bg-white shadow rounded-lg p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-orange-100 rounded-lg p-3">
                        <i class="fas fa-calendar-alt text-orange-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Active Leave Plans</p>
                        <p class="text-2xl font-bold text-gray-900" x-text="departments.reduce((sum, dept) => sum + (dept.active_leave_plans || 0), 0)"></p>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <div class="bg-orange-100 rounded-full p-2">
                        <i class="fas fa-calendar-alt text-orange-600 text-sm"></i>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-orange-600 h-2 rounded-full" :style="`width: ${Math.min((departments.reduce((sum, dept) => sum + (dept.active_leave_plans || 0), 0) / 20) * 100}%`"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Departments Grid View -->
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4 sm:gap-6">
        <template x-for="department in departments" :key="department.id">
            <div class="bg-white shadow-lg rounded-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                <!-- Department Header -->
                <div class="p-4 sm:p-6 border-b border-gray-200">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-building text-white text-sm sm:text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-sm sm:text-lg font-semibold text-gray-900" x-text="department.name"></h3>
                                <p class="text-xs sm:text-sm text-gray-500" x-text="department.short_name"></p>
                            </div>
                        </div>
                        <div class="flex space-x-1 sm:space-x-2">
                            <button @click="editDepartment(department)" 
                                    class="text-primary-600 hover:text-primary-900 p-1.5 sm:p-2 rounded-md hover:bg-primary-50 transition-colors">
                                <i class="fas fa-edit text-sm sm:text-base"></i>
                            </button>
                            <button @click="deleteDepartment(department)" 
                                    class="text-red-600 hover:text-red-900 p-1.5 sm:p-2 rounded-md hover:bg-red-50 transition-colors">
                                <i class="fas fa-trash text-sm sm:text-base"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Department Stats -->
                    <div class="grid grid-cols-2 gap-3 sm:gap-4">
                        <div class="text-center">
                            <div class="text-xl sm:text-2xl font-bold text-gray-900" x-text="department.users?.length || 0"></div>
                            <div class="text-xs text-gray-500">Employees</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl sm:text-2xl font-bold text-gray-900">
                                <span x-text="department.pending_leaves || 0"></span>
                            </div>
                            <div class="text-xs text-gray-500">Pending Leaves</div>
                        </div>
                    </div>
                </div>

                <!-- Department Details -->
                <div class="p-4 sm:p-6 space-y-3">
                    <div class="flex items-center text-sm">
                        <i class="fas fa-id-badge text-gray-400 mr-2 w-4"></i>
                        <span class="text-gray-600">Code:</span>
                        <span class="font-medium text-gray-900 ml-1" x-text="department.code"></span>
                    </div>
                    
                    <div class="flex items-center text-sm" x-show="department.manager">
                        <i class="fas fa-user-tie text-gray-400 mr-2 w-4"></i>
                        <span class="text-gray-600">Manager:</span>
                        <span class="font-medium text-gray-900 ml-1" x-text="department.manager?.full_name"></span>
                    </div>
                    
                    <div class="text-sm text-gray-600" x-show="department.description">
                        <p class="line-clamp-2" x-text="department.description"></p>
                    </div>
                </div>

                <!-- Department Actions -->
                <div class="px-4 sm:px-6 pb-4 sm:pb-6">
                    <div class="flex flex-col space-y-2">
                        <button @click="viewDepartmentDetails(department)" 
                                class="w-full text-center px-3 sm:px-4 py-2 bg-primary-50 text-primary-700 rounded-md hover:bg-primary-100 transition-colors font-medium text-sm">
                            <i class="fas fa-info-circle mr-2"></i>
                            View Details
                        </button>
                        <button @click="viewDepartmentUsers(department)" 
                                class="w-full text-center px-3 sm:px-4 py-2 bg-gray-50 text-gray-700 rounded-md hover:bg-gray-100 transition-colors font-medium text-sm"
                                x-show="department.users && department.users.length > 0">
                            <i class="fas fa-users mr-2"></i>
                            <span x-text="'View Users (' + (department.users?.length || 0) + ')'"></span>
                        </button>
                        <button @click="viewDepartmentLeavePlans(department)" 
                                class="w-full text-center px-3 sm:px-4 py-2 bg-purple-50 text-purple-700 rounded-md hover:bg-purple-100 transition-colors font-medium text-sm">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            Leave Plans
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Departments List View -->
    <div x-show="viewMode === 'list'" class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Department
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Code
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Manager
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Employees
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Leave Plans
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="department in departments" :key="department.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-building text-primary-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900" x-text="department.name"></div>
                                        <div class="text-sm text-gray-500" x-text="department.short_name"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800" x-text="department.code"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900" x-text="department.manager?.full_name || 'Not Assigned'"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900" x-text="department.users?.length || 0"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">0 Active</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button @click="viewDepartmentDetails(department)" class="text-primary-600 hover:text-primary-900">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button @click="editDepartment(department)" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="deleteDepartment(department)" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Empty State -->
    <div x-show="departments.length === 0" class="text-center py-12">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-building text-gray-400 text-4xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No Departments Found</h3>
        <p class="text-gray-500 mb-4">Get started by creating your first department.</p>
        <button @click="showModal = true; editingDepartment = null; resetForm()" 
                class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
            <i class="fas fa-plus mr-2"></i>
            Create Department
        </button>
    </div>

    <!-- Create/Edit Department Modal -->
    <div x-show="showModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 x-text="editingDepartment ? 'Edit Department' : 'Create Department'" class="text-lg font-semibold text-gray-900"></h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form @submit.prevent="saveDepartment" class="space-y-4">
                    <input type="hidden" x-model="form.id">
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Department Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" x-model="form.name" required
                               class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md"
                               placeholder="Enter department name">
                    </div>

                    <div>
                        <label for="short_name" class="block text-sm font-medium text-gray-700">
                            Short Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="short_name" x-model="form.short_name" required
                               class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md"
                               placeholder="e.g., IT, HR, FIN">
                    </div>

                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700">
                            Department Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="code" x-model="form.code" required
                               class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md"
                               placeholder="e.g., IT001, HR001">
                    </div>

                    <div>
                        <label for="manager_id" class="block text-sm font-medium text-gray-700">
                            Department Manager
                        </label>
                        <select id="manager_id" x-model="form.manager_id"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                            <option value="">Select Manager (Optional)</option>
                            <template x-for="manager in managers" :key="manager.id">
                                <option :value="manager.id" :selected="form.manager_id == manager.id">
                                    <span x-text="manager.first_name + ' ' + manager.last_name"></span>
                                </option>
                            </template>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Assign a manager to this department</p>
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">
                            Description
                        </label>
                        <textarea id="description" x-model="form.description" rows="4"
                                  class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md"
                                  placeholder="Enter department description (optional)"></textarea>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                        <button type="button" @click="showModal = false"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="bg-primary-600 text-white px-6 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <i class="fas fa-save mr-2"></i>
                            <span x-text="editingDepartment ? 'Update Department' : 'Create Department'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div x-show="message" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         :class="messageType === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800'"
         class="fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-md">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-400 text-lg" x-show="messageType === 'success'"></i>
                <i class="fas fa-exclamation-circle text-red-400 text-lg" x-show="messageType === 'error'"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium" x-text="message"></p>
            </div>
        </div>
    </div>
</div>

<script>
function loadManagers() {
    fetch('/admin/users/managers')
        .then(response => response.json())
        .then(data => {
            window.managers = data;
        })
        .catch(error => {
            console.error('Error loading managers:', error);
        });
}

function resetForm() {
    window.form = {
        name: '',
        short_name: '',
        code: '',
        description: '',
        manager_id: ''
    };
}

function editDepartment(department) {
    window.editingDepartment = department;
    window.form = {
        id: department.id,
        name: department.name,
        short_name: department.short_name,
        code: department.code,
        description: department.description || '',
        manager_id: department.manager_id || ''
    };
    window.showModal = true;
}

function deleteDepartment(department) {
    if (confirm('Are you sure you want to delete this department? This action cannot be undone.')) {
        fetch(`/admin/departments/${department.id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                showMessage(data.message || 'Failed to delete department', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Failed to delete department', 'error');
        });
    }
}

function viewDepartmentDetails(department) {
    // Navigate to department details page
    window.location.href = `/admin/departments/${department.id}`;
}

function viewDepartmentUsers(department) {
    // Navigate to department users page
    window.location.href = `/admin/departments/${department.id}/users`;
}

function viewDepartmentLeavePlans(department) {
    // Navigate to department leave plans page
    window.location.href = `/admin/departments/${department.id}/leave-plans`;
}

function saveDepartment() {
    const url = window.editingDepartment ? 
        `/admin/departments/${window.editingDepartment.id}` : 
        '/admin/departments';
    
    const method = window.editingDepartment ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(window.form)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.showModal = false;
            window.message = data.message;
            window.messageType = 'success';
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            window.message = data.message || 'Failed to save department';
            window.messageType = 'error';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        window.message = 'Failed to save department';
        window.messageType = 'error';
    });
}

function showMessage(message, type = 'success') {
    window.message = message;
    window.messageType = type;
    setTimeout(() => {
        window.message = '';
        window.messageType = 'success';
    }, 3000);
}

// Load managers when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadManagers();
});
</script>
@endsection
