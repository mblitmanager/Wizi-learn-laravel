<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DailyFormationNotificationController extends Controller
{
    public function notify(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->stagiaire) return response()->json(['ok' => true]);

        $stagiaire = $user->stagiaire;
        $today = Carbon::now()->startOfDay();
        $in7days = Carbon::now()->addDays(7)->endOfDay();

        // Récupère les formations à venir dans moins de 7 jours
        $formations = $stagiaire->catalogue_formations()
            ->whereBetween('date_debut_formation', [$today, $in7days])
            ->get();

        foreach ($formations as $formation) {
            // Vérifie si une notif a déjà été envoyée aujourd'hui pour cette formation
            $alreadyNotified = \App\Models\Notification::where('user_id', $user->id)
                ->where('type', 'formation')
                ->whereDate('created_at', $today)
                ->where('data->formation_id', (string)$formation->id)
                ->exists();

            if (!$alreadyNotified) {
                $title = 'Votre formation commence bientôt !';
                $body = "La formation \"{$formation->titre}\" débute le " . Carbon::parse($formation->date_debut_formation)->format('d/m/Y');
                $data = [
                    'type' => 'formation',
                    'formation_id' => (string)$formation->id,
                    'date_debut_formation' => (string)$formation->date_debut_formation,
                ];
                // Envoi FCM
                if ($user->fcm_token) {
                    app(\App\Services\NotificationService::class)->sendFcmToUser($user, $title, $body, $data);
                }
                // Historique
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => $data['type'],
                    'title' => $title,
                    'message' => $body,
                    'data' => $data,
                    'read' => false,
                ]);
            }
        }

        return response()->json(['ok' => true]);
    }
}
