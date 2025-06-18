@extends('layouts.app')

@section('title', 'ImmoChain - Trouvez votre bien immobilier idéal')

@push('styles')
<style>
    .property-card {
        transition: transform 0.2s ease-in-out;
    }
    .property-card:hover {
        transform: scale(1.02);
    }
    .filter-scroll {
        scrollbar-width: none;
    }
    .filter-scroll::-webkit-scrollbar {
        display: none;
    }
    .property-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Remplaçons le code CSS problématique des badges par cette version */
.transaction-badge {
    padding: 0.2rem 0.6rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 400;
    text-transform: none;
    font-family: 'Inter', 'Segoe UI', sans-serif;
    backdrop-filter: blur(3px);
    transition: all 0.3s ease;
    color: #000; /* texte en noir */
    background-color: rgba(255, 255, 255, 0.7); /* fond blanc semi-transparent par défaut */
}

/* Badge pour "vente" - vert transparent */
.transaction-badge[data-type="vente"] {
    background-color: rgba(34, 197, 94, 0.7); /* vert transparent */
    border: 1px solid rgba(34, 197, 94, 0.8);
}

/* Badge pour "location" - bleu transparent */
.transaction-badge[data-type="location"] {
    background-color: rgba(59, 130, 246, 0.7); /* bleu transparent */
    border: 1px solid rgba(59, 130, 246, 0.8);
}
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Filtres -->
    <div class="mb-8">
        
        <br>
        <div class="flex gap-2 overflow-x-auto filter-scroll pb-4">
            <button class="filter-btn px-4 py-2 border rounded-full hover:border-black whitespace-nowrap bg-black text-white" data-filter="all">Tous les biens</button>
            <button class="filter-btn px-4 py-2 border rounded-full hover:border-black whitespace-nowrap" data-filter="Maison">Maisons</button>
            <button class="filter-btn px-4 py-2 border rounded-full hover:border-black whitespace-nowrap" data-filter="Meublé">Meublé</button>
            <button class="filter-btn px-4 py-2 border rounded-full hover:border-black whitespace-nowrap" data-filter="Hotel">Hotel</button>
            <button class="filter-btn px-4 py-2 border rounded-full hover:border-black whitespace-nowrap" data-filter="Terrain">Terrains</button>
            <button class="filter-btn px-4 py-2 border rounded-full hover:border-black whitespace-nowrap" data-filter="LocalCommercial">Locaux commerciaux</button>
            <button class="filter-btn px-4 py-2 border rounded-full hover:border-black whitespace-nowrap" data-filter="Studio">Studios</button>
            <button class="filter-btn px-4 py-2 border rounded-full hover:border-black whitespace-nowrap" data-filter="Chambre">Chambres</button>
            <button class="filter-btn px-4 py-2 border rounded-full hover:border-black whitespace-nowrap" data-filter="vente">À vendre</button>
            <button class="filter-btn px-4 py-2 border rounded-full hover:border-black whitespace-nowrap" data-filter="location">À louer</button>
        </div>
    </div>

    <!-- Recherche -->
    <div class="mb-8">
        <div class="relative">
            <input type="text" id="search-input" placeholder="Rechercher par titre, adresse, description..." class="w-full pl-10 pr-4 py-3 border rounded-lg">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>
    </div>

    <!-- Grille des propriétés -->
    <div class="relative">
        <div id="loading-indicator" class="absolute inset-0 flex flex-col items-center justify-center bg-white bg-opacity-75 z-10 hidden">
            <i class="fas fa-spinner fa-spin text-3xl mb-2 text-gray-600"></i>
            <p class="text-gray-500">Chargement des biens immobiliers...</p>
        </div>

        <div id="properties-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"></div>

        <div id="no-results" class="hidden text-center py-12">
            <i class="fas fa-search text-5xl text-gray-300 mb-4"></i>
            <p class="text-xl text-gray-500">Aucun bien immobilier ne correspond à votre recherche</p>
            <button id="reset-filters" class="mt-4 px-6 py-2 bg-black text-white rounded-lg">
                Réinitialiser les filtres
            </button>
        </div>
    </div>

    <!-- Bouton Carte -->
    <button onclick="window.location.href='/map'" class="fixed bottom-20 left-1/2 transform -translate-x-1/2 bg-black text-white px-6 py-3 rounded-full shadow-lg hover:scale-105 transition md:bottom-6 z-20">
        <span class="flex items-center gap-2">
            <i class="fas fa-map-marker-alt"></i>
            Afficher la carte
        </span>
    </button>
</div>

