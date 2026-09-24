<?php

namespace Database\Seeders;

use App\Models\Participation;
use App\Models\Progression;
use Illuminate\Database\Seeder;

/**
 * 10 progressions, cohérentes avec les participations aux quiz.
 */
class ProgressionSeeder extends Seeder
{
    public function run(): void
    {
        $participations = Participation::with('quiz')->orderBy('id')->take(10)->get();

        foreach ($participations as $participation) {
            $total = $participation->quiz->questions()->count();
            $correct = intdiv((int) $participation->score, 2);
            $pourcentage = $total > 0 ? (int) round($correct / $total * 100) : 0;

            Progression::updateOrCreate(
                ['stagiaire_id' => $participation->stagiaire_id, 'quiz_id' => $participation->quiz_id],
                [
                    'formation_id' => $participation->quiz->formation_id,
                    'termine' => true,
                    'points' => $participation->score,
                    'pourcentage' => $pourcentage,
                    'correct_answers' => $correct,
                    'score' => $correct * 2,
                    'total_questions' => $total,
                    'time_spent' => (int) $participation->heure,
                    'completion_time' => $participation->updated_at,
                    'explication' => $pourcentage >= 80
                        ? 'Excellent résultat, les notions sont maîtrisées.'
                        : ($pourcentage >= 50 ? 'Bon résultat, quelques notions à revoir.' : 'Résultat à améliorer : revoyez les tutoriels associés.'),
                ]
            );
        }
    }
}
