<?php

namespace Database\Seeders;

use App\Models\Classement;
use App\Models\Participation;
use Illuminate\Database\Seeder;

/**
 * 10 lignes de classement, calculées à partir des participations.
 */
class ClassementSeeder extends Seeder
{
    public function run(): void
    {
        $participations = Participation::orderByRaw('CAST(score AS UNSIGNED) DESC')
            ->orderBy('heure')
            ->take(10)
            ->get();

        foreach ($participations as $rang => $participation) {
            Classement::updateOrCreate(
                ['stagiaire_id' => $participation->stagiaire_id, 'quiz_id' => $participation->quiz_id],
                ['rang' => $rang + 1, 'points' => $participation->score]
            );
        }
    }
}
