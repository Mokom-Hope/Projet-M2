@extends('layouts.app')

@section('title', 'ImmoChain - Modifier un bien')

@push('styles')
<link href="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.css" rel="stylesheet">
<link href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.css" rel="stylesheet">
<style>
    #map {
        width: 100%;
        height: 300px;
        border-radius: 0.5rem;
    }
    .image-preview {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 10px;
        margin-top: 10px;
    }
    .image-preview-item {
        position: relative;
        height: 150px;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .image-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .image-preview-item .remove-image {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(0, 0, 0, 0.5);
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .dropzone {
        border: 2px dashed #ccc;
        border-radius: 0.5rem;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
    }
    .dropzone.dragover {
        border-color: #000;
        background-color: rgba(0, 0, 0, 0.05);
    }
    .progress-bar {
        height: 4px;
        background-color: #e2e8f0;
        border-radius: 2px;
        margin-top: 1rem;
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        background-color: #000;
        width: 0%;
        transition: width 0.3s ease;
    }
    .error-message {
        background-color: #fee2e2;
        border: 1px solid #ef4444;
        color: #b91c1c;
        padding: 0.75rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex items-center mb-6">
            <a href="{{ route('dashboard.properties') }}" class="mr-4 text-gray-600 hover:text-black">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold">Modifier un bien immobilier</h1>
        </div>
        
        <!-- Message d'erreur -->
        <div id="error-container" class="error-message hidden"></div>
        
        <form id="property-form" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Informations générales -->
            <div class="bg-white p-6 rounded-lg border shadow-sm">
                <h2 class="text-xl font-semibold mb-4">Informations générales</h2>
                
                <!-- Type de bien et transaction -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="type" class="block mb-1 font-medium">Type de bien</label>
                        <select id="type" name="type" class="w-full border rounded-lg px-3 py-2">
                            <option value="Maison" {{ $property->type == 'Maison' ? 'selected' : '' }}>Maison</option>
                            <option value="Terrain" {{ $property->type == 'Terrain' ? 'selected' : '' }}>Terrain</option>
                            <option value="LocalCommercial" {{ $property->type == 'LocalCommercial' ? 'selected' : '' }}>Local commercial</option>
                            <option value="Studio" {{ $property->type == 'Studio' ? 'selected' : '' }}>Studio</option>
                            <option value="Chambre" {{ $property->type == 'Chambre' ? 'selected' : '' }}>Chambre</option>
                            <option value="Meublé" {{ $property->type == 'Meublé' ? 'selected' : '' }}>Meublé</option>
                            <option value="Hotel" {{ $property->type == 'Hotel' ? 'selected' : '' }}>Hotel</option>
                        </select>
                    </div>
                    <div>
                        <label for="transaction_type" class="block mb-1 font-medium">Type de transaction</label>
                        <select id="transaction_type" name="transaction_type" class="w-full border rounded-lg px-3 py-2">
                            <option value="vente" {{ $property->transaction_type == 'vente' ? 'selected' : '' }}>Vente</option>
                            <option value="location" {{ $property->transaction_type == 'location' ? 'selected' : '' }}>Location</option>
                        </select>
                    </div>
                </div>
                
                <!-- Titre et description -->
                <div class="mb-4">
                    <label for="titre" class="block mb-1 font-medium">Titre de l'annonce</label>
                    <input type="text" id="titre" name="titre" value="{{ $property->titre }}" class="w-full border rounded-lg px-3 py-2" required>
                    <p class="text-sm text-gray-500 mt-1">Un titre accrocheur pour attirer l'attention (max 100 caractères)</p>
                </div>
                
                <div class="mb-4">
                    <label for="description" class="block mb-1 font-medium">Description</label>
                    <textarea id="description" name="description" rows="4" class="w-full border rounded-lg px-3 py-2" required>{{ $property->description }}</textarea>
                    <p class="text-sm text-gray-500 mt-1">Décrivez votre bien en détail (caractéristiques, état, environnement...)</p>
                </div>
                
                <!-- Prix et superficie -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="prix" class="block mb-1 font-medium">Prix (FCFA)</label>
                        <input type="number" id="prix" name="prix" value="{{ $property->prix }}" class="w-full border rounded-lg px-3 py-2" required>
                    </div>
                    <div>
                        <label for="superficie" class="block mb-1 font-medium">Superficie (m²)</label>
                        <input type="number" id="superficie" name="superficie" value="{{ $property->superficie }}" class="w-full border rounded-lg px-3 py-2" required>
                    </div>
                </div>
            </div>
            
            <!-- Localisation -->
            <div class="bg-white p-6 rounded-lg border shadow-sm">
                <h2 class="text-xl font-semibold mb-4">Localisation</h2>
                
                <!-- Adresse et localisation -->
                <div class="mb-4">
                    <label for="adresse" class="block mb-1 font-medium">Adresse</label>
                    <input type="text" id="adresse" name="adresse" value="{{ $property->adresse }}" class="w-full border rounded-lg px-3 py-2" required>
                </div>
                
                <div class="mb-4">
                    <label class="block mb-1 font-medium">Localisation sur la carte</label>
                    <p class="text-sm text-gray-500 mb-2">Déplacez le marqueur pour définir l'emplacement précis de votre bien</p>
                    <div id="map"></div>
                    <input type="hidden" id="latitude" name="latitude" value="{{ $property->latitude }}">
                    <input type="hidden" id="longitude" name="longitude" value="{{ $property->longitude }}">
                </div>
            </div>
            
            <!-- Médias -->
            <div class="bg-white p-6 rounded-lg border shadow-sm">
                <h2 class="text-xl font-semibold mb-4">Photos et vidéos</h2>
                
                <!-- Images actuelles -->
                <div class="mb-6">
                    <label class="block mb-1 font-medium">Images actuelles</label>
                    <div class="image-preview" id="current-images">
                        @php
                            $images = json_decode($property->images);
                        @endphp
                        
                        @foreach($images as $index => $image)
                            <div class="image-preview-item">
                                <img src="{{ $image }}" alt="Image {{ $index + 1 }}">
                                <div class="remove-image" data-index="{{ $index }}">
                                    <i class="fas fa-times"></i>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-2">
                        <label class="flex items-center">
                            <input type="checkbox" id="delete_images" name="delete_images" class="mr-2">
                            <span class="text-sm text-gray-700">Supprimer toutes les images et les remplacer par les nouvelles</span>
                        </label>
                    </div>
                </div>
                
                <!-- Upload de nouvelles images -->
                <div class="mb-6">
                    <label for="images" class="block mb-1 font-medium">Ajouter de nouvelles images</label>
                    
                    <!-- Zone de dépôt pour ordinateur -->
                    <div id="dropzone" class="dropzone">
                        <p class="text-gray-600 mb-2">Glissez vos images ici ou</p>
                        <label class="bg-black text-white px-4 py-2 rounded-lg cursor-pointer inline-block">
                            <span>Parcourir</span>
                            <input type="file" id="images" name="images[]" accept="image/*" multiple class="hidden">
                        </label>
                        <p class="text-sm text-gray-500 mt-2">Formats acceptés: JPG, PNG, GIF (max 5MB par image)</p>
                    </div>
                    
                    <div class="image-preview mt-4" id="new-image-preview"></div>
                </div>
                
                <!-- Vidéo actuelle -->
                @if($property->video)
                <div class="mb-4" id="current-video-container">
                    <label class="block mb-1 font-medium">Vidéo actuelle</label>
                    <video src="{{ $property->video }}" controls class="w-full rounded-lg"></video>
                    <button type="button" id="remove-current-video" class="mt-2 text-red-600 text-sm">
                        <i class="fas fa-trash mr-1"></i> Supprimer la vidéo
                    </button>
                </div>
                @endif
                
                <!-- Upload de nouvelle vidéo -->
                <div class="mb-4">
                    <label for="video" class="block mb-1 font-medium">{{ $property->video ? 'Remplacer la vidéo' : 'Ajouter une vidéo' }} (optionnel)</label>
                    <input type="file" id="video" name="video" accept="video/*" class="w-full border rounded-lg px-3 py-2">
                    <p class="text-sm text-gray-500 mt-1">Ajoutez une vidéo pour montrer votre bien (max 50MB)</p>
                    
                    <!-- Prévisualisation de la vidéo -->
                    <div id="video-preview-container" class="mt-4 hidden">
                        <video id="video-preview-player" controls class="w-full rounded-lg"></video>
                        <button type="button" id="remove-video-btn" class="mt-2 text-red-600 text-sm">
                            <i class="fas fa-trash mr-1"></i> Supprimer la vidéo
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Boutons de soumission -->
            <div class="flex justify-between">
                <a href="{{ route('dashboard.properties') }}" class="px-6 py-2 bg-gray-200 rounded-lg">Annuler</a>
                <button type="submit" class="px-6 py-2 bg-black text-white rounded-lg">Enregistrer les modifications</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de succès -->
<div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex justify-center items-center">
    <div class="bg-white rounded-lg max-w-md w-full p-6 text-center">
        <div class="mb-4">
            <i class="fas fa-check-circle text-green-500 text-5xl"></i>
        </div>
        <h2 class="text-2xl font-bold mb-2">Bien modifié avec succès !</h2>
        <p class="text-gray-600 mb-6">Votre bien immobilier a été mis à jour et les modifications sont maintenant visibles.</p>
        <div class="flex flex-col gap-2">
            <a href="{{ route('properties.show', $property->id) }}" class="bg-black text-white px-6 py-3 rounded-lg font-medium hover:bg-gray-800 transition">
                Voir mon bien
            </a>
            <a href="{{ route('dashboard.properties') }}" class="px-6 py-3 bg-gray-100 rounded-lg font-medium hover:bg-gray-200 transition">
                Gérer mes biens
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.js"></script>
<script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.min.js"></script>
<script>
    let map;
    let marker;
    let currentImages = @json(json_decode($property->images));
    let newImages = [];
    let deleteCurrentVideo = false;
    
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser la carte
        mapboxgl.accessToken = 'pk.eyJ1IjoiYXJ0aHVyLWNhZHJlbjIzNyIsImEiOiJjbTM1cjE0cGUwNW41Mmlvam56ZjRtdXQzIn0.fWB_Y31S2A3WeZvXoxjDxQ';
        
        map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v11',
            center: [{{ $property->longitude }}, {{ $property->latitude }}],
            zoom: 15
        });
        
        // Ajouter un marqueur déplaçable
        marker = new mapboxgl.Marker({
            draggable: true
        })
        .setLngLat([{{ $property->longitude }}, {{ $property->latitude }}])
        .addTo(map);
        
        // Mettre à jour les coordonnées quand le marqueur est déplacé
        marker.on('dragend', updateCoordinates);
        
        // Ajouter le geocoder pour la recherche d'adresses
        const geocoder = new MapboxGeocoder({
            accessToken: mapboxgl.accessToken,
            mapboxgl: mapboxgl,
            marker: false,
            placeholder: 'Rechercher une adresse'
        });
        
        map.addControl(geocoder);
        
        // Quand une adresse est sélectionnée, mettre à jour le marqueur
        geocoder.on('result', function(e) {
            const coordinates = e.result.center;
            marker.setLngLat(coordinates);
            updateCoordinates();
            
            // Mettre à jour le champ d'adresse
            document.getElementById('adresse').value = e.result.place_name;
        });
        
        // Gestion des images actuelles
        document.querySelectorAll('#current-images .remove-image').forEach(button => {
            button.addEventListener('click', function() {
                const index = parseInt(this.dataset.index);
                removeCurrentImage(index);
            });
        });
        
        // Gestion de la suppression de la vidéo actuelle
        if (document.getElementById('remove-current-video')) {
            document.getElementById('remove-current-video').addEventListener('click', function() {
                document.getElementById('current-video-container').classList.add('hidden');
                deleteCurrentVideo = true;
            });
        }
        
        // Gestion de la prévisualisation des nouvelles images
        const dropzone = document.getElementById('dropzone');
        const imagesInput = document.getElementById('images');
        
        // Événements pour le drag & drop
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            dropzone.classList.add('dragover');
        }
        
        function unhighlight() {
            dropzone.classList.remove('dragover');
        }
        
        // Gérer le drop
        dropzone.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            handleFiles(files);
        }
        
        // Gestion des fichiers
        imagesInput.addEventListener('change', function(e) {
            handleFiles(e.target.files);
        });
        
        // Gestion des vidéos
        document.getElementById('video').addEventListener('change', function(e) {
            handleVideoFile(e.target.files[0]);
        });
        
        // Bouton pour supprimer la vidéo
        document.getElementById('remove-video-btn').addEventListener('click', function() {
            document.getElementById('video-preview-container').classList.add('hidden');
            document.getElementById('video-preview-player').src = '';
            document.getElementById('video').value = '';
        });
        
        // Soumission du formulaire
        document.getElementById('property-form').addEventListener('submit', submitPropertyForm);
    });
    
    // Fonction pour mettre à jour les coordonnées
    function updateCoordinates() {
        const lngLat = marker.getLngLat();
        document.getElementById('latitude').value = lngLat.lat;
        document.getElementById('longitude').value = lngLat.lng;
    }
    
    // Fonction pour supprimer une image actuelle
    function removeCurrentImage(index) {
        // Supprimer l'élément du DOM
        document.querySelectorAll('#current-images .image-preview-item')[index].remove();
        
        // Supprimer l'image du tableau
        currentImages.splice(index, 1);
        
        // Mettre à jour les indices des autres boutons de suppression
        document.querySelectorAll('#current-images .remove-image').forEach((button, i) => {
            button.dataset.index = i;
        });
    }
    
    // Fonction pour gérer les fichiers d'images
    function handleFiles(files) {
        if (!files || files.length === 0) return;
        
        const preview = document.getElementById('new-image-preview');
        
        // Ajouter chaque image à la prévisualisation
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            
            // Vérifier si c'est une image
            if (!file.type.startsWith('image/')) continue;
            
            const reader = new FileReader();
            
            reader.onload = function(e) {
                // Créer un élément de prévisualisation
                const div = document.createElement('div');
                div.className = 'image-preview-item';
                
                const img = document.createElement('img');
                img.src = e.target.result;
                
                const removeButton = document.createElement('div');
                removeButton.className = 'remove-image';
                removeButton.innerHTML = '<i class="fas fa-times"></i>';
                removeButton.dataset.index = newImages.length;
                removeButton.addEventListener('click', function() {
                    removeNewImage(parseInt(this.dataset.index));
                });
                
                div.appendChild(img);
                div.appendChild(removeButton);
                preview.appendChild(div);
                
                // Ajouter l'image à la liste
                newImages.push(file);
            };
            
            reader.readAsDataURL(file);
        }
    }
    
    // Fonction pour supprimer une nouvelle image
    function removeNewImage(index) {
        // Supprimer l'élément du DOM
        document.querySelectorAll('#new-image-preview .image-preview-item')[index].remove();
        
        // Supprimer l'image du tableau
        newImages.splice(index, 1);
        
        // Mettre à jour les indices des autres boutons de suppression
        document.querySelectorAll('#new-image-preview .remove-image').forEach((button, i) => {
            button.dataset.index = i;
        });
    }
    
    // Fonction pour gérer les fichiers vidéo
    function handleVideoFile(file) {
        if (!file) return;
        
        // Vérifier si c'est une vidéo
        if (!file.type.startsWith('video/')) {
            alert('Veuillez sélectionner un fichier vidéo valide.');
            return;
        }
        
        // Vérifier la taille (max 50MB)
        if (file.size > 50 * 1024 * 1024) {
            alert('La vidéo est trop volumineuse. La taille maximale est de 50MB.');
            return;
        }
        
        // Créer une URL pour la prévisualisation
        const videoURL = URL.createObjectURL(file);
        const videoPlayer = document.getElementById('video-preview-player');
        videoPlayer.src = videoURL;
        
        // Afficher la prévisualisation
        document.getElementById('video-preview-container').classList.remove('hidden');
    }
    
    // Fonction pour soumettre le formulaire
    function submitPropertyForm(event) {
        event.preventDefault();
        
        // Masquer les messages d'erreur précédents
        const errorContainer = document.getElementById('error-container');
        errorContainer.classList.add('hidden');
        errorContainer.innerHTML = '';
        
        const form = event.target;
        const formData = new FormData();
        
        // Ajouter les champs du formulaire
        formData.append('_method', 'PUT'); // Pour simuler une requête PUT
        formData.append('titre', document.getElementById('titre').value);
        formData.append('description', document.getElementById('description').value);
        formData.append('type', document.getElementById('type').value);
        formData.append('adresse', document.getElementById('adresse').value);
        formData.append('prix', document.getElementById('prix').value);
        formData.append('superficie', document.getElementById('superficie').value);
        formData.append('latitude', document.getElementById('latitude').value);
        formData.append('longitude', document.getElementById('longitude').value);
        formData.append('transaction_type', document.getElementById('transaction_type').value);
        
        // Ajouter les images actuelles (sauf si on veut toutes les supprimer)
        if (!document.getElementById('delete_images').checked) {
            formData.append('current_images', JSON.stringify(currentImages));
        } else {
            formData.append('delete_images', true);
        }
        
        // Ajouter les nouvelles images
        newImages.forEach(file => {
            formData.append('images[]', file);
        });
        
        // Gérer la vidéo
        if (deleteCurrentVideo) {
            formData.append('delete_video', true);
        }
        
        if (document.getElementById('video').files.length > 0) {
            formData.append('video', document.getElementById('video').files[0]);
        }
        
        // Afficher un indicateur de chargement
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Enregistrement en cours...';
        submitButton.disabled = true;
        
        // Envoyer les données
        fetch('/api/properties/{{ $property->id }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Afficher le modal de succès
                document.getElementById('successModal').classList.remove('hidden');
            } else {
                // Afficher les erreurs détaillées
                errorContainer.classList.remove('hidden');
                
                if (data.errors) {
                    let errorHTML = '<ul class="list-disc pl-5">';
                    for (const field in data.errors) {
                        data.errors[field].forEach(error => {
                            errorHTML += `<li>${error}</li>`;
                        });
                    }
                    errorHTML += '</ul>';
                    errorContainer.innerHTML = errorHTML;
                } else {
                    errorContainer.textContent = data.message || 'Une erreur est survenue lors de la modification du bien';
                }
                
                // Faire défiler jusqu'au message d'erreur
                errorContainer.scrollIntoView({ behavior: 'smooth' });
                
                // Restaurer le bouton
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            }
        })
        .catch(error => {
            console.error('Erreur lors de la modification du bien:', error);
            
            // Afficher l'erreur
            errorContainer.classList.remove('hidden');
            errorContainer.textContent = 'Une erreur est survenue lors de la modification du bien. Veuillez réessayer.';
            
            // Faire défiler jusqu'au message d'erreur
            errorContainer.scrollIntoView({ behavior: 'smooth' });
            
            // Restaurer le bouton
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        });
    }
</script>
@endpush