@extends('layouts.enhanced-app')

@section('title', 'Notifications - HR Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
                <p class="text-gray-600 mt-1">View your recent notifications and alerts</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('hr.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                </a>
                <button onclick="markAllAsRead()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-check-double mr-2"></i>
                    Mark All as Read
                </button>
            </div>
        </div>
    </div>

    <!-- Notification Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Type</label>
                <select id="typeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Types</option>
                    <option value="leave_application">Leave Applications</option>
                    <option value="leave_approval">Leave Approvals</option>
                    <option value="leave_rejection">Leave Rejections</option>
                    <option value="system">System Notifications</option>
                    <option value="reminder">Reminders</option>
                </select>
            </div>
            <div class="flex-1 min-w-64">
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Status</label>
                <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Status</option>
                    <option value="read">Read</option>
                    <option value="unread">Unread</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900">Recent Notifications</h2>
                <div class="flex space-x-2">
                    @php
                        $unreadCount = $notifications->getCollection()->filter(function ($n) {
                            return isset($n->read_at) ? is_null($n->read_at) : !(bool) ($n->read ?? false);
                        })->count();
                        $readCount = $notifications->count() - $unreadCount;
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $unreadCount }} Unread
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        {{ $readCount }} Read
                    </span>
                </div>
            </div>
        </div>
        
        <div class="divide-y divide-gray-200">
            @forelse($notifications as $notification)
                @php
                    $isRead = isset($notification->read_at) ? !is_null($notification->read_at) : (bool) ($notification->read ?? false);
                @endphp
                <div class="p-6 hover:bg-gray-50 transition-colors duration-200 {{ $isRead ? '' : 'bg-blue-50' }}">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center
                                {{ $notification->type == 'leave_application' ? 'bg-yellow-100' :
                                   ($notification->type == 'leave_approval' ? 'bg-green-100' :
                                   ($notification->type == 'leave_rejection' ? 'bg-red-100' :
                                   ($notification->type == 'system' ? 'bg-blue-100' : 'bg-gray-100'))) }}">
                                <i class="fas 
                                    {{ $notification->type == 'leave_application' ? 'fa-calendar-plus' :
                                       ($notification->type == 'leave_approval' ? 'fa-check-circle' :
                                       ($notification->type == 'leave_rejection' ? 'fa-times-circle' :
                                       ($notification->type == 'system' ? 'fa-cog' : 'fa-bell')) }}
                                    {{ $notification->type == 'leave_application' ? 'text-yellow-600' :
                                       ($notification->type == 'leave_approval' ? 'text-green-600' :
                                       ($notification->type == 'leave_rejection' ? 'text-red-600' :
                                       ($notification->type == 'system' ? 'text-blue-600' : 'text-gray-600')) }}">
                                </i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3 class="text-sm font-medium text-gray-900 {{ $isRead ? '' : 'font-bold' }}">
                                        {{ $notification->title }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-600">{{ $notification->message }}</p>
                                    <div class="mt-2 flex items-center space-x-4">
                                        <span class="text-xs text-gray-500">
                                            {{ $notification->created_at->format('M d, Y h:i A') }}
                                        </span>
                                        @if($isRead)
                                            <span class="text-xs text-green-600">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Read
                                            </span>
                                        @else
                                            <span class="text-xs text-blue-600 font-medium">
                                                <i class="fas fa-circle mr-1"></i>
                                                Unread
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-shrink-0 ml-4">
                                    <div class="flex space-x-2">
                                        @if($isRead)
                                            <button onclick="markAsUnread({{ $notification->id }})" 
                                                    class="text-blue-600 hover:text-blue-800" title="Mark as unread">
                                                <i class="fas fa-envelope"></i>
                                            </button>
                                        @else
                                            <button onclick="markAsRead({{ $notification->id }})" 
                                                    class="text-green-600 hover:text-green-800" title="Mark as read">
                                                <i class="fas fa-envelope-open"></i>
                                            </button>
                                        @endif
                                        <button onclick="deleteNotification({{ $notification->id }})" 
                                                class="text-red-600 hover:text-red-800" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @if($notification->data && isset($notification->data['action_url']))
                                <div class="mt-3">
                                    <a href="{{ $notification->data['action_url'] }}" 
                                       class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                                        View Details
                                        <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <i class="fas fa-bell-slash text-gray-400 text-5xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Notifications</h3>
                    <p class="text-gray-500">You don't have any notifications at this time.</p>
                </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $notifications->links() }}
        </div>
    </div>
</div>

<script>
function markAsRead(id) {
    fetch(`/hr/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function markAsUnread(id) {
    fetch(`/hr/notifications/${id}/unread`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function deleteNotification(id) {
    if (confirm('Are you sure you want to delete this notification?')) {
        fetch(`/hr/notifications/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function markAllAsRead() {
    fetch('/hr/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

// Filter functionality
document.getElementById('typeFilter').addEventListener('change', filterNotifications);
document.getElementById('statusFilter').addEventListener('change', filterNotifications);

function filterNotifications() {
    const type = document.getElementById('typeFilter').value;
    const status = document.getElementById('statusFilter').value;
    
    const params = new URLSearchParams();
    if (type) params.append('type', type);
    if (status) params.append('status', status);
    
    window.location.href = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
}
</script>
@endsection
