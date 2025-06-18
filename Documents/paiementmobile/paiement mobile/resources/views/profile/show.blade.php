@extends('layouts.app')

@section('title', 'Mon Profil')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header Section -->
    <div class="mb-8 animate-on-scroll">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-display font-bold text-gray-900 mb-1">Mon Profil</h1>
                <p class="text-gray-600">Gérez vos informations personnelles et paramètres de sécurité</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary-600 to-primary-700 text-white rounded-xl hover:from-primary-700 hover:to-primary-800 transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Modifier le profil
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="glass rounded-2xl shadow-card p-6 border border-white/20 backdrop-blur-xl animate-on-scroll">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-display font-bold text-gray-900">Informations personnelles</h2>
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-100 to-primary-200 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Prénom</label>
                        <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-200">
                            <span class="text-gray-900 font-medium">{{ $user->first_name }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nom</label>
                        <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-200">
                            <span class="text-gray-900 font-medium">{{ $user->last_name }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-200 flex items-center">
                            <span class="text-gray-900 font-medium">{{ $user->email }}</span>
                            @if($user->email_verified_at)
                                <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Vérifié
                                </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Téléphone</label>
                        <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-200">
                            <span class="text-gray-900 font-medium">{{ $user->phone ?? 'Non renseigné' }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Pays</label>
                        <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-200">
                            <span class="text-gray-900 font-medium">{{ $user->country->name ?? 'Non renseigné' }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Devise</label>
                        <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-200">
                            <span class="text-gray-900 font-medium">{{ $user->currency }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="glass rounded-2xl shadow-card p-6 border border-white/20 backdrop-blur-xl animate-on-scroll">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-display font-bold text-gray-900">Sécurité</h2>
                    <div class="w-10 h-10 bg-gradient-to-br from-red-100 to-red-200 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Password -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <div>
                            <h3 class="font-semibold text-gray-900">Mot de passe</h3>
                            <p class="text-sm text-gray-600">Dernière modification il y a {{ $user->updated_at->diffForHumans() }}</p>
                        </div>
                        <button onclick="showChangePasswordModal()" class="text-primary-600 hover:text-primary-700 font-medium text-sm transition-colors duration-200">
                            Modifier
                        </button>
                    </div>

                    <!-- Two Factor Authentication -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <div>
                            <h3 class="font-semibold text-gray-900">Authentification à deux facteurs</h3>
                            <p class="text-sm text-gray-600">
                                @if($user->two_factor_enabled)
                                    <span class="text-green-600">Activée</span> - Protection renforcée de votre compte
                                @else
                                    <span class="text-orange-600">Désactivée</span> - Recommandé pour plus de sécurité
                                @endif
                            </p>
                        </div>
                        @if($user->two_factor_enabled)
                            <form method="POST" action="{{ route('profile.two-factor.disable') }}">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-700 font-medium text-sm transition-colors duration-200">
                                    Désactiver
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('profile.two-factor.enable') }}">
                                @csrf
                                <button type="submit" class="text-primary-600 hover:text-primary-700 font-medium text-sm transition-colors duration-200">
                                    Activer
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Profile Photo -->
            <div class="glass rounded-2xl shadow-card p-6 border border-white/20 backdrop-blur-xl animate-on-scroll">
                <h3 class="text-lg font-display font-bold text-gray-900 mb-4">Photo de profil</h3>
                <div class="text-center">
                    @if($user->profile_photo)
                        <img class="mx-auto h-24 w-24 rounded-full object-cover border-4 border-primary-200 shadow-sm" src="{{ Storage::url($user->profile_photo) }}" alt="{{ $user->full_name }}">
                    @else
                        <div class="mx-auto h-24 w-24 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center shadow-sm">
                            <span class="text-white text-2xl font-bold">{{ substr($user->first_name, 0, 1) }}</span>
                        </div>
                    @endif
                    <p class="mt-2 text-sm text-gray-600">{{ $user->full_name }}</p>
                </div>
            </div>

            <!-- Account Stats -->
            <div class="glass rounded-2xl shadow-card p-6 border border-white/20 backdrop-blur-xl animate-on-scroll">
                <h3 class="text-lg font-display font-bold text-gray-900 mb-4">Statistiques du compte</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Membre depuis</span>
                        <span class="text-sm font-medium text-gray-900">{{ $user->created_at->format('M Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Statut du compte</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Actif
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Niveau de sécurité</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $user->two_factor_enabled ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                            {{ $user->two_factor_enabled ? 'Élevé' : 'Standard' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="glass rounded-2xl shadow-card p-6 border border-white/20 backdrop-blur-xl animate-on-scroll">
                <h3 class="text-lg font-display font-bold text-gray-900 mb-4">Actions rapides</h3>
                <div class="space-y-3">
                    <a href="{{ route('payment-methods.index') }}" class="flex items-center p-3 rounded-xl hover:bg-gray-50 transition-colors duration-200 group">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-200">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-900">Méthodes de paiement</span>
                    </a>
                    <a href="{{ route('transfers.history') }}" class="flex items-center p-3 rounded-xl hover:bg-gray-50 transition-colors duration-200 group">
                        <div class="w-8 h-8 bg-gradient-to-br from-purple-100 to-purple-200 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-200">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-900">Historique des transferts</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div id="changePasswordModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form method="POST" action="{{ route('profile.password') }}">
                @csrf
                <div class="bg-white px-6 pt-6 pb-4">
                    <h3 class="text-lg font-display font-bold text-gray-900 mb-4">Changer le mot de passe</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2">Mot de passe actuel</label>
                            <input type="password" name="current_password" id="current_password" required class="block w-full px-3 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="new_password" class="block text-sm font-semibold text-gray-700 mb-2">Nouveau mot de passe</label>
                            <input type="password" name="new_password" id="new_password" required class="block w-full px-3 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="new_password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirmer le nouveau mot de passe</label>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation" required class="block w-full px-3 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                    <button type="button" onclick="hideChangePasswordModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-primary-600 to-primary-700 rounded-xl hover:from-primary-700 hover:to-primary-800 transition-all duration-200">
                        Modifier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showChangePasswordModal() {
    document.getElementById('changePasswordModal').classList.remove('hidden');
}

function hideChangePasswordModal() {
    document.getElementById('changePasswordModal').classList.add('hidden');
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
