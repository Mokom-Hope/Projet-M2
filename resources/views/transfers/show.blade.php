@extends('layouts.app')

@section('title', 'Détails du transfert')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header Section -->
    <div class="mb-8 animate-on-scroll">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('transfers.history') }}" class="p-2 rounded-xl bg-gray-100 hover:bg-gray-200 transition-colors duration-200">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-display font-bold text-gray-900">Transfert #{{ $transfer->transfer_code }}</h1>
                    <p class="text-gray-600">Détails complets de votre transfert</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $transfer->status_badge }}">
                    @switch($transfer->status)
                        @case('completed')
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Terminé
                            @break
                        @case('sent')
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            En attente
                            @break
                        @case('pending')
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            En cours
                            @break
                        @case('cancelled')
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Annulé
                            @break
                    @endswitch
                </span>
                @if($transfer->sender_id === auth()->id() && $transfer->status === 'sent')
                    <form method="POST" action="{{ route('transfers.cancel', $transfer) }}" class="inline">
                        @csrf
                        <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir annuler ce transfert ?')" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Annuler
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Transfer Overview -->
            <div class="glass rounded-2xl shadow-card p-8 border border-white/20 backdrop-blur-xl animate-on-scroll">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-display font-bold text-gray-900">Aperçu du transfert</h2>
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-100 to-primary-200 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Expéditeur</label>
                            <div class="flex items-center space-x-3">
                                @if($transfer->sender->profile_photo)
                                    <img class="w-10 h-10 rounded-full object-cover" src="{{ Storage::url($transfer->sender->profile_photo) }}" alt="{{ $transfer->sender->full_name }}">
                                @else
                                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-bold">{{ substr($transfer->sender->first_name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-900">{{ $transfer->sender->full_name }}</p>
                                    <p class="text-sm text-gray-600">{{ $transfer->sender->email }}</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Destinataire</label>
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-secondary-500 to-secondary-700 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    @if($transfer->recipient)
                                        <p class="font-medium text-gray-900">{{ $transfer->recipient->full_name }}</p>
                                        <p class="text-sm text-gray-600">{{ $transfer->recipient->email }}</p>
                                    @else
                                        <p class="font-medium text-gray-900">{{ $transfer->recipient_identifier }}</p>
                                        <p class="text-sm text-gray-600">Destinataire externe</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Montant envoyé</label>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($transfer->amount, 0, ',', ' ') }} {{ $transfer->currency }}</p>
                            @if($transfer->fees > 0)
                                <p class="text-sm text-gray-600">+ {{ number_format($transfer->fees, 0, ',', ' ') }} {{ $transfer->currency }} de frais</p>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Montant à recevoir</label>
                            <p class="text-2xl font-bold text-green-600">{{ number_format($transfer->recipient_amount, 0, ',', ' ') }} {{ $transfer->recipient_currency }}</p>
                            @if($transfer->exchange_rate != 1)
                                <p class="text-sm text-gray-600">Taux: 1 {{ $transfer->currency }} = {{ $transfer->exchange_rate }} {{ $transfer->recipient_currency }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transfer Timeline -->
            <div class="glass rounded-2xl shadow-card p-8 border border-white/20 backdrop-blur-xl animate-on-scroll">
                <h2 class="text-xl font-display font-bold text-gray-900 mb-6">Chronologie</h2>
                
                <div class="flow-root">
                    <ul class="-mb-8">
                        <li>
                            <div class="relative pb-8">
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></span>
                                <div class="relative flex space-x-3">
                                    <div class="h-8 w-8 bg-green-500 rounded-full flex items-center justify-center ring-8 ring-white">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-900 font-medium">Transfert créé</p>
                                            <p class="text-sm text-gray-600">Le transfert a été initié avec succès</p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                            {{ $transfer->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        @if($transfer->status !== 'pending')
                            <li>
                                <div class="relative pb-8">
                                    @if($transfer->status !== 'cancelled')
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div class="h-8 w-8 {{ $transfer->status === 'cancelled' ? 'bg-red-500' : 'bg-blue-500' }} rounded-full flex items-center justify-center ring-8 ring-white">
                                            @if($transfer->status === 'cancelled')
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-900 font-medium">
                                                    @if($transfer->status === 'cancelled')
                                                        Transfert annulé
                                                    @else
                                                        Transfert envoyé
                                                    @endif
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    @if($transfer->status === 'cancelled')
                                                        Le transfert a été annulé
                                                    @else
                                                        En attente de récupération par le destinataire
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                {{ $transfer->updated_at->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endif

                        @if($transfer->status === 'completed')
                            <li>
                                <div class="relative">
                                    <div class="relative flex space-x-3">
                                        <div class="h-8 w-8 bg-green-500 rounded-full flex items-center justify-center ring-8 ring-white">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                            </svg>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-900 font-medium">Transfert récupéré</p>
                                                <p class="text-sm text-gray-600">Le destinataire a récupéré l'argent avec succès</p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                {{ $transfer->claimed_at?->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Security Information -->
            @if($transfer->security_question && $transfer->sender_id === auth()->id())
                <div class="glass rounded-2xl shadow-card p-8 border border-white/20 backdrop-blur-xl animate-on-scroll">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-display font-bold text-gray-900">Sécurité</h2>
                        <div class="w-10 h-10 bg-gradient-to-br from-red-100 to-red-200 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Question de sécurité</label>
                        <p class="text-gray-900">{{ $transfer->security_question }}</p>
                    </div>

                    @if($transfer->failed_attempts > 0)
                        <div class="mt-4 bg-orange-50 border border-orange-200 rounded-xl p-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <span class="text-orange-800 text-sm font-medium">
                                    {{ $transfer->failed_attempts }} tentative(s) échouée(s) sur {{ $transfer->max_attempts }} autorisées
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Transfer Code -->
            <div class="glass rounded-2xl shadow-card p-6 border border-white/20 backdrop-blur-xl animate-on-scroll">
                <h3 class="text-lg font-display font-bold text-gray-900 mb-4">Code de transfert</h3>
                <div class="text-center">
                    <div class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-xl p-6 mb-4">
                        <p class="text-3xl font-mono font-bold text-primary-900 tracking-wider">{{ $transfer->transfer_code }}</p>
                    </div>
                    <button onclick="copyTransferCode()" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Copier le code
                    </button>
                </div>
            </div>

            <!-- Transfer Details -->
            <div class="glass rounded-2xl shadow-card p-6 border border-white/20 backdrop-blur-xl animate-on-scroll">
                <h3 class="text-lg font-display font-bold text-gray-900 mb-4">Détails</h3>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Date de création</span>
                        <span class="text-sm font-medium text-gray-900">{{ $transfer->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Expire le</span>
                        <span class="text-sm font-medium text-gray-900">{{ $transfer->expires_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($transfer->notes)
                        <div>
                            <span class="text-sm text-gray-600">Notes</span>
                            <p class="text-sm font-medium text-gray-900 mt-1">{{ $transfer->notes }}</p>
                        </div>
                    @endif
                    @if($transfer->gateway_reference)
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Référence gateway</span>
                            <span class="text-sm font-mono text-gray-900">{{ $transfer->gateway_reference }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            @if($transfer->status === 'sent')
                <div class="glass rounded-2xl shadow-card p-6 border border-white/20 backdrop-blur-xl animate-on-scroll">
                    <h3 class="text-lg font-display font-bold text-gray-900 mb-4">Actions rapides</h3>
                    <div class="space-y-3">
                        <button onclick="shareTransfer()" class="w-full flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                            </svg>
                            Partager le code
                        </button>
                        <a href="{{ route('transfers.claim', ['code' => $transfer->transfer_code]) }}" class="w-full flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Voir page de récupération
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyTransferCode() {
    const code = '{{ $transfer->transfer_code }}';
    navigator.clipboard.writeText(code).then(function() {
        // Show success message
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Copié !';
        button.classList.add('bg-green-600');
        button.classList.remove('bg-primary-600');
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('bg-green-600');
            button.classList.add('bg-primary-600');
        }, 2000);
    });
}

function shareTransfer() {
    const shareData = {
        title: 'Code de transfert',
        text: `Voici votre code de transfert: {{ $transfer->transfer_code }}. Récupérez votre argent sur: {{ route('transfers.claim') }}`,
        url: '{{ route('transfers.claim', ['code' => $transfer->transfer_code]) }}'
    };

    if (navigator.share) {
        navigator.share(shareData);
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(shareData.text + ' ' + shareData.url);
        alert('Informations copiées dans le presse-papiers !');
    }
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
