@extends('layouts.enhanced-app')

@section('title', 'Create Leave Application - ELMS')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Create Leave Application</h1>
            <p class="mt-1 text-sm text-gray-500">Submit a new leave application for approval</p>
        </div>
        <div class="flex items-center">
            <a href="{{ route('leave-applications.index') }}" 
               class="text-gray-600 hover:text-gray-900 px-4 py-2 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-500">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Applications
            </a>
        </div>
    </div>

    <!-- Leave Balance Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400 text-lg"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Your Current Leave Balance</h3>
                <p class="text-sm text-blue-700">Check your available leave days before applying</p>
            </div>
        </div>
    </div>

    <!-- Application Form -->
    <div class="bg-white shadow rounded-lg">
        <form action="{{ route('leave-applications.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Error Messages -->
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Leave Type Selection -->
            <div class="grid grid-cols-1">
                <div>
                    <label for="leave_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Leave Type <span class="text-red-500">*</span>
                    </label>
                    <select id="leave_type_id" name="leave_type_id" required
                            class="mt-1 block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                        <option value="">Select a leave type</option>
                        @foreach($leaveTypes as $leaveType)
                            <option value="{{ $leaveType->id }}" {{ old('leave_type_id') == $leaveType->id ? 'selected' : '' }}>
                                {{ $leaveType->name }} ({{ $leaveType->max_days_per_year }} days/year)
                            </option>
                        @endforeach
                    </select>
                    @error('leave_type_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Start Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="start_date" name="start_date" required
                               value="{{ old('start_date') }}"
                               min="{{ now()->format('Y-m-d') }}"
                               class="mt-1 block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                        @error('start_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            End Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="end_date" name="end_date" required
                               value="{{ old('end_date') }}"
                               :min="document.getElementById('start_date').value"
                               class="mt-1 block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                        @error('end_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Reason -->
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Reason for Leave <span class="text-red-500">*</span>
                    </label>
                    <textarea id="reason" name="reason" rows="4" required
                              class="mt-1 block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md"
                              placeholder="Provide a reason for your leave application">{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Attachments -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Attachments (Optional)
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded-md">
                                <input type="file" name="attachments[]" multiple accept=".pdf,.doc,.docx,.jpg,.png" class="hidden" id="fileInput">
                                <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center cursor-pointer hover:bg-gray-300">
                                    <i class="fas fa-cloud-upload-alt text-gray-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-600">Click to upload files</p>
                                    <p class="text-xs text-gray-500">PDF, DOC, DOCX, JPG, PNG</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <a href="{{ route('leave-applications.index') }}" 
                       class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-primary-600 text-white px-6 py-3 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors duration-200">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Submit Application
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Leave Balance Display -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Available Leave Balance</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($leaveBalances as $balance)
                @php
                    $remaining = max(0, ($balance->balance_days + $balance->carry_over_days) - $balance->used_days);
                    $allocation = max(1, $balance->balance_days + $balance->carry_over_days);
                    $percent = min(100, round(($remaining / $allocation) * 100));
                    $badgeClass = $remaining > 5 ? 'bg-green-100 text-green-800' : ($remaining > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                @endphp
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-medium text-gray-900">{{ $balance->leaveType ? $balance->leaveType->name : 'Leave Type' }}</h4>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $badgeClass }}">
                            {{ $remaining }} days
                        </span>
                    </div>
                    <div class="text-xs text-gray-500">
                        Allocation: {{ $allocation }} | Used: {{ $balance->used_days }} | Remaining: {{ $remaining }}
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div class="bg-primary-600 h-2 rounded-full" style="width: {{ $percent }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');

    if (startDate && endDate) {
        startDate.addEventListener('change', function () {
            endDate.min = this.value;
            if (endDate.value && endDate.value < this.value) {
                endDate.value = this.value;
            }
        });

        endDate.addEventListener('change', function () {
            if (startDate.value && this.value < startDate.value) {
                this.value = startDate.value;
            }
        });
    }
});
</script>
@endsection
