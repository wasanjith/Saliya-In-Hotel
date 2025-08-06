<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Saliya Inn Restaurant</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .login-bg {
            background-image: url('/images/background.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .food-bowl {
            position: absolute;
            width: 150px;
            height: 150px;
            background: linear-gradient(135deg, #20bf6b, #0fb9b1);
            border-radius: 50%;
            opacity: 0.6;
            z-index: -1;
        }
        .food-bowl::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 15px;
            right: 15px;
            bottom: 15px;
            background: linear-gradient(135deg, #ff6b6b, #feca57);
            border-radius: 50%;
        }
    </style>
</head>
<body class="login-bg min-h-screen flex items-center justify-center py-6 px-4 sm:px-6 lg:px-8 relative">
    <!-- Background Food Bowls -->
    <div class="food-bowl top-5 left-5"></div>
    <div class="food-bowl bottom-5 right-5"></div>
    <div class="food-bowl top-1/2 right-10"></div>
    
    <div class="max-w-md w-full space-y-4 relative z-10">
        <!-- Logo and Title -->
        <div class="text-center">
            <div class="flex justify-center mb-3">
                <img src="/images/logoo.saliya.png" alt="Saliya Inn Logo" class="w-24 h-24 object-contain drop-shadow-2xl">
            </div>
            <h2 class="text-2xl font-bold text-white mb-1 drop-shadow-2xl">SALIYA INN</h2>
            <p class="text-white/90 text-lg font-semibold">RESTAURANT</p>
            <p class="text-white/80 text-sm">Point of Sale System</p>
        </div>

        <!-- Login Form -->
        <div class="glass-effect rounded-2xl p-6 shadow-2xl">
            <div class="text-center mb-4">
                <h3 class="text-2xl font-bold text-gray-800 mb-1">Login</h3>
                <p class="text-gray-600 text-sm">More than <span class="text-red-500 font-bold">15,000 recipes</span> from around the world!</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex">
                        <i class="fas fa-exclamation-circle text-red-400 mt-0.5 mr-2"></i>
                        <div>
                            <h4 class="text-sm font-medium text-red-800">Login Error</h4>
                            <div class="mt-1 text-sm text-red-700">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form class="space-y-4" method="POST" action="{{ route('login') }}">
                @csrf
                
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required
                               value="{{ old('email') }}"
                               class="appearance-none relative block w-full pl-10 pr-3 py-2.5 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 focus:z-10 sm:text-sm bg-white/80"
                               placeholder="Enter your email">
                    </div>
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               class="appearance-none relative block w-full pl-10 pr-10 py-2.5 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 focus:z-10 sm:text-sm bg-white/80"
                               placeholder="Enter your password">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="togglePassword()">
                                <i class="fas fa-eye" id="password-toggle"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Remember Me and Forgot Password -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox" 
                               class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>
                    <div class="text-sm">
                        <a href="#" class="font-medium text-orange-600 hover:text-orange-500">
                            Forgot Password?
                        </a>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" 
                            class="group relative w-full flex justify-center py-2.5 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200 shadow-lg">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-sign-in-alt text-orange-300 group-hover:text-orange-200"></i>
                        </span>
                        LOGIN
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center">
            <div class="flex justify-center space-x-4 text-xs text-white/80">
                <a href="#" class="hover:text-white transition-colors duration-200">Explore</a>
                <a href="#" class="hover:text-white transition-colors duration-200">What</a>
                <a href="#" class="hover:text-white transition-colors duration-200">Help & feedback</a>
                <a href="#" class="hover:text-white transition-colors duration-200">Contact</a>
            </div>
            <p class="text-white/60 text-xs mt-2">
                © 2024 Saliya Inn Restaurant. All rights reserved.
            </p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('password-toggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.classList.remove('fa-eye');
                passwordToggle.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordToggle.classList.remove('fa-eye-slash');
                passwordToggle.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html> 