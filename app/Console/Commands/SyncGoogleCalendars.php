<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncGoogleCalendars extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-google-calendars';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize Google Calendars for all users with a refresh token';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\GoogleCalendarSyncService $syncService)
    {
        $users = \App\Models\User::whereNotNull('google_refresh_token')->get();
        $this->info("Début de la synchronisation pour " . $users->count() . " utilisateurs...");

        foreach ($users as $user) {
            try {
                $this->info("Sync pour {$user->email}...");
                $result = $syncService->syncByUserId($user->id);
                if ($result) {
                    $this->line(" - Succès: {$result['calendarsSynced']} calendriers, {$result['eventsSynced']} événements.");
                } else {
                    $this->warn(" - Échec ou pas de token valide.");
                }
            } catch (\Exception $e) {
                $this->error(" - Erreur pour {$user->email}: " . $e->getMessage());
            }
        }

        $this->info("Synchronisation terminée.");
    }
}
