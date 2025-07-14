<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'MoneyTransfer') }} - @yield('title', 'Transfert d\'argent rapide et sécurisé')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700|inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                            950: '#042f2e',
                        },
                        secondary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                            950: '#1e1b4b',
                        },
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'bounce-slow': 'bounce 3s infinite',
                        'gradient': 'gradient 8s ease infinite',
                    },
                    keyframes: {
                        gradient: {
                            '0%, 100%': {
                                'background-position': '0% 50%'
                            },
                            '50%': {
                                'background-position': '100% 50%'
                            },
                        }
                    },
                    backgroundSize: {
                        'size-200': '200% 200%',
                    },
                    boxShadow: {
                        'glow': '0 0 20px rgba(20, 184, 166, 0.3)',
                        'card': '0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.03)',
                        'card-hover': '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
                    }
                }
            }
        }
    </script>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#14b8a6">
    <link rel="manifest" href="/manifest.json">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: #14b8a6;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #0d9488;
        }
        
        /* Smooth Transitions */
        .page-transition {
            transition: all 0.3s ease-in-out;
        }
        
        /* Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        
        /* Gradient Animation */
        .animate-gradient {
            background-size: 200% 200%;
            animation: gradient 8s ease infinite;
        }
        
        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        
        /* Notification Animation */
        @keyframes slideIn {
            0% {
                transform: translateX(100%);
                opacity: 0;
            }
            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .notification {
            animation: slideIn 0.3s forwards;
        }
        
        /* Custom Switch */
        .toggle-checkbox:checked {
            right: 0;
            border-color: #14b8a6;
        }
        .toggle-checkbox:checked + .toggle-label {
            background-color: #14b8a6;
        }
    </style>
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div id="app" class="min-h-screen flex flex-col">
        <!-- Navigation -->
        @auth
            @include('layouts.navigation')
        @endauth

        <!-- Page Content -->
        <main class="flex-grow @auth pb-20 md:pb-0 @endauth">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="fixed top-20 right-4 z-50 max-w-sm w-full bg-white border-l-4 border-primary-500 shadow-lg rounded-lg overflow-hidden transform transition-all duration-300 ease-in-out" role="alert">
                    <div class="p-4 flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            <p class="text-sm font-medium text-gray-900">{{ session('success') }}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button class="inline-flex text-gray-400 focus:outline-none focus:text-gray-500 transition ease-in-out duration-150">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="fixed top-20 right-4 z-50 max-w-sm w-full bg-white border-l-4 border-red-500 shadow-lg rounded-lg overflow-hidden transform transition-all duration-300 ease-in-out" role="alert">
                    <div class="p-4 flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            <p class="text-sm font-medium text-gray-900">{{ session('error') }}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button class="inline-flex text-gray-400 focus:outline-none focus:text-gray-500 transition ease-in-out duration-150">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Mobile Bottom Navigation -->
        @auth
            @include('layouts.mobile-nav')
        @endauth
    </div>

    <!-- Real-time notifications -->
    @auth
        <div id="notifications" class="fixed top-4 right-4 z-50 space-y-2"></div>
    @endauth

    @stack('scripts')

    <!-- Real-time WebSocket -->
    @auth
    <script>
        // Configuration WebSocket pour les notifications temps réel
        window.Echo.private('user.{{ auth()->id() }}')
            .listen('TransferReceived', (e) => {
                showNotification('Nouveau transfert reçu!', e.message, 'success');
            })
            .listen('TransferClaimed', (e) => {
                showNotification('Transfert récupéré!', e.message, 'info');
            });

        function showNotification(title, message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification glass shadow-lg rounded-lg max-w-sm transform transition-all duration-300 ease-in-out`;
            
            let borderColor, iconColor, icon;
            
            if (type === 'success') {
                borderColor = 'border-l-4 border-primary-500';
                iconColor = 'text-primary-500';
                icon = `<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>`;
            } else if (type === 'error') {
                borderColor = 'border-l-4 border-red-500';
                iconColor = 'text-red-500';
                icon = `<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>`;
            } else {
                borderColor = 'border-l-4 border-secondary-500';
                iconColor = 'text-secondary-500';
                icon = `<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>`;
            }
            
            notification.className += ` ${borderColor}`;
            
            notification.innerHTML = `
                <div class="p-4 flex items-start">
                    <div class="flex-shrink-0 ${iconColor}">
                        ${icon}
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium text-gray-900">${title}</p>
                        <p class="mt-1 text-sm text-gray-600">${message}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button onclick="this.parentElement.parentElement.parentElement.remove()" class="inline-flex text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            
            document.getElementById('notifications').appendChild(notification);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.classList.add('opacity-0', 'translate-x-full');
                    setTimeout(() => notification.remove(), 300);
                }
            }, 5000);
        }
    </script>
    @endauth
    
    <script>
        // Animations on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const animatedElements = document.querySelectorAll('.animate-on-scroll');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fade-in');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            
            animatedElements.forEach(el => {
                observer.observe(el);
            });
            
            // Auto-dismiss notifications
            const flashMessages = document.querySelectorAll('[role="alert"]');
            flashMessages.forEach(message => {
                setTimeout(() => {
                    message.classList.add('opacity-0', 'translate-x-full');
                    setTimeout(() => message.remove(), 300);
                }, 5000);
            });
        });
    </script>
</body>
</html>
