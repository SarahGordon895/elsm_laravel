@extends('layouts.enhanced-app')

@section('title', 'Audit Logs - ELMS')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Audit Logs</h1>
            <p class="mt-1 text-sm text-gray-500">System activity and user action logs</p>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="exportAuditLogs()" 
                    class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                <i class="fas fa-download mr-2"></i>
                Export Logs
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Action</label>
                    <select name="action" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                        <option value="">All Actions</option>
                        <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Created</option>
                        <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Updated</option>
                        <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                        <option value="approved" {{ request('action') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('action') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                        <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                        <option value="status_changed" {{ request('action') == 'status_changed' ? 'selected' : '' }}>Status Changed</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">User</label>
                    <select name="user_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <i class="fas fa-filter mr-2"></i>
                    Apply Filters
                </button>
                <a href="{{ route('admin.audit-logs') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-white shadow rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Timestamp
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            User
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Action
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Target
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            IP Address
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Details
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($auditLogs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div>
                                    <p>{{ $log->created_at->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-400">{{ $log->created_at->format('g:i A') }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-gray-600 text-xs font-medium">
                                            {{ strtoupper(substr($log->user->first_name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $log->user->full_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $log->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $log->action == 'created' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $log->action == 'updated' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $log->action == 'deleted' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $log->action == 'approved' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $log->action == 'rejected' ? 'bg-rose-100 text-rose-800' : '' }}
                                    {{ $log->action == 'login' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $log->action == 'logout' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $log->action == 'status_changed' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($log->model_type)
                                    <div>
                                        <p class="font-medium">{{ class_basename($log->model_type) }}</p>
                                        @if($log->model_id)
                                            <p class="text-xs text-gray-400">ID: {{ $log->model_id }}</p>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $log->ip_address ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if($log->new_values)
                                    <div class="max-w-xs">
                                        <button onclick="toggleDetails({{ $log->id }})" 
                                                class="text-primary-600 hover:text-primary-800 text-xs font-medium">
                                            View Details
                                        </button>
                                        <div id="details-{{ $log->id }}" class="hidden mt-2 p-2 bg-gray-50 rounded text-xs">
                                            <pre class="whitespace-pre-wrap">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $auditLogs->links() }}
        </div>
    </div>

    <!-- Empty State -->
    @if($auditLogs->isEmpty())
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-history text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Audit Logs Found</h3>
            <p class="text-gray-500 mb-4">No system activity logs match your current filters.</p>
            <a href="{{ route('admin.audit-logs') }}" 
               class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                Clear Filters
            </a>
        </div>
    @endif

    <!-- Statistics Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-list text-blue-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Logs</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $auditLogs->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-plus text-green-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Created</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $auditLogs->where('action', 'created')->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-edit text-blue-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Updated</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $auditLogs->where('action', 'updated')->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-trash text-red-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Deleted</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $auditLogs->where('action', 'deleted')->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
function toggleDetails(logId) {
    const detailsDiv = document.getElementById(`details-${logId}`);
    detailsDiv.classList.toggle('hidden');
}

function exportAuditLogs() {
    // Get current filter parameters
    const url = new URL(window.location);
    const params = new URLSearchParams(url.search);
    
    // Build export URL with current filters
    const exportUrl = new URL('/admin/export/audit-logs', window.location.origin);
    
    // Add filter parameters to export URL
    if (params.get('action')) exportUrl.searchParams.set('action', params.get('action'));
    if (params.get('user_id')) exportUrl.searchParams.set('user_id', params.get('user_id'));
    if (params.get('date_from')) exportUrl.searchParams.set('date_from', params.get('date_from'));
    if (params.get('date_to')) exportUrl.searchParams.set('date_to', params.get('date_to'));
    
    // Trigger download
    window.location.href = exportUrl.toString();
}

// Auto-refresh logs every 30 seconds
setInterval(() => {
    if (!document.hidden) {
        const url = new URL(window.location);
        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Update only the table content
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTable = doc.querySelector('table tbody');
            const currentTable = document.querySelector('table tbody');
            if (newTable && currentTable) {
                currentTable.innerHTML = newTable.innerHTML;
            }
        })
        .catch(error => {
            console.error('Error refreshing audit logs:', error);
        });
    }
}, 30000);
</script>
@endsection
