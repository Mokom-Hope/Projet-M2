<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController\AuthController;
use App\Http\Controllers\PaymentController\PaymentController;
use App\Http\Controllers\PropertyController\PropertyController;
use App\Http\Controllers\DashboardController\DashboardController;
use App\Http\Controllers\BlockchainController\BlockchainController;
use App\Http\Controllers\ReservationController\ReservationController;

// Routes publiques
Route::get('/', [PropertyController::class, 'index'])->name('home');
Route::get('/map', [PropertyController::class, 'map'])->name('map');
Route::get('/properties/{id}', [PropertyController::class, 'show'])->name('properties.show');

// Routes d'authentification
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes pour l'authentification à deux facteurs
Route::get('/verify', [AuthController::class, 'showVerificationForm'])->name('verify.show');
Route::post('/verify', [AuthController::class, 'verify'])->name('verify.post');
Route::get('/verify/resend', [AuthController::class, 'resendCode'])->name('verify.resend');

// Routes pour la réinitialisation du mot de passe
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Route de test pour vérifier l'authentification
Route::get('/test-auth', function () {
  if (Auth::check()) {
      return 'Vous êtes connecté en tant que: ' . Auth::user()->nom . ' (' . Auth::user()->type_utilisateur . ')';
  } else {
      return 'Vous n\'êtes pas connecté.';
  }
});

// Modifiez les routes pour mettre properties/create dans le groupe middleware auth
// et supprimer la route individuelle

// Supprimer cette ligne
// Route::get('/properties/create', [PropertyController::class, 'create'])->name('properties.create');

// Modifiez la route properties/create pour la sortir du groupe middleware auth
//Route::get('/properties.create', [PropertyController::class, 'create'])->name('properties.create');

// Remplacez par:
Route::get('/properties.create', [PropertyController::class, 'create'])->name('properties.create');

// Routes protégées
Route::middleware(['auth'])->group(function () {
  // Routes pour les propriétaires et clients
  Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
  Route::get('/dashboard/profile', [DashboardController::class, 'profile'])->name('dashboard.profile');
  Route::put('/dashboard/profile', [DashboardController::class, 'updateProfile'])->name('dashboard.profile.update');
  
  // Ajouter cette ligne dans le groupe auth
  //Route::get('/properties/create', [PropertyController::class, 'create'])->name('properties.create');
  
  // Autres routes qui peuvent être protégées par le middleware auth
  Route::get('/properties/{id}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
  Route::get('/dashboard/properties', [DashboardController::class, 'properties'])->name('dashboard.properties');
  Route::get('/dashboard/reservations', [DashboardController::class, 'reservations'])->name('dashboard.reservations');
  Route::get('/dashboard/reservations/{id}', [DashboardController::class, 'showReservation'])->name('dashboard.reservations.show');
  Route::get('/dashboard/messages', [DashboardController::class, 'messages'])->name('dashboard.messages');
  
  // Routes pour les clients
  Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
});

// Routes API
Route::prefix('api')->group(function () {
  // Routes API publiques
  Route::get('/properties', [PropertyController::class, 'apiGetProperties']);
  Route::get('/properties/{id}', [PropertyController::class, 'apiGetProperty']);
  
  // Routes API protégées
  Route::middleware(['auth:sanctum'])->group(function () {
      // Biens
      Route::post('/properties', [PropertyController::class, 'store']);
      Route::put('/properties/{id}', [PropertyController::class, 'update']);
      Route::delete('/properties/{id}', [PropertyController::class, 'destroy']);
      Route::put('/properties/{id}/status', [PropertyController::class, 'updateStatus']);
      
      // Réservations
      Route::post('/reservations', [ReservationController::class, 'store']);
      Route::post('/reservations/{id}/accept', [ReservationController::class, 'accept']);
      Route::post('/reservations/{id}/reject', [ReservationController::class, 'reject']);
      
      // Dashboard
      Route::get('/dashboard/stats', [DashboardController::class, 'apiGetStats']);
      Route::get('/dashboard/properties', [PropertyController::class, 'apiGetOwnerProperties']);
      Route::get('/dashboard/reservations', [ReservationController::class, 'apiGetOwnerReservations']);
      Route::get('/dashboard/reservations/recent', [ReservationController::class, 'apiGetRecentReservations']);
      Route::get('/dashboard/reservations/client', [ReservationController::class, 'apiGetClientReservations']);
  });
});

// Routes pour les paiements NotchPay
Route::post('/payments/initialize', [PaymentController::class, 'initializePayment'])->name('payments.initialize');
Route::post('/payments/reservation', [PaymentController::class, 'initializeReservationPayment'])->name('payments.reservation');
Route::get('/payments/callback', [PaymentController::class, 'handleCallback'])->name('notchpay.callback');
Route::get('/payments/check-access', [PaymentController::class, 'checkOwnerInfoAccess'])->name('payments.check-access');
Route::get('/payments/owner-info', [PaymentController::class, 'getOwnerInfo'])->name('payments.owner-info');
// Nouvelle route pour les paiements mobiles
Route::post('/payments/mobile', [PaymentController::class, 'completeMobilePayment'])->name('payments.mobile');




// Routes pour l'explorateur de blockchain
Route::get('/blockchain/explorer', [BlockchainController::class, 'explorer'])->name('blockchain.explorer');
Route::get('/blockchain/block/{index}', [BlockchainController::class, 'showBlock'])->name('blockchain.block');
Route::get('/blockchain/transaction/{id}', [BlockchainController::class, 'showTransaction'])->name('blockchain.transaction');

// Routes pour la vérification blockchain
Route::get('/blockchain/verify/property/{id}', [BlockchainController::class, 'verifyProperty'])->name('blockchain.verify.property');
Route::get('/blockchain/verify/reservation/{id}', [BlockchainController::class, 'verifyReservation'])->name('blockchain.verify.reservation');