<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('imart-logo.png') }}">
    <title>iMartGroup Leave System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl w-full space-y-8">
            <!-- Header -->
            <div>
                <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-gradient-to-r from-blue-600 to-purple-600 mb-6 shadow-lg overflow-hidden">
                    <img src="{{ asset('imart-logo.png') }}" alt="ELMS Logo" class="h-full w-auto max-w-none object-cover object-left" onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'fas fa-calendar-alt text-white text-2xl\'></i>';">
                </div>
                <h2 class="mt-6 text-center text-4xl font-extrabold text-gray-900">
                    iMartGroup Leave System
                </h2>
                <p class="mt-2 text-center text-lg text-gray-600">
                    Streamline your leave management process
                </p>
            </div>
            
            <!-- Login Options -->
            <div class="mt-8 space-y-6">
                <div class="bg-white py-8 px-6 shadow-xl rounded-2xl">
                    <h3 class="text-xl font-medium text-gray-900 mb-6">Choose Your Login Portal</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Employee Login Card -->
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-6 hover:shadow-2xl transition-all duration-300 transform hover:scale-105 border border border-green-200">
                            <div class="text-center mb-4">
                                <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-green-600 mb-4 shadow-md">
                                    <i class="fas fa-user text-white text-xl"></i>
                                </div>
                                <h4 class="text-lg font-bold text-green-900 mb-2">Employee Portal</h4>
                            </div>
                            
                            <a href="/employee/login" 
                               class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Employee Login
                            </a>
                        </div>
                        
                        <!-- Administrator Login Card -->
                        <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl p-6 hover:shadow-2xl transition-all duration-300 transform hover:scale-105 border border border-purple-200">
                            <div class="text-center mb-4">
                                <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-purple-600 mb-4 shadow-md">
                                    <i class="fas fa-user-shield text-white text-xl"></i>
                                </div>
                                <h4 class="text-lg font-bold text-purple-900 mb-2">Administrator Portal</h4>
                                <p class="text-sm text-purple-700 mb-4">System Management & Administration</p>
                            
                            </div>
                            
                            <a href="/login" 
                               class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-shield-alt mr-2"></i>
                                Administrator Login
                            </a>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
