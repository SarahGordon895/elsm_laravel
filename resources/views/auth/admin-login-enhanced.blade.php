<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Login - ELMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-purple-50 via-white to-indigo-50">
    <div class="min-h-screen flex items-center justify-center py-4 sm:py-8 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-sm sm:max-w-md">
            <!-- Header -->
            <div class="text-center mb-6 sm:mb-8 lg:mb-12">
                <div class="mx-auto h-12 w-12 sm:h-16 sm:w-16 lg:h-20 lg:w-20 flex items-center justify-center rounded-full bg-gradient-to-r from-purple-600 to-indigo-600 mb-3 sm:mb-4 lg:mb-6 shadow-lg">
                    <i class="fas fa-shield-alt text-white text-lg sm:text-xl lg:text-2xl"></i>
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-1 sm:mb-2">Administrator Portal</h1>
                <p class="text-sm sm:text-base text-gray-600">Administrator Access</p>
            </div>

            <!-- Employee Features -->
            <div class="bg-white shadow-xl rounded-2xl p-4 sm:p-6 lg:p-8 mb-6">
                <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 mb-4">Select Your Role</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <!-- Super Admin Option -->
                    <div class="text-center p-4 bg-red-50 rounded-lg border border-red-200 hover:bg-red-100 transition-colors cursor-pointer" onclick="selectRole('super_admin')">
                        <div class="mx-auto h-10 w-10 flex items-center justify-center rounded-full bg-red-600 mb-3">
                            <i class="fas fa-crown text-white"></i>
                        </div>
                        <h4 class="text-sm font-bold text-red-900">Super Admin</h4>
                    </div>
                    
                    <!-- Admin Option -->
                    <div class="text-center p-4 bg-orange-50 rounded-lg border border-orange-200 hover:bg-orange-100 transition-colors cursor-pointer" onclick="selectRole('admin')">
                        <div class="mx-auto h-10 w-10 flex items-center justify-center rounded-full bg-orange-600 mb-3">
                            <i class="fas fa-user-cog text-white"></i>
                        </div>
                        <h4 class="text-sm font-bold text-orange-900">Admin</h4>
                    </div>
                    
                    <!-- HR Option -->
                    <div class="text-center p-4 bg-blue-50 rounded-lg border border-blue-200 hover:bg-blue-100 transition-colors cursor-pointer" onclick="selectRole('hr')">
                        <div class="mx-auto h-10 w-10 flex items-center justify-center rounded-full bg-blue-600 mb-3">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <h4 class="text-sm font-bold text-blue-900">HR</h4>
                    </div>
                    
                    <!-- Head of Department Option -->
                    <div class="text-center p-4 bg-indigo-50 rounded-lg border border-indigo-200 hover:bg-indigo-100 transition-colors cursor-pointer" onclick="selectRole('hod')">
                        <div class="mx-auto h-10 w-10 flex items-center justify-center rounded-full bg-indigo-600 mb-3">
                            <i class="fas fa-user-tie text-white"></i>
                        </div>
                        <h4 class="text-sm font-bold text-indigo-900">Head of Department</h4>
                    </div>
                </div>
            </div>

            <!-- Login Form -->
            <div class="bg-white shadow-xl rounded-2xl p-4 sm:p-6 lg:p-8 hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                <div class="text-center mb-4 sm:mb-6 lg:mb-8">
                    <div class="mx-auto h-10 w-10 sm:h-12 sm:w-12 flex items-center justify-center rounded-full bg-purple-100 mb-3 sm:mb-4 shadow-md">
                        <i class="fas fa-user-shield text-purple-600 text-lg sm:text-xl"></i>
                    </div>
                    <h2 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 mb-2">Administrator Login</h2>
                    <p class="text-xs sm:text-sm text-gray-600">Enter your administrator credentials</p>
                </div>

                <form class="space-y-3 sm:space-y-4 lg:space-y-6" method="POST" action="/login">
                    @csrf
                    <input type="hidden" name="login_type" value="admin">
                    <input type="hidden" name="selected_role" id="selected_role" value="hr">
                    
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input id="email" name="email" type="email" required
                                   class="appearance-none rounded-lg relative block w-full pl-10 pr-3 py-2 sm:py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm"
                                   placeholder="admin@company.com">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="password" name="password" type="password" required
                                   class="appearance-none rounded-lg relative block w-full pl-10 pr-3 py-2 sm:py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm"
                                   placeholder="Enter your password">
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-900">
                            Remember me
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit"
                                class="group relative w-full flex justify-center py-2 sm:py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 transform hover:scale-105">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-sign-in-alt group-hover:translate-x-1 transition-transform duration-200"></i>
                            </span>
                            Login as <span id="role-display">HR</span>
                        </button>
                    </div>

                    <!-- Error Display -->
                    @if(session('error'))
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('status'))
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-green-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-800">{{ session('status') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </form>

                <!-- Back to Home -->
                <div class="mt-6 text-center">
                    <a href="/" class="text-sm text-purple-600 hover:text-purple-500 font-medium">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back to Home
                    </a>
                </div>
            </div>

            <!-- Demo Credentials -->
            <div class="mt-6 bg-purple-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-purple-900 mb-2">Demo Credentials</h4>
                <div class="text-xs text-purple-700 space-y-1">
                    <p><strong>Super Admin:</strong> system@elsm.com / password</p>
                    <p><strong>Admin:</strong> developers@imartgroup.co.tz / password</p>
                    <p><strong>HR:</strong> a.gonda@imartgroup.co.tz / password</p>
                    <p><strong>Head of Department:</strong> m.gadi@imartgroup.co.tz / password</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function selectRole(role) {
            document.getElementById('selected_role').value = role;
            
            // Update button text
            const roleDisplay = document.getElementById('role-display');
            const roleNames = {
                'super_admin': 'Super Admin',
                'admin': 'Admin', 
                'hr': 'HR',
                'hod': 'Head of Department'
            };
            roleDisplay.textContent = roleNames[role] || 'Administrator';
            
            // Update form styling based on role
            const form = document.querySelector('form');
            form.className = form.className.replace(/from-\w+-\d+/, `from-${roleColor(role)}-600`);
            
            // Highlight selected role card
            document.querySelectorAll('.cursor-pointer').forEach(card => {
                card.classList.remove('ring-2', 'ring-offset-2', 'border-opacity-100');
            });
            event.target.closest('.cursor-pointer').classList.add('ring-2', 'ring-offset-2', 'border-opacity-100');
        }
        
        function roleColor(role) {
            const colors = {
                'super_admin': 'red',
                'admin': 'orange',
                'hr': 'blue', 
                'hod': 'indigo'
            };
            return colors[role] || 'purple';
        }
        
        // Initialize with HR as default
        document.addEventListener('DOMContentLoaded', function() {
            selectRole('hr');
        });
    </script>
</body>
</html>
