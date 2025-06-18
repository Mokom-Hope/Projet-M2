@extends('layouts.app')

@section('title', 'ImmoChain - Ajouter un bien')

@push('styles')
<link href="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.css" rel="stylesheet">
<link href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.css" rel="stylesheet">
<style>
  #map {
      width: 100%;
      height: 300px;
      border-radius: 0.5rem;
  }
  .image-preview {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
      gap: 10px;
      margin-top: 10px;
  }
  .image-preview-item {
      position: relative;
      height: 150px;
      border-radius: 0.5rem;
      overflow: hidden;
  }
  .image-preview-item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
  }
  .image-preview-item .remove-image {
      position: absolute;
      top: 5px;
      right: 5px;
      background: rgba(0, 0, 0, 0.5);
      color: white;
      border-radius: 50%;
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
  }
  .dropzone {
      border: 2px dashed #ccc;
      border-radius: 0.5rem;
      padding: 2rem;
      text-align: center;
      transition: all 0.3s ease;
  }
  .dropzone.dragover {
      border-color: #000;
      background-color: rgba(0, 0, 0, 0.05);
  }
  .progress-bar {
      height: 4px;
      background-color: #e2e8f0;
      border-radius: 2px;
      margin-top: 1rem;
      overflow: hidden;
  }
  .progress-bar-fill {
      height: 100%;
      background-color: #000;
      width: 0%;
      transition: width 0.3s ease;
  }
  .form-step {
      display: none;
  }
  .form-step.active {
      display: block;
  }
  .step-indicator {
      display: flex;
      justify-content: space-between;
      margin-bottom: 2rem;
  }
  .step {
      flex: 1;
      text-align: center;
      padding: 0.5rem;
      position: relative;
  }
  .step:not(:last-child)::after {
      content: '';
      position: absolute;
      top: 50%;
      right: -50%;
      width: 100%;
      height: 2px;
      background-color: #e2e8f0;
      z-index: 1;
  }
  .step-number {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background-color: #e2e8f0;
      color: #64748b;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 0.5rem;
      position: relative;
      z-index: 2;
  }
  .step.active .step-number {
      background-color: #000;
      color: #fff;
  }
  .step.completed .step-number {
      background-color: #10b981;
      color: #fff;
  }
  .step.completed:not(:last-child)::after {
      background-color: #10b981;
  }
  
  /* Styles pour les onglets de médias */
  .media-tabs {
      display: flex;
      border-bottom: 1px solid #e2e8f0;
      margin-bottom: 1.5rem;
      overflow-x: auto; /* Pour permettre le défilement sur mobile */
  }
  .media-tab {
      padding: 0.75rem 1.5rem;
      font-weight: 500;
      border-bottom: 2px solid transparent;
      cursor: pointer;
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      white-space: nowrap; /* Empêcher le retour à la ligne */
  }
  .media-tab.active {
      border-bottom-color: #000;
      color: #000;
  }
  .media-tab:hover:not(.active) {
      background-color: #f8fafc;
  }
  .media-content {
      display: none;
  }
  .media-content.active {
      display: block;
      animation: fadeIn 0.3s ease;
  }
  
  /* Styles pour la capture de caméra */
  .camera-container {
      position: relative;
      width: 100%;
      border-radius: 0.5rem;
      overflow: hidden;
      background-color: #000;
      margin-bottom: 1rem;
  }
  .camera-preview {
      width: 100%;
      height: 400px;
      object-fit: cover;
      display: block;
  }
  .camera-controls {
      display: flex;
      justify-content: center;
      gap: 1rem;
      margin-top: 1rem;
  }
  .camera-button {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background-color: #fff;
      border: 3px solid #000;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.2s ease;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }
  .camera-button:hover {
      transform: scale(1.05);
  }
  .camera-button.record {
      background-color: #ef4444;
      border-color: #ef4444;
  }
  .camera-button.record.active {
      animation: pulse 1.5s infinite;
  }
  .camera-options {
      position: absolute;
      bottom: 1rem;
      right: 1rem;
      display: flex;
      gap: 0.5rem;
  }
  .camera-option {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: rgba(0, 0, 0, 0.5);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.2s ease;
  }
  .camera-option:hover {
      background-color: rgba(0, 0, 0, 0.7);
  }
  .filter-options {
      display: flex;
      gap: 0.5rem;
      margin-top: 1rem;
      overflow-x: auto;
      padding-bottom: 0.5rem;
  }
  .filter-option {
      width: 60px;
      height: 60px;
      border-radius: 0.5rem;
      overflow: hidden;
      cursor: pointer;
      transition: all 0.2s ease;
      border: 2px solid transparent;
  }
  .filter-option.active {
      border-color: #000;
  }
  .filter-option img {
      width: 100%;
      height: 100%;
      object-fit: cover;
  }
  .filter-name {
      font-size: 0.75rem;
      text-align: center;
      margin-top: 0.25rem;
  }
  
  /* Animations */
  @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
  }
  @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
  }
  
  /* Styles pour les captures */
  .captures-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
      gap: 10px;
      margin-top: 1rem;
  }
  .capture-item {
      position: relative;
      height: 150px;
      border-radius: 0.5rem;
      overflow: hidden;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      transition: all 0.2s ease;
  }
  .capture-item:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }
  .capture-item img, .capture-item video {
      width: 100%;
      height: 100%;
      object-fit: cover;
  }
  .capture-item .remove-capture {
      position: absolute;
      top: 5px;
      right: 5px;
      background: rgba(0, 0, 0, 0.5);
      color: white;
      border-radius: 50%;
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.2s ease;
  }
  .capture-item .remove-capture:hover {
      background: rgba(0, 0, 0, 0.7);
  }
  
  /* Styles pour le compteur d'enregistrement */
  .record-timer {
      position: absolute;
      top: 1rem;
      left: 1rem;
      background-color: rgba(0, 0, 0, 0.5);
      color: white;
      padding: 0.25rem 0.5rem;
      border-radius: 0.25rem;
      font-size: 0.875rem;
      display: none;
  }
  .record-timer.active {
      display: block;
      animation: blink 1s infinite;
  }
  @keyframes blink {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.5; }
  }
  
  /* Style pour les messages d'erreur */
  .error-message {
      background-color: #fee2e2;
      border: 1px solid #ef4444;
      color: #b91c1c;
      padding: 0.75rem;
      border-radius: 0.5rem;
      margin-bottom: 1rem;
  }

  /* Styles pour les permissions de caméra */
  .camera-permission-error {
      display: none;
      background-color: #fee2e2;
      border: 1px solid #ef4444;
      color: #b91c1c;
      padding: 1rem;
      border-radius: 0.5rem;
      margin-bottom: 1rem;
      text-align: center;
  }

  .camera-permission-error.active {
      display: block;
  }

  /* Styles pour le bouton d'ajout d'images */
  .image-upload-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      width: 100%;
      padding: 0.75rem;
      background-color: #f3f4f6;
      border: 1px dashed #d1d5db;
      border-radius: 0.5rem;
      cursor: pointer;
      transition: all 0.2s ease;
      margin-top: 1rem;
  }

  .image-upload-btn:hover {
      background-color: #e5e7eb;
  }

  /* Styles pour le compteur d'images */
  .image-counter {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: 0.5rem;
      font-size: 0.875rem;
      color: #6b7280;
  }

  .image-counter.warning {
      color: #ef4444;
  }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
  <div class="max-w-3xl mx-auto">
      <h1 class="text-2xl font-bold mb-6">Ajouter un bien immobilier</h1>
      
      <!-- Message d'erreur -->
      <div id="error-container" class="error-message hidden"></div>
      
      <!-- Indicateur d'étapes -->
      <div class="step-indicator mb-8">
          <div class="step active" data-step="1">
              <div class="step-number">1</div>
              <div class="step-title">Informations</div>
          </div>
          <div class="step" data-step="2">
              <div class="step-number">2</div>
              <div class="step-title">Localisation</div>
          </div>
          <div class="step" data-step="3">
              <div class="step-number">3</div>
              <div class="step-title">Médias</div>
          </div>
          <div class="step" data-step="4">
              <div class="step-number">4</div>
              <div class="step-title">Finalisation</div>
          </div>
      </div>
      
      <form id="property-form" class="space-y-6">
          @csrf
          
          <!-- Étape 1: Informations générales -->
          <div class="form-step active" data-step="1">
              <h2 class="text-xl font-semibold mb-4">Informations générales</h2>
              
              <!-- Type de bien et transaction -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                  <div>
                      <label for="type" class="block mb-1 font-medium">Type de bien</label>
                      <select id="type" name="type" class="w-full border rounded-lg px-3 py-2">
                          <option value="Maison">Maison</option>
                          <option value="Terrain">Terrain</option>
                          <option value="LocalCommercial">Local commercial</option>
                          <option value="Studio">Studio</option>
                          <option value="Chambre">Chambre</option>
                          <option value="Meublé">Meublé</option>
                          <option value="Hotel">Hotel</option>
                      </select>
                  </div>
                  <div>
                      <label for="transaction_type" class="block mb-1 font-medium">Type de transaction</label>
                      <select id="transaction_type" name="transaction_type" class="w-full border rounded-lg px-3 py-2">
                          <option value="vente">Vente</option>
                          <option value="location">Location</option>
                      </select>
                  </div>
              </div>
              
              <!-- Titre et description -->
              <div class="mb-4">
                  <label for="titre" class="block mb-1 font-medium">Titre de l'annonce</label>
                  <input type="text" id="titre" name="titre" class="w-full border rounded-lg px-3 py-2" required>
                  <p class="text-sm text-gray-500 mt-1">Un titre accrocheur pour attirer l'attention (max 100 caractères)</p>
              </div>
              
              <div class="mb-4">
                  <label for="description" class="block mb-1 font-medium">Description</label>
                  <textarea id="description" name="description" rows="4" class="w-full border rounded-lg px-3 py-2" required></textarea>
                  <p class="text-sm text-gray-500 mt-1">Décrivez votre bien en détail (caractéristiques, état, environnement...)</p>
              </div>
              
              <!-- Prix et superficie -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                  <div>
                      <label for="prix" class="block mb-1 font-medium">Prix (FCFA)</label>
                      <input type="number" id="prix" name="prix" class="w-full border rounded-lg px-3 py-2" required>
                  </div>
                  <div>
                      <label for="superficie" class="block mb-1 font-medium">Superficie (m²)</label>
                      <input type="number" id="superficie" name="superficie" class="w-full border rounded-lg px-3 py-2" required>
                  </div>
              </div>
              
              <!-- Caractéristiques supplémentaires -->
              <div id="additional-features" class="mb-4">
                  <label class="block mb-1 font-medium">Caractéristiques supplémentaires</label>
                  
                  <div class="grid grid-cols-2 gap-2">
                      <label class="flex items-center">
                          <input type="checkbox" name="features[]" value="parking" class="mr-2">
                          <span>Parking</span>
                      </label>
                      <label class="flex items-center">
                          <input type="checkbox" name="features[]" value="balcony" class="mr-2">
                          <span>Balcon</span>
                      </label>
                      <label class="flex items-center">
                          <input type="checkbox" name="features[]" value="garden" class="mr-2">
                          <span>Jardin</span>
                      </label>
                      <label class="flex items-center">
                          <input type="checkbox" name="features[]" value="pool" class="mr-2">
                          <span>Piscine</span>
                      </label>
                      <label class="flex items-center">
                          <input type="checkbox" name="features[]" value="security" class="mr-2">
                          <span>Sécurité</span>
                      </label>
                      <label class="flex items-center">
                          <input type="checkbox" name="features[]" value="furnished" class="mr-2">
                          <span>Meublé</span>
                      </label>
                  </div>
              </div>
              
              <!-- Boutons de navigation -->
              <div class="flex justify-end mt-6">
                  <button type="button" class="next-step px-6 py-2 bg-black text-white rounded-lg">Continuer</button>
              </div>
          </div>
          
          <!-- Étape 2: Localisation -->
          <div class="form-step" data-step="2">
              <h2 class="text-xl font-semibold mb-4">Localisation</h2>
              
              <!-- Adresse et localisation -->
              <div class="mb-4">
                  <label for="adresse" class="block mb-1 font-medium">Adresse</label>
                  <input type="text" id="adresse" name="adresse" class="w-full border rounded-lg px-3 py-2" required>
              </div>
              
              <div class="mb-4">
                  <label class="block mb-1 font-medium">Localisation sur la carte</label>
                  <div class="bg-blue-50 p-3 rounded-lg mb-3 flex items-start">
                      <i class="fas fa-info-circle text-blue-500 mt-1 mr-2"></i>
                      <p class="text-sm text-blue-700">Votre position actuelle est utilisée par défaut, car nous supposons que vous êtes sur les lieux du bien. Vous pouvez ajuster la position si nécessaire.</p>
                  </div>
                  <p class="text-sm text-gray-500 mb-2">Déplacez le marqueur pour définir l'emplacement précis de votre bien</p>
                  <div id="map"></div>
                  <input type="hidden" id="latitude" name="latitude">
                  <input type="hidden" id="longitude" name="longitude">
              </div>
              
              <!-- Boutons de navigation -->
              <div class="flex justify-between mt-6">
                  <button type="button" class="prev-step px-6 py-2 bg-gray-200 rounded-lg">Retour</button>
                  <button type="button" class="next-step px-6 py-2 bg-black text-white rounded-lg">Continuer</button>
              </div>
          </div>
          
          <!-- Étape 3: Médias -->
          <div class="form-step" data-step="3">
              <h2 class="text-xl font-semibold mb-4">Photos et vidéos</h2>
              
              <!-- Onglets pour les différentes options de médias -->
              <div class="media-tabs">
                  <div class="media-tab active" data-tab="upload">
                      <i class="fas fa-upload"></i>
                      <span>Importer</span>
                  </div>
                  <div class="media-tab" data-tab="camera">
                      <i class="fas fa-camera"></i>
                      <span>Prendre des photos</span>
                  </div>
                  <div class="media-tab" data-tab="video">
                      <i class="fas fa-video"></i>
                      <span>Enregistrer une vidéo</span>
                  </div>
              </div>
              
              <!-- Contenu des onglets -->
              <div class="media-content active" data-tab="upload">
                  <!-- Upload d'images -->
                  <div class="mb-6">
                      <label for="images" class="block mb-1 font-medium">Images (5 minimum)</label>
                      
                      <!-- Compteur d'images -->
                      <div id="image-counter" class="image-counter">
                          <span>0 image(s) sélectionnée(s)</span>
                          <span>Min: 5 / Max: 11</span>
                      </div>
                      
                      <!-- Zone de dépôt pour ordinateur -->
                      <div id="dropzone" class="dropzone hidden md:block">
                          <p class="text-gray-600 mb-2">Glissez vos images ici ou</p>
                          <label class="bg-black text-white px-4 py-2 rounded-lg cursor-pointer inline-block">
                              <span>Parcourir</span>
                              <input type="file" id="images" name="images[]" accept="image/*" multiple class="hidden">
                          </label>
                          <p class="text-sm text-gray-500 mt-2">Formats acceptés: JPG, PNG, GIF (max 5MB par image)</p>
                          <div class="progress-bar mt-4 hidden">
                              <div class="progress-bar-fill"></div>
                          </div>
                      </div>
                      
                      <!-- Bouton d'upload pour mobile -->
                      <div class="md:hidden">
                          <label for="mobile-images" class="bg-black text-white px-4 py-2 rounded-lg cursor-pointer inline-block w-full text-center mb-2">
                              <i class="fas fa-camera mr-2"></i>
                              <span>Prendre une photo</span>
                              <input type="file" id="mobile-images" accept="image/*" capture="environment" class="hidden">
                          </label>
                          
                          <label for="mobile-gallery" class="bg-gray-200 text-black px-4 py-2 rounded-lg cursor-pointer inline-block w-full text-center">
                              <i class="fas fa-images mr-2"></i>
                              <span>Choisir depuis la galerie</span>
                              <input type="file" id="mobile-gallery" accept="image/*" multiple class="hidden">
                          </label>
                          
                          <div id="mobile-upload-buttons" class="mt-4 grid grid-cols-2 gap-2">
                              <!-- Boutons d'ajout d'images supplémentaires -->
                          </div>
                      </div>
                      
                      <div class="image-preview mt-4" id="image-preview"></div>
                  </div>
                  
                  <!-- Upload de vidéo -->
                  <div class="mb-4">
                      <label for="video" class="block mb-1 font-medium">Vidéo (optionnel)</label>
                      
                      <!-- Bouton d'upload vidéo pour ordinateur -->
                      <div class="hidden md:block">
                          <input type="file" id="video" name="video" accept="video/*" class="w-full border rounded-lg px-3 py-2">
                      </div>
                      
                      <!-- Boutons d'upload vidéo pour mobile -->
                      <div class="md:hidden">
                          <label for="mobile-video-camera" class="bg-black text-white px-4 py-2 rounded-lg cursor-pointer inline-block w-full text-center mb-2">
                              <i class="fas fa-video mr-2"></i>
                              <span>Enregistrer une vidéo</span>
                              <input type="file" id="mobile-video-camera" accept="video/*" capture="environment" class="hidden">
                          </label>
                          
                          <label for="mobile-video-gallery" class="bg-gray-200 text-black px-4 py-2 rounded-lg cursor-pointer inline-block w-full text-center">
                              <i class="fas fa-film mr-2"></i>
                              <span>Choisir depuis la galerie</span>
                              <input type="file" id="mobile-video-gallery" accept="video/*" class="hidden">
                          </label>
                      </div>
                      
                      <p class="text-sm text-gray-500 mt-1">Ajoutez une vidéo pour montrer votre bien (max 50MB)</p>
                      
                      <!-- Prévisualisation de la vidéo -->
                      <div id="video-preview-container" class="mt-4 hidden">
                          <video id="video-preview-player" controls class="w-full rounded-lg"></video>
                          <button type="button" id="remove-video-btn" class="mt-2 text-red-600 text-sm">
                              <i class="fas fa-trash mr-1"></i> Supprimer la vidéo
                          </button>
                      </div>
                  </div>
              </div>
              
              <div class="media-content" data-tab="camera">
                  <!-- Message d'erreur pour les permissions de caméra -->
                  <div id="camera-permission-error" class="camera-permission-error">
                      <p><i class="fas fa-exclamation-triangle mr-2"></i> Impossible d'accéder à la caméra</p>
                      <p class="text-sm mt-2">Veuillez vérifier que vous avez accordé les permissions d'accès à la caméra dans les paramètres de votre navigateur.</p>
                      <button type="button" id="retry-camera-btn" class="mt-3 px-4 py-2 bg-black text-white rounded-lg">
                          Réessayer
                      </button>
                  </div>
                  
                  <!-- Capture de photos avec la caméra -->
                  <div class="camera-container">
                      <video id="camera-preview" class="camera-preview" autoplay playsinline muted></video>
                      <div class="record-timer" id="photo-counter">3</div>
                      <div class="camera-options">
                          <div class="camera-option" id="toggle-flash" title="Flash">
                              <i class="fas fa-bolt"></i>
                          </div>
                          <div class="camera-option" id="toggle-grid" title="Grille">
                              <i class="fas fa-th"></i>
                          </div>
                          <div class="camera-option" id="switch-camera" title="Changer de caméra">
                              <i class="fas fa-sync"></i>
                          </div>
                      </div>
                  </div>
                  
                  <div class="filter-options">
                      <div class="filter-option active" data-filter="normal">
                          <img src="/placeholder.svg?height=60&width=60" alt="Normal">
                          <div class="filter-name">Normal</div>
                      </div>
                      <div class="filter-option" data-filter="grayscale">
                          <img src="/placeholder.svg?height=60&width=60" alt="Noir & Blanc">
                          <div class="filter-name">N&B</div>
                      </div>
                      <div class="filter-option" data-filter="sepia">
                          <img src="/placeholder.svg?height=60&width=60" alt="Sépia">
                          <div class="filter-name">Sépia</div>
                      </div>
                      <div class="filter-option" data-filter="vintage">
                          <img src="/placeholder.svg?height=60&width=60" alt="Vintage">
                          <div class="filter-name">Vintage</div>
                      </div>
                      <div class="filter-option" data-filter="warm">
                          <img src="/placeholder.svg?height=60&width=60" alt="Chaud">
                          <div class="filter-name">Chaud</div>
                      </div>
                      <div class="filter-option" data-filter="cool">
                          <img src="/placeholder.svg?height=60&width=60" alt="Froid">
                          <div class="filter-name">Froid</div>
                      </div>
                  </div>
                  
                  <div class="camera-controls">
                      <div class="camera-button" id="capture-photo">
                          <i class="fas fa-camera"></i>
                        id="capture-photo">
                          <i class="fas fa-camera"></i>
                      </div>
                  </div>
                  
                  <div class="mt-4">
                      <h3 class="font-medium mb-2">Photos capturées</h3>
                      <div class="captures-grid" id="photo-captures"></div>
                  </div>
              </div>
              
              <div class="media-content" data-tab="video">
                  <!-- Message d'erreur pour les permissions de caméra vidéo -->
                  <div id="video-permission-error" class="camera-permission-error">
                      <p><i class="fas fa-exclamation-triangle mr-2"></i> Impossible d'accéder à la caméra</p>
                      <p class="text-sm mt-2">Veuillez vérifier que vous avez accordé les permissions d'accès à la caméra dans les paramètres de votre navigateur.</p>
                      <button type="button" id="retry-video-btn" class="mt-3 px-4 py-2 bg-black text-white rounded-lg">
                          Réessayer
                      </button>
                  </div>
                  
                  <!-- Enregistrement vidéo avec la caméra -->
                  <div class="camera-container">
                      <video id="video-preview" class="camera-preview" autoplay playsinline muted></video>
                      <div class="record-timer" id="video-timer">00:00</div>
                  </div>
                  
                  <div class="camera-controls">
                      <div class="camera-button" id="record-video">
                          <i class="fas fa-video"></i>
                      </div>
                      <div class="camera-button" id="stop-video" style="display: none;">
                          <i class="fas fa-stop"></i>
                      </div>
                  </div>
                  
                  <div class="mt-4">
                      <h3 class="font-medium mb-2">Vidéo enregistrée</h3>
                      <div class="captures-grid" id="video-captures"></div>
                  </div>
              </div>
              
              <!-- Boutons de navigation -->
              <div class="flex justify-between mt-6">
                  <button type="button" class="prev-step px-6 py-2 bg-gray-200 rounded-lg">Retour</button>
                  <button type="button" class="next-step px-6 py-2 bg-black text-white rounded-lg">Continuer</button>
              </div>
          </div>
          
          <!-- Étape 4: Finalisation -->
          <div class="form-step" data-step="4">
              <h2 class="text-xl font-semibold mb-4">Finalisation</h2>
              
              <div class="bg-gray-50 p-4 rounded-lg mb-6">
                  <h3 class="font-medium mb-2">Récapitulatif</h3>
                  <div class="grid grid-cols-2 gap-4">
                      <div>
                          <p class="text-sm text-gray-500">Type de bien</p>
                          <p id="recap-type" class="font-medium"></p>
                      </div>
                      <div>
                          <p class="text-sm text-gray-500">Type de transaction</p>
                          <p id="recap-transaction" class="font-medium"></p>
                      </div>
                      <div>
                          <p class="text-sm text-gray-500">Prix</p>
                          <p id="recap-price" class="font-medium"></p>
                      </div>
                      <div>
                          <p class="text-sm text-gray-500">Superficie</p>
                          <p id="recap-area" class="font-medium"></p>
                      </div>
                      <div class="col-span-2">
                          <p class="text-sm text-gray-500">Adresse</p>
                          <p id="recap-address" class="font-medium"></p>
                      </div>
                      <div class="col-span-2">
                          <p class="text-sm text-gray-500">Images</p>
                          <p id="recap-images" class="font-medium"></p>
                      </div>
                  </div>
              </div>
              
              <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200 mb-6">
                  <div class="flex items-start">
                      <i class="fas fa-info-circle text-yellow-500 mt-1 mr-3"></i>
                      <div>
                          <p class="font-medium text-yellow-800">Important</p>
                          <p class="text-sm text-yellow-700">En publiant cette annonce, vous confirmez être le propriétaire légitime de ce bien et que toutes les informations fournies sont exactes.</p>
                      </div>
                  </div>
              </div>
              
              <!-- Boutons de navigation -->
              <div class="flex justify-between mt-6">
                  <button type="button" class="prev-step px-6 py-2 bg-gray-200 rounded-lg">Retour</button>
                  <button type="submit" class="px-6 py-2 bg-black text-white rounded-lg">Publier l'annonce</button>
              </div>
          </div>
      </form>
  </div>
