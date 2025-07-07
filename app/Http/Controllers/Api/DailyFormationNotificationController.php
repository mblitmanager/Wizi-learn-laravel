<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\NotificationService;

class DailyFormationNotificationController extends Controller
{
    
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        
        $this->notificationService = $notificationService;
    }
    public function notify(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->stagiaire) return response()->json(['ok' => true]);

        $stagiaire = $user->stagiaire;
        $today = Carbon::now()->startOfDay();
        $in7days = Carbon::now()->addDays(7)->endOfDay();

        // Récupère les liaisons pivot à venir dans moins de 7 jours
        $pivotRows = $stagiaire->catalogue_formations()->withPivot(['date_debut'])->get()->filter(function($formation) use ($today, $in7days) {
            $dateDebut = $formation->pivot->date_debut ? Carbon::parse($formation->pivot->date_debut) : null;
            return $dateDebut && $dateDebut->between($today, $in7days);
        });

        foreach ($pivotRows as $formation) {
            $dateDebut = $formation->pivot->date_debut;
            // Vérifie si une notif a déjà été envoyée aujourd'hui pour cette formation
            $alreadyNotified = \App\Models\Notification::where('user_id', $user->id)
                ->where('type', 'formation')
                ->whereDate('created_at', $today)
                ->where('data->formation_id', (string)$formation->id)
                ->exists();

            if (!$alreadyNotified) {
                $title = 'Votre formation commence bientôt !';
                $body = "La formation \"{$formation->titre}\" débute le " . Carbon::parse($dateDebut)->format('d/m/Y');
                $data = [
                    'type' => 'formation',
                    'formation_id' => (string)$formation->id,
                    'date_debut_formation' => (string)$dateDebut,
                ];
                // Envoi FCM
                if ($user->fcm_token) {
                    app(\App\Services\NotificationService::class)->sendFcmToUser($user, $title, $body, $data);
                }

                   $this->notificationService->sendFcmToUser(
                        $stagiaire->user,
                        $title,
                        $body,
                        $data
                    );
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
