<?php

namespace App\Console\Commands;

use App\Models\Transfer;
use App\Services\PaymentGatewayService;
use App\Services\TransferNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckPendingPayments extends Command
{
    protected $signature = 'transfers:check-pending';
    protected $description = 'Vérifier les paiements en attente auprès de NotchPay';

    protected $paymentGateway;
    protected $notificationService;

    public function __construct(PaymentGatewayService $paymentGateway, TransferNotificationService $notificationService)
    {
        parent::__construct();
        $this->paymentGateway = $paymentGateway;
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $this->info('🔍 Vérification des paiements en attente...');

        // Récupérer tous les transferts en attente de paiement
        $pendingTransfers = Transfer::where('status', 'payment_pending')
            ->whereNotNull('payment_reference')
            ->where('created_at', '>', now()->subHours(24)) // Seulement les dernières 24h
            ->get();

        $this->info("📊 {$pendingTransfers->count()} transferts en attente trouvés");

        foreach ($pendingTransfers as $transfer) {
            $this->info("🔄 Vérification du transfert #{$transfer->id} - Référence: {$transfer->payment_reference}");

            try {
                // Vérifier le statut auprès de NotchPay
                $result = $this->paymentGateway->verifyPayment($transfer->payment_reference);

                if ($result['success']) {
                    $status = $result['status'];
                    $this->info("📡 Statut NotchPay: {$status}");

                    switch ($status) {
                        case 'completed':
                            // Paiement confirmé
                            $transfer->update([
                                'status' => 'sent',
                                'payment_completed_at' => now()
                            ]);

                            // Envoyer l'email au destinataire
                            $this->notificationService->sendTransferNotification($transfer);

                            $this->info("✅ Transfert #{$transfer->id} confirmé et email envoyé");
                            break;

                        case 'failed':
                        case 'canceled':
                            // Paiement échoué
                            $transfer->update([
                                'status' => 'failed',
                                'failure_reason' => "Payment {$status} on NotchPay"
                            ]);

                            $this->warn("❌ Transfert #{$transfer->id} échoué: {$status}");
                            break;

                        default:
                            $this->info("⏳ Transfert #{$transfer->id} toujours en attente: {$status}");
                            break;
                    }
                } else {
                    $this->error("❌ Erreur lors de la vérification: " . $result['message']);
                }

            } catch (\Exception $e) {
                $this->error("💥 Erreur pour le transfert #{$transfer->id}: " . $e->getMessage());
                Log::error("Check pending payment error for transfer {$transfer->id}: " . $e->getMessage());
            }

            // Pause pour éviter de surcharger l'API
            sleep(1);
        }

        $this->info('✅ Vérification terminée');
    }
}
