<?php

namespace App\Services;

use App\Models\Transfer;
use App\Models\User;
use App\Services\PaymentGatewayService;
use App\Services\FeeCalculatorService;
use App\Services\SecurityService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TransferService
{
    public function __construct(
        private PaymentGatewayService $paymentGateway,
        private FeeCalculatorService $feeCalculator,
        private SecurityService $securityService,
        private NotificationService $notificationService
    ) {}

    public function sendMoney(
        User $sender,
        string $recipientIdentifier,
        float $amount,
        string $currency,
        string $securityQuestion,
        string $securityAnswer,
        int $paymentMethodId,
        ?string $notes = null
    ): Transfer {
        
        return DB::transaction(function () use (
            $sender, $recipientIdentifier, $amount, $currency,
            $securityQuestion, $securityAnswer, $paymentMethodId, $notes
        ) {
            
            // Vérifications de sécurité
            $this->securityService->validateTransfer($sender, $amount, $recipientIdentifier);

            // Vérifications préliminaires
            if (!$sender->canSendMoney()) {
                throw new \Exception('Votre compte ne permet pas d\'envoyer de l\'argent');
            }

            // Vérifier les limites du pays
            $country = $sender->country;
            if ($amount < $country->min_transfer_amount || $amount > $country->max_transfer_amount) {
                throw new \Exception("Le montant doit être entre {$country->min_transfer_amount} et {$country->max_transfer_amount} {$currency}");
            }

            // Calcul des frais (gratuit pour l'instant)
            $fees = 0; // $this->feeCalculator->calculateFees($amount, $currency);
            $totalAmount = $amount + $fees;

            // Vérification du portefeuille
            $wallet = $sender->wallet;
            if (!$wallet->canDebit($totalAmount)) {
                throw new \Exception('Solde insuffisant ou limite de transfert dépassée');
            }

            // Déterminer si le destinataire est un utilisateur existant
            $recipient = $this->findRecipient($recipientIdentifier);

            // Créer le transfert
            $transfer = Transfer::create([
                'sender_id' => $sender->id,
                'recipient_id' => $recipient?->id,
                'recipient_email' => filter_var($recipientIdentifier, FILTER_VALIDATE_EMAIL) ? $recipientIdentifier : null,
                'recipient_phone' => !filter_var($recipientIdentifier, FILTER_VALIDATE_EMAIL) ? $recipientIdentifier : null,
                'amount' => $amount,
                'currency' => $currency,
                'fees' => $fees,
                'total_amount' => $totalAmount,
                'security_question' => $securityQuestion,
                'security_answer_hash' => Hash::make($securityAnswer),
                'payment_method_id' => $paymentMethodId,
                'notes' => $notes,
                'status' => 'pending'
            ]);

            // Débiter le portefeuille de l'expéditeur
            $wallet->debit($totalAmount, "Transfert vers {$recipientIdentifier}", $transfer->id);

            // Traitement du paiement via la gateway
            $paymentResult = $this->paymentGateway->processPayment(
                $sender->paymentMethods()->findOrFail($paymentMethodId),
                $totalAmount,
                $currency,
                $transfer->id
            );

            if (!$paymentResult['success']) {
                // Rembourser le portefeuille en cas d'échec
                $wallet->credit($totalAmount, "Remboursement transfert échoué", $transfer->id);
                throw new \Exception('Échec du paiement: ' . $paymentResult['message']);
            }

            $transfer->update([
                'status' => 'sent',
                'gateway_reference' => $paymentResult['transaction_id'] ?? null,
                'gateway_response' => $paymentResult
            ]);

            // Log de sécurité
            $this->securityService->logTransferSent($transfer);

            // Envoyer la notification au destinataire
            $this->notificationService->sendTransferNotification($transfer);

            return $transfer;
        });
    }

    public function claimMoney(
        string $transferCode,
        string $securityAnswer,
        int $recipientPaymentMethodId,
        string $recipientIdentifier
    ): Transfer {
        
        return DB::transaction(function () use (
            $transferCode, $securityAnswer, $recipientPaymentMethodId, $recipientIdentifier
        ) {
            
            $transfer = Transfer::where('transfer_code', $transferCode)
                ->where('status', 'sent')
                ->firstOrFail();

            // Vérifications de sécurité
            $this->securityService->validateClaim($transfer, $recipientIdentifier);

            // Vérifications
            if ($transfer->isExpired()) {
                throw new \Exception('Ce transfert a expiré');
            }

            if (!$transfer->canAttemptClaim()) {
                throw new \Exception('Nombre maximum de tentatives atteint');
            }

            // Vérifier l'identifiant du destinataire
            if ($transfer->recipient_email !== $recipientIdentifier && 
                $transfer->recipient_phone !== $recipientIdentifier) {
                $transfer->increment('failed_attempts');
                $this->securityService->logFailedClaim($transfer, $recipientIdentifier);
                throw new \Exception('Identifiant de destinataire incorrect');
            }

            // Vérifier la réponse de sécurité
            if (!Hash::check($securityAnswer, $transfer->security_answer_hash)) {
                $transfer->increment('failed_attempts');
                $this->securityService->logFailedClaim($transfer, $recipientIdentifier);
                throw new \Exception('Réponse de sécurité incorrecte');
            }

            // Traitement du paiement vers le destinataire
            $paymentResult = $this->paymentGateway->sendToRecipient(
                $recipientPaymentMethodId,
                $transfer->recipient_amount,
                $transfer->recipient_currency,
                $transfer->id
            );

            if (!$paymentResult['success']) {
                throw new \Exception('Échec du transfert: ' . $paymentResult['message']);
            }

            $transfer->update([
                'status' => 'completed',
                'recipient_payment_method_id' => $recipientPaymentMethodId,
                'claimed_at' => now(),
                'gateway_reference' => $paymentResult['transaction_id'] ?? null
            ]);

            // Log de sécurité
            $this->securityService->logTransferClaimed($transfer);

            // Notification à l'expéditeur
            $this->notificationService->notifyTransferClaimed($transfer);

            return $transfer;
        });
    }

    public function getTransferByCode(string $transferCode): ?Transfer
    {
        return Transfer::where('transfer_code', $transferCode)
            ->with(['sender'])
            ->first();
    }

    public function cancelTransfer(int $transferId, User $user): Transfer
    {
        return DB::transaction(function () use ($transferId, $user) {
            $transfer = Transfer::where('id', $transferId)
                ->where('sender_id', $user->id)
                ->where('status', 'sent')
                ->firstOrFail();

            if ($transfer->isExpired()) {
                throw new \Exception('Ce transfert a déjà expiré');
            }

            // Rembourser l'expéditeur
            $user->wallet->credit(
                $transfer->total_amount,
                "Remboursement transfert annulé #{$transfer->transfer_code}",
                $transfer->id
            );

            $transfer->update(['status' => 'cancelled']);

            // Log de sécurité
            $this->securityService->logTransferCancelled($transfer);

            return $transfer;
        });
    }

    private function findRecipient(string $identifier): ?User
    {
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return User::where('email', $identifier)->first();
        }
        
        return User::where('phone', $identifier)->first();
    }
}
