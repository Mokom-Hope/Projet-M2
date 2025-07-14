@extends('layouts.app')

@section('title', 'Récupérer de l\'argent')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header Section -->
    <div class="mb-8 animate-on-scroll">
        <div class="text-center">
            <div class="mx-auto w-16 h-16 bg-gradient-to-br from-secondary-500 to-secondary-700 rounded-2xl flex items-center justify-center shadow-glow mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-display font-bold text-gray-900 mb-2">Récupérer de l'argent</h1>
            <p class="text-gray-600">Entrez votre code de transfert pour récupérer votre argent</p>
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

                <!-- Step 1: Transfer Code -->
                <div class="space-y-6 mb-8">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">1</div>
                        <h2 class="text-xl font-display font-bold text-gray-900">Code de transfert</h2>
                    </div>

                    <div>
                        <label for="transfer_code" class="block text-sm font-semibold text-gray-700 mb-2">
                            Code de transfert
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-secondary-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                </svg>
                            </div>
                            <input id="transfer_code" name="transfer_code" type="text" required 
                                   class="block w-full pl-10 pr-3 py-4 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-secondary-500 focus:border-transparent transition-all duration-200 bg-white/70 backdrop-blur-sm hover:bg-white/90 text-center text-lg font-mono tracking-wider" 
                                   placeholder="XXXXXXXX" value="{{ old('transfer_code') }}" maxlength="8" style="text-transform: uppercase;">
                        </div>
                        <p class="mt-2 text-sm text-gray-600">Le code de 8 caractères que vous avez reçu</p>
                    </div>

                    <button type="button" onclick="checkTransferCode()" class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-secondary-600 to-secondary-700 hover:from-secondary-700 hover:to-secondary-800 transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Vérifier le code
                    </button>
                </div>

                <!-- Transfer Info (Hidden initially) -->
                <div id="transferInfo" class="hidden space-y-6">
                    <div class="border-t border-gray-200 pt-6">
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4 mb-6">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-green-800 font-medium">Code valide ! Transfert trouvé</span>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-6 mb-6">
                            <h3 class="font-semibold text-gray-900 mb-4">Détails du transfert</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <span class="text-sm text-gray-600">Expéditeur</span>
                                    <p class="font-medium text-gray-900" id="senderName">-</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600">Montant</span>
                                    <p class="font-medium text-gray-900" id="transferAmount">-</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600">Question de sécurité</span>
                                    <p class="font-medium text-gray-900" id="securityQuestion">-</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600">Expire le</span>
                                    <p class="font-medium text-gray-900" id="expiresAt">-</p>
                                </div>
                            </div>
                        </div>

                        <!-- Claim Form -->
                        <form method="POST" action="{{ route('transfers.claim.store') }}" class="space-y-6">
                            @csrf
                            <input type="hidden" name="transfer_code" id="hiddenTransferCode">

                            <!-- Step 2: Recipient Info -->
                            <div class="space-y-4">
                                <div class="flex items-center mb-4">
                                    <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">2</div>
                                    <h2 class="text-xl font-display font-bold text-gray-900">Vos informations</h2>
                                </div>

                                <div>
                                    <label for="recipient_identifier" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Votre email ou numéro de téléphone
                                    </label>
                                    <input id="recipient_identifier" name="recipient_identifier" type="text" required 
                                           class="block w-full px-3 py-4 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-white/70 backdrop-blur-sm hover:bg-white/90" 
                                           placeholder="votre@email.com ou +225 XX XX XX XX">
                                </div>

                                <div>
                                    <label for="security_answer" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Réponse à la question de sécurité
                                    </label>
                                    <input id="security_answer" name="security_answer" type="text" required 
                                           class="block w-full px-3 py-4 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-white/70 backdrop-blur-sm hover:bg-white/90" 
                                           placeholder="Votre réponse">
                                </div>
                            </div>

                            <!-- Step 3: Payment Method -->
                            <div class="space-y-4">
                                <div class="flex items-center mb-4">
                                    <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">3</div>
                                    <h2 class="text-xl font-display font-bold text-gray-900">Méthode de réception</h2>
                                </div>

                                <div>
                                    <label for="country_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Pays
                                    </label>
                                    <select id="country_id" name="country_id" required 
                                            class="block w-full px-3 py-4 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 bg-white/70 backdrop-blur-sm hover:bg-white/90">
                                        <option value="">Sélectionnez votre pays</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="payment_method_type" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Type de méthode
                                    </label>
                                    <select id="payment_method_type" name="payment_method_type" required 
                                            class="block w-full px-3 py-4 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 bg-white/70 backdrop-blur-sm hover:bg-white/90">
                                        <option value="">Sélectionnez le type</option>
                                        <option value="mobile_money" {{ old('payment_method_type') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                        <option value="bank_account" {{ old('payment_method_type') == 'bank_account' ? 'selected' : '' }}>Compte bancaire</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="payment_method_provider" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Fournisseur
                                    </label>
                                    <select id="payment_method_provider" name="payment_method_provider" required 
                                            class="block w-full px-3 py-4 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 bg-white/70 backdrop-blur-sm hover:bg-white/90">
                                        <option value="">Sélectionnez le fournisseur</option>
                                        <!-- Options will be populated by JavaScript -->
                                    </select>
                                </div>

                                <div>
                                    <label for="account_number" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Numéro de compte
                                    </label>
                                    <input id="account_number" name="account_number" type="text" required 
                                           class="block w-full px-3 py-4 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 bg-white/70 backdrop-blur-sm hover:bg-white/90" 
                                           placeholder="Votre numéro de compte">
                                </div>

                                <div>
                                    <label for="account_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nom du titulaire
                                    </label>
                                    <input id="account_name" name="account_name" type="text" required 
                                           class="block w-full px-3 py-4 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 bg-white/70 backdrop-blur-sm hover:bg-white/90" 
                                           placeholder="Nom complet du titulaire">
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-6">
                                <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent text-base font-semibold rounded-xl text-white bg-gradient-to-r from-green-600 via-green-700 to-green-800 hover:from-green-700 hover:via-green-800 hover:to-green-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-300 transform hover:scale-105 hover:shadow-glow bg-size-200 animate-gradient">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                    Récupérer l'argent
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- Instructions -->
            <div class="glass rounded-2xl shadow-card p-6 border border-white/20 backdrop-blur-xl animate-on-scroll">
                <h3 class="text-lg font-display font-bold text-gray-900 mb-4">Comment récupérer ?</h3>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center mr-3 mt-0.5 text-blue-600 font-bold text-sm">1</div>
                        <div>
                            <h4 class="font-semibold text-gray-900 text-sm">Entrez le code</h4>
                            <p class="text-sm text-gray-600">Saisissez le code de 8 caractères reçu par email ou SMS</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-gradient-to-br from-purple-100 to-purple-200 rounded-lg flex items-center justify-center mr-3 mt-0.5 text-purple-600 font-bold text-sm">2</div>
                        <div>
                            <h4 class="font-semibold text-gray-900 text-sm">Vérifiez votre identité</h4>
                            <p class="text-sm text-gray-600">Confirmez votre email/téléphone et répondez à la question</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-gradient-to-br from-green-100 to-green-200 rounded-lg flex items-center justify-center mr-3 mt-0.5 text-green-600 font-bold text-sm">3</div>
                        <div>
                            <h4 class="font-semibold text-gray-900 text-sm">Recevez l'argent</h4>
                            <p class="text-sm text-gray-600">Choisissez votre méthode de réception préférée</p>
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
                        Toutes les transactions sont sécurisées
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Vos données sont protégées
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Support 24h/7j disponible
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function checkTransferCode() {
    const code = document.getElementById('transfer_code').value.trim().toUpperCase();
    
    if (code.length !== 8) {
        alert('Le code doit contenir 8 caractères');
        return;
    }

    // API call to check transfer code
    fetch(`{{ route('transfers.info') }}?transfer_code=${code}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Populate transfer info
                document.getElementById('senderName').textContent = data.data.sender_name;
                document.getElementById('transferAmount').textContent = data.data.amount;
                document.getElementById('securityQuestion').textContent = data.data.security_question;
                document.getElementById('expiresAt').textContent = data.data.expires_at;
                document.getElementById('hiddenTransferCode').value = code;
                
                // Show transfer info section
                document.getElementById('transferInfo').classList.remove('hidden');
                
                // Scroll to the form
                document.getElementById('transferInfo').scrollIntoView({ behavior: 'smooth' });
            } else {
                alert(data.message || 'Code de transfert invalide');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de la vérification du code');
        });
}

// Auto-format transfer code input
document.getElementById('transfer_code').addEventListener('input', function(e) {
    this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
});

// Update providers based on payment method type
document.getElementById('payment_method_type').addEventListener('change', function() {
    const type = this.value;
    const providerSelect = document.getElementById('payment_method_provider');
    
    // Clear current options
    providerSelect.innerHTML = '<option value="">Sélectionnez le fournisseur</option>';
    
    if (type === 'mobile_money') {
        const providers = ['Orange Money', 'MTN Mobile Money', 'Moov Money', 'Wave'];
        providers.forEach(provider => {
            const option = document.createElement('option');
            option.value = provider.toLowerCase().replace(/\s+/g, '_');
            option.textContent = provider;
            providerSelect.appendChild(option);
        });
    } else if (type === 'bank_account') {
        const providers = ['Ecobank', 'UBA', 'SGBCI', 'BICICI', 'NSIA Banque'];
        providers.forEach(provider => {
            const option = document.createElement('option');
            option.value = provider.toLowerCase().replace(/\s+/g, '_');
            option.textContent = provider;
            providerSelect.appendChild(option);
        });
    }
});

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
