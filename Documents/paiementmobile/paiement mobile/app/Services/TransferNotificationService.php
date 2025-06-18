<?php

namespace App\Services;

use App\Models\Transfer;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TransferNotificationService
{
    public function sendTransferNotification(Transfer $transfer)
    {
        try {
            $recipientEmail = $transfer->recipient_email;
            
            if (!$recipientEmail) {
                Log::warning('No recipient email for transfer: ' . $transfer->id);
                return false;
            }

            // Données pour l'email
            $emailData = [
                'transfer_code' => $transfer->transfer_code,
                'amount' => number_format($transfer->amount, 0, ',', ' ') . ' ' . $transfer->currency,
                'sender_name' => $transfer->sender->full_name,
                'security_question' => $transfer->security_question,
                'claim_url' => route('transfers.claim'),
                'expires_at' => $transfer->expires_at ? $transfer->expires_at->format('d/m/Y à H:i') : 'Jamais'
            ];

            // Envoyer l'email
            Mail::send('emails.transfer-notification', $emailData, function ($message) use ($recipientEmail, $transfer) {
                $message->to($recipientEmail)
                        ->subject('💰 Vous avez reçu un transfert d\'argent - Code: ' . $transfer->transfer_code);
            });

            Log::info('Transfer notification sent to: ' . $recipientEmail . ' for transfer: ' . $transfer->id);
            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send transfer notification: ' . $e->getMessage());
            return false;
        }
    }
}
