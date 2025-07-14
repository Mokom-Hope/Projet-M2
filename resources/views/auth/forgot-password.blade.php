@extends('layouts.app')

@section('title', 'Mot de passe oublié')

@section('content')
<div class="min-h-screen flex">
    <!-- Left Panel - Branding -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-amber-500 via-orange-600 to-red-700 relative overflow-hidden">
        <!-- Animated Background -->
        <div class="absolute inset-0">
            <div class="absolute top-20 left-20 w-72 h-72 bg-white/10 rounded-full animate-blob"></div>
            <div class="absolute top-40 right-20 w-64 h-64 bg-white/5 rounded-full animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-20 left-40 w-80 h-80 bg-white/5 rounded-full animate-blob animation-delay-4000"></div>
        </div>
        
        <!-- Content -->
        <div class="relative z-10 flex flex-col justify-center px-12 py-12 text-white">
            <div class="max-w-md">
                <!-- Logo -->
                <div class="mb-8">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                    </div>
                    <h1 class="text-4xl font-bold mb-4">Récupération</h1>
                    <p class="text-xl text-white/80 leading-relaxed">
                        Pas de panique ! Nous allons vous aider à récupérer l'accès à votre compte en toute sécurité.
                    </p>
                </div>

                <!-- Steps -->
                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <span class="text-sm font-bold">1</span>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-1">Entrez votre email</h3>
                            <p class="text-white/70 text-sm">L'adresse email associée à votre compte</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <span class="text-sm font-bold">2</span>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-1">Vérifiez votre boîte mail</h3>
                            <p class="text-white/70 text-sm">Cliquez sur le lien de réinitialisation</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <span class="text-sm font-bold">3</span>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-1">Créez un nouveau mot de passe</h3>
                            <p class="text-white/70 text-sm">Choisissez un mot de passe sécurisé</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel - Form -->
    <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-md w-full space-y-8">
            <!-- Mobile Logo -->
            <div class="lg:hidden text-center">
                <div class="mx-auto w-16 h-16 bg-gradient-to-br from-amber-500 to-red-600 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Récupération</h2>
            </div>

            <!-- Header -->
            <div class="text-center lg:text-left">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    Mot de passe oublié ?
                </h1>
                <p class="text-gray-600">
                    Entrez votre email pour recevoir un lien de réinitialisation
                </p>
            </div>

            <!-- Alert -->
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center animate-fade-in">
                    <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            <!-- Form -->
            <form class="space-y-6" action="{{ route('password.email') }}" method="POST">
                @csrf
                
                <!-- Email Field -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-semibold text-gray-700">
                        Adresse email
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-amber-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required 
                               class="block w-full pl-12 pr-4 py-4 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all duration-200 bg-white hover:border-gray-300 @error('email') border-red-300 focus:ring-red-500 @enderror" 
                               placeholder="votre@email.com" value="{{ old('email') }}">
                    </div>
                    @error('email')
                        <p class="text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="space-y-4">
                    <button type="submit" class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-base font-semibold rounded-xl text-white bg-gradient-to-r from-amber-600 via-orange-700 to-red-700 hover:from-amber-700 hover:via-orange-800 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all duration-300 transform hover:scale-[1.02] hover:shadow-xl">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-4">
                            <svg class="h-5 w-5 text-amber-300 group-hover:text-amber-200 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </span>
                        <span class="relative">Envoyer le lien de réinitialisation</span>
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

            <!-- Help Section -->
            <div class="text-center">
                <div class="inline-flex items-center px-4 py-2 bg-white rounded-full border border-gray-200 shadow-sm">
                    <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-xs font-medium text-gray-700">Vérifiez vos spams si vous ne recevez rien</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
@keyframes blob {
    0% { transform: translate(0px, 0px) scale(1); }
    33% { transform: translate(30px, -50px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
    100% { transform: translate(0px, 0px) scale(1); }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endpush
@endsection
