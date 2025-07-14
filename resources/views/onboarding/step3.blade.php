@extends('layouts.app')

@section('title', 'Prêt à commencer - MoneyTransfer')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-pink-50 relative overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute inset-0">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-purple-400 to-pink-600 rounded-full opacity-10 animate-pulse-slow"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-br from-indigo-400 to-purple-600 rounded-full opacity-10 animate-pulse-slow" style="animation-delay: 2s;"></div>
    </div>

    <!-- Header -->
    <div class="relative z-10 flex justify-between items-center p-6 md:p-8">
        <a href="{{ route('onboarding.step2') }}" class="flex items-center text-gray-600 hover:text-gray-900 transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Retour
        </a>
        <div class="flex space-x-2">
            <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
            <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
            <div class="w-8 h-2 bg-primary-600 rounded-full"></div>
        </div>
        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-primary-600 transition-colors duration-200">
            Se connecter
        </a>
    </div>

    <!-- Content -->
    <div class="relative z-10 flex-1 flex items-center justify-center px-6 md:px-8 py-12">
        <div class="max-w-4xl mx-auto text-center">
            <!-- Celebration Illustration -->
            <div class="mb-12 animate-on-scroll">
                <div class="relative inline-block">
                    <!-- Main Circle -->
                    <div class="w-48 h-48 mx-auto relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-500 via-pink-500 to-red-500 rounded-full shadow-2xl animate-pulse-glow"></div>
                        <div class="absolute inset-4 bg-white rounded-full flex items-center justify-center">
                            <svg class="w-20 h-20 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                        
                        <!-- Confetti Elements -->
                        <div class="absolute -top-8 -left-8 w-4 h-4 bg-yellow-400 rounded-full animate-bounce-slow"></div>
                        <div class="absolute -top-4 -right-8 w-3 h-3 bg-blue-400 rounded-full animate-bounce-slow" style="animation-delay: 0.5s;"></div>
                        <div class="absolute -bottom-8 -right-8 w-5 h-5 bg-green-400 rounded-full animate-bounce-slow" style="animation-delay: 1s;"></div>
                        <div class="absolute -bottom-4 -left-8 w-3 h-3 bg-pink-400 rounded-full animate-bounce-slow" style="animation-delay: 1.5s;"></div>
                        <div class="absolute top-1/4 -left-12 w-2 h-2 bg-purple-400 rounded-full animate-bounce-slow" style="animation-delay: 2s;"></div>
                        <div class="absolute top-3/4 -right-12 w-4 h-4 bg-orange-400 rounded-full animate-bounce-slow" style="animation-delay: 2.5s;"></div>
                    </div>
                </div>
            </div>

            <!-- Text Content -->
            <div class="space-y-6 animate-on-scroll">
                <h1 class="text-4xl md:text-5xl font-display font-bold leading-tight">
                    <span class="bg-gradient-to-r from-gray-900 via-purple-800 to-pink-800 bg-clip-text text-transparent">
                        Vous êtes prêt !
                    </span>
                </h1>
                
                <p class="text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed">
                    Félicitations ! Vous avez maintenant toutes les clés en main pour commencer à transférer de l'argent en toute sécurité.
                </p>
            </div>

            <!-- Benefits Summary -->
            <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-6 animate-on-scroll">
                <div class="group bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-6 border border-blue-100 hover:shadow-xl transition-all duration-300">
                    <div class="w-12 h-12 mx-auto mb-4 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Instantané</h3>
                    <p class="text-gray-600 text-sm">Transferts en quelques secondes</p>
                </div>
                
                <div class="group bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-100 hover:shadow-xl transition-all duration-300">
                    <div class="w-12 h-12 mx-auto mb-4 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Sécurisé</h3>
                    <p class="text-gray-600 text-sm">Protection de niveau bancaire</p>
                </div>
                
                <div class="group bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-100 hover:shadow-xl transition-all duration-300">
                    <div class="w-12 h-12 mx-auto mb-4 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">International</h3>
                    <p class="text-gray-600 text-sm">Plus de 180 pays</p>
                </div>
            </div>

            <!-- Special Offer -->
            <div class="mt-12 animate-on-scroll">
                <div class="bg-gradient-to-r from-purple-500 via-pink-500 to-red-500 rounded-2xl p-1 shadow-2xl">
                    <div class="bg-white rounded-xl p-6">
                        <div class="flex items-center justify-center mb-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">🎉 Offre de bienvenue</h3>
                        <p class="text-gray-600 mb-4">Votre premier transfert est gratuit ! Aucun frais jusqu'à 50,000 XOF</p>
                        <div class="inline-flex items-center px-3 py-1 bg-gradient-to-r from-green-100 to-emerald-100 rounded-full">
                            <span class="text-sm font-semibold text-green-800">Économisez jusqu'à 2,500 XOF</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation -->
    <div class="relative z-10 p-6 md:p-8">
        <div class="max-w-md mx-auto space-y-4">
            <a href="{{ route('register') }}" class="block w-full bg-gradient-to-r from-purple-600 via-pink-600 to-red-600 hover:from-purple-700 hover:via-pink-700 hover:to-red-700 text-white text-center py-4 px-6 rounded-2xl font-semibold text-lg shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300">
                Créer mon compte gratuitement
            </a>
            <p class="text-center text-sm text-gray-500">
                Déjà un compte ? <a href="{{ route('login') }}" class="font-semibold text-purple-600 hover:text-purple-700 transition-colors duration-200">Se connecter</a>
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add('opacity-100', 'translate-y-0');
                    entry.target.classList.remove('opacity-0', 'translate-y-10');
                }, index * 200);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    
    animatedElements.forEach(el => {
        el.classList.add('opacity-0', 'translate-y-10', 'transition-all', 'duration-700', 'ease-out');
        observer.observe(el);
    });
});
</script>
@endpush
@endsection
