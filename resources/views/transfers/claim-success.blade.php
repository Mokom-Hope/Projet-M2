@extends('layouts.app')

@section('title', 'Transfert récupéré avec succès')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-emerald-50 flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Animation de succès -->
        <div class="text-center mb-8">
            <div class="mx-auto w-20 h-20 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center mb-6 animate-bounce">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-display font-bold text-gray-900 mb-2">Transfert récupéré !</h1>
            <p class="text-gray-600">Votre argent a été transféré avec succès</p>
        </div>

        <!-- Détails du transfert -->
        <div class="glass rounded-2xl shadow-card p-6 border border-white/20 backdrop-blur-xl mb-6">
            <div class="text-center">
                <div class="text-sm text-gray-600 mb-1">Code de transfert</div>
                <div class="text-2xl font-mono font-bold text-gray-900 mb-4">{{ $code }}</div>
                
                <div class="w-full h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent mb-4"></div>
                
                <div class="text-sm text-gray-600">
                    Récupéré le {{ now()->format('d/m/Y à H:i') }}
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="space-y-3">
            <a href="{{ route('dashboard') }}" class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Aller au tableau de bord
            </a>
            
            <a href="{{ route('transfers.claim') }}" class="w-full inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Récupérer un autre transfert
            </a>
        </div>

        <!-- Message de sécurité -->
        <div class="mt-6 p-4 bg-blue-50 rounded-xl border border-blue-200">
            <div class="flex">
                <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-sm text-blue-700">
                    <p class="font-medium mb-1">Transfert sécurisé</p>
                    <p>Votre transaction a été traitée de manière sécurisée. Conservez ce code pour vos dossiers.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Confettis d'animation
document.addEventListener('DOMContentLoaded', function() {
    // Animation simple de célébration
    setTimeout(() => {
        const successIcon = document.querySelector('.animate-bounce');
        if (successIcon) {
            successIcon.classList.add('animate-pulse');
        }
    }, 2000);
});
</script>
@endpush
@endsection