</div>

<!-- Modal de succès -->
<div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex justify-center items-center">
  <div class="bg-white rounded-lg max-w-md w-full p-6 text-center">
      <div class="mb-4">
          <i class="fas fa-check-circle text-green-500 text-5xl"></i>
      </div>
      <h2 class="text-2xl font-bold mb-2">Bien ajouté avec succès !</h2>
      <p class="text-gray-600 mb-6">Votre bien immobilier a été publié et est maintenant visible par les utilisateurs.</p>
      <div class="flex flex-col gap-2">
          <a href="" id="view-property-link" class="bg-black text-white px-6 py-3 rounded-lg font-medium hover:bg-gray-800 transition">
              Voir mon bien
          </a>
          <a href="{{ route('dashboard.properties') }}" class="px-6 py-3 bg-gray-100 rounded-lg font-medium hover:bg-gray-200 transition">
              Gérer mes biens
          </a>
      </div>
  </div>
</div>

<!-- Canvas pour appliquer les filtres -->
<canvas id="filter-canvas" style="display: none;"></canvas>
@endsection

@push('scripts')
<script src="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.js"></script>
<script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.min.js"></script>
<script>
  let map;
  let marker;
  let currentStep = 1;
  let uploadedImages = [];
  let capturedPhotos = [];
  let capturedVideo = null;
  let mediaRecorder = null;
  let recordedChunks = [];
  let currentFilter = 'normal';
  let videoStream = null;
  let recordingTimer = null;
  let recordingSeconds = 0;
  let facingMode = 'environment'; // Utiliser la caméra arrière par défaut
  let flashEnabled = false;
  let isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
  
  document.addEventListener('DOMContentLoaded', function() {
      // Initialiser la carte
      mapboxgl.accessToken = 'pk.eyJ1IjoiYXJ0aHVyLWNhZHJlbjIzNyIsImEiOiJjbTM1cjE0cGUwNW41Mmlvam56ZjRtdXQzIn0.fWB_Y31S2A3WeZvXoxjDxQ';
      
      map = new mapboxgl.Map({
          container: 'map',
          style: 'mapbox://styles/mapbox/streets-v11',
          center: [11.5174, 3.8721], // Coordonnées de Yaoundé
          zoom: 12
      });
      
      // Ajouter un marqueur déplaçable
      marker = new mapboxgl.Marker({
          draggable: true
      })
      .setLngLat([11.5174, 3.8721])
      .addTo(map);

      // Utiliser la géolocalisation pour obtenir la position actuelle
      if (navigator.geolocation) {
          // Afficher un message de chargement
          document.getElementById('adresse').placeholder = "Récupération de votre position en cours...";
          
          navigator.geolocation.getCurrentPosition(
              function(position) {
                  const userLocation = [position.coords.longitude, position.coords.latitude];
                  map.flyTo({
                      center: userLocation,
                      zoom: 15
                  });
                  marker.setLngLat(userLocation);
                  updateCoordinates();
                  
                  // Essayer d'obtenir l'adresse à partir des coordonnées (géocodage inverse)
                  fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${userLocation[0]},${userLocation[1]}.json?access_token=${mapboxgl.accessToken}`)
                      .then(response => response.json())
                      .then(data => {
                          if (data.features && data.features.length > 0) {
                              const address = data.features[0].place_name;
                              document.getElementById('adresse').value = address;
                              // Ajouter une notification visuelle que l'adresse a été remplie automatiquement
                              const addressField = document.getElementById('adresse');
                              addressField.style.backgroundColor = "#e6f7ff";
                              setTimeout(() => {
                                  addressField.style.backgroundColor = "";
                              }, 2000);
                          } else {
                              document.getElementById('adresse').placeholder = "Entrez l'adresse du bien";
                          }
                      })
                      .catch(error => {
                          console.error('Erreur lors du géocodage inverse:', error);
                          document.getElementById('adresse').placeholder = "Entrez l'adresse du bien";
                      });
              },
              function(error) {
                  console.error('Erreur de géolocalisation:', error);
                  document.getElementById('adresse').placeholder = "Entrez l'adresse du bien";
              }
          );
      } else {
          document.getElementById('adresse').placeholder = "Entrez l'adresse du bien";
      }

      // Ajouter un message plus clair sous le champ d'adresse
      const addressField = document.querySelector('.mb-4:has(#adresse)');
      const addressHelp = document.createElement('p');
      addressHelp.className = 'text-sm text-gray-500 mt-1';
      addressHelp.innerHTML = 'L\'adresse est automatiquement remplie selon votre position actuelle. Vous pouvez la modifier si nécessaire.';
      addressField.appendChild(addressHelp);
      
      // Mettre à jour les coordonnées quand le marqueur est déplacé
      marker.on('dragend', updateCoordinates);
      
      // Ajouter le geocoder pour la recherche d'adresses
      const geocoder = new MapboxGeocoder({
          accessToken: mapboxgl.accessToken,
          mapboxgl: mapboxgl,
          marker: false,
          placeholder: 'Rechercher une adresse'
      });
      
      map.addControl(geocoder);
      
      // Quand une adresse est sélectionnée, mettre à jour le marqueur
      geocoder.on('result', function(e) {
          const coordinates = e.result.center;
          marker.setLngLat(coordinates);
          updateCoordinates();
          
          // Mettre à jour le champ d'adresse
          document.getElementById('adresse').value = e.result.place_name;
      });
      
      // Initialiser les coordonnées
      updateCoordinates();
      
      // Gestion de la prévisualisation des images
      const dropzone = document.getElementById('dropzone');
      const imagesInput = document.getElementById('images');
      
      // Événements pour le drag & drop
      ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
          dropzone.addEventListener(eventName, preventDefaults, false);
      });
      
      function preventDefaults(e) {
          e.preventDefault();
          e.stopPropagation();
      }
      
      ['dragenter', 'dragover'].forEach(eventName => {
          dropzone.addEventListener(eventName, highlight, false);
      });
      
      ['dragleave', 'drop'].forEach(eventName => {
          dropzone.addEventListener(eventName, unhighlight, false);
      });
      
      function highlight() {
          dropzone.classList.add('dragover');
      }
      
      function unhighlight() {
          dropzone.classList.remove('dragover');
      }
      
      // Gérer le drop
      dropzone.addEventListener('drop', handleDrop, false);
      
      function handleDrop(e) {
          const dt = e.dataTransfer;
          const files = dt.files;
          
          handleFiles(files);
      }
      
      // Gestion des fichiers pour desktop
      imagesInput.addEventListener('change', function(e) {
          handleFiles(e.target.files);
      });
      
      // Gestion des fichiers pour mobile
      document.getElementById('mobile-images').addEventListener('change', function(e) {
          handleFiles(e.target.files);
      });
      
      document.getElementById('mobile-gallery').addEventListener('change', function(e) {
          handleFiles(e.target.files);
      });
      
      // Gestion des vidéos pour mobile
      document.getElementById('mobile-video-camera').addEventListener('change', function(e) {
          handleVideoFile(e.target.files[0]);
      });
      
      document.getElementById('mobile-video-gallery').addEventListener('change', function(e) {
          handleVideoFile(e.target.files[0]);
      });
      
      // Gestion des vidéos pour desktop
      document.getElementById('video').addEventListener('change', function(e) {
          handleVideoFile(e.target.files[0]);
      });
      
      // Bouton pour supprimer la vidéo
      document.getElementById('remove-video-btn').addEventListener('click', function() {
          document.getElementById('video-preview-container').classList.add('hidden');
          document.getElementById('video-preview-player').src = '';
          capturedVideo = null;
          
          // Réinitialiser les inputs de vidéo
          document.getElementById('video').value = '';
          document.getElementById('mobile-video-camera').value = '';
          document.getElementById('mobile-video-gallery').value = '';
      });
      
      // Navigation entre les étapes
      document.querySelectorAll('.next-step').forEach(button => {
          button.addEventListener('click', nextStep);
      });
      
      document.querySelectorAll('.prev-step').forEach(button => {
          button.addEventListener('click', prevStep);
      });
      
      // Soumission du formulaire
      document.getElementById('property-form').addEventListener('submit', submitPropertyForm);
      
      // Gestion des onglets de médias
      document.querySelectorAll('.media-tab').forEach(tab => {
          tab.addEventListener('click', function() {
              const tabId = this.dataset.tab;
              
              // Désactiver tous les onglets et contenus
              document.querySelectorAll('.media-tab').forEach(t => t.classList.remove('active'));
              document.querySelectorAll('.media-content').forEach(c => c.classList.remove('active'));
              
              // Activer l'onglet et le contenu sélectionnés
              this.classList.add('active');
              document.querySelector(`.media-content[data-tab="${tabId}"]`).classList.add('active');
              
              // Initialiser la caméra si nécessaire
              if (tabId === 'camera') {
                  initCamera('camera-preview');
              } else if (tabId === 'video') {
                  initCamera('video-preview', true);
              } else {
                  // Arrêter la caméra si on change d'onglet
                  stopCamera();
              }
          });
      });
      
      // Capture de photo
      document.getElementById('capture-photo').addEventListener('click', capturePhoto);
      
      // Enregistrement vidéo
      document.getElementById('record-video').addEventListener('click', startRecording);
      document.getElementById('stop-video').addEventListener('click', stopRecording);
      
      // Changement de filtre
      document.querySelectorAll('.filter-option').forEach(option => {
          option.addEventListener('click', function() {
              document.querySelectorAll('.filter-option').forEach(o => o.classList.remove('active'));
              this.classList.add('active');
              currentFilter = this.dataset.filter;
              
              // Appliquer le filtre à la prévisualisation
              applyFilterToVideo(currentFilter);
          });
      });
      
      // Options de caméra
      document.getElementById('toggle-flash').addEventListener('click', toggleFlash);
      document.getElementById('toggle-grid').addEventListener('click', toggleGrid);
      document.getElementById('switch-camera').addEventListener('click', switchCamera);
      
      // Boutons de réessai pour les permissions de caméra
      document.getElementById('retry-camera-btn').addEventListener('click', function() {
          document.getElementById('camera-permission-error').classList.remove('active');
          initCamera('camera-preview');
      });
      
      document.getElementById('retry-video-btn').addEventListener('click', function() {
          document.getElementById('video-permission-error').classList.remove('active');
          initCamera('video-preview', true);
      });
  });
  
  // Fonction pour gérer les fichiers d'images
  function handleFiles(files) {
      if (!files || files.length === 0) return;
      
      const preview = document.getElementById('image-preview');
      
      // Vérifier si l'ajout dépasserait la limite
      if (uploadedImages.length + capturedPhotos.length + files.length > 11) {
          alert(`Vous ne pouvez pas ajouter plus de 11 images. Vous avez déjà ${uploadedImages.length + capturedPhotos.length} image(s).`);
          return;
      }
      
      // Ajouter chaque image à la prévisualisation
      for (let i = 0; i < files.length; i++) {
          const file = files[i];
          
          // Vérifier si c'est une image
          if (!file.type.startsWith('image/')) continue;
          
          const reader = new FileReader();
          
          reader.onload = function(e) {
              // Créer un élément de prévisualisation
              const div = document.createElement('div');
              div.className = 'image-preview-item';
              
              const img = document.createElement('img');
              img.src = e.target.result;
              
              const removeButton = document.createElement('div');
              removeButton.className = 'remove-image';
              removeButton.innerHTML = '<i class="fas fa-times"></i>';
              removeButton.dataset.index = uploadedImages.length;
              removeButton.addEventListener('click', function() {
                  removeUploadedImage(parseInt(this.dataset.index));
              });
              
              div.appendChild(img);
              div.appendChild(removeButton);
              preview.appendChild(div);
              
              // Ajouter l'image à la liste
              uploadedImages.push(file);
              
              // Mettre à jour le compteur d'images
              updateImageCounter();
          };
          
          reader.readAsDataURL(file);
      }
  }
  
  // Fonction pour gérer les fichiers vidéo
  function handleVideoFile(file) {
      if (!file) return;
      
      // Vérifier si c'est une vidéo
      if (!file.type.startsWith('video/')) {
          alert('Veuillez sélectionner un fichier vidéo valide.');
          return;
      }
      
      // Vérifier la taille (max 50MB)
      if (file.size > 50 * 1024 * 1024) {
          alert('La vidéo est trop volumineuse. La taille maximale est de 50MB.');
          return;
      }
      
      // Créer une URL pour la prévisualisation
      const videoURL = URL.createObjectURL(file);
      const videoPlayer = document.getElementById('video-preview-player');
      videoPlayer.src = videoURL;
      
      // Afficher la prévisualisation
      document.getElementById('video-preview-container').classList.remove('hidden');
      
      // Stocker la vidéo
      capturedVideo = file;
  }
  
  // Fonction pour supprimer une image téléchargée
  function removeUploadedImage(index) {
      // Supprimer l'image de la liste
      uploadedImages.splice(index, 1);
      
      // Reconstruire la prévisualisation
      const preview = document.getElementById('image-preview');
      preview.innerHTML = '';
      
      uploadedImages.forEach((file, i) => {
          const reader = new FileReader();
          
          reader.onload = function(e) {
              const div = document.createElement('div');
              div.className = 'image-preview-item';
              
              const img = document.createElement('img');
              img.src = e.target.result;
              
              const removeButton = document.createElement('div');
              removeButton.className = 'remove-image';
              removeButton.innerHTML = '<i class="fas fa-times"></i>';
              removeButton.dataset.index = i;
              removeButton.addEventListener('click', function() {
                  removeUploadedImage(parseInt(this.dataset.index));
              });
              
              div.appendChild(img);
              div.appendChild(removeButton);
              preview.appendChild(div);
          };
          
          reader.readAsDataURL(file);
      });
      
      // Mettre à jour le compteur d'images
      updateImageCounter();
  }
  
  // Fonction pour mettre à jour le compteur d'images
  function updateImageCounter() {
      const totalCount = uploadedImages.length + capturedPhotos.length;
      const counter = document.getElementById('image-counter');
      
      counter.innerHTML = `
          <span>${totalCount} image(s) sélectionnée(s)</span>
          <span>Min: 5 / Max: 11</span>
      `;
      
      // Ajouter une classe d'avertissement si le nombre est insuffisant ou trop élevé
      if (totalCount < 5 || totalCount > 11) {
          counter.classList.add('warning');
      } else {
          counter.classList.remove('warning');
      }
      
      // Mettre à jour le récapitulatif
      document.getElementById('recap-images').textContent = `${totalCount} image(s) sélectionnée(s)`;
  }
  
  // Fonction pour mettre à jour les coordonnées
  function updateCoordinates() {
      const lngLat = marker.getLngLat();
      document.getElementById('latitude').value = lngLat.lat;
      document.getElementById('longitude').value = lngLat.lng;
  }
  
  // Fonction pour passer à l'étape suivante
  function nextStep() {
      // Valider l'étape actuelle
      if (!validateStep(currentStep)) {
          return;
      }
      
      // Mettre à jour le récapitulatif si on passe à l'étape 4
      if (currentStep === 3) {
          updateRecap();
      }
      
      // Masquer l'étape actuelle
      document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.remove('active');
      
      // Marquer l'étape comme complétée
      document.querySelector(`.step[data-step="${currentStep}"]`).classList.add('completed');
      document.querySelector(`.step[data-step="${currentStep}"]`).classList.remove('active');
      
      // Passer à l'étape suivante
      currentStep++;
      
      // Afficher la nouvelle étape
      document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.add('active');
      document.querySelector(`.step[data-step="${currentStep}"]`).classList.add('active');
      
      // Arrêter la caméra si on quitte l'étape 3
      if (currentStep !== 3) {
          stopCamera();
      }
  }
  
  // Fonction pour revenir à l'étape précédente
  function prevStep() {
      // Masquer l'étape actuelle
      document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.remove('active');
      document.querySelector(`.step[data-step="${currentStep}"]`).classList.remove('active');
      
      // Revenir à l'étape précédente
      currentStep--;
      
      // Afficher la nouvelle étape
      document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.add('active');
      document.querySelector(`.step[data-step="${currentStep}"]`).classList.add('active');
  }
  
  // Fonction pour valider une étape
  function validateStep(step) {
      switch (step) {
          case 1:
              // Valider les informations générales
              const titre = document.getElementById('titre').value;
              const description = document.getElementById('description').value;
              const prix = document.getElementById('prix').value;
              const superficie = document.getElementById('superficie').value;
              
              if (!titre || !description || !prix || !superficie) {
                  alert('Veuillez remplir tous les champs obligatoires');
                  return false;
              }
              return true;
              
          case 2:
              // Valider la localisation
              const adresse = document.getElementById('adresse').value;
              const latitude = document.getElementById('latitude').value;
              const longitude = document.getElementById('longitude').value;
              
              if (!adresse || !latitude || !longitude) {
                  alert('Veuillez indiquer l\'adresse et la position sur la carte');
                  return false;
              }
              return true;
              
          case 3:
              // Valider les médias
              const totalImages = uploadedImages.length + capturedPhotos.length;
              
              if (totalImages < 5) {
                  alert('Veuillez ajouter au moins 5 images (téléchargées ou capturées)');
                  return false;
              }
              
              if (totalImages > 11) {
                  alert('Vous ne pouvez pas ajouter plus de 11 images. Veuillez en supprimer quelques-unes.');
                  return false;
              }
              
              return true;
              
          default:
              return true;
      }
  }
  
  // Fonction pour mettre à jour le récapitulatif
  function updateRecap() {
      const type = document.getElementById('type');
      const transaction = document.getElementById('transaction_type');
      const prix = document.getElementById('prix').value;
      const superficie = document.getElementById('superficie').value;
      const adresse = document.getElementById('adresse').value;
      const totalImages = uploadedImages.length + capturedPhotos.length;
      
      document.getElementById('recap-type').textContent = type.options[type.selectedIndex].text;
      document.getElementById('recap-transaction').textContent = transaction.options[transaction.selectedIndex].text;
      document.getElementById('recap-price').textContent = `${parseInt(prix).toLocaleString()} FCFA`;
      document.getElementById('recap-area').textContent = `${superficie} m²`;
      document.getElementById('recap-address').textContent = adresse;
      document.getElementById('recap-images').textContent = `${totalImages} image(s) sélectionnée(s)`;
  }
  
  // Fonction pour initialiser la caméra
  function initCamera(videoElementId, withAudio = false) {
      const videoElement = document.getElementById(videoElementId);
      const isVideoTab = videoElementId === 'video-preview';
      
      // Masquer les messages d'erreur
      document.getElementById('camera-permission-error').classList.remove('active');
      document.getElementById('video-permission-error').classList.remove('active');
      
      // Arrêter tout flux existant
      if (videoStream) {
          videoStream.getTracks().forEach(track => track.stop());
          videoStream = null;
      }
      
      // Contraintes pour la caméra
      const constraints = {
          video: {
              width: { ideal: 1280 },
              height: { ideal: 720 },
              facingMode: facingMode
          },
          audio: withAudio // Audio uniquement pour l'enregistrement vidéo
      };
      
      // Demander l'accès à la caméra
      navigator.mediaDevices.getUserMedia(constraints)
          .then(stream => {
              videoStream = stream;
              videoElement.srcObject = stream;
              
              // Jouer la vidéo (nécessaire sur certains navigateurs mobiles)
              videoElement.play().catch(error => {
                  console.error('Erreur lors de la lecture de la vidéo:', error);
              });
              
              // Appliquer le filtre actuel
              if (videoElementId === 'camera-preview') {
                  applyFilterToVideo(currentFilter);
              }
              
              // Préparer l'enregistreur pour la vidéo
              if (isVideoTab) {
                  prepareMediaRecorder(stream);
              }
              
              // Vérifier si le flash est disponible
              checkFlashAvailability(stream);
          })
          .catch(error => {
              console.error('Erreur lors de l\'accès à la caméra:', error);
              
              // Afficher le message d'erreur approprié
              if (isVideoTab) {
                  document.getElementById('video-permission-error').classList.add('active');
              } else {
                  document.getElementById('camera-permission-error').classList.add('active');
              }
          });
  }
  
  // Fonction pour vérifier si le flash est disponible
  function checkFlashAvailability(stream) {
      if (!stream) return;
      
      const videoTrack = stream.getVideoTracks()[0];
      
      if (videoTrack && typeof videoTrack.getCapabilities === 'function') {
          const capabilities = videoTrack.getCapabilities();
          
          // Vérifier si le flash est disponible
          if (capabilities && capabilities.torch) {
              document.getElementById('toggle-flash').style.display = 'flex';
          } else {
              document.getElementById('toggle-flash').style.display = 'none';
          }
      } else {
          document.getElementById('toggle-flash').style.display = 'none';
      }
  }
  
  // Fonction pour arrêter la caméra
  function stopCamera() {
      if (videoStream) {
          videoStream.getTracks().forEach(track => track.stop());
          videoStream = null;
      }
      
      // Arrêter l'enregistrement vidéo si en cours
      if (mediaRecorder && mediaRecorder.state === 'recording') {
          stopRecording();
      }
  }
  
  // Fonction pour capturer une photo
  function capturePhoto() {
      const video = document.getElementById('camera-preview');
      
      // Vérifier si la vidéo est prête
      if (!video.srcObject || !video.srcObject.active) {
          alert('La caméra n\'est pas prête. Veuillez réessayer.');
          return;
      }
      
      // Vérifier si l'ajout dépasserait la limite
      if (uploadedImages.length + capturedPhotos.length >= 11) {
          alert('Vous avez atteint le nombre maximum d\'images (11). Veuillez en supprimer pour en ajouter de nouvelles.');
          return;
      }
      
      const canvas = document.getElementById('filter-canvas');
      const capturesContainer = document.getElementById('photo-captures');
      const counter = document.getElementById('photo-counter');
      
      // Afficher le compteur
      counter.classList.add('active');
      counter.textContent = '3';
      
      // Compte à rebours
      let count = 3;
      const countdown = setInterval(() => {
          count--;
          counter.textContent = count;
          
          if (count <= 0) {
              clearInterval(countdown);
              counter.classList.remove('active');
              
              // Capturer l'image
              canvas.width = video.videoWidth || 640;
              canvas.height = video.videoHeight || 480;
              const ctx = canvas.getContext('2d');
              
              // Dessiner la vidéo sur le canvas
              ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
              
              // Appliquer le filtre
              applyFilter(ctx, currentFilter);
              
              // Convertir en blob
              canvas.toBlob(blob => {
                  // Créer un fichier à partir du blob
                  const file = new File([blob], `photo_${Date.now()}.jpg`, { type: 'image/jpeg' });
                  
                  // Ajouter à la liste des photos capturées
                  capturedPhotos.push(file);
                  
                  // Créer l'élément de prévisualisation
                  const div = document.createElement('div');
                  div.className = 'capture-item';
                  
                  const img = document.createElement('img');
                  img.src = URL.createObjectURL(blob);
                  
                  const removeButton = document.createElement('div');
                  removeButton.className = 'remove-capture';
                  removeButton.innerHTML = '<i class="fas fa-times"></i>';
                  removeButton.dataset.index = capturedPhotos.length - 1;
                  removeButton.addEventListener('click', removeCapturedPhoto);
                  
                  div.appendChild(img);
                  div.appendChild(removeButton);
                  capturesContainer.appendChild(div);
                  
                  // Mettre à jour le compteur d'images
                  updateImageCounter();
              }, 'image/jpeg', 0.9);
          }
      }, 1000);
  }
  
  // Fonction pour supprimer une photo capturée
  function removeCapturedPhoto(event) {
      const index = parseInt(event.currentTarget.dataset.index);
      
      // Supprimer la photo de la liste
      capturedPhotos.splice(index, 1);
      
      // Supprimer l'élément de l'interface
      event.currentTarget.parentElement.remove();
      
      // Mettre à jour les indices des autres boutons de suppression
      document.querySelectorAll('#photo-captures .remove-capture').forEach((button, i) => {
          button.dataset.index = i;
      });
      
      // Mettre à jour le compteur d'images
      updateImageCounter();
  }
  
  // Fonction pour préparer l'enregistreur vidéo
  function prepareMediaRecorder(stream) {
      try {
          // Essayer différents formats MIME pour une meilleure compatibilité
          const mimeTypes = [
              'video/webm;codecs=vp9,opus',
              'video/webm;codecs=vp8,opus',
              'video/webm;codecs=h264,opus',
              'video/webm',
              'video/mp4',
              'video/mpeg'
          ];
          
          let mimeType = '';
          
          // Trouver le premier type MIME supporté
          for (let type of mimeTypes) {
              if (MediaRecorder.isTypeSupported(type)) {
                  mimeType = type;
                  break;
              }
          }
          
          // Créer l'enregistreur avec le type MIME supporté
          mediaRecorder = new MediaRecorder(stream, { 
              mimeType: mimeType || '', 
              videoBitsPerSecond: 2500000 // 2.5 Mbps pour une qualité raisonnable
          });
          
          mediaRecorder.ondataavailable = function(e) {
              if (e.data && e.data.size > 0) {
                  recordedChunks.push(e.data);
              }
          };
          
          mediaRecorder.onstop = function() {
              // Créer un blob à partir des chunks
              const blob = new Blob(recordedChunks, { 
                  type: mimeType || 'video/webm' 
              });
              
              // Créer un fichier à partir du blob
              const file = new File([blob], `video_${Date.now()}.webm`, { 
                  type: mimeType || 'video/webm' 
              });
              
              // Stocker la vidéo capturée
              capturedVideo = file;
              
              // Afficher la vidéo capturée
              const capturesContainer = document.getElementById('video-captures');
              capturesContainer.innerHTML = '';
              
              const div = document.createElement('div');
              div.className = 'capture-item';
              
              const video = document.createElement('video');
              video.src = URL.createObjectURL(blob);
              video.controls = true;
              video.autoplay = false;
              
              const removeButton = document.createElement('div');
              removeButton.className = 'remove-capture';
              removeButton.innerHTML = '<i class="fas fa-times"></i>';
              removeButton.addEventListener('click', removeCapturedVideo);
              
              div.appendChild(video);
              div.appendChild(removeButton);
              capturesContainer.appendChild(div);
              
              // Réinitialiser les chunks pour le prochain enregistrement
              recordedChunks = [];
          };
      } catch (error) {
          console.error('Erreur lors de la préparation de l\'enregistreur:', error);
          alert('Votre navigateur ne prend pas en charge l\'enregistrement vidéo. Veuillez utiliser un navigateur plus récent ou essayer d\'importer une vidéo.');
      }
  }
  
  // Fonction pour démarrer l'enregistrement vidéo
  function startRecording() {
      if (!mediaRecorder) {
          alert('L\'enregistreur vidéo n\'est pas prêt. Veuillez réessayer.');
          return;
      }
      
      if (mediaRecorder.state !== 'recording') {
          // Réinitialiser le compteur
          recordingSeconds = 0;
          document.getElementById('video-timer').textContent = '00:00';
          document.getElementById('video-timer').classList.add('active');
          
          // Démarrer l'enregistrement
          try {
              mediaRecorder.start(100); // Collecter les données toutes les 100ms
              
              // Mettre à jour l'interface
              document.getElementById('record-video').style.display = 'none';
              document.getElementById('stop-video').style.display = 'flex';
              document.getElementById('record-video').classList.add('record', 'active');
              
              // Démarrer le timer
              recordingTimer = setInterval(() => {
                  recordingSeconds++;
                  const minutes = Math.floor(recordingSeconds / 60);
                  const seconds = recordingSeconds % 60;
                  document.getElementById('video-timer').textContent = 
                      `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                  
                  // Limiter l'enregistrement à 2 minutes
                  if (recordingSeconds >= 120) {
                      stopRecording();
                  }
              }, 1000);
          } catch (error) {
              console.error('Erreur lors du démarrage de l\'enregistrement:', error);
              alert('Impossible de démarrer l\'enregistrement. Veuillez réessayer ou utiliser un autre navigateur.');
          }
      }
  }
  
  // Fonction pour arrêter l'enregistrement vidéo
  function stopRecording() {
      if (mediaRecorder && mediaRecorder.state === 'recording') {
          mediaRecorder.stop();
          
          // Arrêter le timer
          clearInterval(recordingTimer);
          document.getElementById('video-timer').classList.remove('active');
          
          // Mettre à jour l'interface
          document.getElementById('record-video').style.display = 'flex';
          document.getElementById('stop-video').style.display = 'none';
          document.getElementById('record-video').classList.remove('record', 'active');
      }
  }
  
  // Fonction pour supprimer la vidéo capturée
  function removeCapturedVideo() {
      capturedVideo = null;
      document.getElementById('video-captures').innerHTML = '';
  }
  
  // Fonction pour appliquer un filtre à la vidéo en direct
  function applyFilterToVideo(filter) {
      const video = document.getElementById('camera-preview');
      
      // Réinitialiser les filtres
      video.style.filter = '';
      
      // Appliquer le filtre sélectionné
      switch (filter) {
          case 'grayscale':
              video.style.filter = 'grayscale(100%)';
              break;
          case 'sepia':
              video.style.filter = 'sepia(100%)';
              break;
          case 'vintage':
              video.style.filter = 'sepia(50%) contrast(120%) brightness(90%)';
              break;
          case 'warm':
              video.style.filter = 'saturate(150%) contrast(110%) brightness(110%)';
              break;
          case 'cool':
              video.style.filter = 'saturate(80%) hue-rotate(30deg) brightness(105%)';
              break;
          default:
              // Pas de filtre
              break;
      }
  }
  
  // Fonction pour appliquer un filtre à un contexte canvas
  function applyFilter(ctx, filter) {
      const canvas = ctx.canvas;
      const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
      const data = imageData.data;
      
      switch (filter) {
          case 'grayscale':
              for (let i = 0; i < data.length; i += 4) {
                  const avg = (data[i] + data[i + 1] + data[i + 2]) / 3;
                  data[i] = avg;     // Rouge
                  data[i + 1] = avg; // Vert
                  data[i + 2] = avg; // Bleu
              }
              break;
          case 'sepia':
              for (let i = 0; i < data.length; i += 4) {
                  const r = data[i];
                  const g = data[i + 1];
                  const b = data[i + 2];
                  
                  data[i] = Math.min(255, (r * 0.393) + (g * 0.769) + (b * 0.189));
                  data[i + 1] = Math.min(255, (r * 0.349) + (g * 0.686) + (b * 0.168));
                  data[i + 2] = Math.min(255, (r * 0.272) + (g * 0.534) + (b * 0.131));
              }
              break;
          case 'vintage':
              for (let i = 0; i < data.length; i += 4) {
                  const r = data[i];
                  const g = data[i + 1];
                  const b = data[i + 2];
                  
                  data[i] = Math.min(255, (r * 0.5) + (g * 0.4) + (b * 0.1) + 30);
                  data[i + 1] = Math.min(255, (r * 0.2) + (g * 0.7) + (b * 0.1) + 10);
                  data[i + 2] = Math.min(255, (r * 0.1) + (g * 0.3) + (b * 0.6));
              }
              break;
          case 'warm':
              for (let i = 0; i < data.length; i += 4) {
                  data[i] = Math.min(255, data[i] * 1.2);       // Augmenter le rouge
                  data[i + 1] = Math.min(255, data[i + 1] * 1.1); // Augmenter légèrement le vert
              }
              break;
          case 'cool':
              for (let i = 0; i < data.length; i += 4) {
                  data[i + 2] = Math.min(255, data[i + 2] * 1.2); // Augmenter le bleu
              }
              break;
          default:
              // Pas de filtre
              break;
      }
      
      ctx.putImageData(imageData, 0, 0);
  }
  
  // Fonction pour basculer le flash
  function toggleFlash() {
      if (!videoStream) return;
      
      const videoTrack = videoStream.getVideoTracks()[0];
      
      if (videoTrack && typeof videoTrack.getCapabilities === 'function') {
          const capabilities = videoTrack.getCapabilities();
          
          // Vérifier si le flash est disponible
          if (capabilities && capabilities.torch) {
              try {
                  // Basculer l'état du flash
                  flashEnabled = !flashEnabled;
                  
                  // Appliquer le paramètre
                  videoTrack.applyConstraints({
                      advanced: [{ torch: flashEnabled }]
                  }).then(() => {
                      // Mettre à jour l'interface
                      const flashButton = document.getElementById('toggle-flash');
                      if (flashEnabled) {
                          flashButton.classList.add('active');
                          flashButton.style.backgroundColor = '#f59e0b';
                      } else {
                          flashButton.classList.remove('active');
                          flashButton.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
                      }
                  }).catch(error => {
                      console.error('Erreur lors de l\'activation du flash:', error);
                      alert('Impossible d\'activer le flash. Votre appareil ne supporte peut-être pas cette fonctionnalité.');
                  });
              } catch (error) {
                  console.error('Erreur lors de la manipulation du flash:', error);
                  alert('Impossible de contrôler le flash. Veuillez utiliser un éclairage externe si nécessaire.');
              }
          } else {
              alert('Votre appareil ne dispose pas de flash ou ne permet pas de le contrôler via le navigateur.');
          }
      } else {
          alert('Votre navigateur ne prend pas en charge le contrôle du flash.');
      }
  }
  
  // Fonction pour afficher/masquer la grille
  function toggleGrid() {
      const cameraContainer = document.querySelector('.camera-container');
      
      if (cameraContainer.style.backgroundImage) {
          cameraContainer.style.backgroundImage = '';
      } else {
          cameraContainer.style.backgroundImage = 'linear-gradient(to right, rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.1) 1px, transparent 1px)';
          cameraContainer.style.backgroundSize = '33.33% 33.33%';
      }
  }
  
  // Fonction pour changer de caméra
  function switchCamera() {
      facingMode = facingMode === 'user' ? 'environment' : 'user';
      
      // Réinitialiser la caméra avec la nouvelle configuration
      const activeTab = document.querySelector('.media-tab.active').dataset.tab;
      if (activeTab === 'camera') {
          initCamera('camera-preview');
      } else if (activeTab === 'video') {
          initCamera('video-preview', true);
      }
  }
  
  // Fonction pour soumettre le formulaire
  function submitPropertyForm(event) {
    event.preventDefault();
    
    // Masquer les messages d'erreur précédents
    const errorContainer = document.getElementById('error-container');
    errorContainer.classList.add('hidden');
    errorContainer.innerHTML = '';
    
    const form = event.target;
    const formData = new FormData();
    
    // Ajouter les champs du formulaire
    formData.append('titre', document.getElementById('titre').value);
    formData.append('description', document.getElementById('description').value);
    formData.append('type', document.getElementById('type').value);
    formData.append('adresse', document.getElementById('adresse').value);
    formData.append('prix', document.getElementById('prix').value);
    formData.append('superficie', document.getElementById('superficie').value);
    formData.append('latitude', document.getElementById('latitude').value);
    formData.append('longitude', document.getElementById('longitude').value);
    formData.append('transaction_type', document.getElementById('transaction_type').value);
    
    // Ajouter les caractéristiques supplémentaires
    document.querySelectorAll('input[name="features[]"]:checked').forEach(checkbox => {
        formData.append('features[]', checkbox.value);
    });
    
    // Afficher un indicateur de chargement
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Publication en cours...';
    submitButton.disabled = true;
    
    // Optimiser et ajouter les images
    const totalImages = uploadedImages.length + capturedPhotos.length;
    let processedImages = 0;
    
    // Fonction pour vérifier si toutes les images ont été traitées
    const checkAllImagesProcessed = () => {
        processedImages++;
        if (processedImages === totalImages) {
            // Toutes les images ont été traitées, soumettre le formulaire
            submitFormData(formData, submitButton, originalText);
        }
    };
    
    // Fonction pour optimiser une image
    const optimizeImage = (file, index, isUploaded) => {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const img = new Image();
        
        img.onload = () => {
            // Calculer les dimensions pour redimensionner si nécessaire
            let width = img.width;
            let height = img.height;
            
            // Limiter la taille maximale à 1920px tout en conservant le ratio
            const maxSize = 1920;
            if (width > maxSize || height > maxSize) {
                if (width > height) {
                    height = Math.round(height * (maxSize / width));
                    width = maxSize;
                } else {
                    width = Math.round(width * (maxSize / height));
                    height = maxSize;
                }
            }
            
            canvas.width = width;
            canvas.height = height;
            
            // Dessiner l'image redimensionnée
            ctx.drawImage(img, 0, 0, width, height);
            
            // Convertir en WebP avec une qualité de 85%
            canvas.toBlob(blob => {
                const optimizedFile = new File([blob], `image_${index}.webp`, { type: 'image/webp' });
                formData.append('images[]', optimizedFile);
                checkAllImagesProcessed();
            }, 'image/webp', 0.85);
        };
        
        img.onerror = () => {
            // En cas d'erreur, utiliser l'image originale
            formData.append('images[]', file);
            checkAllImagesProcessed();
        };
        
        img.src = URL.createObjectURL(file);
    };
    
    // Traiter toutes les images
    if (totalImages > 0) {
        uploadedImages.forEach((file, index) => {
            optimizeImage(file, index, true);
        });
        
        capturedPhotos.forEach((file, index) => {
            optimizeImage(file, uploadedImages.length + index, false);
        });
    } else {
        // Pas d'images à traiter
        submitFormData(formData, submitButton, originalText);
    }
    
    // Ajouter la vidéo capturée si elle existe
    if (capturedVideo) {
        formData.append('video', capturedVideo);
    }
}

