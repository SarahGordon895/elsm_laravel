@extends('layouts.enhanced-app')

@section('title', 'Leave Applications - HR Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Leave Applications</h1>
                <p class="text-gray-600 mt-1">Manage and review employee leave requests</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('hr.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                <select id="departmentFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Departments</option>
                    @foreach(App\Models\Department::all() as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Leave Type</label>
                <select id="leaveTypeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Types</option>
                    @foreach(App\Models\LeaveType::all() as $leaveType)
                        <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" id="searchFilter" placeholder="Search employee..." class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900">Applications ({{ $applications->total() }})</h2>
                <div class="flex space-x-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        {{ $applications->where('status', 'pending')->count() }} Pending
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ $applications->where('status', 'approved')->count() }} Approved
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        {{ $applications->where('status', 'rejected')->count() }} Rejected
                    </span>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applied</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($applications as $application)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img src="{{ $application->user->profile_photo ?? asset('images/default-avatar.png') }}" 
                                         alt="{{ $application->user->full_name }}" 
                                         class="w-8 h-8 rounded-full mr-3">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $application->user->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $application->user->department->name ?? 'No Department' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $application->leaveType->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $application->duration }} days</div>
                                <div class="text-sm text-gray-500">{{ $application->start_date->format('M d, Y') }} - {{ $application->end_date->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $application->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($application->status == 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $application->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('hr.leave-applications.show', $application) }}" 
                                       class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($application->status == 'pending')
                                        <button onclick="approveLeave({{ $application->id }})" 
                                                class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button onclick="rejectLeave({{ $application->id }})" 
                                                class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No leave applications found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $applications->links() }}
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div id="approvalModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Confirm Action</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="modalMessage">Are you sure you want to proceed?</p>
            </div>
            <div class="items-center px-4 py-3">
                <input type="hidden" id="applicationId" />
                <input type="hidden" id="actionType" />
                <textarea id="rejectionReason" class="hidden w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                          rows="3" placeholder="Please provide a reason for rejection..."></textarea>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="submitApproval()" id="submitBtn" class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md w-full hover:bg-blue-700">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function approveLeave(id) {
    document.getElementById('applicationId').value = id;
    document.getElementById('actionType').value = 'approve';
    document.getElementById('modalMessage').textContent = 'Are you sure you want to approve this leave application?';
    document.getElementById('rejectionReason').classList.add('hidden');
    document.getElementById('submitBtn').textContent = 'Approve';
    document.getElementById('submitBtn').className = 'px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md w-full hover:bg-green-700';
    document.getElementById('approvalModal').classList.remove('hidden');
}

function rejectLeave(id) {
    document.getElementById('applicationId').value = id;
    document.getElementById('actionType').value = 'reject';
    document.getElementById('modalMessage').textContent = 'Are you sure you want to reject this leave application?';
    document.getElementById('rejectionReason').classList.remove('hidden');
    document.getElementById('submitBtn').textContent = 'Reject';
    document.getElementById('submitBtn').className = 'px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full hover:bg-red-700';
    document.getElementById('approvalModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('approvalModal').classList.add('hidden');
    document.getElementById('rejectionReason').value = '';
}

function submitApproval() {
    const id = document.getElementById('applicationId').value;
    const action = document.getElementById('actionType').value;
    const reason = document.getElementById('rejectionReason').value;
    
    const formData = new FormData();
    formData.append('application_id', id);
    
    const url = action === 'approve' ? 
        `{{ route('hr.leave-applications.approve', ':id') }}`.replace(':id', id) :
        `{{ route('hr.leave-applications.reject', ':id') }}`.replace(':id', id);
    
    if (action === 'reject') {
        formData.append('reason', reason);
    }
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'An error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing your request');
    });
}
</script>
@endsection
