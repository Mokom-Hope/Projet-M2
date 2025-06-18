@extends('layouts.app')

@section('title', 'ImmoChain - Détails du bien')

@push('styles')
<link href="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.css" rel="stylesheet">
<style>
    .swiper-container {
        width: 100%;
        height: 500px;
    }
    .swiper-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    #property-map {
        width: 100%;
        height: 300px;
        border-radius: 0.5rem;
    }
    .virtual-tour-container {
        width: 100%;
        height: 600px;
    }
    .error-message {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    .date-error {
        display: none;
        color: #ef4444;
    }
    .owner-info-locked {
        background-color: #f9fafb;
        border-radius: 0.5rem;
        padding: 1rem;
        text-align: center;
        margin-top: 1rem;
    }
    .owner-info-locked i {
        font-size: 2rem;
        color: #9ca3af;
        margin-bottom: 0.5rem;
    }
    .owner-info-locked p {
        color: #4b5563;
        margin-bottom: 1rem;
    }
    .owner-info-visible {
        animation: fadeIn 0.5s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .placeholder-image {
        background-color: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        font-size: 1rem;
    }

    #virtualTourModal {
        z-index: 9999 !important; /* Supérieur au header (z-50) */
        position: fixed;
        inset: 0; /* top: 0; right: 0; bottom: 0; left: 0 */
        overflow: hidden;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <!-- Galerie d'images -->
        <div class="swiper-container rounded-xl overflow-hidden">
            <div class="swiper-wrapper" id="property-images">
                <!-- Les images seront chargées dynamiquement ici -->
                <div class="swiper-slide">
                    <div class="placeholder-image w-full h-full">
                        <span>Chargement des images...</span>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>

    <!-- Le reste du contenu reste inchangé -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="mb-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold" id="property-title"></h1>
                        <p class="text-gray-600" id="property-address"></p>
                    </div>
                    <div class="flex items-center">
                        <span class="px-3 py-1 bg-gray-100 rounded-full text-sm" id="property-type"></span>
                        <!-- boutton pour partager sur les reseaux sociaux -->
                        <button id="share-button" class="p-2 text-gray-500 hover:text-black hover:bg-gray-100 rounded-full transition-colors" title="Partager">
                            <i class="fas fa-share-alt"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-2">Description</h2>
                <p class="text-gray-700" id="property-description"></p>
            </div>

            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-2">Caractéristiques</h2>
                <div class="grid grid-cols-2 gap-4" id="property-features">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-ruler-combined text-gray-500"></i>
                        <span id="property-area"></span>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-2">Localisation</h2>
                <div id="property-map" class="mt-2"></div>
            </div>
        </div>

        <div>
            <div class="bg-white p-6 rounded-xl border shadow-sm sticky top-24">
                <div class="mb-4">
                    <div class="flex items-baseline">
                        <span class="text-2xl font-bold" id="property-price"></span>
                        <span class="ml-1 text-gray-600" id="property-price-type"></span>
                    </div>
                </div>

                <div id="reservation-form" class="space-y-4">
                    <div>
                        <label for="visit-date" class="block text-sm font-medium text-gray-700 mb-1">Date de visite</label>
                        <input type="date" id="visit-date" name="visit_date" class="w-full p-2 border rounded-lg">
                        <p class="text-sm text-gray-500 mt-1">La date doit être dans les 3 prochains jours</p>
                        <p id="date-error" class="date-error"></p>
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message au propriétaire</label>
                        <textarea id="message" name="message" rows="3" class="w-full p-2 border rounded-lg"></textarea>
                    </div>
                    <button id="reserve-button" class="w-full bg-black text-white py-3 rounded-lg font-medium hover:bg-gray-800 transition">
                        Réserver ce bien (500 FCFA)
                    </button>
                    <p class="text-xs text-gray-500 text-center">
                        En réservant, vous payez 500 FCFA pour accéder aux coordonnées du propriétaire
                    </p>
                </div>

                <div id="owner-info" class="mt-6 pt-6 border-t">
                    <h3 class="font-semibold mb-2">Propriétaire</h3>
                    
                    <!-- Version verrouillée (par défaut) -->
                    <div id="owner-info-locked" class="owner-info-locked">
                        <i class="fas fa-lock"></i>
                        <p>Les coordonnées du propriétaire sont accessibles après paiement</p>
                        <button id="unlock-owner-info" class="px-4 py-2 bg-black text-white rounded-lg text-sm font-medium hover:bg-gray-800 transition">
                            Débloquer pour 500 FCFA
                        </button>
                    </div>
                    
                    <!-- Version débloquée (après paiement) -->
                    <div id="owner-info-visible" class="hidden owner-info-visible">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-user text-gray-500"></i>
                            </div>
                            <div>
                                <p class="font-medium" id="owner-name"></p>
                                <p class="text-sm text-gray-500">Membre depuis <span id="owner-since"></span></p>
                            </div>
                        </div>
                        <div class="mt-4 space-y-2">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-envelope text-gray-500"></i>
                                <span id="owner-email"></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-phone text-gray-500"></i>
                                <span id="owner-phone"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ajouter cette section après la section des caractéristiques du bien -->
@if($property->blockchain_registered)
<div class="mb-6">
    <h2 class="text-xl font-semibold mb-2">Certification Blockchain</h2>
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-center">
            <i class="fas fa-shield-alt text-green-500 text-2xl mr-3"></i>
            <div>
                <p class="font-medium text-green-800">Ce bien est certifié sur la blockchain</p>
                <p class="text-sm text-green-700 mt-1">
                    Les informations de ce bien immobilier sont enregistrées de manière sécurisée et immuable sur la blockchain Polygon.
                </p>
                @if($property->blockchain_tx)
                <a href="{{ $property->getBlockchainExplorerUrl() }}" target="_blank" class="inline-flex items-center text-sm text-green-600 hover:text-green-800 mt-2">
                    <i class="fas fa-external-link-alt mr-1"></i> Voir la transaction sur l'explorateur blockchain
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endif


    <!-- Visite virtuelle -->
    <div class="mt-12 mb-8">
        <h2 class="text-xl font-semibold mb-4">Visite virtuelle</h2>
        <div class="bg-gray-100 rounded-xl p-8 text-center" id="virtual-tour-container">
            <p class="text-gray-500 mb-4">Explorez les alentours de ce bien en 3D</p>
            <button id="start-virtual-tour" class="px-6 py-3 bg-black text-white rounded-lg font-medium hover:bg-gray-800 transition">
                Démarrer la visite virtuelle
            </button>
        </div>
    </div>
</div>

<!-- Modal de réservation réussie -->
<div id="reservationSuccessModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex justify-center items-center">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <div class="text-center">
            <i class="fas fa-check-circle text-green-500 text-5xl mb-4"></i>
            <h2 class="text-2xl font-bold mb-2">Réservation effectuée !</h2>
            <p class="text-gray-600 mb-6">Votre demande de réservation a été envoyée au propriétaire. Vous avez maintenant accès à ses coordonnées.</p>
            <div class="flex justify-center">
                <button onclick="toggleModal('reservationSuccessModal')" class="px-6 py-3 bg-black text-white rounded-lg font-medium hover:bg-gray-800 transition">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de visite virtuelle -->
<div id="virtualTourModal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden">
    <div class="h-full flex flex-col">
        <div class="p-4 flex justify-between items-center">
            <h2 class="text-white text-xl font-semibold">Visite virtuelle - Environnement</h2>
            <button onclick="toggleModal('virtualTourModal')" class="text-white text-2xl">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="flex-1 flex items-center justify-center">
            <div id="virtual-tour-content" class="w-full h-full max-w-5xl mx-auto">
                <!-- Le contenu de la visite virtuelle sera chargé ici -->
            </div>
        </div>
    </div>
</div>

<!-- Modal d'erreur -->
<div id="errorModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex justify-center items-center">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <div class="text-center">
            <i class="fas fa-exclamation-circle text-red-500 text-5xl mb-4"></i>
            <h2 class="text-2xl font-bold mb-2">Erreur</h2>
            <p id="error-message" class="text-gray-600 mb-6">Une erreur est survenue.</p>
            <div class="flex justify-center">
                <button onclick="toggleModal('errorModal')" class="px-6 py-3 bg-black text-white rounded-lg font-medium hover:bg-gray-800 transition">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de paiement en cours -->
<div id="paymentProcessingModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex justify-center items-center">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-black mx-auto mb-4"></div>
            <h2 class="text-xl font-bold mb-2">Redirection vers NotchPay</h2>
            <p class="text-gray-600 mb-6">Vous allez être redirigé vers la page de paiement sécurisée NotchPay...</p>
        </div>
    </div>
</div>


<!--paiement mobile vue-->

<!-- Ajouter ce modal pour les paiements mobiles après les autres modals -->
<div id="mobilePaymentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex justify-center items-center">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <div class="text-center">
            <h2 class="text-xl font-bold mb-4">Paiement Mobile</h2>
            <p class="text-gray-600 mb-4">Veuillez entrer votre numéro de téléphone pour finaliser le paiement.</p>
            
            <form id="mobilePaymentForm" class="space-y-4">
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Numéro de téléphone</label>
                    <input type="tel" id="phone" name="phone" class="w-full p-2 border rounded-lg" placeholder="+237 6XX XXX XXX" required>
                </div>
                
                <div>
                    <label for="channel" class="block text-sm font-medium text-gray-700 mb-1">Opérateur</label>
                    <select id="channel" name="channel" class="w-full p-2 border rounded-lg" required>
                        <option value="cm.mtn">MTN Mobile Money</option>
                        <option value="cm.orange">Orange Money</option>
                    </select>
                </div>
                
                <input type="hidden" id="payment-reference" name="reference">
                
                <div class="flex space-x-3">
                    <button type="button" onclick="toggleModal('mobilePaymentModal')" class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg">
                        Annuler
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-black text-white rounded-lg">
                        Payer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@include('properties.partage')


@endsection

@push('scripts')
<script src="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
<script>
    let property = null;
    let map = null;
    let swiper = null;
    let isAuthenticated = {{ Auth::check() ? 'true' : 'false' }};
    let hasOwnerInfoAccess = false;
    let isInitialized = false; // Pour éviter des initialisations multiples

    document.addEventListener('DOMContentLoaded', function () {
        // Récupérer l'ID du bien depuis l'URL
        const propertyId = window.location.pathname.split('/').pop();

        // Charger les détails du bien
        loadPropertyDetails(propertyId);

        // Initialiser les événements
        initializeEvents(propertyId);

        // Vérifier si l'utilisateur a accès aux informations du propriétaire
        if (isAuthenticated) {
            checkOwnerInfoAccess(propertyId);
        }
    });

    // Fonction pour charger les détails du bien
    function loadPropertyDetails(propertyId) {
        fetch(`/api/properties/${propertyId}`)
            .then(response => {
                if (!response.ok) throw new Error('Erreur réseau: ' + response.status);
                return response.json();
            })
            .then(data => {
                property = data;
                displayPropertyDetails(property);
                initializeMap(property);
                initializeSwiper(property);
                setupDateValidation();
            })
            .catch(error => {
                console.error('Erreur lors du chargement:', error);
                showError('Impossible de charger les détails du bien.');
            });
    }

    // Fonction pour vérifier l'accès aux informations du propriétaire
    function checkOwnerInfoAccess(propertyId) {
        fetch(`/payments/check-access?property_id=${propertyId}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.has_access) {
                hasOwnerInfoAccess = true;
                loadOwnerInfo(propertyId);
            }
        })
        .catch(error => {
            console.error('Erreur lors de la vérification de l\'accès:', error);
        });
    }

    // Fonction pour charger les informations du propriétaire
    function loadOwnerInfo(propertyId) {
        fetch(`/payments/owner-info?property_id=${propertyId}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Vous n\'avez pas accès à ces informations');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Afficher les informations du propriétaire
                document.getElementById('owner-name').textContent = data.owner.name;
                document.getElementById('owner-email').textContent = data.owner.email;
                document.getElementById('owner-phone').textContent = data.owner.phone;
                
                // Formater la date d'inscription
                if (data.owner.since) {
                    const createdAt = new Date(data.owner.since);
                    const month = createdAt.toLocaleString('fr-FR', { month: 'long' });
                    const year = createdAt.getFullYear();
                    document.getElementById('owner-since').textContent = `${month} ${year}`;
                } else {
                    document.getElementById('owner-since').textContent = 'Date inconnue';
                }
                
                // Afficher la version débloquée
                document.getElementById('owner-info-locked').classList.add('hidden');
                document.getElementById('owner-info-visible').classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement des informations du propriétaire:', error);
        });
    }

    // Fonction pour initialiser les événements
    function initializeEvents(propertyId) {
        // Événement pour le bouton de réservation
        document.getElementById('reserve-button').addEventListener('click', function () {
            if (!isAuthenticated) {
                showError('Vous devez être connecté pour réserver ce bien.');
                return;
            }
            
            const visitDateInput = document.getElementById('visit-date');
            if (!validateVisitDate(visitDateInput.value)) return;
            
            initiateReservationPayment(propertyId);
        });

        // Événement pour le bouton de déblocage des informations du propriétaire
        document.getElementById('unlock-owner-info').addEventListener('click', function() {
            if (!isAuthenticated) {
                showError('Vous devez être connecté pour accéder à ces informations.');
                return;
            }
            
            initiateOwnerInfoPayment(propertyId);
        });

        // Événement pour le bouton de visite virtuelle
        document.getElementById('start-virtual-tour').addEventListener('click', function () {
            toggleModal('virtualTourModal');
            initializeVirtualTour();
        });
    }

    // Fonction pour initialiser un paiement pour la réservation
    function initiateReservationPayment(propertyId) {
        // Afficher le modal de chargement
        toggleModal('paymentProcessingModal');
        
        const visitDate = document.getElementById('visit-date').value;
        const message = document.getElementById('message').value;
        
        // Envoyer la demande de paiement
        fetch('/payments/reservation', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                property_id: propertyId,
                visit_date: visitDate,
                message: message
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            toggleModal('paymentProcessingModal');
            
            if (data.success) {
                if (data.authorization_url) {
                    // Rediriger vers la page de paiement NotchPay
                    window.location.href = data.authorization_url;
                } else if (data.reference) {
                    // Afficher le modal de paiement mobile
                    showMobilePaymentModal(data.reference);
                }
            } else {
                showError(data.message || 'Une erreur est survenue lors de l\'initialisation du paiement');
            }
        })
        .catch(error => {
            console.error('Erreur lors de l\'initialisation du paiement:', error);
            toggleModal('paymentProcessingModal');
            showError('Une erreur est survenue lors de l\'initialisation du paiement. Veuillez réessayer.');
        });
    }

    // Fonction pour initialiser un paiement pour accéder aux informations du propriétaire
    function initiateOwnerInfoPayment(propertyId) {
    // Afficher le modal de chargement
        toggleModal('paymentProcessingModal');
        
        // Envoyer la demande de paiement
        fetch('/payments/initialize', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                property_id: propertyId
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            toggleModal('paymentProcessingModal');
            
            if (data.success) {
                if (data.authorization_url) {
                    // Rediriger vers la page de paiement NotchPay
                    window.location.href = data.authorization_url;
                } else if (data.reference) {
                    // Afficher le modal de paiement mobile
                    showMobilePaymentModal(data.reference);
                }
            } else {
                showError(data.message || 'Une erreur est survenue lors de l\'initialisation du paiement');
            }
        })
        .catch(error => {
            console.error('Erreur lors de l\'initialisation du paiement:', error);
            toggleModal('paymentProcessingModal');
            showError('Une erreur est survenue lors de l\'initialisation du paiement. Veuillez réessayer.');
        });
    }

    // Fonction pour initialiser la visite virtuelle
    function initializeVirtualTour() {
        const virtualTourContent = document.getElementById('virtual-tour-content');
        
        // Exemple de contenu pour la visite virtuelle
        if (property && property.latitude && property.longitude) {
            virtualTourContent.innerHTML = `
                <div id="virtual-tour-map" style="width: 100%; height: 100%;"></div>
            `;
            
            // Initialiser la carte pour la visite virtuelle
            const virtualTourMap = new mapboxgl.Map({
                container: 'virtual-tour-map',
                style: 'mapbox://styles/mapbox/satellite-streets-v11',
                center: [property.longitude, property.latitude],
                zoom: 17,
                pitch: 60,
                bearing: 0
            });
            
            // Ajouter un marqueur
            new mapboxgl.Marker({
                color: '#000000'
            })
                .setLngLat([property.longitude, property.latitude])
                .addTo(virtualTourMap);
        } else {
            virtualTourContent.innerHTML = `
                <div class="text-white text-center">
                    <p>Coordonnées non disponibles pour la visite virtuelle.</p>
                </div>
            `;
        }
    }

    // Fonction pour configurer la validation de date
    function setupDateValidation() {
        const visitDateInput = document.getElementById('visit-date');
        const today = new Date();
        const maxDate = new Date();
        maxDate.setDate(today.getDate() + 3);

        visitDateInput.min = formatDateForInput(today);
        visitDateInput.max = formatDateForInput(maxDate);
        visitDateInput.value = formatDateForInput(today);

        visitDateInput.addEventListener('change', function () {
            validateVisitDate(this.value);
        });
    }

    // Fonction pour valider la date de visite
    function validateVisitDate(dateString) {
        const dateError = document.getElementById('date-error');
        
        if (!dateString) {
            dateError.textContent = "Veuillez sélectionner une date de visite";
            dateError.style.display = 'block';
            return false;
        }
        
        const selectedDate = new Date(dateString);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        const maxDate = new Date();
        maxDate.setDate(today.getDate() + 3);
        maxDate.setHours(23, 59, 59, 999);
        
        if (selectedDate < today) {
            dateError.textContent = "La date de visite ne peut pas être dans le passé";
            dateError.style.display = 'block';
            return false;
        }
        
        if (selectedDate > maxDate) {
            dateError.textContent = "La date de visite doit être dans les 3 prochains jours";
            dateError.style.display = 'block';
            return false;
        }
        
        dateError.textContent = '';
        dateError.style.display = 'none';
        return true;
    }

    // Fonction pour formater une date pour un input date
    function formatDateForInput(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // Fonction pour afficher les détails du bien
    function displayPropertyDetails(property) {
        // Mettre à jour les éléments HTML avec les détails du bien
        document.getElementById('property-title').textContent = property.titre || 'Sans titre';
        document.getElementById('property-address').textContent = property.adresse || 'Adresse non spécifiée';
        document.getElementById('property-type').textContent = property.type || 'Type non spécifié';
        document.getElementById('property-description').textContent = property.description || 'Aucune description disponible';
        document.getElementById('property-area').textContent = `${property.superficie || 0} m²`;
        document.getElementById('property-price').textContent = `${(property.prix || 0).toLocaleString()} FCFA`;
        document.getElementById('property-price-type').textContent = property.transaction_type === 'vente' ? ' à l\'achat' : ' par mois';
    }

    // Fonction pour initialiser le carrousel
    function initializeSwiper(property) {
        // Empêcher les initialisations multiples
        if (isInitialized) return;
        isInitialized = true;
        
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
        
        // Vider le conteneur d'images
        const imagesContainer = document.getElementById('property-images');
        imagesContainer.innerHTML = '';
        
        // S'il n'y a pas d'images, afficher un placeholder CSS
        if (!images || images.length === 0) {
            const slide = document.createElement('div');
            slide.className = 'swiper-slide';
            
            const placeholderDiv = document.createElement('div');
            placeholderDiv.className = 'placeholder-image w-full h-full';
            placeholderDiv.innerHTML = '<span>Aucune image disponible</span>';
            
            slide.appendChild(placeholderDiv);
            imagesContainer.appendChild(slide);
        } else {
            // Ajouter chaque image au carrousel
            images.forEach(image => {
                const slide = document.createElement('div');
                slide.className = 'swiper-slide';
                
                const img = document.createElement('img');
                img.src = image;
                img.alt = property.titre || 'Image du bien';
                img.onerror = function() {
                    // Remplacer l'image défectueuse par un div de placeholder
                    const placeholderDiv = document.createElement('div');
                    placeholderDiv.className = 'placeholder-image w-full h-full';
                    placeholderDiv.innerHTML = '<span>Image non disponible</span>';
                    
                    this.parentNode.replaceChild(placeholderDiv, this);
                };
                
                slide.appendChild(img);
                imagesContainer.appendChild(slide);
            });
        }
        
        // Initialiser Swiper seulement une fois que le contenu est prêt
        swiper = new Swiper('.swiper-container', {
            loop: images.length > 1,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            autoplay: images.length > 1 ? {
                delay: 5000,
                disableOnInteraction: false,
            } : false,
        });
    }

    // Fonction pour initialiser la carte
    function initializeMap(property) {
        mapboxgl.accessToken = 'pk.eyJ1IjoiYXJ0aHVyLWNhZHJlbjIzNyIsImEiOiJjbTM1cjE0cGUwNW41Mmlvam56ZjRtdXQzIn0.fWB_Y31S2A3WeZvXoxjDxQ';
        
        // Vérifier si les coordonnées sont valides
        if (!property.latitude || !property.longitude) {
            console.error('Coordonnées invalides pour la carte');
            return;
        }
        
        // Initialiser la carte
        map = new mapboxgl.Map({
            container: 'property-map',
            style: 'mapbox://styles/mapbox/streets-v11',
            center: [property.longitude, property.latitude],
            zoom: 15
        });
        
        // Ajouter les contrôles de navigation
        map.addControl(new mapboxgl.NavigationControl());
        
        // Ajouter un marqueur
        new mapboxgl.Marker({
            color: '#000000'
        })
            .setLngLat([property.longitude, property.latitude])
            .setPopup(new mapboxgl.Popup({ offset: 25 })
                .setHTML(`<h3 class="font-semibold">${property.titre}</h3><p>${property.adresse}</p>`))
            .addTo(map);
    }

    // Fonction pour afficher/masquer les modals
    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }

    // Fonction pour afficher une erreur
    function showError(message) {
        document.getElementById('error-message').textContent = message;
        toggleModal('errorModal');
    }