// Fonction pour envoyer les données du formulaire
function submitFormData(formData, submitButton, originalText) {
    // Envoyer les données
    fetch('/api/properties', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mettre à jour le lien vers le bien
            document.getElementById('view-property-link').href = `/properties/${data.property_id}`;
            
            // Afficher le modal de succès
            toggleModal('successModal');
        } else {
            // Afficher les erreurs détaillées
            const errorContainer = document.getElementById('error-container');
            errorContainer.classList.remove('hidden');
            
            if (data.errors) {
                let errorHTML = '<ul class="list-disc pl-5">';
                for (const field in data.errors) {
                    data.errors[field].forEach(error => {
                        errorHTML += `<li>${error}</li>`;
                    });
                }
                errorHTML += '</ul>';
                errorContainer.innerHTML = errorHTML;
            } else {
                errorContainer.textContent = data.message || 'Une erreur est survenue lors de l\'ajout du bien';
            }
            
            // Faire défiler jusqu'au message d'erreur
            errorContainer.scrollIntoView({ behavior: 'smooth' });
            
            // Restaurer le bouton
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        }
    })
    .catch(error => {
        console.error('Erreur lors de l\'ajout du bien:', error);
        
        // Afficher l'erreur
        const errorContainer = document.getElementById('error-container');
        errorContainer.classList.remove('hidden');
        errorContainer.textContent = 'Une erreur est survenue lors de l\'ajout du bien. Veuillez réessayer.';
        
        // Faire défiler jusqu'au message d'erreur
        errorContainer.scrollIntoView({ behavior: 'smooth' });
        
        // Restaurer le bouton
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    });
}

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
</script>
<script>
// Remplacer la section de géolocalisation dans le script JavaScript par ce code amélioré:

