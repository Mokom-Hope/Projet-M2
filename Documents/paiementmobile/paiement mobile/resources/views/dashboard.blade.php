@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Welcome Section -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="mb-4 md:mb-0">
                <h1 class="text-3xl font-display font-bold text-gray-900 mb-1">
                    {{ $greeting }}, {{ auth()->user()->first_name }} 👋
                </h1>
                <p class="text-gray-600">Bienvenue sur votre espace personnel</p>
            </div>
            <div class="flex items-center space-x-2">
                <div class="bg-white rounded-full px-4 py-2 shadow-sm border border-gray-100 flex items-center">
                    <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-sm text-gray-700">{{ now()->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Wallet Card -->
    <div class="mb-8 animate-on-scroll">
        <div class="relative overflow-hidden bg-gradient-to-r from-primary-500 to-primary-700 rounded-2xl shadow-lg p-6 md:p-8 bg-size-200 animate-gradient">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full"></div>
            <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-white opacity-10 rounded-full"></div>
            
            <div class="relative z-10">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-primary-100 text-sm font-medium mb-1">Solde disponible</p>
                        <div class="flex items-baseline">
                            <h2 class="text-4xl md:text-5xl font-display font-bold text-white">
                                {{ number_format($stats['wallet_balance'], 0, ',', ' ') }}
                            </h2>
                            <span class="ml-2 text-lg text-primary-100">{{ auth()->user()->currency }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-4 md:mt-0 flex space-x-3">
                        <a href="{{ route('transfers.create') }}" class="inline-flex items-center px-4 py-2 bg-white text-primary-700 rounded-lg shadow-sm hover:bg-primary-50 transition-all duration-200 font-medium text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Envoyer
                        </a>
                        <a href="{{ route('transfers.claim') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg shadow-sm hover:bg-primary-700 transition-all duration-200 font-medium text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            Récupérer
                        </a>
                    </div>
                </div>
                
                <div class="mt-6 grid grid-cols-3 gap-4">
                    <div class="bg-white bg-opacity-20 rounded-xl p-4">
                        <p class="text-xs text-primary-100 mb-1">Total envoyé</p>
                        <p class="text-xl font-bold text-white">{{ number_format($stats['total_sent'], 0, ',', ' ') }}</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-xl p-4">
                        <p class="text-xs text-primary-100 mb-1">Total reçu</p>
                        <p class="text-xl font-bold text-white">{{ number_format($stats['total_received'], 0, ',', ' ') }}</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-xl p-4">
                        <p class="text-xs text-primary-100 mb-1">En attente</p>
                        <p class="text-xl font-bold text-white">{{ $stats['pending_transfers'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-8 animate-on-scroll">
        <h2 class="text-xl font-display font-bold text-gray-900 mb-4">Actions rapides</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('transfers.create') }}" class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 transform hover:scale-105 hover:border-primary-200 group">
                <div class="flex items-center">
                    <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg p-3 mr-4 shadow-sm group-hover:shadow-glow transition-all duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-primary-600 transition-colors duration-200">Envoyer</h3>
                        <p class="text-gray-500 text-sm">Transférer rapidement</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('transfers.claim') }}" class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 transform hover:scale-105 hover:border-primary-200 group">
                <div class="flex items-center">
                    <div class="bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-lg p-3 mr-4 shadow-sm group-hover:shadow transition-all duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-secondary-600 transition-colors duration-200">Récupérer</h3>
                        <p class="text-gray-500 text-sm">Avec un code</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('payment-methods.index') }}" class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 transform hover:scale-105 hover:border-primary-200 group">
                <div class="flex items-center">
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-3 mr-4 shadow-sm group-hover:shadow transition-all duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-purple-600 transition-colors duration-200">Méthodes</h3>
                        <p class="text-gray-500 text-sm">Gérer vos comptes</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('transfers.history') }}" class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 transform hover:scale-105 hover:border-primary-200 group">
                <div class="flex items-center">
                    <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg p-3 mr-4 shadow-sm group-hover:shadow transition-all duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-amber-600 transition-colors duration-200">Historique</h3>
                        <p class="text-gray-500 text-sm">Vos transactions</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 animate-on-scroll">
        <!-- Recent Transfers -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-display font-bold text-gray-900">Transferts récents</h3>
                    <a href="{{ route('transfers.history') }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium flex items-center">
                        Voir tout
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentTransfers as $transfer)
                    <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $transfer->sender_id === auth()->id() ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600' }}">
                                    @if($transfer->sender_id === auth()->id())
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">
                                        @if($transfer->sender_id === auth()->id())
                                            Envoyé à {{ $transfer->recipient_email ?? $transfer->recipient_phone }}
                                        @else
                                            Reçu de {{ $transfer->sender->full_name }}
                                        @endif
                                    </p>
                                    <div class="flex items-center text-xs text-gray-500">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $transfer->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium {{ $transfer->sender_id === auth()->id() ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $transfer->sender_id === auth()->id() ? '-' : '+' }}{{ number_format($transfer->amount, 0, ',', ' ') }} {{ $transfer->currency }}
                                </p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
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
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <h3 class="text-base font-medium text-gray-900">Aucun transfert</h3>
                        <p class="mt-1 text-sm text-gray-500">Commencez par envoyer votre premier transfert.</p>
                        <div class="mt-6">
                            <a href="{{ route('transfers.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Nouveau transfert
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-display font-bold text-gray-900">Transactions récentes</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentTransactions as $transaction)
                    <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $transaction->type === 'credit' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                    @if($transaction->type === 'credit')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $transaction->description }}</p>
                                    <div class="flex items-center text-xs text-gray-500">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $transaction->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->type === 'credit' ? '+' : '-' }}{{ number_format($transaction->amount, 0, ',', ' ') }} {{ $transaction->currency }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    Solde: {{ number_format($transaction->balance_after, 0, ',', ' ') }} {{ $transaction->currency }}
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <h3 class="text-base font-medium text-gray-900">Aucune transaction</h3>
                        <p class="mt-1 text-sm text-gray-500">Vos transactions apparaîtront ici.</p>
                    </div>
                @endforelse
            </div>
        </div>
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
