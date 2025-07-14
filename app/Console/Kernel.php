<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\CheckPendingPayments::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Vérifier les paiements en attente toutes les 5 minutes
        $schedule->command('transfers:check-pending')
                 ->everyFiveMinutes()
                 ->withoutOverlapping();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
