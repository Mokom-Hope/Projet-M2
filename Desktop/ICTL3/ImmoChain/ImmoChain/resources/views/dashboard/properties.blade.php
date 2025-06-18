@extends('layouts.app')

@section('title', 'ImmoChain - Mes biens')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Mes biens immobiliers</h1>
            <a href="{{ route('properties.create') }}" class="px-6 py-2 bg-black text-white rounded-lg font-medium hover:bg-gray-800 transition">
                Ajouter un bien
            </a>
        </div>
        
        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
            <div class="p-4 border-b">
                <div class="flex flex-col md:flex-row md:items-center gap-4">
                    <div class="relative flex-1">
                        <input type="text" id="search" placeholder="Rechercher un bien..." class="w-full pl-10 pr-4 py-2 border rounded-lg">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                    <div class="flex gap-2">
                        <select id="status-filter" class="border rounded-lg px-3 py-2">
                            <option value="all">Tous les statuts</option>
                            <option value="Disponible">Disponible</option>
                            <option value="Réservé">Réservé</option>
                        </select>
                        <select id="type-filter" class="border rounded-lg px-3 py-2">
                            <option value="all">Tous les types</option>
                            <option value="Maison">Maison</option>
                            <option value="Terrain">Terrain</option>
                            <option value="LocalCommercial">Local commercial</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="text-left py-3 px-4">Bien</th>
                            <th class="text-left py-3 px-4">Type</th>
                            <th class="text-left py-3 px-4">Prix</th>
                            <th class="text-left py-3 px-4">Statut</th>
                            <th class="text-left py-3 px-4">Date d'ajout</th>
                            <th class="text-left py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="properties-list">
                        <!-- Les biens seront chargés dynamiquement ici -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div id="deleteConfirmModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex justify-center items-center">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h2 class="text-xl font-bold mb-4">Confirmer la suppression</h2>
        <p class="mb-6">Êtes-vous sûr de vouloir supprimer ce bien ? Cette action est irréversible.</p>
        <div class="flex justify-end space-x-4">
            <button onclick="toggleModal('deleteConfirmModal')" class="px-4 py-2 bg-gray-200 rounded-lg">Annuler</button>
            <button id="confirm-delete-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg">Supprimer</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let properties = [];
    let propertyToDelete = null;
    
    document.addEventListener('DOMContentLoaded', function() {
        // Charger les biens
        loadProperties();
        
        // Événements pour les filtres
        document.getElementById('search').addEventListener('input', filterProperties);
        document.getElementById('status-filter').addEventListener('change', filterProperties);
        document.getElementById('type-filter').addEventListener('change', filterProperties);
        
        // Événement pour le bouton de confirmation de suppression
        document.getElementById('confirm-delete-btn').addEventListener('click', confirmDeleteProperty);
    });
    
    // Fonction pour charger les biens
    function loadProperties() {
        fetch('/api/dashboard/properties')
            .then(response => response.json())
            .then(data => {
                properties = data;
                displayProperties(properties);
            })
            .catch(error => console.error('Erreur lors du chargement des biens:', error));
    }
    
    // Fonction pour afficher les biens
    function displayProperties(properties) {
        const propertiesList = document.getElementById('properties-list');
        
        if (properties.length === 0) {
            propertiesList.innerHTML = `
                <tr>
                    <td colspan="6" class="py-4 px-4 text-center text-gray-500">Aucun bien immobilier trouvé</td>
                </tr>
            `;
            return;
        }
        
        propertiesList.innerHTML = '';
        
        properties.forEach(property => {
            const row = document.createElement('tr');
            row.className = 'border-b hover:bg-gray-50';
            
            row.innerHTML = `
                <td class="py-3 px-4">
                    <div class="flex items-center">
                        <img src="${property.images[0]}" alt="${property.titre}" class="w-10 h-10 rounded object-cover mr-3">
                        <span>${property.titre}</span>
                    </div>
                </td>
                <td class="py-3 px-4">${property.type}</td>
                <td class="py-3 px-4">${property.prix.toLocaleString()} FCFA</td>
                <td class="py-3 px-4">
                    <span class="px-2 py-1 rounded-full text-xs ${getStatusClass(property.statut)}">
                        ${property.statut}
                    </span>
                </td>
                <td class="py-3 px-4">${new Date(property.created_at).toLocaleDateString('fr-FR')}</td>
                <td class="py-3 px-4">
                    <div class="flex space-x-2">
                        <a href="/properties/${property.id}" class="text-blue-500 hover:text-blue-700" title="Voir">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="/properties/${property.id}/edit" class="text-green-500 hover:text-green-700" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="text-red-500 hover:text-red-700" title="Supprimer" onclick="deleteProperty(${property.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                        ${property.statut === 'Disponible' ? `
                            <button class="text-yellow-500 hover:text-yellow-700" title="Marquer comme réservé" onclick="togglePropertyStatus(${property.id}, 'Réservé')">
                                <i class="fas fa-lock"></i>
                            </button>
                        ` : `
                            <button class="text-green-500 hover:text-green-700" title="Marquer comme disponible" onclick="togglePropertyStatus(${property.id}, 'Disponible')">
                                <i class="fas fa-lock-open"></i>
                            </button>
                        `}
                    </div>
                </td>
            `;
            
            propertiesList.appendChild(row);
        });
    }
    
    // Fonction pour filtrer les biens
    function filterProperties() {
        const searchTerm = document.getElementById('search').value.toLowerCase();
        const statusFilter = document.getElementById('status-filter').value;
        const typeFilter = document.getElementById('type-filter').value;
        
        const filteredProperties = properties.filter(property => {
            // Filtre par recherche
            const matchesSearch = property.titre.toLowerCase().includes(searchTerm) || 
                                 property.description.toLowerCase().includes(searchTerm) ||
                                 property.adresse.toLowerCase().includes(searchTerm);
            
            // Filtre par statut
            const matchesStatus = statusFilter === 'all' || property.statut === statusFilter;
            
            // Filtre par type
            const matchesType = typeFilter === 'all' || property.type === typeFilter;
            
            return matchesSearch && matchesStatus && matchesType;
        });
        
        displayProperties(filteredProperties);
    }
    
    // Fonction pour obtenir la classe CSS en fonction du statut
    function getStatusClass(status) {
        switch (status) {
            case 'Disponible':
                return 'bg-green-100 text-green-800';
            case 'Réservé':
                return 'bg-yellow-100 text-yellow-800';
            case 'Supprimé':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }
    
    // Fonction pour supprimer un bien
    function deleteProperty(id) {
        propertyToDelete = id;
        toggleModal('deleteConfirmModal');
    }
    
    // Fonction pour confirmer la suppression d'un bien
    function confirmDeleteProperty() {
        if (!propertyToDelete) return;
        
        fetch(`/api/properties/${propertyToDelete}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Recharger les biens
                loadProperties();
                toggleModal('deleteConfirmModal');
            } else {
                alert(data.message || 'Une erreur est survenue lors de la suppression');
            }
        })
        .catch(error => {
            console.error('Erreur lors de la suppression:', error);
            alert('Une erreur est survenue lors de la suppression');
        });
    }
    
    // Fonction pour changer le statut d'un bien
    function togglePropertyStatus(id, newStatus) {
        fetch(`/api/properties/${id}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Recharger les biens
                loadProperties();
            } else {
                alert(data.message || 'Une erreur est survenue lors du changement de statut');
            }
        })
        .catch(error => {
            console.error('Erreur lors du changement de statut:', error);
            alert('Une erreur est survenue lors du changement de statut');
        });
    }
</script>
@endpush

