<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Login - ELMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-green-50 via-white to-emerald-50">
    <div class="min-h-screen flex items-center justify-center py-4 sm:py-8 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-sm sm:max-w-md">
            <!-- Header -->
            <div class="text-center mb-6 sm:mb-8 lg:mb-12">
                <div class="mx-auto h-12 w-12 sm:h-16 sm:w-16 lg:h-20 lg:w-20 flex items-center justify-center rounded-full bg-gradient-to-r from-green-600 to-emerald-600 mb-3 sm:mb-4 lg:mb-6 shadow-lg">
                    <i class="fas fa-user text-white text-lg sm:text-xl lg:text-2xl"></i>
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-1 sm:mb-2">Employee Portal</h1>
                <p class="text-sm sm:text-base text-gray-600">Personal Dashboard & Leave Management</p>
            </div>

            <!-- Login Form -->
            <div class="bg-white shadow-xl rounded-2xl p-4 sm:p-6 lg:p-8 hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                <div class="text-center mb-4 sm:mb-6 lg:mb-8">
                    <div class="mx-auto h-10 w-10 sm:h-12 sm:w-12 flex items-center justify-center rounded-full bg-green-100 mb-3 sm:mb-4 shadow-md">
                        <i class="fas fa-user text-green-600 text-lg sm:text-xl"></i>
                    </div>
                    <h2 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 mb-2">Employee Login</h2>
                    <p class="text-xs sm:text-sm text-gray-600">Access your personal dashboard</p>
                </div>

                <form class="space-y-3 sm:space-y-4 lg:space-y-6" method="POST" action="/employee/login">
                    @csrf
                    <input type="hidden" name="login_type" value="employee">
                    
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input id="email" name="email" type="email" required
                                   class="appearance-none rounded-lg relative block w-full pl-10 pr-3 py-2 sm:py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm"
                                   placeholder="employee@company.com" value="david.brown@company.com">
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
                                   class="appearance-none rounded-lg relative block w-full pl-10 pr-3 py-2 sm:py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm"
                                   placeholder="Enter your password" value="password123">
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-900">
                            Remember me
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit"
                                class="group relative w-full flex justify-center py-2 sm:py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 transform hover:scale-105">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-sign-in-alt group-hover:translate-x-1 transition-transform duration-200"></i>
                            </span>
                            Sign In to Employee Portal
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
                    <a href="/" class="text-sm text-green-600 hover:text-green-500 font-medium">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back to Home
                    </a>
                </div>
            </div>

            <!-- Demo Credentials -->
            <div class="mt-6 bg-green-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-green-900 mb-2">Demo Credentials</h4>
                <div class="text-xs text-green-700 space-y-1">
                    <p><strong>Email:</strong> david.brown@company.com</p>
                    <p><strong>Password:</strong> password123</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
