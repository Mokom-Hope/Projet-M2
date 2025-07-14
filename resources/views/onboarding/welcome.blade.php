@extends('layouts.app')

@section('title', 'Bienvenue sur MoneyTransfer')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-primary-50 via-white to-secondary-50 relative overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute inset-0">
        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-primary-100/30 to-secondary-100/30"></div>
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full opacity-10 animate-pulse-slow"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-gradient-to-br from-secondary-400 to-secondary-600 rounded-full opacity-10 animate-pulse-slow" style="animation-delay: 2s;"></div>
        <div class="absolute top-1/4 right-1/3 w-32 h-32 bg-gradient-to-br from-purple-300 to-pink-300 rounded-full opacity-20 animate-float" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-1/4 left-1/4 w-24 h-24 bg-gradient-to-br from-blue-300 to-cyan-300 rounded-full opacity-20 animate-float" style="animation-delay: 3s;"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 flex flex-col min-h-screen">
        <!-- Header -->
        <div class="flex justify-between items-center p-6 md:p-8">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <span class="text-xl font-bold text-gray-900">MoneyTransfer</span>
            </div>
            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-primary-600 transition-colors duration-200">
                Se connecter
            </a>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex items-center justify-center px-6 md:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <!-- Hero Animation -->
                <div class="mb-12 animate-on-scroll">
                    <div class="relative inline-block">
                        <div class="w-32 h-32 md:w-40 md:h-40 mx-auto mb-8 relative">
                            <!-- Main Circle -->
                            <div class="absolute inset-0 bg-gradient-to-br from-primary-500 via-primary-600 to-secondary-600 rounded-full shadow-2xl animate-pulse-glow"></div>
                            <!-- Inner Elements -->
                            <div class="absolute inset-4 bg-white rounded-full flex items-center justify-center">
                                <svg class="w-16 h-16 md:w-20 md:h-20 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <!-- Floating Elements -->
                            <div class="absolute -top-2 -right-2 w-6 h-6 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full animate-bounce-slow"></div>
                            <div class="absolute -bottom-2 -left-2 w-4 h-4 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full animate-bounce-slow" style="animation-delay: 1s;"></div>
                            <div class="absolute top-1/2 -left-4 w-3 h-3 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-full animate-bounce-slow" style="animation-delay: 2s;"></div>
                        </div>
                    </div>
                </div>

                <!-- Text Content -->
                <div class="space-y-6 animate-on-scroll">
                    <h1 class="text-4xl md:text-6xl font-display font-bold leading-tight">
                        <span class="bg-gradient-to-r from-gray-900 via-primary-800 to-secondary-800 bg-clip-text text-transparent">
                            Transférez de l'argent
                        </span>
                        <br>
                        <span class="bg-gradient-to-r from-primary-600 via-secondary-600 to-purple-600 bg-clip-text text-transparent">
                            en toute simplicité
                        </span>
                    </h1>
                    
                    <p class="text-xl md:text-2xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Envoyez et recevez de l'argent instantanément, en toute sécurité, partout dans le monde avec MoneyTransfer.
                    </p>
                </div>

                <!-- Features Preview -->
                <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8 animate-on-scroll">
                    <div class="group">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Transferts instantanés</h3>
                        <p class="text-gray-600">Vos transferts arrivent en quelques secondes</p>
                    </div>
                    
                    <div class="group">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">100% sécurisé</h3>
                        <p class="text-gray-600">Chiffrement de niveau bancaire</p>
                    </div>
                    
                    <div class="group">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Partout dans le monde</h3>
                        <p class="text-gray-600">Plus de 180 pays supportés</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom CTA -->
        <div class="p-6 md:p-8">
            <div class="max-w-md mx-auto space-y-4">
                <a href="{{ route('onboarding.step1') }}" class="block w-full bg-gradient-to-r from-primary-600 via-primary-700 to-secondary-700 hover:from-primary-700 hover:via-primary-800 hover:to-secondary-800 text-white text-center py-4 px-6 rounded-2xl font-semibold text-lg shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 bg-size-200 animate-gradient">
                    Commencer maintenant
                </a>
                <p class="text-center text-sm text-gray-500">
                    Gratuit • Sans engagement • Configuration en 2 minutes
                </p>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
@keyframes pulse-glow {
    0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.3); }
    50% { box-shadow: 0 0 40px rgba(59, 130, 246, 0.6), 0 0 60px rgba(59, 130, 246, 0.3); }
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

@keyframes bounce-slow {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.animate-pulse-glow {
    animation: pulse-glow 3s ease-in-out infinite;
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-bounce-slow {
    animation: bounce-slow 3s ease-in-out infinite;
}
</style>
@endpush

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
