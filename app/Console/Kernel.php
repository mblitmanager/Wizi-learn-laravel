<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\NotifyUpcomingFormations::class,
    ];

    protected function schedule(\Illuminate\Console\Scheduling\Schedule $schedule)
    {
        // Exécuter la notification chaque jour à 8h
        $schedule->command('notify:upcoming-formations')->dailyAt('08:00');
    }
}
