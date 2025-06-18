<!-- resources/views/interactive-map.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImmoChain - Carte Interactive</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
</head>
<body class="bg-gray-100">
    <header class="bg-white shadow-md">
        <nav class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="/" class="text-2xl font-bold text-blue-600">ImmoChain</a>
            <div>
                <button onclick="openModal('loginModal')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded mr-2">Se connecter</button>
                <button onclick="openModal('registerModal')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">S'inscrire</button>
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Carte Interactive des Biens Immobiliers</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-2">
                <div id="map" class="h-[600px] rounded-lg shadow-md"></div>
            </div>
            <div>
                <div class="bg-white rounded-lg shadow-md p-4 mb-4">
                    <h2 class="text-xl font-semibold mb-4">Filtres</h2>
                    <form id="filterForm">
                        <div class="mb-4">
                            <label for="type" class="block mb-2">Type de bien</label>
                            <select id="type" name="type" class="w-full px-3 py-2 border rounded">
                                <option value="">Tous</option>
                                <option value="maison">Maison</option>
                                <option value="appartement">Appartement</option>
                                <option value="terrain">Terrain</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="price" class="block mb-2">Prix maximum</label>
                            <input type="number" id="price" name="price" class="w-full px-3 py-2 border rounded" placeholder="Prix en €">
                        </div>
                        <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Appliquer les filtres</button>
                    </form>
                </div>
                <div id="propertyDetails" class="bg-white rounded-lg shadow-md p-4 hidden">
                    <h2 class="text-xl font-semibold mb-4">Détails du bien</h2>
                    <div id="propertyContent"></div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-8 mt-8">
        <div class="container mx-auto px-4">
            <div class="flex flex-wrap justify-between">
                <div class="w-full md:w-1/3 mb-4 md:mb-0">
                    <h3 class="text-xl font-bold mb-2">ImmoChain</h3>
                    <p>Révolutionnez vos transactions immobilières grâce à la blockchain.</p>
                </div>
                <div class="w-full md:w-1/3 mb-4 md:mb-0">
                    <h3 class="text-xl font-bold mb-2">Liens utiles</h3>
                    <ul>
                        <li><a href="#" class="hover:text-blue-300">Mentions légales</a></li>
                        <li><a href="#" class="hover:text-blue-300">Politique de confidentialité</a></li>
                        <li><a href="#" class="hover:text-blue-300">Conditions d'utilisation</a></li>
                    </ul>
                </div>
                <div class="w-full md:w-1/3">
                    <h3 class="text-xl font-bold mb-2">Suivez-nous</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="hover:text-blue-300"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
                        <a href="#" class="hover:text-blue-300"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg></a>
                        <a href="#" class="hover:text-blue-300"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm4.441 16.892c-2.102.144-6.784.144-8.883 0C5.282 16.736 5.017 15.622 5 12c.017-3.629.285-4.736 2.558-4.892 2.099-.144 6.782-.144 8.883 0C18.718 7.264 18.982 8.378 19 12c-.018 3.629-.285 4.736-2.559 4.892zM10 9.658l4.917 2.338L10 14.342V9.658z"/></svg></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Login and Register Modals (same as in home.blade.php) -->

    <script>
        // Initialize the map
        const map = L.map('map').setView([46.603354, 1.888334], 6); // Center on France

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Sample property data (replace with actual data from your backend)
        const properties = [
            { id: 1, lat: 48.8566, lng: 2.3522, type: 'appartement', price: 350000, title: 'Appartement à Paris' },
            { id: 2, lat: 43.2965, lng: 5.3698, type: 'maison', price: 450000, title: 'Maison à Marseille' },
            { id: 3, lat: 45.7640, lng: 4.8357, type: 'terrain', price: 200000, title: 'Terrain à Lyon' },
        ];

        // Add markers to the map
        properties.forEach(property => {
            const marker = L.marker([property.lat, property.lng]).addTo(map);
            marker.on('click', () => showPropertyDetails(property));
        });

        function showPropertyDetails(property) {
            const propertyDetails = document.getElementById('propertyDetails');
            const propertyContent = document.getElementById('propertyContent');
            propertyContent.innerHTML = `
                <h3 class="text-lg font-semibold mb-2">${property.title}</h3>
                <p><strong>Type:</strong> ${property.type}</p>
                <p><strong>Prix:</strong> ${property.price.toLocaleString()} €</p>
                <button onclick="sendOffer(${property.id})" class="mt-4 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Envoyer une offre</button>
            `;
            propertyDetails.classList.remove('hidden');
        }

        function sendOffer(propertyId) {
            // Implement the logic to send an offer (e.g., open a modal or redirect to an offer page)
            alert(`Envoi d'une offre pour le bien ${propertyId}`);
        }

        // Filter form submission
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const type = document.getElementById('type').value;
            const maxPrice = document.getElementById('price').value;

            // Filter properties based on form inputs
            const filteredProperties = properties.filter(property => {
                return (!type || property.type === type) && (!maxPrice || property.price <= maxPrice);
            });

            // Clear existing markers and add filtered markers
            map.eachLayer(layer => {
                if (layer instanceof L.Marker) {
                    map.removeLayer(layer);
                }
            });

            filteredProperties.forEach(property => {
                const marker = L.marker([property.lat, property.lng]).addTo(map);
                marker.on('click', () => showPropertyDetails(property));
            });
        });

        // Modal functions (same as in home.blade.php)
        function openModal(modalId, role = '') {
            const modal = document.getElementById(modalId);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            if (role && modalId === 'registerModal') {
                document.getElementById('role').value = role.toLowerCase();
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    </script>
</body>
</html>