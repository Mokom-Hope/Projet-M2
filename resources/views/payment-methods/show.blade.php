@extends('layouts.app')

@section('title', 'Détails de la méthode de paiement')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-display font-bold text-gray-900">Détails de la méthode</h1>
                <p class="text-gray-600 mt-1">Informations de votre méthode de paiement</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('payment-methods.edit', $paymentMethod) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Modifier
                </a>
                <a href="{{ route('payment-methods.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-xl text-sm font-medium text-white bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Retour
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <div class="glass rounded-2xl shadow-card p-8 border border-white/20 backdrop-blur-xl">
                <!-- Status Badge -->
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-display font-bold text-gray-900">Informations générales</h2>
                    <div class="flex items-center space-x-2">
                        @if($paymentMethod->is_default)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 border border-green-200">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Par défaut
                            </span>
                        @endif
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                            @if($paymentMethod->status === 'active') bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 border border-green-200
                            @else bg-gradient-to-r from-red-100 to-pink-100 text-red-800 border border-red-200 @endif">
                            {{ ucfirst($paymentMethod->status) }}
                        </span>
                    </div>
                </div>

                <!-- Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Type</label>
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-primary-100 to-primary-200 rounded-lg flex items-center justify-center mr-3">
                                    @if($paymentMethod->type === 'mobile_money')
                                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                    @elseif($paymentMethod->type === 'bank_account')
                                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $paymentMethod->type)) }}</p>
                                    <p class="text-sm text-gray-600">{{ $paymentMethod->provider }}</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nom du compte</label>
                            <p class="text-gray-900 font-medium">{{ $paymentMethod->account_name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Numéro de compte</label>
                            <p class="text-gray-900 font-mono">{{ $paymentMethod->getMaskedAccountNumber() }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Pays</label>
                            <div class="flex items-center">
                                <span class="text-2xl mr-2">{{ $paymentMethod->getCountryFlag() }}</span>
                                <p class="text-gray-900 font-medium">{{ $paymentMethod->country_code }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Ajouté le</label>
                            <p class="text-gray-900">{{ $paymentMethod->created_at->format('d/m/Y à H:i') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Dernière modification</label>
                            <p class="text-gray-900">{{ $paymentMethod->updated_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Metadata -->
                @if($paymentMethod->metadata && count($paymentMethod->metadata) > 0)
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-display font-bold text-gray-900 mb-4">Informations supplémentaires</h3>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($paymentMethod->metadata as $key => $value)
                                    <div>
                                        <dt class="text-sm font-semibold text-gray-700">{{ ucfirst(str_replace('_', ' ', $key)) }}</dt>
                                        <dd class="text-gray-900">{{ $value }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Actions -->
            <div class="glass rounded-2xl shadow-card p-6 border border-white/20 backdrop-blur-xl">
                <h3 class="text-lg font-display font-bold text-gray-900 mb-4">Actions</h3>
                <div class="space-y-3">
                    @if(!$paymentMethod->is_default)
                        <form method="POST" action="{{ route('payment-methods.default', $paymentMethod) }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center px-4 py-2 border border-green-300 rounded-xl text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Définir par défaut
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('payment-methods.edit', $paymentMethod) }}" 
                       class="w-full flex items-center justify-center px-4 py-2 border border-blue-300 rounded-xl text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Modifier
                    </a>

                    <form method="POST" action="{{ route('payment-methods.destroy', $paymentMethod) }}" 
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette méthode de paiement ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full flex items-center justify-center px-4 py-2 border border-red-300 rounded-xl text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>

            <!-- Security Info -->
            <div class="glass rounded-2xl shadow-card p-6 border border-white/20 backdrop-blur-xl">
                <h3 class="text-lg font-display font-bold text-gray-900 mb-4">Sécurité</h3>
                <div class="space-y-3">
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Données chiffrées
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Accès sécurisé
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Transactions rapides
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
