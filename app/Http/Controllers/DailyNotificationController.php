<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Http\Request;
use App\Events\DailyNotificationEvent;
use App\Models\User;
use App\Models\Stagiaire;

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
        // if ($now->greaterThanOrEqualTo($targetTime) && !Cache::has($cacheKey)) {
            // Récupérer les stagiaires dont la date de début de formation est dans 7 jours ou moins
            $stagiaires = Stagiaire::whereDate('date_debut_formation', '>=', $today)
                ->whereDate('date_debut_formation', '<=', $now->copy()->addDays(7)->toDateString())
                ->with('user')
                ->get();
                
            foreach ($stagiaires as $stagiaire) {
                if ($stagiaire->user) {
                    
                    Event::dispatch(new DailyNotificationEvent($stagiaire->user));
                }
            }
            // Marquer comme envoyé pour aujourd'hui (expire dans 24h)
            Cache::put($cacheKey, true, now()->addDay());
            return response()->json(['status' => 'sent', 'count' => $stagiaires->count()]);
        // }
        return response()->json(['status' => 'already_sent_or_too_early']);
    }
}
