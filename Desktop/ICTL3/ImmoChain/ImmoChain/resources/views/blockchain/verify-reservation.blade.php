@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Vérification Blockchain de la Réservation</h1>
    
    @if(isset($error))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ $error }}
        </div>
    @endif
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-xl font-semibold">Informations de la Réservation</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-700 mb-2"><span class="font-semibold">ID:</span> {{ $reservation->id }}</p>
                    <p class="text-gray-700 mb-2"><span class="font-semibold">Bien:</span> {{ $reservation->bien->titre }}</p>
                    <p class="text-gray-700 mb-2"><span class="font-semibold">Client:</span> {{ $reservation->client->name }}</p>
                </div>
                <div>
                    <p class="text-gray-700 mb-2"><span class="font-semibold">Date de réservation:</span> {{ $reservation->date_reservation }}</p>
                    <p class="text-gray-700 mb-2"><span class="font-semibold">Date de visite:</span> {{ $reservation->date_visite }}</p>
                    <p class="text-gray-700 mb-2"><span class="font-semibold">Statut:</span> {{ $reservation->statut }}</p>
                </div>
            </div>
        </div>
    </div>
    
    @if(isset($blockchainData))
        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
            <div class="px-6 py-4 bg-green-50 border-b border-green-200">
                <h2 class="text-xl font-semibold text-green-800">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    Certification Blockchain Vérifiée
                </h2>
            </div>
            <div class="p-6">
                <p class="text-gray-700 mb-4">
                    Cette réservation est certifiée sur la blockchain ImmoChain. Les informations suivantes ont été enregistrées de manière sécurisée et immuable.
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-gray-700 mb-2"><span class="font-semibold">Transaction ID:</span> <span class="break-all">{{ $blockchainData['transaction']['id'] }}</span></p>
                        <p class="text-gray-700 mb-2"><span class="font-semibold">Date d'enregistrement:</span> {{ date('d/m/Y H:i:s', $blockchainData['timestamp'] / 1000) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-700 mb-2"><span class="font-semibold">Bloc:</span> <a href="{{ route('blockchain.block', $blockchainData['blockIndex']) }}" class="text-indigo-600 hover:text-indigo-900">{{ $blockchainData['blockIndex'] }}</a></p>
                        <p class="text-gray-700 mb-2"><span class="font-semibold">Hash du bloc:</span> <span class="break-all">{{ $blockchainData['blockHash'] }}</span></p>
                    </div>
                </div>
                
                <div class="flex space-x-4">
                    <a href="{{ route('blockchain.transaction', $blockchainData['transaction']['id']) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Voir la transaction
                    </a>
                    <a href="{{ route('blockchain.explorer') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring ring-gray-200 disabled:opacity-25 transition ease-in-out duration-150">
                        Explorer la blockchain
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
            <div class="px-6 py-4 bg-red-50 border-b border-red-200">
                <h2 class="text-xl font-semibold text-red-800">
                    <i class="fas fa-times-circle text-red-500 mr-2"></i>
                    Non Certifiée sur la Blockchain
                </h2>
            </div>
            <div class="p-6">
                <p class="text-gray-700 mb-4">
                    Cette réservation n'est pas encore certifiée sur la blockchain ImmoChain.
                </p>
            </div>
        </div>
    @endif
</div>
@endsection
