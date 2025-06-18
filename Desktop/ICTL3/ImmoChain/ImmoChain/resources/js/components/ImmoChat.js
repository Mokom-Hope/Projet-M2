// Composant ImmoChat.js - À importer dans votre application
const ImmoChat = () => {
    // Variables de conversation
    let sessionId = generateSessionId()
    let chatHistory = []
    let userName = ""
  
    // Initialisation du composant
    function init() {
      const chatContainer = document.createElement("div")
      chatContainer.id = "immo-chat"
      chatContainer.className = "fixed bottom-6 right-6 z-50"
  
      chatContainer.innerHTML = `
              <button id="toggle-chat" class="bg-black text-white p-3 rounded-full shadow-lg hover:bg-gray-800 focus:outline-none flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                  </svg>
              </button>
  
              <div id="chat-box" class="hidden bg-white rounded-lg w-80 md:w-96 max-h-[500px] flex flex-col shadow-xl">
                  <div class="bg-black text-white p-4 rounded-t-lg flex justify-between items-center sticky top-0 z-10">
                      <div class="flex items-center">
                          <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center mr-2">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                              </svg>
                          </div>
                          <span class="font-bold">Immo, votre agent immobilier</span>
                      </div>
                      <button id="close-chat" class="text-white hover:text-gray-200 p-2">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                          </svg>
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
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                    stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                          </svg>
                      </button>
                  </form>
              </div>
          `
  
      document.body.appendChild(chatContainer)
  
      // Ajout des styles pour l'animation de typing
      const style = document.createElement("style")
      style.textContent = `
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
              
              /* Assurer que l'en-tête et le formulaire restent visibles */
              #chat-box {
                  display: flex;
                  flex-direction: column;
              }
              
              #chat-messages {
                  flex: 1;
                  overflow-y: auto;
                  max-height: calc(500px - 130px); /* Hauteur totale moins l'en-tête et le formulaire */
                  scroll-behavior: smooth;
              }
          `
      document.head.appendChild(style)
  
      // Ajout des événements
      setupEventListeners()
  
      // Restaurer la conversation si elle existe
      restoreConversation()
  
      // S'assurer que le chat est fermé au chargement
      const chatBox = document.getElementById("chat-box")
      chatBox.classList.add("hidden")
    }
  
    // Configuration des écouteurs d'événements
    function setupEventListeners() {
      const toggleBtn = document.getElementById("toggle-chat")
      const closeBtn = document.getElementById("close-chat")
      const chatBox = document.getElementById("chat-box")
      const form = document.getElementById("chat-form")
      const input = document.getElementById("chat-input")
      const messages = document.getElementById("chat-messages")
  
      toggleBtn.addEventListener("click", (e) => {
        e.preventDefault()
        e.stopPropagation()
        chatBox.classList.toggle("hidden")
        if (chatBox.classList.contains("hidden")) {
          return
        }
  
        // Si c'est la première ouverture et qu'il n'y a pas d'historique
        if (messages.innerHTML.trim() === "" && chatHistory.length === 0) {
          showWelcomeMessage()
        }
  
        // Focus sur l'input
        setTimeout(() => input.focus(), 100)
  
        // Scroll vers le bas
        scrollToBottom()
      })
  
      closeBtn.addEventListener("click", (e) => {
        e.preventDefault()
        e.stopPropagation()
        chatBox.classList.add("hidden")
      })
  
      // Ajout d'un gestionnaire global pour s'assurer que le bouton fonctionne
      document.addEventListener("click", (e) => {
        if (e.target.closest("#close-chat") || e.target.closest("#close-chat svg")) {
          chatBox.classList.add("hidden")
        }
      })
  
      form.addEventListener("submit", async (e) => {
        e.preventDefault()
        const userMsg = input.value.trim()
        if (!userMsg) return
  
        // Ajouter le message utilisateur
        appendMessage("Vous", userMsg)
        input.value = ""
  
        // Vérifier si c'est une présentation de nom
        const nameMatch = userMsg.match(/(?:je m['']appelle|moi c['']est|je suis)\s+(\w+)/i)
        if (nameMatch) {
          userName = nameMatch[1].charAt(0).toUpperCase() + nameMatch[1].slice(1)
        }
  
        // Sauvegarder dans l'historique
        chatHistory.push({ role: "user", content: userMsg })
        saveConversation()
  
        // Afficher l'indicateur de typing
        document.getElementById("typing-indicator").classList.remove("hidden")
  
        // Scroll vers le bas pour voir l'indicateur de typing
        scrollToBottom()
  
        // Envoi au backend
        await sendMessageToBackend(userMsg)
      })
    }
  
    // Fonction pour faire défiler vers le bas
    function scrollToBottom() {
      setTimeout(() => {
        const messages = document.getElementById("chat-messages")
        messages.scrollTop = messages.scrollHeight
      }, 100)
    }
  
    // Affiche le message de bienvenue
    function showWelcomeMessage() {
      appendMessage(
        "Immo",
        "Bonjour, je suis Immo, votre assistant immobilier virtuel. Comment puis-je vous aider aujourd'hui ? Vous cherchez à acheter, louer ou investir dans l'immobilier ?",
      )
  
      // Sauvegarder dans l'historique
      chatHistory.push({
        role: "assistant",
        content:
          "Bonjour, je suis Immo, votre assistant immobilier virtuel. Comment puis-je vous aider aujourd'hui ? Vous cherchez à acheter, louer ou investir dans l'immobilier ?",
      })
      saveConversation()
    }
  
    // Envoie le message au backend
    async function sendMessageToBackend(userMsg) {
      try {
        const response = await fetch("/api/immo-chat", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
          },
          body: JSON.stringify({
            message: userMsg,
            name: userName || "Client", // Utiliser le nom si disponible
            session_id: sessionId,
          }),
        })
  
        if (!response.ok) {
          throw new Error("Erreur réseau")
        }
  
        const data = await response.json()
  
        // Masquer l'indicateur de typing
        document.getElementById("typing-indicator").classList.add("hidden")
  
        // Afficher la réponse avec un délai pour simuler la réflexion
        setTimeout(() => {
          appendMessage("Immo", data.reply)
  
          // Sauvegarder dans l'historique
          chatHistory.push({ role: "assistant", content: data.reply })
          saveConversation()
  
          // Scroll vers le bas
          scrollToBottom()
        }, 1000)
      } catch (error) {
        console.error("Erreur:", error)
  
        // Masquer l'indicateur de typing
        document.getElementById("typing-indicator").classList.add("hidden")
  
        // Message d'erreur
        appendMessage("Immo", "Désolé, j'ai rencontré un problème technique. Pourriez-vous reformuler votre demande ?")
        scrollToBottom()
      }
    }
  
    // Ajoute un message dans la conversation
    function appendMessage(sender, text) {
      const messages = document.getElementById("chat-messages")
      const msg = document.createElement("div")
  
      if (sender === "Immo") {
        msg.className = "flex items-start"
        msg.innerHTML = `
                  <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center mr-2 flex-shrink-0">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                  </div>
                  <div class="bg-gray-100 rounded-lg py-2 px-3 max-w-[85%]">
                      <div class="text-xs text-gray-500 mb-1">Immo</div>
                      <div>${text}</div>
                  </div>
              `
      } else {
        msg.className = "flex items-start justify-end"
        msg.innerHTML = `
                  <div class="bg-black text-white rounded-lg py-2 px-3 max-w-[85%]">
                      <div class="text-xs text-gray-300 mb-1">Vous</div>
                      <div>${text}</div>
                  </div>
              `
      }
  
      messages.appendChild(msg)
    }
  
    // Génère un ID de session unique
    function generateSessionId() {
      return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, (c) => {
        const r = (Math.random() * 16) | 0
        const v = c === "x" ? r : (r & 0x3) | 0x8
        return v.toString(16)
      })
    }
  
    // Sauvegarde la conversation dans le localStorage
    function saveConversation() {
      localStorage.setItem("immo_chat_history", JSON.stringify(chatHistory))
      localStorage.setItem("immo_session_id", sessionId)
      if (userName) {
        localStorage.setItem("immo_user_name", userName)
      }
    }
  
    // Restaure la conversation depuis le localStorage
    function restoreConversation() {
      const savedHistory = localStorage.getItem("immo_chat_history")
      const savedSessionId = localStorage.getItem("immo_session_id")
      const savedUserName = localStorage.getItem("immo_user_name")
  
      if (savedHistory && savedSessionId) {
        chatHistory = JSON.parse(savedHistory)
        sessionId = savedSessionId
  
        if (savedUserName) {
          userName = savedUserName
        }
  
        // Recréer la conversation dans l'interface
        const messages = document.getElementById("chat-messages")
        messages.innerHTML = ""
  
        chatHistory.forEach((msg) => {
          if (msg.role === "user") {
            appendMessage("Vous", msg.content)
          } else {
            appendMessage("Immo", msg.content)
          }
        })
  
        // Scroll vers le bas après restauration
        scrollToBottom()
      }
    }
  
    // Fonction pour effacer l'historique (utile pour les tests)
    function clearHistory() {
      localStorage.removeItem("immo_chat_history")
      localStorage.removeItem("immo_session_id")
      localStorage.removeItem("immo_user_name")
      chatHistory = []
      userName = ""
      sessionId = generateSessionId()
      document.getElementById("chat-messages").innerHTML = ""
    }
  
    // Initialisation au chargement de la page
    document.addEventListener("DOMContentLoaded", init)
  
    // Exposer certaines fonctions pour les tests
    return {
      clearHistory,
      init,
    }
  }
  
  export default ImmoChat
  