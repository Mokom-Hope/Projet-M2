// Fonction pour vérifier et demander les permissions de géolocalisation
async function checkLocationPermission() {
    try {
        // Vérifier si la géolocalisation est supportée
        if (!("geolocation" in navigator)) {
            throw new Error("La géolocalisation n'est pas supportée par votre navigateur.");
        }

        // Vérifier l'état des permissions
        const permissionStatus = await navigator.permissions.query({ name: 'geolocation' });
        
        switch (permissionStatus.state) {
            case 'granted':
                return true;
            case 'prompt':
                // Afficher un message explicatif à l'utilisateur
                const userChoice = confirm(
                    "ColiGo a besoin d'accéder à votre position pour :" +
                    "\n- Afficher les missions à proximité" +
                    "\n- Calculer les distances de livraison" +
                    "\n- Suivre vos livraisons en temps réel" +
                    "\n\nAutorisez-vous l'accès à votre position ?"
                );
                if (!userChoice) {
                    throw new Error("Vous devez autoriser l'accès à votre position pour utiliser ColiGo.");
                }
                return true;
            case 'denied':
                throw new Error(
                    "L'accès à votre position a été bloqué. " +
                    "Veuillez modifier les paramètres de votre navigateur pour permettre la géolocalisation."
                );
            default:
                return false;
        }
    } catch (error) {
        console.error("Erreur de permission géolocalisation:", error);
        // Afficher un message d'erreur convivial à l'utilisateur
        showLocationError(error.message);
        return false;
    }
}

// Fonction pour afficher les erreurs de géolocalisation
function showLocationError(message) {
    // Créer une alerte personnalisée
    const alertDiv = document.createElement('div');
    alertDiv.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50';
    alertDiv.role = 'alert';
    
    alertDiv.innerHTML = `
        <strong class="font-bold">Erreur de localisation!</strong>
        <span class="block sm:inline">${message}</span>
        <div class="mt-2">
            <button onclick="requestLocationPermission()" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                Réessayer
            </button>
            <button onclick="useDefaultLocation()" class="ml-2 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Utiliser position par défaut
            </button>
        </div>
    `;

    // Supprimer l'alerte précédente si elle existe
    const existingAlert = document.querySelector('[role="alert"]');
    if (existingAlert) {
        existingAlert.remove();
    }

    document.body.appendChild(alertDiv);
}

// Fonction pour demander à nouveau la permission
async function requestLocationPermission() {
    try {
        const position = await getCurrentPosition();
        initializeMap(position.coords.longitude, position.coords.latitude);
        // Supprimer le message d'erreur
        const alertDiv = document.querySelector('[role="alert"]');
        if (alertDiv) alertDiv.remove();
    } catch (error) {
        console.error("Erreur lors de la demande de permission:", error);
    }
}

// Fonction pour utiliser une position par défaut (Yaoundé)
function useDefaultLocation() {
    initializeMap(11.5021, 3.8480);
    // Supprimer le message d'erreur
    const alertDiv = document.querySelector('[role="alert"]');
    if (alertDiv) alertDiv.remove();
}

// Fonction pour obtenir la position actuelle
function getCurrentPosition() {
    return new Promise((resolve, reject) => {
        navigator.geolocation.getCurrentPosition(resolve, reject, {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 0
        });
    });
}

// Modifier la fonction d'initialisation de la carte
async function initializeMapWithPermission() {
    try {
        const hasPermission = await checkLocationPermission();
        if (hasPermission) {
            const position = await getCurrentPosition();
            initializeMap(position.coords.longitude, position.coords.latitude);
        } else {
            useDefaultLocation();
        }
    } catch (error) {
        console.error("Erreur d'initialisation de la carte:", error);
        useDefaultLocation();
    }
}

// Remplacer l'ancien code d'initialisation par celui-ci
document.addEventListener('DOMContentLoaded', () => {
    initializeMapWithPermission();
    loadRecentActivities();
    loadUserStats();
});