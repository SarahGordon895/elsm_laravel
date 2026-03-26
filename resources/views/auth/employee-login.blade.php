@extends('layouts.enhanced-app')

@section('title', 'Employee Login - ELMS')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-emerald-50 flex items-center justify-center py-4 sm:py-8 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-sm sm:max-w-md">
        <!-- Responsive Header -->
        <div class="text-center mb-6 sm:mb-8 lg:mb-12">
            <div class="mx-auto h-12 w-12 sm:h-16 sm:w-16 lg:h-20 lg:w-20 flex items-center justify-center rounded-full bg-gradient-to-r from-green-600 to-emerald-600 mb-3 sm:mb-4 lg:mb-6 shadow-lg">
                <i class="fas fa-user text-white text-lg sm:text-xl lg:text-2xl"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-1 sm:mb-2">Employee Portal</h1>
            <p class="text-sm sm:text-base text-gray-600">Personal Dashboard & Leave Management</p>
        </div>

        <!-- Employee Login Form -->
        <div class="bg-white shadow-xl rounded-2xl p-4 sm:p-6 lg:p-8 hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
            <div class="text-center mb-4 sm:mb-6 lg:mb-8">
                <div class="mx-auto h-10 w-10 sm:h-12 sm:w-12 flex items-center justify-center rounded-full bg-green-100 mb-3 sm:mb-4 shadow-md">
                    <i class="fas fa-user text-green-600 text-lg sm:text-xl"></i>
                </div>
                <h2 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 mb-2">Employee Login</h2>
                <p class="text-xs sm:text-sm text-gray-600">Access your personal dashboard</p>
            </div>

            <form class="space-y-3 sm:space-y-4 lg:space-y-6" method="POST" action="{{ route('employee.login.store') }}" x-data="{ 
                loading: false,
                validateForm() {
                    const email = this.$el.querySelector('input[type="email"]');
                    const password = this.$el.querySelector('input[type="password"]');
                    return email.value && password.value;
                }
            }">
                @csrf
                <input type="hidden" name="login_type" value="employee">
                
                <div class="space-y-3 sm:space-y-4">
                    <div>
                        <label for="employee_email" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-2 sm:pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400 text-sm sm:text-base"></i>
                            </div>
                            <input id="employee_email" name="email" type="email" autocomplete="email" required 
                                   class="appearance-none relative block w-full pl-8 sm:pl-10 pr-3 sm:pr-4 py-2 sm:py-3 border border-gray-300 rounded-lg placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-xs sm:text-sm lg:text-base transition-all duration-200" 
                                   placeholder="employee@company.com" value="{{ old('email') }}"
                                   @input="$el.classList.remove('border-red-500')"
                                   x-on:blur="$el.value ? $el.classList.add('border-green-500') : $el.classList.remove('border-green-500')">
                        </div>
                    </div>
                    
                    <div>
                        <label for="employee_password" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-2 sm:pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 text-sm sm:text-base"></i>
                            </div>
                            <input id="employee_password" name="password" type="password" autocomplete="current-password" required 
                                   class="appearance-none relative block w-full pl-8 sm:pl-10 pr-10 sm:pr-12 py-2 sm:py-3 border border-gray-300 rounded-lg placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-xs sm:text-sm lg:text-base transition-all duration-200" 
                                   placeholder="Enter your password"
                                   @input="$el.classList.remove('border-red-500')"
                                   x-on:blur="$el.value ? $el.classList.add('border-green-500') : $el.classList.remove('border-green-500')">
                            <button type="button" @click="$el.type = $el.type === 'password' ? 'text' : 'password'" 
                                    class="absolute inset-y-0 right-0 pr-2 sm:pr-3 flex items-center">
                                <i x-show="$el.previousElementSibling.type === 'password'" class="fas fa-eye text-gray-400 hover:text-gray-600 text-sm sm:text-base"></i>
                                <i x-show="$el.previousElementSibling.type === 'text'" class="fas fa-eye-slash text-gray-400 hover:text-gray-600 text-sm sm:text-base"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-2 sm:space-y-0">
                    <div class="flex items-center">
                        <input id="employee_remember" name="remember" type="checkbox" 
                               class="h-3 w-3 sm:h-4 sm:w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label for="employee_remember" class="ml-2 block text-xs sm:text-sm text-gray-900">
                            Remember me
                        </label>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs sm:text-sm text-green-600 hover:text-green-500 transition-colors duration-200">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <div class="pt-2 sm:pt-4">
                    <button type="submit" 
                            :disabled="loading"
                            @click="loading = true; if (!validateForm()) { $el.closest('form').querySelector('input[name=email]').classList.add('border-red-500'); $el.closest('form').querySelector('input[name=password]').classList.add('border-red-500'); loading = false; $event.preventDefault(); }"
                            class="w-full flex justify-center py-2 sm:py-3 lg:py-4 px-4 border border-transparent text-xs sm:text-sm lg:text-base font-medium rounded-lg text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed">
                        <template x-if="!loading">
                            <span><i class="fas fa-sign-in-alt mr-2"></i> Employee Login</span>
                        </template>
                        <template x-if="loading">
                            <span><i class="fas fa-spinner fa-spin mr-2"></i> Signing in...</span>
                        </template>
                    </button>
                </div>
            </form>

            <!-- Employee Role Badge -->
            <div class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-gray-200">
                <div class="flex items-center justify-center">
                    <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-user-circle mr-1 sm:mr-2"></i>
                        Employee Access Level
                    </span>
                </div>
            </div>
        </div>

        <!-- Administrator Login Link -->
        <div class="mt-4 sm:mt-6 text-center">
            <p class="text-xs sm:text-sm text-gray-600">
                Are you an administrator? 
                <a href="{{ route('login') }}" class="text-purple-600 hover:text-purple-500 font-medium transition-colors duration-200">
                    Click here for Administrator Login
                </a>
            </p>
        </div>
    </div>
