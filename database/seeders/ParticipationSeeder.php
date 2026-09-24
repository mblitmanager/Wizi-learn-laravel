<?php

namespace Database\Seeders;

use App\Models\Participation;
use App\Models\Quiz;
use App\Models\QuizParticipation;
use App\Models\Stagiaire;
use Illuminate\Database\Seeder;

/**
 * 10 participations : chaque stagiaire joue un quiz. Alimente à la fois
 * "participations" (par stagiaire) et "quiz_participations" (par utilisateur).
 */
class ParticipationSeeder extends Seeder
{
    /** Nombre de bonnes réponses (sur 5) pour chaque stagiaire */
    public const BONNES_REPONSES = [5, 4, 3, 5, 2, 4, 5, 3, 4, 5];

    public function run(): void
    {
        $quizzes = Quiz::withCount('questions')->orderBy('id')->get();

        foreach (Stagiaire::orderBy('id')->take(10)->get() as $i => $stagiaire) {
            $quiz = $quizzes[$i % $quizzes->count()];
            $correct = min(self::BONNES_REPONSES[$i], $quiz->questions_count);
            $joueLe = now()->subDays(20 - $i)->setTime(9 + $i, 15);
            $duree = 180 + $i * 25;

            Participation::updateOrCreate(
                ['stagiaire_id' => $stagiaire->id, 'quiz_id' => $quiz->id],
                [
                    'date' => $joueLe->toDateString(),
                    'heure' => (string) $duree,
                    'score' => (string) ($correct * 2),
                    'deja_jouer' => true,
                ]
            );

            QuizParticipation::updateOrCreate(
                ['user_id' => $stagiaire->user_id, 'quiz_id' => $quiz->id],
                [
                    'status' => 'completed',
                    'started_at' => $joueLe,
                    'completed_at' => $joueLe->copy()->addSeconds($duree),
                    'score' => $correct * 2,
                    'correct_answers' => $correct,
                    'time_spent' => $duree,
                ]
            );
        }
    }
}
