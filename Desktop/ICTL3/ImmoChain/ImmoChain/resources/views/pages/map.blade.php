@extends('layouts.app')

@section('title', 'ImmoChain - Carte des biens')

@push('styles')
<link href="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.css" rel="stylesheet">
<link href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.css" rel="stylesheet">
<style>
    #map {
        width: 100%;
        height: 100%;
    }
    .mapboxgl-popup {
        max-width: 300px;
    }
    .property-popup {
        padding: 0;
    }
    .property-popup img {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 4px 4px 0 0;
    }
    .property-popup-content {
        padding: 10px;
    }
    .filter-panel {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        max-height: calc(100vh - 100px);
        overflow-y: auto;
    }
    .filter-panel::-webkit-scrollbar {
        width: 6px;
    }
    .filter-panel::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
        border-radius: 3px;
    }
    .range-slider {
        -webkit-appearance: none;
        width: 100%;
        height: 6px;
        border-radius: 5px;
        background: #d3d3d3;
        outline: none;
    }
    .range-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: black;
        cursor: pointer;
    }
    .range-slider::-moz-range-thumb {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: black;
        cursor: pointer;
    }
    /* Styles pour les écrans mobiles */
    @media (max-width: 768px) {
        .map-container {
            height: calc(100vh - 180px);
        }
        .filter-container {
            height: auto;
        }
        .mobile-map-toggle {
            display: block;
        }
        .mobile-filters-toggle {
            display: block;
        }
        .mobile-filter-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: white;
            padding: 10px;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 10;
            display: flex;
            justify-content: space-around;
        }
        .mobile-filter-button {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.875rem;
        }
        .mobile-filter-button i {
            font-size: 1.25rem;
            margin-bottom: 4px;
        }
    }
    .mobile-map-toggle, .mobile-filters-toggle {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="h-[calc(100vh-80px)] flex flex-col md:flex-row">
    <!-- Boutons de basculement pour mobile -->
    <div class="flex justify-center gap-4 p-2 md:hidden">
        <button id="show-filters" class="mobile-filters-toggle px-4 py-2 bg-black text-white rounded-lg">
            <i class="fas fa-filter mr-2"></i>Filtres
        </button>
        <button id="show-map" class="mobile-map-toggle px-4 py-2 bg-black text-white rounded-lg">
            <i class="fas fa-map mr-2"></i>Carte
        </button>
    </div>

    <!-- Panneau de filtres -->
    <div id="filters-panel" class="w-full md:w-1/3 lg:w-1/4 p-4 overflow-y-auto filter-container">
        <div class="filter-panel p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Filtres de recherche</h2>
                <button id="close-filters" class="md:hidden text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <p class="text-sm text-gray-500 mb-4">Affinez votre recherche immobilière</p>

            <form id="filter-form" class="space-y-5">
                <!-- Recherche par lieu -->
                <div>
                    <label for="location-search" class="block mb-2 font-medium">Localisation</label>
                    <div id="geocoder" class="w-full"></div>
                </div>

                <!-- Type de transaction -->
                <div>
                    <label class="block mb-2 font-medium">Type de transaction</label>
                    <div class="flex gap-2">
                        <label class="flex items-center">
                            <input type="radio" name="transaction_type" value="all" checked class="mr-2">
                            <span>Tous</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="transaction_type" value="vente" class="mr-2">
                            <span>Vente</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="transaction_type" value="location" class="mr-2">
                            <span>Location</span>
                        </label>
                    </div>
                </div>

                <!-- Type de bien -->
                <div>
                    <label class="block mb-2 font-medium">Type de bien</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="property_type[]" value="Maison" class="mr-2">
                            <span>Maison</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="property_type[]" value="Terrain" class="mr-2">
                            <span>Terrain</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="property_type[]" value="LocalCommercial" class="mr-2">
                            <span>Local commercial</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="property_type[]" value="Studio" class="mr-2">
                            <span>Studio</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="property_type[]" value="Chambre" class="mr-2">
                            <span>Chambre</span>
                        </label>
                    </div>
                </div>

                <!-- Prix -->
                <div>
                    <label class="block mb-2 font-medium">Prix (FCFA)</label>
                    <div class="flex items-center gap-2 mb-2">
                        <input type="number" id="min-price" name="min_price" placeholder="Min" class="w-full border rounded-lg px-3 py-2 filter-input">
                        <span>-</span>
                        <input type="number" id="max-price" name="max_price" placeholder="Max" class="w-full border rounded-lg px-3 py-2 filter-input">
                    </div>
                    <div class="px-1">
                        <input type="range" id="price-slider" class="range-slider filter-input" min="0" max="100000000" step="100000">
                    </div>
                </div>

                <!-- Superficie -->
                <div>
                    <label class="block mb-2 font-medium">Superficie (m²)</label>
                    <div class="flex items-center gap-2 mb-2">
                        <input type="number" id="min-area" name="min_area" placeholder="Min" class="w-full border rounded-lg px-3 py-2 filter-input">
                        <span>-</span>
                        <input type="number" id="max-area" name="max_area" placeholder="Max" class="w-full border rounded-lg px-3 py-2 filter-input">
                    </div>
                </div>

                <!-- Nombre de pièces -->
                <div>
                    <label class="block mb-2 font-medium">Nombre de pièces</label>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" class="rooms-btn px-3 py-1 border rounded-lg hover:bg-black hover:text-white" data-value="1">1</button>
                        <button type="button" class="rooms-btn px-3 py-1 border rounded-lg hover:bg-black hover:text-white" data-value="2">2</button>
                        <button type="button" class="rooms-btn px-3 py-1 border rounded-lg hover:bg-black hover:text-white" data-value="3">3</button>
                        <button type="button" class="rooms-btn px-3 py-1 border rounded-lg hover:bg-black hover:text-white" data-value="4">4</button>
                        <button type="button" class="rooms-btn px-3 py-1 border rounded-lg hover:bg-black hover:text-white" data-value="5+">5+</button>
                    </div>
                    <input type="hidden" id="rooms" name="rooms">
                </div>

                <!-- Boutons d'action -->
                <div class="flex gap-2">
                    <button type="button" id="apply-filters" class="flex-1 bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition">
                        Appliquer
                    </button>
                    <button type="button" id="reset-filters" class="px-4 py-2 border rounded-lg hover:border-black">
                        Réinitialiser
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Carte -->
    <div id="map-container" class="w-full md:w-2/3 lg:w-3/4 bg-gray-100 map-container hidden md:block">
        <div id="map"></div>
    </div>
    
    <!-- Barre de filtres mobile fixe -->
    <div class="mobile-filter-bar md:hidden">
        <button id="mobile-show-filters" class="mobile-filter-button bg-gray-100">
            <i class="fas fa-filter"></i>
            <span>Filtres</span>
        </button>
        <button id="mobile-sort" class="mobile-filter-button">
            <i class="fas fa-sort-amount-down"></i>
            <span>Trier</span>
        </button>
        <button id="mobile-view-list" class="mobile-filter-button">
            <i class="fas fa-list"></i>
            <span>Liste</span>
        </button>
        <button id="mobile-view-map" class="mobile-filter-button bg-gray-100">
            <i class="fas fa-map-marker-alt"></i>
            <span>Carte</span>
        </button>
    </div>
</div>

<!-- Template pour les propriétés -->
<template id="property-template">
    <div class="flex gap-4 p-4 hover:bg-gray-50 rounded-xl cursor-pointer property-card-transition" data-id="">
        <!-- Images -->
        <div class="relative w-40 h-40 flex-shrink-0">
            <img src="/placeholder.svg" alt="" class="w-full h-full object-cover rounded-xl">
            <button class="absolute top-2 right-2 z-10 text-white hover:scale-110 transition favorite-btn">
                <i class="far fa-heart text-xl drop-shadow-lg"></i>
            </button>
        </div>

        <!-- Informations -->
        <div class="flex-1">
            <div class="flex justify-between items-start">
                <h3 class="font-semibold property-title"></h3>
                <div class="property-type px-2 py-1 text-xs rounded-full bg-gray-100"></div>
            </div>
            <p class="text-gray-500 property-address"></p>
            <p class="text-gray-500 property-details"></p>
            <p class="mt-2">
                <span class="font-semibold property-price"></span>
                <span class="property-transaction-type"></span>
            </p>
        </div>
    </div>
</template>

<!-- Popup de visite virtuelle -->
<div id="virtual-tour-popup" class="virtual-tour-popup hidden">
    <div class="close-btn" onclick="closeVirtualTour()">
        <i class="fas fa-times"></i>
    </div>
    <div class="virtual-tour-content">
        <div class="virtual-tour-swiper">
            <div class="swiper-wrapper" id="virtual-tour-slides">
                <!-- Les images et vidéos seront ajoutées ici dynamiquement -->
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.js"></script>
<script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.min.js"></script>
<script>
    // Initialisation de Mapbox
    mapboxgl.accessToken = 'pk.eyJ1IjoiYXJ0aHVyLWNhZHJlbjIzNyIsImEiOiJjbTM1cjE0cGUwNW41Mmlvam56ZjRtdXQzIn0.fWB_Y31S2A3WeZvXoxjDxQ';
    
    let map;
    let markers = [];
    let properties = [];
    let geocoder;
    let selectedRooms = [];
    let virtualTourSwiper;
    let isMobile = window.innerWidth < 768;
    let isFilterFocused = false;

    document.addEventListener('DOMContentLoaded', function() {
        // Gestion de l'affichage mobile
        setupMobileView();
        
        // Initialiser la carte
        initMap();
        
        // Événements pour les filtres
        document.getElementById('apply-filters').addEventListener('click', applyFilters);
        document.getElementById('reset-filters').addEventListener('click', resetFilters);
        
        // Événements pour le slider de prix
        const priceSlider = document.getElementById('price-slider');
        const minPriceInput = document.getElementById('min-price');
        const maxPriceInput = document.getElementById('max-price');
        
        priceSlider.addEventListener('input', function() {
            maxPriceInput.value = this.value;
        });
        
        minPriceInput.addEventListener('input', function() {
            if (parseInt(this.value) > parseInt(maxPriceInput.value)) {
                maxPriceInput.value = this.value;
            }
        });
        
        // Événements pour les boutons de nombre de pièces
        document.querySelectorAll('.rooms-btn').forEach(button => {
            button.addEventListener('click', function() {
                const value = this.dataset.value;
                
                if (this.classList.contains('bg-black')) {
                    // Désélectionner
                    this.classList.remove('bg-black', 'text-white');
                    selectedRooms = selectedRooms.filter(room => room !== value);
                } else {
                    // Sélectionner
                    this.classList.add('bg-black', 'text-white');
                    selectedRooms.push(value);
                }
                
                document.getElementById('rooms').value = selectedRooms.join(',');
            });
        });
        
        // Empêcher la fermeture du panneau de filtres lors de la saisie dans les champs
        document.querySelectorAll('.filter-input').forEach(input => {
            input.addEventListener('focus', function() {
                isFilterFocused = true;
            });
            
            input.addEventListener('blur', function() {
                isFilterFocused = false;
            });
        });
        
        // Gestion des boutons mobiles
        if (isMobile) {
            document.getElementById('show-filters').addEventListener('click', function() {
                document.getElementById('filters-panel').style.display = 'block';
                document.getElementById('map-container').style.display = 'none';
            });
            
            document.getElementById('show-map').addEventListener('click', function() {
                if (!isFilterFocused) {
                    document.getElementById('filters-panel').style.display = 'none';
                    document.getElementById('map-container').style.display = 'block';
                }
            });
            
            document.getElementById('close-filters').addEventListener('click', function() {
                if (!isFilterFocused) {
                    document.getElementById('filters-panel').style.display = 'none';
                    document.getElementById('map-container').style.display = 'block';
                }
            });
            
            // Nouveau: Gestion du bouton de filtre mobile fixe
            document.getElementById('mobile-show-filters').addEventListener('click', function() {
                document.getElementById('filters-panel').style.display = 'block';
                document.getElementById('map-container').style.display = 'none';
            });
            
            document.getElementById('mobile-view-map').addEventListener('click', function() {
                if (!isFilterFocused) {
                    document.getElementById('filters-panel').style.display = 'none';
                    document.getElementById('map-container').style.display = 'block';
                }
            });
        }
        
        // Gérer le redimensionnement de la fenêtre
        window.addEventListener('resize', function() {
            isMobile = window.innerWidth < 768;
            setupMobileView();
            if (map) map.resize();
        });
    });
    
    // Fonction pour configurer l'affichage mobile
    function setupMobileView() {
        if (window.innerWidth < 768) {
            // Afficher la carte par défaut sur mobile, mais garder la barre de filtres visible
            if (!isFilterFocused) {
                document.getElementById('filters-panel').style.display = 'none';
                document.getElementById('map-container').style.display = 'block';
                document.getElementById('map-container').classList.remove('hidden');
            }
        } else {
            // Afficher les deux sur desktop
            document.getElementById('filters-panel').style.display = 'block';
            document.getElementById('map-container').style.display = 'block';
            document.getElementById('map-container').classList.remove('hidden');
        }
    }
    
    // Fonction pour initialiser la carte
    function initMap() {
        map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v11',
            center: [11.5174, 3.8721], // Coordonnées de Yaoundé
            zoom: 12
        });

        // Ajouter les contrôles de navigation
        map.addControl(new mapboxgl.NavigationControl());
        map.addControl(new mapboxgl.GeolocateControl({
            positionOptions: {
                enableHighAccuracy: true
            },
            trackUserLocation: true
        }));

        // Initialiser le geocoder
        geocoder = new MapboxGeocoder({
            accessToken: mapboxgl.accessToken,
            mapboxgl: mapboxgl,
            placeholder: 'Rechercher un lieu...',
            marker: false,
            countries: 'cm' // Limiter à Cameroun
        });
        document.getElementById('geocoder').appendChild(geocoder.onAdd(map));
        
        // Empêcher le geocoder de fermer le panneau de filtres
        const geocoderInput = document.querySelector('.mapboxgl-ctrl-geocoder--input');
        if (geocoderInput) {
            geocoderInput.addEventListener('focus', function() {
                isFilterFocused = true;
            });
            
            geocoderInput.addEventListener('blur', function() {
                isFilterFocused = false;
            });
        }

        // Attendre que la carte soit chargée
        map.on('load', function() {
            // Charger les propriétés
            fetchProperties();
        });
    }

    // Fonction pour récupérer les propriétés
    function fetchProperties() {
        fetch('/api/properties')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                properties = data;
                addMarkersToMap(properties);
            })
            .catch(error => {
                console.error('Erreur lors du chargement des propriétés:', error);
                alert('Erreur lors du chargement des propriétés. Veuillez réessayer.');
            });
    }

    // Fonction pour ajouter les marqueurs sur la carte
    function addMarkersToMap(properties) {
        // Supprimer les marqueurs existants
        markers.forEach(marker => marker.remove());
        markers = [];

        if (properties.length === 0) {
            // Aucune propriété à afficher
            return;
        }

        const bounds = new mapboxgl.LngLatBounds();

        properties.forEach(property => {
            // Vérifier si les coordonnées sont valides
            if (!property.latitude || !property.longitude) {
                return;
            }

            // Créer un élément pour le popup
            const popupElement = document.createElement('div');
            popupElement.className = 'property-popup';
            
            // Traiter les images
            let images = [];
            if (typeof property.images === 'string') {
                try {
                    images = JSON.parse(property.images);
                } catch (e) {
                    console.error('Erreur lors du parsing des images:', e);
                    images = [];
                }
            } else {
                images = property.images || [];
            }
            
            // S'assurer qu'il y a au moins une image
            if (!images || images.length === 0) {
                images = ['/placeholder.svg?height=300&width=300'];
            }
            
            // Ajouter l'image
            const img = document.createElement('img');
            img.src = images[0];
            img.alt = property.titre;
            img.onerror = function() {
                this.src = '/placeholder.svg?height=300&width=300';
            };
            popupElement.appendChild(img);
            
            // Ajouter le contenu
            const content = document.createElement('div');
            content.className = 'property-popup-content';
            content.innerHTML = `
                <h3 class="font-semibold">${property.titre}</h3>
                <p>${property.adresse}</p>
                <p class="font-semibold">${property.prix.toLocaleString()} FCFA ${property.transaction_type === 'vente' ? 'à l\'achat' : 'par mois'}</p>
                <div class="flex gap-2 mt-2">
                    <button class="details-btn px-4 py-1 bg-black text-white rounded-lg text-sm flex-1">Voir détails</button>
                    <button class="virtual-tour-btn px-4 py-1 bg-blue-600 text-white rounded-lg text-sm flex-1">Visite virtuelle</button>
                </div>
            `;
            popupElement.appendChild(content);
            
            // Créer le popup
            const popup = new mapboxgl.Popup({ offset: 25 })
                .setDOMContent(popupElement);
            
            // Ajouter des événements de clic sur les boutons
            popupElement.querySelector('.details-btn').addEventListener('click', () => {
                window.location.href = `/properties/${property.id}`;
            });
            
            popupElement.querySelector('.virtual-tour-btn').addEventListener('click', () => {
                startVirtualTour(property);
            });
            
            // Créer le marqueur
            const marker = new mapboxgl.Marker({
                color: property.transaction_type === 'vente' ? '#ef4444' : '#3b82f6'
            })
                .setLngLat([property.longitude, property.latitude])
                .setPopup(popup)
                .addTo(map);
            
            markers.push(marker);
            
            // Étendre les limites pour inclure ce marqueur
            bounds.extend([property.longitude, property.latitude]);
        });

        // Ajuster la vue de la carte pour inclure tous les marqueurs
        if (!bounds.isEmpty()) {
            map.fitBounds(bounds, {
                padding: 50,
                maxZoom: 15
            });
        }
    }

    // Fonction pour démarrer la visite virtuelle
    function startVirtualTour(property) {
        const slidesContainer = document.getElementById('virtual-tour-slides');
        slidesContainer.innerHTML = '';
        
        // Traiter les images
        let images = [];
        if (typeof property.images === 'string') {
            try {
                images = JSON.parse(property.images);
            } catch (e) {
                console.error('Erreur lors du parsing des images:', e);
                images = [];
            }
        } else {
            images = property.images || [];
        }
        
        // Ajouter chaque image comme slide
        images.forEach(image => {
            const slide = document.createElement('div');
            slide.className = 'swiper-slide';
            
            const img = document.createElement('img');
            img.src = image;
            img.alt = property.titre;
            
            slide.appendChild(img);
            slidesContainer.appendChild(slide);
        });
        
        // Ajouter la vidéo si elle existe
        if (property.video) {
            const slide = document.createElement('div');
            slide.className = 'swiper-slide';
            
            const video = document.createElement('video');
            video.src = property.video;
            video.controls = true;
            video.autoplay = false;
            
            slide.appendChild(video);
            slidesContainer.appendChild(slide);
        }
        
        // Afficher le popup de visite virtuelle
        document.getElementById('virtual-tour-popup').classList.remove('hidden');
    }

    // Fonction pour fermer la visite virtuelle
    function closeVirtualTour() {
        document.getElementById('virtual-tour-popup').classList.add('hidden');
        
        // Arrêter les vidéos en cours de lecture
        const videos = document.querySelectorAll('#virtual-tour-slides video');
        videos.forEach(video => {
            video.pause();
        });
    }

    // Fonction pour appliquer les filtres
    function applyFilters() {
        const transactionType = document.querySelector('input[name="transaction_type"]:checked').value;
        const propertyTypes = Array.from(document.querySelectorAll('input[name="property_type[]"]:checked')).map(input => input.value);
        const minPrice = document.getElementById('min-price').value;
        const maxPrice = document.getElementById('max-price').value;
        const minArea = document.getElementById('min-area').value;
        const maxArea = document.getElementById('max-area').value;
        const rooms = selectedRooms;

        // Filtrer les propriétés
        const filteredProperties = properties.filter(property => {
            // Filtre par type de transaction
            if (transactionType !== 'all' && property.transaction_type !== transactionType) {
                return false;
            }

            // Filtre par type de bien
            if (propertyTypes.length > 0 && !propertyTypes.includes(property.type)) {
                return false;
            }

            // Filtre par prix minimum
            if (minPrice && property.prix < parseInt(minPrice)) {
                return false;
            }

            // Filtre par prix maximum
            if (maxPrice && property.prix > parseInt(maxPrice)) {
                return false;
            }

            // Filtre par superficie minimum
            if (minArea && property.superficie < parseInt(minArea)) {
                return false;
            }

            // Filtre par superficie maximum
            if (maxArea && property.superficie > parseInt(maxArea)) {
                return false;
            }

            return true;
        });

        // Mettre à jour les marqueurs sur la carte
        addMarkersToMap(filteredProperties);
        
        // Sur mobile, afficher la carte après avoir appliqué les filtres
        if (isMobile && !isFilterFocused) {
            document.getElementById('filters-panel').style.display = 'none';
            document.getElementById('map-container').style.display = 'block';
        }
    }

    // Fonction pour réinitialiser les filtres
    function resetFilters() {
        // Réinitialiser le type de transaction
        document.querySelector('input[name="transaction_type"][value="all"]').checked = true;

        // Réinitialiser les types de bien
        document.querySelectorAll('input[name="property_type[]"]').forEach(input => {
            input.checked = false;
        });

        // Réinitialiser les prix
        document.getElementById('min-price').value = '';
        document.getElementById('max-price').value = '';
        document.getElementById('price-slider').value = 100000000;

        // Réinitialiser les superficies
        document.getElementById('min-area').value = '';
        document.getElementById('max-area').value = '';

        // Réinitialiser les pièces
        document.querySelectorAll('.rooms-btn').forEach(button => {
            button.classList.remove('bg-black', 'text-white');
        });
        selectedRooms = [];
        document.getElementById('rooms').value = '';

        // Réinitialiser le geocoder
        geocoder.clear();

        // Recharger toutes les propriétés
        addMarkersToMap(properties);
    }
</script>
@endpush