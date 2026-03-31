<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('imart-logo.png') }}">
    <title>@yield('title', 'ELMS - Professional Leave Management System')</title>
    
    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <!-- Custom CSS for Mobile Responsiveness -->
    <style>
        .modal-backdrop.mobile-modal .bg-white {
            margin: 1rem;
            max-width: calc(100vw - 2rem);
            width: 100%;
        }
        
        .modal-backdrop.mobile-modal .flex {
            padding: 0;
        }
        
        @media (max-width: 768px) {
            .modal-backdrop {
                padding: 1rem;
            }
            
            .modal-backdrop .bg-white {
                margin: 0;
                max-width: 100%;
                width: 100%;
                min-height: 100vh;
                border-radius: 0;
            }
            
            .modal-backdrop .flex {
                align-items: stretch;
                padding: 0;
            }
            
            .modal-backdrop .inline-block {
                width: 100%;
                max-width: 100%;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }
        
        /* Enhanced button styles for better mobile experience */
        .sidebar button {
            min-height: 44px; /* Touch-friendly size */
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
        }
        
        .sidebar button:active {
            transform: scale(0.98);
        }
        
        /* Loading state animations */
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
        
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        /* Enhanced hover effects */
        .sidebar button:hover {
            transform: translateX(2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        /* Focus states for accessibility */
        .sidebar button:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }
    </style>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                        success: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        },
                        warning: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                        },
                        danger: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-down': 'slideDown 0.3s ease-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                    }
                }
            }
        }
    </script>
    
    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideDown {
            from { transform: translateY(-10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        @keyframes slideUp {
            from { transform: translateY(10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .sidebar-transition {
            transition: all 0.3s ease-in-out;
        }
        
        .hover-lift {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .notification-badge {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .card-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .card-shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Header -->
    @auth
        <header class="bg-white shadow-sm border-b border-gray-200 fixed top-0 left-0 right-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo and Title -->
                    <div class="flex items-center">
                        <button id="sidebarToggle" class="lg:hidden p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <div class="flex items-center ml-4">
                            <div class="w-10 h-10 bg-white border border-gray-200 rounded-lg flex items-center justify-center overflow-hidden">
                                <img src="{{ asset('imart-logo.png') }}" alt="iMartGroup Logo" class="h-full w-auto max-w-none object-cover object-left" onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'fas fa-calendar-alt text-primary-600 text-sm\'></i>';">
                            </div>
                            <div class="ml-3 leading-tight">
                                <div class="text-base sm:text-lg font-semibold text-gray-900">iMartGroup ELMS</div>
                                <div class="text-xs text-gray-500 hidden sm:block">Enterprise Leave Management</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right side items -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <div class="relative">
                            <button class="p-2 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500">
                                <i class="fas fa-bell text-lg"></i>
                                @php
                                    $unreadCount = \App\Models\SystemNotification::where('user_id', auth()->id())
                                        ->where('is_read', false)
                                        ->count();
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 notification-badge"></span>
                                @endif
                            </button>
                        </div>
                        
                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500">
                                <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                                    <span class="text-primary-600 font-medium text-sm">
                                        {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}
                                    </span>
                                </div>
                                <div class="hidden md:block text-left">
                                    <div class="text-sm font-medium text-gray-900">{{ auth()->user()->full_name }}</div>
                                    <div class="text-xs text-gray-500">{{ auth()->user()->department?->name }}</div>
                                </div>
                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user mr-2"></i>
                                        My Profile
                                    </a>
                                    <a href="{{ $dashboardRoute ?? (in_array(auth()->user()->role, ['super_admin', 'admin']) ? route('admin.dashboard') : (auth()->user()->role == 'hr' ? route('hr.dashboard') : (in_array(auth()->user()->role, ['head_of_department', 'hod']) ? route('hod.dashboard') : route('dashboard')))) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-tachometer-alt mr-2"></i>
                                        Dashboard
                                    </a>
                                    @can('view-leave-applications')
                                        <a href="{{ route('leave-applications.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-calendar-alt mr-2"></i>
                                            My Applications
                                        </a>
                                    @endcan
                                    <div class="border-t border-gray-100"></div>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <i class="fas fa-sign-out-alt mr-2"></i>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed top-16 left-0 bottom-0 w-64 bg-white border-r border-gray-200 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-30">
            <nav class="mt-5 px-2">
                <div class="space-y-1">
                    <!-- Dashboard -->
                    @php
                        $role = auth()->user()->role;
                        $dashboardRoute = in_array($role, ['super_admin', 'admin']) ? route('admin.dashboard')
                            : ($role === 'hr' ? route('hr.dashboard')
                            : (in_array($role, ['head_of_department', 'hod']) ? route('hod.dashboard') : route('dashboard')));
                    @endphp
                    <a href="{{ $dashboardRoute }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dashboard') || request()->routeIs('hod.dashboard') || request()->routeIs('hr.dashboard') || request()->routeIs('admin.dashboard') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-200">
                        <i class="fas fa-tachometer-alt mr-3 text-lg"></i>
                        <span class="flex-1">Dashboard</span>
                    </a>
                    
                    <!-- Leave Management - Available to all authenticated users -->
                    <div class="space-y-1">
                        <div class="px-2 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Leave Management
                        </div>
                        
                        <!-- Employee Leave Options -->
                        @if(in_array(auth()->user()->role, ['employee']))
                            <a href="{{ route('leave-applications.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('leave-applications.index') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-200">
                                <i class="fas fa-calendar-alt mr-3 text-lg"></i>
                                <span class="flex-1">My Applications</span>
                            </a>
                            <a href="{{ route('leave-applications.create') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('leave-applications.create') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-200">
                                <i class="fas fa-plus-circle mr-3 text-lg"></i>
                                <span class="flex-1">Apply Leave</span>
                            </a>
                        @endif

                        @if(in_array(auth()->user()->role, ['head_of_department', 'hod']))
                            <a href="{{ route('leave-applications.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('leave-applications.index') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-200">
                                <i class="fas fa-calendar-alt mr-3 text-lg"></i>
                                <span class="flex-1">Leave View</span>
                            </a>
                        @endif
                        
                        <!-- Management Leave Options -->
                        @if(in_array(auth()->user()->role, ['super_admin', 'admin', 'hr']))
                            <a href="{{ route('leave-applications.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('leave-applications.index') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-200">
                                <i class="fas fa-tasks mr-3 text-lg"></i>
                                <span class="flex-1">Leave Management</span>
                            </a>
                        @endif
                        
                        <!-- Leave Plans - Available to all authenticated users -->
                        <a href="{{ route('leave-plans.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('leave-plans.index') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-200">
                            <i class="fas fa-clipboard-list mr-3 text-lg"></i>
                            <span class="flex-1">Leave Plans</span>
                        </a>
                        
                        <!-- Pending Approval - Only for managers and admins -->
                        @if(in_array(auth()->user()->role, ['super_admin', 'admin', 'hr']))
                            @php
                                $pendingRoute = auth()->user()->role === 'hr' ? route('hr.leave-applications') : route('pending.applications');
                            @endphp
                            <a href="{{ $pendingRoute }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('pending.applications') || request()->routeIs('hr.leave-applications') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-200">
                                <i class="fas fa-clipboard-check mr-3 text-lg"></i>
                                <span class="flex-1">Pending Approval</span>
                                @if(($pendingCount ?? 0) > 0)
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded-full">{{ $pendingCount }}</span>
                                @endif
                            </a>
                        @endif

                        @if(in_array(auth()->user()->role, ['head_of_department', 'hod']))
                            <a href="{{ route('hod.departments') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('hod.departments') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-200">
                                <i class="fas fa-building mr-3 text-lg"></i>
                                <span class="flex-1">Department</span>
                            </a>
                        @endif
                    </div>
                    
                    <!-- User Management - Admin and HR -->
                    @if(in_array(auth()->user()->role, ['super_admin', 'admin', 'hr']))
                        <div class="space-y-1">
                            <div class="px-2 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                User Management
                            </div>
                            @php
                                $employeeMgmtRoute = auth()->user()->role === 'hr' ? route('hr.users') : route('admin.users');
                                $departmentMgmtRoute = auth()->user()->role === 'hr' ? route('hr.departments.manage') : route('admin.departments');
                            @endphp
                            <a href="{{ $employeeMgmtRoute }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.users*') || request()->routeIs('hr.users*') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-200">
                                <i class="fas fa-users mr-3 text-lg"></i>
                                <span class="flex-1">Employees</span>
                            </a>
                            <a href="{{ $departmentMgmtRoute }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.departments*') || request()->routeIs('hr.departments*') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-200">
                                <i class="fas fa-building mr-3 text-lg"></i>
                                <span class="flex-1">Departments</span>
                            </a>
                        </div>
                    @endif
                    
                    <!-- Leave Balance - Available to all authenticated users -->
                    <div class="space-y-1">
                        <div class="px-2 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Leave Balance
                        </div>
                        <button onclick="showLeaveBalance()" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 w-full text-left relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-green-500 to-transparent opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
                            <i class="fas fa-wallet mr-3 text-lg relative z-10"></i>
                            <span class="flex-1 relative z-10">My Balance</span>
                            <i class="fas fa-chevron-right text-xs relative z-10 group-hover:translate-x-1 transition-transform duration-200"></i>
                        </button>
                    </div>
                    
                    <!-- Notifications - Available to all authenticated users -->
                    <div class="space-y-1">
                        <div class="px-2 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Notifications
                        </div>
                        <button onclick="showNotifications()" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 w-full text-left relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-transparent opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
                            <i class="fas fa-bell mr-3 text-lg relative z-10"></i>
                            <span class="flex-1 relative z-10">My Notifications</span>
                            @php
                                $notificationsCount = $notificationsCount ?? $unreadCount;
                            @endphp
                            @if($notificationsCount > 0)
                                <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full animate-pulse relative z-10">
                                    {{ $notificationsCount }}
                                </span>
                            @endif
                            <i class="fas fa-chevron-right text-xs relative z-10 group-hover:translate-x-1 transition-transform duration-200"></i>
                        </button>
                    </div>
                    
                    <!-- Reports - Only for admins and managers -->
                    @if(in_array(auth()->user()->role, ['super_admin', 'admin', 'hr', 'head_of_department', 'hod']))
                        <div class="space-y-1">
                            <div class="px-2 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Reports
                            </div>
                            <a href="{{ route('analytics.leave') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.reports') || request()->routeIs('analytics.*') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-200">
                                <i class="fas fa-chart-bar mr-3 text-lg"></i>
                                <span class="flex-1">Analytics</span>
                            </a>
                            @if(in_array(auth()->user()->role, ['super_admin', 'admin']))
                                <a href="{{ route('admin.audit-logs') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.audit-logs') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-200">
                                    <i class="fas fa-history mr-3 text-lg"></i>
                                    <span class="flex-1">Audit Logs</span>
                                </a>
                            @endif
                            @if(in_array(auth()->user()->role, ['super_admin', 'admin']))
                                <a href="{{ route('admin.settings') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.settings') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-200">
                                    <i class="fas fa-cog mr-3 text-lg"></i>
                                    <span class="flex-1">System Settings</span>
                                </a>
                            @endif
                        </div>
                    @endif
                    
                    <!-- Profile Section -->
                    <div class="border-t border-gray-200 pt-4 mt-4">
                        <div class="px-2 py-2">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-primary-500 flex items-center justify-center">
                                        <span class="text-white font-medium text-sm">{{ substr(auth()->user()->first_name, 0, 1) }}{{ substr(auth()->user()->last_name, 0, 1) }}</span>
                                    </div>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->full_name }}</p>
                                    <p class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</p>
                                </div>
                            </div>
                            <div class="mt-3 space-y-1">
                                <a href="{{ route('profile') }}" class="group flex items-center px-2 py-1 text-xs text-gray-600 hover:text-gray-900 transition-colors duration-200">
                                    <i class="fas fa-user mr-2"></i>
                                    <span>Profile</span>
                                </a>
                                <form action="{{ route('logout') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="group flex items-center px-2 py-1 text-xs text-gray-600 hover:text-gray-900 transition-colors duration-200 w-full text-left">
                                        <i class="fas fa-sign-out-alt mr-2"></i>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </aside>
        
        <!-- Mobile Sidebar Overlay -->
        @auth
            <div id="sidebarOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-50 z-20 lg:hidden hidden"></div>
        @endauth
        
        <!-- Main Content -->
        <main class="lg:pl-64 flex-1 min-h-screen pt-16">
            <div class="py-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>
    @endauth
    
    @php
        $leaveBalances = $leaveBalances ?? (\Illuminate\Support\Facades\Auth::check()
            ? \App\Models\LeaveBalance::where('user_id', auth()->id())
                ->whereHas('leaveType', function ($query) {
                    $query->where('is_active', true)->whereIn('name', ['Annual Leave', 'Sick Leave', 'Maternity Leave', 'Paternity Leave']);
                })
                ->with('leaveType')
                ->get()
            : collect());
    @endphp
    <!-- Leave Balance Modal -->
    @if($leaveBalances->count() > 0)
    <div id="leaveBalanceModal" 
         class="fixed inset-0 z-50 overflow-y-auto modal-backdrop opacity-0" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" onclick="hideLeaveBalance()">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                <i class="fas fa-wallet mr-2"></i>
                                Leave Balance Details
                            </h3>
                            <div class="space-y-3">
                                @foreach($leaveBalances as $balance)
                                    @php
                                        $remaining = max(0, ($balance->balance_days + $balance->carry_over_days) - $balance->used_days);
                                        $allocation = max(1, $balance->balance_days + $balance->carry_over_days);
                                        $percent = min(100, round(($remaining / $allocation) * 100));
                                    @endphp
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex justify-between items-center mb-2">
                                            <h4 class="text-sm font-medium text-gray-900">
                                            {{ $balance->leaveType ? $balance->leaveType->name : 'Leave Type' }}
                                        </h4>
                                            <span class="text-lg font-semibold text-primary-600">
                                                {{ $remaining }} / {{ $allocation }} days
                                            </span>
                                        </div>
                                        <div class="text-xs text-gray-500 mb-2">
                                            Allocated: {{ $allocation }} days | Used: {{ $balance->used_days }} days | Remaining: {{ $remaining }} days
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-3">
                                            <div class="bg-primary-600 h-3 rounded-full transition-all duration-300" style="width: {{ $percent }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="hideLeaveBalance()" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @php
        $notifications = $notifications ?? (\Illuminate\Support\Facades\Auth::check()
            ? \App\Models\SystemNotification::where('user_id', auth()->id())
                ->latest()
                ->take(20)
                ->get()
            : collect());
    @endphp
    <!-- Notifications Modal -->
    @if($notifications->count() > 0 || \Illuminate\Support\Facades\Auth::check())
    <div id="notificationsModal" 
         class="fixed inset-0 z-50 overflow-y-auto modal-backdrop" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" onclick="hideNotifications()">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                <i class="fas fa-bell mr-2"></i>
                                Notifications
                            </h3>
                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                @if($notifications->count() > 0)
                                    @foreach($notifications as $notification)
                                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                                            <div class="flex justify-between items-start mb-2">
                                                <h4 class="text-sm font-medium text-gray-900">{{ $notification->title }}</h4>
                                                <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-sm text-gray-600">{{ $notification->message }}</p>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-8">
                                        <i class="fas fa-bell-slash text-gray-400 text-4xl mb-4"></i>
                                        <p class="text-gray-500">No notifications at this time.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="hideNotifications()" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Scripts -->
    <script>
        // Global functions for sidebar
        function showLeaveBalance() {
            const modal = document.getElementById('leaveBalanceModal');
            if (modal) {
                modal.style.display = 'block';
                setTimeout(() => {
                    modal.classList.add('opacity-100');
                    modal.classList.remove('opacity-0');
                }, 10);
            }
        }

        function hideLeaveBalance() {
            const modal = document.getElementById('leaveBalanceModal');
            if (modal) {
                modal.classList.remove('opacity-100');
                modal.classList.add('opacity-0');
                setTimeout(() => {
                    modal.style.display = 'none';
                }, 300);
            }
        }

        function showNotifications() {
            const modal = document.getElementById('notificationsModal');
            if (modal) {
                modal.style.display = 'block';
                setTimeout(() => {
                    modal.classList.add('opacity-100');
                    modal.classList.remove('opacity-0');
                }, 10);
            }
        }

        function hideNotifications() {
            const modal = document.getElementById('notificationsModal');
            if (modal) {
                modal.classList.remove('opacity-100');
                modal.classList.add('opacity-0');
                setTimeout(() => {
                    modal.style.display = 'none';
                }, 300);
            }
        }

        // Event listeners for custom events
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('showLeaveBalance', function() {
                showLeaveBalance();
            });

            document.addEventListener('showNotifications', function() {
                showNotifications();
            });

            // Close modals on escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    hideNotifications();
                    hideLeaveBalance();
                }
            });

            // Close modals on backdrop click
            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('modal-backdrop')) {
                    hideNotifications();
                    hideLeaveBalance();
                }
            });
        });

        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });
        
        // Close sidebar when clicking overlay
        document.getElementById('sidebarOverlay')?.addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });
        
        // Auto-hide flash messages
        setTimeout(function() {
            const flashMessages = document.querySelectorAll('[class*="bg-green-50"], [class*="bg-red-50"], [class*="bg-yellow-50"]');
            flashMessages.forEach(function(element) {
                element.style.opacity = '0';
                element.style.transform = 'translateY(-10px)';
                setTimeout(function() {
                    element.remove();
                }, 300);
            });
        }, 5000);
        
        // Initialize tooltips and other UI components
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth scrolling
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            });
            
            // Add loading states to forms
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    const submitButton = form.querySelector('button[type="submit"]');
                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
                    }
                });
            });
        });
    </script>
    
    <!-- Alpine.js for Modal Management -->
    <script>
        // Global modal state
        window.showNotificationsModal = false;
        window.showLeaveBalanceModal = false;
        
        // Show notifications modal
        function showNotificationsModal() {
            const modal = document.getElementById('notificationsModal');
            if (modal) {
                // Add loading state
                const button = event.target.closest('button');
                const originalContent = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-3 text-lg"></i><span class="flex-1">Loading...</span><i class="fas fa-chevron-right text-xs"></i>';
                button.disabled = true;
                
                modal.style.display = 'block';
                setTimeout(() => {
                    modal.classList.add('opacity-100');
                    modal.classList.remove('opacity-0');
                    // Restore button state
                    button.innerHTML = originalContent;
                    button.disabled = false;
                }, 300);
            }
        }
        
        // Hide notifications modal
        function hideNotificationsModal() {
            const modal = document.getElementById('notificationsModal');
            if (modal) {
                modal.classList.remove('opacity-100');
                modal.classList.add('opacity-0');
                setTimeout(() => {
                    modal.style.display = 'none';
                }, 300);
            }
        }
        
        // Show leave balance modal
        function showLeaveBalanceModal() {
            const modal = document.getElementById('leaveBalanceModal');
            if (modal) {
                // Add loading state
                const button = event.target.closest('button');
                const originalContent = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-3 text-lg"></i><span class="flex-1">Loading...</span><i class="fas fa-chevron-right text-xs"></i>';
                button.disabled = true;
                
                modal.style.display = 'block';
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    modal.classList.add('opacity-100');
                    // Restore button state
                    button.innerHTML = originalContent;
                    button.disabled = false;
                }, 300);
            }
        }
        
        // Close modals on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                hideNotificationsModal();
                hideLeaveBalanceModal();
            }
        });
        
        // Close modals on backdrop click
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal-backdrop')) {
                hideNotificationsModal();
                hideLeaveBalanceModal();
            }
        });
        
        // Mobile touch support
        if ('ontouchstart' in window) {
            // Add touch feedback for mobile devices
            document.addEventListener('touchstart', function(e) {
                const button = e.target.closest('button');
                if (button && button.onclick) {
                    button.style.transform = 'scale(0.95)';
                    button.style.transition = 'transform 0.1s';
                }
            });
            
            document.addEventListener('touchend', function(e) {
                const button = e.target.closest('button');
                if (button && button.onclick) {
                    button.style.transform = 'scale(1)';
                }
            });
        }
        
        // Responsive modal positioning for mobile
        function adjustModalForMobile() {
            const isMobile = window.innerWidth < 768;
            const modals = document.querySelectorAll('.modal-backdrop');
            
            modals.forEach(modal => {
                if (isMobile) {
                    modal.classList.add('mobile-modal');
                } else {
                    modal.classList.remove('mobile-modal');
                }
            });
        }
        
        // Adjust modals on resize
        window.addEventListener('resize', adjustModalForMobile);
        adjustModalForMobile();
    </script>
    
    @stack('scripts')
</body>
</html>
