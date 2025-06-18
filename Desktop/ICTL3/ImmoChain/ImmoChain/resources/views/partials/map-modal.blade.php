<div id="mapModal" class="modal fixed inset-0 z-50 hidden">
    <div class="h-screen flex flex-col">
        <div class="fixed inset-0 bg-black opacity-40"></div>
        
        <div class="relative flex-1 bg-white">
            <!-- En-tête -->
            <div class="absolute top-0 left-0 right-0 z-10 bg-white border-b">
                <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                    <button onclick="closeModal('mapModal')" class="text-2xl">&times;</button>
                    <div class="search-bar bg-white rounded-full border px-4 py-2 flex items-center space-x-2">
                        <i class="fas fa-search text-gray-400"></i>
                        <input type="text" placeholder="Rechercher une destination" class="focus:outline-none">
                    </div>
                    <div></div>
                </div>
            </div>

            <!-- Carte et liste des propriétés -->
            <div class="h-full flex">
                <!-- Liste des propriétés -->
                <div class="w-1/2 overflow-y-auto pt-20">
                    <div class="container mx-auto px-4 py-6">
                        <div class="space-y-6">
                            @foreach($properties as $property)
                            <div class="flex space-x-4 p-4 hover:bg-gray-50 rounded-lg cursor-pointer">
                                <div class="w-32 h-32 rounded-lg overflow-hidden">
                                    <img src="{{ $property->image }}" alt="{{ $property->title }}" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold">{{ $property->location }}</h3>
                                    <p class="text-gray-500">{{ $property->description }}</p>
                                    <p class="mt-2"><span class="font-semibold">{{ $property->price }}€</span> par nuit</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Carte -->
                <div class="w-1/2 bg-gray-100" id="map"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY"></script>
<script>
    function initMap() {
        const map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: 46.227638, lng: 2.213749 }, // Centre de la France
            zoom: 6
        });

        // Ajouter les marqueurs pour chaque propriété
        properties.forEach(property => {
            const marker = new google.maps.Marker({
                position: { lat: property.lat, lng: property.lng },
                map: map,
                title: property.title
            });

            marker.addListener('click', () => {
                // Afficher les détails de la propriété
            });
        });
    }
</script>
@endpush