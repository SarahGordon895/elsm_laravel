@extends('layouts.enhanced-app')

@section('title', 'Create Department - ELMS')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="flex items-center">
            <a href="{{ route('admin.departments') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Create Department</h1>
                <p class="mt-1 text-sm text-gray-500">Add a new department to the organization</p>
            </div>
        </div>
    </div>

    <!-- Create Form -->
    <div class="bg-white shadow rounded-lg">
        <form action="{{ route('departments.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Department Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Department Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" required
                           class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md"
                           placeholder="Enter department name">
                </div>

                <!-- Short Name -->
                <div>
                    <label for="short_name" class="block text-sm font-medium text-gray-700">
                        Short Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="short_name" name="short_name" required
                           class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md"
                           placeholder="e.g., IT, HR, FIN">
                </div>

                <!-- Department Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">
                        Department Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="code" name="code" required
                           class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md"
                           placeholder="e.g., IT001, HR001">
                </div>

                <!-- Manager -->
                <div>
                    <label for="manager_id" class="block text-sm font-medium text-gray-700">
                        Department Manager
                    </label>
                    <select id="manager_id" name="manager_id"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                        <option value="">Select Manager (Optional)</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Assign a manager to this department</p>
                </div>
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700">
                    Description
                </label>
                <textarea id="description" name="description" rows="4"
                          class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md"
                          placeholder="Enter department description (optional)"></textarea>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.departments') }}" 
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-primary-600 text-white px-6 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <i class="fas fa-plus mr-2"></i>
                    Create Department
                </button>
            </div>
        </form>
    </div>

    <!-- Information Card -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400 text-lg"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Department Information</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Department Name: Full department name for display</li>
                        <li>Short Name: Abbreviation used in dropdowns and lists</li>
                        <li>Department Code: Unique identifier for system integration</li>
                        <li>Manager: Optional manager assignment for department oversight</li>
                        <li>Description: Brief description of department's purpose and functions</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load managers when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadManagers();
});

function loadManagers() {
    fetch('{{ route("users.managers") }}')
        .then(response => response.json())
        .then(managers => {
            const select = document.getElementById('manager_id');
            select.innerHTML = '<option value="">Select Manager (Optional)</option>';
            
            managers.forEach(manager => {
                const option = document.createElement('option');
                option.value = manager.id;
                option.textContent = manager.first_name + ' ' + manager.last_name;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error loading managers:', error);
        });
}
</script>
@endsection
