// Importation des bibliothèques nécessaires
import QrScanner from 'qr-scanner';
import QRCode from 'qrcode';

document.addEventListener('DOMContentLoaded', function() {
    // Éléments du DOM
    const workflowButton = document.getElementById('workflowMainButton');
    const workflowMenu = document.getElementById('workflowMenu');
    const scanQRButton = document.getElementById('scanQRButton');
    const qrScannerModal = document.getElementById('qrScannerModal');
    const qrCodeModal = document.getElementById('qrCodeModal');
    const closeQRScannerButton = document.getElementById('closeQRScannerButton');
    const closeQRCodeButton = document.getElementById('closeQRCodeButton');
    const missionsList = document.getElementById('missionsList');

    // Toggle du menu workflow
    workflowButton.addEventListener('click', () => {
        workflowMenu.classList.toggle('hidden');
        loadUserMissions(); // Charger les missions à chaque ouverture
    });

    // Fermeture du menu au clic en dehors
    document.addEventListener('click', (e) => {
        if (!workflowButton.contains(e.target) && !workflowMenu.contains(e.target)) {
            workflowMenu.classList.add('hidden');
        }
    });

    // Fonction pour charger les missions de l'utilisateur
    async function loadUserMissions() {
        try {
            const response = await fetch('/user-missions');
            const data = await response.json();
            
            missionsList.innerHTML = '';
            
            data.missions.forEach(mission => {
                const missionElement = createMissionElement(mission);
                missionsList.appendChild(missionElement);
            });
        } catch (error) {
            console.error('Erreur lors du chargement des missions:', error);
        }
    }

    // Création d'un élément de mission
    function createMissionElement(mission) {
        const div = document.createElement('div');
        div.className = 'p-4 border-b hover:bg-gray-50 cursor-pointer';
        
        const statusColor = getStatusColor(mission.statut);
        
        div.innerHTML = `
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="font-medium">${mission.description}</h4>
                    <p class="text-sm text-gray-600">${mission.recipient_address}</p>
                </div>
                <span class="px-2 py-1 rounded-full text-xs ${statusColor}">
                    ${mission.statut}
                </span>
            </div>
            <div class="mt-2 flex gap-2">
                <button onclick="showQRCode('${mission.id}')" class="text-sm text-coligo-blue hover:text-coligo-orange">
                    Voir QR Code
                </button>
                ${mission.statut === 'en cours' ? `
                    <button onclick="markAsDelivered('${mission.id}')" class="text-sm text-green-600 hover:text-green-700">
                        Marquer comme livré
                    </button>
                ` : ''}
            </div>
        `;
        
        return div;
    }

    // Couleurs selon le statut
    function getStatusColor(status) {
        switch (status) {
            case 'en attente':
                return 'bg-yellow-100 text-yellow-800';
            case 'en cours':
                return 'bg-blue-100 text-blue-800';
            case 'livré':
                return 'bg-green-100 text-green-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    // Gestion du scanner QR
    let qrScanner = null;

    scanQRButton.addEventListener('click', () => {
        qrScannerModal.classList.remove('hidden');
        initQRScanner();
    });

    closeQRScannerButton.addEventListener('click', () => {
        qrScannerModal.classList.add('hidden');
        if (qrScanner) {
            qrScanner.stop();
        }
    });

    async function initQRScanner() {
        const videoElement = document.createElement('video');
        document.getElementById('qrScanner').appendChild(videoElement);

        qrScanner = new QrScanner(
            videoElement,
            result => handleQRScan(result),
            {
                returnDetailedScanResult: true,
                highlightScanRegion: true,
                highlightCodeOutline: true,
            }
        );

        try {
            await qrScanner.start();
        } catch (error) {
            console.error('Erreur lors du démarrage du scanner:', error);
            alert('Impossible d\'accéder à la caméra. Veuillez vérifier vos permissions.');
        }
    }

    async function handleQRScan(result) {
        qrScanner.stop();
        qrScannerModal.classList.add('hidden');

        try {
            const missionId = result.data;
            const response = await fetch(`/verify-mission/${missionId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                alert('Mission vérifiée avec succès!');
                loadUserMissions(); // Recharger les missions
            } else {
                alert(data.message || 'Erreur lors de la vérification de la mission');
            }
        } catch (error) {
            console.error('Erreur lors de la vérification du QR code:', error);
            alert('Erreur lors de la vérification du QR code');
        }
    }

    // Génération et affichage du QR Code
    window.showQRCode = async function(missionId) {
        const qrCodeDisplay = document.getElementById('qrCodeDisplay');
        qrCodeDisplay.innerHTML = '';

        try {
            const qrCodeUrl = await QRCode.toDataURL(missionId);
            const img = document.createElement('img');
            img.src = qrCodeUrl;
            img.className = 'w-48 h-48';
            qrCodeDisplay.appendChild(img);
            qrCodeModal.classList.remove('hidden');
        } catch (error) {
            console.error('Erreur lors de la génération du QR code:', error);
            alert('Erreur lors de la génération du QR code');
        }
    };

    // Marquer une mission comme livrée
    window.markAsDelivered = async function(missionId) {
        try {
            const response = await fetch(`/mark-delivered/${missionId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                alert('Mission marquée comme livrée!');
                loadUserMissions(); // Recharger les missions
            } else {
                alert(data.message || 'Erreur lors de la mise à jour de la mission');
            }
        } catch (error) {
            console.error('Erreur lors de la mise à jour de la mission:', error);
            alert('Erreur lors de la mise à jour de la mission');
        }
    };

    // Fermeture du modal QR Code
    closeQRCodeButton.addEventListener('click', () => {
        qrCodeModal.classList.add('hidden');
    });
});

