<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Http\Request;
use App\Events\DailyNotificationEvent;
use App\Models\User;

class DailyNotificationController extends Controller
{
    public function send(Request $request)
    {
        // Heure française (Europe/Paris)
        $now = now()->setTimezone('Europe/Paris');
        $today = $now->toDateString();
        $targetTime = $now->copy()->setTime(9, 0, 0);

        // Clé de cache pour éviter les doublons
        $cacheKey = 'daily_notification_sent_' . $today;

        // Vérifier si déjà envoyé après 09h
        if ($now->greaterThanOrEqualTo($targetTime) && !Cache::has($cacheKey)) {
            // Récupérer les stagiaires (supposons un champ 'role' = 'stagiaire')
            $stagiaires = User::where('role', 'stagiaire')->get();
            foreach ($stagiaires as $stagiaire) {
                Event::dispatch(new DailyNotificationEvent($stagiaire));
            }
            // Marquer comme envoyé pour aujourd'hui (expire dans 24h)
            Cache::put($cacheKey, true, now()->addDay());
            return response()->json(['status' => 'sent']);
        }
        return response()->json(['status' => 'already_sent_or_too_early']);
    }
}
