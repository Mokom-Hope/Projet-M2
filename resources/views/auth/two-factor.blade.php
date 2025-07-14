@extends('layouts.app')

@section('title', 'Authentification à deux facteurs')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 via-primary-50 to-secondary-50 py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-1/4 right-1/4 w-64 h-64 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full opacity-20 animate-pulse-slow"></div>
        <div class="absolute bottom-1/4 left-1/4 w-64 h-64 bg-gradient-to-br from-secondary-400 to-secondary-600 rounded-full opacity-20 animate-pulse-slow" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-gradient-to-br from-purple-300 to-pink-300 rounded-full opacity-10 animate-bounce-slow"></div>
    </div>

    <div class="max-w-md w-full space-y-8 relative z-10">
        <!-- Logo Section -->
        <div class="text-center animate-on-scroll">
            <div class="mx-auto w-20 h-20 bg-gradient-to-br from-purple-500 via-purple-600 to-purple-700 rounded-3xl flex items-center justify-center shadow-glow transform transition-all duration-500 hover:scale-110 hover:rotate-3 bg-size-200 animate-gradient">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h1 class="mt-6 text-4xl font-display font-bold bg-gradient-to-r from-gray-900 via-purple-800 to-primary-800 bg-clip-text text-transparent">
                Sécurité renforcée
            </h1>
            <p class="mt-2 text-base text-gray-600 font-medium">
                Entrez le code à 6 chiffres de votre application d'authentification
            </p>
        </div>

        <!-- Form Container -->
        <div class="glass rounded-2xl shadow-card-hover p-8 border border-white/20 backdrop-blur-xl animate-on-scroll">
            <form class="space-y-6" action="{{ route('2fa.verify') }}" method="POST">
                @csrf
                
                <!-- 2FA Code Input -->
                <div class="space-y-4">
                    <label for="code" class="block text-sm font-semibold text-gray-700 text-center">
                        Code d'authentification
                    </label>
                    
                    <!-- Code Input with Special Styling -->
                    <div class="flex justify-center">
                        <input id="code" name="code" type="text" maxlength="6" required 
                               class="w-48 px-4 py-6 border-2 border-gray-200 rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-center text-3xl font-mono tracking-widest bg-white/70 backdrop-blur-sm hover:bg-white/90 transition-all duration-200 @error('code') border-red-300 focus:ring-red-500 @enderror" 
                               placeholder="000000" autocomplete="one-time-code">
                    </div>
                    
                    @error('code')
                        <p class="text-sm text-red-600 flex items-center justify-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror

                    <!-- Visual Feedback -->
                    <div class="flex justify-center space-x-2">
                        <div id="dot1" class="w-3 h-3 bg-gray-300 rounded-full transition-all duration-200"></div>
                        <div id="dot2" class="w-3 h-3 bg-gray-300 rounded-full transition-all duration-200"></div>
                        <div id="dot3" class="w-3 h-3 bg-gray-300 rounded-full transition-all duration-200"></div>
                        <div id="dot4" class="w-3 h-3 bg-gray-300 rounded-full transition-all duration-200"></div>
                        <div id="dot5" class="w-3 h-3 bg-gray-300 rounded-full transition-all duration-200"></div>
                        <div id="dot6" class="w-3 h-3 bg-gray-300 rounded-full transition-all duration-200"></div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="space-y-4">
                    <button type="submit" class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-base font-semibold rounded-xl text-white bg-gradient-to-r from-purple-600 via-purple-700 to-purple-800 hover:from-purple-700 hover:via-purple-800 hover:to-purple-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-300 transform hover:scale-105 hover:shadow-glow bg-size-200 animate-gradient">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-purple-300 group-hover:text-purple-200 transition-colors duration-200" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                        <span class="relative">Vérifier le code</span>
                        <div class="absolute inset-0 rounded-xl bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                    </button>

                    <!-- Back to Login -->
                    <div class="text-center">
                        <a href="{{ route('login') }}" class="font-semibold text-primary-600 hover:text-primary-700 transition-colors duration-200 hover:underline flex items-center justify-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Retour à la connexion
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Help Section -->
        <div class="text-center animate-on-scroll">
            <div class="inline-flex items-center px-4 py-2 bg-white/60 backdrop-blur-sm rounded-full border border-gray-200 shadow-sm">
                <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-xs font-medium text-gray-700">Utilisez Google Authenticator ou Authy</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-focus and format the 2FA code input
document.getElementById('code').addEventListener('input', function(e) {
    // Remove any non-numeric characters
    this.value = this.value.replace(/\D/g, '');
    
    // Update visual dots
    updateDots(this.value.length);
    
    // Auto-submit when 6 digits are entered
    if (this.value.length === 6) {
        setTimeout(() => {
            this.form.submit();
        }, 500);
    }
});

function updateDots(length) {
    for (let i = 1; i <= 6; i++) {
        const dot = document.getElementById(`dot${i}`);
        if (i <= length) {
            dot.classList.remove('bg-gray-300');
            dot.classList.add('bg-purple-500', 'scale-125');
        } else {
            dot.classList.remove('bg-purple-500', 'scale-125');
            dot.classList.add('bg-gray-300');
        }
    }
}

// Auto-focus on page load
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('code').focus();
    
    // Animation on scroll
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('opacity-100', 'translate-y-0');
                entry.target.classList.remove('opacity-0', 'translate-y-10');
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
