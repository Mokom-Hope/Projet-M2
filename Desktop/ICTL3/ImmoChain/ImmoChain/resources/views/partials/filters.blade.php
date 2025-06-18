<div id="filtersModal" class="fixed inset-0 z-50 hidden">
    <div class="min-h-screen px-4 text-center">
        <div class="fixed inset-0 bg-black bg-opacity-40 transition-opacity"></div>
        
        <div class="inline-block w-full max-w-4xl my-8 text-left align-middle transition-all transform bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- En-tête -->
            <div class="border-b p-4">
                <div class="flex items-center justify-between">
                    <button onclick="toggleModal('filtersModal')" class="text-2xl">&times;</button>
                    <h2 class="text-lg font-semibold">Filtres</h2>
                    <div></div>
                </div>
            </div>

            <!-- Contenu -->
            <div class="p-6 max-h-[80vh] overflow-y-auto">
                <!-- Type de logement -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4">Type de logement</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @foreach(['Maison entière', 'Chambre privée', 'Chambre partagée', 'Hôtel', 'Appartement', 'Loft'] as $type)
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="form-checkbox">
                            <span>{{ $type }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Fourchette de prix -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4">Fourchette de prix</h3>
                    <div class="flex items-center space-x-4">
                        <input type="number" placeholder="Prix min" class="w-1/2 p-3 border rounded-lg">
                        <span>-</span>
                        <input type="number" placeholder="Prix max" class="w-1/2 p-3 border rounded-lg">
                    </div>
                </div>

                <!-- Chambres et lits -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4">Chambres et lits</h3>
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-medium mb-2">Chambres</h4>
                            <div class="flex space-x-2">
                                @foreach(['Tout', '1', '2', '3', '4', '5+'] as $option)
                                <button class="px-4 py-2 border rounded-full hover:border-black">{{ $option }}</button>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <h4 class="font-medium mb-2">Lits</h4>
                            <div class="flex space-x-2">
                                @foreach(['Tout', '1', '2', '3', '4', '5+'] as $option)
                                <button class="px-4 py-2 border rounded-full hover:border-black">{{ $option }}</button>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <h4 class="font-medium mb-2">Salles de bain</h4>
                            <div class="flex space-x-2">
                                @foreach(['Tout', '1', '2', '3', '4', '5+'] as $option)
                                <button class="px-4 py-2 border rounded-full hover:border-black">{{ $option }}</button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Équipements -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4">Équipements</h3>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach(['Wi-Fi', 'Cuisine', 'Lave-linge', 'Climatisation', 'Chauffage', 'Télévision', 'Fer à repasser', 'Parking gratuit', 'Piscine', 'Jacuzzi', 'Sèche-cheveux', 'Détecteur de fumée'] as $amenity)
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="form-checkbox">
                            <span>{{ $amenity }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Pied de page -->
            <div class="border-t p-4 flex justify-between items-center">
                <button class="text-primary font-medium">Effacer tout</button>
                <button class="bg-primary text-white px-6 py-3 rounded-lg font-medium hover:bg-primary-dark transition">
                    Afficher les résultats
                </button>
            </div>
        </div>
    </div>
</div>