@extends('layouts.app')

@section('title', 'ImmoChain - Mes réservations')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Mes réservations</h1>
            
            <div class="flex gap-2">
                <button id="refresh-btn" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    <i class="fas fa-sync-alt mr-2"></i>Actualiser
                </button>
            </div>
        </div>
        
        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
            <div class="p-4 border-b">
                <div class="flex flex-col md:flex-row md:items-center gap-4">
                    <div class="relative flex-1">
                        <input type="text" id="search" placeholder="Rechercher une réservation..." class="w-full pl-10 pr-4 py-2 border rounded-lg">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                    <div class="flex gap-2">
                        <select id="status-filter" class="border rounded-lg px-3 py-2">
                            <option value="all">Tous les statuts</option>
                            <option value="pending">En attente</option>
                            <option value="accepted">Acceptée</option>
                            <option value="rejected">Refusée</option>
                            <option value="completed">Terminée</option>
                        </select>
                        <select id="date-filter" class="border rounded-lg px-3 py-2">
                            <option value="all">Toutes les dates</option>
                            <option value="today">Aujourd'hui</option>
                            <option value="week">Cette semaine</option>
                            <option value="month">Ce mois</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="text-left py-3 px-4">Bien</th>
                            <th class="text-left py-3 px-4">Client</th>
                            <th class="text-left py-3 px-4">Date de visite</th>
                            <th class="text-left py-3 px-4">Date de réservation</th>
                            <th class="text-left py-3 px-4">Statut</th>
                            <th class="text-left py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="reservations-list">
                        <!-- Les réservations seront chargées dynamiquement ici -->
                        <tr>
                            <td colspan="6" class="py-4 px-4 text-center text-gray-500">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Chargement des réservations...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div id="pagination" class="p-4 border-t flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    <span id="total-count">0</span> réservation(s) trouvée(s)
                </div>
                <div class="flex gap-2">
                    <button id="prev-page" class="px-3 py-1 border rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span id="page-info" class="px-3 py-1">Page 1</span>
                    <button id="next-page" class="px-3 py-1 border rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de détails de réservation -->
<div id="reservationDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex justify-center items-center">
    <div class="bg-white rounded-lg max-w-2xl w-full p-6">
        <div class="flex justify-between items-start mb-4">
            <h2 class="text-xl font-bold">Détails de la réservation</h2>
            <button onclick="toggleModal('reservationDetailsModal')" class="text-gray-500 hover:text-black">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div id="reservation-details-content">
            <!-- Le contenu sera chargé dynamiquement ici -->
        </div>
        
        <div class="flex justify-end mt-6 gap-2" id="reservation-actions">
            <!-- Les boutons d'action seront ajoutés dynamiquement ici -->
        </div>
    </div>
</div>
<!-- Ajouter ce code dans la section qui affiche les détails d'une réservation -->
@foreach ($reservations as $reservation)
    <!-- Autres détails de la réservation ici (titre, client, date, etc.) -->

    @if($reservation->blockchain_registered)
        <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-shield-alt text-green-500 text-2xl mr-3"></i>
                <div>
                    <p class="font-medium text-green-800">Cette réservation est certifiée sur la blockchain</p>
                    <p class="text-sm text-green-700 mt-1">
                        Les détails de cette réservation sont enregistrés de manière sécurisée et immuable sur la blockchain Polygon.
                    </p>
                    @if($reservation->blockchain_tx)
                        <a href="{{ $reservation->getBlockchainExplorerUrl() }}" target="_blank" class="inline-flex items-center text-sm text-green-600 hover:text-green-800 mt-2">
                            <i class="fas fa-external-link-alt mr-1"></i> Voir la transaction sur l'explorateur blockchain
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif
@endforeach



