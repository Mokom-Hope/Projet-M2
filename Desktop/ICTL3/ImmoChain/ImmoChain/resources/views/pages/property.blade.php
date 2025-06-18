@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold mb-2">Villa avec vue exceptionnelle</h1>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center">
                    <i class="fas fa-star"></i>
                    <span class="ml-1">4.9</span>
                </div>
                <span>·</span>
                <a href="#reviews" class="underline">128 commentaires</a>
                <span>·</span>
                <span>Cannes, France</span>
            </div>
            <div class="flex items-center gap-4">
                <button class="flex items-center gap-2 hover:bg-gray-100 px-4 py-2 rounded-lg">
                    <i class="far fa-heart"></i>
                    <span>Enregistrer</span>
                </button>
                <button class="flex items-center gap-2 hover:bg-gray-100 px-4 py-2 rounded-lg">
                    <i class="fas fa-share"></i>
                    <span>Partager</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Galerie photos -->
    <div class="grid grid-cols-4 gap-2 rounded-xl overflow-hidden mb-8">
        <div class="col-span-2 row-span-2">
            <img src="https://picsum.photos/800/600?random=1" alt="Main" class="w-full h-full object-cover">
        </div>
        <div>
            <img src="https://picsum.photos/400/300?random=2" alt="Second" class="w-full h-full object-cover">
        </div>
        <div>
            <img src="https://picsum.photos/400/300?random=3" alt="Third" class="w-full h-full object-cover">
        </div>
        <div>
            <img src="https://picsum.photos/400/300?random=4" alt="Fourth" class="w-full h-full object-cover">
        </div>
        <div>
            <img src="https://picsum.photos/400/300?random=5" alt="Fifth" class="w-full h-full object-cover">
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="grid grid-cols-3 gap-12">
        <!-- Informations principales -->
        <div class="col-span-2">
            <div class="flex justify-between items-start pb-6 border-b">
                <div>
                    <h2 class="text-xl font-semibold mb-2">Villa entière · Hôte : Marie</h2>
                    <div class="flex gap-4 text-gray-600">
                        <span>6 voyageurs</span>
                        <span>·</span>
                        <span>3 chambres</span>
                        <span>·</span>
                        <span>4 lits</span>
                        <span>·</span>
                        <span>2 salles de bain</span>
                    </div>
                </div>
                <img src="https://picsum.photos/56/56?random=6" alt="Host" class="rounded-full w-14 h-14">
            </div>

            <!-- Caractéristiques -->
            <div class="py-6 border-b">
                <div class="grid grid-cols-2 gap-4">
                    @foreach([
                        ['icon' => 'home', 'title' => 'Villa entière', 'desc' => 'Vous aurez le logement en entier'],
                        ['icon' => 'star', 'title' => 'Propreté exceptionnelle', 'desc' => '95% des voyageurs ont trouvé le logement parfaitement propre'],
                        ['icon' => 'key', 'title' => 'Arrivée autonome', 'desc' => 'Vous pouvez entrer dans les lieux avec une boîte à clé sécurisée'],
                        ['icon' => 'calendar', 'title' => 'Annulation gratuite', 'desc' => 'Annulez jusqu'à 24h avant']
                    ] as $feature)
                    <div class="flex gap-4">
                        <i class="fas fa-{{ $feature['icon'] }} text-xl"></i>
                        <div>
                            <h3 class="font-medium">{{ $feature['title'] }}</h3>
                            <p class="text-gray-600">{{ $feature['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Description -->
            <div class="py-6 border-b">
                <p class="text-gray-600 whitespace-pre-line">
                    Magnifique villa avec vue mer panoramique, située dans un quartier calme et prisé.
                    La villa dispose d'une piscine à débordement, d'une terrasse spacieuse et d'un jardin paysager.
                    
                    L'espace
                    - 3 chambres avec salles de bain privatives
                    - Cuisine entièrement équipée
                    - Salon avec baies vitrées
                    - Piscine chauffée
                    - Parking privé
                </p>
                <button class="mt-4 font-medium underline">Afficher plus</button>
            </div>
        </div>

        <!-- Carte de réservation -->
        <div class="relative">
            <div class="sticky top-24 bg-white rounded-xl border p-6 shadow-xl">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <span class="text-2xl font-semibold">350€</span>
                        <span class="text-gray-600">par nuit</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-star"></i>
                        <span class="ml-1">4.9</span>
                        <span class="mx-1">·</span>
                        <a href="#reviews" class="underline">128 commentaires</a>
                    </div>
                </div>

                <!-- Formulaire de réservation -->
                <div class="border rounded-xl overflow-hidden mb-4">
                    <div class="grid grid-cols-2">
                        <div class="p-3 border-r border-b">
                            <label class="block text-xs font-medium">ARRIVÉE</label>
                            <input type="date" class="w-full mt-1">
                        </div>
                        <div class="p-3 border-b">
                            <label class="block text-xs font-medium">DÉPART</label>
                            <input type="date" class="w-full mt-1">
                        </div>
                        <div class="col-span-2 p-3">
                            <label class="block text-xs font-medium">VOYAGEURS</label>
                            <select class="w-full mt-1">
                                <option>1 voyageur</option>
                                <option>2 voyageurs</option>
                                <option>3 voyageurs</option>
                                <option>4 voyageurs</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button class="w-full bg-primary text-white py-3 rounded-lg font-medium hover:bg-primary-dark transition">
                    Réserver
                </button>

                <!-- Résumé des coûts -->
                <div class="mt-4 space-y-3">
                    <div class="flex justify-between">
                        <span class="underline">350€ x 5 nuits</span>
                        <span>1 750€</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="underline">Frais de ménage</span>
                        <span>90€</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="underline">Frais de service</span>
                        <span>150€</span>
                    </div>
                    <div class="pt-3 border-t flex justify-between font-semibold">
                        <span>Total</span>
                        <span>1 990€</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection