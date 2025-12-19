<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
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
        'formation_end' => 1,
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
        $this->sendFormationEndReminders();
        $this->sendAppInactivityReminders();
        $this->sendQuizInactivityReminders();
        $this->sendFirstQuizReminders();

        Cache::put('auto_reminders_last_run', Carbon::now()->toDateTimeString());

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
        $this->info('Traitement: inactivité quiz (7, 30, 90 jours)');
        $daysList = [7, 30, 90]; // Updated to match user request: 7, 30, 3 months

        $users = User::with('stagiaire')->get();

        foreach ($users as $user) {
            $lastParticipation = QuizParticipation::where('user_id', $user->id)
                ->whereNotNull('completed_at')
                ->orderBy('completed_at', 'desc')
                ->first();

            if (!$lastParticipation) continue; // Skip those who never played (handled by sendFirstQuizReminders)

            $lastQuizAt = Carbon::parse($lastParticipation->completed_at);

            foreach ($daysList as $days) {
                $threshold = Carbon::now()->subDays($days);
                
                if ($lastQuizAt->lte($threshold)) {
                    $window = $this->deduplicationWindow['inactivity_quiz'] ?? 7;
                    $already = \App\Models\Notification::where('user_id', $user->id)
                        ->where('type', 'inactivity_quiz')
                        ->where('data->days', (string)$days)
                        ->where('created_at', '>=', Carbon::now()->subDays($window))
                        ->exists();
                    if ($already) continue;

                    $title = 'On vous attend sur les quiz !';
                    $message = "Cela fait {$days} jours depuis votre dernier quiz. Revisitez vos formations et testez vos connaissances !";

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

    protected function sendFirstQuizReminders()
    {
        $this->info('Traitement: premier quiz non joué (1, 3, 7 jours après connexion)');
        $daysList = [1, 3, 7];

        foreach ($daysList as $days) {
            $thresholdStart = Carbon::now()->subDays($days)->startOfDay();
            $thresholdEnd = Carbon::now()->subDays($days)->endOfDay();

            // Users who logged in for the first time exactly $days ago AND never played a quiz
            $users = User::whereBetween('created_at', [$thresholdStart, $thresholdEnd])
                ->whereDoesntHave('quizParticipations')
                ->get();

            foreach ($users as $user) {
                $already = \App\Models\Notification::where('user_id', $user->id)
                    ->where('type', 'first_quiz_reminder')
                    ->where('data->days', (string)$days)
                    ->exists();
                if ($already) continue;

                $title = 'Lancez votre premier quiz !';
                $message = "Vous êtes inscrit depuis {$days} jour(s). N'attendez plus pour tester vos connaissances avec votre premier quiz !";

                if ($user->fcm_token) {
                    $this->notificationService->sendFcmToUser($user, $title, $message, [
                        'type' => 'first_quiz_reminder',
                        'days' => (string)$days,
                    ]);
                }

                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => 'first_quiz_reminder',
                    'message' => $message,
                    'data' => ['days' => $days],
                    'read' => false,
                ]);
                $this->info("Notifié user {$user->email} pour premier quiz non joué ({$days} jours)");
            }
        }
    }

    protected function sendFormationEndReminders()
    {
        $this->info('Traitement: rappels fin de formation (J-3)');
        $today = Carbon::today();
        $days = 3;

        $targetDate = $today->copy()->addDays($days)->toDateString();
        $stagiaires = Stagiaire::whereDate('date_fin_formation', $targetDate)->with('user')->get();

        foreach ($stagiaires as $stagiaire) {
            $user = $stagiaire->user;
            if (!$user) continue;

            $already = \App\Models\Notification::where('user_id', $user->id)
                ->where('type', 'formation_end')
                ->where('data->formation_id', (string)($stagiaire->formation_id ?? ''))
                ->where('created_at', '>=', Carbon::now()->subDays(1))
                ->exists();

            if ($already) continue;

            $title = 'Votre formation finit bientôt !';
            $message = "Votre formation se termine dans 3 jours. C'est le moment idéal pour valider vos derniers quiz et obtenir votre attestation !";

            if ($user->fcm_token) {
                $this->notificationService->sendFcmToUser($user, $title, $message, [
                    'type' => 'formation_end',
                    'formation_id' => (string)($stagiaire->formation_id ?? ''),
                ]);
            }

            \App\Models\Notification::create([
                'user_id' => $user->id,
                'type' => 'formation_end',
                'message' => $message,
                'data' => [
                    'formation_id' => (string)($stagiaire->formation_id ?? ''),
                    'date_fin_formation' => (string)$stagiaire->date_fin_formation,
                ],
                'read' => false,
            ]);
            $this->info("Notifié user {$user->email} pour fin de formation (J-3)");
        }
    }
}
