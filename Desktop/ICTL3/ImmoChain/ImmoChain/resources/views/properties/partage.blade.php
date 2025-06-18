<!-- Ajoutez ce Modal de partage juste avant la fermeture de @section('content') -->
<div id="shareModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex justify-center items-center">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Partager ce bien</h3>
            <button onclick="toggleModal('shareModal')" class="text-gray-500 hover:text-black">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="mb-4">
            <p class="text-gray-600 mb-2">Lien du bien</p>
            <div class="flex items-center">
                <input type="text" id="share-url" class="flex-1 p-2 border rounded-l-lg bg-gray-50" readonly>
                <button id="copy-link-button" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-r-lg border border-l-0 transition-colors">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
        </div>

        <p class="text-gray-600 mb-3">Ou partager sur</p>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <a href="#" id="share-facebook" class="share-btn bg-[#1877F2] hover:bg-[#0e5fc0] text-white py-2 px-4 rounded flex items-center justify-center gap-2 transition-colors">
                <i class="fa-brands fa-facebook-f"></i>
                <span>Facebook</span>
            </a>
            <a href="#" id="share-twitter" class="share-btn bg-[#1DA1F2] hover:bg-[#0c85d0] text-white py-2 px-4 rounded flex items-center justify-center gap-2 transition-colors">
                <i class="fab fa-twitter"></i>
                <span>Twitter</span>
            </a>
            <a href="#" id="share-whatsapp" class="share-btn bg-[#25D366] hover:bg-[#1dad55] text-white py-2 px-4 rounded flex items-center justify-center gap-2 transition-colors">
                <i class="fab fa-whatsapp"></i>
                <span>WhatsApp</span>
            </a>
            <a href="#" id="share-telegram" class="share-btn bg-[#0088cc] hover:bg-[#0077b3] text-white py-2 px-4 rounded flex items-center justify-center gap-2 transition-colors">
                <i class="fab fa-telegram"></i> <!-- changé de 'fa-telegram-plane' -->
                <span>Telegram</span>
            </a>
            <a href="#" id="share-linkedin" class="share-btn bg-[#0077b5] hover:bg-[#005f8d] text-white py-2 px-4 rounded flex items-center justify-center gap-2 transition-colors">
                <i class="fab fa-linkedin"></i> <!-- changé de 'fa-linkedin-in' -->
                <span>LinkedIn</span>
            </a>
            <a href="#" id="share-email" class="share-btn bg-gray-700 hover:bg-gray-800 text-white py-2 px-4 rounded flex items-center justify-center gap-2 transition-colors">
                <i class="fas fa-envelope"></i>
                <span>Email</span>
            </a>
        </div>

        
        <div class="mt-4 text-center">
            <p class="text-sm text-gray-500">Aidez vos proches à trouver leur future maison !</p>
        </div>
    </div>
</div>

<!-- Ajoutez ce toast pour la confirmation de copie du lien -->
<div id="toast-notification" class="fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg transform transition-all duration-300 translate-y-20 opacity-0 flex items-center">
    <i class="fas fa-check-circle mr-2"></i>
    <span>Lien copié dans le presse-papiers !</span>
</div>



@push('scripts')
<script>
    // Initialiser le bouton de partage
    document.addEventListener('DOMContentLoaded', function() {
        // Ajouter l'événement pour ouvrir le modal de partage
        document.getElementById('share-button').addEventListener('click', function() {
            document.getElementById('share-url').value = window.location.href;
            toggleModal('shareModal');
        });
        
        // Copier le lien dans le presse-papiers
        document.getElementById('copy-link-button').addEventListener('click', function() {
            const shareUrl = document.getElementById('share-url');
            shareUrl.select();
            shareUrl.setSelectionRange(0, 99999); // Pour les mobiles
            
            try {
                navigator.clipboard.writeText(shareUrl.value).then(function() {
                    showToast();
                });
            } catch (err) {
                // Fallback pour les navigateurs qui ne supportent pas l'API clipboard
                document.execCommand('copy');
                showToast();
            }
        });
        
        // Initialiser les boutons de partage social
        setupShareButtons();
    });
    
    // Configurer les boutons de partage social
    function setupShareButtons() {
        document.getElementById('share-facebook').addEventListener('click', function(e) {
            e.preventDefault();
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.getElementById('property-title').textContent);
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}&quote=${title}`, '_blank');
        });
        
        document.getElementById('share-twitter').addEventListener('click', function(e) {
            e.preventDefault();
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.getElementById('property-title').textContent);
            window.open(`https://twitter.com/intent/tweet?text=${title}&url=${url}`, '_blank');
        });
        
        document.getElementById('share-whatsapp').addEventListener('click', function(e) {
            e.preventDefault();
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.getElementById('property-title').textContent);
            window.open(`https://wa.me/?text=${title} ${url}`, '_blank');
        });
        
        document.getElementById('share-telegram').addEventListener('click', function(e) {
            e.preventDefault();
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.getElementById('property-title').textContent);
            window.open(`https://t.me/share/url?url=${url}&text=${title}`, '_blank');
        });
        
        document.getElementById('share-linkedin').addEventListener('click', function(e) {
            e.preventDefault();
            const url = encodeURIComponent(window.location.href);
            window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank');
        });
        
        document.getElementById('share-email').addEventListener('click', function(e) {
            e.preventDefault();
            const url = window.location.href;
            const title = document.getElementById('property-title').textContent;
            const subject = encodeURIComponent(`Partage d'une annonce immobilière : ${title}`);
            const body = encodeURIComponent(`Bonjour,\n\nJ'ai trouvé ce bien immobilier qui pourrait t'intéresser : ${title}\n\nVoici le lien : ${url}\n\nÀ bientôt !`);
            window.location.href = `mailto:?subject=${subject}&body=${body}`;
        });
    }
    
    // Afficher un toast de confirmation
    function showToast() {
        const toast = document.getElementById('toast-notification');
        toast.classList.remove('translate-y-20', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');
        
        setTimeout(function() {
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('translate-y-20', 'opacity-0');
        }, 3000);
    }
</script>


@endpush