// Utiliser la géolocalisation pour obtenir la position actuelle
if (navigator.geolocation) {
    // Afficher un message de chargement
    document.getElementById('adresse').placeholder = "Récupération de votre position précise en cours...";
    
    // Ajouter un indicateur de chargement à côté de la carte
    const mapContainer = document.getElementById('map').parentElement;
    const loadingIndicator = document.createElement('div');
    loadingIndicator.id = 'location-loading';
    loadingIndicator.className = 'bg-blue-100 text-blue-800 p-2 rounded-lg mb-2 flex items-center';
    loadingIndicator.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Récupération de votre position GPS précise...';
    mapContainer.insertBefore(loadingIndicator, document.getElementById('map'));
    
    // Options de haute précision pour la géolocalisation
    const geoOptions = {
        enableHighAccuracy: true, // Demander la plus haute précision possible (GPS)
        timeout: 10000,           // Timeout après 10 secondes
        maximumAge: 0             // Ne pas utiliser de cache, toujours obtenir une position fraîche
    };
    
    navigator.geolocation.getCurrentPosition(
        function(position) {
            // Supprimer l'indicateur de chargement
            document.getElementById('location-loading').remove();
            
            const userLocation = [position.coords.longitude, position.coords.latitude];
            const accuracy = position.coords.accuracy; // Précision en mètres
            
            // Afficher la précision
            const accuracyInfo = document.createElement('div');
            accuracyInfo.className = 'text-sm text-gray-600 mb-2';
            if (accuracy < 100) {
                accuracyInfo.innerHTML = `<i class="fas fa-check-circle text-green-500 mr-1"></i> Position GPS précise obtenue (précision: ~${Math.round(accuracy)} mètres)`;
            } else {
                accuracyInfo.innerHTML = `<i class="fas fa-info-circle text-yellow-500 mr-1"></i> Position obtenue avec une précision de ~${Math.round(accuracy)} mètres`;
            }
            mapContainer.insertBefore(accuracyInfo, document.getElementById('map'));
            
            // Ajouter un bouton pour rafraîchir la position
            const refreshButton = document.createElement('button');
            refreshButton.type = 'button';
            refreshButton.className = 'text-sm text-blue-600 mb-2 flex items-center';
            refreshButton.innerHTML = '<i class="fas fa-sync-alt mr-1"></i> Actualiser ma position GPS';
            refreshButton.onclick = refreshUserLocation;
            mapContainer.insertBefore(refreshButton, document.getElementById('map'));
            
            // Centrer la carte sur la position de l'utilisateur
            map.flyTo({
                center: userLocation,
                zoom: 18 // Zoom élevé pour une vue détaillée
            });
            
            // Placer le marqueur à la position exacte
            marker.setLngLat(userLocation);
            updateCoordinates();
            
            // Essayer d'obtenir l'adresse à partir des coordonnées (géocodage inverse)
            fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${userLocation[0]},${userLocation[1]}.json?access_token=${mapboxgl.accessToken}`)
                .then(response => response.json())
                .then(data => {
                    if (data.features && data.features.length > 0) {
                        const address = data.features[0].place_name;
                        document.getElementById('adresse').value = address;
                        // Ajouter une notification visuelle que l'adresse a été remplie automatiquement
                        const addressField = document.getElementById('adresse');
                        addressField.style.backgroundColor = "#e6f7ff";
                        setTimeout(() => {
                            addressField.style.backgroundColor = "";
                        }, 2000);
                    } else {
                        document.getElementById('adresse').placeholder = "Entrez l'adresse du bien";
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du géocodage inverse:', error);
                    document.getElementById('adresse').placeholder = "Entrez l'adresse du bien";
                });
                
            // Ajouter un cercle pour montrer la précision de la localisation
            if (window.accuracyCircle) {
                window.accuracyCircle.remove();
            }
            
            window.accuracyCircle = new mapboxgl.Marker({
                element: createAccuracyCircle(accuracy),
                anchor: 'center'
            })
            .setLngLat(userLocation)
            .addTo(map);
        },
        function(error) {
            // Supprimer l'indicateur de chargement
            document.getElementById('location-loading').remove();
            
            console.error('Erreur de géolocalisation:', error);
            
            // Afficher un message d'erreur approprié
            const errorMessage = document.createElement('div');
            errorMessage.className = 'bg-red-100 text-red-800 p-2 rounded-lg mb-2';
            
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    errorMessage.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> Vous avez refusé l\'accès à votre position. Pour utiliser la localisation précise, veuillez autoriser l\'accès à votre position dans les paramètres de votre navigateur.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMessage.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> Votre position n\'a pas pu être déterminée. Veuillez vérifier que votre GPS est activé.';
                    break;
                case error.TIMEOUT:
                    errorMessage.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> La demande de position a expiré. Veuillez réessayer.';
                    break;
                default:
                    errorMessage.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> Une erreur inconnue s\'est produite lors de la récupération de votre position.';
            }
            
            // Ajouter un bouton pour réessayer
            errorMessage.innerHTML += '<button id="retry-geolocation" class="ml-2 text-blue-600 underline">Réessayer</button>';
            mapContainer.insertBefore(errorMessage, document.getElementById('map'));
            
            // Ajouter l'événement pour réessayer
            document.getElementById('retry-geolocation').addEventListener('click', refreshUserLocation);
            
            document.getElementById('adresse').placeholder = "Entrez l'adresse du bien";
        },
        geoOptions // Utiliser les options de haute précision
    );
} else {
    document.getElementById('adresse').placeholder = "Entrez l'adresse du bien";
}

// Fonction pour créer un cercle de précision
function createAccuracyCircle(accuracy) {
    const element = document.createElement('div');
    const size = Math.max(50, Math.min(300, accuracy)); // Limiter la taille entre 50 et 300px
    
    element.style.width = size + 'px';
    element.style.height = size + 'px';
    element.style.borderRadius = '50%';
    element.style.backgroundColor = 'rgba(66, 133, 244, 0.2)';
    element.style.border = '2px solid rgba(66, 133, 244, 0.6)';
    
    return element;
}

// Fonction pour rafraîchir la position de l'utilisateur
function refreshUserLocation() {
    // Supprimer les éléments existants
    if (document.getElementById('location-loading')) {
        document.getElementById('location-loading').remove();
    }
    
    const mapContainer = document.getElementById('map').parentElement;
    
    // Supprimer tous les messages et boutons précédents
    mapContainer.querySelectorAll('.text-sm, .bg-red-100').forEach(el => el.remove());
    
    // Ajouter un nouvel indicateur de chargement
    const loadingIndicator = document.createElement('div');
    loadingIndicator.id = 'location-loading';
    loadingIndicator.className = 'bg-blue-100 text-blue-800 p-2 rounded-lg mb-2 flex items-center';
    loadingIndicator.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Actualisation de votre position GPS...';
    mapContainer.insertBefore(loadingIndicator, document.getElementById('map'));
    
    // Options de haute précision pour la géolocalisation
    const geoOptions = {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0
    };
    
    navigator.geolocation.getCurrentPosition(
        function(position) {
            // Supprimer l'indicateur de chargement
            document.getElementById('location-loading').remove();
            
            const userLocation = [position.coords.longitude, position.coords.latitude];
            const accuracy = position.coords.accuracy;
            
            // Afficher la précision
            const accuracyInfo = document.createElement('div');
            accuracyInfo.className = 'text-sm text-gray-600 mb-2';
            if (accuracy < 100) {
                accuracyInfo.innerHTML = `<i class="fas fa-check-circle text-green-500 mr-1"></i> Position GPS précise obtenue (précision: ~${Math.round(accuracy)} mètres)`;
            } else {
                accuracyInfo.innerHTML = `<i class="fas fa-info-circle text-yellow-500 mr-1"></i> Position obtenue avec une précision de ~${Math.round(accuracy)} mètres`;
            }
            mapContainer.insertBefore(accuracyInfo, document.getElementById('map'));
            
            // Ajouter un bouton pour rafraîchir la position
            const refreshButton = document.createElement('button');
            refreshButton.type = 'button';
            refreshButton.className = 'text-sm text-blue-600 mb-2 flex items-center';
            refreshButton.innerHTML = '<i class="fas fa-sync-alt mr-1"></i> Actualiser ma position GPS';
            refreshButton.onclick = refreshUserLocation;
            mapContainer.insertBefore(refreshButton, document.getElementById('map'));
            
            // Centrer la carte sur la position de l'utilisateur
            map.flyTo({
                center: userLocation,
                zoom: 18
            });
            
            // Placer le marqueur à la position exacte
            marker.setLngLat(userLocation);
            updateCoordinates();
            
            // Essayer d'obtenir l'adresse à partir des coordonnées
            fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${userLocation[0]},${userLocation[1]}.json?access_token=${mapboxgl.accessToken}`)
                .then(response => response.json())
                .then(data => {
                    if (data.features && data.features.length > 0) {
                        const address = data.features[0].place_name;
                        document.getElementById('adresse').value = address;
                        // Notification visuelle
                        const addressField = document.getElementById('adresse');
                        addressField.style.backgroundColor = "#e6f7ff";
                        setTimeout(() => {
                            addressField.style.backgroundColor = "";
                        }, 2000);
                    }
                })
                .catch(error => console.error('Erreur lors du géocodage inverse:', error));
                
            // Mettre à jour le cercle de précision
            if (window.accuracyCircle) {
                window.accuracyCircle.remove();
            }
            
            window.accuracyCircle = new mapboxgl.Marker({
                element: createAccuracyCircle(accuracy),
                anchor: 'center'
            })
            .setLngLat(userLocation)
            .addTo(map);
        },
        function(error) {
            // Supprimer l'indicateur de chargement
            document.getElementById('location-loading').remove();
            
            console.error('Erreur de géolocalisation:', error);
            
            // Afficher un message d'erreur approprié
            const errorMessage = document.createElement('div');
            errorMessage.className = 'bg-red-100 text-red-800 p-2 rounded-lg mb-2';
            
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    errorMessage.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> Vous avez refusé l\'accès à votre position. Pour utiliser la localisation précise, veuillez autoriser l\'accès à votre position dans les paramètres de votre navigateur.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMessage.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> Votre position n\'a pas pu être déterminée. Veuillez vérifier que votre GPS est activé.';
                    break;
                case error.TIMEOUT:
                    errorMessage.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> La demande de position a expiré. Veuillez réessayer.';
                    break;
                default:
                    errorMessage.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> Une erreur inconnue s\'est produite lors de la récupération de votre position.';
            }
            
            // Ajouter un bouton pour réessayer
            errorMessage.innerHTML += '<button id="retry-geolocation" class="ml-2 text-blue-600 underline">Réessayer</button>';
            mapContainer.insertBefore(errorMessage, document.getElementById('map'));
            
            // Ajouter l'événement pour réessayer
            document.getElementById('retry-geolocation').addEventListener('click', refreshUserLocation);
        },
        geoOptions
    );
}

