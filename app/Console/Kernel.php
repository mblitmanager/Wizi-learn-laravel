<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\NotifyUpcomingFormations::class,
        \App\Console\Commands\SendScheduledNotifications::class,
    ];

    protected function schedule(\Illuminate\Console\Scheduling\Schedule $schedule)
    {
        // Exécuter la commande unique pour rappels programmés (formations & inactivité) chaque jour à 8h
        $schedule->command('notify:scheduled')->dailyAt('08:00');

        // Process scheduled announcements every minute
        $schedule->command('announcements:process')->everyMinute();
    }
}
