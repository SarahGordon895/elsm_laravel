@extends('layouts.enhanced-app')

@section('title', 'Create Leave Application - ELMS')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <form action="{{ route('leave-applications.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-red-400 text-lg"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-800 font-medium">Please fix the following errors:</p>
                            <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Personal Information -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Employee</label>
                        <div class="mt-1 flex items-center">
                            <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-primary-600 font-medium text-sm">
                                    {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ auth()->user()->full_name }}</p>
                                <p class="text-xs text-gray-500">{{ auth()->user()->employee_id }}</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Department</label>
                        <div class="mt-1">
                            <p class="text-sm text-gray-900">{{ auth()->user()->department?->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leave Details -->
            <div class="space-y-4">
                <div>
                    <label for="leave_type_id" class="block text-sm font-medium text-gray-700">
                        Leave Type <span class="text-red-500">*</span>
                    </label>
                    <select id="leave_type_id" name="leave_type_id" required
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                        <option value="">Select a leave type</option>
                        @foreach($leaveTypes as $leaveType)
                            <option value="{{ $leaveType->id }}" {{ old('leave_type_id') == $leaveType->id ? 'selected' : '' }}>
                                {{ $leaveType->name }} ({{ $leaveType->max_days_per_year }} days/year)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">
                            Start Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="start_date" name="start_date" required
                               value="{{ old('start_date') }}"
                               min="{{ now()->format('Y-m-d') }}"
                               class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">
                            End Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="end_date" name="end_date" required
                               value="{{ old('end_date') }}"
                               min="{{ now()->format('Y-m-d') }}"
                               class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                    </div>
                </div>

                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700">
                        Reason <span class="text-red-500">*</span>
                    </label>
                    <textarea id="reason" name="reason" rows="4" required
                              placeholder="Please provide a detailed reason for your leave application..."
                              class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">{{ old('reason') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Maximum 1000 characters</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Supporting Documents</label>
                    <div class="mt-1">
                        <input type="file" name="documents[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-md
                                      border border-gray-300 placeholder:text-gray-400 focus:outline-none
                                      focus:ring-primary-500 focus:border-primary-500">
                        <p class="text-xs text-gray-500">
                            Upload supporting documents (PDF, DOC, DOCX, JPG, PNG). Maximum 5 files, 2MB each.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Leave Balance Information -->
            <div class="bg-blue-50 rounded-lg p-4">
                <h3 class="text-lg font-medium text-blue-900 mb-4">Your Leave Balances</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($leaveBalances as $balance)
                        <div class="bg-white rounded-lg p-4 border border-blue-200">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-medium text-gray-900">{{ $balance->leaveType->name }}</h4>
                                <span class="text-xs text-gray-500">{{ $balance->leaveType->max_days_per_year }} days/year</span>
                            </div>
                            <div class="space-y-1">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Available:</span>
                                    <span class="font-medium text-green-600">{{ $balance->available_days }} days</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Used:</span>
                                    <span class="font-medium text-red-600">{{ $balance->used_days }} days</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Balance:</span>
                                    <span class="font-medium text-blue-600">{{ $balance->balance_days }} days</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('leave-applications.index') }}" 
                   class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-primary-600 text-white px-6 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Submit Application
                </button>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    
    startDate.addEventListener('change', function() {
        endDate.min = this.value;
        if (endDate.value && endDate.value < this.value) {
            endDate.value = this.value;
        }
        updateDuration();
    });
    
    endDate.addEventListener('change', updateDuration);
    
    function updateDuration() {
        if (startDate.value && endDate.value) {
            const start = new Date(startDate.value);
            const end = new Date(endDate.value);
            const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
            
            // Update duration display if needed
            const durationElement = document.getElementById('duration-display');
            if (durationElement) {
                durationElement.textContent = `${days} days`;
            }
        }
    }
});
</script>
@endsection