// Fonction pour créer un cercle de précision
function createAccuracyCircle(accuracy) {
    const element = document.createElement('div');
    const size = Math.max(50, Math.min(300, accuracy)); // Limiter la taille entre 50 et 300px
    
    element.style.width = size + 'px';
    element.style.height = size + 'px';
    element.style.borderRadius = '50%';
    element.style.backgroundColor = 'rgba(66, 133, 244, 0.2)';
    element.style.border = '2px solid rgba(66, 133, 244, 0.6)';
    
    return element;
}

// Fonction pour rafraîchir la position de l'utilisateur
function refreshUserLocation() {
    // Supprimer les éléments existants
    if (document.getElementById('location-loading')) {
        document.getElementById('location-loading').remove();
    }
    
    const mapContainer = document.getElementById('map').parentElement;
    
    // Supprimer tous les messages et boutons précédents
    mapContainer.querySelectorAll('.text-sm, .bg-red-100').forEach(el => el.remove());
    
    // Ajouter un nouvel indicateur de chargement
    const loadingIndicator = document.createElement('div');
    loadingIndicator.id = 'location-loading';
    loadingIndicator.className = 'bg-blue-100 text-blue-800 p-2 rounded-lg mb-2 flex items-center';
    loadingIndicator.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Actualisation de votre position GPS...';
    mapContainer.insertBefore(loadingIndicator, document.getElementById('map'));
    
    // Options de haute précision pour la géolocalisation
    const geoOptions = {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0
    };
    
    navigator.geolocation.getCurrentPosition(
        function(position) {
            // Supprimer l'indicateur de chargement
            document.getElementById('location-loading').remove();
            
            const userLocation = [position.coords.longitude, position.coords.latitude];
            const accuracy = position.coords.accuracy;
            
            // Afficher la précision
            const accuracyInfo = document.createElement('div');
            accuracyInfo.className = 'text-sm text-gray-600 mb-2';
            if (accuracy < 100) {
                accuracyInfo.innerHTML = `<i class="fas fa-check-circle text-green-500 mr-1"></i> Position GPS précise obtenue (précision: ~${Math.round(accuracy)} mètres)`;
            } else {
                accuracyInfo.innerHTML = `<i class="fas fa-info-circle text-yellow-500 mr-1"></i> Position obtenue avec une précision de ~${Math.round(accuracy)} mètres`;
            }
            mapContainer.insertBefore(accuracyInfo, document.getElementById('map'));
            
            // Ajouter un bouton pour rafraîchir la position
            const refreshButton = document.createElement('button');
            refreshButton.type = 'button';
            refreshButton.className = 'text-sm text-blue-600 mb-2 flex items-center';
            refreshButton.innerHTML = '<i class="fas fa-sync-alt mr-1"></i> Actualiser ma position GPS';
            refreshButton.onclick = refreshUserLocation;
            mapContainer.insertBefore(refreshButton, document.getElementById('map'));
            
            // Centrer la carte sur la position de l'utilisateur
            map.flyTo({
                center: userLocation,
                zoom: 18
            });
            
            // Placer le marqueur à la position exacte
            marker.setLngLat(userLocation);
            updateCoordinates();
            
            // Essayer d'obtenir l'adresse à partir des coordonnées
            fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${userLocation[0]},${userLocation[1]}.json?access_token=${mapboxgl.accessToken}`)
                .then(response => response.json())
                .then(data => {
                    if (data.features && data.features.length > 0) {
                        const address = data.features[0].place_name;
                        document.getElementById('adresse').value = address;
                        // Notification visuelle
                        const addressField = document.getElementById('adresse');
                        addressField.style.backgroundColor = "#e6f7ff";
                        setTimeout(() => {
                            addressField.style.backgroundColor = "";
                        }, 2000);
                    }
                })
                .catch(error => console.error('Erreur lors du géocodage inverse:', error));
                
            // Mettre à jour le cercle de précision
            if (window.accuracyCircle) {
                window.accuracyCircle.remove();
            }
            
            window.accuracyCircle = new mapboxgl.Marker({
                element: createAccuracyCircle(accuracy),
                anchor: 'center'
            })
            .setLngLat(userLocation)
            .addTo(map);
        },
        function(error) {
            // Supprimer l'indicateur de chargement
            document.getElementById('location-loading').remove();
            
            console.error('Erreur de géolocalisation:', error);
            
            // Afficher un message d'erreur approprié
            const errorMessage = document.createElement('div');
            errorMessage.className = 'bg-red-100 text-red-800 p-2 rounded-lg mb-2';
            
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    errorMessage.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> Vous avez refusé l\'accès à votre position. Pour utiliser la localisation précise, veuillez autoriser l\'accès à votre position dans les paramètres de votre navigateur.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMessage.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> Votre position n\'a pas pu être déterminée. Veuillez vérifier que votre GPS est activé.';
                    break;
                case error.TIMEOUT:
                    errorMessage.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> La demande de position a expiré. Veuillez réessayer.';
                    break;
                default:
                    errorMessage.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> Une erreur inconnue s\'est produite lors de la récupération de votre position.';
            }
            
            // Ajouter un bouton pour réessayer
            errorMessage.innerHTML += '<button id="retry-geolocation" class="ml-2 text-blue-600 underline">Réessayer</button>';
            mapContainer.insertBefore(errorMessage, document.getElementById('map'));
            
            // Ajouter l'événement pour réessayer
            document.getElementById('retry-geolocation').addEventListener('click', refreshUserLocation);
        },
        geoOptions
    );
}

// Remplacer le texte du message d'information sur la géolocalisation
const infoMessage = document.querySelector('.bg-blue-50.p-3.rounded-lg.mb-3');
infoMessage.innerHTML = `
    <i class="fas fa-info-circle text-blue-500 mt-1 mr-2"></i>
    <p class="text-sm text-blue-700">Votre position GPS précise est utilisée par défaut, car nous supposons que vous êtes sur les lieux du bien. L'adresse correspondante est automatiquement remplie. Vous pouvez ajuster la position si nécessaire.</p>
`;
</script>
@endpush