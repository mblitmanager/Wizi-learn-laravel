<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stagiaire;
use App\Services\NotificationService;
use Carbon\Carbon;

class NotifyUpcomingFormations extends Command
{
    protected $signature = 'notify:upcoming-formations';
    protected $description = 'Envoie une notification aux stagiaires dont la formation commence dans moins d\'une semaine';

    public function handle(NotificationService $notificationService)
    {
        $today = Carbon::today();
        $inAWeek = $today->copy()->addWeek();

        $stagiaires = Stagiaire::whereNotNull('date_debut_formation')
            ->whereDate('date_debut_formation', '>=', $today)
            ->whereDate('date_debut_formation', '<=', $inAWeek)
            ->get();

        foreach ($stagiaires as $stagiaire) {
            $user = $stagiaire->user;
            if ($user) {
                $dateDebut = Carbon::parse($stagiaire->date_debut_formation)->locale('fr')->isoFormat('dddd D MMMM');
                $notificationService->notifyFormationUpdate(
                    $user->id,
                    'Votre formation démarre bientôt',
                    $stagiaire->id
                );
            }
        }
        $this->info('Notifications envoyées aux stagiaires concernés.');
    }
}
