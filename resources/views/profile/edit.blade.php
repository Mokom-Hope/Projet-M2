@extends('layouts.app')

@section('title', 'Modifier le Profil')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header Section -->
    <div class="mb-8 animate-on-scroll">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-display font-bold text-gray-900 mb-1">Modifier le Profil</h1>
                <p class="text-gray-600">Mettez à jour vos informations personnelles</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('profile.show') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Retour au profil
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Personal Information Card -->
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
                        <!-- First Name -->
                        <div class="form-group">
                            <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Prénom <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       name="first_name" 
                                       id="first_name" 
                                       value="{{ old('first_name', $user->first_name) }}"
                                       required
                                       class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 @error('first_name') border-red-300 @enderror">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div class="form-group">
                            <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Nom <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       name="last_name" 
                                       id="last_name" 
                                       value="{{ old('last_name', $user->last_name) }}"
                                       required
                                       class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 @error('last_name') border-red-300 @enderror">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       value="{{ old('email', $user->email) }}"
                                       required
                                       class="block w-full px-4 py-3 pl-10 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 @error('email') border-red-300 @enderror">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="form-group">
                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                Téléphone <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="tel" 
                                       name="phone" 
                                       id="phone" 
                                       value="{{ old('phone', $user->phone) }}"
                                       required
                                       class="block w-full px-4 py-3 pl-10 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 @error('phone') border-red-300 @enderror">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Country -->
                        <div class="form-group md:col-span-2">
                            <label for="country_code" class="block text-sm font-semibold text-gray-700 mb-2">
                                Pays <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="country_code" 
                                        id="country_code" 
                                        required
                                        class="block w-full px-4 py-3 pl-10 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 @error('country_code') border-red-300 @enderror">
                                    <option value="">Sélectionner un pays</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->code }}" 
                                                {{ old('country_code', $user->country_code) == $country->code ? 'selected' : '' }}>
                                            {{ $country->name }} ({{ $country->currency }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('country_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Photo Upload Card -->
                <div class="glass rounded-2xl shadow-card p-6 border border-white/20 backdrop-blur-xl animate-on-scroll">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-display font-bold text-gray-900">Photo de profil</h2>
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="flex items-center space-x-6">
                        <!-- Current Photo -->
                        <div class="flex-shrink-0">
                            @if($user->profile_photo)
                                <img class="h-20 w-20 rounded-full object-cover border-4 border-primary-200 shadow-sm" 
                                     src="{{ Storage::url($user->profile_photo) }}" 
                                     alt="{{ $user->full_name }}"
                                     id="current-photo">
                            @else
                                <div class="h-20 w-20 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center shadow-sm" id="current-photo">
                                    <span class="text-white text-xl font-bold">{{ substr($user->first_name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Upload Input -->
                        <div class="flex-1">
                            <label for="profile_photo" class="block text-sm font-semibold text-gray-700 mb-2">
                                Nouvelle photo (optionnel)
                            </label>
                            <input type="file" 
                                   name="profile_photo" 
                                   id="profile_photo" 
                                   accept="image/jpeg,image/png,image/jpg"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition-all duration-200">
                            <p class="mt-1 text-xs text-gray-500">JPG, PNG jusqu'à 2MB</p>
                            @error('profile_photo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('profile.show') }}" 
                       class="px-6 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-all duration-200 transform hover:scale-105">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-primary-600 to-primary-700 rounded-xl hover:from-primary-700 hover:to-primary-800 transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Sauvegarder les modifications
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Security Tips -->
            <div class="glass rounded-2xl shadow-card p-6 border border-white/20 backdrop-blur-xl animate-on-scroll">
                <h3 class="text-lg font-display font-bold text-gray-900 mb-4">Conseils de sécurité</h3>
                <div class="space-y-3">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mt-0.5">
                            <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-600">Utilisez une adresse email valide et accessible</p>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mt-0.5">
                            <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-600">Vérifiez que votre numéro de téléphone est correct</p>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mt-0.5">
                            <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-600">Gardez vos informations à jour pour la sécurité</p>
                    </div>
                </div>
            </div>

            <!-- Account Status -->
            <div class="glass rounded-2xl shadow-card p-6 border border-white/20 backdrop-blur-xl animate-on-scroll">
                <h3 class="text-lg font-display font-bold text-gray-900 mb-4">Statut du compte</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Email vérifié</span>
                        @if($user->email_verified_at)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Vérifié
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                En attente
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Statut KYC</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $user->kyc_status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                            {{ ucfirst($user->kyc_status ?? 'pending') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Preview uploaded image
document.getElementById('profile_photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const currentPhoto = document.getElementById('current-photo');
            currentPhoto.innerHTML = `<img class="h-20 w-20 rounded-full object-cover border-4 border-primary-200 shadow-sm" src="${e.target.result}" alt="Preview">`;
        };
        reader.readAsDataURL(file);
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
