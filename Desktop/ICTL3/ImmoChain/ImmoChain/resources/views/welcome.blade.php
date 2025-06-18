@extends('layouts.app')

@section('title', 'ImmoChain - Trouvez votre bien immobilier idéal')

@section('content')
<div class="container mx-auto px-4">
    <!-- Grille de propriétés -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($properties as $property)
        <div class="property-card group">
            <div class="relative aspect-w-16 aspect-h-9 rounded-xl overflow-hidden mb-3">
                <!-- Slider d'images -->
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                        @foreach($property->images as $image)
                        <div class="swiper-slide">
                            <img src="{{ $image }}" alt="{{ $property->title }}" class="object-cover w-full h-full">
                        </div>
                        @endforeach
                    </div>
                    <!-- Navigation -->
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
                
                <!-- Bouton favoris -->
                <button class="absolute top-3 right-3 text-white hover:scale-110 transition">
                    <i class="far fa-heart text-2xl"></i>
                </button>
            </div>

            <!-- Informations de la propriété -->
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-semibold">{{ $property->location }}</h3>
                    <p class="text-gray-500">Hôte : {{ $property->host }}</p>
                    <p class="text-gray-500">{{ $property->dates }}</p>
                    <p class="mt-1"><span class="font-semibold">{{ $property->price }}€</span> par nuit</p>
                </div>
                @if($property->rating)
                <div class="flex items-center">
                    <i class="fas fa-star text-sm"></i>
                    <span class="ml-1">{{ $property->rating }}</span>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Bouton Carte -->
    <button onclick="openModal('mapModal')" class="fixed bottom-20 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white px-6 py-3 rounded-full shadow-lg hover:scale-105 transition md:bottom-6">
        <i class="fas fa-map-marker-alt mr-2"></i>
        Afficher la carte
    </button>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script>
    // Initialisation des sliders
    const swipers = document.querySelectorAll('.swiper-container');
    swipers.forEach(element => {
        new Swiper(element, {
            loop: true,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    });
</script>
@endpush