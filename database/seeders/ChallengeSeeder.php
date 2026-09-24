<?php

namespace Database\Seeders;

use App\Models\Challenge;
use App\Models\Participation;
use Illuminate\Database\Seeder;

class ChallengeSeeder extends Seeder
{
    public function run(): void
    {
        $challenges = [
            ['Défi Sans Faute', 'Obtenez 100 % de bonnes réponses à un quiz.', 20],
            ['Défi Rapidité', 'Terminez un quiz en moins de 3 minutes.', 15],
            ['Défi Régularité', 'Jouez un quiz chaque jour pendant une semaine.', 30],
            ['Défi Découverte', 'Participez à un quiz d\'une nouvelle catégorie.', 10],
            ['Défi Bureautique', 'Réussissez les quiz Word, Excel et PowerPoint.', 40],
            ['Défi Créatif', 'Réussissez les quiz Photoshop et Illustrator.', 25],
            ['Défi Polyglotte', 'Obtenez au moins 80 % au quiz d\'anglais.', 20],
            ['Défi Digital', 'Terminez le quiz DigComp sans erreur.', 15],
            ['Défi Remontée', 'Améliorez votre score sur un quiz déjà joué.', 10],
            ['Défi du Mois', 'Terminez 5 quiz dans le mois.', 50],
        ];

        $participations = Participation::orderBy('id')->take(10)->get();
        if ($participations->isEmpty()) {
            return;
        }

        foreach ($challenges as $i => [$titre, $description, $points]) {
            Challenge::updateOrCreate(
                ['titre' => $titre],
                [
                    'description' => $description,
                    'date_debut' => now()->startOfMonth()->toDateString(),
                    'date_fin' => now()->endOfMonth()->toDateString(),
                    'points' => (string) $points,
                    'participation_id' => $participations[$i % $participations->count()]->id,
                ]
            );
        }
    }
}
