<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ImmoChain')</title>
    
    <!-- Favicon -->
    <link rel="icon" href="/favicon.ico">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css">
    
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <!-- Styles personnalisés -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            padding-top: 80px;
        }
        .search-bar-shadow {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .property-card-transition {
            transition: transform 0.2s ease-in-out;
        }
        .property-card-transition:hover {
            transform: translateY(-4px);
        }
        .flash-message {
            position: fixed;
            top: 90px;
            right: 20px;
            z-index: 1000;
            max-width: 350px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: fadeInOut 5s forwards;
        }
        @keyframes fadeInOut {
            0% { opacity: 0; transform: translateY(-20px); }
            10% { opacity: 1; transform: translateY(0); }
            90% { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(-20px); }
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    @include('partials.header')
    
    <!-- Messages flash -->
    <div id="flash-messages">
        @if (session('success'))
        <div class="flash-message bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="absolute top-0 right-0 p-2" onclick="this.parentElement.remove()">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        @endif

        @if (session('error'))
        <div class="flash-message bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" class="absolute top-0 right-0 p-2" onclick="this.parentElement.remove()">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        @endif
    </div>
    
    <!-- Contenu principal -->
    <main>
        @yield('content')
    </main>
    
    <!-- Footer -->
    @include('partials.footer')
    
    <!-- Modals -->
    <div id="searchModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex justify-center items-center">
        <div class="bg-white rounded-lg max-w-lg w-full p-6 modal-content">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Recherche avancée</h2>
                <button onclick="toggleModal('searchModal')" class="text-gray-500 hover:text-black">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="/map" method="GET" class="space-y-4">
                <div>
                    <label for="search-location" class="block mb-1 font-medium">Où cherchez-vous ?</label>
                    <input type="text" id="search-location" name="location" placeholder="Ville, quartier..." class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label for="search-type" class="block mb-1 font-medium">Type de bien</label>
                    <select id="search-type" name="type" class="w-full border rounded-lg px-3 py-2">
                        <option value="">Tous les types</option>
                        <option value="Maison">Maison</option>
                        <option value="Terrain">Terrain</option>
                        <option value="LocalCommercial">Local commercial</option>
                        <option value="Studio">Studio</option>
                        <option value="Chambre">Chambre</option>
                    </select>
                </div>
                <div>
                    <label for="search-transaction" class="block mb-1 font-medium">Type de transaction</label>
                    <select id="search-transaction" name="transaction" class="w-full border rounded-lg px-3 py-2">
                        <option value="">Tous</option>
                        <option value="vente">Vente</option>
                        <option value="location">Location</option>
                    </select>
                </div>
                <div>
                    <label for="search-price" class="block mb-1 font-medium">Budget maximum</label>
                    <input type="number" id="search-price" name="max_price" placeholder="Prix maximum" class="w-full border rounded-lg px-3 py-2">
                </div>
                <button type="submit" class="w-full bg-black text-white py-3 rounded-lg font-medium hover:bg-gray-800 transition">
                    Rechercher
                </button>
            </form>
        </div>
    </div>
    
    <div id="filtersModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex justify-center items-center">
        <div class="bg-white rounded-lg max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto modal-content">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Filtres</h2>
                <button onclick="toggleModal('filtersModal')" class="text-gray-500 hover:text-black">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="/map" method="GET" class="space-y-4">
                <div>
                    <label class="block mb-1 font-medium">Type de transaction</label>
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="transaction" value="" checked class="mr-2">
                            <span>Tous</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="transaction" value="vente" class="mr-2">
                            <span>Vente</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="transaction" value="location" class="mr-2">
                            <span>Location</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block mb-1 font-medium">Type de bien</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="type[]" value="Maison" class="mr-2">
                            <span>Maison</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="type[]" value="Terrain" class="mr-2">
                            <span>Terrain</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="type[]" value="LocalCommercial" class="mr-2">
                            <span>Local commercial</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="type[]" value="Studio" class="mr-2">
                            <span>Studio</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="type[]" value="Chambre" class="mr-2">
                            <span>Chambre</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block mb-1 font-medium">Prix</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="min_price" placeholder="Min" class="w-full border rounded-lg px-3 py-2">
                        <span>-</span>
                        <input type="number" name="max_price" placeholder="Max" class="w-full border rounded-lg px-3 py-2">
                    </div>
                </div>
                <div>
                    <label class="block mb-1 font-medium">Superficie (m²)</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="min_area" placeholder="Min" class="w-full border rounded-lg px-3 py-2">
                        <span>-</span>
                        <input type="number" name="max_area" placeholder="Max" class="w-full border rounded-lg px-3 py-2">
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition">
                        Appliquer
                    </button>
                    <button type="reset" class="px-4 py-2 border rounded-lg hover:border-black">
                        Réinitialiser
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
    <script>
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
        
        // Empêcher la fermeture des modals lors des clics à l'intérieur
        document.addEventListener('DOMContentLoaded', function() {
            // Empêcher la propagation des clics à l'intérieur des modals
            document.querySelectorAll('.modal-content').forEach(modal => {
                modal.addEventListener('click', function(event) {
                    event.stopPropagation();
                });
            });
            
            // Fermer les modals en cliquant en dehors
            document.addEventListener('click', function(event) {
                const searchModal = document.getElementById('searchModal');
                const filtersModal = document.getElementById('filtersModal');
                
                // Vérifier si le clic est en dehors du modal et n'est pas sur le bouton qui ouvre le modal
                if (!searchModal.classList.contains('hidden') && 
                    !event.target.closest('.modal-content') && 
                    !event.target.closest('button[onclick*="toggleModal"]') &&
                    !event.target.closest('.search-trigger')) {
                    searchModal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }
                
                if (!filtersModal.classList.contains('hidden') && 
                    !event.target.closest('.modal-content') && 
                    !event.target.closest('button[onclick*="toggleModal"]') &&
                    !event.target.closest('.filters-trigger')) {
                    filtersModal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }
            });
        });
        
        // Supprimer automatiquement les messages flash après 5 secondes
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const flashMessages = document.querySelectorAll('.flash-message');
                flashMessages.forEach(function(message) {
                    message.remove();
                });
            }, 5000);
        });
    </script>
    

    @stack('scripts')
    <!-- Chatbot Immo (IA Immobilière) -->
    <div id="immo-chat" class="fixed bottom-6 right-6 z-50">
        <button id="toggle-chat" class="bg-black text-white p-3 rounded-full shadow-lg hover:bg-gray-800 focus:outline-none flex items-center justify-center">
            <i class="fas fa-comments"></i>
        </button>

        <div id="chat-box" class="hidden bg-white rounded-lg w-80 md:w-96 max-h-[500px] flex flex-col shadow-xl">
            <div class="bg-black text-white p-4 rounded-t-lg flex justify-between items-center sticky top-0 z-10">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center mr-2">
                        <i class="fas fa-user text-black"></i>
                    </div>
                    <span class="font-bold">Immo, votre agent immobilier</span>
                </div>
                <button id="close-chat" class="text-white hover:text-gray-200 p-2">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="chat-messages" class="p-4 overflow-y-auto flex-1 space-y-3 text-sm"></div>
            <div id="typing-indicator" class="px-4 py-2 hidden">
                <div class="flex items-center text-gray-500 text-sm">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <span class="ml-2">Immo réfléchit...</span>
                </div>
            </div>
            <form id="chat-form" class="flex border-t sticky bottom-0 bg-white">
                <input type="text" id="chat-input" placeholder="Votre message..." class="flex-1 p-3 border-0 focus:outline-none text-sm" autocomplete="off">
                <button type="submit" class="bg-black text-white px-4 hover:bg-gray-800 transition">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>

    <style>
        .typing-dot {
            width: 8px;
            height: 8px;
            background-color: #000;
            border-radius: 50%;
            margin: 0 2px;
            display: inline-block;
            animation: typing-animation 1.4s infinite ease-in-out both;
        }

        .typing-dot:nth-child(1) { animation-delay: -0.32s; }
        .typing-dot:nth-child(2) { animation-delay: -0.16s; }

        @keyframes typing-animation {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }

        /* chat est masqué par défaut, on ne définit PAS display ici */
        #chat-box {
            flex-direction: column;
            max-height: 80vh;
        }

        /* Scroll correct pour la zone de messages */
        #chat-messages {
            flex: 1;
            overflow-y: auto;
            max-height: calc(500px - 130px); /* Ajuster selon header/footer */
            scroll-behavior: smooth;
        }
    </style>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Variables de conversation
            let sessionId = generateSessionId();
            let chatHistory = [];
            let userName = '';

            const toggleBtn = document.getElementById('toggle-chat');
            const closeBtn = document.getElementById('close-chat');
            const chatBox = document.getElementById('chat-box');
            const form = document.getElementById('chat-form');
            const input = document.getElementById('chat-input');
            const messages = document.getElementById('chat-messages');
            const typingIndicator = document.getElementById('typing-indicator');

            // S'assurer que le chat est fermé au chargement
            chatBox.classList.add('hidden');

            // Événements
            toggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                chatBox.classList.toggle('hidden');
                if (!chatBox.classList.contains('hidden')) {
                    // Si c'est la première ouverture et qu'il n'y a pas d'historique
                    if (messages.innerHTML.trim() === "" && chatHistory.length === 0) {
                        showWelcomeMessage();
                    }
                    // Focus sur l'input
                    setTimeout(() => input.focus(), 100);
                    // Scroll vers le bas
                    scrollToBottom();
                }
            });

            // Correction du bouton de fermeture
            closeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation(); // Empêcher la propagation de l'événement
                chatBox.classList.add('hidden');
            });

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const userMsg = input.value.trim();
                if (!userMsg) return;

                // Ajouter le message utilisateur
                appendMessage('Vous', userMsg);
                input.value = '';

                // Vérifier si c'est une présentation de nom
                const nameMatch = userMsg.match(/(?:je m['']appelle|moi c['']est|je suis)\s+(\w+)/i);
                if (nameMatch) {
                    userName = nameMatch[1].charAt(0).toUpperCase() + nameMatch[1].slice(1);
                }

                // Sauvegarder dans l'historique
                chatHistory.push({ role: 'user', content: userMsg });
                saveConversation();

                // Afficher l'indicateur de typing
                typingIndicator.classList.remove('hidden');
                
                // Scroll vers le bas pour voir l'indicateur de typing
                scrollToBottom();

                // Envoi au backend
                await sendMessageToBackend(userMsg);
            });

            // Fonction pour faire défiler vers le bas
            function scrollToBottom() {
                setTimeout(() => {
                    messages.scrollTop = messages.scrollHeight;
                }, 100);
            }

            // Affiche le message de bienvenue
            function showWelcomeMessage() {
                appendMessage('Immo', "Bonjour, je suis Immo, votre assistant immobilier virtuel. Comment puis-je vous aider aujourd'hui ? Vous cherchez à acheter, louer ou investir dans l'immobilier ?");
                
                // Sauvegarder dans l'historique
                chatHistory.push({
                    role: 'assistant',
                    content: "Bonjour, je suis Immo, votre assistant immobilier virtuel. Comment puis-je vous aider aujourd'hui ? Vous cherchez à acheter, louer ou investir dans l'immobilier ?"
                });
                saveConversation();
            }

            // Envoie le message au backend
            async function sendMessageToBackend(userMsg) {
                try {
                    const response = await fetch('/api/immo-chat', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            message: userMsg,
                            name: userName || 'Client', // Utiliser le nom si disponible
                            session_id: sessionId
                        })
                    });

                    if (!response.ok) {
                        throw new Error('Erreur réseau');
                    }

                    const data = await response.json();

                    // Masquer l'indicateur de typing
                    typingIndicator.classList.add('hidden');

                    // Afficher la réponse avec un délai pour simuler la réflexion
                    setTimeout(() => {
                        appendMessage('Immo', data.reply);

                        // Sauvegarder dans l'historique
                        chatHistory.push({ role: 'assistant', content: data.reply });
                        saveConversation();

                        // Scroll vers le bas
                        scrollToBottom();
                    }, 1000);
                } catch (error) {
                    console.error('Erreur:', error);

                    // Masquer l'indicateur de typing
                    typingIndicator.classList.add('hidden');

                    // Message d'erreur
                    appendMessage('Immo', "Désolé, j'ai rencontré un problème technique. Pourriez-vous reformuler votre demande ?");
                    scrollToBottom();
                }
            }

            // Ajoute un message dans la conversation
            function appendMessage(sender, text) {
                const msg = document.createElement('div');

                if (sender === 'Immo') {
                    msg.className = 'flex items-start';
                    msg.innerHTML = `
                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center mr-2 flex-shrink-0">
                            <i class="fas fa-user text-gray-600 text-sm"></i>
                        </div>
                        <div class="bg-gray-100 rounded-lg py-2 px-3 max-w-[85%]">
                            <div class="text-xs text-gray-500 mb-1">Immo</div>
                            <div>${text}</div>
                        </div>
                    `;
                } else {
                    msg.className = 'flex items-start justify-end';
                    msg.innerHTML = `
                        <div class="bg-black text-white rounded-lg py-2 px-3 max-w-[85%]">
                            <div class="text-xs text-gray-300 mb-1">Vous</div>
                            <div>${text}</div>
                        </div>
                    `;
                }

                messages.appendChild(msg);
            }

            // Génère un ID de session unique
            function generateSessionId() {
                return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
                    const r = (Math.random() * 16) | 0;
                    const v = c === 'x' ? r : (r & 0x3) | 0x8;
                    return v.toString(16);
                });
            }

            // Sauvegarde la conversation dans le localStorage
            function saveConversation() {
                localStorage.setItem('immo_chat_history', JSON.stringify(chatHistory));
                localStorage.setItem('immo_session_id', sessionId);
                if (userName) {
                    localStorage.setItem('immo_user_name', userName);
                }
            }

            // Restaure la conversation depuis le localStorage
            function restoreConversation() {
                const savedHistory = localStorage.getItem('immo_chat_history');
                const savedSessionId = localStorage.getItem('immo_session_id');
                const savedUserName = localStorage.getItem('immo_user_name');

                if (savedHistory && savedSessionId) {
                    chatHistory = JSON.parse(savedHistory);
                    sessionId = savedSessionId;
                    
                    if (savedUserName) {
                        userName = savedUserName;
                    }

                    // Recréer la conversation dans l'interface
                    messages.innerHTML = '';

                    chatHistory.forEach((msg) => {
                        if (msg.role === 'user') {
                            appendMessage('Vous', msg.content);
                        } else {
                            appendMessage('Immo', msg.content);
                        }
                    });
                }
            }

            // Restaurer la conversation au chargement
            restoreConversation();
        });
    </script>
</body>
</html>
