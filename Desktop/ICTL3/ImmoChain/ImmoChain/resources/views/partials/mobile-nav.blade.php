<nav class="fixed bottom-0 left-0 right-0 bg-white border-t md:hidden z-40">
    <div class="flex justify-around py-3">
        <a href="/" class="flex flex-col items-center text-xs {{ Request::is('/') ? 'text-primary' : 'text-gray-500' }}">
            <i class="fas fa-search mb-1"></i>
            <span>Explorer</span>
        </a>
        <a href="/favorites" class="flex flex-col items-center text-xs {{ Request::is('favorites') ? 'text-primary' : 'text-gray-500' }}">
            <i class="far fa-heart mb-1"></i>
            <span>Favoris</span>
        </a>
        <a href="/login" class="flex flex-col items-center text-xs {{ Request::is('login') ? 'text-primary' : 'text-gray-500' }}">
            <i class="far fa-user mb-1"></i>
            <span>Connexion</span>
        </a>
    </div>
</nav>