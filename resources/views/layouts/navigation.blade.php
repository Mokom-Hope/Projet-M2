<nav class="sticky top-0 z-40 bg-white/80 backdrop-blur-lg shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center shadow-glow transform transition-all duration-300 hover:scale-105">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <span class="ml-3 text-xl font-display font-bold bg-gradient-to-r from-primary-600 to-secondary-600 bg-clip-text text-transparent hidden sm:block">MoneyTransfer</span>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-1">
                <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 ease-in-out {{ request()->routeIs('dashboard') ? 'text-primary-700 bg-primary-50' : 'text-gray-700 hover:text-primary-600 hover:bg-gray-50' }}">
                    Tableau de bord
                </a>
                <a href="{{ route('transfers.create') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 ease-in-out {{ request()->routeIs('transfers.create') ? 'text-primary-700 bg-primary-50' : 'text-gray-700 hover:text-primary-600 hover:bg-gray-50' }}">
                    Envoyer
                </a>
                <a href="{{ route('transfers.claim') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 ease-in-out {{ request()->routeIs('transfers.claim') ? 'text-primary-700 bg-primary-50' : 'text-gray-700 hover:text-primary-600 hover:bg-gray-50' }}">
                    Récupérer
                </a>
                <a href="{{ route('transfers.history') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 ease-in-out {{ request()->routeIs('transfers.history') ? 'text-primary-700 bg-primary-50' : 'text-gray-700 hover:text-primary-600 hover:bg-gray-50' }}">
                    Historique
                </a>
            </div>

            <!-- User Menu -->
            <div class="flex items-center space-x-4">
                <!-- Notifications -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="p-2 rounded-full text-gray-500 hover:text-primary-600 hover:bg-gray-100 transition-all duration-200 relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h10a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center animate-pulse">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>
                    
                    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="origin-top-right absolute right-0 mt-2 w-80 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50" style="display: none;">
                        <div class="py-2">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
                            </div>
                            <div class="max-h-60 overflow-y-auto">
                                @forelse(auth()->user()->notifications->take(5) as $notification)
                                    <div class="px-4 py-3 hover:bg-gray-50 {{ $notification->read_at ? 'opacity-75' : '' }}">
                                        <p class="text-sm text-gray-900">{{ $notification->data['message'] }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                @empty
                                    <div class="px-4 py-6 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">Pas de notifications</p>
                                    </div>
                                @endforelse
                            </div>
                            @if(auth()->user()->notifications->count() > 0)
                                <div class="px-4 py-2 border-t border-gray-100 text-center">
                                    <a href="#" class="text-xs font-medium text-primary-600 hover:text-primary-700">
                                        Voir toutes les notifications
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Wallet Balance -->
                <div class="hidden sm:flex items-center bg-gradient-to-r from-primary-50 to-primary-100 px-4 py-2 rounded-full shadow-sm">
                    <svg class="w-4 h-4 text-primary-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    <span class="text-sm font-medium text-primary-700">
                        {{ number_format(auth()->user()->wallet->balance ?? 0, 0, ',', ' ') }} {{ auth()->user()->currency }}
                    </span>
                </div>

                <!-- Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all duration-200">
                        @if(auth()->user()->profile_photo)
                            <img class="h-9 w-9 rounded-full object-cover border-2 border-primary-200" src="{{ Storage::url(auth()->user()->profile_photo) }}" alt="{{ auth()->user()->full_name }}">
                        @else
                            <div class="h-9 w-9 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center shadow-sm">
                                <span class="text-white text-sm font-medium">{{ substr(auth()->user()->first_name, 0, 1) }}</span>
                            </div>
                        @endif
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="origin-top-right absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1 z-50" style="display: none;">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm text-gray-900 font-medium">{{ auth()->user()->full_name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150">Mon Profil</a>
                        <a href="{{ route('payment-methods.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150">Méthodes de paiement</a>
                        <div class="border-t border-gray-100"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                                Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
