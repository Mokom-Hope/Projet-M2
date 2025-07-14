<?php

namespace App\Services;

use App\Models\Transfer;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function sendTransferNotification(Transfer $transfer): void
    {
        try {
            // Email au destinataire
            if ($transfer->recipient_email) {
                $this->sendEmailNotification($transfer);
            }
            
            // SMS au destinataire
            if ($transfer->recipient_phone) {
                $this->sendSMSNotification($transfer);
            }

        } catch (\Exception $e) {
            Log::error('Notification error: ' . $e->getMessage());
        }
    }

    public function notifyTransferClaimed(Transfer $transfer): void
    {
        try {
            // Notifier l'expéditeur que son transfert a été récupéré
            Mail::send('emails.transfer-claimed', [
                'transfer' => $transfer,
                'sender_name' => $transfer->sender->full_name,
                'amount' => $transfer->amount,
                'currency' => $transfer->currency,
                'claimed_at' => $transfer->claimed_at
            ], function ($message) use ($transfer) {
                $message->to($transfer->sender->email)
                       ->subject('Votre transfert a été récupéré');
            });

        } catch (\Exception $e) {
            Log::error('Transfer claimed notification error: ' . $e->getMessage());
        }
    }

    private function sendEmailNotification(Transfer $transfer): void
    {
        $claimUrl = route('transfers.claim', ['code' => $transfer->transfer_code]);
        
        Mail::send('emails.transfer-notification', [
            'transfer' => $transfer,
            'claim_url' => $claimUrl,
            'sender_name' => $transfer->sender->full_name,
            'amount' => $transfer->recipient_amount,
            'currency' => $transfer->recipient_currency,
            'security_question' => $transfer->security_question,
            'expires_at' => $transfer->expires_at
        ], function ($message) use ($transfer) {
            $message->to($transfer->recipient_email)
                   ->subject('Vous avez reçu un transfert d\'argent');
        });
    }

    private function sendSMSNotification(Transfer $transfer): void
    {
        $claimUrl = route('transfers.claim', ['code' => $transfer->transfer_code]);
        
        $message = "Vous avez reçu {$transfer->recipient_amount} {$transfer->recipient_currency} de {$transfer->sender->full_name}. ";
        $message .= "Code: {$transfer->transfer_code}. ";
        $message .= "Récupérez sur: {$claimUrl}";

        // Intégration SMS (à adapter selon le fournisseur)
        $this->sendSMS($transfer->recipient_phone, $message);
    }

    private function sendSMS(string $phone, string $message): void
    {
        // Implémentation SMS selon le fournisseur choisi
        Log::info("SMS envoyé à {$phone}: {$message}");
    }
}