<!-- Modal de confirmation -->
<div id="confirmationModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex justify-center items-center">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h2 class="text-xl font-bold mb-4" id="confirmation-title">Confirmation</h2>
        <p id="confirmation-message" class="mb-6">Êtes-vous sûr de vouloir effectuer cette action ?</p>
        <div class="flex justify-end space-x-4">
            <button onclick="toggleModal('confirmationModal')" class="px-4 py-2 bg-gray-200 rounded-lg">Annuler</button>
            <button id="confirm-action-btn" class="px-4 py-2 bg-black text-white rounded-lg">Confirmer</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let reservations = [];
    let currentPage = 1;
    let totalPages = 1;
    let currentReservationId = null;
    let currentAction = null;
    
    document.addEventListener('DOMContentLoaded', function() {
        // Charger les réservations
        loadReservations();
        
        // Événements pour les filtres
        document.getElementById('search').addEventListener('input', filterReservations);
        document.getElementById('status-filter').addEventListener('change', filterReservations);
        document.getElementById('date-filter').addEventListener('change', filterReservations);
        
        // Événement pour le bouton d'actualisation
        document.getElementById('refresh-btn').addEventListener('click', loadReservations);
        
        // Événements pour la pagination
        document.getElementById('prev-page').addEventListener('click', goToPrevPage);
        document.getElementById('next-page').addEventListener('click', goToNextPage);
        
        // Événement pour le bouton de confirmation
        document.getElementById('confirm-action-btn').addEventListener('click', confirmAction);
    });
    
    // Fonction pour charger les réservations
    function loadReservations() {
        const reservationsList = document.getElementById('reservations-list');
        reservationsList.innerHTML = `
            <tr>
                <td colspan="6" class="py-4 px-4 text-center text-gray-500">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Chargement des réservations...
                </td>
            </tr>
        `;
        
        fetch('/api/dashboard/reservations')
            .then(response => response.json())
            .then(data => {
                reservations = data;
                displayReservations(reservations);
                updatePagination();
            })
            .catch(error => {
                console.error('Erreur lors du chargement des réservations:', error);
                reservationsList.innerHTML = `
                    <tr>
                        <td colspan="6" class="py-4 px-4 text-center text-red-500">
                            Une erreur est survenue lors du chargement des réservations. Veuillez réessayer.
                        </td>
                    </tr>
                `;
            });
    }
    
    // Fonction pour afficher les réservations
    function displayReservations(reservations) {
        const reservationsList = document.getElementById('reservations-list');
        
        if (reservations.length === 0) {
            reservationsList.innerHTML = `
                <tr>
                    <td colspan="6" class="py-4 px-4 text-center text-gray-500">Aucune réservation trouvée</td>
                </tr>
            `;
            document.getElementById('total-count').textContent = '0';
            return;
        }
        
        document.getElementById('total-count').textContent = reservations.length;
        
        // Calculer les réservations pour la page actuelle
        const startIndex = (currentPage - 1) * 10;
        const endIndex = Math.min(startIndex + 10, reservations.length);
        const currentReservations = reservations.slice(startIndex, endIndex);
        
        reservationsList.innerHTML = '';
        
        currentReservations.forEach(reservation => {
            const row = document.createElement('tr');
            row.className = 'border-b hover:bg-gray-50';
            
            // Traiter les images
            let propertyImage = '/placeholder.svg?height=300&width=300';
            if (reservation.bien.images) {
                try {
                    const images = typeof reservation.bien.images === 'string' 
                        ? JSON.parse(reservation.bien.images) 
                        : reservation.bien.images;
                    
                    if (images && images.length > 0) {
                        propertyImage = images[0];
                    }
                } catch (e) {
                    console.error('Erreur lors du parsing des images:', e);
                }
            }
            
            // Formater les dates
            const visitDate = new Date(reservation.date_visite).toLocaleDateString('fr-FR');
            const reservationDate = new Date(reservation.date_reservation).toLocaleDateString('fr-FR');
            
            row.innerHTML = `
                <td class="py-3 px-4">
                    <div class="flex items-center">
                        <img src="${propertyImage}" alt="${reservation.bien.titre}" class="w-10 h-10 rounded object-cover mr-3">
                        <span>${reservation.bien.titre}</span>
                    </div>
                </td>
                <td class="py-3 px-4">${reservation.client.nom}</td>
                <td class="py-3 px-4">${visitDate}</td>
                <td class="py-3 px-4">${reservationDate}</td>
                <td class="py-3 px-4">
                    <span class="px-2 py-1 rounded-full text-xs ${getStatusClass(reservation.statut)}">
                        ${getStatusText(reservation.statut)}
                    </span>
                </td>
                <td class="py-3 px-4">
                    <div class="flex space-x-2">
                        <button class="text-blue-500 hover:text-blue-700" onclick="viewReservationDetails(${reservation.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${reservation.statut === 'pending' ? `
                            <button class="text-green-500 hover:text-green-700" onclick="acceptReservation(${reservation.id})">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="text-red-500 hover:text-red-700" onclick="rejectReservation(${reservation.id})">
                                <i class="fas fa-times"></i>
                            </button>
                        ` : ''}
                    </div>
                </td>
            `;
            
            reservationsList.appendChild(row);
        });
    }
    
    // Fonction pour filtrer les réservations
    function filterReservations() {
        const searchTerm = document.getElementById('search').value.toLowerCase();
        const statusFilter = document.getElementById('status-filter').value;
        const dateFilter = document.getElementById('date-filter').value;
        
        const filteredReservations = reservations.filter(reservation => {
            // Filtre par recherche
            const matchesSearch = 
                (reservation.bien.titre && reservation.bien.titre.toLowerCase().includes(searchTerm)) ||
                (reservation.client.nom && reservation.client.nom.toLowerCase().includes(searchTerm)) ||
                (reservation.message && reservation.message.toLowerCase().includes(searchTerm));
            
            // Filtre par statut
            const matchesStatus = statusFilter === 'all' || reservation.statut === statusFilter;
            
            // Filtre par date
            let matchesDate = true;
            if (dateFilter !== 'all') {
                const visitDate = new Date(reservation.date_visite);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                if (dateFilter === 'today') {
                    const tomorrow = new Date(today);
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    matchesDate = visitDate >= today && visitDate < tomorrow;
                } else if (dateFilter === 'week') {
                    const startOfWeek = new Date(today);
                    startOfWeek.setDate(today.getDate() - today.getDay());
                    const endOfWeek = new Date(startOfWeek);
                    endOfWeek.setDate(startOfWeek.getDate() + 7);
                    matchesDate = visitDate >= startOfWeek && visitDate < endOfWeek;
                } else if (dateFilter === 'month') {
                    const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                    const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    matchesDate = visitDate >= startOfMonth && visitDate <= endOfMonth;
                }
            }
            
            return matchesSearch && matchesStatus && matchesDate;
        });
        
        // Réinitialiser la pagination
        currentPage = 1;
        
        // Afficher les réservations filtrées
        displayReservations(filteredReservations);
        updatePagination(filteredReservations.length);
    }
    
    // Fonction pour obtenir la classe CSS en fonction du statut
    function getStatusClass(status) {
        switch (status) {
            case 'pending':
                return 'bg-yellow-100 text-yellow-800';
            case 'accepted':
                return 'bg-green-100 text-green-800';
            case 'rejected':
                return 'bg-red-100 text-red-800';
            case 'completed':
                return 'bg-blue-100 text-blue-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }
    
    // Fonction pour obtenir le texte en fonction du statut
    function getStatusText(status) {
        switch (status) {
            case 'pending':
                return 'En attente';
            case 'accepted':
                return 'Acceptée';
            case 'rejected':
                return 'Refusée';
            case 'completed':
                return 'Terminée';
            default:
                return 'Inconnu';
        }
    }
    
    // Fonction pour mettre à jour la pagination
    function updatePagination(count = null) {
        const totalCount = count !== null ? count : reservations.length;
        totalPages = Math.ceil(totalCount / 10);
        
        document.getElementById('page-info').textContent = `Page ${currentPage} sur ${totalPages || 1}`;
        document.getElementById('prev-page').disabled = currentPage <= 1;
        document.getElementById('next-page').disabled = currentPage >= totalPages;
    }
    
    // Fonction pour aller à la page précédente
    function goToPrevPage() {
        if (currentPage > 1) {
            currentPage--;
            displayReservations(reservations);
            updatePagination();
        }
    }
    
    // Fonction pour aller à la page suivante
    function goToNextPage() {
        if (currentPage < totalPages) {
            currentPage++;
            displayReservations(reservations);
            updatePagination();
        }
    }
    
    // Fonction pour voir les détails d'une réservation
    function viewReservationDetails(id) {
        const reservation = reservations.find(r => r.id === id);
        if (!reservation) return;
        
        const detailsContent = document.getElementById('reservation-details-content');
        const actionsContainer = document.getElementById('reservation-actions');
        
        // Traiter les images
        let propertyImage = '/placeholder.svg?height=300&width=300';
        if (reservation.bien.images) {
            try {
                const images = typeof reservation.bien.images === 'string' 
                    ? JSON.parse(reservation.bien.images) 
                    : reservation.bien.images;
                
                if (images && images.length > 0) {
                    propertyImage = images[0];
                }
            } catch (e) {
                console.error('Erreur lors du parsing des images:', e);
            }
        }
        
        // Formater les dates
        const visitDate = new Date(reservation.date_visite).toLocaleDateString('fr-FR', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        const reservationDate = new Date(reservation.date_reservation).toLocaleDateString('fr-FR', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        detailsContent.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold mb-2">Informations sur le bien</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center mb-3">
                            <img src="${propertyImage}" alt="${reservation.bien.titre}" class="w-16 h-16 rounded object-cover mr-3">
                            <div>
                                <p class="font-medium">${reservation.bien.titre}</p>
                                <p class="text-sm text-gray-500">${reservation.bien.adresse}</p>
                            </div>
                        </div>
                        <p><span class="font-medium">Type:</span> ${reservation.bien.type}</p>
                        <p><span class="font-medium">Prix:</span> ${reservation.bien.prix.toLocaleString()} FCFA</p>
                        <p><span class="font-medium">Superficie:</span> ${reservation.bien.superficie} m²</p>
                        <p class="mt-2">
                            <a href="/properties/${reservation.bien.id}" class="text-blue-600 hover:underline" target="_blank">
                                Voir la page du bien <i class="fas fa-external-link-alt text-xs"></i>
                            </a>
                        </p>
                    </div>
                </div>
                
                <div>
                    <h3 class="font-semibold mb-2">Informations sur le client</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p><span class="font-medium">Nom:</span> ${reservation.client.nom}</p>
                        <p><span class="font-medium">Email:</span> ${reservation.client.email}</p>
                        <p><span class="font-medium">Téléphone:</span> ${reservation.client.telephone}</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-6">
                <h3 class="font-semibold mb-2">Détails de la réservation</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p><span class="font-medium">Statut:</span> <span class="px-2 py-1 rounded-full text-xs ${getStatusClass(reservation.statut)}">${getStatusText(reservation.statut)}</span></p>
                    <p><span class="font-medium">Date de visite:</span> ${visitDate}</p>
                    <p><span class="font-medium">Date de réservation:</span> ${reservationDate}</p>
                    ${reservation.message ? `
                        <div class="mt-3">
                            <p class="font-medium">Message du client:</p>
                            <p class="bg-white p-3 rounded border mt-1">${reservation.message}</p>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
        
        // Ajouter les boutons d'action en fonction du statut
        actionsContainer.innerHTML = '';
        
        // Bouton de fermeture
        const closeButton = document.createElement('button');
        closeButton.className = 'px-4 py-2 bg-gray-200 rounded-lg';
        closeButton.textContent = 'Fermer';
        closeButton.onclick = function() {
            toggleModal('reservationDetailsModal');
        };
        actionsContainer.appendChild(closeButton);
        
        // Boutons d'action selon le statut
        if (reservation.statut === 'pending') {
            const acceptButton = document.createElement('button');
            acceptButton.className = 'px-4 py-2 bg-green-600 text-white rounded-lg';
            acceptButton.innerHTML = '<i class="fas fa-check mr-2"></i>Accepter';
            acceptButton.onclick = function() {
                toggleModal('reservationDetailsModal');
                acceptReservation(reservation.id);
            };
            actionsContainer.appendChild(acceptButton);
            
            const rejectButton = document.createElement('button');
            rejectButton.className = 'px-4 py-2 bg-red-600 text-white rounded-lg';
            rejectButton.innerHTML = '<i class="fas fa-times mr-2"></i>Refuser';
            rejectButton.onclick = function() {
                toggleModal('reservationDetailsModal');
                rejectReservation(reservation.id);
            };
            actionsContainer.appendChild(rejectButton);
        }
        
        toggleModal('reservationDetailsModal');
    }
    
    // Fonction pour accepter une réservation
    function acceptReservation(id) {
        currentReservationId = id;
        currentAction = 'accept';
        
        document.getElementById('confirmation-title').textContent = 'Accepter la réservation';
        document.getElementById('confirmation-message').textContent = 'Êtes-vous sûr de vouloir accepter cette réservation ? Le client sera notifié par email.';
        document.getElementById('confirm-action-btn').className = 'px-4 py-2 bg-green-600 text-white rounded-lg';
        
        toggleModal('confirmationModal');
    }
    
    // Fonction pour refuser une réservation
    function rejectReservation(id) {
        currentReservationId = id;
        currentAction = 'reject';
        
        document.getElementById('confirmation-title').textContent = 'Refuser la réservation';
        document.getElementById('confirmation-message').textContent = 'Êtes-vous sûr de vouloir refuser cette réservation ? Le client sera notifié par email.';
        document.getElementById('confirm-action-btn').className = 'px-4 py-2 bg-red-600 text-white rounded-lg';
        
        toggleModal('confirmationModal');
    }
    
    // Fonction pour confirmer l'action
    function confirmAction() {
        if (!currentReservationId || !currentAction) return;
        
        const endpoint = `/api/reservations/${currentReservationId}/${currentAction}`;
        
        // Désactiver le bouton de confirmation
        const confirmButton = document.getElementById('confirm-action-btn');
        const originalText = confirmButton.textContent;
        confirmButton.disabled = true;
        confirmButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Traitement...';
        
        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Fermer le modal
                toggleModal('confirmationModal');
                
                // Recharger les réservations
                loadReservations();
                
                // Afficher un message de succès
                alert(currentAction === 'accept' ? 'Réservation acceptée avec succès.' : 'Réservation refusée avec succès.');
            } else {
                alert(data.message || 'Une erreur est survenue');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue');
        })
        .finally(() => {
            // Réinitialiser le bouton
            confirmButton.disabled = false;
            confirmButton.textContent = originalText;
            
            // Réinitialiser les variables
            currentReservationId = null;
            currentAction = null;
        });
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
</script>
@endpush

