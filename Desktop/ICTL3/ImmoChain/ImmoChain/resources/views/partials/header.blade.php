<header class="fixed top-0 left-0 right-0 bg-white border-b z-50">
    <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <!-- Logo -->
            <a href="/" class="text-2xl font-bold">
            <img src="{{ asset('images/logo.png') }}" alt="ImmoChain" class="h-20">

            </a>
            
            <!-- Barre de recherche (visible sur desktop) -->
            <div class="hidden md:flex items-center search-bar-shadow rounded-full border px-4 py-2">
                <button onclick="toggleModal('searchModal')" class="flex items-center space-x-2">
                    <span>Où ?</span>
                    <span class="text-gray-300">|</span>
                    <span>Quoi ?</span>
                    <span class="text-gray-300">|</span>
                    <span>Prix ?</span>
                    <div class="ml-2 bg-black text-white rounded-full w-8 h-8 flex items-center justify-center">
                        <i class="fas fa-search text-sm"></i>
                    </div>
                </button>
            </div>
            
            <!-- Menu de navigation (visible sur desktop) -->
            <div class="hidden md:flex items-center space-x-4">
                @auth
                    @if(Auth::user()->type_utilisateur === 'Propriétaire')
                        <a href="{{ route('dashboard') }}" class="hover:bg-gray-100 px-3 py-2 rounded-full">Tableau de bord</a>
                        <a href="{{ route('properties.create') }}" class="hover:bg-gray-100 px-3 py-2 rounded-full">Ajouter un bien</a>
                    @endif
                    <div class="relative">
                        <button id="desktop-menu-button" class="flex items-center space-x-2 border rounded-full px-3 py-2 hover:shadow-md transition">
                            <i class="fas fa-bars"></i>
                            <i class="fas fa-user-circle text-xl"></i>
                        </button>
                        <div id="desktop-menu" class="absolute right-0 mt-2 w-48 bg-white border rounded-xl shadow-lg overflow-hidden hidden z-50">
                            <div class="py-2">
                                <span class="block px-4 py-2 text-sm text-gray-500">{{ Auth::user()->nom }}</span>
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Tableau de bord</a>
                                @if(Auth::user()->type_utilisateur === 'Propriétaire')
                                    <a href="{{ route('properties.create') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Ajouter un bien</a>
                                    <a href="{{ route('dashboard.properties') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Mes biens</a>
                                    <a href="{{ route('dashboard.reservations') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Réservations</a>
                                @endif
                                <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100">Mes favoris</a>
                                <form action="{{ route('logout') }}" method="POST" class="border-t mt-1">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Déconnexion</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hover:bg-gray-100 px-3 py-2 rounded-full">Connexion</a>
                    <a href="{{ route('register') }}" class="bg-black text-white px-3 py-2 rounded-full hover:bg-gray-800 transition">Inscription</a>
                @endauth
            </div>
            
            <!-- Menu mobile -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="flex items-center space-x-2 border rounded-full px-3 py-2">
                    <i class="fas fa-bars"></i>
                    @auth
                        <i class="fas fa-user-circle text-xl"></i>
                    @else
                        <i class="fas fa-user text-xl"></i>
                    @endauth
                </button>
            </div>
        </div>
        
        <!-- Menu mobile déroulant -->
        <div id="mobile-menu" class="hidden md:hidden mt-4 bg-white rounded-xl border shadow-lg overflow-hidden">
            <div class="py-2">
                @auth
                    <span class="block px-4 py-2 text-sm text-gray-500">{{ Auth::user()->nom }}</span>
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Tableau de bord</a>
                    @if(Auth::user()->type_utilisateur === 'Propriétaire')
                        <a href="{{ route('properties.create') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Ajouter un bien</a>
                        <a href="{{ route('dashboard.properties') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Mes biens</a>
                        <a href="{{ route('dashboard.reservations') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Réservations</a>
                    @endif
                    <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100">Mes favoris</a>
                    <form action="{{ route('logout') }}" method="POST" class="border-t mt-1">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Déconnexion</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Connexion</a>
                    <a href="{{ route('register') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Inscription</a>
                @endauth
                <div class="border-t mt-1">
                    <button onclick="toggleModal('searchModal')" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">
                        <i class="fas fa-search mr-2"></i> Recherche avancée
                    </button>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Menu mobile
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
            
            // Fermer le menu en cliquant en dehors
            document.addEventListener('click', function(event) {
                if (!event.target.closest('#mobile-menu') && 
                    !event.target.closest('#mobile-menu-button')) {
                    if (!mobileMenu.classList.contains('hidden')) {
                        mobileMenu.classList.add('hidden');
                    }
                }
            });
        }
        
        // Menu desktop
        const desktopMenuButton = document.getElementById('desktop-menu-button');
        const desktopMenu = document.getElementById('desktop-menu');
        
        if (desktopMenuButton && desktopMenu) {
            desktopMenuButton.addEventListener('click', function() {
                desktopMenu.classList.toggle('hidden');
            });
            
            // Fermer le menu en cliquant en dehors
            document.addEventListener('click', function(event) {
                if (!event.target.closest('#desktop-menu') && 
                    !event.target.closest('#desktop-menu-button')) {
                    if (!desktopMenu.classList.contains('hidden')) {
                        desktopMenu.classList.add('hidden');
                    }
                }
            });
        }
    });
</script>

