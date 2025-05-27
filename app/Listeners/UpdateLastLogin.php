<?php

namespace App\Listeners;

use App\Models\LoginHistories;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;

class UpdateLastLogin
{
    public function handle(Login $event): void
    {
        try {
            $ip = request()->header('X-Client-IP') ??
                request()->header('X-Forwarded-For') ??
                request()->ip();

            // Géolocalisation
            $location = $this->getLocation($ip);

            // Mettre à jour l'utilisateur
            $event->user->update([
                'last_login_at' => now(),
                'last_activity_at' => now(),
                'last_login_ip' => $ip,
                'is_online' => true
            ]);
            // Enregistrer l'historique détaillé
            LoginHistories::create([
                'user_id' => $event->user->id,
                'ip_address' => $ip,
                'country' => $location['country'] ?? null,
                'city' => $location['city'] ?? null,
                'device' => request()->userAgent(),
                'browser' => $this->getBrowser(),
                'platform' => $this->getPlatform(),
                'login_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Login tracking failed', [
                'error' => $e->getMessage(),
                'user_id' => $event->user->id ?? 'unknown'
            ]);
        }
    }

    protected function getLocation($ip)
    {
        if ($ip === '127.0.0.1') {
            return [];
        }

        try {
            // Utilisation d'un service comme ipinfo.io (gratuit jusqu'à 50k requêtes/mois)
            $response = Http::get("https://ipinfo.io/{$ip}/json?token=" . config('services.ipinfo.token'));
            return $response->json();
        } catch (\Exception $e) {
            Log::warning("Failed to get location for IP: {$ip}");
            return [];
        }
    }

    protected function getBrowser()
    {
        $agent = new Agent();
        return $agent->browser();
    }

    protected function getPlatform()
    {
        $agent = new Agent();
        return $agent->platform();
    }
}
