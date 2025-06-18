<div id="addPropertyModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex justify-center items-center">
    <div class="bg-white rounded-lg max-w-3xl w-full p-6 overflow-y-auto max-h-screen relative">
        <h2 class="text-2xl font-bold mb-4">Ajouter mon bien</h2>
        <form action="#" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <!-- Type de bien -->
                <div>
                    <label for="type" class="block mb-1">Type de bien</label>
                    <select id="type" name="type" class="w-full border rounded px-3 py-2">
                        <option value="house">Maison</option>
                        <option value="apartment">Appartement</option>
                        <option value="land">Terrain</option>
                        <option value="commercial">Local commercial</option>
                        <option value="building">Immeuble</option>
                    </select>
                </div>

                <!-- Type de transaction -->
                <div>
                    <label for="transaction_type" class="block mb-1">Type de transaction</label>
                    <select id="transaction_type" name="transaction_type" class="w-full border rounded px-3 py-2">
                        <option value="sale">Vente</option>
                        <option value="rent">Location</option>
                    </select>
                </div>

                <!-- Titre de l'annonce -->
                <div>
                    <label for="title" class="block mb-1">Titre de l'annonce</label>
                    <input type="text" id="title" name="title" class="w-full border rounded px-3 py-2" required>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block mb-1">Description</label>
                    <textarea id="description" name="description" rows="4" class="w-full border rounded px-3 py-2" required></textarea>
                </div>

                <!-- Prix -->
                <div>
                    <label for="price" class="block mb-1">Prix</label>
                    <input type="number" id="price" name="price" class="w-full border rounded px-3 py-2" required>
                </div>

                <!-- Adresse -->
                <div>
                    <label for="address" class="block mb-1">Adresse</label>
                    <input type="text" id="address" name="address" class="w-full border rounded px-3 py-2" required>
                </div>

                <!-- Détails pour Immeuble -->
                <div id="buildingDetails" class="hidden">
                    <label for="apartment_count" class="block mb-1">Nombre d'appartements</label>
                    <input type="number" id="apartment_count" name="apartment_count" class="w-full border rounded px-3 py-2">
                </div>

                <!-- Images -->
                <div>
                    <label for="images" class="block mb-1">Images (5 minimum)</label>
                    <input type="file" id="images" name="images[]" accept="image/*" multiple required class="w-full border rounded px-3 py-2">
                </div>

                <!-- Vidéo -->
                <div>
                    <label for="video" class="block mb-1">Vidéo</label>
                    <input type="file" id="video" name="video" accept="video/*" class="w-full border rounded px-3 py-2">
                </div>

                <!-- Papiers légaux -->
                <div id="legalDocuments" class="hidden">
                    <label for="legal_files" class="block mb-1">Papiers légaux</label>
                    <div class="border border-dashed border-gray-400 rounded-lg p-4 text-center">
                        <p class="text-sm text-gray-600">Glissez vos fichiers ici ou</p>
                        <label class="text-primary font-bold cursor-pointer">
                            <span>cliquez pour sélectionner</span>
                            <input type="file" id="legal_files" name="legal_files[]" accept=".pdf,.doc,.docx,.jpg,.png" multiple class="hidden">
                        </label>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Formats acceptés : PDF, DOC, JPG, PNG. Taille max : 5MB.</p>
                </div>
            </div>

            <!-- Boutons -->
            <div class="mt-6 flex justify-end">
                <button type="button" onclick="toggleModal('addPropertyModal')" class="px-4 py-2 bg-gray-200 rounded mr-2">Annuler</button>
                <button type="submit" class="px-4 py-2 bg-black text-white rounded">Ajouter le bien</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.toggle('hidden');
    }

    // Fermer le modal en cliquant à l'extérieur
    document.getElementById('addPropertyModal').addEventListener('click', function (event) {
        if (event.target === this) {
            toggleModal('addPropertyModal');
        }
    });

    // Afficher ou cacher les détails de l'immeuble
    document.getElementById('type').addEventListener('change', function () {
        const buildingDetails = document.getElementById('buildingDetails');
        if (this.value === 'building') {
            buildingDetails.classList.remove('hidden');
        } else {
            buildingDetails.classList.add('hidden');
        }
    });

    // Afficher ou cacher les papiers légaux
    document.getElementById('transaction_type').addEventListener('change', function () {
        const legalDocuments = document.getElementById('legalDocuments');
        if (this.value === 'sale') {
            legalDocuments.classList.remove('hidden');
        } else {
            legalDocuments.classList.add('hidden');
        }
    });
</script>
