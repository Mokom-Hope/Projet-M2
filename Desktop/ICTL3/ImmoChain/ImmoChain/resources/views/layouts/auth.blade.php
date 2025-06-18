<!DOCTYPE html>
<html lang="fr" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>ColiGo - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'coligo-blue': '#4A90E2',
                        'coligo-yellow': '#FFD700',
                        'coligo-orange': '#FFA500',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .slide-up {
            animation: slideUp 0.5s ease-out;
        }
        .native-tap-highlight {
            -webkit-tap-highlight-color: transparent;
        }
    </style>
</head>
<body class="h-full">
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gradient-to-b from-white to-gray-50">
        <div class="sm:mx-auto sm:w-full sm:max-w-md slide-up">
            <img class="mx-auto h-20 w-auto" src="{{ asset('images/coligo4.jpg') }}" alt="ColiGo">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                @yield('header')
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                @yield('subheader')
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md slide-up" style="animation-delay: 0.1s">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                @yield('content')
            </div>
        </div>
    </div>

    <script>
        // Add touch feedback for mobile
        document.querySelectorAll('button, a').forEach(element => {
            element.addEventListener('touchstart', function() {
                this.style.opacity = '0.8';
            });
            element.addEventListener('touchend', function() {
                this.style.opacity = '1';
            });
        });
    </script>
    @yield('scripts')
</body>
</html>