<!-- Mobile Bottom Navigation -->
<div class="md:hidden fixed bottom-0 left-0 right-0 z-50">
    <div class="glass border-t border-gray-200 shadow-lg">
        <div class="grid grid-cols-5 h-16">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center {{ request()->routeIs('dashboard') ? 'text-primary-600' : 'text-gray-500' }} transition-all duration-200 hover:text-primary-500">
                <div class="relative">
                    @if(request()->routeIs('dashboard'))
                        <span class="absolute inset-0 rounded-full bg-primary-100 animate-pulse-slow"></span>
                    @endif
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
                <span class="text-xs mt-1 font-medium">Accueil</span>
            </a>
            
            <!-- Send Money -->
            <a href="{{ route('transfers.create') }}" class="flex flex-col items-center justify-center {{ request()->routeIs('transfers.create') ? 'text-primary-600' : 'text-gray-500' }} transition-all duration-200 hover:text-primary-500">
                <div class="relative">
                    @if(request()->routeIs('transfers.create'))
                        <span class="absolute inset-0 rounded-full bg-primary-100 animate-pulse-slow"></span>
                    @endif
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </div>
                <span class="text-xs mt-1 font-medium">Envoyer</span>
            </a>
            
            <!-- Quick Action Button -->
            <div class="relative flex justify-center">
                <div class="absolute -top-8 bg-gradient-to-r from-primary-500 to-primary-600 rounded-full p-3 shadow-lg transform transition-all duration-300 hover:scale-110 hover:shadow-glow">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
            </div>
            
            <!-- Claim Money -->
            <a href="{{ route('transfers.claim') }}" class="flex flex-col items-center justify-center {{ request()->routeIs('transfers.claim') ? 'text-primary-600' : 'text-gray-500' }} transition-all duration-200 hover:text-primary-500">
                <div class="relative">
                    @if(request()->routeIs('transfers.claim'))
                        <span class="absolute inset-0 rounded-full bg-primary-100 animate-pulse-slow"></span>
                    @endif
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <span class="text-xs mt-1 font-medium">Récupérer</span>
            </a>
            
            <!-- Profile -->
            <a href="{{ route('profile.show') }}" class="flex flex-col items-center justify-center {{ request()->routeIs('profile.*') ? 'text-primary-600' : 'text-gray-500' }} transition-all duration-200 hover:text-primary-500">
                <div class="relative">
                    @if(request()->routeIs('profile.*'))
                        <span class="absolute inset-0 rounded-full bg-primary-100 animate-pulse-slow"></span>
                    @endif
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <span class="text-xs mt-1 font-medium">Profil</span>
            </a>
        </div>
    </div>
</div>
