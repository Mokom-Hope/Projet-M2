@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center mb-6">
        <a href="{{ route('blockchain.explorer') }}" class="text-indigo-600 hover:text-indigo-900 mr-2">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
        <h1 class="text-3xl font-bold">Détails de la Transaction</h1>
    </div>
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-xl font-semibold">Informations de la Transaction</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-700 mb-2"><span class="font-semibold">ID:</span> <span class="break-all">{{ $transaction['transaction']['id'] }}</span></p>
                    <p class="text-gray-700 mb-2"><span class="font-semibold">Type:</span> 
                        @if($transaction['transaction']['type'] === 'property')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Bien Immobilier
                            </span>
                        @elseif($transaction['transaction']['type'] === 'reservation')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                Réservation
                            </span>
                        @elseif($transaction['transaction']['type'] === 'reward')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Récompense
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ $transaction['transaction']['type'] }}
                            </span>
                        @endif
                    </p>
                    <p class="text-gray-700 mb-2"><span class="font-semibold">Timestamp:</span> {{ date('d/m/Y H:i:s', $transaction['transaction']['timestamp'] / 1000) }}</p>
                </div>
                <div>
                    <p class="text-gray-700 mb-2"><span class="font-semibold">Bloc:</span> <a href="{{ route('blockchain.block', $transaction['blockIndex']) }}" class="text-indigo-600 hover:text-indigo-900">{{ $transaction['blockIndex'] }}</a></p>
                    <p class="text-gray-700 mb-2"><span class="font-semibold">Hash du bloc:</span> <span class="break-all">{{ $transaction['blockHash'] }}</span></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-xl font-semibold">Données de la Transaction</h2>
        </div>
        <div class="p-6">
            @if($transaction['transaction']['type'] === 'property')
                <div class="mb-4">
                    <h3 class="text-lg font-semibold mb-2">Bien Immobilier #{{ $transaction['transaction']['data']['id'] }}</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700 mb-2"><span class="font-semibold">Titre:</span> {{ $transaction['transaction']['data']['titre'] }}</p>
                        <p class="text-gray-700 mb-2"><span class="font-semibold">Type:</span> {{ $transaction['transaction']['data']['type'] }}</p>
                        <p class="text-gray-700 mb-2"><span class="font-semibold">Adresse:</span> {{ $transaction['transaction']['data']['adresse'] }}</p>
                        <p class="text-gray-700 mb-2"><span class="font-semibold">Prix:</span> {{ number_format($transaction['transaction']['data']['prix'], 0, ',', ' ') }} FCFA</p>
                        <p class="text-gray-700 mb-2"><span class="font-semibold">Superficie:</span> {{ $transaction['transaction']['data']['superficie'] }} m²</p>
                        <p class="text-gray-700 mb-2"><span class="font-semibold">ID Propriétaire:</span> {{ $transaction['transaction']['data']['id_proprietaire'] }}</p>
                        <p class="text-gray-700 mb-2"><span class="font-semibold">Date de création:</span> {{ $transaction['transaction']['data']['created_at'] }}</p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('properties.show', $transaction['transaction']['data']['id']) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Voir le bien immobilier
                    </a>
                </div>
            @elseif($transaction['transaction']['type'] === 'reservation')
                <div class="mb-4">
                    <h3 class="text-lg font-semibold mb-2">Réservation #{{ $transaction['transaction']['data']['id'] }}</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700 mb-2"><span class="font-semibold">ID Bien:</span> {{ $transaction['transaction']['data']['id_bien'] }}</p>
                        <p class="text-gray-700 mb-2"><span class="font-semibold">ID Client:</span> {{ $transaction['transaction']['data']['id_client'] }}</p>
                        <p class="text-gray-700 mb-2"><span class="font-semibold">Date de réservation:</span> {{ $transaction['transaction']['data']['date_reservation'] }}</p>
                        <p class="text-gray-700 mb-2"><span class="font-semibold">Date de visite:</span> {{ $transaction['transaction']['data']['date_visite'] }}</p>
                        <p class="text-gray-700 mb-2"><span class="font-semibold">Statut:</span> {{ $transaction['transaction']['data']['statut'] }}</p>
                    </div>
                </div>
            @elseif($transaction['transaction']['type'] === 'reward')
                <div class="mb-4">
                    <h3 class="text-lg font-semibold mb-2">Récompense de Minage</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700 mb-2"><span class="font-semibold">De:</span> {{ $transaction['transaction']['from'] }}</p>
                        <p class="text-gray-700 mb-2"><span class="font-semibold">À:</span> {{ $transaction['transaction']['to'] }}</p>
                        <p class="text-gray-700 mb-2"><span class="font-semibold">Montant:</span> {{ $transaction['transaction']['amount'] }} tokens</p>
                    </div>
                </div>
            @else
                <pre class="bg-gray-50 p-4 rounded-lg overflow-x-auto">{{ json_encode($transaction['transaction'], JSON_PRETTY_PRINT) }}</pre>
            @endif
        </div>
    </div>
</div>
@endsection