</div>

<!-- Responsive Error Messages -->
@if ($errors->any())
    <div class="fixed top-4 right-4 z-50 max-w-xs sm:max-w-md w-full" x-data="{ show: true }" x-show="show" x-transition>
        <div class="bg-red-50 border border-red-200 text-red-800 px-3 sm:px-4 py-2 sm:py-3 rounded-lg shadow-xl backdrop-blur">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400 text-sm sm:text-base"></i>
                </div>
                <div class="ml-2 sm:ml-3 flex-1">
                    <h3 class="text-xs sm:text-sm font-medium text-red-800">Login Failed</h3>
                    <div class="mt-1 text-xs sm:text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button @click="show = false" class="ml-2 text-red-400 hover:text-red-600">
                    <i class="fas fa-times text-xs sm:text-sm"></i>
                </button>
            </div>
        </div>
    </div>
@endif

@if (session('status'))
    <div class="fixed top-4 right-4 z-50 max-w-xs sm:max-w-md w-full" x-data="{ show: true }" x-show="show" x-transition>
        <div class="bg-green-50 border border-green-200 text-green-800 px-3 sm:px-4 py-2 sm:py-3 rounded-lg shadow-xl backdrop-blur">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400 text-sm sm:text-base"></i>
                </div>
                <div class="ml-2 sm:ml-3 flex-1">
                    <h3 class="text-xs sm:text-sm font-medium text-green-800">Success</h3>
                    <div class="mt-1 text-xs sm:text-sm text-green-700">
                        {{ session('status') }}
                    </div>
                </div>
                <button @click="show = false" class="ml-2 text-green-400 hover:text-green-600">
                    <i class="fas fa-times text-xs sm:text-sm"></i>
                </button>
            </div>
        </div>
    </div>
@endif

<script>
// Enhanced responsive functionality
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus email field
    const emailField = document.getElementById('employee_email');
    if (emailField) {
        emailField.focus();
    }

    // Handle responsive viewport
    function handleResponsive() {
        const width = window.innerWidth;
        const forms = document.querySelectorAll('.bg-white');
        
        if (width < 640) {
            // Mobile optimizations
            forms.forEach(form => {
                form.classList.add('mobile-optimized');
            });
        } else {
            // Desktop optimizations
            forms.forEach(form => {
                form.classList.remove('mobile-optimized');
            });
        }
    }

    // Initialize responsive handling
    window.addEventListener('resize', handleResponsive);
    handleResponsive();

    // Auto-clear messages after 5 seconds
    setTimeout(() => {
        const errorDivs = document.querySelectorAll('.bg-red-50');
        const successDivs = document.querySelectorAll('.bg-green-50');
        
        errorDivs.forEach(div => div.remove());
        successDivs.forEach(div => div.remove());
    }, 5000);
});
</script>
@endsection
