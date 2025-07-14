@extends('layouts.app')

@section('title', 'Vérification email')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 via-primary-50 to-secondary-50 py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-blue-400 to-indigo-600 rounded-full opacity-20 animate-pulse-slow"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-br from-cyan-400 to-blue-600 rounded-full opacity-20 animate-pulse-slow" style="animation-delay: 2s;"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-gradient-to-br from-sky-300 to-blue-300 rounded-full opacity-10 animate-bounce-slow"></div>
    </div>

    <div class="max-w-md w-full space-y-8 relative z-10">
        <!-- Logo Section -->
        <div class="text-center animate-on-scroll">
            <div class="mx-auto w-20 h-20 bg-gradient-to-br from-blue-500 via-indigo-600 to-purple-700 rounded-3xl flex items-center justify-center shadow-glow transform transition-all duration-500 hover:scale-110 hover:rotate-3 bg-size-200 animate-gradient">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h1 class="mt-6 text-4xl font-display font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 bg-clip-text text-transparent">
                Vérifiez votre email
            </h1>
            <p class="mt-2 text-base text-gray-600 font-medium">
                Un lien de vérification a été envoyé à votre adresse email
            </p>
        </div>

        <!-- Status Message -->
        @if (session('status') == 'verification-link-sent')
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center animate-fade-in">
                <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Un nouveau lien de vérification a été envoyé à votre adresse email.
            </div>
        @endif

        <!-- Main Content -->
        <div class="glass rounded-2xl shadow-card-hover p-8 border border-white/20 backdrop-blur-xl animate-on-scroll">
            <div class="text-center space-y-6">
                <!-- Email Icon -->
                <div class="mx-auto w-16 h-16 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-2xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>

                <div>
                    <h3 class="text-xl font-display font-bold text-gray-900">Vérification requise</h3>
                    <p class="mt-2 text-sm text-gray-600 leading-relaxed">
                        Avant de continuer, veuillez vérifier votre adresse email en cliquant sur le lien que nous venons de vous envoyer.
                    </p>
                    <p class="mt-2 text-sm text-gray-600 leading-relaxed">
                        Si vous n'avez pas reçu l'email, nous pouvons vous en envoyer un autre.
                    </p>
                </div>

                <div class="space-y-4">
                    <!-- Resend Verification Email -->
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-base font-semibold rounded-xl text-white bg-gradient-to-r from-blue-600 via-indigo-700 to-purple-800 hover:from-blue-700 hover:via-indigo-800 hover:to-purple-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 transform hover:scale-105 hover:shadow-glow bg-size-200 animate-gradient">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-blue-300 group-hover:text-blue-200 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </span>
                            <span class="relative">Renvoyer l'email de vérification</span>
                            <div class="absolute inset-0 rounded-xl bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                        </button>
                    </form>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-gray-200 text-base font-medium rounded-xl text-gray-700 bg-white/70 backdrop-blur-sm hover:bg-white/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200">
                            Se déconnecter
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Section -->
        <div class="text-center animate-on-scroll">
            <div class="inline-flex items-center px-4 py-2 bg-white/60 backdrop-blur-sm rounded-full border border-gray-200 shadow-sm">
                <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-xs font-medium text-gray-700">Vérifiez vos spams si vous ne trouvez pas l'email</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Animation on scroll
document.addEventListener('DOMContentLoaded', function() {
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
