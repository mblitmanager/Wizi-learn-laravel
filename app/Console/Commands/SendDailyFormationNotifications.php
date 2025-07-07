<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stagiaire;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendDailyFormationNotifications extends Command
{
    protected $signature = 'formation:notify-daily';
    protected $description = 'Envoie les notifications de formation à venir à tous les stagiaires';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $today = Carbon::now()->startOfDay();
        $in7days = Carbon::now()->addDays(7)->endOfDay();

        $stagiaires = Stagiaire::with(['user', 'catalogue_formations' => function($q) {
            $q->withPivot(['date_debut']);
        }])->get();

        foreach ($stagiaires as $stagiaire) {
            $user = $stagiaire->user;
            if (!$user) {
                continue;
            }
            $pivotRows = $stagiaire->catalogue_formations->filter(function($formation) use ($today, $in7days) {
                $dateDebut = $formation->pivot->date_debut ? Carbon::parse($formation->pivot->date_debut) : null;
                return $dateDebut && $dateDebut->between($today, $in7days);
            });
            foreach ($pivotRows as $formation) {
                $dateDebut = $formation->pivot->date_debut;
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
                    if ($user->fcm_token) {
                        $this->notificationService->sendFcmToUser($user, $title, $body, $data);
                    }
                    \App\Models\Notification::create([
                        'user_id' => $user->id,
                        'type' => $data['type'],
                        'title' => $title,
                        'message' => $body,
                        'data' => $data,
                        'read' => false,
                    ]);
                    $this->info("Notification envoyée à {$user->email} pour la formation {$formation->titre}");
                }
            }
        }
        $this->info('Notifications quotidiennes terminées.');
        return 0;
    }
}
