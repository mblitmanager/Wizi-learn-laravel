<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;
use Carbon\Carbon;
use App\Models\Stagiaire;
use App\Models\User;
use App\Models\QuizParticipation;

class SendScheduledNotifications extends Command
{
    protected $signature = 'notify:scheduled';
    protected $description = 'Envoie les notifications programmées : formation (J-7,J-4,J-1), inactivité application et quiz (3,7,30 jours)';

    protected $notificationService;
    /**
     * Déduplication window (en jours) par type
     * 'formation' => ne pas renotifier pour la même formation si envoyé dans les X derniers jours
     * 'inactivity_app' => ne pas renotifier le même seuil si envoyé dans les X derniers jours
     * 'inactivity_quiz' => idem
     */
    protected $deduplicationWindow = [
        'formation' => 1,
        'inactivity_app' => 7,
        'inactivity_quiz' => 7,
    ];

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $this->info('Début envoi notifications programmées...');

        $this->sendFormationReminders();
        $this->sendAppInactivityReminders();
        $this->sendQuizInactivityReminders();

        $this->info('Fin envoi notifications programmées.');
        return 0;
    }

    protected function sendFormationReminders()
    {
        $this->info('Traitement: rappels formations (J-7, J-4, J-1)');
        $today = Carbon::today();
        $offsets = [7,4,1];

        foreach ($offsets as $days) {
            $targetDate = $today->copy()->addDays($days)->toDateString();
            $stagiaires = Stagiaire::whereDate('date_debut_formation', $targetDate)->with('user')->get();
            foreach ($stagiaires as $stagiaire) {
                $user = $stagiaire->user;
                if (!$user) continue;

                // Avoid duplicate notification for same formation within deduplication window
                $window = $this->deduplicationWindow['formation'] ?? 1;
                $already = \App\Models\Notification::where('user_id', $user->id)
                    ->where('type', 'formation')
                    ->where('data->formation_id', (string)$stagiaire->formation_id ?? '')
                    ->where('created_at', '>=', Carbon::now()->subDays($window))
                    ->exists();

                if ($already) continue;

                $dateDebut = Carbon::parse($stagiaire->date_debut_formation)->locale('fr')->isoFormat('dddd D MMMM');
                $title = 'Votre formation démarre bientôt';
                $message = "Votre formation commence dans {$days} jour(s) le {$dateDebut}";

                if ($user->fcm_token) {
                    $this->notificationService->sendFcmToUser($user, $title, $message, [
                        'type' => 'formation',
                        'formation_id' => (string)($stagiaire->formation_id ?? ''),
                        'date_debut' => (string)$stagiaire->date_debut_formation,
                        'days_before' => (string)$days,
                    ]);
                }

                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => 'formation',
                    'message' => $message,
                    'data' => [
                        'formation_id' => (string)($stagiaire->formation_id ?? ''),
                        'date_debut_formation' => (string)$stagiaire->date_debut_formation,
                        'days_before' => $days
                    ],
                    'read' => false,
                ]);
                $this->info("Notifié user {$user->email} pour formation (J-{$days})");
            }
        }
    }

    protected function sendAppInactivityReminders()
    {
        $this->info('Traitement: inactivité application (3,7,30 jours)');
        $daysList = [3,7,30];
        foreach ($daysList as $days) {
            $threshold = Carbon::now()->subDays($days);
            // users whose last_activity_at is before threshold OR who never had it but have app usage
            $users = User::where(function($q) use ($threshold) {
                $q->whereNotNull('last_activity_at')->where('last_activity_at', '<=', $threshold);
            })->orWhereHas('appUsages', function($q) use ($threshold) {
                $q->whereNotNull('last_used_at')->where('last_used_at', '<=', $threshold);
            })->get();

            foreach ($users as $user) {
                // skip if already notified in deduplication window for this inactivity period
                $window = $this->deduplicationWindow['inactivity_app'] ?? 7;
                $already = \App\Models\Notification::where('user_id', $user->id)
                    ->where('type', 'inactivity_app')
                    ->where('data->days', (string)$days)
                    ->where('created_at', '>=', Carbon::now()->subDays($window))
                    ->exists();
                if ($already) continue;

                $title = 'On vous a remarqué moins actif';
                $message = "Ça fait {$days} jours que vous n'avez pas utilisé l'application. Revenez découvrir de nouveaux contenus !";

                if ($user->fcm_token) {
                    $this->notificationService->sendFcmToUser($user, $title, $message, [
                        'type' => 'inactivity_app',
                        'days' => (string)$days,
                    ]);
                }

                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => 'inactivity_app',
                    'message' => $message,
                    'data' => ['days' => $days],
                    'read' => false,
                ]);
                $this->info("Notifié user {$user->email} pour inactivité app {$days} jours");
            }
        }
    }

    protected function sendQuizInactivityReminders()
    {
        $this->info('Traitement: inactivité quiz (3,7,30 jours)');
        $daysList = [3,7,30];

        // We'll compute last quiz participation date per user using quiz participations (completed_at or started_at)
        $users = User::with('appUsages')->get();

        foreach ($users as $user) {
            $lastParticipation = QuizParticipation::where('user_id', $user->id)
                ->whereNotNull('completed_at')
                ->orderBy('completed_at', 'desc')
                ->first();

            $lastQuizAt = $lastParticipation ? Carbon::parse($lastParticipation->completed_at) : null;

            foreach ($daysList as $days) {
                $threshold = Carbon::now()->subDays($days);
                $shouldNotify = false;

                if ($lastQuizAt) {
                    if ($lastQuizAt->lte($threshold)) {
                        $shouldNotify = true;
                    }
                } else {
                    // never played any quiz -> consider for notification at all thresholds
                    $shouldNotify = true;
                }

                if ($shouldNotify) {
                    $window = $this->deduplicationWindow['inactivity_quiz'] ?? 7;
                    $already = \App\Models\Notification::where('user_id', $user->id)
                        ->where('type', 'inactivity_quiz')
                        ->where('data->days', (string)$days)
                        ->where('created_at', '>=', Carbon::now()->subDays($window))
                        ->exists();
                    if ($already) continue;

                    $title = 'On vous attend sur les quiz !';
                    $message = $lastQuizAt
                        ? "Cela fait {$days} jours depuis votre dernier quiz. Revisitez vos formations et testez vos connaissances !"
                        : "Vous n'avez pas encore joué à un quiz. Lancez-en un pour tester vos connaissances !";

                    if ($user->fcm_token) {
                        $this->notificationService->sendFcmToUser($user, $title, $message, [
                            'type' => 'inactivity_quiz',
                            'days' => (string)$days,
                        ]);
                    }

                    \App\Models\Notification::create([
                        'user_id' => $user->id,
                        'type' => 'inactivity_quiz',
                        'message' => $message,
                        'data' => ['days' => $days],
                        'read' => false,
                    ]);
                    $this->info("Notifié user {$user->email} pour inactivité quiz {$days} jours");
                }
            }
        }
    }
}