<!-- Template -->
<template id="property-template">
    <a href="#" class="property-card group block">
        <div class="relative aspect-square rounded-xl overflow-hidden mb-3">
            <img src="/placeholder.svg" alt="" class="property-image">
            <button class="absolute top-3 right-3 z-10 text-white hover:scale-110 transition favorite-btn">
                <i class="far fa-heart text-2xl drop-shadow-lg"></i>
            </button>
            <div class="absolute top-3 left-3 z-10 px-2 py-1 rounded-full text-xs font-semibold transaction-badge"></div>
        </div>
        <div>
            <h3 class="font-semibold property-title"></h3>
            <p class="text-gray-500 property-address"></p>
            <p class="text-gray-500 property-type"></p>
            <p class="mt-1">
                <span class="font-semibold property-price"></span>
                <span class="property-price-type"></span>
            </p>
        </div>
    </a>
</template>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const grid = document.getElementById('properties-grid');
        const loading = document.getElementById('loading-indicator');
        const noResults = document.getElementById('no-results');

        const showLoading = () => loading.classList.remove('hidden');
        const hideLoading = () => loading.classList.add('hidden');

        const fetchProperties = () => {
            showLoading();
            noResults.classList.add('hidden');
            fetch('/api/properties')
                .then(res => res.json())
                .then(data => {
                    hideLoading();
                    updateGrid(data);
                })
                .catch(() => {
                    hideLoading();
                    grid.innerHTML = `<div class="col-span-full text-center py-12 text-gray-500">
                        <i class="fas fa-exclamation-circle text-3xl mb-4"></i>
                        <p>Erreur lors du chargement.</p>
                        <button onclick="fetchProperties()" class="mt-4 px-6 py-2 bg-black text-white rounded-lg">Réessayer</button>
                    </div>`;
                });
        };

        const updateGrid = (properties) => {
            grid.innerHTML = '';
            if (!properties.length) {
                noResults.classList.remove('hidden');
                return;
            }

            const template = document.getElementById('property-template');
            const fragment = document.createDocumentFragment();

            properties.forEach(p => {
                const clone = template.content.cloneNode(true);
                clone.querySelector('.property-card').href = `/properties/${p.id}`;
                //clone.querySelector('.property-image').src = p.image || '/placeholder.svg';
                if (p.images && p.images.length > 0) {
                        clone.querySelector('.property-image').src = p.images[0];
                    } else {
                        clone.querySelector('.property-image').src = '/placeholder.svg';
                    }
                clone.querySelector('.transaction-badge').textContent = p.transaction_type;
                clone.querySelector('.transaction-badge').setAttribute('data-type', p.transaction_type);
                clone.querySelector('.property-title').textContent = p.titre;
                clone.querySelector('.property-address').textContent = p.adresse;
                clone.querySelector('.property-type').textContent = p.type;
                clone.querySelector('.property-price').textContent = p.prix + ' FCFA';
                clone.querySelector('.property-price-type').textContent = p.transaction_type === 'location' ? '/mois' : '';
                fragment.appendChild(clone);
            });

            grid.appendChild(fragment);
        };

        const filterProperties = (type, value) => {
            showLoading();
            fetch('/api/properties')
                .then(res => res.json())
                .then(data => {
                    let filtered = data;
                    if (type === 'type') filtered = data.filter(p => p.type === value);
                    if (type === 'transaction') filtered = data.filter(p => p.transaction_type === value);
                    if (type === 'search') filtered = data.filter(p =>
                        (p.titre || '').toLowerCase().includes(value) ||
                        (p.adresse || '').toLowerCase().includes(value) ||
                        (p.description || '').toLowerCase().includes(value)
                    );
                    hideLoading();
                    updateGrid(filtered);
                });
        };

        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.filter-btn').forEach(b => {
                    b.classList.remove('bg-black', 'text-white');
                    b.classList.add('hover:border-black');
                });
                this.classList.add('bg-black', 'text-white');
                this.classList.remove('hover:border-black');

                const filter = this.dataset.filter;
                if (['Maison', 'Meublé', 'Hotel', 'Terrain', 'LocalCommercial', 'Studio', 'Chambre'].includes(filter)) {
                    filterProperties('type', filter);
                } else if (['vente', 'location'].includes(filter)) {
                    filterProperties('transaction', filter);
                } else {
                    fetchProperties();
                }
            });
        });

        document.getElementById('search-input').addEventListener('input', e => {
            const val = e.target.value.toLowerCase();
            if (val.length > 2) filterProperties('search', val);
            else if (val.length === 0) fetchProperties();
        });

        document.getElementById('reset-filters').addEventListener('click', () => {
            document.getElementById('search-input').value = '';
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('bg-black', 'text-white');
                btn.classList.add('hover:border-black');
            });
            document.querySelector('.filter-btn[data-filter="all"]').classList.add('bg-black', 'text-white');
            fetchProperties();
        });

        fetchProperties();
    });
</script>
@endpush
