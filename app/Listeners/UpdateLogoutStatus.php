<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateLogoutStatus
{
    public function handle(object $event): void
    {
        try {
            $user = $event->user;

            // Mettre à jour la session la plus récente
            $user->loginHistories()
                ->whereNull('logout_at')
                ->latest()
                ->first()
                ?->update([
                    'logout_at' => now()
                ]);

            $user->update([
                'is_online' => false
            ]);
        } catch (\Exception $e) {
            Log::error('Logout tracking failed', [
                'error' => $e->getMessage(),
                'user_id' => $event->user->id ?? 'unknown'
            ]);
        }
    }
}
