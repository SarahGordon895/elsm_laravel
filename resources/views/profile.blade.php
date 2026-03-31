@extends('layouts.enhanced-app')

@section('title', 'My Profile - ELMS')

@section('content')
@php /** @var \App\Models\User $user */ @endphp
<div class="max-w-4xl mx-auto" x-data="{ activeTab: 'personal' }">
    <!-- Page Header -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">My Profile</h1>
                <p class="mt-1 text-sm text-gray-500">Manage your personal information and account settings</p>
            </div>
            <div class="flex items-center space-x-3">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center">
                    <span class="text-primary-600 text-xl font-bold">
                        {{ strtoupper(substr($user->first_name, 0, 1)) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 animate-fade-in">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400 text-lg"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4 animate-fade-in">
            <p class="text-sm font-medium text-red-800 mb-2">Please fix the following:</p>
            <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Profile Tabs -->
    <div class="bg-white shadow rounded-lg">
        <div class="border-b border-gray-200">
            <nav class="flex flex-wrap -mb-px">
                <button @click="activeTab = 'personal'" 
                        :class="activeTab === 'personal' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-6 border-b-2 font-medium text-sm focus:outline-none transition-colors duration-200">
                    <i class="fas fa-user mr-2"></i>
                    Personal Information
                </button>
                <button @click="activeTab = 'employment'"
                        :class="activeTab === 'employment' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-6 border-b-2 font-medium text-sm focus:outline-none transition-colors duration-200">
                    <i class="fas fa-briefcase mr-2"></i>
                    Employment Details
                </button>
                <button @click="activeTab = 'security'"
                        :class="activeTab === 'security' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-6 border-b-2 font-medium text-sm focus:outline-none transition-colors duration-200">
                    <i class="fas fa-lock mr-2"></i>
                    Security
                </button>
                <button @click="activeTab = 'notifications'"
                        :class="activeTab === 'notifications' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-6 border-b-2 font-medium text-sm focus:outline-none transition-colors duration-200">
                    <i class="fas fa-bell mr-2"></i>
                    Notifications
                </button>
            </nav>
        </div>

        <!-- Personal Information Tab -->
        <div x-show="activeTab === 'personal'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <form action="{{ route('profile.update') }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="first_name" name="first_name" required
                               value="{{ old('first_name', $user->first_name) }}"
                               class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="last_name" name="last_name" required
                               value="{{ old('last_name', $user->last_name) }}"
                               class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" required
                               value="{{ old('email', $user->email) }}"
                               class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                    </div>

                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="tel" id="phone_number" name="phone_number"
                               value="{{ old('phone_number', $user->phone_number) }}"
                               class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                    </div>

                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                        <input type="date" id="date_of_birth" name="date_of_birth"
                               value="{{ old('date_of_birth', $user->date_of_birth) }}"
                               class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                    </div>

                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                        <select id="gender" name="gender" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea id="address" name="address" rows="3"
                              class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">{{ old('address', $user->address) }}</textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            class="bg-primary-600 text-white px-6 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Employment Details Tab -->
        <div x-show="activeTab === 'employment'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="p-6 space-y-6">
                <!-- Employee Information -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Employee Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Employee ID</p>
                            <p class="text-lg font-medium text-gray-900">{{ $user->employee_id }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Department</p>
                            <p class="text-lg font-medium text-gray-900">{{ $user->department?->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Role</p>
                            <p class="text-lg font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $user->getEffectiveRole())) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Employment Type</p>
                            <p class="text-lg font-medium text-gray-900">{{ ucfirst($user->employment_type) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Hire Date</p>
                            <p class="text-lg font-medium text-gray-900">{{ $user->join_date ? \Illuminate\Support\Carbon::parse($user->join_date)->format('M d, Y') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Account Status</p>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $user->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Leave Balance Summary -->
                <div class="bg-blue-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-blue-900 mb-4">Leave Balance Summary</h3>
                    @if($leaveBalances->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($leaveBalances as $balance)
                                <div class="bg-white rounded-lg p-4 border border-blue-200 hover:shadow-md transition-shadow duration-200">
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
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-calendar-times text-gray-400 text-3xl mb-2"></i>
                            <p class="text-gray-500">No leave balances found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Security Tab -->
        <div x-show="activeTab === 'security'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="p-6 space-y-6">
                <!-- Change Password -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Change Password</h3>
                    <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-4" x-data="{ showPassword: false, showConfirmPassword: false }">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700">
                                Current Password <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative">
                                <input :type="showPassword ? 'text' : 'password'" id="current_password" name="current_password" required
                                       class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 px-3 flex items-center">
                                    <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-gray-400"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                New Password <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative">
                                <input :type="showConfirmPassword ? 'text' : 'password'" id="password" name="password" required
                                       class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                                <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute inset-y-0 right-0 px-3 flex items-center">
                                    <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-gray-400"></i>
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Must be at least 8 characters</p>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                Confirm New Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                   class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="bg-primary-600 text-white px-6 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors duration-200">
                                <i class="fas fa-lock mr-2"></i>
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Account Security Info -->
                <div class="bg-yellow-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-yellow-900 mb-4">Security Tips</h3>
                    <ul class="space-y-2 text-sm text-yellow-800">
                        <li class="flex items-start">
                            <i class="fas fa-shield-alt mt-1 mr-2 flex-shrink-0"></i>
                            <span>Use a strong password with at least 8 characters, including uppercase, lowercase, numbers, and symbols.</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-key mt-1 mr-2 flex-shrink-0"></i>
                            <span>Never share your password with anyone or write it down in an insecure location.</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-sync mt-1 mr-2 flex-shrink-0"></i>
                            <span>Change your password regularly to maintain account security.</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-sign-out-alt mt-1 mr-2 flex-shrink-0"></i>
                            <span>Always log out when using shared or public computers.</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-blue-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-blue-900 mb-4">Profile Activity</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-blue-800">Last profile update</span>
                            <span class="font-medium text-blue-900">{{ $profileUpdatedAt ? \Illuminate\Support\Carbon::parse($profileUpdatedAt)->diffForHumans() : 'No updates yet' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-blue-800">Last password change</span>
                            <span class="font-medium text-blue-900">{{ $passwordChangedAt ? \Illuminate\Support\Carbon::parse($passwordChangedAt)->diffForHumans() : 'Not changed yet' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications Tab -->
        <div x-show="activeTab === 'notifications'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="p-6 space-y-6">
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Notification Preferences</h3>
                    <p class="text-sm text-gray-600 mb-4">Choose how you want to receive system events like leave updates and management actions.</p>

                    <form action="{{ route('profile.notifications.update') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <label class="flex items-start gap-3 p-3 bg-white rounded-md border border-gray-200">
                            <input type="checkbox" name="notify_system" value="1" class="mt-1 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                   {{ (($user->notify_system ?? true) ? 'checked' : '') }}>
                            <span>
                                <span class="block text-sm font-medium text-gray-800">In-app notifications</span>
                                <span class="block text-xs text-gray-500">Show notifications inside your dashboard.</span>
                            </span>
                        </label>

                        <label class="flex items-start gap-3 p-3 bg-white rounded-md border border-gray-200">
                            <input type="checkbox" name="notify_email" value="1" class="mt-1 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                   {{ (($user->notify_email ?? true) ? 'checked' : '') }}>
                            <span>
                                <span class="block text-sm font-medium text-gray-800">Email notifications</span>
                                <span class="block text-xs text-gray-500">Receive event updates by email.</span>
                            </span>
                        </label>

                        <label class="flex items-start gap-3 p-3 bg-white rounded-md border border-gray-200">
                            <input type="checkbox" name="notify_sms" value="1" class="mt-1 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                   {{ (($user->notify_sms ?? true) ? 'checked' : '') }}>
                            <span>
                                <span class="block text-sm font-medium text-gray-800">SMS notifications</span>
                                <span class="block text-xs text-gray-500">Receive urgent event updates by text message.</span>
                            </span>
                        </label>

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="bg-primary-600 text-white px-6 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors duration-200">
                                <i class="fas fa-save mr-2"></i>
                                Save Preferences
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-lg p-6 border border-gray-200">
                    <h4 class="text-base font-semibold text-gray-900 mb-2">Per Event Preferences</h4>
                    <p class="text-sm text-gray-600 mb-4">Override channels for specific events.</p>

                    <form action="{{ route('profile.notifications.events.update') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-600 border-b">
                                        <th class="py-2 pr-3">Event</th>
                                        <th class="py-2 px-3">In-app</th>
                                        <th class="py-2 px-3">Email</th>
                                        <th class="py-2 px-3">SMS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notificationEventLabels as $eventType => $eventLabel)
                                        @php
                                            $preference = $notificationPreferences[$eventType] ?? null;
                                            $prefixed = str_replace('.', '_', $eventType);
                                        @endphp
                                        <tr class="border-b last:border-b-0">
                                            <td class="py-2 pr-3 text-gray-800">{{ $eventLabel }}</td>
                                            <td class="py-2 px-3">
                                                <input type="checkbox"
                                                       name="event_notify_system_{{ $prefixed }}"
                                                       value="1"
                                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                                       {{ ($preference?->notify_system ?? ($user->notify_system ?? true)) ? 'checked' : '' }}>
                                            </td>
                                            <td class="py-2 px-3">
                                                <input type="checkbox"
                                                       name="event_notify_email_{{ $prefixed }}"
                                                       value="1"
                                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                                       {{ ($preference?->notify_email ?? ($user->notify_email ?? true)) ? 'checked' : '' }}>
                                            </td>
                                            <td class="py-2 px-3">
                                                <input type="checkbox"
                                                       name="event_notify_sms_{{ $prefixed }}"
                                                       value="1"
                                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                                       {{ ($preference?->notify_sms ?? ($user->notify_sms ?? true)) ? 'checked' : '' }}>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="bg-primary-600 text-white px-6 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors duration-200">
                                <i class="fas fa-save mr-2"></i>
                                Save Event Preferences
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