</script>
    <script>
        // Ajouter ceci à la fin de la section scripts
document.getElementById('mobilePaymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const phone = document.getElementById('phone').value;
    const channel = document.getElementById('channel').value;
    const reference = document.getElementById('payment-reference').value;
    
    // Afficher le modal de chargement
    toggleModal('mobilePaymentModal');
    toggleModal('paymentProcessingModal');
    
    // Envoyer la demande de paiement mobile
    fetch('/payments/mobile', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            phone: phone,
            channel: channel,
            reference: reference
        })
    })
    .then(response => response.json())
    .then(data => {
        toggleModal('paymentProcessingModal');
        
        if (data.success) {
            // Afficher un message de confirmation
            document.getElementById('error-message').textContent = data.message;
            toggleModal('errorModal');
        } else {
            // Afficher un message d'erreur
            document.getElementById('error-message').textContent = data.message;
            toggleModal('errorModal');
        }
    })
    .catch(error => {
        console.error('Erreur lors du paiement mobile:', error);
        toggleModal('paymentProcessingModal');
        
        document.getElementById('error-message').textContent = 'Une erreur est survenue lors du paiement mobile. Veuillez réessayer.';
        toggleModal('errorModal');
    });
});

// Fonction pour afficher le modal de paiement mobile
function showMobilePaymentModal(reference) {
    document.getElementById('payment-reference').value = reference;
    toggleModal('mobilePaymentModal');
}
    </script>
@endpush

