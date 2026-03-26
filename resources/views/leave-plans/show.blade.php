@extends('layouts.enhanced-app')

@section('title', 'Leave Plan Details - ELMS')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('leave-plans.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Leave Plan Details</h1>
                    <p class="mt-1 text-sm text-gray-500">View and manage leave plan information</p>
                </div>
            </div>
            <div class="flex space-x-2">
                @if($leavePlan->status === 'pending' && auth()->user()->hasPermission('approve-leave-plans'))
                    <form action="{{ route('leave-plans.approve', $leavePlan) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                                onclick="return confirm('Are you sure you want to approve this leave plan?')">
                            <i class="fas fa-check mr-2"></i>
                            Approve
                        </button>
                    </form>
                @endif
                
                @if($leavePlan->status === 'pending' && auth()->user()->hasPermission('reject-leave-plans'))
                    <button type="button" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                            onclick="openRejectModal({{ $leavePlan->id }})">
                        <i class="fas fa-times mr-2"></i>
                        Reject
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Leave Plan Details -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6">
            <!-- Status Badge -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-900">Plan Information</h2>
                    {!! $leavePlan->status_badge !!}
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Employee Information -->
                <div class="space-y-4">
                    <h3 class="text-sm font-medium text-gray-900 border-b pb-2">Employee Details</h3>
                    
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                            <span class="text-primary-600 font-medium">
                                {{ strtoupper(substr($leavePlan->user->first_name, 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $leavePlan->user->full_name }}</p>
                            <p class="text-sm text-gray-500">{{ $leavePlan->user->employee_id }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Department:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $leavePlan->user->department?->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Role:</span>
                            <span class="text-sm font-medium text-gray-900">{{ ucfirst($leavePlan->user->role) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Email:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $leavePlan->user->email }}</span>
                        </div>
                    </div>
                </div>

                <!-- Leave Plan Details -->
                <div class="space-y-4">
                    <h3 class="text-sm font-medium text-gray-900 border-b pb-2">Leave Plan Details</h3>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Leave Type:</span>
                            <span class="text-sm font-medium text-gray-900">
                                {{ $leavePlan->leaveType->name }}
                                <span class="ml-1 px-2 py-1 text-xs rounded-full {{ $leavePlan->leaveType->paid ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $leavePlan->leaveType->paid ? 'Paid' : 'Unpaid' }}
                                </span>
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Allocated Days:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $leavePlan->allocated_days }} days</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Used Days:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $leavePlan->used_days }} days</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Remaining Days:</span>
                            <span class="text-sm font-medium text-primary-600">{{ $leavePlan->remaining_days }} days</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Effective Date:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $leavePlan->effective_date->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Expiry Date:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $leavePlan->expiry_date->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mt-6 pt-6 border-t">
                <h3 class="text-sm font-medium text-gray-900 mb-3">Leave Usage Progress</h3>
                <div class="relative">
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="bg-primary-600 h-4 rounded-full" style="width: {{ ($leavePlan->used_days / $leavePlan->allocated_days) * 100 }}%"></div>
                    </div>
                    <div class="flex justify-between mt-2">
                        <span class="text-xs text-gray-500">{{ $leavePlan->used_days }} days used</span>
                        <span class="text-xs text-gray-500">{{ $leavePlan->remaining_days }} days remaining</span>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($leavePlan->notes)
                <div class="mt-6 pt-6 border-t">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Notes</h3>
                    <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-md">{{ $leavePlan->notes }}</p>
                </div>
            @endif

            <!-- Rejection Reason -->
            @if($leavePlan->rejection_reason)
                <div class="mt-6 pt-6 border-t">
                    <h3 class="text-sm font-medium text-red-900 mb-3">Rejection Reason</h3>
                    <p class="text-sm text-red-700 bg-red-50 p-3 rounded-md">{{ $leavePlan->rejection_reason }}</p>
                </div>
            @endif

            <!-- Approval Information -->
            @if($leavePlan->approver)
                <div class="mt-6 pt-6 border-t">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Approval Information</h3>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-green-600 font-medium text-sm">
                                {{ strtoupper(substr($leavePlan->approver->first_name, 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $leavePlan->approver->full_name }}</p>
                            <p class="text-sm text-gray-500">{{ $leavePlan->updated_at->format('M d, Y \a\t H:i') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="mt-6 pt-6 border-t">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-500">
                        Created on {{ $leavePlan->created_at->format('M d, Y \a\t H:i') }}
                    </div>
                    <div class="flex space-x-2">
                        @if($leavePlan->status !== 'approved' && $leavePlan->user_id === auth()->id())
                            <a href="{{ route('leave-plans.edit', $leavePlan) }}" 
                               class="text-primary-600 hover:text-primary-800 text-sm">
                                <i class="fas fa-edit mr-1"></i>
                                Edit
                            </a>
                        @endif
                        
                        @if($leavePlan->status !== 'approved' && $leavePlan->user_id === auth()->id())
                            <form action="{{ route('leave-plans.destroy', $leavePlan) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm"
                                        onclick="return confirm('Are you sure you want to delete this leave plan?')">
                                    <i class="fas fa-trash mr-1"></i>
                                    Delete
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Reject Leave Plan</h3>
                <button type="button" onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="rejectForm" action="" method="POST">
                @csrf
                @method('POST')
                <input type="hidden" name="_method" value="POST">
                
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Rejection Reason <span class="text-red-500">*</span>
                    </label>
                    <textarea id="rejection_reason" name="rejection_reason" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                              placeholder="Please provide a reason for rejecting this leave plan..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeRejectModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Reject Leave Plan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openRejectModal(leavePlanId) {
    const form = document.getElementById('rejectForm');
    form.action = `/leave-plans/${leavePlanId}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejection_reason').value = '';
}
</script>
@endsection
