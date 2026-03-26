@extends('layouts.enhanced-app')

@section('title', 'Create Leave Plan - ELMS')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="flex items-center">
            <a href="{{ route('leave-plans.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Create Leave Plan</h1>
                <p class="mt-1 text-sm text-gray-500">Create a new leave plan for HR approval</p>
            </div>
        </div>
    </div>

    <!-- Create Form -->
    <div class="bg-white shadow rounded-lg">
        <form action="{{ route('leave-plans.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Leave Type -->
                <div class="md:col-span-2">
                    <label for="leave_type_id" class="block text-sm font-medium text-gray-700">
                        Leave Type <span class="text-red-500">*</span>
                    </label>
                    <select id="leave_type_id" name="leave_type_id" required
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                        <option value="">Select a leave type</option>
                        @foreach($leaveTypes as $leaveType)
                            <option value="{{ $leaveType->id }}">
                                {{ $leaveType->name }} ({{ $leaveType->paid ? 'Paid' : 'Unpaid' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Allocated Days -->
                <div>
                    <label for="allocated_days" class="block text-sm font-medium text-gray-700">
                        Allocated Days <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="allocated_days" name="allocated_days" required
                           min="1" max="365"
                           class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md"
                           placeholder="Number of days">
                    <p class="mt-1 text-xs text-gray-500">Maximum 365 days per year</p>
                </div>

                <!-- Effective Date -->
                <div>
                    <label for="effective_date" class="block text-sm font-medium text-gray-700">
                        Effective Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="effective_date" name="effective_date" required
                           class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                    <p class="mt-1 text-xs text-gray-500">When the leave plan becomes effective</p>
                </div>

                <!-- Expiry Date -->
                <div class="md:col-span-2">
                    <label for="expiry_date" class="block text-sm font-medium text-gray-700">
                        Expiry Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="expiry_date" name="expiry_date" required
                           class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                    <p class="mt-1 text-xs text-gray-500">When the leave plan expires</p>
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700">
                        Notes
                    </label>
                    <textarea id="notes" name="notes" rows="4"
                              class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md"
                              placeholder="Additional notes or comments..."></textarea>
                    <p class="mt-1 text-xs text-gray-500">Optional notes for HR review</p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('leave-plans.index') }}" 
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Submit for Approval
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
                <h3 class="text-sm font-medium text-blue-800">Leave Plan Information</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Your leave plan will be submitted for HR approval</li>
                        <li>Once approved, the allocated days will be added to your leave balance</li>
                        <li>You will receive notifications about the approval status</li>
                        <li>Pending plans can be edited or deleted</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Set minimum date to today
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('effective_date').setAttribute('min', today);
    document.getElementById('expiry_date').setAttribute('min', today);
    
    // Update expiry date minimum when effective date changes
    document.getElementById('effective_date').addEventListener('change', function() {
        const effectiveDate = new Date(this.value);
        const minExpiryDate = new Date(effectiveDate);
        minExpiryDate.setDate(minExpiryDate.getDate() + 1);
        
        document.getElementById('expiry_date').setAttribute('min', minExpiryDate.toISOString().split('T')[0]);
    });
});
</script>
@endsection
