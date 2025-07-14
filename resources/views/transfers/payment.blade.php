@extends('layouts.app')

@section('title', 'Paiement du transfert')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header Section -->
    <div class="mb-8 animate-on-scroll">
        <div class="text-center">
            <div class="mx-auto w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl flex items-center justify-center shadow-glow mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-display font-bold text-gray-900 mb-2">Finaliser le paiement</h1>
            <p class="text-gray-600">Complétez votre transfert en effectuant le paiement</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Payment Form -->
        <div class="lg:col-span-2">
            <div class="glass rounded-2xl shadow-card-hover p-8 border border-white/20 backdrop-blur-xl animate-on-scroll">
                <!-- Transfer Summary -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6 mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Résumé du transfert</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-sm text-gray-600">Montant</span>
                            <p class="font-semibold text-gray-900">{{ number_format($transfer->amount, 0, ',', ' ') }} {{ $transfer->currency }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">Frais</span>
                            <p class="font-semibold text-gray-900">{{ number_format($transfer->fees, 0, ',', ' ') }} {{ $transfer->currency }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">Total à payer</span>
                            <p class="font-bold text-xl text-primary-600">{{ number_format($transfer->total_amount, 0, ',', ' ') }} {{ $transfer->currency }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">Destinataire</span>
                            <p class="font-semibold text-gray-900">{{ $transfer->recipient_email ?? $transfer->recipient_phone }}</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="space-y-6">
                    <h3 class="text-xl font-display font-bold text-gray-900">Choisir une méthode de paiement</h3>
                    
                    <!-- Mobile Money -->
                    <div class="space-y-4">
                        <h4 class="font-semibold text-gray-900">Mobile Money</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <button onclick="selectPaymentMethod('mtn')" class="payment-method-btn p-4 border-2 border-gray-200 rounded-xl hover:border-green-300 transition-all duration-200 text-left">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                                        <span class="text-yellow-600 font-bold">M</span>
                                    </div>
                                    <div>
                                        <h5 class="font-semibold text-gray-900">MTN Mobile Money</h5>
                                        <p class="text-sm text-gray-600">Paiement via MTN MoMo</p>
                                    </div>
                                </div>
                            </button>

                            <button onclick="selectPaymentMethod('orange')" class="payment-method-btn p-4 border-2 border-gray-200 rounded-xl hover:border-orange-300 transition-all duration-200 text-left">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                                        <span class="text-orange-600 font-bold">O</span>
                                    </div>
                                    <div>
                                        <h5 class="font-semibold text-gray-900">Orange Money</h5>
                                        <p class="text-sm text-gray-600">Paiement via Orange Money</p>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <div id="paymentForm" class="hidden space-y-4">
                        <div class="bg-gray-50 rounded-xl p-6">
                            <h4 class="font-semibold text-gray-900 mb-4">Informations de paiement</h4>
                            <div class="space-y-4">
                                <div>
                                    <label for="phone_number" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Numéro de téléphone
                                    </label>
                                    <input id="phone_number" type="tel" required 
                                           class="block w-full px-3 py-3 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200" 
                                           placeholder="237XXXXXXXXX">
                                </div>
                                
                                <button onclick="processPayment()" class="w-full flex justify-center py-4 px-4 border border-transparent text-base font-semibold rounded-xl text-white bg-gradient-to-r from-primary-600 via-primary-700 to-primary-800 hover:from-primary-700 hover:via-primary-800 hover:to-primary-900 transition-all duration-300 transform hover:scale-105">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                    Payer {{ number_format($transfer->total_amount, 0, ',', ' ') }} {{ $transfer->currency }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- Security -->
            <div class="glass rounded-2xl shadow-card p-6 border border-white/20 backdrop-blur-xl animate-on-scroll">
                <h3 class="text-lg font-display font-bold text-gray-900 mb-4">Paiement sécurisé</h3>
                <div class="space-y-3">
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Chiffrement SSL 256-bit
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Données protégées
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Paiement instantané
                    </div>
                </div>
            </div>

            <!-- Help -->
            <div class="glass rounded-2xl shadow-card p-6 border border-white/20 backdrop-blur-xl animate-on-scroll">
                <h3 class="text-lg font-display font-bold text-gray-900 mb-4">Besoin d'aide ?</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Notre équipe support est disponible 24h/7j pour vous aider.
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
<script src="https://js.notchpay.co/v1/notchpay.js"></script>
<script>
let selectedMethod = null;

function selectPaymentMethod(method) {
    selectedMethod = method;
    
    // Reset all buttons
    document.querySelectorAll('.payment-method-btn').forEach(btn => {
        btn.classList.remove('border-primary-500', 'bg-primary-50');
        btn.classList.add('border-gray-200');
    });
    
    // Highlight selected button
    event.target.closest('.payment-method-btn').classList.remove('border-gray-200');
    event.target.closest('.payment-method-btn').classList.add('border-primary-500', 'bg-primary-50');
    
    // Show payment form
    document.getElementById('paymentForm').classList.remove('hidden');
}

function processPayment() {
    if (!selectedMethod) {
        alert('Veuillez sélectionner une méthode de paiement');
        return;
    }
    
    const phoneNumber = document.getElementById('phone_number').value;
    if (!phoneNumber) {
        alert('Veuillez entrer votre numéro de téléphone');
        return;
    }
    
    // Initialiser NotchPay avec notre interface
    const notchpay = new NotchPay({
        public_key: '{{ config("payment.notchpay.public_key") }}',
        amount: {{ $transfer->total_amount }},
        currency: '{{ $transfer->currency }}',
        reference: '{{ $transfer->payment_reference }}',
        customer: {
            email: '{{ $transfer->sender->email }}',
            name: '{{ $transfer->sender->full_name }}',
            phone: phoneNumber
        },
        callback_url: '{{ route("payment.callback") }}',
        return_url: '{{ route("transfers.show", $transfer) }}',
        onSuccess: function(response) {
            // Rediriger vers la page de succès
            window.location.href = '{{ route("transfers.show", $transfer) }}?payment=success';
        },
        onError: function(error) {
            alert('Erreur de paiement: ' + error.message);
        },
        onClose: function() {
            console.log('Paiement fermé par l\'utilisateur');
        }
    });
    
    // Ouvrir le widget de paiement
    notchpay.open();
}

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
