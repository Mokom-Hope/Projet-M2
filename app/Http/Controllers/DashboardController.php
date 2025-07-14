<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Statistiques
        $stats = [
            'wallet_balance' => $user->wallet->balance ?? 0,
            'total_sent' => $user->sentTransfers()->where('status', 'completed')->sum('amount'),
            'total_received' => $user->receivedTransfers()->where('status', 'completed')->sum('amount'),
            'pending_transfers' => $user->sentTransfers()->where('status', 'sent')->count(),
        ];

        // Transferts récents
        $recentTransfers = Transfer::where(function($query) use ($user) {
            $query->where('sender_id', $user->id)
                  ->orWhere('recipient_id', $user->id);
        })
        ->with(['sender', 'recipient'])
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

        // Transactions récentes
        $recentTransactions = Transaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Salutation dynamique basée sur l'heure
        $greeting = $this->getDynamicGreeting();

        return view('dashboard', compact('stats', 'recentTransfers', 'recentTransactions', 'greeting'));
    }

    private function getDynamicGreeting()
    {
        $hour = Carbon::now()->hour;
        
        if ($hour >= 5 && $hour < 12) {
            return 'Bonjour';
        } elseif ($hour >= 12 && $hour < 17) {
            return 'Bon après-midi';
        } elseif ($hour >= 17 && $hour < 21) {
            return 'Bonsoir';
        } else {
            return 'Bonne nuit';
        }
    }
}
