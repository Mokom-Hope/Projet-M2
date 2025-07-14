<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route racine
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('onboarding.welcome');
})->name('home');

// Guest Routes
Route::middleware('guest')->group(function () {
    // Inscription avec vérification email
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])
        ->name('register');
    Route::post('register', [RegisterController::class, 'register']);
    
    // Vérification du code email
    Route::get('register/verify', [RegisterController::class, 'showVerificationForm'])
        ->name('register.verify');
    Route::post('register/verify-code', [RegisterController::class, 'verifyCode'])
        ->name('register.verify.code');
    Route::post('register/resend', [RegisterController::class, 'resendCode'])
        ->name('register.resend');
    
    // Connexion
    Route::get('login', [LoginController::class, 'showLoginForm'])
        ->name('login');
    Route::post('login', [LoginController::class, 'login']);
    
    // 2FA
    Route::get('two-factor-authentication', [LoginController::class, 'showTwoFactorForm'])
        ->name('2fa.verify');
    Route::post('two-factor-authentication', [LoginController::class, 'verifyTwoFactor'])
        ->name('2fa.verify');
    
    // Mot de passe oublié
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');
    
    // Réinitialisation du mot de passe
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
    
    // Onboarding
    Route::get('welcome', function () {
        return view('onboarding.welcome');
    })->name('onboarding.welcome');
    Route::get('onboarding/step1', function () {
        return view('onboarding.step1');
    })->name('onboarding.step1');
    Route::get('onboarding/step2', function () {
        return view('onboarding.step2');
    })->name('onboarding.step2');
    Route::get('onboarding/step3', function () {
        return view('onboarding.step3');
    })->name('onboarding.step3');
});

// Routes publiques pour récupérer l'argent
Route::get('transfers/claim', [TransferController::class, 'showClaimForm'])
    ->name('transfers.claim');
Route::post('transfers/claim', [TransferController::class, 'claim'])
    ->name('transfers.claim.store');
Route::get('transfers/claim/{code}/success', function($code) {
    return view('transfers.claim-success', compact('code'));
})->name('transfers.claim.success');
Route::get('transfers/info', [TransferController::class, 'getTransferInfo'])
    ->name('transfers.info');
Route::get('transfers/payment-methods', [TransferController::class, 'getPaymentMethods'])
    ->name('transfers.payment-methods');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Tableau de bord
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    
    // Vérification email
    Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke'])
        ->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    
    // Profil utilisateur
    Route::get('profile', [ProfileController::class, 'show'])
        ->name('profile.show');
    Route::get('profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::post('profile/password', [ProfileController::class, 'changePassword'])
        ->name('profile.password');
    Route::post('profile/two-factor/enable', [ProfileController::class, 'enableTwoFactor'])
        ->name('profile.two-factor.enable');
    Route::post('profile/two-factor/disable', [ProfileController::class, 'disableTwoFactor'])
        ->name('profile.two-factor.disable');
    
    // Transferts - Routes générales
    Route::get('transfers/history', [TransferController::class, 'history'])
        ->name('transfers.history');
    Route::get('transfers/create', [TransferController::class, 'create'])
        ->name('transfers.create');
    Route::post('transfers', [TransferController::class, 'store'])
        ->name('transfers.store');
    Route::get('transfers/{transfer}', [TransferController::class, 'show'])
        ->name('transfers.show');
    Route::get('transfers/{transfer}/payment', [TransferController::class, 'showPayment'])
        ->name('transfers.payment');
    Route::post('transfers/{transfer}/cancel', [TransferController::class, 'cancel'])
        ->name('transfers.cancel');
    Route::post('transfers/{transfer}/check-payment', [TransferController::class, 'checkPaymentStatus'])
        ->name('transfers.check-payment');
    
    // Nouvelles routes ajoutées pour les transferts
    Route::post('transfers/{transfer}/simulate-success', [TransferController::class, 'simulatePaymentSuccess'])
        ->name('transfers.simulate-success');
    Route::post('transfers/{transfer}/resend-notification', [TransferController::class, 'resendNotification'])
        ->name('transfers.resend-notification');
    
    // Moyens de paiement
    Route::resource('payment-methods', PaymentMethodController::class);
    Route::post('payment-methods/{paymentMethod}/default', [PaymentMethodController::class, 'setDefault'])
        ->name('payment-methods.default');
    
    // Déconnexion
    Route::post('logout', [LoginController::class, 'logout'])
        ->name('logout');
});

// Webhook pour NotchPay
Route::post('payment/webhook', function() {
    // Traitement des webhooks NotchPay
    return response()->json(['status' => 'success']);
})->name('payment.webhook');

// Payment callbacks
Route::post('/payment/callback/notchpay', [PaymentCallbackController::class, 'notchpayCallback'])
    ->name('payment.callback');
Route::get('/payment/return', [PaymentCallbackController::class, 'paymentReturn'])
    ->name('payment.return');
