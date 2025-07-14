@extends('layouts.app')

@section('title', 'Historique des transferts')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header Section -->
    <div class="mb-8 animate-on-scroll">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-display font-bold text-gray-900 mb-1">Historique des transferts</h1>
                <p class="text-gray-600">Consultez tous vos transferts envoyés et reçus</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('transfers.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary-600 to-primary-700 text-white rounded-xl hover:from-primary-700 hover:to-primary-800 transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Nouveau transfert
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 animate-on-scroll">
        <div class="glass rounded-2xl shadow-card p-6 border border-white/20 backdrop-blur-xl">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium text-gray-700">Filtrer par :</span>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('transfers.history', ['type' => 'all']) }}" 
                       class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ $type === 'all' ? 'bg-primary-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Tous
                    </a>
                    <a href="{{ route('transfers.history', ['type' => 'sent']) }}" 
                       class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ $type === 'sent' ? 'bg-primary-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Envoyés
                    </a>
                    <a href="{{ route('transfers.history', ['type' => 'received']) }}" 
                       class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ $type === 'received' ? 'bg-primary-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Reçus
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Transfers List -->
    <div class="glass rounded-2xl shadow-card border border-white/20 backdrop-blur-xl overflow-hidden animate-on-scroll">
        @if($transfers->count() > 0)
            <div class="divide-y divide-gray-100">
                @foreach($transfers as $transfer)
                    <div class="p-6 hover:bg-gray-50/50 transition-colors duration-150">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <!-- Transfer Icon -->
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center {{ $transfer->sender_id === auth()->id() ? 'bg-gradient-to-br from-blue-100 to-blue-200' : 'bg-gradient-to-br from-green-100 to-green-200' }}">
                                    @if($transfer->sender_id === auth()->id())
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                    @endif
                                </div>

                                <!-- Transfer Details -->
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            @if($transfer->sender_id === auth()->id())
                                                Envoyé à {{ $transfer->recipient_email ?? $transfer->recipient_phone }}
                                            @else
                                                Reçu de {{ $transfer->sender->full_name }}
                                            @endif
                                        </h3>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            {{ $transfer->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $transfer->status === 'sent' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $transfer->status === 'pending' ? 'bg-gray-100 text-gray-800' : '' }}
                                            {{ $transfer->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                            @switch($transfer->status)
                                                @case('completed')
                                                    Terminé
                                                    @break
                                                @case('sent')
                                                    En attente
                                                    @break
                                                @case('pending')
                                                    En cours
                                                    @break
                                                @case('cancelled')
                                                    Annulé
                                                    @break
                                                @default
                                                    {{ ucfirst($transfer->status) }}
                                            @endswitch
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                            </svg>
                                            <span class="font-mono">{{ $transfer->transfer_code }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>{{ $transfer->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                        @if($transfer->notes)
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                                </svg>
                                                <span class="truncate max-w-xs">{{ $transfer->notes }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Amount and Actions -->
                            <div class="text-right">
                                <div class="text-xl font-bold {{ $transfer->sender_id === auth()->id() ? 'text-red-600' : 'text-green-600' }} mb-2">
                                    {{ $transfer->sender_id === auth()->id() ? '-' : '+' }}{{ number_format($transfer->amount, 0, ',', ' ') }} {{ $transfer->currency }}
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('transfers.show', $transfer) }}" class="inline-flex items-center px-3 py-1 text-xs font-medium text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors duration-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Voir
                                    </a>
                                    @if($transfer->sender_id === auth()->id() && $transfer->status === 'sent')
                                        <form method="POST" action="{{ route('transfers.cancel', $transfer) }}" class="inline">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir annuler ce transfert ?')" class="inline-flex items-center px-3 py-1 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors duration-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Annuler
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($transfers->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $transfers->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-16">
                <div class="mx-auto w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-display font-bold text-gray-900 mb-2">Aucun transfert</h3>
                <p class="text-gray-600 mb-6">
                    @if($type === 'sent')
                        Vous n'avez encore envoyé aucun transfert.
                    @elseif($type === 'received')
                        Vous n'avez encore reçu aucun transfert.
                    @else
                        Votre historique de transferts est vide.
                    @endif
                </p>
                <a href="{{ route('transfers.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Envoyer votre premier transfert
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
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
