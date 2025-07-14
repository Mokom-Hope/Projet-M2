@extends('layouts.app')

@section('title', 'Modifier la méthode de paiement')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-display font-bold text-gray-900">Modifier la méthode</h1>
                <p class="text-gray-600 mt-1">Mettre à jour les informations de votre méthode de paiement</p>
            </div>
            <a href="{{ route('payment-methods.show', $paymentMethod) }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="glass rounded-2xl shadow-card p-8 border border-white/20 backdrop-blur-xl">
        <form method="POST" action="{{ route('payment-methods.update', $paymentMethod) }}">
            @csrf
            @method('PUT')

            <!-- Current Method Info -->
            <div class="mb-8 p-4 bg-gray-50 rounded-xl">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-primary-100 to-primary-200 rounded-xl flex items-center justify-center mr-4">
                        <span class="text-xl">{{ $paymentMethod->getTypeIcon() }}</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">{{ $paymentMethod->provider }}</p>
                        <p class="text-gray-600">{{ $paymentMethod->getMaskedAccountNumber() }}</p>
                        <p class="text-sm text-gray-500">{{ $paymentMethod->getCountryName() }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Account Name -->
                <div class="md:col-span-2">
                    <label for="account_name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nom du compte <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="account_name" 
                           name="account_name" 
                           value="{{ old('account_name', $paymentMethod->account_name) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('account_name') border-red-500 @enderror"
                           placeholder="Nom complet du titulaire du compte"
                           required>
                    @error('account_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Number -->
                <div class="md:col-span-2">
                    <label for="account_number" class="block text-sm font-semibold text-gray-700 mb-2">
                        Numéro de compte <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="account_number" 
                           name="account_number" 
                           value="{{ old('account_number', $paymentMethod->account_number) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('account_number') border-red-500 @enderror"
                           placeholder="Numéro de compte ou téléphone"
                           required>
                    @error('account_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Provider -->
                <div>
                    <label for="provider" class="block text-sm font-semibold text-gray-700 mb-2">
                        Fournisseur <span class="text-red-500">*</span>
                    </label>
                    <select id="provider" 
                            name="provider" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('provider') border-red-500 @enderror"
                            required>
                        <option value="">Sélectionner un fournisseur</option>
                        @if($paymentMethod->type === 'mobile_money')
                            <option value="Orange Money" {{ old('provider', $paymentMethod->provider) === 'Orange Money' ? 'selected' : '' }}>Orange Money</option>
                            <option value="MTN Mobile Money" {{ old('provider', $paymentMethod->provider) === 'MTN Mobile Money' ? 'selected' : '' }}>MTN Mobile Money</option>
                            <option value="Moov Money" {{ old('provider', $paymentMethod->provider) === 'Moov Money' ? 'selected' : '' }}>Moov Money</option>
                            <option value="Wave" {{ old('provider', $paymentMethod->provider) === 'Wave' ? 'selected' : '' }}>Wave</option>
                        @elseif($paymentMethod->type === 'bank_account')
                            <option value="Ecobank" {{ old('provider', $paymentMethod->provider) === 'Ecobank' ? 'selected' : '' }}>Ecobank</option>
                            <option value="UBA" {{ old('provider', $paymentMethod->provider) === 'UBA' ? 'selected' : '' }}>UBA</option>
                            <option value="SGBCI" {{ old('provider', $paymentMethod->provider) === 'SGBCI' ? 'selected' : '' }}>SGBCI</option>
                            <option value="BICICI" {{ old('provider', $paymentMethod->provider) === 'BICICI' ? 'selected' : '' }}>BICICI</option>
                        @else
                            <option value="Visa" {{ old('provider', $paymentMethod->provider) === 'Visa' ? 'selected' : '' }}>Visa</option>
                            <option value="Mastercard" {{ old('provider', $paymentMethod->provider) === 'Mastercard' ? 'selected' : '' }}>Mastercard</option>
                        @endif
                    </select>
                    @error('provider')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                        Statut
                    </label>
                    <select id="status" 
                            name="status" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('status') border-red-500 @enderror">
                        <option value="active" {{ old('status', $paymentMethod->status) === 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ old('status', $paymentMethod->status) === 'inactive' ? 'selected' : '' }}>Inactif</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Default Method -->
            <div class="mt-6">
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="is_default" 
                           name="is_default" 
                           value="1"
                           {{ old('is_default', $paymentMethod->is_default) ? 'checked' : '' }}
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="is_default" class="ml-2 block text-sm text-gray-700">
                        Définir comme méthode par défaut
                    </label>
                </div>
                @error('is_default')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="mt-8 flex items-center justify-end space-x-4">
                <a href="{{ route('payment-methods.show', $paymentMethod) }}" 
                   class="px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-3 border border-transparent rounded-xl text-sm font-medium text-white bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
