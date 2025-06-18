<div id="loginModal" class="fixed inset-0 z-50 hidden">
    <div class="min-h-screen px-4 text-center">
        <div class="fixed inset-0 bg-black bg-opacity-40 transition-opacity"></div>
        
        <div class="inline-block w-full max-w-md my-8 text-left align-middle transition-all transform bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- En-tête -->
            <div class="border-b p-4">
                <div class="flex items-center justify-between">
                    <button onclick="toggleModal('loginModal')" class="text-2xl">&times;</button>
                    <h2 class="text-lg font-semibold">Connexion ou inscription</h2>
                    <div></div>
                </div>
            </div>

            <!-- Contenu -->
            <div class="p-6">
                <h3 class="text-2xl font-semibold mb-6">Bienvenue sur ImmoChain</h3>
                
                <!-- Sélecteur de pays -->
                <div class="relative mb-4">
                    <select class="w-full p-4 border rounded-t-lg appearance-none">
                        <option value="FR">France (+33)</option>
                        <option value="BE">Belgique (+32)</option>
                        <option value="CH">Suisse (+41)</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2"></i>
                </div>

                <!-- Numéro de téléphone -->
                <input type="tel" placeholder="Numéro de téléphone" 
                       class="w-full p-4 border rounded-b-lg mb-4">

                <p class="text-sm text-gray-500 mb-6">
                    Nous vous appellerons ou vous enverrons un SMS pour confirmer votre numéro. 
                    Les frais standards d'envoi de messages et d'échange de données s'appliquent.
                </p>

                <button class="w-full bg-primary text-white py-3 rounded-lg font-medium hover:bg-primary-dark transition mb-4">
                    Continuer
                </button>

                <div class="relative text-center mb-4">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t"></div>
                    </div>
                    <div class="relative">
                        <span class="px-4 bg-white text-sm text-gray-500">ou</span>
                    </div>
                </div>

                <!-- Boutons de connexion sociale -->
                <div class="space-y-4">
                    <button class="w-full p-3 border rounded-lg flex items-center justify-center gap-2 hover:bg-gray-50 transition">
                        <img src="{{ asset('images/google.svg') }}" alt="Google" class="w-5 h-5">
                        <span>Continuer avec Google</span>
                    </button>

                    <button class="w-full p-3 border rounded-lg flex items-center justify-center gap-2 hover:bg-gray-50 transition">
                        <i class="fab fa-apple text-xl"></i>
                        <span>Continuer avec Apple</span>
                    </button>

                    <button class="w-full p-3 border rounded-lg flex items-center justify-center gap-2 hover:bg-gray-50 transition">
                        <i class="far fa-envelope text-xl"></i>
                        <span>Continuer avec un e-mail</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>