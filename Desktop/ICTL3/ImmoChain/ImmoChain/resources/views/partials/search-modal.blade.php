<!-- <div id="searchModal" class="modal fixed inset-0 z-50 hidden">
    <div class="min-h-screen px-4 text-center">
        <div class="fixed inset-0 bg-black opacity-40"></div>
        
        <div class="inline-block w-full max-w-2xl my-8 text-left align-middle transition-all transform bg-white rounded-2xl shadow-xl">
            <div class="border-b">
                <div class="flex justify-between items-center p-4">
                    <button onclick="closeModal('searchModal')" class="text-2xl">&times;</button>
                    <div class="flex space-x-4">
                        <button class="px-4 py-2 hover:bg-gray-100 rounded-full">Logements</button>
                        <button class="px-4 py-2 hover:bg-gray-100 rounded-full">Expériences</button>
                    </div>
                    <div></div>
                </div>
            </div>

            <div class="p-6">
                <div class="space-y-4">
                    <div class="border rounded-lg p-4">
                        <label class="block text-sm font-medium">Destination</label>
                        <input type="text" placeholder="Où allez-vous ?" class="w-full mt-1 focus:outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="border rounded-lg p-4">
                            <label class="block text-sm font-medium">Arrivée</label>
                            <input type="date" class="w-full mt-1 focus:outline-none">
                        </div>
                        <div class="border rounded-lg p-4">
                            <label class="block text-sm font-medium">Départ</label>
                            <input type="date" class="w-full mt-1 focus:outline-none">
                        </div>
                    </div>

                    <div class="border rounded-lg p-4">
                        <label class="block text-sm font-medium">Voyageurs</label>
                        <div class="flex justify-between items-center mt-2">
                            <span>Adultes</span>
                            <div class="flex items-center space-x-4">
                                <button class="w-8 h-8 rounded-full border flex items-center justify-center">-</button>
                                <span>0</span>
                                <button class="w-8 h-8 rounded-full border flex items-center justify-center">+</button>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="mt-6 w-full bg-primary text-white py-3 rounded-lg font-medium hover:bg-primary-dark transition">
                    Rechercher
                </button>
            </div>
        </div>
    </div>
</div> --><div id="searchModal" class="fixed inset-0 z-50 hidden">
    <div class="min-h-screen px-4 text-center">
        <div class="fixed inset-0 bg-black bg-opacity-40 transition-opacity"></div>
        
        <div class="inline-block w-full max-w-4xl my-8 text-left align-middle transition-all transform bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- En-tête -->
            <div class="border-b p-4">
                <div class="flex items-center justify-between">
                    <button onclick="toggleModal('searchModal')" class="text-2xl">&times;</button>
                    <div class="flex space-x-4">
                        <button class="px-4 py-2 rounded-full hover:bg-gray-100">Où</button>
                        <button class="px-4 py-2 rounded-full hover:bg-gray-100">Quand</button>
                        <button class="px-4 py-2 rounded-full hover:bg-gray-100">Qui</button>
                    </div>
                    <div></div>
                </div>
            </div>

            <!-- Contenu -->
            <div class="p-6">
                <!-- Où -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4">Où allez-vous ?</h3>
                    <div class="relative">
                        <input type="text" placeholder="Recherchez une destination" class="w-full p-4 border rounded-lg pl-12">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <!-- Quand -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4">Quand ?</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Arrivée</label>
                            <input type="date" class="w-full p-3 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Départ</label>
                            <input type="date" class="w-full p-3 border rounded-lg">
                        </div>
                    </div>
                </div>

                <!-- Qui -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Qui ?</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-medium">Adultes</h4>
                                <p class="text-sm text-gray-500">13 ans et plus</p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <button class="w-8 h-8 rounded-full border flex items-center justify-center">-</button>
                                <span>2</span>
                                <button class="w-8 h-8 rounded-full border flex items-center justify-center">+</button>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-medium">Enfants</h4>
                                <p class="text-sm text-gray-500">De 2 à 12 ans</p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <button class="w-8 h-8 rounded-full border flex items-center justify-center">-</button>
                                <span>0</span>
                                <button class="w-8 h-8 rounded-full border flex items-center justify-center">+</button>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-medium">Bébés</h4>
                                <p class="text-sm text-gray-500">Moins de 2 ans</p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <button class="w-8 h-8 rounded-full border flex items-center justify-center">-</button>
                                <span>0</span>
                                <button class="w-8 h-8 rounded-full border flex items-center justify-center">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pied de page -->
            <div class="border-t p-4 flex justify-between items-center">
                <button class="text-primary font-medium">Effacer tout</button>
                <button class="bg-primary text-white px-6 py-3 rounded-lg font-medium hover:bg-primary-dark transition">
                    Rechercher
                </button>
            </div>
        </div>
    </div>
</div>