@extends('layouts.app')

@section('title', 'ImmoChain - Tableau de bord')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Tableau de bord</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl border shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold">Mes biens</h2>
                    <span class="text-2xl font-bold" id="properties-count">0</span>
                </div>
                <a href="/dashboard/properties" class="text-black font-medium hover:underline">Gérer mes biens</a>
            </div>
            
            <div class="bg-white p-6 rounded-xl border shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold">Réservations</h2>
                    <span class="text-2xl font-bold" id="reservations-count">0</span>
                </div>
                <a href="/dashboard/reservations" class="text-black font-medium hover:underline">Voir les réservations</a>
            </div>
            
            <div class="bg-white p-6 rounded-xl border shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold">Messages</h2>
                    <span class="text-2xl font-bold" id="messages-count">0</span>
                </div>
                <a href="/dashboard/messages" class="text-black font-medium hover:underline">Voir les messages</a>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-xl border shadow-sm mb-8">
            <h2 class="text-xl font-semibold mb-4">Actions rapides</h2>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('properties.create') }}" class="px-6 py-3 bg-black text-white rounded-lg font-medium hover:bg-gray-800 transition">
                    Ajouter un bien
                </a>
                <a href="/dashboard/profile" class="px-6 py-3 bg-gray-100 text-black rounded-lg font-medium hover:bg-gray-200 transition">
                    Modifier mon profil
                </a>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-xl border shadow-sm">
            <h2 class="text-xl font-semibold mb-4">Dernières réservations</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-3 px-4">Bien</th>
                            <th class="text-left py-3 px-4">Client</th>
                            <th class="text-left py-3 px-4">Date</th>
                            <th class="text-left py-3 px-4">Statut</th>
                            <th class="text-left py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="recent-reservations">
                        <!-- Les réservations seront chargées dynamiquement ici -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Charger les statistiques
        fetch('/api/dashboard/stats')
            .then(response => response.json())
            .then(data => {
                document.getElementById('properties-count').textContent = data.properties_count;
                document.getElementById('reservations-count').textContent = data.reservations_count;
                document.getElementById('messages-count').textContent = data.messages_count;
            })
            .catch(error => console.error('Erreur lors du chargement des statistiques:', error));
        
        // Charger les dernières réservations
        fetch('/api/dashboard/reservations/recent')
            .then(response => response.json())
            .then(data => {
                const reservationsTable = document.getElementById('recent-reservations');
                
                if (data.length === 0) {
                    reservationsTable.innerHTML = `
                        <tr>
                            <td colspan="5" class="py-4 px-4 text-center text-gray-500">Aucune réservation récente</td>
                        </tr>
                    `;
                    return;
                }
                
                reservationsTable.innerHTML = '';
                
                data.forEach(reservation => {
                    const row = document.createElement('tr');
                    row.className = 'border-b hover:bg-gray-50';
                    
                    row.innerHTML = `
                        <td class="py-3 px-4">
                            <div class="flex items-center">
                                <img src="${reservation.property.images[0]}" alt="${reservation.property.titre}" class="w-10 h-10 rounded object-cover mr-3">
                                <span>${reservation.property.titre}</span>
                            </div>
                        </td>
                        <td class="py-3 px-4">${reservation.client.nom}</td>
                        <td class="py-3 px-4">${new Date(reservation.date_reservation).toLocaleDateString('fr-FR')}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 rounded-full text-xs ${getStatusClass(reservation.status)}">
                                ${getStatusText(reservation.status)}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex space-x-2">
                                <button class="text-blue-500 hover:text-blue-700" onclick="viewReservation(${reservation.id})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                ${reservation.status === 'pending' ? `
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
                    
                    reservationsTable.appendChild(row);
                });
            })
            .catch(error => console.error('Erreur lors du chargement des réservations:', error));
    });
    
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
    
    // Fonction pour voir les détails d'une réservation
    function viewReservation(id) {
        window.location.href = `/dashboard/reservations/${id}`;
    }
    
    // Fonction pour accepter une réservation
    function acceptReservation(id) {
        if (confirm('Êtes-vous sûr de vouloir accepter cette réservation ?')) {
            fetch(`/api/reservations/${id}/accept`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Une erreur est survenue');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
            });
        }
    }
    
    // Fonction pour refuser une réservation
    function rejectReservation(id) {
        if (confirm('Êtes-vous sûr de vouloir refuser cette réservation ?')) {
            fetch(`/api/reservations/${id}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Une erreur est survenue');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
            });
        }
    }
</script>
@endpush

