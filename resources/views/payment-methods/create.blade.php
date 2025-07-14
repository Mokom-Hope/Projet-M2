@extends('layouts.app')

@section('title', 'Ajouter une méthode de paiement')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header Section -->
    <div class="mb-8 animate-on-scroll">
        <div class="flex items-center space-x-4">
            <a href="{{ route('payment-methods.index') }}" class="p-2 rounded-xl bg-gray-100 hover:bg-gray-200 transition-colors duration-200">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-display font-bold text-gray-900">Ajouter une méthode de paiement</h1>
                <p class="text-gray-600">Configurez votre compte bancaire ou portefeuille mobile</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="glass rounded-2xl shadow-card-hover p-8 border border-white/20 backdrop-blur-xl animate-on-scroll">
                @if ($errors->any())
                    <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 mr-3 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <ul class="space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li class="text-sm">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('payment-methods.store') }}" class="space-y-6">
                    @csrf

                    <!-- Step 1: Type Selection -->
                    <div class="space-y-4">
                        <div class="flex items-center mb-4">
                            <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">1</div>
                            <h2 class="text-xl font-display font-bold text-gray-900">Type de méthode</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="mobile_money" class="sr-only peer" checked>
                                <div class="p-6 border-2 border-gray-200 rounded-xl hover:border-green-300 peer-checked:border-green-500 peer-checked:bg-green-50 transition-all duration-200">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-12 bg-gradient-to-br from-green-100 to-green-200 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-900">Mobile Money</h3>
                                            <p class="text-sm text-gray-600">MTN, Orange, Express Union</p>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="bank_account" class="sr-only peer">
                                <div class="p-6 border-2 border-gray-200 rounded-xl hover:border-blue-300 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all duration-200">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-900">Compte bancaire</h3>
                                            <p class="text-sm text-gray-600">Banques traditionnelles</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Step 2: Provider Selection -->
                    <div class="space-y-4">
                        <div class="flex items-center mb-4">
                            <div class="w-8 h-8 bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">2</div>
                            <h2 class="text-xl font-display font-bold text-gray-900">Fournisseur</h2>
                        </div>

                        <div id="mobile_money_providers" class="provider-section">
                            <label for="provider_mobile" class="block text-sm font-semibold text-gray-700 mb-2">Opérateur mobile</label>
                            <select id="provider_mobile" name="provider" class="block w-full px-3 py-4 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-secondary-500 focus:border-transparent transition-all duration-200 bg-white/70 backdrop-blur-sm hover:bg-white/90">
                                <option value="">Sélectionnez un opérateur</option>
                                <option value="MTN" {{ old('provider') == 'MTN' ? 'selected' : '' }}>MTN Mobile Money</option>
                                <option value="ORANGE" {{ old('provider') == 'ORANGE' ? 'selected' : '' }}>Orange Money</option>
                                <option value="EXPRESS_UNION" {{ old('provider') == 'EXPRESS_UNION' ? 'selected' : '' }}>Express Union Mobile</option>
                            </select>
                        </div>

                        <div id="bank_providers" class="provider-section hidden">
                            <label for="provider_bank" class="block text-sm font-semibold text-gray-700 mb-2">Banque</label>
                            <select id="provider_bank" name="provider_bank" class="block w-full px-3 py-4 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-secondary-500 focus:border-transparent transition-all duration-200 bg-white/70 backdrop-blur-sm hover:bg-white/90">
                                <option value="">Sélectionnez une banque</option>
                                <option value="AFRILAND" {{ old('provider') == 'AFRILAND' ? 'selected' : '' }}>Afriland First Bank</option>
                                <option value="BICEC" {{ old('provider') == 'BICEC' ? 'selected' : '' }}>BICEC</option>
                                <option value="UBA" {{ old('provider') == 'UBA' ? 'selected' : '' }}>UBA Cameroun</option>
                                <option value="SGBC" {{ old('provider') == 'SGBC' ? 'selected' : '' }}>Société Générale</option>
                                <option value="ECOBANK" {{ old('provider') == 'ECOBANK' ? 'selected' : '' }}>Ecobank</option>
                            </select>
                        </div>
                    </div>

                    <!-- Step 3: Account Details -->
                    <div class="space-y-4">
                        <div class="flex items-center mb-4">
                            <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">3</div>
                            <h2 class="text-xl font-display font-bold text-gray-900">Détails du compte</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="account_name" class="block text-sm font-semibold text-gray-700 mb-2">Nom du titulaire</label>
                                <input id="account_name" name="account_name" type="text" required 
                                       class="block w-full px-3 py-4 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 bg-white/70 backdrop-blur-sm hover:bg-white/90" 
                                       placeholder="Nom complet du titulaire" value="{{ old('account_name') }}">
                            </div>

                            <div>
                                <label for="account_number" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <span id="account_number_label">Numéro de téléphone</span>
                                </label>
                                <input id="account_number" name="account_number" type="text" required 
                                       class="block w-full px-3 py-4 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 bg-white/70 backdrop-blur-sm hover:bg-white/90" 
                                       placeholder="Ex: 237XXXXXXXXX" value="{{ old('account_number') }}">
                            </div>
                        </div>

                        <div>
                            <label for="country_id" class="block text-sm font-semibold text-gray-700 mb-2">Pays</label>
                            <select id="country_id" name="country_id" required 
                                    class="block w-full px-3 py-4 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 bg-white/70 backdrop-blur-sm hover:bg-white/90">
                                <option value="">Sélectionnez un pays</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="currency" class="block text-sm font-semibold text-gray-700 mb-2">Devise</label>
                            <select id="currency" name="currency" required 
                                    class="block w-full px-3 py-4 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 bg-white/70 backdrop-blur-sm hover:bg-white/90">
                                <option value="XAF" {{ old('currency') == 'XAF' ? 'selected' : '' }}>XAF (Franc CFA)</option>
                                <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD (Dollar)</option>
                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR (Euro)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Step 4: Settings -->
                    <div class="space-y-4">
                        <div class="flex items-center mb-4">
                            <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">4</div>
                            <h2 class="text-xl font-display font-bold text-gray-900">Paramètres</h2>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
                            <div>
                                <h3 class="font-semibold text-gray-900">Méthode par défaut</h3>
                                <p class="text-sm text-gray-600">Utiliser cette méthode par défaut pour les transferts</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_default" value="1" class="sr-only peer" {{ old('is_default') ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-6">
                        <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent text-base font-semibold rounded-xl text-white bg-gradient-to-r from-primary-600 via-primary-700 to-primary-800 hover:from-primary-700 hover:via-primary-800 hover:to-primary-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-300 transform hover:scale-105 hover:shadow-glow bg-size-200 animate-gradient">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Ajouter la méthode de paiement
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- Supported Methods -->
            <div class="glass rounded-2xl shadow-card p-6 border border-white/20 backdrop-blur-xl animate-on-scroll">
                <h3 class="text-lg font-display font-bold text-gray-900 mb-4">Méthodes supportées</h3>
                <div class="space-y-4">
                    <div>
                        <h4 class="font-semibold text-gray-900 text-sm mb-2">Mobile Money</h4>
                        <div class="space-y-2">
                            <div class="flex items-center text-sm text-gray-600">
                                <div class="w-6 h-6 bg-yellow-100 rounded-lg flex items-center justify-center mr-2">
                                    <span class="text-yellow-600 font-bold text-xs">M</span>
                                </div>
                                MTN Mobile Money
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <div class="w-6 h-6 bg-orange-100 rounded-lg flex items-center justify-center mr-2">
                                    <span class="text-orange-600 font-bold text-xs">O</span>
                                </div>
                                Orange Money
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center mr-2">
                                    <span class="text-blue-600 font-bold text-xs">E</span>
                                </div>
                                Express Union Mobile
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 text-sm mb-2">Banques</h4>
                        <div class="space-y-2">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Toutes les banques camerounaises
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="glass rounded-2xl shadow-card p-6 border border-white/20 backdrop-blur-xl animate-on-scroll">
                <h3 class="text-lg font-display font-bold text-gray-900 mb-4">Sécurité</h3>
                <div class="space-y-3">
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Vos données sont chiffrées
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Conformité PCI DSS
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Vérification automatique
                    </div>
                </div>
            </div>

            <!-- Help -->
            <div class="glass rounded-2xl shadow-card p-6 border border-white/20 backdrop-blur-xl animate-on-scroll">
                <h3 class="text-lg font-display font-bold text-gray-900 mb-4">Besoin d'aide ?</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Notre équipe support est disponible pour vous aider à configurer vos méthodes de paiement.
                </p>
                <a href="#" class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    Contacter le support
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const mobileProviders = document.getElementById('mobile_money_providers');
    const bankProviders = document.getElementById('bank_providers');
    const accountNumberLabel = document.getElementById('account_number_label');
    const accountNumberInput = document.getElementById('account_number');

    function updateProviderSection() {
        const selectedType = document.querySelector('input[name="type"]:checked').value;
        
        if (selectedType === 'mobile_money') {
            mobileProviders.classList.remove('hidden');
            bankProviders.classList.add('hidden');
            accountNumberLabel.textContent = 'Numéro de téléphone';
            accountNumberInput.placeholder = 'Ex: 237XXXXXXXXX';
            
            // Clear bank provider
            document.getElementById('provider_bank').value = '';
        } else {
            mobileProviders.classList.add('hidden');
            bankProviders.classList.remove('hidden');
            accountNumberLabel.textContent = 'Numéro de compte';
            accountNumberInput.placeholder = 'Ex: 1234567890';
            
            // Clear mobile provider
            document.getElementById('provider_mobile').value = '';
        }
    }

    typeRadios.forEach(radio => {
        radio.addEventListener('change', updateProviderSection);
    });

    // Initialize on page load
    updateProviderSection();

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

    // Mapping pays -> devise
    const countryCurrencyMap = {
        'CM': 'XAF',  // Cameroun
        'CI': 'XOF',  // Côte d'Ivoire
        'SN': 'XOF',  // Sénégal
        'ML': 'XOF',  // Mali
        'BF': 'XOF',  // Burkina Faso
        'NE': 'XOF',  // Niger
        'TG': 'XOF',  // Togo
        'BJ': 'XOF',  // Bénin
        'GN': 'GNF',  // Guinée
        'FR': 'EUR',  // France
        'US': 'USD',  // États-Unis
        'CA': 'CAD',  // Canada
        'GB': 'GBP'   // Royaume-Uni
    };

    // Fonction pour mettre à jour la devise
    function updateCurrency() {
        const countrySelect = document.getElementById('country_id');
        const currencySelect = document.getElementById('currency');
        
        if (countrySelect && currencySelect) {
            const selectedOption = countrySelect.options[countrySelect.selectedIndex];
            const countryCode = selectedOption.getAttribute('data-code');
            
            if (countryCode && countryCurrencyMap[countryCode]) {
                currencySelect.value = countryCurrencyMap[countryCode];
            }
        }
    }

    // Ajouter l'attribut data-code aux options de pays
    const countrySelect = document.getElementById('country_id');
    if (countrySelect) {
        // Ajouter les codes pays aux options (vous devrez adapter selon vos données)
        const options = countrySelect.querySelectorAll('option');
        options.forEach(option => {
            const countryName = option.textContent.toLowerCase();
            if (countryName.includes('cameroun')) option.setAttribute('data-code', 'CM');
            if (countryName.includes('côte d\'ivoire')) option.setAttribute('data-code', 'CI');
            if (countryName.includes('sénégal')) option.setAttribute('data-code', 'SN');
            if (countryName.includes('france')) option.setAttribute('data-code', 'FR');
            // Ajoutez d'autres mappings selon vos besoins
        });
        
        // Écouter les changements
        countrySelect.addEventListener('change', updateCurrency);
    }
});
</script>
@endpush
@endsection
