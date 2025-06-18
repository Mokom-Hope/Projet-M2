@extends('layouts.app')

@section('title', 'Comment ça marche - MoneyTransfer')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 relative overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute inset-0">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-blue-400 to-indigo-600 rounded-full opacity-10 animate-pulse-slow"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-br from-cyan-400 to-blue-600 rounded-full opacity-10 animate-pulse-slow" style="animation-delay: 2s;"></div>
    </div>

    <!-- Header -->
    <div class="relative z-10 flex justify-between items-center p-6 md:p-8">
        <a href="{{ route('onboarding.welcome') }}" class="flex items-center text-gray-600 hover:text-gray-900 transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Retour
        </a>
        <div class="flex space-x-2">
            <div class="w-8 h-2 bg-primary-600 rounded-full"></div>
            <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
            <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
        </div>
        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-primary-600 transition-colors duration-200">
            Se connecter
        </a>
    </div>

    <!-- Content -->
    <div class="relative z-10 flex-1 flex items-center justify-center px-6 md:px-8 py-12">
        <div class="max-w-4xl mx-auto text-center">
            <!-- Illustration -->
            <div class="mb-12 animate-on-scroll">
                <div class="relative inline-block">
                    <!-- Main Device -->
                    <div class="w-64 h-80 mx-auto bg-gradient-to-br from-gray-900 to-gray-800 rounded-3xl shadow-2xl p-2 transform rotate-3 hover:rotate-0 transition-transform duration-500">
                        <div class="w-full h-full bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-6 flex flex-col">
                            <!-- Screen Header -->
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-8 h-8 bg-white/20 rounded-full"></div>
                                <div class="text-white text-sm font-medium">MoneyTransfer</div>
                                <div class="w-8 h-8 bg-white/20 rounded-full"></div>
                            </div>
                            
                            <!-- Transfer Form -->
                            <div class="flex-1 space-y-4">
                                <div class="bg-white/10 rounded-xl p-4">
                                    <div class="text-white/70 text-xs mb-2">Montant</div>
                                    <div class="text-white text-xl font-bold">50,000 XOF</div>
                                </div>
                                <div class="bg-white/10 rounded-xl p-4">
                                    <div class="text-white/70 text-xs mb-2">Destinataire</div>
                                    <div class="text-white text-sm">+225 XX XX XX XX</div>
                                </div>
                                <div class="bg-white rounded-xl p-4 mt-6">
                                    <div class="text-blue-600 text-sm font-semibold text-center">Envoyer</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Floating Elements -->
                    <div class="absolute -top-4 -right-4 w-12 h-12 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center shadow-lg animate-bounce-slow">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="absolute -bottom-4 -left-4 w-10 h-10 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg animate-bounce-slow" style="animation-delay: 1s;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Text Content -->
            <div class="space-y-6 animate-on-scroll">
                <h1 class="text-4xl md:text-5xl font-display font-bold leading-tight">
                    <span class="bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 bg-clip-text text-transparent">
                        Simple comme bonjour
                    </span>
                </h1>
                
                <p class="text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed">
                    Saisissez le montant, choisissez le destinataire et envoyez. 
                    Votre argent arrive instantanément, où que vous soyez.
                </p>
            </div>

            <!-- Steps -->
            <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8 animate-on-scroll">
                <div class="group">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <span class="text-2xl font-bold text-white">1</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Saisissez le montant</h3>
                    <p class="text-gray-600">Indiquez combien vous voulez envoyer</p>
                </div>
                
                <div class="group">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <span class="text-2xl font-bold text-white">2</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Choisissez le destinataire</h3>
                    <p class="text-gray-600">Numéro de téléphone ou email</p>
                </div>
                
                <div class="group">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <span class="text-2xl font-bold text-white">3</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Confirmez l'envoi</h3>
                    <p class="text-gray-600">L'argent arrive instantanément</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation -->
    <div class="relative z-10 p-6 md:p-8">
        <div class="max-w-md mx-auto">
            <a href="{{ route('onboarding.step2') }}" class="block w-full bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 hover:from-blue-700 hover:via-blue-800 hover:to-indigo-800 text-white text-center py-4 px-6 rounded-2xl font-semibold text-lg shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300">
                Suivant
            </a>
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
