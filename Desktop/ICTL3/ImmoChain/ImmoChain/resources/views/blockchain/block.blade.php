@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center mb-6">
        <a href="{{ route('blockchain.explorer') }}" class="text-indigo-600 hover:text-indigo-900 mr-2">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
        <h1 class="text-3xl font-bold">Détails du Bloc #{{ $block['index'] }}</h1>
    </div>
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-xl font-semibold">Informations du Bloc</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-700 mb-2"><span class="font-semibold">Index:</span> {{ $block['index'] }}</p>
                    <p class="text-gray-700 mb-2"><span class="font-semibold">Hash:</span> <span class="break-all">{{ $block['hash'] }}</span></p>
                    <p class="text-gray-700 mb-2"><span class="font-semibold">Hash précédent:</span> <span class="break-all">{{ $block['previousHash'] }}</span></p>
                </div>
                <div>
                    <p class="text-gray-700 mb-2"><span class="font-semibold">Timestamp:</span> {{ date('d/m/Y H:i:s', $block['timestamp'] / 1000) }}</p>
                    <p class="text-gray-700 mb-2"><span class="font-semibold">Nonce:</span> {{ $block['nonce'] }}</p>
                    <p class="text-gray-700 mb-2"><span class="font-semibold">Nombre de transactions:</span> {{ count($block['data']) }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-xl font-semibold">Transactions</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if(count($block['data']) > 0)
                        @foreach($block['data'] as $transaction)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="truncate block max-w-xs">{{ $transaction['id'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($transaction['type'] === 'property')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Bien Immobilier
                                        </span>
                                    @elseif($transaction['type'] === 'reservation')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Réservation
                                        </span>
                                    @elseif($transaction['type'] === 'reward')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Récompense
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $transaction['type'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ date('d/m/Y H:i:s', $transaction['timestamp'] / 1000) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('blockchain.transaction', $transaction['id']) }}" class="text-indigo-600 hover:text-indigo-900">Voir détails</a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                Aucune transaction trouvée
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
