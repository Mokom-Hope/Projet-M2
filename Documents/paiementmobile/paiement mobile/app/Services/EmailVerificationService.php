<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EmailVerificationService
{
    private const CACHE_PREFIX = 'email_verification:';
    private const CODE_LENGTH = 6;
    private const EXPIRY_MINUTES = 10;
    private const MAX_ATTEMPTS = 3;
    private const RATE_LIMIT_MINUTES = 1;

    /**
     * Envoyer un code de vérification
     */
    public function sendVerificationCode(string $email, array $userData): array
    {
        // Vérifier le rate limiting
        $rateLimitKey = $this->getRateLimitKey($email);
        if (Cache::has($rateLimitKey)) {
            return [
                'success' => false,
                'message' => 'Veuillez attendre avant de demander un nouveau code.'
            ];
        }

        // Générer un code à 6 chiffres
        $code = $this->generateCode();
        
        // Créer la clé de cache sécurisée
        $cacheKey = $this->getCacheKey($email);
        
        // Stocker les données dans le cache
        $verificationData = [
            'code' => hash('sha256', $code), // Hasher le code
            'user_data' => encrypt($userData), // Chiffrer les données utilisateur
            'attempts' => 0,
            'created_at' => now()->toISOString(),
            'expires_at' => now()->addMinutes(self::EXPIRY_MINUTES)->toISOString()
        ];

        Cache::put($cacheKey, $verificationData, now()->addMinutes(self::EXPIRY_MINUTES));
        
        // Définir le rate limit
        Cache::put($rateLimitKey, true, now()->addMinutes(self::RATE_LIMIT_MINUTES));

        try {
            // Envoyer l'email
            Mail::send('emails.verification-code', [
                'code' => $code,
                'email' => $email,
                'expires_in' => self::EXPIRY_MINUTES
            ], function ($message) use ($email) {
                $message->to($email)
                        ->subject('Code de vérification MoneyTransfer');
            });

            return [
                'success' => true,
                'message' => 'Code de vérification envoyé avec succès.',
                'expires_in' => self::EXPIRY_MINUTES
            ];

        } catch (\Exception $e) {
            // Supprimer les données du cache en cas d'erreur d'envoi
            Cache::forget($cacheKey);
            Cache::forget($rateLimitKey);
            
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email. Veuillez réessayer.'
            ];
        }
    }

    /**
     * Vérifier un code
     */
    public function verifyCode(string $email, string $code): array
    {
        $cacheKey = $this->getCacheKey($email);
        $verificationData = Cache::get($cacheKey);

        if (!$verificationData) {
            return [
                'success' => false,
                'message' => 'Code expiré ou invalide. Veuillez demander un nouveau code.'
            ];
        }

        // Vérifier l'expiration
        if (Carbon::parse($verificationData['expires_at'])->isPast()) {
            Cache::forget($cacheKey);
            return [
                'success' => false,
                'message' => 'Code expiré. Veuillez demander un nouveau code.'
            ];
        }

        // Vérifier le nombre de tentatives
        if ($verificationData['attempts'] >= self::MAX_ATTEMPTS) {
            Cache::forget($cacheKey);
            return [
                'success' => false,
                'message' => 'Trop de tentatives. Veuillez demander un nouveau code.'
            ];
        }

        // Vérifier le code
        $hashedCode = hash('sha256', $code);
        if ($hashedCode !== $verificationData['code']) {
            // Incrémenter les tentatives
            $verificationData['attempts']++;
            Cache::put($cacheKey, $verificationData, Carbon::parse($verificationData['expires_at']));
            
            $remainingAttempts = self::MAX_ATTEMPTS - $verificationData['attempts'];
            return [
                'success' => false,
                'message' => "Code incorrect. Il vous reste {$remainingAttempts} tentative(s)."
            ];
        }

        // Code correct, récupérer les données utilisateur
        try {
            $userData = decrypt($verificationData['user_data']);
            Cache::forget($cacheKey); // Supprimer les données du cache
            
            return [
                'success' => true,
                'message' => 'Code vérifié avec succès.',
                'user_data' => $userData
            ];
        } catch (\Exception $e) {
            Cache::forget($cacheKey);
            return [
                'success' => false,
                'message' => 'Erreur lors de la vérification. Veuillez recommencer.'
            ];
        }
    }

    /**
     * Obtenir les informations de vérification
     */
    public function getVerificationInfo(string $email): ?array
    {
        $cacheKey = $this->getCacheKey($email);
        $verificationData = Cache::get($cacheKey);

        if (!$verificationData) {
            return null;
        }

        return [
            'expires_at' => $verificationData['expires_at'],
            'attempts' => $verificationData['attempts'],
            'max_attempts' => self::MAX_ATTEMPTS,
            'created_at' => $verificationData['created_at']
        ];
    }

    /**
     * Annuler une vérification
     */
    public function cancelVerification(string $email): void
    {
        $cacheKey = $this->getCacheKey($email);
        Cache::forget($cacheKey);
    }

    /**
     * Générer un code à 6 chiffres
     */
    private function generateCode(): string
    {
        return str_pad(random_int(0, 999999), self::CODE_LENGTH, '0', STR_PAD_LEFT);
    }

    /**
     * Obtenir la clé de cache sécurisée
     */
    private function getCacheKey(string $email): string
    {
        return self::CACHE_PREFIX . hash('sha256', $email);
    }

    /**
     * Obtenir la clé de rate limiting
     */
    private function getRateLimitKey(string $email): string
    {
        return 'rate_limit:' . hash('sha256', $email);
    }
}
