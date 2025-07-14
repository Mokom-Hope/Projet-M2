@extends('layouts.app')

@section('title', 'Créer un compte')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 relative overflow-hidden">
    <!-- Animated Background -->
    <div class="absolute inset-0">
        <div class="absolute top-0 -left-4 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
        <div class="absolute top-0 -right-4 w-72 h-72 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
    </div>

    <div class="relative z-10 flex min-h-screen">
        <!-- Left Side - Branding -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 relative overflow-hidden">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="relative z-10 flex flex-col justify-center px-12 text-white">
                <div class="mb-8">
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mb-6 backdrop-blur-sm">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <h1 class="text-4xl font-bold mb-4">Rejoignez MoneyTransfer</h1>
                    <p class="text-xl text-white/90 leading-relaxed">
                        La plateforme de transfert d'argent la plus sécurisée et la plus rapide d'Afrique.
                    </p>
                </div>
                
                <div class="space-y-6">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold">Transferts instantanés</h3>
                            <p class="text-white/80">Envoyez de l'argent en quelques secondes</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold">100% Sécurisé</h3>
                            <p class="text-white/80">Chiffrement de niveau bancaire</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold">Frais réduits</h3>
                            <p class="text-white/80">Les meilleurs taux du marché</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Floating Elements -->
            <div class="absolute top-20 right-20 w-32 h-32 bg-white/10 rounded-full animate-float"></div>
            <div class="absolute bottom-20 right-32 w-20 h-20 bg-white/10 rounded-full animate-float-delayed"></div>
        </div>

        <!-- Right Side - Form -->
        <div class="flex-1 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="lg:hidden w-16 h-16 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Créer un compte</h2>
                    <p class="text-gray-600">Rejoignez des milliers d'utilisateurs satisfaits</p>
                </div>

                <!-- Progress Steps -->
                <div class="flex items-center justify-center mb-8">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-semibold">1</div>
                            <span class="ml-2 text-sm font-medium text-indigo-600">Informations</span>
                        </div>
                        <div class="w-8 h-px bg-gray-300"></div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gray-300 text-gray-500 rounded-full flex items-center justify-center text-sm font-semibold">2</div>
                            <span class="ml-2 text-sm font-medium text-gray-500">Vérification</span>
                        </div>
                    </div>
                </div>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-red-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Veuillez corriger les erreurs suivantes :</h3>
                                <ul class="mt-2 text-sm text-red-700 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>• {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Form -->
                <form action="{{ route('register') }}" method="POST" class="space-y-6" id="registerForm">
                    @csrf
                    
                    <!-- Names -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-2">Prénom</label>
                            <input type="text" id="first_name" name="first_name" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 @error('first_name') border-red-300 @enderror"
                                   placeholder="Votre prénom" value="{{ old('first_name') }}">
                        </div>
                        <div class="form-group">
                            <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-2">Nom</label>
                            <input type="text" id="last_name" name="last_name" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 @error('last_name') border-red-300 @enderror"
                                   placeholder="Votre nom" value="{{ old('last_name') }}">
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Adresse email</label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 @error('email') border-red-300 @enderror"
                               placeholder="votre@email.com" value="{{ old('email') }}">
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Téléphone</label>
                        <input type="tel" id="phone" name="phone" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 @error('phone') border-red-300 @enderror"
                               placeholder="+225 XX XX XX XX" value="{{ old('phone') }}">
                    </div>

                    <!-- Country & Currency -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="country_code" class="block text-sm font-semibold text-gray-700 mb-2">Pays</label>
                            <select id="country_code" name="country_code" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 @error('country_code') border-red-300 @enderror">
                                <option value="">Sélectionnez</option>
                                @if(isset($countries))
                                    @foreach($countries as $country)
                                        <option value="{{ $country->code }}" {{ old('country_code') == $country->code ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="currency" class="block text-sm font-semibold text-gray-700 mb-2">Devise</label>
                            <select id="currency" name="currency" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 @error('currency') border-red-300 @enderror">
                                <option value="">Sélectionnez</option>
                                <option value="XOF" {{ old('currency') == 'XOF' ? 'selected' : '' }}>XOF (CFA)</option>
                                <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD (Dollar)</option>
                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR (Euro)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Passwords -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Mot de passe</label>
                            <div class="relative">
                                <input type="password" id="password" name="password" required
                                       class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 @error('password') border-red-300 @enderror"
                                       placeholder="Min. 8 caractères">
                                <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirmer</label>
                            <div class="relative">
                                <input type="password" id="password_confirmation" name="password_confirmation" required
                                       class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
                                       placeholder="Confirmez">
                                <button type="button" onclick="togglePassword('password_confirmation')" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Terms -->
                    <div class="flex items-start space-x-3">
                        <input type="checkbox" id="terms" name="terms" required
                               class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="terms" class="text-sm text-gray-700 leading-relaxed">
                            J'accepte les <a href="#" class="text-indigo-600 hover:text-indigo-700 font-semibold">conditions d'utilisation</a> et la <a href="#" class="text-indigo-600 hover:text-indigo-700 font-semibold">politique de confidentialité</a>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transform transition-all duration-200 hover:scale-105 shadow-lg hover:shadow-xl">
                        <span class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            Créer mon compte
                        </span>
                    </button>

                    <!-- Login Link -->
                    <div class="text-center">
                        <p class="text-gray-600">
                            Déjà un compte ?
                            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">
                                Se connecter
                            </a>
                        </p>
                    </div>
                </form>

                <!-- Security Badge -->
                <div class="mt-8 text-center">
                    <div class="inline-flex items-center px-4 py-2 bg-green-50 border border-green-200 rounded-full">
                        <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <span class="text-sm font-medium text-green-800">Données sécurisées SSL 256-bit</span>
                    </div>
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
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }
    .animate-blob { animation: blob 7s infinite; }
    .animation-delay-2000 { animation-delay: 2s; }
    .animation-delay-4000 { animation-delay: 4s; }
    .animate-float { animation: float 6s ease-in-out infinite; }
    .animate-float-delayed { animation: float 6s ease-in-out infinite 3s; }
    
    .form-group input:focus, .form-group select:focus {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
</style>
@endpush

@push('scripts')
<script>
function togglePassword(fieldId) {
    const input = document.getElementById(fieldId);
    const button = input.nextElementSibling;
    const icon = button.querySelector('svg');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
        `;
    } else {
        input.type = 'password';
        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
        `;
    }
}

// Form validation and enhancement
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const inputs = form.querySelectorAll('input, select');
    
    // Add real-time validation
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('border-red-300')) {
                validateField(this);
            }
        });
    });
    
    function validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        
        // Remove existing error styling
        field.classList.remove('border-red-300', 'border-green-300');
        
        // Basic validation
        if (field.hasAttribute('required') && !value) {
            isValid = false;
        }
        
        // Email validation
        if (field.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            isValid = emailRegex.test(value);
        }
        
        // Password confirmation
        if (field.name === 'password_confirmation' && value) {
            const password = document.getElementById('password').value;
            isValid = value === password;
        }
        
        // Apply styling
        if (value) {
            field.classList.add(isValid ? 'border-green-300' : 'border-red-300');
        }
    }
});
</script>
@endpush
@endsection
