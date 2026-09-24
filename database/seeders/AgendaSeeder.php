<?php

namespace Database\Seeders;

use App\Models\Agenda;
use App\Models\Stagiaire;
use Illuminate\Database\Seeder;

class AgendaSeeder extends Seeder
{
    public function run(): void
    {
        // [titre, description, type d'événement, décalage en jours, heure, durée en minutes]
        $evenements = [
            ['Réunion de lancement', 'Présentation du parcours, de la plateforme e-learning et du planning.', 'Réunion de lancement', -30, 9, 120],
            ['Test de positionnement', 'Évaluation initiale du niveau avant le démarrage.', 'Évaluation', -28, 10, 45],
            ['Suivi individuel n°1', 'Point sur l\'avancement des premiers modules.', 'Suivi formateur', -20, 14, 60],
            ['Suivi individuel n°2', 'Exercices pratiques et réponses aux questions.', 'Suivi formateur', -10, 11, 60],
            ['Atelier cas pratique', 'Mise en situation sur un cas concret du module en cours.', 'Atelier', -3, 15, 90],
            ['Suivi individuel n°3', 'Revue des modules terminés et objectifs de la quinzaine.', 'Suivi formateur', 2, 10, 60],
            ['Entraînement à la certification', 'Examen blanc dans les conditions de la certification.', 'Évaluation', 7, 9, 90],
            ['Suivi individuel n°4', 'Correction de l\'examen blanc et axes de progrès.', 'Suivi formateur', 12, 14, 60],
            ['Passage de la certification', 'Examen final de certification (TOSA, TOEIC…).', 'Certification', 20, 9, 120],
            ['Bilan de fin de formation', 'Questionnaire de satisfaction et remise de l\'attestation.', 'Bilan', 25, 16, 45],
        ];

        $stagiaires = Stagiaire::orderBy('id')->take(10)->get();
        if ($stagiaires->isEmpty()) {
            return;
        }

        foreach ($evenements as $i => [$titre, $description, $type, $jours, $heure, $minutes]) {
            $stagiaire = $stagiaires[$i % $stagiaires->count()];
            $debut = now()->addDays($jours)->setTime($heure, 0);

            Agenda::updateOrCreate(
                ['titre' => $titre, 'stagiaire_id' => $stagiaire->id],
                [
                    'description' => $description,
                    'evenement' => $type,
                    'date_debut' => $debut->format('Y-m-d H:i:s'),
                    'date_fin' => $debut->copy()->addMinutes($minutes)->format('Y-m-d H:i:s'),
                    'commentaire' => $jours < 0 ? 'Séance réalisée.' : 'À venir - lien de visioconférence envoyé par e-mail.',
                ]
            );
        }
    }
}
