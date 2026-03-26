<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Leave Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                    <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Employee Leave Management System
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Streamline your leave management process
                </p>
            </div>
            
            <div class="mt-8 space-y-6">
                <div class="bg-white py-8 px-6 shadow rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Login to Your Account</h3>
                    
                    <div class="space-y-4">
                        <a href="{{ route('employee.login') }}" 
                           class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="fas fa-user mr-2"></i>
                            Employee Login
                        </a>
                        
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white text-gray-500">or</span>
                            </div>
                        </div>
                        
                        <a href="{{ route('login') }}" 
                           class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-user-shield mr-2"></i>
                            Administrator Login
                        </a>
                    </div>
                </div>
                
                <div class="bg-blue-50 py-4 px-6 rounded-lg">
                    <h4 class="text-sm font-medium text-blue-900 mb-2">Default Credentials</h4>
                    <div class="text-xs text-blue-700 space-y-1">
                        <p><strong>Admin:</strong> admin@elms.com / admin123</p>
                        <p><strong>Employee:</strong> john.doe@elms.com / password123</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
